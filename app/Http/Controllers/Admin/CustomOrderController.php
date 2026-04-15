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
        if ($request->ajax()) {
            $orders = CustomOrder::with(['customer', 'vendor'])->latest()->get();
            return DataTables::of($orders)
                ->addIndexColumn()
                ->addColumn('customer_name', fn($r) => $r->customer ? $r->customer->name : '-')
                ->addColumn('vendor_name', fn($r) => $r->vendor ? $r->vendor->name : '<span class="badge badge-light-warning">Not Assigned</span>')
                ->addColumn('order_date_formatted', fn($r) => $r->order_date->format('d M, Y'))
                ->addColumn('grand_total_formatted', fn($r) => number_format($r->grand_total, 2))
                ->addColumn('paid_formatted', fn($r) => number_format($r->paid, 2))
                ->addColumn('due_formatted', fn($r) => '<span class="' . ($r->due > 0 ? 'text-danger font-weight-bold' : 'text-success') . '">' . number_format($r->due, 2) . '</span>')
                ->addColumn('status_badge', function ($r) {
                    $badges = [
                        'pending'    => 'badge-light-warning',
                        'processing' => 'badge-light-info',
                        'completed'  => 'badge-light-success',
                        'cancelled'  => 'badge-light-danger',
                    ];
                    $cls = $badges[$r->status] ?? 'badge-light-secondary';
                    return '<span class="badge ' . $cls . '">' . ucfirst($r->status) . '</span>';
                })
                ->addColumn('assign_vendor', function ($r) {
                    $vendors = Vendor::orderBy('name')->get();
                    $options = '<option value="">-- Select --</option>';
                    foreach ($vendors as $v) {
                        $sel = $r->vendor_id == $v->id ? 'selected' : '';
                        $options .= '<option value="' . $v->id . '" ' . $sel . '>' . $v->name . '</option>';
                    }
                    return '<select class="form-control form-control-sm assignVendorSelect" data-order-id="' . $r->id . '" style="min-width:130px">' . $options . '</select>';
                })
                ->addColumn('action', function ($r) {
                    $btn  = '<a href="' . route('admin.custom-order.edit', $r->id) . '" class="btn btn-primary btn-sm"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $r->id . '" class="btn btn-danger btn-sm deleteOrder"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['vendor_name', 'due_formatted', 'status_badge', 'assign_vendor', 'action'])
                ->make(true);
        }
        return view('admin.custom-order.index');
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $styleNumber = CustomOrder::generateStyleNumber();
        $customers   = Customer::orderBy('name')->get();
        $vendors     = Vendor::orderBy('name')->get();
        $fabrics     = Fabric::where('status', 1)->orderBy('name')->get();
        $fabricPrices = FabricPrice::with('fabric')->where('status', 1)->get();

        return view('admin.custom-order.create', compact(
            'styleNumber', 'customers', 'vendors', 'fabrics', 'fabricPrices'
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
            // cart items
            'cart_fabric_price_id'   => 'required|array|min:1',
            'cart_fabric_price_id.*' => 'required|exists:fabric_prices,id',
            'cart_quantity'          => 'required|array',
            'cart_quantity.*'        => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $styleNumber = CustomOrder::generateStyleNumber();

            // Calculate totals from cart
            $totalQty  = 0;
            $subTotal  = 0;
            $cartItems = [];
            foreach ($request->cart_fabric_price_id as $idx => $fpId) {
                $fp  = FabricPrice::with('fabric')->findOrFail($fpId);
                $qty = (int) $request->cart_quantity[$idx];
                $lineTotal = $fp->price * $qty;
                $totalQty += $qty;
                $subTotal += $lineTotal;
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
                'style_number'    => $styleNumber,
                'order_date'      => $request->order_date,
                'customer_id'     => $request->customer_id,
                'type'            => $request->type,
                'sleeve'          => $request->sleeve,
                'customer_note'   => $request->customer_note,
                'vendor_note'     => $request->vendor_note,
                'delivery_date'   => $request->delivery_date,
                'collected_date'  => $request->collected_date,
                'total_quantity'  => $totalQty,
                'sub_total'       => $subTotal,
                'carrying_charge' => $carryingCharge,
                'grand_total'     => $grandTotal,
                'order_type'      => $request->order_type,
                'paid'            => $paid,
                'due'             => $due,
                'vendor_id'       => $request->vendor_id ?: null,
                'status'          => 'pending',
            ]);

            // Save items
            foreach ($cartItems as $item) {
                $item['custom_order_id'] = $order->id;
                CustomOrderItem::create($item);
            }

            // Save images
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

    /**
     * Show edit form.
     */
    public function edit($id)
    {
        $order       = CustomOrder::with(['items', 'images', 'customer', 'vendor'])->findOrFail($id);
        $customers   = Customer::orderBy('name')->get();
        $vendors     = Vendor::orderBy('name')->get();
        $fabrics     = Fabric::where('status', 1)->orderBy('name')->get();
        $fabricPrices = FabricPrice::with('fabric')->where('status', 1)->get();

        return view('admin.custom-order.edit', compact(
            'order', 'customers', 'vendors', 'fabrics', 'fabricPrices'
        ));
    }

    /**
     * Update custom order.
     */
    public function update(Request $request, $id)
    {
        $order = CustomOrder::findOrFail($id);

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
        ]);

        try {
            DB::beginTransaction();

            // Recalculate from cart
            $totalQty  = 0;
            $subTotal  = 0;
            $cartItems = [];
            foreach ($request->cart_fabric_price_id as $idx => $fpId) {
                $fp  = FabricPrice::with('fabric')->findOrFail($fpId);
                $qty = (int) $request->cart_quantity[$idx];
                $lineTotal = $fp->price * $qty;
                $totalQty += $qty;
                $subTotal += $lineTotal;
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

            // Update items: delete old, insert new
            $order->items()->delete();
            foreach ($cartItems as $item) {
                $item['custom_order_id'] = $order->id;
                CustomOrderItem::create($item);
            }

            // Delete removed images
            if ($request->has('remove_images')) {
                $removeIds = $request->remove_images;
                $toRemove  = CustomOrderImage::whereIn('id', $removeIds)->where('custom_order_id', $order->id)->get();
                foreach ($toRemove as $img) {
                    $path = public_path($img->image);
                    if (File::exists($path)) File::delete($path);
                    $img->delete();
                }
            }

            // Add new images
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
            toastr()->success('Custom order updated successfully.');
            return redirect()->route('admin.custom-order.index');
        } catch (\Exception $e) {
            DB::rollBack();
            toastr()->error('Error: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Delete custom order.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $order = CustomOrder::findOrFail($id);

            // Delete images from disk
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

    /**
     * AJAX: Assign vendor from list page.
     */
    public function assignVendor(Request $request)
    {
        $request->validate([
            'order_id'  => 'required|exists:custom_orders,id',
            'vendor_id' => 'nullable|exists:vendors,id',
        ]);

        $order = CustomOrder::findOrFail($request->order_id);
        $order->update(['vendor_id' => $request->vendor_id ?: null]);

        return response()->json(['success' => 'Vendor assigned successfully.']);
    }

    /**
     * AJAX: Get fabric prices filtered by type & sleeve.
     */
    public function getFabricPrices(Request $request)
    {
        $query = FabricPrice::with('fabric')->where('status', 1);

        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->sleeve) {
            $query->where('sleeve', $request->sleeve);
        }

        $prices = $query->get()->map(function ($fp) {
            return [
                'id'          => $fp->id,
                'fabric_name' => $fp->fabric->name ?? '-',
                'type'        => $fp->type,
                'sleeve'      => $fp->sleeve,
                'price'       => $fp->price,
                'label'       => ($fp->fabric->name ?? '-') . ' - ' . ucfirst($fp->type) . ' - ' . ucfirst($fp->sleeve) . ' (৳' . number_format($fp->price, 2) . ')',
            ];
        });

        return response()->json($prices);
    }
}
