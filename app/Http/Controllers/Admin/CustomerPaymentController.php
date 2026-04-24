<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\CustomOrder;
use App\Models\PosOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerPaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $payments = CustomerPayment::with(['customer', 'payable', 'creator'])->latest()->get();
            return DataTables::of($payments)
                ->addIndexColumn()
                ->addColumn('customer_info', function($r) {
                    if ($r->customer) {
                        return '<strong>'.$r->customer->name.'</strong><br><small>'.$r->customer->phone.'</small>';
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('payment_for', function($r) {
                    $label = $r->payment_for ?? 'Other';
                    $orderNum = '';
                    if ($r->payable) {
                        $num = $r->payable->order_number ?? $r->payable->id;
                        $orderNum = ' <small class="text-muted">(#'.$num.')</small>';
                    }
                    return '<span class="badge badge-light-primary text-uppercase">'.str_replace('_', ' ', $label).'</span>'.$orderNum;
                })
                ->addColumn('amount', fn($r) => '৳' . number_format($r->amount, 2))
                ->addColumn('date', fn($r) => $r->payment_date ? $r->payment_date->format('d M, Y') : '-')
                ->addColumn('method', fn($r) => '<span class="badge badge-light-secondary">'.$r->payment_method.'</span>')
                ->rawColumns(['customer_info', 'payment_for', 'method'])
                ->make(true);
        }
        return view('admin.due-collection.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'    => 'required',
            'amount'         => 'required|numeric|min:0.01',
            'payment_date'   => 'required|date',
            'payment_method' => 'required',
            'payable_id'     => 'required',
            'payable_type'   => 'required|in:customOrder,POSOrder',
        ]);

        try {
            DB::beginTransaction();

            $payableModel    = null;
            $paymentForLabel = '';

            if ($request->payable_type == 'customOrder') {
                $payableModel = CustomOrder::findOrFail($request->payable_id);
                $paymentForLabel = 'custom_order';

                if ($request->amount > ($payableModel->due + 0.01)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Amount exceeds due balance (৳'.number_format($payableModel->due, 2).')'
                    ], 422);
                }
                $payableModel->increment('paid', $request->amount);
                $payableModel->decrement('due', $request->amount);

            } elseif ($request->payable_type == 'POSOrder') {
                $payableModel = PosOrder::findOrFail($request->payable_id);
                $paymentForLabel = 'pos_order';

                if ($request->amount > ($payableModel->due_amount + 0.01)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Amount exceeds due balance (৳'.number_format($payableModel->due_amount, 2).')'
                    ], 422);
                }
                $payableModel->increment('paid_amount', $request->amount);
                $payableModel->decrement('due_amount', $request->amount);

                $freshOrder = $payableModel->fresh();
                if ($freshOrder->due_amount <= 0) {
                    $payableModel->update(['payment_status' => 'paid']);
                } else {
                    $payableModel->update(['payment_status' => 'partial']);
                }
            }

            CustomerPayment::create([
                'customer_id'    => $request->customer_id,
                'payable_id'     => $payableModel->id,
                'payable_type'   => get_class($payableModel),
                'amount'         => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_date'   => $request->payment_date,
                'payment_for'    => $paymentForLabel,
                'note'           => $request->note,
                'created_by'     => auth('admin')->id(),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Payment recorded successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function customerHistory($id)
    {
        $customer = Customer::findOrFail($id);
        $payments = CustomerPayment::where('customer_id', $id)
            ->with(['payable', 'creator'])
            ->latest()
            ->get();
        return view('admin.due-collection.customer_history', compact('customer', 'payments'));
    }
}
