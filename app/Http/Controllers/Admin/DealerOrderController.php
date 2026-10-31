<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\DealerOrder;
use App\Models\DealerOrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class DealerOrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DealerOrder::with('dealer')->latest();

            if ($request->order_number) {
                $query->where('order_number', 'LIKE', '%' . $request->order_number . '%');
            }
            if ($request->start_date && $request->end_date) {
                $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
            }
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $orders = $query->get();
            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('order_info', function($r) {
                    return '<strong>Order Num:</strong> ' . $r->order_number . '<br>' .
                             '<strong>Order Status:</strong> ' . $r->status . '<br>' .
                           '<strong>Date:</strong> ' . $r->order_date->format('d M, Y');
                })
                ->addColumn('dealer_name', function($r) {
                    return '<strong>Shop Name:</strong> ' . $r->dealer->shop_name . '<br>' .
                           '<strong>Name:</strong> ' . $r->dealer->name. '<br>' .
                           '<strong>Phone:</strong> ' . $r->dealer->phone;
                })
                ->addColumn('totals', function($r) {
                    return 'Total: ৳' . number_format($r->grand_total, 2) . '<br>' .
                            '<span class="text-success font-weight-bold">Paid: ৳' . number_format($r->paid, 2) . '</span><br>' .
                           '<small class="' . ($r->due > 0 ? 'text-danger font-weight-bold' : 'text-success') . '">Due: ৳' . number_format($r->due, 2) . '</small>';
                })
                ->addColumn('status_badge', function ($r) {
                    $statuses = [
                        'pending'        => 'Pending',
                        'confirm'        => 'Confirm',
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
                    $btn = '<div class="d-inline-flex align-items-center" style="gap: 4px;">';
                    $btn .= '<a href="' . route('admin.dealer-order.show', $r->id) . '" class="btn btn-info btn-sm" title="View"><i data-feather="eye"></i></a>';
                    if (!in_array($r->status, ['delivered', 'cancelled'])) {
                        $btn .= '<a href="' . route('admin.dealer-order.edit', $r->id) . '" class="btn btn-primary btn-sm" title="Edit"><i data-feather="edit"></i></a>';
                    }
                    $btn .= '<a href="' . route('admin.dealer-order.export-pdf', $r->id) . '" class="btn btn-secondary btn-sm" title="Download PDF"><i data-feather="download"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $r->id . '" class="btn btn-danger btn-sm deleteOrder" title="Delete"><i data-feather="trash"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['order_info', 'dealer_name', 'totals', 'status_badge', 'action'])
                ->make(true);
        }
        return view('admin.dealer-order.index');
    }

    public function dueList(Request $request)
    {
        if ($request->ajax()) {
            $query = DealerOrder::with('dealer')->where('due', '>', 0)->where('status','!=','Pending')->latest();

            if ($request->order_number) {
                $query->where('order_number', 'LIKE', '%' . $request->order_number . '%');
            }
            if ($request->shop_name) {
                $query->whereHas('dealer', function ($q) use ($request) {
                    $q->where('shop_name', 'LIKE', '%' . $request->shop_name . '%');
                });
            }
            if ($request->dealer_phone) {
                $query->whereHas('dealer', function ($q) use ($request) {
                    $q->where('phone', 'LIKE', '%' . $request->dealer_phone . '%');
                });
            }
            if ($request->start_date && $request->end_date) {
                $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
            }
            if ($request->status) {
                $query->where('status', $request->status);
            }

            $orders = $query->get();
            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('order_info', function($r) {
                    return '<strong>Num:</strong> ' . $r->order_number . '<br>' .
                           '<strong>Date:</strong> ' . $r->order_date->format('d M, Y');
                })
                ->addColumn('dealer_name', function($r) {
                    return '<strong>Shop Name:</strong> ' . $r->dealer->shop_name . '<br>' .
                            '<strong>Name:</strong> ' . $r->dealer->name . '<br>' .
                            '<strong>Phone:</strong> ' . $r->dealer->phone;
                })
                ->addColumn('totals', function($r) {
                    return 'Total: ৳' . number_format($r->grand_total, 2) . '<br>' .
                            '<span class="text-success font-weight-bold">Paid: ৳' . number_format($r->paid, 2) . '</span><br>' .
                           '<small class="text-danger font-weight-bold">Due: ৳' . number_format($r->due, 2) . '</small>';
                })
                ->addColumn('status_badge', function ($r) {
                    return ucfirst($r->status);
                })
                ->addColumn('action', function ($r) {
                    $btn = '<div class="d-inline-flex align-items-center" style="gap: 4px;">';
                    $btn .= '<a href="' . route('admin.dealer-order.show', $r->id) . '" class="btn btn-info btn-sm" title="View"><i data-feather="eye"></i></a>';
                    $btn .= '<a href="' . route('admin.dealer-order.export-pdf', $r->id) . '" class="btn btn-secondary btn-sm" title="Download PDF"><i data-feather="download"></i></a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['order_info', 'dealer_name', 'totals', 'status_badge', 'action'])
                ->make(true);
        }
        return view('admin.dealer-order.due_list');
    }

    public function create()
    {
        $orderNumber = DealerOrder::generateOrderNumber();
        $dealers     = Dealer::orderBy('shop_name')->get();
        $categories  = \App\Models\Category::orderBy('name')->get();

        $calculator  = app(\App\Services\ProductPriceCalculator::class);
        $rawProducts = Product::with(['recipe.items.rawMaterialProduct', 'recipe.costSetting.items', 'category'])
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $products = [];
        foreach ($rawProducts as $p) {
            $regularPrice = 0;
            $specialPrice = 0;

            if ($p->is_manufacturer && $p->recipe) {
                try {
                    $quote = $calculator->quote($p->id);
                    $regularPrice = (float) ($quote['regular_dealer_price'] ?? 0);
                    $specialPrice = (float) ($quote['special_dealer_price'] ?? 0);
                } catch (\Exception $e) {
                    $regularPrice = 0;
                    $specialPrice = 0;
                }
            } else {
                $basePrice = (float) ($p->selling_price ?? 0);
                $regularPrice = $basePrice;
                $specialPrice = $basePrice;
            }

            // Exclude products where price is 0 or less
            if ($regularPrice <= 0 && $specialPrice <= 0) {
                continue;
            }

            $pImg = $p->featured_image ? asset(config('imagepath.product') . $p->featured_image) : null;
            $mfgImg = null;
            if ($p->is_manufacturer && $p->recipe) {
                $mImgs = $p->recipe->manufacture_images;
                if (is_string($mImgs)) {
                    $mImgs = json_decode($mImgs, true);
                }
                if (is_array($mImgs) && count($mImgs) > 0 && !empty($mImgs[0])) {
                    $mfgImg = asset($mImgs[0]);
                }
            }

            $products[] = [
                'id'                   => $p->id,
                'name'                 => $p->name,
                'model_no'             => $p->model_no,
                'category_id'          => $p->category_id,
                'category_name'        => $p->category ? $p->category->name : 'General',
                'is_manufacturer'      => $p->is_manufacturer ? 1 : 0,
                'featured_image'       => $pImg,
                'manufacture_image'    => $mfgImg,
                'regular_dealer_price' => $regularPrice,
                'special_dealer_price' => $specialPrice,
                'stock'                => $p->is_manufacturer ? 'Made to Order' : ($p->stock ?? 0),
                'stock_qty'            => (int) ($p->stock ?? 0),
            ];
        }

        return view('admin.dealer-order.create', compact('orderNumber', 'dealers', 'categories', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_date'      => 'required|date',
            'dealer_id'       => 'required|exists:dealers,id',
            'carrying_charge' => 'nullable|numeric|min:0',
            'discount'        => 'nullable|numeric|min:0',
            'paid'            => 'nullable|numeric|min:0',
            'items'           => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'     => 'required|integer|min:1',
            'items.*.price'   => 'required|numeric|min:0',
        ]);

        $stockService = app(\App\Services\StockService::class);
        $stockErrors = $stockService->validateAvailability($request->items);
        if (!empty($stockErrors)) {
            toastr()->error(implode(' ', $stockErrors));
            return back()->withInput();
        }

        try {
            DB::beginTransaction();
            $orderNumber = DealerOrder::generateOrderNumber();

            $subTotal = 0;
            $cartItems = [];

            foreach ($request->items as $item) {
                $qty = (int) $item['qty'];
                $price = (float) $item['price'];
                $lineTotal = $price * $qty;
                $subTotal += $lineTotal;

                $cartItems[] = [
                    'product_id' => $item['product_id'],
                    'qty'        => $qty,
                    'price'      => $price,
                    'total'      => $lineTotal,
                ];
            }

            $carryingCharge = (float) ($request->carrying_charge ?? 0);
            $discount       = (float) ($request->discount ?? 0);
            $grandTotal     = ($subTotal - $discount) + $carryingCharge;
            $paid           = (float) ($request->paid ?? 0);
            $due            = $grandTotal - $paid;

            $order = DealerOrder::create([
                'order_number'    => $orderNumber,
                'order_date'      => $request->order_date,
                'dealer_id'       => $request->dealer_id,
                'note'            => $request->note,
                'admin_notes'     => $request->admin_notes,
                'sub_total'       => $subTotal,
                'discount'        => $discount,
                'carrying_charge' => $carryingCharge,
                'grand_total'     => $grandTotal,
                'paid'            => $paid,
                'due'             => $due,
                'status'          => $request->status ?? 'pending',
            ]);

            foreach ($cartItems as $item) {
                $item['dealer_order_id'] = $order->id;
                DealerOrderItem::create($item);
            }

            if (in_array($order->status, ['confirm', 'delivered'])) {
                $order->load('items');
                $this->deductOrderStock($order);
            }

            if ($order->status === 'delivered') {
                app(\App\Services\DealerWalletService::class)->settleDeliveredOrders($order->dealer_id);
            }

            DB::commit();
            toastr()->success('Dealer order created successfully.');
            return redirect()->route('admin.dealer-order.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Error: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function show($id)
    {
        $order = DealerOrder::with(['items.product.gallery', 'items.product.recipe.costSetting.items.costHead', 'dealer'])->findOrFail($id);
        $workers = \App\Models\Worker::where('status', 1)->get();
        $setting = \App\Models\WebSetting::first();

        $refNumber = trim($order->order_number ?? '');
        $refVariants = array_unique(array_filter([$refNumber, '#' . ltrim($refNumber, '#'), ltrim($refNumber, '#')]));
        $assignedProductIds = !empty($refVariants) ? \App\Models\ManufactureOrderItem::whereIn('reff_invoice', $refVariants)
            ->pluck('product_id')
            ->merge(
                \App\Models\Manufacture::whereIn('reff_invoice', $refVariants)
                    ->whereNotNull('product_id')
                    ->pluck('product_id')
            )
            ->filter()
            ->unique()
            ->values()
            ->toArray() : [];

        return view('admin.dealer-order.show', compact('order', 'workers', 'setting', 'assignedProductIds'));
    }

    public function edit($id)
    {
        $order = DealerOrder::with(['items.product.recipe', 'dealer'])->findOrFail($id);

        if ($order->status == 'delivered') {
            toastr()->warning('Delivered orders cannot be modified.');
            return redirect()->route('admin.dealer-order.index');
        }

        $dealers     = Dealer::orderBy('shop_name')->get();
        $categories  = \App\Models\Category::orderBy('name')->get();
        $calculator  = app(\App\Services\ProductPriceCalculator::class);

        $rawProducts = Product::with(['recipe.items.rawMaterialProduct', 'recipe.costSetting.items', 'category'])
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $products = [];
        foreach ($rawProducts as $p) {
            $regularPrice = 0;
            $specialPrice = 0;

            if ($p->is_manufacturer && $p->recipe) {
                try {
                    $quote = $calculator->quote($p->id);
                    $regularPrice = (float) ($quote['regular_dealer_price'] ?? 0);
                    $specialPrice = (float) ($quote['special_dealer_price'] ?? 0);
                } catch (\Exception $e) {
                    $regularPrice = 0;
                    $specialPrice = 0;
                }
            } else {
                $basePrice = (float) ($p->selling_price ?? 0);
                $regularPrice = $basePrice;
                $specialPrice = $basePrice;
            }

            // Exclude products where price is 0 or less
            if ($regularPrice <= 0 && $specialPrice <= 0) {
                continue;
            }

            $pImg = $p->featured_image ? asset(config('imagepath.product') . $p->featured_image) : null;
            $mfgImg = null;
            if ($p->is_manufacturer && $p->recipe) {
                $mImgs = $p->recipe->manufacture_images;
                if (is_string($mImgs)) {
                    $mImgs = json_decode($mImgs, true);
                }
                if (is_array($mImgs) && count($mImgs) > 0 && !empty($mImgs[0])) {
                    $mfgImg = asset($mImgs[0]);
                }
            }

            $products[] = [
                'id'                   => $p->id,
                'name'                 => $p->name,
                'model_no'             => $p->model_no,
                'category_id'          => $p->category_id,
                'category_name'        => $p->category ? $p->category->name : 'General',
                'is_manufacturer'      => $p->is_manufacturer ? 1 : 0,
                'featured_image'       => $pImg,
                'manufacture_image'    => $mfgImg,
                'regular_dealer_price' => $regularPrice,
                'special_dealer_price' => $specialPrice,
                'stock'                => $p->is_manufacturer ? 'Made to Order' : ($p->stock ?? 0),
                'stock_qty'            => (int) ($p->stock ?? 0),
            ];
        }

        return view('admin.dealer-order.edit', compact('order', 'dealers', 'categories', 'products'));
    }

    public function update(Request $request, $id)
    {
        $order = DealerOrder::with('items')->findOrFail($id);

        if ($order->status == 'delivered') {
            toastr()->error('Delivered orders are locked and cannot be changed.');
            return back();
        }

        $request->validate([
            'order_date'      => 'required|date',
            'dealer_id'       => 'required|exists:dealers,id',
            'carrying_charge' => 'nullable|numeric|min:0',
            'discount'        => 'nullable|numeric|min:0',
            'paid'            => 'nullable|numeric|min:0',
            'items'           => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'     => 'required|integer|min:1',
            'items.*.price'   => 'required|numeric|min:0',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status ?? $order->status;

        $stockService = app(\App\Services\StockService::class);
        if (in_array($newStatus, ['confirm', 'delivered']) && !in_array($oldStatus, ['confirm', 'delivered'])) {
            $stockErrors = $stockService->validateAvailability($request->items);
            if (!empty($stockErrors)) {
                toastr()->error(implode(' ', $stockErrors));
                return back()->withInput();
            }
        }

        try {
            DB::beginTransaction();

            // If old status had deducted stock, restore old items before replacing
            if (in_array($oldStatus, ['confirm', 'delivered'])) {
                $this->restoreOrderStock($order);
            }

            $subTotal = 0;
            $cartItems = [];

            foreach ($request->items as $item) {
                $qty = (int) $item['qty'];
                $price = (float) $item['price'];
                $lineTotal = $price * $qty;
                $subTotal += $lineTotal;

                $cartItems[] = [
                    'product_id' => $item['product_id'],
                    'qty'        => $qty,
                    'price'      => $price,
                    'total'      => $lineTotal,
                ];
            }

            $carryingCharge = (float) ($request->carrying_charge ?? 0);
            $discount       = (float) ($request->discount ?? 0);
            $grandTotal     = ($subTotal - $discount) + $carryingCharge;
            $paid           = (float) ($request->paid ?? 0);
            $due            = $grandTotal - $paid;

            $order->update([
                'order_date'      => $request->order_date,
                'dealer_id'       => $request->dealer_id,
                'note'            => $request->note,
                'admin_notes'     => $request->admin_notes,
                'sub_total'       => $subTotal,
                'discount'        => $discount,
                'carrying_charge' => $carryingCharge,
                'grand_total'     => $grandTotal,
                'paid'            => $paid,
                'due'             => $due,
                'status'          => $newStatus,
            ]);

            $order->items()->delete();
            foreach ($cartItems as $item) {
                $item['dealer_order_id'] = $order->id;
                DealerOrderItem::create($item);
            }

            if (in_array($newStatus, ['confirm', 'delivered'])) {
                $order->load('items');
                $this->deductOrderStock($order);
            }

            DB::commit();
            toastr()->success('Dealer order updated successfully.');
            return redirect()->route('admin.dealer-order.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Error: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function updateItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:dealer_order_items,id',
            'confirm_qty' => 'nullable|integer|min:0',
            'note' => 'nullable|string'
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $item = DealerOrderItem::findOrFail($request->item_id);
            $qty = $request->confirm_qty !== null ? $request->confirm_qty : $item->qty;
            
            $item->update([
                'qty' => $qty,
                'confirm_qty' => $qty,
                'total' => $item->price * $qty,
                'note' => $request->note
            ]);

            // Recalculate Order Totals
            $order = DealerOrder::findOrFail($item->dealer_order_id);
            $subTotal = DealerOrderItem::where('dealer_order_id', $order->id)->sum('total');
            $grandTotal = ($subTotal - $order->discount) + $order->carrying_charge;
            $due = $grandTotal - $order->paid;

            $order->update([
                'sub_total' => $subTotal,
                'grand_total' => $grandTotal,
                'due' => $due
            ]);

            \Illuminate\Support\Facades\DB::commit();
            return response()->json(['success' => true, 'message' => 'Item and order totals updated successfully']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $order = DealerOrder::with('items')->findOrFail($id);
            if ($order->status == 'delivered') {
                return response()->json(['error' => 'Delivered orders cannot be deleted.'], 403);
            }

            if (in_array($order->status, ['confirm', 'delivered'])) {
                $this->restoreOrderStock($order);
            }

            $order->items()->delete();
            $order->delete();
            DB::commit();
            return response()->json(['success' => 'Dealer order deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate(['order_id' => 'required|exists:dealer_orders,id', 'status' => 'required']);
        $order = DealerOrder::with('items.product')->findOrFail($request->order_id);

        if ($order->status == 'delivered') return response()->json(['success' => false, 'message' => 'LOCKED: Status cannot be changed after delivery.'], 403);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) {
            return response()->json(['success' => true, 'message' => 'Status unchanged']);
        }

        $stockService = app(\App\Services\StockService::class);

        // Moving to confirm or delivered -> check non-mfg availability and deduct
        if (!in_array($oldStatus, ['confirm', 'delivered']) && in_array($newStatus, ['confirm', 'delivered'])) {
            $items = $order->items->map(fn($it) => ['product_id' => $it->product_id, 'qty' => $it->qty])->toArray();
            $stockErrors = $stockService->validateAvailability($items);
            if (!empty($stockErrors)) {
                return response()->json(['success' => false, 'message' => implode(' ', $stockErrors)], 422);
            }
            $this->deductOrderStock($order);
        }

        // Moving to cancelled from confirmed/delivered -> restore stock
        if (in_array($oldStatus, ['confirm', 'delivered']) && $newStatus === 'cancelled') {
            $this->restoreOrderStock($order);
        }

        $order->update(['status' => $newStatus]);

        // If status changed to delivered, auto-settle from dealer wallet (FIFO)
        if ($newStatus === 'delivered') {
            app(\App\Services\DealerWalletService::class)->settleDeliveredOrders($order->dealer_id);
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    private function deductOrderStock(DealerOrder $order): void
    {
        $stockService = app(\App\Services\StockService::class);
        foreach ($order->items as $item) {
            $stockService->recordOut(
                $item->product_id,
                $item->qty,
                'dealer_order',
                $order->id,
                $order->order_number,
                "Dealer order confirmed/delivered: {$order->order_number}",
                null,
                false
            );
        }
    }

    private function restoreOrderStock(DealerOrder $order): void
    {
        $stockService = app(\App\Services\StockService::class);
        foreach ($order->items as $item) {
            $stockService->recordReversal(
                $item->product_id,
                $item->qty,
                'dealer_order',
                $order->id,
                $order->order_number,
                "Dealer order cancelled/reverted - stock restored: {$order->order_number}"
            );
        }
    }

    public function exportPdf($id)
    {
        $order = DealerOrder::with(['items.product', 'dealer'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.dealer-order.pdf', compact('order'));
        return $pdf->download('Invoice-' . $order->order_number . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'dealer_orders_' . date('Y-m-d') . '.csv';
        $query = DealerOrder::with(['dealer', 'items'])->latest();

        if ($request->order_number) {
            $query->where('order_number', 'LIKE', '%' . $request->order_number . '%');
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
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

        $columns = ['Order No', 'Order Date', 'Dealer Name', 'Dealer Phone', 'Qty', 'Sub Total', 'Discount', 'Carrying Charge', 'Grand Total', 'Paid', 'Due', 'Status'];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->order_date->format('Y-m-d'),
                    $order->dealer->shop_name ?? ($order->dealer->name ?? 'N/A'),
                    $order->dealer->phone ?? 'N/A',
                    $order->items->sum('qty'),
                    $order->sub_total,
                    $order->discount,
                    $order->carrying_charge,
                    $order->grand_total,
                    $order->paid,
                    $order->due,
                    ucfirst($order->status),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportListPdf(Request $request)
    {
        $query = DealerOrder::with(['dealer', 'items'])->latest();

        if ($request->order_number) {
            $query->where('order_number', 'LIKE', '%' . $request->order_number . '%');
        }
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->get();
        $filters = [
            'order_number' => $request->order_number,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'status'       => $request->status,
        ];

        $pdf = Pdf::loadView('admin.dealer-order.list_pdf', compact('orders', 'filters'))
            ->setPaper('a4', 'landscape');
        return $pdf->download('dealer_orders_' . date('Y-m-d') . '.pdf');
    }
}
