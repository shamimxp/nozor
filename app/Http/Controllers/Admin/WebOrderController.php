<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\WebOrder;
use App\Models\WebOrderAddress;
use App\Models\WebOrderItem;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class WebOrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = WebOrder::with(['address', 'items'])->latest();

            if ($request->invoice_no) {
                $query->where('invoice_no', 'LIKE', '%' . $request->invoice_no . '%');
            }
            if ($request->start_date && $request->end_date) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $orders = $query->get();
            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('order_info', function($r) {
                    return '<strong>Num:</strong> ' . $r->invoice_no . '<br>' .
                           '<strong>Date:</strong> ' . $r->created_at->format('d M, Y');
                })
                ->addColumn('customer_name', function($r) {
                    if($r->address) {
                        return '<strong>'.$r->address->name.'</strong><br><small>'.$r->address->phone.'</small>';
                    }
                    return '-';
                })
                ->addColumn('items_summary', function($r) {
                    return '<small>Qty: ' . $r->items->sum('quantity') . '</small>';
                })
                ->addColumn('totals', function($r) {
                    return 'Total: ৳' . number_format($r->total, 2) . '<br>' .
                           '<small>Pay: ' . strtoupper($r->payment_method) . '</small>';
                })
                ->addColumn('status_badge', function ($r) {
                    $statuses = [
                        'pending'        => 'Pending',
                        'processing'     => 'Processing',
                        'delivered'      => 'Delivered',
                        'cancelled'      => 'Cancelled',
                    ];

                    $disabled = ($r->status == 'delivered') ? 'disabled' : '';

                    $options = '';
                    foreach ($statuses as $val => $label) {
                        $sel = ($r->status == $val) ? 'selected' : '';
                        $options .= '<option value="' . $val . '" ' . $sel . '>' . $label . '</option>';
                    }

                    return '<select class="form-control form-control-sm updateStatusSelect" data-order-id="' . $r->id . '" style="min-width:130px" ' . $disabled . '>' . $options . '</select>';
                })
                ->addColumn('action', function ($r) {
                    $btn = '';
                    $btn .= '<a href="' . route('admin.web-order.show', $r->id) . '" class="btn btn-info btn-sm mr-25"><i data-feather="eye"></i></a>';
                    if ($r->status === 'pending') {
                        $btn .= '<a href="' . route('admin.web-order.edit', $r->id) . '" class="btn btn-primary btn-sm mr-25"><i data-feather="edit"></i></a>';
                    }
                    $btn .= '<a href="' . route('admin.web-order.export-pdf', $r->id) . '" class="btn btn-secondary btn-sm"><i data-feather="download"></i></a>';
                    return $btn;
                })
                ->rawColumns(['order_info', 'customer_name', 'items_summary', 'totals', 'status_badge', 'action'])
                ->make(true);
        }
        return view('admin.web-order.index');
    }

    public function show($id)
    {
        $order = WebOrder::with(['address', 'items.product'])->findOrFail($id);
        
        if (!$order->is_read) {
            $order->update(['is_read' => 1]);
        }

        return view('admin.web-order.show', compact('order'));
    }

    public function edit($id)
    {
        $order = WebOrder::with(['address', 'items.product'])->findOrFail($id);

        if (!$order->is_read) {
            $order->update(['is_read' => 1]);
        }

        if ($order->status !== 'pending') {
            toastr()->warning('Only pending orders can be modified.');
            return redirect()->route('admin.web-order.index');
        }

        $settings = \App\Models\WebSetting::first();

        return view('admin.web-order.edit', compact('order', 'settings'));
    }

    public function update(Request $request, $id)
    {
        $order = WebOrder::findOrFail($id);

        if ($order->status == 'delivered') {
            toastr()->error('Delivered orders are locked and cannot be changed.');
            return back();
        }

        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'status' => 'required',
            'items' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            $submittedItemIds = [];

            foreach ($request->items as $itemId => $itemData) {
                $item = WebOrderItem::where('web_order_id', $order->id)->find($itemId);
                if ($item) {
                    $item->update([
                        'size' => $itemData['size'],
                        'color' => $itemData['color'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                    ]);
                    $subtotal += ($itemData['quantity'] * $itemData['price']);
                    $submittedItemIds[] = $itemId;
                }
            }

            // Remove deleted items
            WebOrderItem::where('web_order_id', $order->id)
                ->whereNotIn('id', $submittedItemIds)
                ->delete();

            $order->update([
                'status' => $request->status,
                'subtotal' => $subtotal,
                'discount' => $request->discount ?? $order->discount,
                'shipping_charge' => $request->shipping_charge ?? $order->shipping_charge,
            ]);
            
            // Recalculate total if discount or shipping changed
            $order->update([
                'total' => $order->subtotal - $order->discount + $order->shipping_charge
            ]);

            if ($order->address) {
                $order->address->update([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'note' => $request->note
                ]);
            }

            DB::commit();
            toastr()->success('Web order updated successfully.');
            return redirect()->route('admin.web-order.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Error: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate(['order_id' => 'required|exists:web_orders,id', 'status' => 'required']);
        $order = WebOrder::findOrFail($request->order_id);

        if ($order->status == 'delivered') return response()->json(['success' => false, 'message' => 'LOCKED: Status cannot be changed after delivery.'], 403);

        $order->update(['status' => $request->status]);
        return response()->json(['success' => true, 'message' => 'Status updated to ' . ucfirst(str_replace('_', ' ', $request->status))]);
    }

    public function exportPdf($id)
    {
        $order = WebOrder::with(['address', 'items.product'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.web-order.pdf', compact('order'));
        return $pdf->download('Invoice-' . $order->invoice_no . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'web_orders_' . date('Y-m-d') . '.csv';
        $query = WebOrder::with('address', 'items')->latest();

        if ($request->invoice_no) {
            $query->where('invoice_no', 'LIKE', '%' . $request->invoice_no . '%');
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();

        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $fileName,
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $columns = ['Invoice No', 'Order Date', 'Customer Name', 'Phone', 'Address', 'Total Qty', 'Subtotal', 'Shipping', 'Discount', 'Grand Total', 'Payment Method', 'Payment Status', 'Status'];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->invoice_no,
                    $order->created_at->format('Y-m-d'),
                    $order->address->name ?? 'N/A',
                    $order->address->phone ?? 'N/A',
                    $order->address->address ?? 'N/A',
                    $order->items->sum('quantity'),
                    $order->subtotal,
                    $order->shipping_charge,
                    $order->discount,
                    $order->total,
                    strtoupper($order->payment_method),
                    $order->status == 'delivered' ? 'PAID' : 'DUE',
                    strtoupper(str_replace('_', ' ', $order->status)),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportListPdf(Request $request)
    {
        $query = WebOrder::with('address', 'items')->latest();

        if ($request->invoice_no) {
            $query->where('invoice_no', 'LIKE', '%' . $request->invoice_no . '%');
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();
        $filters = [
            'invoice_no' => $request->invoice_no,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => $request->status,
        ];

        $pdf = Pdf::loadView('admin.web-order.list_pdf', compact('orders', 'filters'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('web_orders_' . date('Y-m-d') . '.pdf');
    }

    public function notifications()
    {
        $pendingOrders = WebOrder::with('address')->where('is_read', 0)->latest()->take(10)->get();
        $count = WebOrder::where('is_read', 0)->count();

        $html = '';
        foreach ($pendingOrders as $order) {
            $customerName = $order->address ? $order->address->name : 'Unknown';
            $url = route('admin.web-order.show', $order->id);
            $html .= '<a class="d-flex" href="' . $url . '">
                            <div class="media d-flex align-items-start">
                                <div class="media-left">
                                    <div class="avatar bg-light-warning">
                                        <div class="avatar-content"><i class="avatar-icon" data-feather="shopping-cart"></i></div>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <p class="media-heading"><span class="font-weight-bolder">New Web Order #' . $order->invoice_no . '</span></p>
                                    <small class="notification-text"> From: ' . $customerName . ' (' . $order->created_at->diffForHumans() . ')</small>
                                </div>
                            </div>
                        </a>';
        }

        if ($count == 0) {
            $html = '<div class="p-2 text-center text-muted">No new web orders</div>';
        }

        return response()->json([
            'count' => $count,
            'html' => $html
        ]);
    }
}
