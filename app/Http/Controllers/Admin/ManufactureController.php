<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CostSetting;
use App\Models\Dealer;
use App\Models\Manufacture;
use App\Models\ManufactureOrderItem;
use App\Models\Product;
use App\Models\WebSetting;
use App\Models\Worker;
use App\Models\WorkerPayment;
use App\Http\Requests\StoreManufactureRequest;
use App\Http\Requests\UpdateManufactureRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManufactureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ManufactureOrderItem::with([
                'manufacture.dealer',
                'manufacture.worker',
                'manufacture.creator',
                'manufacture.completedBy',
                'manufacture.collectedBy',
                'product.recipe',
                'costSetting'
            ])->latest('id');

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereDate('created_at', '>=', $request->from_date)
                      ->whereDate('created_at', '<=', $request->to_date);
            }

            if ($request->filled('worker_id')) {
                $query->whereHas('manufacture', function ($q) use ($request) {
                    $q->where('worker_id', $request->worker_id);
                });
            }

            if ($request->filled('dealer_id')) {
                $query->whereHas('manufacture', function ($q) use ($request) {
                    $q->where('dealer_id', $request->dealer_id);
                });
            }

            if ($request->filled('status')) {
                $query->whereHas('manufacture', function ($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }

            if ($request->filled('part_type')) {
                $partType = $request->part_type;
                $query->where(function ($q) use ($partType) {
                    $q->where('part_type', $partType)
                      ->orWhere('part_type', $partType . ' part')
                      ->orWhere('part_type', 'both');
                });
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('product_image', function ($row) {
                    $product = $row->product;
                    $img = $product && $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png');
                    return '<div class="d-flex align-items-center"><img src="'.$img.'" width="44" height="44" style="object-fit:cover; border-radius:6px; box-shadow: 0 2px 4px rgba(0,0,0,0.08);"></div>';
                })
                ->addColumn('manufacture_image', function ($row) {
                    $product = $row->product;
                    $recipe = $product ? $product->recipe : null;
                    $imgs = $recipe && !empty($recipe->manufacture_images) ? $recipe->manufacture_images : [];
                    $img = !empty($imgs) && isset($imgs[0]) ? asset($imgs[0]) : asset('images/no-image.png');
                    $allImgs = !empty($imgs) ? array_map(function($im) { return asset($im); }, $imgs) : [$img];
                    $m = $row->manufacture;
                    $title = $product ? $product->name : 'Manufacture Blueprint';
                    $invoice = $m ? $m->invoice_no : '';
                    $printUrl = ($m && $m->status >= 1) ? route('admin.manufacture.print', $m->id) : '';

                    return '<div class="btn-preview-mfg-img"
                                 data-img="'.$img.'"
                                 data-all-images="'.e(json_encode($allImgs)).'"
                                 data-title="'.e($title).'"
                                 data-invoice="'.e($invoice).'"
                                 data-print-url="'.$printUrl.'"
                                 title="Click to view full blueprint">
                                <img src="'.$img.'" alt="Blueprint">
                                <div class="mfg-zoom-badge">
                                    <i data-feather="zoom-in" style="width: 10px; height: 10px;"></i>
                                </div>
                            </div>';
                })
                ->addColumn('invoice_no', function ($row) {
                    $m = $row->manufacture;
                    if (!$m) return 'N/A';
                    return '<a href="'.route('admin.manufacture.show', $m->id).'" class="badge badge-light-primary font-weight-bolder" style="font-size: 11px; letter-spacing: 0.3px;">'.e($m->invoice_no).'</a>';
                })
                ->addColumn('product_name', function ($row) {
                    $name = $row->product ? e($row->product->name) : 'N/A';
                    $html = '<div><strong class="text-dark">' . $name . '</strong>';
                    if (!empty($row->cost_title)) {
                        $html .= '<br><small class="text-primary font-weight-bold"><i data-feather="tag" style="width:11px; height:11px;"></i> ' . e($row->cost_title) . '</small>';
                    }
                    if (!empty($row->reff_invoice)) {
                        $html .= '<br><small class="text-muted"><i data-feather="file" style="width:11px; height:11px;"></i> Ref: ' . e($row->reff_invoice) . '</small>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('worker_name', function ($row) {
                    $worker = $row->manufacture ? $row->manufacture->worker : null;
                    $name = $worker->name ?? 'N/A';
                    $phone = $worker->phone ?? '';
                    return '<div><strong>' . e($name) . '</strong></div>' . ($phone ? '<small class="text-bold text-muted">' . e($phone) . '</small>' : '');
                })
                ->addColumn('dealer_name', function ($row) {
                    $m = $row->manufacture;
                    $name = $m->dealer_name ?? ($m->dealer->name ?? 'N/A');
                    $phone = $m->dealer_phone ?? ($m->dealer->phone ?? '');
                    return '<div><strong>' . e($name) . '</strong></div>' . ($phone ? '<small class="text-bold text-muted">' . e($phone) . '</small>' : '');
                })
                ->addColumn('manufacture_qty', function ($row) {
                    $orderQty = (int) ($row->qty ?? ($row->manufacture->manufacture_qty ?? 0));
                    $receivedQty = (int) ($row->received_qty ?? ($row->manufacture->received_qty ?? 0));
                    $pendingQty = max(0, $orderQty - $receivedQty);

                    $html = '<div class="text-center" style="min-width: 95px;">';
                    $html .= '<span class="badge badge-light-primary font-weight-bolder" style="font-size: 11px;" title="Total Order Quantity">Total: ' . $orderQty . '</span><br>';
                    $html .= '<span class="badge badge-light-success font-weight-bold mt-25 mr-25" style="font-size: 10px;" title="Received Quantity"><i data-feather="check" style="width:10px;height:10px;"></i> ' . $receivedQty . '</span>';
                    if ($pendingQty > 0) {
                        $html .= '<span class="badge badge-light-danger font-weight-bold mt-25" style="font-size: 10px;" title="Pending Quantity to Receive"><i data-feather="clock" style="width:10px;height:10px;"></i> ' . $pendingQty . '</span>';
                    } else {
                        $html .= '<span class="badge badge-light-secondary font-weight-normal mt-25" style="font-size: 10px;" title="Fully Received">Done</span>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('part_type', function ($row) {
                    $type = strtolower($row->part_type ?? '');
                    if ($type === 'body' || $type === 'body part') {
                        return '<span class="badge badge-light-info">Body Part</span>';
                    } elseif ($type === 'finishing' || $type === 'finishing part') {
                        return '<span class="badge badge-light-primary">Finishing Part</span>';
                    } elseif ($type === 'both') {
                        return '<span class="badge badge-light-success">Both</span>';
                    }
                    return '<span class="badge badge-light-secondary">' . e(ucfirst($row->part_type ?? 'N/A')) . '</span>';
                })
                ->addColumn('price', function ($row) {
                    return '৳' . number_format($row->unit_price, 2);
                })
                ->addColumn('totals', function ($row) {
                    return '<strong>৳' . number_format($row->total_price, 2) . '</strong>';
                })
                ->addColumn('status', function ($row) {
                    $m = $row->manufacture;
                    if (!$m) return 'N/A';
                    $checked = ($m->status == 1 || $m->is_confirm == 1 || $m->status == 2) ? 'checked' : '';
                    $disabled = $m->status == 2 ? 'disabled' : '';
                    return '<div class="custom-control custom-switch custom-switch-success">
                                <input type="checkbox" class="custom-control-input change-status" id="status_'.$row->id.'" data-id="'.$m->id.'" data-field="is_confirm" '.$checked.' '.$disabled.'>
                                <label class="custom-control-label" for="status_'.$row->id.'">
                                    <span class="switch-icon-left"><i data-feather="check"></i></span>
                                    <span class="switch-icon-right"><i data-feather="x"></i></span>
                                </label>
                            </div>';
                })
                ->addColumn('is_complete', function ($row) {
                    $m = $row->manufacture;
                    if (!$m) return 'N/A';
                    $checked = ($m->status == 2) ? 'checked' : '';
                    $disabled = ($m->status == 0 && $m->is_confirm == 0) ? 'disabled' : '';
                    if ($m->collected_by) {
                        $disabled = 'disabled';
                    }
                    return '<div class="custom-control custom-switch custom-switch-primary">
                                <input type="checkbox" class="custom-control-input change-status" id="complete_'.$row->id.'" data-id="'.$m->id.'" data-field="is_complete" '.$checked.' '.$disabled.'>
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
                    return $row->manufacture->creator->name ?? 'N/A';
                })
                ->addColumn('collected_by', function ($row) {
                    $m = $row->manufacture;
                    $orderQty = (int) ($row->qty ?? ($m->manufacture_qty ?? 0));
                    $receivedQty = (int) ($row->received_qty ?? ($m->received_qty ?? 0));

                    if ($receivedQty <= 0) {
                        return '<span class="badge badge-light-warning" title="No quantity received yet"><i data-feather="clock" style="width:11px;height:11px;" class="mr-25"></i> Receive Pending</span>';
                    } elseif ($receivedQty < $orderQty) {
                        return '<span class="badge badge-light-info" title="Partially received"><i data-feather="package" style="width:11px;height:11px;" class="mr-25"></i> Partial (' . $receivedQty . '/' . $orderQty . ')</span>';
                    } else {
                        $name = $m && $m->collectedBy ? $m->collectedBy->name : 'Received';
                        return '<span class="badge badge-light-success" title="Fully received and collected"><i data-feather="check-circle" style="width:11px;height:11px;" class="mr-25"></i> ' . e($name) . '</span>';
                    }
                })
                ->addColumn('action', function ($row) {
                    $m = $row->manufacture;
                    return $m ? view('admin.manufacture.action_button', ['manufacture' => $m])->render() : '';
                })
                ->filterColumn('invoice_no', function ($query, $keyword) {
                    $query->whereHas('manufacture', function ($q) use ($keyword) {
                        $q->where('invoice_no', 'like', "%{$keyword}%");
                    })->orWhere('reff_invoice', 'like', "%{$keyword}%");
                })
                ->filterColumn('product_name', function ($query, $keyword) {
                    $query->whereHas('product', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('worker_name', function ($query, $keyword) {
                    $query->whereHas('manufacture.worker', function ($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")->orWhere('phone', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('dealer_name', function ($query, $keyword) {
                    $query->whereHas('manufacture', function ($q) use ($keyword) {
                        $q->where('dealer_name', 'like', "%{$keyword}%")
                          ->orWhere('dealer_phone', 'like', "%{$keyword}%")
                          ->orWhereHas('dealer', function ($sub) use ($keyword) {
                              $sub->where('name', 'like', "%{$keyword}%")->orWhere('phone', 'like', "%{$keyword}%");
                          });
                    });
                })
                ->rawColumns(['product_image', 'manufacture_image', 'invoice_no', 'product_name', 'worker_name', 'dealer_name', 'manufacture_qty', 'part_type', 'totals', 'status', 'is_complete', 'collected_by', 'action'])
                ->make(true);
        }

        $workers = Worker::all();
        $dealers = Dealer::all();
        return view('admin.manufacture.index', compact('workers', 'dealers'));
    }

    public function create()
    {
        $products = Product::with(['recipe.costSetting.items.costHead'])
            ->where('is_manufacturer', 1)
            ->where(function ($query) {
                $query->where('status', 1)
                      ->orWhereHas('recipe');
            })
            ->get()
            ->sortByDesc(function ($product) {
                return ($product->recipe && $product->recipe->cost_setting_id) ? 1 : 0;
            })
            ->values();

        $costSettings = CostSetting::with(['items.costHead'])
            ->where('status', 1)
            ->get();

        $dealers = Dealer::where('status', 1)->get();
        $workers = Worker::all();

        // Generate unique invoice number
        $lastManufacture = Manufacture::orderBy('id', 'desc')->first();
        $nextId = $lastManufacture ? $lastManufacture->id + 1 : 1;
        $invoice_no = 'MF-' . date('Ymd') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        return view('admin.manufacture.create', compact('products', 'costSettings', 'dealers', 'workers', 'invoice_no'));
    }

    public function getPrices(Request $request)
    {
        if ($request->filled('cost_setting_id')) {
            $costSetting = CostSetting::with(['items.costHead'])->find($request->cost_setting_id);
            if ($costSetting) {
                return response()->json([
                    'cost_setting_id' => $costSetting->id,
                    'cost_title' => $costSetting->cost_title,
                    'body_part_price' => $costSetting->body_charge,
                    'finishing_part_price' => $costSetting->finishing_charge,
                ]);
            }
        }

        if ($request->filled('product_id')) {
            $product = Product::with(['recipe.costSetting.items.costHead'])->find($request->product_id);
            $recipe = $product ? $product->recipe : null;
            $costSetting = $recipe && $recipe->costSetting ? $recipe->costSetting : null;

            $bodyPrice = 0;
            $finishingPrice = 0;
            $costSettingId = null;
            $costTitle = null;

            if ($costSetting) {
                $bodyPrice = $costSetting->body_charge;
                $finishingPrice = $costSetting->finishing_charge;
                $costSettingId = $costSetting->id;
                $costTitle = $costSetting->cost_title;
            } elseif ($recipe) {
                $bodyPrice = (float) ($recipe->body_charge ?? 0);
                $finishingPrice = (float) ($recipe->finishing_charge ?? 0);
            }

            return response()->json([
                'product_id' => $request->product_id,
                'cost_setting_id' => $costSettingId,
                'cost_title' => $costTitle,
                'body_part_price' => $bodyPrice,
                'finishing_part_price' => $finishingPrice,
            ]);
        }

        return response()->json(['body_part_price' => 0, 'finishing_part_price' => 0]);
    }

    public function store(StoreManufactureRequest $request)
    {
        $validated = $request->validated();
        $dealer = Dealer::find($validated['dealer_id']);
        $items = array_values($validated['items']);
        $isConfirm = $request->has('is_confirm') && $request->is_confirm ? 1 : 0;

        DB::beginTransaction();
        try {
            $createdOrders = [];
            $lastManufacture = Manufacture::orderBy('id', 'desc')->lockForUpdate()->first();
            $baseId = $lastManufacture ? $lastManufacture->id : 0;

            foreach ($items as $index => $item) {
                $orderSeq = $baseId + $index + 1;
                $invoice_no = 'MF-' . date('Ymd') . '-' . str_pad($orderSeq, 5, '0', STR_PAD_LEFT);

                while (Manufacture::where('invoice_no', $invoice_no)->exists()) {
                    $orderSeq++;
                    $invoice_no = 'MF-' . date('Ymd') . '-' . str_pad($orderSeq, 5, '0', STR_PAD_LEFT);
                }

                $qty = (int) $item['qty'];
                $unitPrice = (float) $item['unit_price'];
                $itemTotal = $qty * $unitPrice;
                $partType = $item['part_type'];

                $bodyPrice = isset($item['body_part_price']) ? (float) $item['body_part_price'] : 0;
                $finishingPrice = isset($item['finishing_part_price']) ? (float) $item['finishing_part_price'] : 0;

                $bodyTotal = 0;
                $finishingTotal = 0;
                if ($partType === 'body') {
                    $bodyTotal = $itemTotal;
                } elseif ($partType === 'finishing') {
                    $finishingTotal = $itemTotal;
                } elseif ($partType === 'both') {
                    $bodyTotal = ($bodyPrice * $qty);
                    $finishingTotal = ($finishingPrice * $qty);
                }

                $manufacture = Manufacture::create([
                    'product_id' => $item['product_id'],
                    'invoice_no' => $invoice_no,
                    'reff_invoice' => $item['reff_invoice'] ?? null,
                    'dealer_id' => $validated['dealer_id'],
                    'dealer_name' => $dealer->name ?? null,
                    'dealer_phone' => $dealer->phone ?? null,
                    'dealer_address' => $dealer->address ?? null,
                    'worker_id' => $validated['worker_id'],
                    'manufacture_qty' => $qty,
                    'received_qty' => 0,
                    'body_part_price' => $bodyPrice,
                    'finishing_part_price' => $finishingPrice,
                    'body_total' => $bodyTotal,
                    'finishing_total' => $finishingTotal,
                    'grand_total' => $itemTotal,
                    'note' => $item['note'] ?? ($validated['note'] ?? null),
                    'is_confirm' => $isConfirm,
                    'status' => $isConfirm,
                    'created_by' => auth('admin')->id() ?? auth()->id(),
                ]);

                $costSetting = !empty($item['cost_setting_id']) ? CostSetting::find($item['cost_setting_id']) : null;
                $costTitle = $costSetting ? $costSetting->cost_title : ($item['cost_title'] ?? null);

                ManufactureOrderItem::create([
                    'manufacture_id' => $manufacture->id,
                    'product_id' => $item['product_id'],
                    'reff_invoice' => $item['reff_invoice'] ?? null,
                    'cost_setting_id' => $item['cost_setting_id'] ?? null,
                    'cost_title' => $costTitle,
                    'part_type' => $partType,
                    'body_part_price' => $bodyPrice,
                    'finishing_part_price' => $finishingPrice,
                    'unit_price' => $unitPrice,
                    'qty' => $qty,
                    'received_qty' => 0,
                    'total_price' => $itemTotal,
                    'note' => $item['note'] ?? null,
                ]);

                $createdOrders[] = $manufacture;
            }

            DB::commit();

            $firstOrder = $createdOrders[0] ?? null;
            $redirectUrl = count($createdOrders) === 1 && $firstOrder
                ? route('admin.manufacture.show', $firstOrder->id)
                : route('admin.manufacture.index');

            $msg = count($createdOrders) === 1
                ? 'Manufacture order created successfully with Invoice No: ' . $firstOrder->invoice_no
                : count($createdOrders) . ' individual manufacture orders created successfully with unique invoice numbers.';

            return response()->json([
                'success' => $msg,
                'redirect' => $redirectUrl
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $manufacture = Manufacture::with([
            'items.product.recipe',
            'items.costSetting',
            'product.recipe',
            'dealer',
            'worker',
            'creator',
            'completedBy',
            'collectedBy'
        ])->findOrFail($id);

        return view('admin.manufacture.show', compact('manufacture'));
    }

    public function edit(string $id)
    {
        $manufacture = Manufacture::with(['items.product.recipe', 'items.costSetting'])->findOrFail($id);
        if ($manufacture->is_confirm) {
            return redirect()->route('admin.manufacture.index')->with('error', 'Confirmed records cannot be edited.');
        }

        $existingProductIds = $manufacture->items->pluck('product_id')->filter()->toArray();
        $products = Product::with(['recipe.costSetting.items.costHead'])
            ->where(function ($q) use ($existingProductIds) {
                $q->where(function ($sub) {
                    $sub->where('is_manufacturer', 1)
                        ->where(function ($nested) {
                            $nested->where('status', 1)
                                   ->orWhereHas('recipe');
                        });
                });
                if (!empty($existingProductIds)) {
                    $q->orWhereIn('id', $existingProductIds);
                }
            })
            ->get()
            ->sortByDesc(function ($product) {
                return ($product->recipe && $product->recipe->cost_setting_id) ? 1 : 0;
            })
            ->values();

        $costSettings = CostSetting::with(['items.costHead'])
            ->where('status', 1)
            ->get();

        $dealers = Dealer::where('status', 1)->get();
        $workers = Worker::all();

        return view('admin.manufacture.edit', compact('manufacture', 'products', 'costSettings', 'dealers', 'workers'));
    }

    public function update(UpdateManufactureRequest $request, string $id)
    {
        $manufacture = Manufacture::findOrFail($id);
        if ($manufacture->is_confirm) {
            return response()->json(['error' => 'Confirmed records cannot be edited.'], 403);
        }

        $validated = $request->validated();
        $dealer = Dealer::find($validated['dealer_id']);

        DB::beginTransaction();
        try {
            $items = array_values($validated['items']);
            $firstItem = $items[0] ?? null;

            $totalQty = 0;
            $bodyTotal = 0;
            $finishingTotal = 0;
            $grandTotal = 0;

            foreach ($items as $item) {
                $qty = (int) $item['qty'];
                $unitPrice = (float) $item['unit_price'];
                $itemTotal = $qty * $unitPrice;
                $partType = $item['part_type'];

                $bodyPrice = isset($item['body_part_price']) ? (float) $item['body_part_price'] : 0;
                $finishingPrice = isset($item['finishing_part_price']) ? (float) $item['finishing_part_price'] : 0;

                if ($partType === 'body') {
                    $bodyTotal += $itemTotal;
                } elseif ($partType === 'finishing') {
                    $finishingTotal += $itemTotal;
                } elseif ($partType === 'both') {
                    $bodyTotal += ($bodyPrice * $qty);
                    $finishingTotal += ($finishingPrice * $qty);
                }

                $totalQty += $qty;
                $grandTotal += $itemTotal;
            }

            $isConfirm = $request->has('is_confirm') && $request->is_confirm ? 1 : 0;

            $manufacture->update([
                'product_id' => $firstItem['product_id'] ?? null,
                'invoice_no' => $validated['invoice_no'] ?? $manufacture->invoice_no,
                'reff_invoice' => $firstItem['reff_invoice'] ?? null,
                'dealer_id' => $validated['dealer_id'],
                'dealer_name' => $dealer->name ?? null,
                'dealer_phone' => $dealer->phone ?? null,
                'dealer_address' => $dealer->address ?? null,
                'worker_id' => $validated['worker_id'],
                'manufacture_qty' => $totalQty,
                'body_part_price' => $firstItem['body_part_price'] ?? 0,
                'finishing_part_price' => $firstItem['finishing_part_price'] ?? 0,
                'body_total' => $bodyTotal,
                'finishing_total' => $finishingTotal,
                'grand_total' => $grandTotal,
                'note' => $validated['note'] ?? null,
                'is_confirm' => $isConfirm,
                'status' => $isConfirm,
                'updated_by' => auth('admin')->id() ?? auth()->id(),
            ]);

            // Delete old items and re-create
            $manufacture->items()->delete();

            foreach ($items as $item) {
                $costSetting = !empty($item['cost_setting_id']) ? CostSetting::find($item['cost_setting_id']) : null;
                $costTitle = $costSetting ? $costSetting->cost_title : ($item['cost_title'] ?? null);

                $qty = (int) $item['qty'];
                $unitPrice = (float) $item['unit_price'];

                ManufactureOrderItem::create([
                    'manufacture_id' => $manufacture->id,
                    'product_id' => $item['product_id'],
                    'reff_invoice' => $item['reff_invoice'] ?? null,
                    'cost_setting_id' => $item['cost_setting_id'] ?? null,
                    'cost_title' => $costTitle,
                    'part_type' => $item['part_type'],
                    'body_part_price' => $item['body_part_price'] ?? 0,
                    'finishing_part_price' => $item['finishing_part_price'] ?? 0,
                    'unit_price' => $unitPrice,
                    'qty' => $qty,
                    'total_price' => $qty * $unitPrice,
                    'note' => $item['note'] ?? null,
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => 'Manufacture order updated successfully.',
                'redirect' => route('admin.manufacture.show', $manufacture->id)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        $manufacture = Manufacture::findOrFail($id);
        if ($manufacture->is_confirm) {
            return response()->json(['error' => 'Confirmed records cannot be deleted.'], 403);
        }

        $manufacture->delete();
        return response()->json(['success' => 'Manufacture order deleted successfully.']);
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

        $manufacture = Manufacture::findOrFail($request->id);

        if ($request->field === 'is_confirm') {
            $manufacture->is_confirm = $request->value;
            if ($request->value == 1 && $manufacture->status == 0) {
                $manufacture->status = 1;
            } elseif ($request->value == 0 && $manufacture->status == 1) {
                $manufacture->status = 0;
            }
        } elseif ($request->field === 'is_complete') {
            if ($request->value == 1) {
                $pending = $manufacture->manufacture_qty - ($manufacture->received_qty ?? 0);
                if ($pending > 0) {
                    return response()->json([
                        'error' => "Cannot mark as Complete: {$pending} unit(s) are still pending to be received. Please receive all items from the Receive Order page first."
                    ], 422);
                }
                $manufacture->status = 2;
                $manufacture->completed_by = auth()->id();
                $manufacture->is_confirm = 1;
            } else {
                if ($manufacture->collected_by) {
                    return response()->json(['error' => 'Cannot change complete status because the order has already been received/collected.'], 403);
                }
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
        $manufacture = Manufacture::with(['items.product.recipe', 'items.costSetting', 'product.recipe', 'worker'])->findOrFail($id);

        if ($manufacture->status < 1) {
            return "<script>alert('Please confirm this order before printing.'); window.close();</script>";
        }

        $setting = WebSetting::first();

        return view('admin.manufacture.print', compact('manufacture', 'setting'));
    }

    public function receiveUI()
    {
        $pending_orders = Manufacture::with(['items.product.recipe', 'items.costSetting', 'product.recipe', 'worker'])
            ->where(function ($q) {
                $q->where('is_confirm', 1)->orWhere('status', '>=', 1);
            })
            ->where(function ($q) {
                $q->whereColumn('received_qty', '<', 'manufacture_qty')
                  ->orWhereNull('received_qty');
            })
            ->latest('id')
            ->get();

        return view('admin.manufacture.receive', compact('pending_orders'));
    }

    public function receiveProcess(Request $request)
    {
        $ordersInput = [];

        if ($request->has('orders') && is_array($request->orders)) {
            $ordersInput = $request->orders;
        } elseif ($request->has('manufacture_ids') && is_array($request->manufacture_ids)) {
            foreach ($request->manufacture_ids as $mId) {
                $m = Manufacture::find($mId);
                if ($m) {
                    $pending = max(1, $m->manufacture_qty - ($m->received_qty ?? 0));
                    $ordersInput[] = [
                        'id' => $mId,
                        'receive_qty' => $pending
                    ];
                }
            }
        }

        if (empty($ordersInput)) {
            return response()->json(['error' => 'No orders selected to receive.'], 400);
        }

        DB::beginTransaction();
        try {
            $processedCount = 0;
            $totalReceivedUnits = 0;
            $totalWorkerPayment = 0;

            foreach ($ordersInput as $item) {
                $orderId = $item['id'] ?? null;
                $receiveQty = isset($item['receive_qty']) ? (int) $item['receive_qty'] : 0;

                if (!$orderId || $receiveQty <= 0) {
                    continue;
                }

                $order = Manufacture::with(['items', 'worker'])->lockForUpdate()->find($orderId);
                if (!$order) {
                    continue;
                }

                $pendingQty = max(0, $order->manufacture_qty - ($order->received_qty ?? 0));
                if ($receiveQty > $pendingQty) {
                    return response()->json([
                        'error' => "Receive quantity ({$receiveQty}) exceeds pending quantity ({$pendingQty}) for order {$order->invoice_no}."
                    ], 422);
                }

                // Increment received quantities
                $newReceivedQty = ($order->received_qty ?? 0) + $receiveQty;
                $order->received_qty = $newReceivedQty;

                // Also update the item(s)
                foreach ($order->items as $orderItem) {
                    $orderItem->received_qty = ($orderItem->received_qty ?? 0) + $receiveQty;
                    $orderItem->save();
                }

                // Check if all pending products received
                if ($newReceivedQty >= $order->manufacture_qty) {
                    $order->status = 2; // Is Complete!
                    $order->completed_by = auth()->id();
                    $order->collected_by = auth()->id();
                } else {
                    if ($order->status < 1) {
                        $order->status = 1;
                    }
                }
                $order->save();

                // Determine unit price for worker payment
                $firstItem = $order->items->first();
                $unitPrice = $firstItem ? (float) $firstItem->unit_price : (float) ($order->body_part_price > 0 ? $order->body_part_price : $order->finishing_part_price);
                $paymentAmount = $receiveQty * $unitPrice;

                // Increment Product Stock and log in ledger for manufactured goods
                $stockService = app(\App\Services\StockService::class);
                $receivedProductsCount = 0;
                foreach ($order->items as $orderItem) {
                    if ($orderItem->product_id) {
                        $stockService->recordIn(
                            $orderItem->product_id,
                            $receiveQty,
                            'manufacture_receive',
                            $order->id,
                            $order->invoice_no,
                            "Manufactured goods received from production ({$orderItem->part_type}) - Ref Invoice: {$order->invoice_no}"
                        );
                        $receivedProductsCount++;
                    }
                }
                if ($receivedProductsCount === 0 && $order->product_id) {
                    $stockService->recordIn(
                        $order->product_id,
                        $receiveQty,
                        'manufacture_receive',
                        $order->id,
                        $order->invoice_no,
                        "Manufactured goods received from production - Ref Invoice: {$order->invoice_no}"
                    );
                }

                // Task 4: Create Worker Payment record based on the received quantity!
                if ($order->worker_id && $paymentAmount > 0) {
                    WorkerPayment::create([
                        'manufacture_order_id' => $order->id,
                        'date' => date('Y-m-d'),
                        'worker_id' => $order->worker_id,
                        'received_qty' => $receiveQty,
                        'unit_price' => $unitPrice,
                        'total_amount' => $paymentAmount,
                        'paid_amount' => 0,
                        'due_amount' => $paymentAmount,
                        'created_by' => auth()->id(),
                        'status' => 'pending'
                    ]);
                    $totalWorkerPayment += $paymentAmount;
                }

                $processedCount++;
                $totalReceivedUnits += $receiveQty;
            }

            DB::commit();

            return response()->json([
                'success' => "Successfully received {$totalReceivedUnits} unit(s) across {$processedCount} order(s). Worker payment of ৳ " . number_format($totalWorkerPayment, 2) . " created in pending status."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
}
