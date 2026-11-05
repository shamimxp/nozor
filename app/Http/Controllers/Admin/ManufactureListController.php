<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manufacture;
use App\Models\ManufactureOrderItem;
use App\Models\Worker;
use App\Models\Dealer;

class ManufactureListController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manufacture.list');
    }

    /**
     * Common DataTable builder for Manufacture Order Items
     */
    private function buildDataTable($query)
    {
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

    private function getList(Request $request, $status, $page_title)
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
            ])
            ->whereHas('manufacture', function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest('id');

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

            if ($request->filled('part_type')) {
                $partType = $request->part_type;
                $query->where(function ($q) use ($partType) {
                    $q->where('part_type', $partType)
                      ->orWhere('part_type', $partType . ' part')
                      ->orWhere('part_type', 'both');
                });
            }

            return $this->buildDataTable($query);
        }

        $workers = Worker::all();
        $dealers = Dealer::all();
        $fixed_status = $status;
        return view('admin.manufacture.index', compact('workers', 'dealers', 'fixed_status', 'page_title'));
    }

    private function getListByPartType(Request $request, $part_type, $page_title)
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
            ])
            ->where(function ($q) use ($part_type) {
                $q->where('part_type', $part_type)
                  ->orWhere('part_type', $part_type . ' part')
                  ->orWhere('part_type', 'both');
            })
            ->latest('id');

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

            return $this->buildDataTable($query);
        }

        $workers = Worker::all();
        $dealers = Dealer::all();
        $fixed_part_type = $part_type;
        return view('admin.manufacture.index', compact('workers', 'dealers', 'fixed_part_type', 'page_title'));
    }

    public function bodyPart(Request $request)
    {
        return $this->getListByPartType($request, 'body', 'Body Part Orders');
    }

    public function finishingPart(Request $request)
    {
        return $this->getListByPartType($request, 'finishing', 'Finishing Part Orders');
    }

    public function pending(Request $request)
    {
        return $this->getList($request, 0, 'Pending Manufacture Orders');
    }

    public function confirm(Request $request)
    {
        return $this->getList($request, 1, 'Confirm Manufacture Orders');
    }

    public function complete(Request $request)
    {
        return $this->getList($request, 2, 'Complete Manufacture Orders');
    }
}
