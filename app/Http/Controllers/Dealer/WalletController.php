<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\DealerOrder;
use App\Models\DealerWalletTransaction;
use App\Models\DealerOrderPayment;
use App\Services\DealerWalletService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class WalletController extends Controller
{
    protected DealerWalletService $walletService;

    public function __construct(DealerWalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Dealer Wallet Dashboard & Statement
     */
    public function index(Request $request)
    {
        $dealerId = auth('dealer')->id();
        $dealer = Dealer::findOrFail($dealerId);
        $summary = $this->walletService->getDealerSummary($dealerId);

        if ($request->ajax()) {
            $query = DealerWalletTransaction::where('dealer_id', $dealerId)
                ->with(['dealerOrder', 'collection'])
                ->latest('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('transaction_no', fn($r) => '<span class="font-weight-bolder text-primary">#' . e($r->transaction_no) . '</span>')
                ->addColumn('date', fn($r) => $r->created_at->format('d M, Y h:i A'))
                ->addColumn('type_badge', function ($r) {
                    if ($r->type === 'credit') {
                        return '<span class="badge badge-light-success font-weight-bold"><i data-feather="arrow-down-left" class="mr-25"></i> Deposit (Credit)</span>';
                    }
                    return '<span class="badge badge-light-danger font-weight-bold"><i data-feather="arrow-up-right" class="mr-25"></i> Order Payment (Debit)</span>';
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
                        return '<a href="' . route('dealer.orders.show', $r->dealerOrder->id) . '" class="badge badge-light-primary">Order #' . e($r->dealerOrder->order_number) . '</a>';
                    }
                    return e($r->reference_no ?? '-');
                })
                ->addColumn('status_badge', function ($r) {
                    if ($r->collection) {
                        $st = $r->collection->status;
                        if ($st === 'accepted') {
                            return '<span class="badge badge-pill badge-light-success font-weight-bold"><i data-feather="check-circle" class="mr-25"></i> Accepted</span>';
                        } elseif ($st === 'pending') {
                            return '<span class="badge badge-pill badge-light-warning font-weight-bold"><i data-feather="clock" class="mr-25"></i> Pending</span>';
                        } elseif ($st === 'rejected') {
                            return '<span class="badge badge-pill badge-light-danger font-weight-bold"><i data-feather="x-circle" class="mr-25"></i> Rejected</span>';
                        }
                    }
                    if ($r->type === 'debit') {
                        return '<span class="badge badge-pill badge-light-info font-weight-bold"><i data-feather="check" class="mr-25"></i> Settled</span>';
                    }
                    return '<span class="badge badge-pill badge-light-success font-weight-bold"><i data-feather="check-circle" class="mr-25"></i> Accepted</span>';
                })
                ->addColumn('note', function ($r) {
                    $note = trim($r->note ?? '');
                    if (empty($note) || $note === '-') {
                        return '<span class="text-muted font-small-2">-</span>';
                    }

                    $escapedFullNote = e($note);
                    $escapedTxn = e($r->transaction_no ?? '-');
                    $escapedDate = e($r->created_at->format('d M, Y h:i A'));
                    $typeLabel = $r->type === 'credit' ? 'Deposit (Credit)' : 'Order Payment (Debit)';
                    $typeBadgeClass = $r->type === 'credit' ? 'badge-light-success' : 'badge-light-danger';

                    // If note is short (<= 32 chars), display directly
                    if (mb_strlen($note) <= 32) {
                        return '<span class="font-small-2 text-dark font-weight-500">' . $escapedFullNote . '</span>';
                    }

                    $shortNote = mb_substr($note, 0, 26) . '...';

                    return '<div class="d-flex align-items-center note-cell-wrapper" style="gap: 5px;">
                        <span class="font-small-2 text-dark font-weight-500 text-truncate" style="max-width: 140px;" title="' . $escapedFullNote . '">' . e($shortNote) . '</span>
                        <button type="button" class="btn btn-xs btn-outline-primary btn-show-note d-inline-flex align-items-center py-25 px-50"
                            style="border-radius: 4px; font-weight: 600; font-size: 11px; white-space: nowrap;"
                            data-txn="#' . $escapedTxn . '"
                            data-date="' . $escapedDate . '"
                            data-type="' . $typeLabel . '"
                            data-type-badge="' . $typeBadgeClass . '"
                            data-note="' . $escapedFullNote . '"
                            title="View Full Note">
                            <i data-feather="file-text" style="width: 11px; height: 11px; margin-right: 3px;"></i> View
                        </button>
                    </div>';
                })
                ->rawColumns(['transaction_no', 'type_badge', 'amount', 'balance', 'method', 'reference', 'status_badge', 'note'])
                ->make(true);
        }

        return view('dealer.wallet.index', compact('dealer', 'summary'));
    }

    /**
     * Dealer Order Payment History
     */
    public function payments(Request $request)
    {
        $dealerId = auth('dealer')->id();
        $dealer = Dealer::findOrFail($dealerId);
        $summary = $this->walletService->getDealerSummary($dealerId);

        if ($request->ajax()) {
            $query = DealerOrderPayment::whereHas('dealerOrder', function ($q) use ($dealerId) {
                $q->where('dealer_id', $dealerId);
            })->with(['dealerOrder'])->latest('id');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('order_number', function ($r) {
                    if ($r->dealerOrder) {
                        return '<a href="' . route('dealer.orders.show', $r->dealerOrder->id) . '" class="font-weight-bolder text-primary">#' . e($r->dealerOrder->order_number) . '</a>';
                    }
                    return '-';
                })
                ->addColumn('amount', fn($r) => '<strong class="text-success font-weight-bolder">৳ ' . number_format($r->amount, 2) . '</strong>')
                ->addColumn('date', fn($r) => $r->payment_date ? $r->payment_date->format('d M, Y') : '-')
                ->addColumn('note', fn($r) => e($r->note ?? 'Wallet Auto-Settlement'))
                ->rawColumns(['order_number', 'amount'])
                ->make(true);
        }

        return view('dealer.wallet.payments', compact('dealer', 'summary'));
    }
}
