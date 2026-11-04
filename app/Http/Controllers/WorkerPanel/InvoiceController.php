<?php

namespace App\Http\Controllers\WorkerPanel;

use App\Http\Controllers\Controller;
use App\Models\WorkerPayment;
use App\Models\WorkerPayout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $worker = Auth::guard('worker')->user();

        if ($request->ajax()) {
            $payments = WorkerPayment::with(['manufacture.items.product', 'manufacture.product', 'creator'])
                ->where('worker_id', $worker->id)
                ->latest();

            return DataTables::of($payments)
                ->addIndexColumn()
                ->addColumn('date', fn ($row) => $row->date ? date('d M Y', strtotime($row->date)) : 'N/A')
                ->addColumn('invoice_no', function ($row) {
                    if (! $row->manufacture) {
                        return 'N/A';
                    }

                    return '<a href="' . route('mypanel.manufactures.show', $row->manufacture->id) . '" class="badge badge-light-primary font-weight-bolder">' . e($row->manufacture->invoice_no) . '</a>';
                })
                ->addColumn('product', function ($row) {
                    $m = $row->manufacture;
                    $firstItem = $m ? $m->items->first() : null;
                    $p = $firstItem ? $firstItem->product : ($m ? $m->product : null);

                    return $p ? '<strong class="text-dark">' . e($p->name) . '</strong>' : 'N/A';
                })
                ->addColumn('part_type', function ($row) {
                    $m = $row->manufacture;
                    $firstItem = $m ? $m->items->first() : null;
                    $type = strtolower($firstItem ? ($firstItem->part_type ?? '') : '');
                    if (! $type && $m) {
                        if ($m->body_total > 0 && $m->finishing_total > 0) {
                            $type = 'both';
                        } elseif ($m->body_total > 0) {
                            $type = 'body';
                        } elseif ($m->finishing_total > 0) {
                            $type = 'finishing';
                        }
                    }
                    if (in_array($type, ['body', 'body part'], true)) {
                        return '<span class="badge badge-light-info font-weight-bold">Body Part</span>';
                    }
                    if (in_array($type, ['finishing', 'finishing part'], true)) {
                        return '<span class="badge badge-light-primary font-weight-bold">Finishing Part</span>';
                    }
                    if ($type === 'both') {
                        return '<span class="badge badge-light-success font-weight-bold">Both</span>';
                    }

                    return '<span class="badge badge-light-secondary font-weight-bold">' . e(ucfirst($type ?: 'N/A')) . '</span>';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status == 'paid' || $row->due_amount <= 0) {
                        return '<span class="badge badge-light-success font-weight-bold">Paid</span>';
                    }
                    if ($row->status == 'partially_paid' || ($row->paid_amount > 0 && $row->due_amount > 0)) {
                        return '<span class="badge badge-light-info font-weight-bold">Partially Paid</span>';
                    }

                    return '<span class="badge badge-light-warning font-weight-bold">Pending</span>';
                })
                ->addColumn('total_amount', fn ($row) => '৳ ' . number_format($row->total_amount, 2))
                ->addColumn('paid_amount', fn ($row) => '৳ ' . number_format($row->paid_amount, 2))
                ->addColumn('due_amount', fn ($row) => '৳ ' . number_format($row->due_amount, 2))
                ->rawColumns(['status', 'invoice_no', 'product', 'part_type'])
                ->make(true);
        }

        $totalAmount = (float) WorkerPayment::where('worker_id', $worker->id)->sum('total_amount');
        $totalPaid = (float) WorkerPayment::where('worker_id', $worker->id)->sum('paid_amount');
        $totalDue = (float) WorkerPayment::where('worker_id', $worker->id)->sum('due_amount');
        $payoutCount = WorkerPayout::where('worker_id', $worker->id)->count();

        return view('mypanel.invoices.index', compact('worker', 'totalAmount', 'totalPaid', 'totalDue', 'payoutCount'));
    }

    public function payments(Request $request)
    {
        $worker = Auth::guard('worker')->user();

        if ($request->ajax()) {
            $payouts = WorkerPayout::with('items')
                ->where('worker_id', $worker->id)
                ->latest('id');

            return DataTables::of($payouts)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    if ($row->payment_date) {
                        return $row->payment_date->format('d M Y');
                    }

                    return $row->created_at ? $row->created_at->format('d M Y') : 'N/A';
                })
                ->addColumn('payment_number', fn ($row) => e($row->payment_number ?: ('PAY-' . $row->id)))
                ->addColumn('amount', fn ($row) => '<strong class="text-success">৳ ' . number_format($row->amount, 2) . '</strong>')
                ->addColumn('method', fn ($row) => e($row->payment_method ?: 'N/A'))
                ->addColumn('note', fn ($row) => e($row->note ?: '—'))
                ->rawColumns(['amount'])
                ->make(true);
        }

        return view('mypanel.invoices.payments', compact('worker'));
    }
}
