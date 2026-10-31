<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Dealer;
use App\Models\DealerOrder;
use App\Models\DealerOrderItem;
use App\Models\DealerWalletTransaction;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\Product;
use App\Models\ReturnOrder;
use App\Models\ReturnOrderItem;
use App\Models\User;
use App\Models\WebOrder;
use App\Models\WebOrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReturnService
{
    /**
     * Create a new Return Order in 'pending' status
     */
    public function createReturn(array $data, int $userId): ReturnOrder
    {
        return DB::transaction(function () use ($data, $userId) {
            $channel = strtolower($data['channel'] ?? 'dealer');
            $partyId = !empty($data['party_id']) ? (int) $data['party_id'] : null;

            // Resolve party model and names
            $partyType = null;
            $customerName = $data['customer_name'] ?? null;
            $customerPhone = $data['customer_phone'] ?? null;

            if ($channel === 'dealer' && $partyId) {
                $dealer = Dealer::findOrFail($partyId);
                $partyType = Dealer::class;
                $customerName = $dealer->name . ($dealer->shop_name ? ' (' . $dealer->shop_name . ')' : '');
                $customerPhone = $dealer->phone;
            } elseif ($channel === 'pos' && $partyId) {
                $customer = Customer::find($partyId);
                if ($customer) {
                    $partyType = Customer::class;
                    $customerName = $customer->name;
                    $customerPhone = $customer->phone;
                }
            } elseif ($channel === 'web' && $partyId) {
                $user = User::find($partyId);
                if ($user) {
                    $partyType = User::class;
                    $customerName = $user->name;
                    $customerPhone = $user->phone ?? $user->email;
                }
            }

            // Refund type rule: Non-dealers can ONLY be cash refund
            $refundType = strtolower($data['refund_type'] ?? 'cash');
            if ($channel !== 'dealer') {
                $refundType = 'cash';
            }

            $itemsData = $data['items'] ?? [];
            if (empty($itemsData)) {
                throw ValidationException::withMessages(['items' => 'At least one product must be selected for return.']);
            }

            $totalItemsCount = 0;
            $totalQty = 0;
            $subtotal = 0;
            $processedItems = [];

            foreach ($itemsData as $item) {
                // Must be explicitly selected by the user
                if (empty($item['selected']) || $item['selected'] != '1') {
                    continue;
                }

                $returnQty = (float) ($item['return_qty'] ?? 0);
                if ($returnQty <= 0) {
                    continue; // Skip zero/negative returns
                }

                $productId = (int) ($item['product_id'] ?? 0);
                $product = Product::findOrFail($productId);
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $lineSubtotal = round($returnQty * $unitPrice, 2);

                $totalItemsCount++;
                $totalQty += $returnQty;
                $subtotal += $lineSubtotal;

                $processedItems[] = [
                    'order_type'              => $channel,
                    'order_id'                => (int) ($item['order_id'] ?? 0),
                    'order_number'            => (string) ($item['order_number'] ?? '-'),
                    'order_item_id'           => !empty($item['order_item_id']) ? (int) $item['order_item_id'] : null,
                    'product_id'              => $product->id,
                    'product_name'            => $product->name,
                    'product_code'            => $product->code ?? $product->sku ?? null,
                    'ordered_qty'             => (float) ($item['ordered_qty'] ?? 0),
                    'previously_returned_qty' => (float) ($item['previously_returned_qty'] ?? 0),
                    'return_qty'              => $returnQty,
                    'unit_price'              => $unitPrice,
                    'subtotal'                => $lineSubtotal,
                    'condition'               => $item['condition'] ?? 'good',
                    'restock'                 => !empty($item['restock']),
                    'item_note'               => !empty($item['item_note']) ? trim($item['item_note']) : null,
                ];
            }

            if (empty($processedItems)) {
                throw ValidationException::withMessages(['items' => 'Total return quantity must be greater than zero.']);
            }

            $deduction = max(0, (float) ($data['deduction_amount'] ?? 0));
            $netRefundAmount = max(0, round($subtotal - $deduction, 2));

            // Create Master Return Record in 'pending' status
            $returnOrder = ReturnOrder::create([
                'return_no'        => ReturnOrder::generateReturnNo(),
                'return_date'      => $data['return_date'] ?? now()->toDateString(),
                'channel'          => $channel,
                'party_type'       => $partyType,
                'party_id'         => $partyId,
                'customer_name'    => $customerName,
                'customer_phone'   => $customerPhone,
                'total_items_count'=> $totalItemsCount,
                'total_qty'        => $totalQty,
                'subtotal'         => $subtotal,
                'deduction_amount' => $deduction,
                'refund_amount'    => $netRefundAmount,
                'refund_type'      => $refundType,
                'status'           => 'pending',
                'return_note'      => !empty($data['return_note']) ? trim($data['return_note']) : null,
                'created_by'       => $userId,
            ]);

            // Save line items
            foreach ($processedItems as $itemRow) {
                $itemRow['return_id'] = $returnOrder->id;
                ReturnOrderItem::create($itemRow);
            }

            // Log event in UserActivity
            ActivityLogger::log(
                "Created Return Request #{$returnOrder->return_no} ({$returnOrder->channel_badge}, Amount: {$returnOrder->refund_amount}, Status: Pending)",
                'created',
                $returnOrder,
                ['attributes' => $returnOrder->toArray()],
                'admin',
                'order'
            );

            return $returnOrder->fresh(['items', 'party']);
        });
    }

    /**
     * Approve a pending return: applies wallet balance credit (if wallet type) and restocks items.
     */
    public function approveReturn(ReturnOrder $return, ?string $adminNote, int $userId): ReturnOrder
    {
        if ($return->status !== 'pending') {
            throw new \Exception("Only pending returns can be approved.");
        }

        return DB::transaction(function () use ($return, $adminNote, $userId) {
            $now = now();

            // 1. If Refund Type is 'wallet' and Party is Dealer: Credit Dealer Wallet
            if ($return->refund_type === 'wallet' && $return->channel === 'dealer' && $return->party_id) {
                $dealer = Dealer::lockForUpdate()->findOrFail($return->party_id);
                $before = (float) ($dealer->wallet_balance ?? 0.00);
                $after  = $before + (float) $return->refund_amount;

                // Update dealer wallet balance
                $dealer->wallet_balance = $after;
                $dealer->save();

                // Format wallet transaction note with return note
                $noteText = "Return Refund #{$return->return_no}";
                if (!empty($return->return_note)) {
                    $noteText .= " - Note: {$return->return_note}";
                }
                if (!empty($adminNote)) {
                    $noteText .= " [Admin: {$adminNote}]";
                }

                // Log immutable ledger transaction
                $dwt = DealerWalletTransaction::create([
                    'dealer_id'       => $dealer->id,
                    'transaction_no'  => DealerWalletTransaction::generateTransactionNo(),
                    'collection_id'   => null,
                    'dealer_order_id' => null,
                    'type'            => 'credit',
                    'amount'          => $return->refund_amount,
                    'before_balance'  => $before,
                    'after_balance'   => $after,
                    'payment_method'  => 'wallet_return',
                    'reference_no'    => $return->return_no,
                    'note'            => $noteText,
                    'created_by'      => $userId,
                ]);

                $return->dealer_wallet_transaction_id = $dwt->id;
            }

            // 2. Restock eligible products into warehouse stock
            if (!$return->stock_restocked) {
                $stockService = app(StockService::class);
                foreach ($return->items as $item) {
                    if ($item->restock && $item->condition === 'good' && $item->return_qty > 0) {
                        $itemNoteSuffix = !empty($item->item_note) ? " ({$item->item_note})" : "";
                        $stockService->recordIn(
                            productId: $item->product_id,
                            quantity: $item->return_qty,
                            sourceType: 'return',
                            sourceId: $return->id,
                            reference: $return->return_no,
                            note: "Restock: Return #{$return->return_no}{$itemNoteSuffix}",
                            userId: $userId
                        );
                    }
                }
                $return->stock_restocked = true;
            }

            // 3. Mark return status as 'approved'
            $return->status = 'approved';
            $return->approved_at = $now;
            $return->approved_by = $userId;
            if (!empty($adminNote)) {
                $return->admin_review_note = trim($adminNote);
            }
            $return->save();

            // Log event in UserActivity
            ActivityLogger::log(
                "Approved Return #{$return->return_no} (Refund: {$return->refund_type}, Amount: {$return->refund_amount})",
                'status_change',
                $return,
                ['old' => ['status' => 'pending'], 'new' => ['status' => 'approved']],
                'admin',
                'order'
            );

            return $return->fresh(['items', 'walletTransaction', 'party']);
        });
    }

    /**
     * Reject a pending return request
     */
    public function rejectReturn(ReturnOrder $return, string $reason, int $userId): ReturnOrder
    {
        if ($return->status !== 'pending') {
            throw new \Exception("Only pending returns can be rejected.");
        }

        return DB::transaction(function () use ($return, $reason, $userId) {
            $return->status = 'rejected';
            $return->rejected_at = now();
            $return->rejected_by = $userId;
            $return->reject_reason = trim($reason);
            $return->admin_review_note = trim($reason);
            $return->save();

            // Log event in UserActivity
            ActivityLogger::log(
                "Rejected Return #{$return->return_no} (Reason: {$return->reject_reason})",
                'status_change',
                $return,
                ['old' => ['status' => 'pending'], 'new' => ['status' => 'rejected']],
                'admin',
                'order'
            );

            return $return->fresh();
        });
    }

    /**
     * Fetch delivered invoices for party across channel
     */
    public function getInvoicesByParty(string $channel, int $partyId)
    {
        if ($channel === 'dealer') {
            return DealerOrder::where('dealer_id', $partyId)
                ->where('status', 'delivered')
                ->orderByDesc('id')
                ->get(['id', 'order_number', 'order_date', 'grand_total', 'paid', 'due', 'status'])
                ->map(function ($o) {
                    return [
                        'id'           => $o->id,
                        'order_number' => $o->order_number,
                        'order_date'   => $o->order_date ? date('Y-m-d', strtotime($o->order_date)) : $o->created_at->format('Y-m-d'),
                        'grand_total'  => (float) $o->grand_total,
                        'paid'         => (float) $o->paid,
                        'status'       => ucfirst($o->status),
                    ];
                });
        }

        if ($channel === 'pos') {
            return PosOrder::where('customer_id', $partyId)
                ->where('order_status', 'completed')
                ->orderByDesc('id')
                ->get(['id', 'order_number', 'order_date', 'payable_amount', 'paid_amount', 'order_status'])
                ->map(function ($o) {
                    return [
                        'id'           => $o->id,
                        'order_number' => $o->order_number,
                        'order_date'   => $o->order_date ? date('Y-m-d', strtotime($o->order_date)) : $o->created_at->format('Y-m-d'),
                        'grand_total'  => (float) $o->payable_amount,
                        'paid'         => (float) $o->paid_amount,
                        'status'       => ucfirst($o->order_status ?? 'completed'),
                    ];
                });
        }

        if ($channel === 'web') {
            return WebOrder::where('user_id', $partyId)
                ->where('status', 'delivered')
                ->orderByDesc('id')
                ->get(['id', 'invoice_no', 'total', 'status', 'created_at'])
                ->map(function ($o) {
                    return [
                        'id'           => $o->id,
                        'order_number' => $o->invoice_no ?? '#' . $o->id,
                        'order_date'   => $o->created_at->format('Y-m-d'),
                        'grand_total'  => (float) $o->total,
                        'paid'         => (float) $o->total,
                        'status'       => ucfirst($o->status ?? 'delivered'),
                    ];
                });
        }

        return collect();
    }

    /**
     * Fetch products from selected invoices with previously returned quantities calculated
     */
    public function getInvoiceProducts(string $channel, array $orderIds): array
    {
        if (empty($orderIds)) {
            return [];
        }

        $items = [];

        if ($channel === 'dealer') {
            $orderItems = DealerOrderItem::with(['product'])
                ->whereIn('dealer_order_id', $orderIds)
                ->get();

            foreach ($orderItems as $oi) {
                $order = DealerOrder::find($oi->dealer_order_id);
                $deliveredQty = (float) ($oi->confirm_qty ?: $oi->qty);

                // Calculate already returned quantity for this order item
                $previouslyReturned = (float) ReturnOrderItem::where('order_type', 'dealer')
                    ->where('order_id', $oi->dealer_order_id)
                    ->where('product_id', $oi->product_id)
                    ->whereHas('returnOrder', function ($q) {
                        $q->whereIn('status', ['pending', 'approved']);
                    })
                    ->sum('return_qty');

                $remainingReturnable = max(0, $deliveredQty - $previouslyReturned);

                $productImage = asset('images/no-image.png');
                if ($oi->product && !empty($oi->product->featured_image)) {
                    $img = ltrim($oi->product->featured_image, '/');
                    $productImage = asset('images/product/' . $img);
                }

                $items[] = [
                    'order_id'                => $oi->dealer_order_id,
                    'order_number'            => $order ? $order->order_number : '#' . $oi->dealer_order_id,
                    'order_item_id'           => $oi->id,
                    'product_id'              => $oi->product_id,
                    'product_name'            => $oi->product ? $oi->product->name : 'Product #' . $oi->product_id,
                    'product_code'            => $oi->product ? ($oi->product->code ?? $oi->product->sku) : '-',
                    'product_image'           => $productImage,
                    'ordered_qty'             => $deliveredQty,
                    'previously_returned_qty' => $previouslyReturned,
                    'remaining_returnable'    => $remainingReturnable,
                    'unit_price'              => (float) $oi->price,
                ];
            }
        } elseif ($channel === 'pos') {
            $orderItems = PosOrderItem::with(['product'])
                ->whereIn('pos_order_id', $orderIds)
                ->get();

            foreach ($orderItems as $oi) {
                $order = PosOrder::find($oi->pos_order_id);
                $deliveredQty = (float) $oi->quantity;

                $previouslyReturned = (float) ReturnOrderItem::where('order_type', 'pos')
                    ->where('order_id', $oi->pos_order_id)
                    ->where('product_id', $oi->product_id)
                    ->whereHas('returnOrder', function ($q) {
                        $q->whereIn('status', ['pending', 'approved']);
                    })
                    ->sum('return_qty');

                $remainingReturnable = max(0, $deliveredQty - $previouslyReturned);

                $productImage = asset('images/no-image.png');
                if ($oi->product && !empty($oi->product->featured_image)) {
                    $img = ltrim($oi->product->featured_image, '/');
                    $productImage = asset('images/product/' . $img);
                }

                $items[] = [
                    'order_id'                => $oi->pos_order_id,
                    'order_number'            => $order ? $order->order_number : '#' . $oi->pos_order_id,
                    'order_item_id'           => $oi->id,
                    'product_id'              => $oi->product_id,
                    'product_name'            => $oi->product_name ?? ($oi->product ? $oi->product->name : 'Product #' . $oi->product_id),
                    'product_code'            => $oi->product ? ($oi->product->code ?? $oi->product->sku) : '-',
                    'product_image'           => $productImage,
                    'ordered_qty'             => $deliveredQty,
                    'previously_returned_qty' => $previouslyReturned,
                    'remaining_returnable'    => $remainingReturnable,
                    'unit_price'              => (float) $oi->unit_price,
                ];
            }
        } elseif ($channel === 'web') {
            $orderItems = WebOrderItem::with(['product'])
                ->whereIn('web_order_id', $orderIds)
                ->get();

            foreach ($orderItems as $oi) {
                $order = WebOrder::find($oi->web_order_id);
                $deliveredQty = (float) $oi->quantity;

                $previouslyReturned = (float) ReturnOrderItem::where('order_type', 'web')
                    ->where('order_id', $oi->web_order_id)
                    ->where('product_id', $oi->product_id)
                    ->whereHas('returnOrder', function ($q) {
                        $q->whereIn('status', ['pending', 'approved']);
                    })
                    ->sum('return_qty');

                $remainingReturnable = max(0, $deliveredQty - $previouslyReturned);

                $productImage = asset('images/no-image.png');
                if ($oi->product && !empty($oi->product->featured_image)) {
                    $img = ltrim($oi->product->featured_image, '/');
                    $productImage = asset('images/product/' . $img);
                }

                $items[] = [
                    'order_id'                => $oi->web_order_id,
                    'order_number'            => $order ? ($order->invoice_no ?? '#' . $order->id) : '#' . $oi->web_order_id,
                    'order_item_id'           => $oi->id,
                    'product_id'              => $oi->product_id,
                    'product_name'            => $oi->product ? $oi->product->name : 'Product #' . $oi->product_id,
                    'product_code'            => $oi->product ? ($oi->product->code ?? $oi->product->sku) : '-',
                    'product_image'           => $productImage,
                    'ordered_qty'             => $deliveredQty,
                    'previously_returned_qty' => $previouslyReturned,
                    'remaining_returnable'    => $remainingReturnable,
                    'unit_price'              => (float) $oi->price,
                ];
            }
        }

        return $items;
    }
}
