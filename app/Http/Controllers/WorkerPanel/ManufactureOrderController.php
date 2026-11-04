<?php

namespace App\Http\Controllers\WorkerPanel;

use App\Http\Controllers\Controller;
use App\Models\Manufacture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ManufactureOrderController extends Controller
{
    public function index(Request $request)
    {
        $worker = Auth::guard('worker')->user();

        if ($request->ajax()) {
            $orders = Manufacture::with([
                'product.recipe',
                'items.product.recipe',
                'collectedBy',
                'creator',
            ])->where('worker_id', $worker->id);

            if ($request->filled('status')) {
                if ($request->status === 'pending') {
                    $orders->whereNull('collected_by');
                } elseif ($request->status === 'collected') {
                    $orders->whereNotNull('collected_by');
                }
            }

            return DataTables::of($orders->latest('id'))
                ->addIndexColumn()
                ->addColumn('date', fn ($row) => $row->created_at ? $row->created_at->format('d M Y') : 'N/A')
                ->addColumn('invoice_no', function ($row) {
                    return '<a href="' . route('mypanel.manufactures.show', $row->id) . '" class="badge badge-light-primary font-weight-bolder">' . e($row->invoice_no) . '</a>';
                })
                ->addColumn('product', function ($row) {
                    $firstItem = $row->items->first();
                    $p = $firstItem ? $firstItem->product : $row->product;

                    return $p ? '<strong class="text-dark">' . e($p->name) . '</strong>' : 'N/A';
                })
                ->addColumn('part_type', function ($row) {
                    $firstItem = $row->items->first();
                    $type = strtolower($firstItem ? ($firstItem->part_type ?? '') : '');
                    if (! $type) {
                        if ($row->body_total > 0 && $row->finishing_total > 0) {
                            $type = 'both';
                        } elseif ($row->body_total > 0) {
                            $type = 'body';
                        } elseif ($row->finishing_total > 0) {
                            $type = 'finishing';
                        }
                    }
                    if (in_array($type, ['body', 'body part'], true)) {
                        return '<span class="badge badge-light-info font-weight-bold">Body Part</span>';
                    }
                    if (in_array($type, ['finishing', 'finishing part'], true)) {
                        return '<span class="badge badge-light-primary font-weight-bold">Finishing Part</span>';
                    }
                    if ($type === 'both') {
                        return '<span class="badge badge-light-success font-weight-bold">Both</span>';
                    }

                    return '<span class="badge badge-light-secondary font-weight-bold">' . e(ucfirst($type ?: 'N/A')) . '</span>';
                })
                ->addColumn('manufacture_qty', fn ($row) => '<span class="badge badge-light-primary font-weight-bold">' . (int) $row->manufacture_qty . ' pcs</span>')
                ->addColumn('received_qty', fn ($row) => '<span class="badge badge-light-success font-weight-bold">' . (int) ($row->received_qty ?? 0) . ' pcs</span>')
                ->addColumn('total_price', fn ($row) => '<strong>৳ ' . number_format($row->grand_total, 2) . '</strong>')
                ->addColumn('collection_status', function ($row) {
                    if ($row->collected_by) {
                        return '<span class="badge badge-light-success font-weight-bold">Collected</span>';
                    }
                    $recv = (int) ($row->received_qty ?? 0);
                    $orderQty = (int) $row->manufacture_qty;
                    if ($recv > 0 && $recv < $orderQty) {
                        return '<span class="badge badge-light-info font-weight-bold">Partial (' . $recv . '/' . $orderQty . ')</span>';
                    }

                    return '<span class="badge badge-light-warning font-weight-bold">Pending</span>';
                })
                ->addColumn('action', function ($row) {
                    $html = '<a href="' . route('mypanel.manufactures.show', $row->id) . '" class="btn btn-sm btn-info" title="View"><i data-feather="eye"></i></a>';
                    if ($row->status >= 1) {
                        $html .= ' <a href="' . route('mypanel.manufactures.print', $row->id) . '" target="_blank" class="btn btn-sm btn-outline-primary" title="Print"><i data-feather="printer"></i></a>';
                    }

                    return $html;
                })
                ->rawColumns(['invoice_no', 'product', 'part_type', 'manufacture_qty', 'received_qty', 'total_price', 'collection_status', 'action'])
                ->make(true);
        }

        $totalOrders = Manufacture::where('worker_id', $worker->id)->count();
        $pendingOrders = Manufacture::where('worker_id', $worker->id)->whereNull('collected_by')->count();
        $collectedOrders = Manufacture::where('worker_id', $worker->id)->whereNotNull('collected_by')->count();

        return view('mypanel.manufactures.index', compact('worker', 'totalOrders', 'pendingOrders', 'collectedOrders'));
    }

    public function show($id)
    {
        $worker = Auth::guard('worker')->user();
        $order = Manufacture::with([
            'product.recipe',
            'items.product.recipe',
            'dealer',
            'collectedBy',
            'creator',
        ])->where('worker_id', $worker->id)->findOrFail($id);

        return view('mypanel.manufactures.show', compact('order', 'worker'));
    }

    public function print($id)
    {
        $worker = Auth::guard('worker')->user();
        $manufacture = Manufacture::with([
            'product.recipe',
            'items.product.recipe',
            'dealer',
            'worker',
        ])->where('worker_id', $worker->id)->findOrFail($id);

        return view('admin.manufacture.print', compact('manufacture'));
    }
}
