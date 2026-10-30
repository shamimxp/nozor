<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\DealerOrder;
use App\Models\DealerWalletTransaction;
use App\Services\DealerWalletService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DealerWalletController extends Controller
{
    protected DealerWalletService $walletService;

    public function __construct(DealerWalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $dealers = Dealer::with(['orders'])->latest('id')->get();

            return DataTables::of($dealers)
                ->addIndexColumn()
                ->addColumn('dealer_info', function ($d) {
                    return '<div><strong class="text-dark font-weight-bolder">' . e($d->shop_name) . '</strong></div>' .
                           '<small class="text-muted">' . e($d->name) . ' (' . e($d->phone) . ')</small>';
                })
                ->addColumn('wallet_balance', function ($d) {
                    $balance = (float) ($d->wallet_balance ?? 0.00);
                    if ($balance > 0) {
                        return '<span class="badge badge-light-success font-weight-bolder font-medium-1 px-1 py-50">৳ ' . number_format($balance, 2) . '</span>';
                    }
                    return '<span class="text-muted font-weight-bold">৳ 0.00</span>';
                })
                ->addColumn('total_orders', function ($d) {
                    $total = $d->orders->where('status', '!=', 'cancelled')->sum('grand_total');
                    $count = $d->orders->where('status', '!=', 'cancelled')->count();
                    return '<strong>৳ ' . number_format($total, 2) . '</strong><br><small class="text-muted">' . $count . ' orders</small>';
                })
                ->addColumn('total_paid', function ($d) {
                    $paid = $d->orders->where('status', '!=', 'cancelled')->sum('paid');
                    return '<span class="text-success font-weight-bold">৳ ' . number_format($paid, 2) . '</span>';
                })
                ->addColumn('total_due', function ($d) {
                    $deliveredDue = $d->orders->where('status', 'delivered')->sum('due');
                    $totalDue = $d->orders->where('status', '!=', 'cancelled')->sum('due');

                    $html = '<strong class="' . ($deliveredDue > 0 ? 'text-danger' : 'text-success') . '" style="font-size:14px;">৳ ' . number_format($deliveredDue, 2) . '</strong>';
                    if ($totalDue > $deliveredDue) {
                        $html .= '<br><small class="text-muted" title="Including pending/confirmed orders">Total: ৳ ' . number_format($totalDue, 2) . '</small>';
                    }
                    return $html;
                })
                ->addColumn('action', function ($d) {
                    $balance = (float) ($d->wallet_balance ?? 0.00);
                    $deliveredDue = (float) $d->orders->where('status', 'delivered')->sum('due');

                    return '<div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-primary addDepositBtn"
                            data-id="' . $d->id . '"
                            data-name="' . e($d->name) . '"
                            data-shop="' . e($d->shop_name) . '"
                            data-balance="' . $balance . '"
                            data-due="' . $deliveredDue . '"
                            title="Add Deposit to Wallet">
                            <i data-feather="plus-circle" class="mr-25"></i> Deposit
                        </button>
                        <a href="' . route('admin.dealer-wallets.show', $d->id) . '" class="btn btn-outline-info" title="View Statement / Ledger">
                            <i data-feather="file-text" class="mr-25"></i> Statement
                        </a>
                    </div>';
                })
                ->rawColumns(['dealer_info', 'wallet_balance', 'total_orders', 'total_paid', 'total_due', 'action'])
                ->make(true);
        }

        $allDealers = Dealer::with('orders')->get();
        $totalDealers = $allDealers->count();
        $totalWalletBalance = (float) $allDealers->sum('wallet_balance');
        $totalDeliveredDue = 0.0;
        $totalOrdersAmount = 0.0;

        foreach ($allDealers as $d) {
            $totalOrdersAmount += (float) $d->orders->where('status', '!=', 'cancelled')->sum('grand_total');
            $totalDeliveredDue += (float) $d->orders->where('status', 'delivered')->sum('due');
        }

        return view('admin.dealer_wallet.index', compact(
            'totalDealers',
            'totalWalletBalance',
            'totalOrdersAmount',
            'totalDeliveredDue'
        ));
    }

    public function show(Request $request, $id)
    {
        $dealer = Dealer::findOrFail($id);
        $summary = $this->walletService->getDealerSummary($dealer->id);

        if ($request->ajax()) {
            $query = DealerWalletTransaction::where('dealer_id', $dealer->id)
                ->with(['creator', 'dealerOrder'])
                ->latest('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('transaction_no', fn($r) => '<span class="font-weight-bolder text-primary">#' . e($r->transaction_no) . '</span>')
                ->addColumn('date', fn($r) => $r->created_at->format('d M, Y h:i A'))
                ->addColumn('type_badge', function ($r) {
                    if ($r->type === 'credit') {
                        return '<span class="badge badge-light-success font-weight-bold"><i data-feather="arrow-down-left" class="mr-25"></i> Credit (Deposit)</span>';
                    }
                    return '<span class="badge badge-light-danger font-weight-bold"><i data-feather="arrow-up-right" class="mr-25"></i> Debit (Order Pay)</span>';
                })
                ->addColumn('amount', function ($r) {
                    if ($r->type === 'credit') {
                        return '<span class="text-success font-weight-bolder">+ ৳ ' . number_format($r->amount, 2) . '</span>';
                    }
                    return '<span class="text-danger font-weight-bolder">- ৳ ' . number_format($r->amount, 2) . '</span>';
                })
                ->addColumn('balance', function ($r) {
                    return '<small class="text-muted">৳ ' . number_format($r->before_balance, 2) . ' → </small>' .
                           '<strong class="text-dark">৳ ' . number_format($r->after_balance, 2) . '</strong>';
                })
                ->addColumn('method', fn($r) => '<span class="badge badge-light-secondary">' . e($r->payment_method) . '</span>')
                ->addColumn('reference', function ($r) {
                    if ($r->dealerOrder) {
                        return '<a href="' . route('admin.dealer-order.show', $r->dealerOrder->id) . '" class="badge badge-light-primary" target="_blank">Order #' . e($r->dealerOrder->order_number) . '</a>';
                    }
                    return e($r->reference_no ?? '-');
                })
                ->addColumn('note', fn($r) => e($r->note ?? '-'))
                ->rawColumns(['transaction_no', 'type_badge', 'amount', 'balance', 'method', 'reference'])
                ->make(true);
        }

        return view('admin.dealer_wallet.show', compact('dealer', 'summary'));
    }

    public function deposit(Request $request, $id)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Cash,Bank,bKash,Nagad,Rocket',
            'payment_date'   => 'required|date',
            'transaction_id' => 'nullable|string|max:191',
            'bank_name'      => 'nullable|string|max:191',
            'account_no'     => 'nullable|string|max:191',
            'note'           => 'nullable|string|max:1000',
        ]);

        try {
            $result = $this->walletService->deposit(
                (int) $id,
                (float) $request->amount,
                $request->payment_method,
                [
                    'payment_date'   => $request->payment_date,
                    'transaction_id' => $request->transaction_id,
                    'bank_name'      => $request->bank_name,
                    'account_no'     => $request->account_no,
                    'note'           => $request->note,
                    'created_by'     => auth('admin')->id(),
                ]
            );

            $settledCount = count($result['settled_orders'] ?? []);
            $msg = '৳ ' . number_format($request->amount, 2) . ' credited to dealer wallet.';
            if ($settledCount > 0) {
                $msg .= ' Automatically settled ' . $settledCount . ' delivered order(s) via FIFO!';
            }

            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
