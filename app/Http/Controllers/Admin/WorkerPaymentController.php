<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkerPayment;
use App\Models\Worker;
use App\Models\Manufacture;

class WorkerPaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = WorkerPayment::with(['worker', 'manufacture.items.product.recipe', 'manufacture.product.recipe', 'creator'])->latest('id');

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->whereDate('date', '>=', $request->from_date)
                      ->whereDate('date', '<=', $request->to_date);
            }

            if ($request->filled('worker_id')) {
                $query->where('worker_id', $request->worker_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    return $row->date ? date('d M Y', strtotime($row->date)) : 'N/A';
                })
                ->addColumn('manufacture_image', function ($row) {
                    $m = $row->manufacture;
                    $firstItem = $m ? $m->items->first() : null;
                    $p = $firstItem ? $firstItem->product : ($m ? $m->product : null);
                    $recipe = $p ? $p->recipe : null;
                    $imgs = $recipe && !empty($recipe->manufacture_images) ? $recipe->manufacture_images : [];
                    $img = !empty($imgs) && isset($imgs[0]) ? asset($imgs[0]) : asset('images/no-image.png');
                    $allImgs = !empty($imgs) ? array_map(function($im) { return asset($im); }, $imgs) : [$img];
                    $title = $p ? $p->name : 'Manufacture Blueprint';
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
                ->addColumn('worker_name', function ($row) {
                    $name = $row->worker->name ?? 'N/A';
                    $phone = $row->worker->phone ?? '';
                    return '<div><strong>' . e($name) . '</strong></div>' . ($phone ? '<small class="text-bold text-muted">' . e($phone) . '</small>' : '');
                })
                ->addColumn('invoice_no', function ($row) {
                    if (!$row->manufacture) return 'N/A';
                    return '<a href="' . route('admin.manufacture.show', $row->manufacture->id) . '" class="badge badge-light-primary font-weight-bolder" style="font-size: 11px;">' . e($row->manufacture->invoice_no) . '</a>';
                })
                ->addColumn('product', function ($row) {
                    $m = $row->manufacture;
                    $firstItem = $m ? $m->items->first() : null;
                    $p = $firstItem ? $firstItem->product : ($m ? $m->product : null);
                    return $p ? '<strong class="text-dark">' . e($p->name) . '</strong>' : 'N/A';
                })
                ->addColumn('part_type', function ($row) {
                    $m = $row->manufacture;
                    $firstItem = $m ? $m->items->first() : null;
                    $type = strtolower($firstItem ? ($firstItem->part_type ?? '') : '');
                    if (!$type && $m) {
                        if ($m->body_total > 0 && $m->finishing_total > 0) $type = 'both';
                        elseif ($m->body_total > 0) $type = 'body';
                        elseif ($m->finishing_total > 0) $type = 'finishing';
                    }
                    if ($type === 'body' || $type === 'body part') {
                        return '<span class="badge badge-light-info font-weight-bold">Body Part</span>';
                    } elseif ($type === 'finishing' || $type === 'finishing part') {
                        return '<span class="badge badge-light-primary font-weight-bold">Finishing Part</span>';
                    } elseif ($type === 'both') {
                        return '<span class="badge badge-light-success font-weight-bold">Both</span>';
                    }
                    return '<span class="badge badge-light-secondary font-weight-bold">' . e(ucfirst($type ?: 'N/A')) . '</span>';
                })
                ->addColumn('quantity', function ($row) {
                    $qty = $row->received_qty ?? ($row->manufacture ? $row->manufacture->manufacture_qty : null);
                    if ($qty !== null && $qty !== '') {
                        $html = '<div class="text-center"><span class="badge badge-light-primary font-weight-bolder" style="font-size: 12px; padding: 4px 8px;">' . (int)$qty . ' pcs</span>';
                        if ($row->unit_price) {
                            $html .= '<br><small class="text-muted font-weight-bold">@ ৳ ' . number_format($row->unit_price, 2) . '</small>';
                        }
                        $html .= '</div>';
                        return $html;
                    }
                    return '<span class="text-muted">—</span>';
                })
                ->addColumn('total_amount', function ($row) {
                    return '<strong class="text-success" style="font-size: 13px;">৳ ' . number_format($row->total_amount, 2) . '</strong>';
                })
                ->addColumn('status', function ($row) {
                    if ($row->status === 'paid') {
                        return '<span class="badge badge-pill badge-light-success font-weight-bolder" style="font-size: 11px; padding: 6px 12px;"><i data-feather="check-circle" style="width: 12px; height: 12px;" class="mr-25"></i> Paid</span>';
                    }

                    return '<div class="custom-control custom-switch custom-switch-success">
                                <input type="checkbox" class="custom-control-input change-status" id="status_'.$row->id.'" data-id="'.$row->id.'" data-field="status">
                                <label class="custom-control-label font-weight-bold cursor-pointer" for="status_'.$row->id.'">
                                    <span class="badge badge-light-warning">Pending</span>
                                </label>
                            </div>';
                })
                ->addColumn('created_by', function ($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="btn-group">';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger delete-record" data-id="'.$row->id.'" title="Delete"><i data-feather="trash-2"></i></button>';
                    $btn .= '</div>';
                    if($row->status === 'paid') {
                        return '<span class="badge badge-light-success font-weight-bolder" style="font-size: 11px; padding: 6px 12px;"><i data-feather="check-circle" style="width: 12px; height: 12px;" class="mr-25"></i> Paid</span>';
                    }else{
                        return $btn;
                    }

                })
                ->rawColumns(['worker_name', 'manufacture_image', 'invoice_no', 'product', 'part_type', 'quantity', 'total_amount', 'status', 'action'])
                ->make(true);
        }

        $workers = Worker::all();
        $page_title = 'Worker Payments';

        return view('admin.worker_payment.index', compact('workers', 'page_title'));
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:worker_payments,id',
        ]);

        $payment = WorkerPayment::findOrFail($request->id);

        if ($payment->status === 'paid') {
            return response()->json(['error' => 'This payment is already paid and cannot be changed back to pending.'], 403);
        }

        $payment->status = 'paid';
        $payment->save();

        return response()->json(['success' => 'Worker payment status changed to Paid successfully.']);
    }

    public function destroy($id)
    {
        $payment = WorkerPayment::findOrFail($id);
        $payment->delete();

        return response()->json(['success' => 'Worker payment record deleted successfully']);
    }
}
