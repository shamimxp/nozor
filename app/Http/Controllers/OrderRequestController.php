<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderRequestController extends Controller
{
    protected $orderRequestService;

    public function __construct(\App\Services\OrderRequestService $orderRequestService)
    {
        $this->orderRequestService = $orderRequestService;
    }

    /**
     * FEATURE 2: ADMIN PANEL - REQUEST LIST
     * Use AJAX DataTable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\OrderRequest::with('dealer')->latest();

            // Filter logic
            if ($request->filled('request_number')) {
                $query->where('request_number', 'like', '%' . $request->request_number . '%')
                      ->orWhere('id', 'like', '%' . $request->request_number . '%');
            }
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('request_info', function ($row) {
                    $num = $row->request_number ?? '#'.$row->id;
                    $date = $row->created_at ? $row->created_at->format('d M, Y h:i A') : 'N/A';
                    return "<div class='font-weight-bold text-primary'>{$num}</div><div class='small text-dark'>{$date}</div>";
                })
                ->addColumn('dealer_info', function ($row) {
                    $name = $row->dealer->name ?? 'N/A';
                    $shop = $row->dealer->shop_name ?? 'N/A';
                    $phone = $row->dealer->phone ?? 'N/A';
                    return "<div class='font-weight-bold'>{$name}</div>
                            <div class='small text-dark'><i data-feather='shopping-bag' style='width:12px;height:12px;'></i> {$shop}</div>
                            <div class='small text-dark'><i data-feather='phone' style='width:12px;height:12px;'></i> {$phone}</div>";
                })
                ->addColumn('total_items', function ($row) {
                    return '<span class="badge badge-light-secondary">' . $row->items()->count() . ' items</span>';
                })

                 ->addColumn('total_amount', function ($row) {
                    $priceCalculator = app(\App\Services\ProductPriceCalculator::class);
                    $total = 0;
                    foreach ($row->items as $item) {
                        $unitPrice = 0;
                        if ($item->product) {
                            if ($item->product->is_manufacturer == 1) {
                                try {
                                    $priceData = $priceCalculator->calculate($item->product_id, $row->dealer_id);
                                    $unitPrice = $priceData['dealer_price'];
                                } catch (\Exception $e) { $unitPrice = 0; }
                            } else {
                                $unitPrice = $item->product->selling_price ?? 0;
                            }
                        }
                        $total += ($unitPrice * $item->requested_qty);
                    }
                    return '<span class="font-weight-bolder text-primary">' . number_format($total, 0) . ' <small class="text-dark">TK</small></span>';
                })
                ->addColumn('status_badge', function ($row) {
                    if ($row->status == 1 || $row->status === 'confirmed' || strtolower($row->status) === 'confirm') {
                        return '<span class="badge badge-light-success">Confirmed</span>';
                    } elseif ($row->status == 0 || $row->status === 'pending') {
                        return '<span class="badge badge-light-warning">Pending</span>';
                    } else {
                        return '<span class="badge badge-light-secondary">'.ucfirst($row->status).'</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    return '<a href="'.route('admin.order-requests.show', $row->id).'" class="btn btn-sm btn-outline-primary" style="padding: 0.3rem 0.6rem;">
                                <i data-feather="eye" style="width: 14px; height: 14px; margin-right: 4px;"></i> Details
                            </a>';
                })
                ->rawColumns(['request_info', 'dealer_info', 'total_items', 'total_amount', 'status_badge', 'action'])
                ->make(true);
        }

        return view('admin.order-requests.index');
    }

    /**
     * FEATURE 3: REQUEST DETAILS PAGE
     */
    public function show($id)
    {
        $orderRequest = \App\Models\OrderRequest::with(['dealer', 'items.product'])->findOrFail($id);
        return view('admin.order-requests.show', compact('orderRequest'));
    }

    /**
     * FEATURE 4 & 5 & 6: PARTIAL ORDER CONFIRMATION
     */
    public function confirm(Request $request, $id)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.order_request_item_id' => 'required|exists:order_request_items,id',
            'items.*.confirm_qty' => 'required|integer|min:0',
            'order_note' => 'nullable|string',
            'admin_note' => 'nullable|string',
        ]);

        try {
            $order = $this->orderRequestService->confirmOrderRequest($id, $request->items, $request->order_note, $request->admin_note);
            return redirect()->route('admin.order-requests.index')->with('success', 'Order created/confirmed successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
