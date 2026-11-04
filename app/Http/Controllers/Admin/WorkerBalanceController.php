<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesTrash;
use App\Http\Controllers\Controller;
use App\Helpers\NumberToWords;
use App\Models\Worker;
use App\Models\WorkerPayment;
use App\Models\WorkerPayout;
use App\Models\WorkerPayoutItem;
use App\Services\Deletion\SoftDeleteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class WorkerBalanceController extends Controller
{
    use HandlesTrash;

    protected function trashModuleKey(): string
    {
        return 'worker_payout';
    }

    /**
     * Display Worker Balance Sheet
     */
    public function balanceSheet(Request $request)
    {
        if ($request->ajax()) {
            $workers = Worker::with(['invoices'])->latest('id')->get();

            return DataTables::of($workers)
                ->addIndexColumn()
                ->addColumn('worker_info', function ($worker) {
                    $name = e($worker->name);
                    return "<strong class='text-dark'>{$name}</strong>";
                })
                ->addColumn('phone', function ($worker) {
                    return $worker->phone ? '<a href="tel:' . e($worker->phone) . '" class="text-dark font-weight-bold">' . e($worker->phone) . '</a>' : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('total_earned', function ($worker) {
                    $earned = $worker->invoices->sum('total_amount');
                    return '<span class="font-weight-bold">৳ ' . number_format($earned, 2) . '</span>';
                })
                ->addColumn('total_paid', function ($worker) {
                    $paid = $worker->invoices->sum('paid_amount');
                    return '<span class="text-success font-weight-bold">৳ ' . number_format($paid, 2) . '</span>';
                })
                ->addColumn('balance', function ($worker) {
                    $balance = $worker->invoices->where('due_amount', '>', 0)->sum('due_amount');
                    if ($balance > 0) {
                        return '<strong class="text-danger" style="font-size: 14px;">৳ ' . number_format($balance, 2) . '</strong>';
                    }
                    return '<span class="badge badge-light-success font-weight-bold">৳ 0.00 (Clear)</span>';
                })
                ->addColumn('status', function ($worker) {
                    if ($worker->status == 1 || strtolower($worker->status) === 'active') {
                        return '<span class="badge badge-pill badge-light-success font-weight-bold">Active</span>';
                    }
                    return '<span class="badge badge-pill badge-light-danger font-weight-bold">Inactive</span>';
                })
                ->addColumn('action', function ($worker) {
                    $balance = (float) $worker->invoices->where('due_amount', '>', 0)->sum('due_amount');
                    if ($balance > 0) {
                        return '<button type="button" class="btn btn-sm make-payment-btn btn-pay-active"
                                    data-id="' . $worker->id . '"
                                    data-name="' . e($worker->name) . '"
                                    data-phone="' . e($worker->phone) . '"
                                    data-balance="' . $balance . '"
                                    title="Make Payment to ' . e($worker->name) . '">
                                    <i data-feather="dollar-sign"></i> Make Payment
                                </button>';
                    }

                    return '<button type="button" class="btn btn-sm make-payment-btn btn-pay-clear" disabled
                                title="All dues are cleared for ' . e($worker->name) . '">
                                <i data-feather="check-circle"></i> All Clear
                            </button>';
                })
                ->rawColumns(['worker_info', 'phone', 'total_earned', 'total_paid', 'balance', 'status', 'action'])
                ->make(true);
        }

        $page_title = 'Worker Balance Sheet';
        return view('admin.worker_balance.index', compact('page_title'));
    }

    /**
     * Store a payment made to a worker and allocate to his unpaid invoices (ASC order)
     */
    public function storePayout(Request $request)
    {
        $request->validate([
            'worker_id'       => 'required|exists:workers,id',
            'amount'          => 'required|numeric|min:0.01',
            'payment_date'    => 'required|date',
            'payment_method'  => 'required|in:Cash,Bank,bKash,Nagad,Rocket',
            'account_details' => 'nullable|string|max:191',
            'note'            => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $worker = Worker::findOrFail($request->worker_id);

            // Fetch unpaid or partially paid invoices in ascending date order (1,2,3,4...)
            $invoices = WorkerPayment::where('worker_id', $worker->id)
                ->where('due_amount', '>', 0)
                ->orderBy('date', 'asc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->get();

            $totalBalance = (float) $invoices->sum('due_amount');

            if ($request->amount > ($totalBalance + 0.01)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount (৳' . number_format($request->amount, 2) . ') cannot exceed worker total balance (৳' . number_format($totalBalance, 2) . ').'
                ], 422);
            }

            $remainingBalance = max(0, $totalBalance - (float) $request->amount);

            // 1. Create Worker Payout record
            $payout = WorkerPayout::create([
                'payment_number'    => WorkerPayout::generatePaymentNumber(),
                'worker_id'         => $worker->id,
                'payment_date'      => $request->payment_date,
                'total_balance'     => $totalBalance,
                'amount'            => $request->amount,
                'remaining_balance' => $remainingBalance,
                'payment_method'    => $request->payment_method,
                'account_details'   => $request->account_details,
                'note'              => $request->note,
                'created_by'        => auth()->id(),
            ]);

            // 2. Allocate payment amount to invoices in ASC order
            $remainingToPay = (float) $request->amount;

            foreach ($invoices as $invoice) {
                if ($remainingToPay <= 0) {
                    break;
                }

                $due = (float) $invoice->due_amount;
                $payForThis = min($remainingToPay, $due);

                // Create detail item
                WorkerPayoutItem::create([
                    'worker_payout_id'  => $payout->id,
                    'worker_payment_id' => $invoice->id,
                    'amount'            => $payForThis,
                ]);

                $invoice->paid_amount += $payForThis;
                $invoice->due_amount  -= $payForThis;

                if ($invoice->due_amount <= 0.001) {
                    $invoice->status = 'paid';
                    $invoice->due_amount = 0;
                } else {
                    $invoice->status = 'partially_paid';
                }
                $invoice->save();

                $remainingToPay -= $payForThis;
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Payment of ৳' . number_format($payout->amount, 2) . ' recorded successfully for ' . $worker->name . '!',
                'payout_id'    => $payout->id,
                'show_url'     => route('admin.worker-payouts.show', $payout->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error recording payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display Worker Payment List
     */
    public function paymentList(Request $request)
    {
        if ($request->ajax()) {
            $payouts = WorkerPayout::with(['worker', 'creator'])->latest('id');

            if ($request->filled('worker_id')) {
                $payouts->where('worker_id', $request->worker_id);
            }
            if ($request->filled('payment_method')) {
                $payouts->where('payment_method', $request->payment_method);
            }
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $payouts->whereDate('payment_date', '>=', $request->from_date)
                        ->whereDate('payment_date', '<=', $request->to_date);
            }

            return DataTables::of($payouts)
                ->addIndexColumn()
                ->filterColumn('worker_info', function ($query, $keyword) {
                    $query->whereHas('worker', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                          ->orWhere('phone', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('created_by', function ($query, $keyword) {
                    $query->whereHas('creator', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('token_no', function ($query, $keyword) {
                    $query->where('payment_number', 'like', "%{$keyword}%");
                })
                ->filterColumn('date', function ($query, $keyword) {
                    $query->where('payment_date', 'like', "%{$keyword}%");
                })
                ->addColumn('token_no', function ($row) {
                    return '<a href="' . route('admin.worker-payouts.show', $row->id) . '" class="badge badge-light-primary font-weight-bolder" style="font-size: 11px;">' . e($row->payment_number) . '</a>';
                })
                ->addColumn('date', function ($row) {
                    return $row->payment_date ? $row->payment_date->format('d M Y') : 'N/A';
                })
                ->addColumn('worker_info', function ($row) {
                    $name = $row->worker->name ?? 'N/A';
                    $phone = $row->worker->phone ?? '';
                    return '<div><strong>' . e($name) . '</strong></div>' . ($phone ? '<small class="text-muted">' . e($phone) . '</small>' : '');
                })
                ->addColumn('total_balance', function ($row) {
                    return '<span class="font-weight-bold text-dark">৳ ' . number_format($row->total_balance, 2) . '</span>';
                })
                ->addColumn('amount', function ($row) {
                    return '<strong class="text-success font-weight-bolder" style="font-size: 13px;">৳ ' . number_format($row->amount, 2) . '</strong>';
                })
                ->addColumn('remaining_balance', function ($row) {
                    return '<span class="text-muted font-weight-bold">৳ ' . number_format($row->remaining_balance, 2) . '</span>';
                })
                ->addColumn('method', function ($row) {
                    $method = $row->payment_method;
                    $logo = asset('images/payments/' . strtolower($method) . '.svg');
                    $details = $row->account_details ? '<br><small class="text-muted">' . e($row->account_details) . '</small>' : '';
                    return '<div class="d-flex align-items-center"><img src="' . $logo . '" alt="' . e($method) . '" style="width: 20px; height: 20px; margin-right: 6px; object-fit: contain;"> <span class="font-weight-bold">' . e($method) . '</span></div>' . $details;
                })
                ->addColumn('created_by', function ($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<a href="' . route('admin.worker-payouts.show', $row->id) . '" class="btn btn-sm btn-info" title="View Cheque Token"><i data-feather="file-text"></i> Token</a>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger delete-payout" data-id="' . $row->id . '" title="Delete & Revert to Balance"><i data-feather="trash-2"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['token_no', 'worker_info', 'total_balance', 'amount', 'remaining_balance', 'method', 'action'])
                ->make(true);
        }

        $workers = Worker::all();
        $page_title = 'Worker Payment List';
        return view('admin.worker_balance.payments', compact('workers', 'page_title'));
    }

    /**
     * Show Payment Token (Bank Cheque Style)
     */
    public function showPayout($id)
    {
        $payout = WorkerPayout::with(['worker', 'creator', 'items.invoice.manufacture'])->findOrFail($id);
        $amountInWords = NumberToWords::convert($payout->amount);
        $page_title = 'Payment Token - ' . $payout->payment_number;

        return view('admin.worker_balance.show_token', compact('payout', 'amountInWords', 'page_title'));
    }

    /**
     * Download Payment Token as PDF
     */
    public function downloadTokenPdf($id)
    {
        $payout = WorkerPayout::with(['worker', 'creator', 'items.invoice.manufacture'])->findOrFail($id);
        $amountInWords = NumberToWords::convert($payout->amount);

        $pdf = Pdf::loadView('admin.worker_balance.token_pdf', compact('payout', 'amountInWords'));
        $pdf->setPaper([0, 0, 842, 346], 'landscape');

        $fileName = 'Payment-Token-' . ($payout->payment_number ?: $payout->id) . '.pdf';
        return $pdf->download($fileName);
    }

    /**
     * Delete payment history and revert the paid amounts on worker's invoices back to due balance
     */
    public function destroyPayout($id)
    {
        try {
            DB::beginTransaction();

            $payout = WorkerPayout::with('items.invoice')->findOrFail($id);
            $revertedAmount = (float) $payout->amount;

            // Revert each invoice that received a portion of this payout
            foreach ($payout->items as $item) {
                $invoice = $item->invoice;
                if ($invoice) {
                    $invoice->paid_amount = max(0, (float)$invoice->paid_amount - (float)$item->amount);
                    $invoice->due_amount  = min((float)$invoice->total_amount, (float)$invoice->due_amount + (float)$item->amount);

                    if ($invoice->paid_amount <= 0.001) {
                        $invoice->status = 'pending';
                        $invoice->paid_amount = 0;
                    } else {
                        $invoice->status = 'partially_paid';
                    }
                    $invoice->save();
                }
            }

            $softDeleteService = app(SoftDeleteService::class);
            $result = $softDeleteService->delete($payout, $this->trashModuleKey());
            if (! $result['success']) {
                DB::rollBack();
                return response()->json($result, 422);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment history moved to trash! ৳' . number_format($revertedAmount, 2) . ' has been added back to worker balance.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error deleting payment record: ' . $e->getMessage()
            ], 500);
        }
    }
}
