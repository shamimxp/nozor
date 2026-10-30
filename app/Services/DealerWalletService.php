<?php

namespace App\Services;

use App\Models\Dealer;
use App\Models\DealerOrder;
use App\Models\DealerWalletTransaction;
use App\Models\DealerOrderPayment;
use App\Models\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DealerWalletService
{
    /**
     * Add deposit/funds into dealer wallet and auto-settle delivered orders (FIFO).
     */
    public function deposit(
        int $dealerId,
        float $amount,
        string $paymentMethod,
        array $details = []
    ): array {
        if ($amount <= 0) {
            throw new InvalidArgumentException("Deposit amount must be greater than 0.");
        }

        $status = $details['status'] ?? 'pending';

        return DB::transaction(function () use ($dealerId, $amount, $paymentMethod, $details, $status) {
            $dealer = Dealer::lockForUpdate()->findOrFail($dealerId);
            $userId = $details['created_by'] ?? (auth('admin')->id() ?? auth()->id());
            $paymentDate = $details['payment_date'] ?? now()->toDateString();

            if ($status === 'pending') {
                $collection = Collection::create([
                    'collection_no'   => Collection::generateCollectionNo(),
                    'collection_type' => 'dealer',
                    'party_type'      => Dealer::class,
                    'party_id'        => $dealer->id,
                    'source_type'     => 'dealer_wallet',
                    'source_id'       => null,
                    'amount'          => $amount,
                    'payment_method'  => $paymentMethod,
                    'status'          => 'pending',
                    'transaction_id'  => $details['transaction_id'] ?? null,
                    'bank_name'       => $details['bank_name'] ?? null,
                    'account_no'      => $details['account_no'] ?? null,
                    'payment_date'    => $paymentDate,
                    'note'            => !empty($details['note']) ? $details['note'] : 'Dealer wallet deposit',
                    'created_by'      => $userId,
                ]);

                return [
                    'collection'     => $collection,
                    'status'         => 'pending',
                    'transaction'    => null,
                    'settled_orders' => [],
                    'dealer'         => $dealer,
                ];
            }

            // If status is 'accepted'
            $beforeBalance = (float) ($dealer->wallet_balance ?? 0.00);
            $afterBalance  = $beforeBalance + $amount;

            // 1. Record Central Collection
            $collection = Collection::create([
                'collection_no'   => Collection::generateCollectionNo(),
                'collection_type' => 'dealer',
                'party_type'      => Dealer::class,
                'party_id'        => $dealer->id,
                'source_type'     => 'dealer_wallet',
                'source_id'       => null,
                'amount'          => $amount,
                'payment_method'  => $paymentMethod,
                'status'          => 'accepted',
                'accepted_at'     => now(),
                'accepted_by'     => $userId,
                'transaction_id'  => $details['transaction_id'] ?? null,
                'bank_name'       => $details['bank_name'] ?? null,
                'account_no'      => $details['account_no'] ?? null,
                'payment_date'    => $paymentDate,
                'note'            => $details['note'] ?? 'Dealer wallet deposit',
                'created_by'      => $userId,
            ]);

            // 2. Update Dealer Wallet Balance
            $dealer->wallet_balance = $afterBalance;
            $dealer->save();

            // 3. Log Credit Transaction in Ledger
            $transaction = DealerWalletTransaction::create([
                'dealer_id'       => $dealer->id,
                'transaction_no'  => DealerWalletTransaction::generateTransactionNo(),
                'collection_id'   => $collection->id,
                'dealer_order_id' => null,
                'type'            => 'credit',
                'amount'          => $amount,
                'before_balance'  => $beforeBalance,
                'after_balance'   => $afterBalance,
                'payment_method'  => $paymentMethod,
                'reference_no'    => $details['transaction_id'] ?? $collection->collection_no,
                'note'            => $details['note'] ?? 'Wallet Deposit',
                'created_by'      => $userId,
            ]);

            // 4. Auto-settle delivered orders in FIFO order (1, 2, 3...)
            $settledOrders = $this->settleDeliveredOrders($dealer->id, $userId);

            return [
                'collection'     => $collection,
                'status'         => 'accepted',
                'transaction'    => $transaction,
                'settled_orders' => $settledOrders,
                'dealer'         => $dealer->fresh(),
            ];
        });
    }

    /**
     * Accept a pending dealer collection and apply funds to wallet + FIFO settlement.
     */
    public function acceptDealerCollection(Collection $collection, ?int $adminId = null): array
    {
        return DB::transaction(function () use ($collection, $adminId) {
            if ($collection->status === 'accepted') {
                throw new InvalidArgumentException("Collection #{$collection->collection_no} is already accepted.");
            }

            $dealer = Dealer::lockForUpdate()->findOrFail($collection->party_id);
            $userId = $adminId ?? (auth('admin')->id() ?? auth()->id());

            $beforeBalance = (float) ($dealer->wallet_balance ?? 0.00);
            $afterBalance  = $beforeBalance + (float) $collection->amount;

            // 1. Update Dealer Wallet Balance
            $dealer->wallet_balance = $afterBalance;
            $dealer->save();

            // 2. Log Credit Transaction in Ledger
            $transaction = DealerWalletTransaction::create([
                'dealer_id'       => $dealer->id,
                'transaction_no'  => DealerWalletTransaction::generateTransactionNo(),
                'collection_id'   => $collection->id,
                'dealer_order_id' => null,
                'type'            => 'credit',
                'amount'          => $collection->amount,
                'before_balance'  => $beforeBalance,
                'after_balance'   => $afterBalance,
                'payment_method'  => $collection->payment_method,
                'reference_no'    => $collection->transaction_id ?? $collection->collection_no,
                'note'            => !empty($collection->note) ? str_replace(' (Pending)', '', $collection->note) : 'Dealer wallet deposit',
                'created_by'      => $userId,
            ]);

            // 3. Mark collection as accepted
            $collection->update([
                'status'      => 'accepted',
                'accepted_at' => now(),
                'accepted_by' => $userId,
            ]);

            // 4. Auto-settle delivered orders in FIFO order
            $settledOrders = $this->settleDeliveredOrders($dealer->id, $userId);

            return [
                'collection'     => $collection->fresh(),
                'status'         => 'accepted',
                'transaction'    => $transaction,
                'settled_orders' => $settledOrders,
                'dealer'         => $dealer->fresh(),
            ];
        });
    }

    /**
     * Reject a pending dealer collection.
     */
    public function rejectDealerCollection(Collection $collection, ?string $reason = null, ?int $adminId = null): Collection
    {
        return DB::transaction(function () use ($collection, $reason, $adminId) {
            if ($collection->status === 'accepted') {
                throw new InvalidArgumentException("Cannot reject an already accepted collection.");
            }

            $userId = $adminId ?? (auth('admin')->id() ?? auth()->id());
            $collection->update([
                'status'        => 'rejected',
                'rejected_at'   => now(),
                'rejected_by'   => $userId,
                'reject_reason' => $reason,
            ]);

            return $collection->fresh();
        });
    }

    /**
     * Settle delivered dealer orders using available wallet balance in FIFO order (order_date ASC, id ASC).
     */
    public function settleDeliveredOrders(int $dealerId, ?int $userId = null): array
    {
        return DB::transaction(function () use ($dealerId, $userId) {
            $dealer = Dealer::lockForUpdate()->findOrFail($dealerId);

            if ((float) $dealer->wallet_balance <= 0) {
                return [];
            }

            // Fetch delivered orders with pending dues in strict ascending chronological FIFO order
            $orders = DealerOrder::where('dealer_id', $dealer->id)
                ->where('status', 'delivered')
                ->where('due', '>', 0)
                ->orderBy('order_date', 'asc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            $settled = [];
            $adminId = $userId ?? (auth('admin')->id() ?? auth()->id());

            foreach ($orders as $order) {
                if ((float) $dealer->wallet_balance <= 0) {
                    break;
                }

                $orderDue = (float) $order->due;
                $settleAmount = min((float) $dealer->wallet_balance, $orderDue);

                if ($settleAmount <= 0) {
                    continue;
                }

                $before = (float) $dealer->wallet_balance;
                $after  = $before - $settleAmount;

                // Update dealer wallet balance
                $dealer->wallet_balance = $after;
                $dealer->save();

                // 1. Record Debit Transaction in Wallet Ledger
                $dwt = DealerWalletTransaction::create([
                    'dealer_id'       => $dealer->id,
                    'transaction_no'  => DealerWalletTransaction::generateTransactionNo(),
                    'collection_id'   => null,
                    'dealer_order_id' => $order->id,
                    'type'            => 'debit',
                    'amount'          => $settleAmount,
                    'before_balance'  => $before,
                    'after_balance'   => $after,
                    'payment_method'  => 'Wallet',
                    'reference_no'    => $order->order_number,
                    'note'            => "Settlement for delivered order #{$order->order_number}",
                    'created_by'      => $adminId,
                ]);

                // 2. Record Order Payment Breakdown
                $dop = DealerOrderPayment::create([
                    'dealer_order_id'              => $order->id,
                    'dealer_wallet_transaction_id' => $dwt->id,
                    'collection_id'                => null,
                    'amount'                       => $settleAmount,
                    'payment_date'                 => now()->toDateString(),
                    'note'                         => "FIFO Auto-Settlement from Wallet",
                    'created_by'                   => $adminId,
                ]);

                // 3. Update Order Paid & Due
                $order->paid = (float) $order->paid + $settleAmount;
                $order->due  = (float) $order->due  - $settleAmount;
                if ($order->due <= 0.001) {
                    $order->due = 0.00;
                }
                $order->save();

                $settled[] = [
                    'order_id'       => $order->id,
                    'order_number'   => $order->order_number,
                    'settled_amount' => $settleAmount,
                    'remaining_due'  => (float) $order->due,
                ];
            }

            return $settled;
        });
    }

    /**
     * Get dealer summary metrics.
     */
    public function getDealerSummary(int $dealerId): array
    {
        $dealer = Dealer::find($dealerId);
        if (!$dealer) {
            return [
                'wallet_balance'     => 0.00,
                'total_orders_count' => 0,
                'total_orders'       => 0.00,
                'total_paid'         => 0.00,
                'total_due'          => 0.00,
                'delivered_due'      => 0.00,
            ];
        }

        $orders = DealerOrder::where('dealer_id', $dealerId)
            ->where('status', '!=', 'cancelled')
            ->get();

        $deliveredOrders = $orders->where('status', 'delivered');
        $walletTxns = DealerWalletTransaction::where('dealer_id', $dealerId)->get();

        return [
            'wallet_balance'     => (float) ($dealer->wallet_balance ?? 0.00),
            'total_orders_count' => $orders->count(),
            'total_orders'       => (float) $orders->sum('grand_total'),
            'total_paid'         => (float) $orders->sum('paid'),
            'total_due'          => (float) $orders->sum('due'),
            'delivered_due'      => (float) $deliveredOrders->sum('due'),
            'total_credit'       => (float) $walletTxns->where('type', 'credit')->sum('amount'),
            'total_debit'        => (float) $walletTxns->where('type', 'debit')->sum('amount'),
        ];
    }
}
