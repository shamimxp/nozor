<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomOrder;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Vendor;
use App\Models\VendorPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Purchase::with(['vendor', 'customOrder'])->latest();

            if ($request->purchase_number) {
                $query->where('purchase_number', 'LIKE', '%' . $request->purchase_number . '%');
            }
            if ($request->order_number) {
                $query->whereHas('customOrder', function ($q) use ($request) {
                    $q->where('order_number', 'LIKE', '%' . $request->order_number . '%');
                });
            }
            if ($request->vendor_phone) {
                $query->whereHas('vendor', function ($q) use ($request) {
                    $q->where('phone', 'LIKE', '%' . $request->vendor_phone . '%');
                });
            }
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $purchases = $query->get();
            return DataTables::of($purchases)
                ->addIndexColumn()
                ->addColumn('purchase_info', function($r) {
                    return '<strong>ID:</strong> ' . $r->purchase_number . '<br>' .
                           '<strong>Order:</strong> ' . $r->customOrder->order_number . '<br>' .
                           '<strong>Style:</strong> ' . $r->style_number;
                })
                ->addColumn('vendor_info', function($r) {
                    return '<strong>' . $r->vendor->name . '</strong><br>' .
                           '<small>' . $r->vendor->phone . '</small>';
                })
                ->addColumn('financials', function($r) {
                    return 'Total: ৳' . number_format($r->grand_total, 2) . '<br>' .
                           'Paid: ৳' . number_format($r->paid_amount, 2) . '<br>' .
                           '<small class="' . ($r->due_amount > 0 ? 'text-danger' : 'text-success') . '">Due: ৳' . number_format($r->due_amount, 2) . '</small>';
                })
                ->addColumn('status', function($r) {
                    $class = [
                        'pending' => 'warning',
                        'confirm' => 'info',
                        'received' => 'success'
                    ][$r->status] ?? 'secondary';
                    return '<span class="badge badge-light-' . $class . ' text-uppercase">' . $r->status . '</span>';
                })
                ->addColumn('action', function($r) {
                    $btn = '<div class="d-flex">';
                    $btn .= '<a href="' . route('admin.purchase.show', $r->id) . '" class="btn btn-sm btn-info mr-50"><i data-feather="eye"></i></a>';
                    if ($r->status != 'received') {
                        $btn .= '<a href="' . route('admin.purchase.edit', $r->id) . '" class="btn btn-sm btn-primary mr-50"><i data-feather="edit"></i></a>';
                    }
                    $btn .= '<a href="javascript:void(0)" data-id="' . $r->id . '" class="btn btn-sm btn-danger deletePurchase"><i data-feather="trash"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['purchase_info', 'vendor_info', 'financials', 'status', 'action'])
                ->make(true);
        }
        return view('admin.purchase.index');
    }

    public function create(Request $request)
    {
        $customOrderId = $request->custom_order_id;
        $customOrder = null;
        if ($customOrderId) {
            $customOrder = CustomOrder::with('items.fabricPrice.fabric')->findOrFail($customOrderId);
        }

        // Get custom_order_ids that already have a purchase order
        $existingPurchaseOrderIds = Purchase::pluck('custom_order_id')->filter()->toArray();

        // Exclude already-purchased custom orders, but keep the currently selected one
        $customOrders = CustomOrder::whereNotIn('status', ['delivered', 'cancelled'])
            ->where(function ($q) use ($existingPurchaseOrderIds, $customOrder) {
                $q->whereNotIn('id', $existingPurchaseOrderIds);
                if ($customOrder) {
                    $q->orWhere('id', $customOrder->id);
                }
            })
            ->latest()->get();

        $vendors = Vendor::orderBy('name')->get();
        $purchaseNumber = Purchase::generatePurchaseNumber();

        return view('admin.purchase.create', compact('customOrder', 'customOrders', 'vendors', 'purchaseNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'custom_order_id' => 'required|exists:custom_orders,id',
            'vendor_id' => 'required|exists:vendors,id',
            'items' => 'required|array|min:1',
            'items.*.fabric_price_id' => 'required|exists:fabric_prices,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'received_date' => 'required|date',
            'status' => 'required|in:pending,confirm,received',
        ]);

        try {
            DB::beginTransaction();

            $subTotal = 0;
            foreach ($request->items as $item) {
                $subTotal += $item['quantity'] * $item['unit_cost'];
            }

            $carryingCharge = $request->carrying_charge ?? 0;
            $grandTotal = $subTotal + $carryingCharge;
            $paidAmount = $request->paid_amount ?? 0;
            $dueAmount = $grandTotal - $paidAmount;

            $customOrder = CustomOrder::findOrFail($request->custom_order_id);

            $purchase = Purchase::create([
                'purchase_number' => Purchase::generatePurchaseNumber(),
                'custom_order_id' => $request->custom_order_id,
                'style_number' => $customOrder->style_number,
                'vendor_id' => $request->vendor_id,
                'sub_total' => $subTotal,
                'carrying_charge' => $carryingCharge,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'status' => $request->status,
                'received_date' => $request->received_date,
                'created_by' => auth('admin')->id(),
            ]);

            foreach ($request->items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'fabric_price_id' => $item['fabric_price_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['quantity'] * $item['unit_cost'],
                ]);
            }

            if ($paidAmount > 0) {
                VendorPayment::create([
                    'vendor_id' => $request->vendor_id,
                    'purchase_id' => $purchase->id,
                    'amount' => $paidAmount,
                    'payment_date' => now(),
                    'payment_method' => $request->payment_method ?? 'Cash',
                    'note' => 'Initial payment for Purchase #' . $purchase->purchase_number,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Purchase order created successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['vendor', 'customOrder', 'items.fabricPrice.fabric', 'payments', 'creator'])->findOrFail($id);
        return view('admin.purchase.show', compact('purchase'));
    }

    public function dueList(Request $request)
    {
        if ($request->ajax()) {
            $purchases = Purchase::with('vendor')->where('due_amount', '>', 0)->latest()->get();
            return DataTables::of($purchases)
                ->addIndexColumn()
                ->addColumn('purchase_info', function($r) {
                    return '<strong>' . $r->purchase_number . '</strong><br>' .
                           '<small>Style: ' . $r->style_number . '</small>';
                })
                ->addColumn('vendor_name', function($r) {
                    return '<strong>' . $r->vendor->name . '</strong><br>' .
                           '<small>' . ($r->vendor->company_name ?? '') . '</small>';
                })
                ->addColumn('financials', function($r) {
                    return 'Total: ৳' . number_format($r->grand_total, 2) . '<br>' .
                           'Paid: ৳' . number_format($r->paid_amount, 2) . '<br>' .
                           '<strong class="text-danger">Due: ৳' . number_format($r->due_amount, 2) . '</strong>';
                })
                ->addColumn('status', function($r) {
                    $class = ['pending' => 'warning', 'confirm' => 'info', 'received' => 'success'][$r->status] ?? 'secondary';
                    return '<span class="badge badge-light-' . $class . ' text-uppercase">' . $r->status . '</span>';
                })
                ->addColumn('action', function($r) {
                    $btn = '<a href="' . route('admin.purchase.show', $r->id) . '" class="btn btn-sm btn-info mr-25">View</a>';
                    $btn .= '<button type="button" class="btn btn-sm btn-success payBtn ml-25" 
                                data-id="'.$r->id.'" 
                                data-vendor-name="'.$r->vendor->name.'" 
                                data-purchase-number="'.$r->purchase_number.'" 
                                data-due="'.$r->due_amount.'">Pay</button>';
                    return $btn;
                })
                ->rawColumns(['purchase_info', 'vendor_name', 'financials', 'status', 'action'])
                ->make(true);
        }
        return view('admin.purchase.due_list');
    }

    public function vendorHistory(Request $request)
    {
        if ($request->ajax()) {
            $vendors = Vendor::all();
            $data = [];
            foreach ($vendors as $vendor) {
                $total_purchased = Purchase::where('vendor_id', $vendor->id)->sum('grand_total');
                $total_paid = VendorPayment::where('vendor_id', $vendor->id)->sum('amount');
                $total_due = $total_purchased - $total_paid;

                $data[] = [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'company' => $vendor->company_name,
                    'phone' => $vendor->phone,
                    'total_purchased' => number_format($total_purchased, 2),
                    'total_paid' => number_format($total_paid, 2),
                    'total_due' => number_format($total_due, 2),
                ];
            }
            return DataTables::of($data)->addIndexColumn()->make(true);
        }
        return view('admin.purchase.vendor_history');
    }

    public function vendorPaymentDetails($id)
    {
        $vendor = Vendor::findOrFail($id);
        $payments = VendorPayment::with('purchase')->where('vendor_id', $id)->latest()->get();
        return view('admin.purchase.vendor_payment_details', compact('vendor', 'payments'));
    }

    public function addPayment(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            $purchase = Purchase::findOrFail($request->purchase_id);
            if ($request->amount > $purchase->due_amount) {
                return response()->json(['success' => false, 'message' => 'Payment amount cannot exceed due amount!'], 422);
            }

            VendorPayment::create([
                'vendor_id' => $purchase->vendor_id,
                'purchase_id' => $purchase->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method ?? 'Cash',
                'note' => $request->note,
            ]);

            $purchase->increment('paid_amount', $request->amount);
            $purchase->decrement('due_amount', $request->amount);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Payment added successfully!']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $purchase = Purchase::with(['items.fabricPrice.fabric', 'customOrder.items.fabricPrice.fabric'])->findOrFail($id);
        $vendors = Vendor::orderBy('name')->get();
        return view('admin.purchase.edit', compact('purchase', 'vendors'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'items' => 'required|array|min:1',
            'items.*.fabric_price_id' => 'required|exists:fabric_prices,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'received_date' => 'required|date',
            'status' => 'required|in:pending,confirm,received',
        ]);

        try {
            DB::beginTransaction();

            $purchase = Purchase::findOrFail($id);

            $subTotal = 0;
            foreach ($request->items as $item) {
                $subTotal += $item['quantity'] * $item['unit_cost'];
            }

            $carryingCharge = $request->carrying_charge ?? 0;
            $grandTotal = $subTotal + $carryingCharge;
            $totalPaid = $purchase->paid_amount;
            $dueAmount = $grandTotal - $totalPaid;

            $purchase->update([
                'vendor_id' => $request->vendor_id,
                'sub_total' => $subTotal,
                'carrying_charge' => $carryingCharge,
                'grand_total' => $grandTotal,
                'due_amount' => $dueAmount,
                'status' => $request->status,
                'received_date' => $request->received_date,
            ]);

            $purchase->items()->delete();
            foreach ($request->items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'fabric_price_id' => $item['fabric_price_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['quantity'] * $item['unit_cost'],
                ]);
            }

            DB::commit();
            toastr()->success('Purchase updated successfully.');
            return redirect()->route('admin.purchase.index');

        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Error: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $purchase = Purchase::findOrFail($id);
            $purchase->delete();
            return response()->json(['success' => true, 'message' => 'Purchase deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Export Purchase List to CSV/Excel
     */
    public function exportExcel(Request $request)
    {
        $fileName = 'purchases_' . date('Y-m-d') . '.csv';
        $query = Purchase::with(['vendor', 'customOrder'])->latest();

        if ($request->purchase_number) {
            $query->where('purchase_number', 'LIKE', '%' . $request->purchase_number . '%');
        }
        if ($request->order_number) {
            $query->whereHas('customOrder', fn($q) => $q->where('order_number', 'LIKE', '%' . $request->order_number . '%'));
        }
        if ($request->vendor_phone) {
            $query->whereHas('vendor', fn($q) => $q->where('phone', 'LIKE', '%' . $request->vendor_phone . '%'));
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $purchases = $query->get();

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $fileName,
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = ['Purchase No', 'Order No', 'Style No', 'Received Date', 'Vendor', 'Vendor Phone', 'Sub Total', 'Carrying Charge', 'Grand Total', 'Paid', 'Due', 'Status'];

        $callback = function () use ($purchases, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($purchases as $p) {
                fputcsv($file, [
                    $p->purchase_number,
                    $p->customOrder->order_number ?? 'N/A',
                    $p->style_number,
                    $p->received_date ? $p->received_date->format('Y-m-d') : '',
                    $p->vendor->name ?? 'N/A',
                    $p->vendor->phone ?? 'N/A',
                    $p->sub_total,
                    $p->carrying_charge,
                    $p->grand_total,
                    $p->paid_amount,
                    $p->due_amount,
                    strtoupper($p->status),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Purchase List to PDF
     */
    public function exportListPdf(Request $request)
    {
        $query = Purchase::with(['vendor', 'customOrder'])->latest();

        if ($request->purchase_number) {
            $query->where('purchase_number', 'LIKE', '%' . $request->purchase_number . '%');
        }
        if ($request->order_number) {
            $query->whereHas('customOrder', fn($q) => $q->where('order_number', 'LIKE', '%' . $request->order_number . '%'));
        }
        if ($request->vendor_phone) {
            $query->whereHas('vendor', fn($q) => $q->where('phone', 'LIKE', '%' . $request->vendor_phone . '%'));
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $purchases = $query->get();
        $filters = [
            'purchase_number' => $request->purchase_number,
            'order_number'    => $request->order_number,
            'vendor_phone'    => $request->vendor_phone,
            'status'          => $request->status,
        ];

        $pdf = Pdf::loadView('admin.purchase.list_pdf', compact('purchases', 'filters'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('purchases_' . date('Y-m-d') . '.pdf');
    }
}
