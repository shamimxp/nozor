<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkerPayment;
use App\Models\Worker;
use App\Models\Manufacture;

class WorkerPaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = WorkerPayment::with(['worker', 'manufacture.product', 'creator'])->latest();

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereDate('date', '>=', $request->from_date)
                      ->whereDate('date', '<=', $request->to_date);
            }

            if ($request->filled('worker_id')) {
                $query->where('worker_id', $request->worker_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    return $row->date ? date('d M Y', strtotime($row->date)) : 'N/A';
                })
                ->addColumn('worker_name', function ($row) {
                    $name = $row->worker->name ?? 'N/A';
                    $phone = $row->worker->phone ?? '';
                    return '<div><strong>' . $name . '</strong></div>' . ($phone ? '<small class="text-bold">' . $phone . '</small>' : '');
                })
                ->addColumn('invoice_no', function ($row) {
                    return $row->manufacture->invoice_no ?? 'N/A';
                })
                ->addColumn('product', function ($row) {
                    return $row->manufacture->product->name ?? 'N/A';
                })
                ->addColumn('total_amount', function ($row) {
                    return number_format($row->total_amount, 2);
                })
                ->addColumn('status', function ($row) {
                    $checked = ($row->status === 'paid') ? 'checked' : '';
                    return '<div class="custom-control custom-switch custom-switch-success">
                                <input type="checkbox" class="custom-control-input change-status" id="status_'.$row->id.'" data-id="'.$row->id.'" data-field="status" '.$checked.'>
                                <label class="custom-control-label" for="status_'.$row->id.'">
                                    <span class="switch-icon-left"><i data-feather="check"></i></span>
                                    <span class="switch-icon-right"><i data-feather="x"></i></span>
                                </label>
                            </div>';
                })
                ->addColumn('created_by', function ($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger delete-record" data-id="'.$row->id.'" title="Delete"><i data-feather="trash-2"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['worker_name', 'status', 'action'])
                ->make(true);
        }

        $workers = Worker::all();
        $page_title = 'Worker Payments';
        
        return view('admin.worker_payment.index', compact('workers', 'page_title'));
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:worker_payments,id',
            'value' => 'required|boolean'
        ]);

        $payment = WorkerPayment::find($request->id);
        
        $payment->status = $request->value ? 'paid' : 'pending';
        $payment->save();

        return response()->json(['success' => 'Payment status updated successfully']);
    }

    public function destroy($id)
    {
        $payment = WorkerPayment::findOrFail($id);
        $payment->delete();
        
        return response()->json(['success' => 'Worker payment record deleted successfully']);
    }
}
