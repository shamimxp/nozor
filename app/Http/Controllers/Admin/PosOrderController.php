<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PosOrder;
use App\Models\PosOrderItem;
use App\Models\Customer;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PosOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $orders = PosOrder::with('customer')->latest()->get();
            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('order_info', function($r) {
                    return '<strong>ID:</strong> ' . $r->order_number . '<br>' .
                           '<strong>Date:</strong> ' . date('d M, Y', strtotime($r->order_date));
                })
                ->addColumn('customer_info', function($r) {
                    if ($r->customer) {
                        return '<strong>' . $r->customer->name . '</strong><br>' .
                               '<small>' . $r->customer->phone . '</small>';
                    }
                    return '<span class="badge badge-light-secondary">Walk-in Customer</span>';
                })
                ->addColumn('payment_summary', function($r) {
                    return 'Total: ৳' . number_format($r->total_amount, 2) . '<br>' .
                           'Paid: ৳' . number_format($r->paid_amount, 2) . '<br>' .
                           '<small class="' . ($r->due_amount > 0 ? 'text-danger' : 'text-success') . '">Due: ৳' . number_format($r->due_amount, 2) . '</small>';
                })
                ->addColumn('status', function($r) {
                    $status_class = [
                        'completed' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger'
                    ][$r->order_status] ?? 'info';

                    return '<span class="badge badge-light-' . $status_class . ' text-uppercase">' . $r->order_status . '</span>';
                })
                ->addColumn('action', function($r) {
                    $btn = '<div class="d-flex">';
                    $btn .= '<a href="' . route('admin.pos-order.show', $r->id) . '" class="btn btn-sm btn-info mr-50" title="View"><i data-feather="eye"></i></a>';
                    $btn .= '<a href="' . route('admin.pos-order.export-pdf', $r->id) . '" class="btn btn-sm btn-secondary mr-50" title="Download PDF"><i data-feather="download"></i></a>';
                    if($r->order_status != 'cancelled') {
                        $btn .= '<a href="javascript:void(0)" data-id="' . $r->id . '" class="btn btn-sm btn-warning mr-50 cancelOrder" title="Cancel Order"><i data-feather="x-circle"></i></a>';
                    }
                    $btn .= '<a href="javascript:void(0)" data-id="' . $r->id . '" class="btn btn-sm btn-danger deleteOrder" title="Delete Permanent"><i data-feather="trash"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['order_info', 'customer_info', 'payment_summary', 'status', 'action'])
                ->make(true);
        }
        return view('admin.pos-order.index');
    }

    public function dueList(Request $request)
    {
        if ($request->ajax()) {
            $orders = PosOrder::with('customer')->where('due_amount', '>', 0)->where('order_status', '!=', 'cancelled')->latest()->get();
            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('order_info', function($r) {
                    return '<strong>' . $r->order_number . '</strong><br>' .
                           '<small>' . date('d M, Y', strtotime($r->order_date)) . '</small>';
                })
                ->addColumn('customer_info', function($r) {
                    return $r->customer ? '<strong>' . $r->customer->name . '</strong><br><small>' . $r->customer->phone . '</small>' : '<span class="text-muted">Walk-in</span>';
                })
                ->addColumn('financials', function($r) {
                    return '<strong class="text-danger">Due: ৳' . number_format($r->due_amount, 2) . '</strong>';
                })
                ->addColumn('action', function($r) {
                    return '<a href="' . route('admin.pos-order.show', $r->id) . '" class="btn btn-sm btn-info">View</a>';
                })
                ->rawColumns(['order_info', 'customer_info', 'financials', 'action'])
                ->make(true);
        }
        return view('admin.pos-order.due_list');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'total_amount' => 'required|numeric',
            'payable_amount' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $order = PosOrder::create([
                'order_number' => PosOrder::generateOrderNumber(),
                'customer_id' => $request->customer_id,
                'total_amount' => $request->total_amount,
                'discount_amount' => $request->discount_amount ?? 0,
                'payable_amount' => $request->payable_amount,
                'paid_amount' => $request->paid_amount ?? 0,
                'due_amount' => $request->due_amount ?? 0,
                'payment_method' => $request->payment_method ?? 'Cash',
                'payment_status' => ($request->due_amount <= 0) ? 'paid' : ($request->paid_amount > 0 ? 'partial' : 'due'),
                'order_status' => 'completed',
                'note' => $request->note,
                'staff_note' => $request->staff_note,
                'created_by' => auth('admin')->id(),
                'order_date' => now(),
            ]);

            foreach ($request->items as $item) {
                PosOrderItem::create([
                    'pos_order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'discount' => ($item['discountAmount'] ?? 0) * $item['quantity'],
                ]);

                // Update product stock
                $product = Product::find($item['id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = PosOrder::with(['customer', 'items.product', 'creator'])->findOrFail($id);
        return view('admin.pos-order.show', compact('order'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            $order = PosOrder::findOrFail($id);
            $order->items()->delete();
            $order->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    /**
     * POS Order Analysis/Report
     */
    public function analysis()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $query = PosOrder::whereMonth('order_date', $currentMonth)
                         ->whereYear('order_date', $currentYear);

        $total_sales = (clone $query)->sum('total_amount');
        $total_paid = (clone $query)->sum('paid_amount');
        $total_due = (clone $query)->sum('due_amount');
        $total_orders = (clone $query)->count();
        
        $recent_orders = PosOrder::with('customer')
                         ->whereMonth('order_date', $currentMonth)
                         ->whereYear('order_date', $currentYear)
                         ->latest()
                         ->limit(10)
                         ->get();

        return view('admin.pos-order.analysis', compact('total_sales', 'total_paid', 'total_due', 'total_orders', 'recent_orders'));
    }

    /**
     * Cancel POS Order
     */
    public function cancel(string $id)
    {
        try {
            DB::beginTransaction();
            $order = PosOrder::with('items')->findOrFail($id);

            if ($order->order_status == 'cancelled') {
                return response()->json(['success' => false, 'message' => 'Order is already cancelled.']);
            }

            // Restore product stock
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            $order->update(['order_status' => 'cancelled']);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Order cancelled successfully and stock restored.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Export POS Order PDF
     */
    public function exportPdf($id)
    {
        $order = PosOrder::with(['customer', 'items.product', 'creator'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.pos-order.pdf', compact('order'));
        return $pdf->download('POS-Invoice-' . $order->order_number . '.pdf');
    }
}
