<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Manufacture;
use App\Models\Worker;
use App\Models\Dealer;

class ManufactureListController extends Controller
{
    private function getList(Request $request, $status, $page_title)
    {
        if ($request->ajax()) {
            $query = Manufacture::with(['product.recipe', 'dealer', 'worker', 'completedBy', 'collectedBy'])
                ->where('status', $status)
                ->select('manufactures.*') // Free query optimization
                ->latest();

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

        $workers = Worker::all();
        $dealers = Dealer::all();
        $fixed_status = $status;
        return view('admin.manufacture.index', compact('workers', 'dealers', 'fixed_status', 'page_title'));
    }

    private function getListByPartType(Request $request, $part_type, $page_title)
    {
        if ($request->ajax()) {
            $query = Manufacture::with(['product.recipe', 'dealer', 'worker', 'completedBy', 'collectedBy'])
                ->select('manufactures.*')
                ->latest();

            if ($part_type == 'body') {
                $query->where('body_total', '>', 0);
            } elseif ($part_type == 'finishing') {
                $query->where('finishing_total', '>', 0);
            }

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
                    $disabled = ($row->status == 0 && $row->is_confirm == 0) ? 'disabled' : '';
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
