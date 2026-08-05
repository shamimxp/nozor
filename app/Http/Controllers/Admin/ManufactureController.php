<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ManufactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = \App\Models\Manufacture::with(['product.recipe', 'dealer', 'worker', 'completedBy', 'collectedBy'])->latest();

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereDate('created_at', '>=', $request->from_date)
                      ->whereDate('created_at', '<=', $request->to_date);
            }

            if ($request->filled('worker_id')) {
                $query->where('worker_id', $request->worker_id);
            }

            if ($request->filled('dealer_id')) {
                $query->where('dealer_id', $request->dealer_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('part_type')) {
                if ($request->part_type == 'body') {
                    $query->where('body_total', '>', 0);
                } elseif ($request->part_type == 'finishing') {
                    $query->where('finishing_total', '>', 0);
                }
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('product_image', function ($row) {
                    $img = $row->product && $row->product->featured_image ? asset(config('imagepath.product') . $row->product->featured_image) : asset('images/no-image.png');
                    return '<img src="'.$img.'" width="50" height="50" style="object-fit:cover; border-radius:4px;">';
                })
                ->addColumn('manufacture_image', function ($row) {
                    $recipe = $row->product ? $row->product->recipe : null;
                    $imgs = $recipe && !empty($recipe->manufacture_images) ? $recipe->manufacture_images : [];
                    $img = !empty($imgs) && isset($imgs[0]) ? asset($imgs[0]) : asset('images/no-image.png');
                    
                    if ($row->status >= 1) {
                        return '<a href="'.route('admin.manufacture.print', $row->id).'" target="_blank"><img src="'.$img.'" width="50" height="50" style="object-fit:cover; border-radius:4px;" title="Print"></a>';
                    } else {
                        return '<a href="javascript:void(0)" onclick="toastr.error(\'Please confirm this order before printing.\')"><img src="'.$img.'" width="50" height="50" style="object-fit:cover; border-radius:4px;" title="Please confirm to print"></a>';
                    }
                })
                ->addColumn('product_name', function ($row) {
                    return $row->product->name ?? 'N/A';
                })
                ->addColumn('dealer_name', function ($row) {
                    $name = $row->dealer_name ?? ($row->dealer->name ?? 'N/A');
                    $phone = $row->dealer_phone ?? ($row->dealer->phone ?? '');
                    return '<div><strong>' . $name . '</strong></div>' . ($phone ? '<small class="text-bold">' . $phone . '</small>' : '');
                })
                ->addColumn('worker_name', function ($row) {
                    $name = $row->worker->name ?? 'N/A';
                    $phone = $row->worker->phone ?? '';
                    return '<div><strong>' . $name . '</strong></div>' . ($phone ? '<small class="text-bold">' . $phone . '</small>' : '');
                })
                ->addColumn('part_type', function ($row) {
                    if ($row->body_total > 0 && $row->finishing_total > 0) return 'Both';
                    return $row->body_total > 0 ? 'Body Part' : 'Finishing Part';
                })
                ->addColumn('price', function ($row) {
                    if ($row->body_total > 0 && $row->finishing_total > 0) return number_format($row->body_part_price + $row->finishing_part_price, 2);
                    return $row->body_total > 0 ? number_format($row->body_part_price, 2) : number_format($row->finishing_part_price, 2);
                })
                ->addColumn('totals', function ($row) {
                    return number_format($row->grand_total, 2);
                })
                ->addColumn('status', function ($row) {
                    $checked = ($row->status == 1 || $row->is_confirm == 1 || $row->status == 2) ? 'checked' : '';
                    $disabled = $row->status == 2 ? 'disabled' : '';
                    return '<div class="custom-control custom-switch custom-switch-success">
                                <input type="checkbox" class="custom-control-input change-status" id="status_'.$row->id.'" data-id="'.$row->id.'" data-field="is_confirm" '.$checked.' '.$disabled.'>
                                <label class="custom-control-label" for="status_'.$row->id.'">
                                    <span class="switch-icon-left"><i data-feather="check"></i></span>
                                    <span class="switch-icon-right"><i data-feather="x"></i></span>
                                </label>
                            </div>';
                })
                ->addColumn('is_complete', function ($row) {
                    $checked = ($row->status == 2) ? 'checked' : '';
                    $disabled = ($row->status == 0 && $row->is_confirm == 0) ? 'disabled' : ''; // Can only complete if confirmed
                    return '<div class="custom-control custom-switch custom-switch-primary">
                                <input type="checkbox" class="custom-control-input change-status" id="complete_'.$row->id.'" data-id="'.$row->id.'" data-field="is_complete" '.$checked.' '.$disabled.'>
                                <label class="custom-control-label" for="complete_'.$row->id.'">
                                    <span class="switch-icon-left"><i data-feather="check"></i></span>
                                    <span class="switch-icon-right"><i data-feather="x"></i></span>
                                </label>
                            </div>';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('d M Y h:i A') : 'N/A';
                })
                ->addColumn('updated_at', function ($row) {
                    return $row->updated_at ? $row->updated_at->format('d M Y h:i A') : 'N/A';
                })
                ->addColumn('creaded_by', function ($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('collected_by', function ($row) {
                    return $row->collectedBy->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return view('admin.manufacture.action_button', ['manufacture' => $row])->render();
                })
                ->rawColumns(['product_image', 'manufacture_image', 'worker_name', 'dealer_name', 'totals', 'status', 'is_complete', 'collected_by', 'action'])
                ->make(true);
        }

        $workers = \App\Models\Worker::all();
        $dealers = \App\Models\Dealer::all();
        return view('admin.manufacture.index', compact('workers', 'dealers'));
    }

    public function create()
    {
        $products = \App\Models\Product::with('recipe')->where('status', 1)->get();
        $dealers = \App\Models\Dealer::where('status', 1)->get();
        $workers = \App\Models\Worker::all(); // Assuming Worker model exists
        // Generate unique invoice number
        $lastManufacture = \App\Models\Manufacture::orderBy('id', 'desc')->first();
        $nextId = $lastManufacture ? $lastManufacture->id + 1 : 1;
        $invoice_no = 'MF-' . date('Ymd') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        return view('admin.manufacture.create', compact('products', 'dealers', 'workers', 'invoice_no'));
    }

    public function getPrices(Request $request)
    {
        $setting = \App\Models\WebSetting::first();
        return response()->json([
            'body_part_price' => $setting ? $setting->body_part_price : 0,
            'finishing_part_price' => $setting ? $setting->finishing_part_price : 0,
        ]);
    }

    public function store(\App\Http\Requests\StoreManufactureRequest $request)
    {
        $validated = $request->validated();
        
        $setting = \App\Models\WebSetting::first();
        $dealer = \App\Models\Dealer::find($validated['dealer_id']);
        $qty = $validated['manufacture_qty'];
        
        \DB::beginTransaction();
        try {
          
            $lastManufacture = \App\Models\Manufacture::orderBy('id', 'desc')->first();
            $nextId = $lastManufacture ? $lastManufacture->id + 1 : 1;
            $invoice_no = 'MF-' . date('Ymd') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

            foreach ($validated['parts'] as $part) {
                $body_price = ($part === 'body') ? ($setting->body_part_price ?? 0) : 0;
                $finishing_price = ($part === 'finishing') ? ($setting->finishing_part_price ?? 0) : 0;
                
                $body_total = $body_price * $qty;
                $finishing_total = $finishing_price * $qty;

                \App\Models\Manufacture::create([
                    'product_id' => $validated['product_id'],
                    'invoice_no' => $invoice_no,
                    'reff_invoice' => $validated['reff_invoice'] ?? null,
                    'dealer_id' => $validated['dealer_id'],
                    'dealer_name' => $dealer->name ?? null,
                    'dealer_phone' => $dealer->phone ?? null,
                    'dealer_address' => $dealer->address ?? null,
                    'worker_id' => $validated['worker_id'],
                    'manufacture_qty' => $qty,
                    'body_part_price' => $body_price,
                    'finishing_part_price' => $finishing_price,
                    'body_total' => $body_total,
                    'finishing_total' => $finishing_total,
                    'grand_total' => $body_total + $finishing_total,
                    'note' => $validated['note'] ?? null,
                    'is_confirm' => $request->has('is_confirm') ? 1 : 0,
                    'status' => $request->has('is_confirm') ? 1 : 0,
                    'created_by' => auth('admin')->id() ?? auth()->id(),
                ]);
            }
            
            \DB::commit();
            return response()->json(['success' => 'Manufacture created successfully.']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $manufacture = \App\Models\Manufacture::with(['product', 'dealer', 'creator'])->findOrFail($id);
        return view('admin.manufacture.show', compact('manufacture'));
    }

    public function edit(string $id)
    {
        $manufacture = \App\Models\Manufacture::findOrFail($id);
        if ($manufacture->is_confirm) {
            return redirect()->route('admin.manufacture.index')->with('error', 'Confirmed records cannot be edited.');
        }

        $products = \App\Models\Product::with('recipe')->where('status', 1)->get();
        $dealers = \App\Models\Dealer::where('status', 1)->get();
        $workers = \App\Models\Worker::all();
        return view('admin.manufacture.edit', compact('manufacture', 'products', 'dealers', 'workers'));
    }

    public function update(\App\Http\Requests\UpdateManufactureRequest $request, string $id)
    {
        $manufacture = \App\Models\Manufacture::findOrFail($id);
        if ($manufacture->is_confirm) {
            return response()->json(['error' => 'Confirmed records cannot be edited.'], 403);
        }

        $validated = $request->validated();
        
        $setting = \App\Models\WebSetting::first();
        $dealer = \App\Models\Dealer::find($validated['dealer_id']);
        
        $body_price = in_array('body', $validated['parts']) ? ($setting->body_part_price ?? 0) : 0;
        $finishing_price = in_array('finishing', $validated['parts']) ? ($setting->finishing_part_price ?? 0) : 0;
        
        $qty = $validated['manufacture_qty'];
        $body_total = $body_price * $qty;
        $finishing_total = $finishing_price * $qty;
        
        \DB::beginTransaction();
        try {
            // For update, we will update the existing single record
            // If they modify parts, we update the prices accordingly
            $manufacture->update([
                'invoice_no' => $validated['invoice_no'] ?? $manufacture->invoice_no,
                'reff_invoice' => $validated['reff_invoice'] ?? null,
                'product_id' => $validated['product_id'],
                'dealer_id' => $validated['dealer_id'],
                'dealer_name' => $dealer->name ?? null,
                'dealer_phone' => $dealer->phone ?? null,
                'dealer_address' => $dealer->address ?? null,
                'worker_id' => $validated['worker_id'],
                'manufacture_qty' => $qty,
                'body_part_price' => $body_price,
                'finishing_part_price' => $finishing_price,
                'body_total' => $body_total,
                'finishing_total' => $finishing_total,
                'grand_total' => $body_total + $finishing_total,
                'note' => $validated['note'] ?? null,
                'is_confirm' => $request->has('is_confirm') ? 1 : 0,
                'status' => $request->has('is_confirm') ? 1 : 0,
                'updated_by' => auth('admin')->id() ?? auth()->id(),
            ]);
            
            \DB::commit();
            return response()->json(['success' => 'Manufacture updated successfully.']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        $manufacture = \App\Models\Manufacture::findOrFail($id);
        if ($manufacture->is_confirm) {
            return response()->json(['error' => 'Confirmed records cannot be deleted.'], 403);
        }
        
        $manufacture->delete();
        return response()->json(['success' => 'Manufacture deleted successfully.']);
    }

    public function export(Request $request)
    {
        return (new \App\Exports\ManufactureExport)->export($request);
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:manufactures,id',
            'field' => 'required|in:is_confirm,is_complete,collected_by',
            'value' => 'nullable'
        ]);

        $manufacture = \App\Models\Manufacture::findOrFail($request->id);
        
        if ($request->field === 'is_confirm') {
            $manufacture->is_confirm = $request->value;
            if ($request->value == 1 && $manufacture->status == 0) {
                $manufacture->status = 1;
            } elseif ($request->value == 0 && $manufacture->status == 1) {
                $manufacture->status = 0;
            }
        } elseif ($request->field === 'is_complete') {
            if ($request->value == 1) {
                $manufacture->status = 2;
                $manufacture->completed_by = auth()->id();
                // Ensure it's marked as confirmed if not already
                $manufacture->is_confirm = 1; 
            } else {
                $manufacture->status = $manufacture->is_confirm ? 1 : 0;
                $manufacture->completed_by = null;
            }
        } elseif ($request->field === 'collected_by') {
            $manufacture->collected_by = $request->value;
        }
        
        $manufacture->save();

        return response()->json(['success' => 'Status updated successfully.']);
    }

    public function print($id)
    {
        $manufacture = \App\Models\Manufacture::with(['product.recipe', 'worker'])->findOrFail($id);
        
        if ($manufacture->status < 1) {
            return "<script>alert('Please confirm this order before printing.'); window.close();</script>";
        }

        $setting = \App\Models\WebSetting::first(); 
        
        return view('admin.manufacture.print', compact('manufacture', 'setting'));
    }
}
