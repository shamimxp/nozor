<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomOrder;
use App\Models\CustomOrderImage;
use App\Models\CustomOrderItem;
use App\Models\Fabric;
use App\Models\FabricPrice;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class CustomOrderController extends Controller
{
    /**
     * Display a listing of custom orders.
     */
    public function index(Request $request)
    {
        $vendors = Vendor::orderBy('name')->get();

        if ($request->ajax()) {
            $orders = CustomOrder::with(['customer', 'vendor'])->latest()->get();
            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('order_info', function($r) {
                    return '<strong>Num:</strong> ' . $r->order_number . '<br>' .
                           '<strong>Style:</strong> ' . $r->style_number . '<br>' .
                           '<strong>Date:</strong> ' . $r->order_date->format('d M, Y');
                })
                ->addColumn('customer_name', fn($r) => $r->customer ? '<strong>'.$r->customer->name.'</strong><br><small>'.$r->customer->phone.'</small>' : '-')

                ->addColumn('items_summary', function($r) {
                    return '<span class="badge badge-light-info">' . strtoupper($r->type) . '</span> ' .
                           '<span class="badge badge-light-secondary">' . strtoupper($r->sleeve) . '</span><br>' .
                           '<small>Qty: ' . $r->total_quantity . '</small>';
                })
                ->addColumn('totals', function($r) {
                    return 'Total: ৳' . number_format($r->grand_total, 2) . '<br>' .
                            'Paid: ৳' . number_format($r->paid, 2) . '<br>' .
                           '<small class="' . ($r->due > 0 ? 'text-danger font-weight-bold' : 'text-success') . '">Due: ৳' . number_format($r->due, 2) . '</small>';
                })
                ->addColumn('assign_vendor', function ($r) use ($vendors) {
                    // Lock vendor if delivered
                    $disabled = ($r->status == 'delivered') ? 'disabled' : '';
                    $options = '<option value="">-- Choose Vendor --</option>';
                    foreach ($vendors as $v) {
                        $sel = $r->vendor_id == $v->id ? 'selected' : '';
                        $options .= '<option value="' . $v->id . '" ' . $sel . '>' . $v->name . '</option>';
                    }
                    return '<select class="form-control form-control-sm assignVendorSelect" data-order-id="' . $r->id . '" style="min-width:130px" ' . $disabled . '>' . $options . '</select>';
                })
                ->addColumn('status_badge', function ($r) {
                    $statuses = [
                        'pending'        => 'Pending',
                        'purchase_order' => 'Purchase Order',
                        'order_confirm'  => 'Order Confirm',
                        'received'       => 'Received',
                        'delivered'      => 'Delivered',
                        'cancelled'      => 'Cancelled',
                    ];

                    // IF already delivered, disable the dropdown
                    $disabled = ($r->status == 'delivered') ? 'disabled' : '';

                    $options = '';
                    foreach ($statuses as $val => $label) {
                        $sel = ($r->status == $val) ? 'selected' : '';
                        $options .= '<option value="' . $val . '" ' . $sel . '>' . $label . '</option>';
                    }

                    $class = 'status-select-' . $r->status; // Can style specifically if needed
                    return '<select class="form-control form-control-sm updateStatusSelect" data-order-id="' . $r->id . '" style="min-width:130px" ' . $disabled . '>' . $options . '</select>';
                })
                ->addColumn('action', function ($r) {
                    $btn = '';
                    if ($r->status != 'delivered') {
                        $btn .= '<a href="' . route('admin.custom-order.edit', $r->id) . '" class="btn btn-primary btn-sm mr-25"><i data-feather="edit"></i></a>';
                    } else {
                        $btn .= '<span class="badge badge-light-success text-uppercase mr-25" title="Locked">Locked</span>';
                    }
                    $btn .= '<a href="javascript:void(0)" data-id="' . $r->id . '" class="btn btn-danger btn-sm deleteOrder"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['order_info', 'customer_name', 'items_summary', 'totals', 'assign_vendor', 'status_badge', 'action'])
                ->make(true);
        }
        return view('admin.custom-order.index', compact('vendors'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $styleNumber = CustomOrder::generateStyleNumber();
        $orderNumber = CustomOrder::generateOrderNumber();
        $customers   = Customer::orderBy('name')->get();
        $vendors     = Vendor::orderBy('name')->get();
        $fabrics     = Fabric::where('status', 1)->orderBy('name')->get();
        $fabricPrices = FabricPrice::with('fabric')->where('status', 1)->get();

        return view('admin.custom-order.create', compact(
            'styleNumber', 'orderNumber', 'customers', 'vendors', 'fabrics', 'fabricPrices'
        ));
    }

    /**
     * Store a new custom order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_date'      => 'required|date',
            'customer_id'     => 'required|exists:customers,id',
            'type'            => 'required|in:polo,t-shirt',
            'sleeve'          => 'required|in:half,full',
            'order_type'      => 'required|in:take_away,home_delivery',
            'carrying_charge' => 'nullable|numeric|min:0',
            'paid'            => 'nullable|numeric|min:0',
            'images.*'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'cart_fabric_price_id'   => 'required|array|min:1',
            'cart_fabric_price_id.*' => 'required|exists:fabric_prices,id',
            'cart_quantity'          => 'required|array',
            'cart_quantity.*'        => 'required|integer|min:1',
        ], [
            'cart_fabric_price_id.required' => 'At least one Fabric Specification is mandatory.',
            'cart_fabric_price_id.min'      => 'At least one Fabric Specification is mandatory.'
        ]);

        if ($request->status == 'purchase_order' && !$request->vendor_id) {
            toastr()->error('Vendor selection is required for Purchase Order status.');
            return back()->withInput();
        }

        try {
            DB::beginTransaction();
            $orderNumber = CustomOrder::generateOrderNumber();
            $styleNumber = CustomOrder::generateStyleNumber();

            $totalQty = 0; $subTotal = 0; $cartItems = [];
            foreach ($request->cart_fabric_price_id as $idx => $fpId) {
                $fp = FabricPrice::with('fabric')->findOrFail($fpId);
                $qty = (int) $request->cart_quantity[$idx];
                $lineTotal = $fp->price * $qty;
                $totalQty += $qty; $subTotal += $lineTotal;
                $cartItems[] = [
                    'fabric_price_id' => $fp->id,
                    'fabric_name'     => $fp->fabric->name ?? '-',
                    'type'            => $fp->type,
                    'sleeve'          => $fp->sleeve,
                    'unit_price'      => $fp->price,
                    'quantity'        => $qty,
                    'total'           => $lineTotal,
                ];
            }

            $carryingCharge = (float) ($request->carrying_charge ?? 0);
            $grandTotal     = $subTotal + $carryingCharge;
            $paid           = (float) ($request->paid ?? 0);
            $due            = $grandTotal - $paid;

            $order = CustomOrder::create([
                'order_number'    => $orderNumber,
                'style_number'    => $styleNumber,
                'order_date'      => $request->order_date,
                'customer_id'     => $request->customer_id,
                'type'            => $request->type,
                'sleeve'          => $request->sleeve,
                'customer_note'   => $request->customer_note,
                'vendor_note'     => $request->vendor_note,
                'delivery_date'   => $request->delivery_date,
                'delivered_date'  => $request->delivered_date,
                'collected_date'  => $request->collected_date,
                'total_quantity'  => $totalQty,
                'sub_total'       => $subTotal,
                'carrying_charge' => $carryingCharge,
                'grand_total'     => $grandTotal,
                'order_type'      => $request->order_type,
                'paid'            => $paid,
                'due'             => $due,
                'vendor_id'       => $request->vendor_id ?: null,
                'status'          => $request->status ?? 'pending',
            ]);

            foreach ($cartItems as $item) {
                $item['custom_order_id'] = $order->id;
                CustomOrderItem::create($item);
            }

            if ($request->hasFile('images')) {
                $dir = public_path('images/custom_orders/' . $order->id);
                if (!File::isDirectory($dir)) File::makeDirectory($dir, 0755, true);
                foreach ($request->file('images') as $file) {
                    $name = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($dir, $name);
                    CustomOrderImage::create([
                        'custom_order_id' => $order->id,
                        'image'           => 'images/custom_orders/' . $order->id . '/' . $name,
                    ]);
                }
            }

            DB::commit();
            toastr()->success('Custom order created successfully.');
            return redirect()->route('admin.custom-order.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Error: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function edit($id)
    {
        $order = CustomOrder::with(['items', 'images', 'customer', 'vendor'])->findOrFail($id);

        // Prevent editing if delivered
        if ($order->status == 'delivered') {
            toastr()->warning('Delivered orders cannot be modified.');
            return redirect()->route('admin.custom-order.index');
        }

        $customers   = Customer::orderBy('name')->get();
        $vendors     = Vendor::orderBy('name')->get();
        $fabrics     = Fabric::where('status', 1)->orderBy('name')->get();
        $fabricPrices = FabricPrice::with('fabric')->where('status', 1)->get();

        return view('admin.custom-order.edit', compact(
            'order', 'customers', 'vendors', 'fabrics', 'fabricPrices'
        ));
    }

    public function update(Request $request, $id)
    {
        $order = CustomOrder::findOrFail($id);

        // Prevent update if already delivered
        if ($order->status == 'delivered') {
            toastr()->error('Delivered orders are locked and cannot be changed.');
            return back();
        }

        $request->validate([
            'order_date'      => 'required|date',
            'customer_id'     => 'required|exists:customers,id',
            'type'            => 'required|in:polo,t-shirt',
            'sleeve'          => 'required|in:half,full',
            'order_type'      => 'required|in:take_away,home_delivery',
            'cart_fabric_price_id'   => 'required|array|min:1',
            'cart_fabric_price_id.*' => 'required|exists:fabric_prices,id',
            'cart_quantity'          => 'required|array',
            'cart_quantity.*'        => 'required|integer|min:1',
        ], [
            'cart_fabric_price_id.required' => 'At least one Fabric Specification is mandatory.',
            'cart_fabric_price_id.min'      => 'At least one Fabric Specification is mandatory.'
        ]);

        if ($request->status == 'purchase_order' && !$request->vendor_id) {
            toastr()->error('Vendor selection is required for Purchase Order status.');
            return back()->withInput();
        }

        try {
            DB::beginTransaction();
            $totalQty = 0; $subTotal = 0; $cartItems = [];
            foreach ($request->cart_fabric_price_id as $idx => $fpId) {
                $fp = FabricPrice::with('fabric')->findOrFail($fpId);
                $qty = (int) $request->cart_quantity[$idx];
                $lineTotal = $fp->price * $qty;
                $totalQty += $qty; $subTotal += $lineTotal;
                $cartItems[] = [
                    'fabric_price_id' => $fp->id,
                    'fabric_name'     => $fp->fabric->name ?? '-',
                    'type'            => $fp->type,
                    'sleeve'          => $fp->sleeve,
                    'unit_price'      => $fp->price,
                    'quantity'        => $qty,
                    'total'           => $lineTotal,
                ];
            }

            $carryingCharge = (float) ($request->carrying_charge ?? 0);
            $grandTotal     = $subTotal + $carryingCharge;
            $paid           = (float) ($request->paid ?? 0);
            $due            = $grandTotal - $paid;

            $order->update([
                'order_date'      => $request->order_date,
                'customer_id'     => $request->customer_id,
                'type'            => $request->type,
                'sleeve'          => $request->sleeve,
                'customer_note'   => $request->customer_note,
                'vendor_note'     => $request->vendor_note,
                'delivery_date'   => $request->delivery_date,
                'delivered_date'  => $request->delivered_date,
                'collected_date'  => $request->collected_date,
                'total_quantity'  => $totalQty,
                'sub_total'       => $subTotal,
                'carrying_charge' => $carryingCharge,
                'grand_total'     => $grandTotal,
                'order_type'      => $request->order_type,
                'paid'            => $paid,
                'due'             => $due,
                'vendor_id'       => $request->vendor_id ?: null,
                'status'          => $request->status ?? $order->status,
            ]);

            $order->items()->delete();
            foreach ($cartItems as $item) {
                $item['custom_order_id'] = $order->id;
                CustomOrderItem::create($item);
            }

            if ($request->has('remove_images')) {
                $toRemove = CustomOrderImage::whereIn('id', $request->remove_images)->get();
                foreach ($toRemove as $img) {
                    $path = public_path($img->image);
                    if (File::exists($path)) File::delete($path);
                    $img->delete();
                }
            }

            if ($request->hasFile('images')) {
                $dir = public_path('images/custom_orders/' . $order->id);
                if (!File::isDirectory($dir)) File::makeDirectory($dir, 0755, true);
                foreach ($request->file('images') as $file) {
                    $name = uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($dir, $name);
                    CustomOrderImage::create(['custom_order_id' => $order->id, 'image' => 'images/custom_orders/' . $order->id . '/' . $name]);
                }
            }

            DB::commit();
            toastr()->success('Custom order updated successfully.');
            return redirect()->route('admin.custom-order.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Error: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $order = CustomOrder::findOrFail($id);
            if ($order->status == 'delivered') {
                return response()->json(['error' => 'Delivered orders cannot be deleted.'], 403);
            }
            $dir = public_path('images/custom_orders/' . $order->id);
            if (File::isDirectory($dir)) File::deleteDirectory($dir);
            $order->items()->delete();
            $order->images()->delete();
            $order->delete();
            DB::commit();
            return response()->json(['success' => 'Custom order deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function assignVendor(Request $request)
    {
        $request->validate(['order_id' => 'required|exists:custom_orders,id', 'vendor_id' => 'nullable|exists:vendors,id']);
        $order = CustomOrder::findOrFail($request->order_id);
        if ($order->status == 'delivered') return response()->json(['success' => false, 'message' => 'LOCKED: Delivered orders cannot be changed.'], 403);
        if ($order->status == 'purchase_order' && !$request->vendor_id) return response()->json(['success' => false, 'message' => 'Vendor is required for Purchase Order.'], 422);

        $order->update(['vendor_id' => $request->vendor_id ?: null]);
        return response()->json(['success' => true, 'message' => 'Vendor assigned.']);
    }

    public function updateStatus(Request $request)
    {
        $request->validate(['order_id' => 'required|exists:custom_orders,id', 'status' => 'required']);
        $order = CustomOrder::findOrFail($request->order_id);

        if ($order->status == 'delivered') return response()->json(['success' => false, 'message' => 'LOCKED: Status cannot be changed after delivery.'], 403);
        if ($request->status == 'purchase_order' && !$order->vendor_id) return response()->json(['success' => false, 'message' => 'Vendor must be assigned before moving to Purchase Order.'], 422);

        $order->update(['status' => $request->status]);
        return response()->json(['success' => true, 'message' => 'Status updated to ' . ucfirst(str_replace('_', ' ', $request->status))]);
    }

    public function getFabricPrices(Request $request)
    {
        $query = FabricPrice::with('fabric')->where('status', 1);
        if ($request->type) $query->where('type', $request->type);
        if ($request->sleeve) $query->where('sleeve', $request->sleeve);
        return response()->json($query->get());
    }
}
