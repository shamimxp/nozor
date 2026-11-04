<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\Concerns\HandlesTrash;
use App\Models\Worker;
use App\Services\Deletion\SoftDeleteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class WorkerController extends Controller
{
    use HandlesTrash;

    protected function trashModuleKey(): string
    {
        return 'worker';
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Worker::query();
            
            if ($request->filled('name')) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            if ($request->filled('phone')) {
                $query->where('phone', 'like', '%' . $request->phone . '%');
            }
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $workers = $query->latest()->get();
            return DataTables::of($workers)
                ->addIndexColumn()
                ->addColumn('type_label', function($row){
                    return $row->type == 1 ? 'Finishing Part' : 'Body Part';
                })
                ->addColumn('status_label', function($row){
                    return $row->status == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('image', function($row){
                    if($row->profile_image) {
                        return '<img src="'.asset('storage/'.$row->profile_image).'" width="50" height="50" style="object-fit:cover; border-radius:50%;">';
                    }
                    return 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('admin.worker.show', $row->id) . '" class="btn btn-info btn-sm" title="View Profile"><i data-feather="eye"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm editWorker"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteWorker"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status_label', 'image'])
                ->make(true);
        }
        return view('admin.worker.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email|unique:workers,email',
            'type' => 'required|in:1,2',
            'password' => 'required|confirmed',
            'status' => 'required|in:0,1',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['password', 'password_confirmation', 'profile_image']);
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('workers', 'public');
        }

        Worker::create($data);

        return response()->json(['success' => 'Worker created successfully.']);
    }

    public function show(\Illuminate\Http\Request $request, $id)
    {
        $worker = Worker::findOrFail($id);
        
        if ($request->ajax()) {
            if ($request->get('type') === 'orders') {
                $orders = \App\Models\Manufacture::with([
                    'product.recipe', 
                    'items.product.recipe', 
                    'collectedBy', 
                    'creator'
                ])
                ->where('worker_id', $worker->id);

                if ($request->filled('status')) {
                    if ($request->status === 'pending') {
                        $orders->whereNull('collected_by');
                    } elseif ($request->status === 'collected') {
                        $orders->whereNotNull('collected_by');
                    }
                }

                return datatables()->of($orders->latest('id'))
                    ->addIndexColumn()
                    ->addColumn('date', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M Y') : 'N/A';
                    })
                    ->addColumn('manufacture_image', function ($row) {
                        $firstItem = $row->items->first();
                        $p = $firstItem ? $firstItem->product : $row->product;
                        $recipe = $p ? $p->recipe : null;
                        $imgs = $recipe && !empty($recipe->manufacture_images) ? $recipe->manufacture_images : [];
                        $img = !empty($imgs) && isset($imgs[0]) ? asset($imgs[0]) : ($p && $p->featured_image ? asset(config('imagepath.product') . $p->featured_image) : asset('images/no-image.png'));
                        $allImgs = !empty($imgs) ? array_map(function($im) { return asset($im); }, $imgs) : [$img];
                        $title = $p ? $p->name : 'Manufacture Blueprint';
                        $invoice = $row->invoice_no ?? '';
                        $printUrl = ($row->status >= 1) ? route('admin.manufacture.print', $row->id) : '';

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
                        return '<a href="' . route('admin.manufacture.show', $row->id) . '" class="badge badge-light-primary font-weight-bolder" style="font-size: 11px;">' . e($row->invoice_no) . '</a>';
                    })
                    ->addColumn('product', function ($row) {
                        $firstItem = $row->items->first();
                        $p = $firstItem ? $firstItem->product : $row->product;
                        return $p ? '<strong class="text-dark">' . e($p->name) . '</strong>' : 'N/A';
                    })
                    ->addColumn('part_type', function ($row) {
                        $firstItem = $row->items->first();
                        $type = strtolower($firstItem ? ($firstItem->part_type ?? '') : '');
                        if (!$type) {
                            if ($row->body_total > 0 && $row->finishing_total > 0) $type = 'both';
                            elseif ($row->body_total > 0) $type = 'body';
                            elseif ($row->finishing_total > 0) $type = 'finishing';
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
                    ->addColumn('manufacture_qty', function ($row) {
                        return '<span class="badge badge-light-primary font-weight-bold">' . (int)$row->manufacture_qty . ' pcs</span>';
                    })
                    ->addColumn('received_qty', function ($row) {
                        $recv = (int)($row->received_qty ?? 0);
                        return '<span class="badge badge-light-success font-weight-bold">' . $recv . ' pcs</span>';
                    })
                    ->addColumn('pending_qty', function ($row) {
                        $pending = max(0, (int)$row->manufacture_qty - (int)($row->received_qty ?? 0));
                        if ($pending > 0) {
                            return '<span class="badge badge-light-danger font-weight-bold">' . $pending . ' pcs</span>';
                        }
                        return '<span class="badge badge-light-secondary font-weight-normal">0 pcs</span>';
                    })
                    ->addColumn('total_price', function ($row) {
                        return '<strong class="text-dark font-weight-bold">৳ ' . number_format($row->grand_total, 2) . '</strong>';
                    })
                    ->addColumn('collection_status', function ($row) {
                        if ($row->collected_by) {
                            $name = $row->collectedBy ? $row->collectedBy->name : 'Collected';
                            return '<span class="badge badge-light-success font-weight-bold"><i data-feather="check-circle" style="width:11px;height:11px;" class="mr-25"></i> Collected</span><br><small class="text-muted">' . e($name) . '</small>';
                        } else {
                            $recv = (int)($row->received_qty ?? 0);
                            $orderQty = (int)$row->manufacture_qty;
                            if ($recv > 0 && $recv < $orderQty) {
                                return '<span class="badge badge-light-info font-weight-bold"><i data-feather="package" style="width:11px;height:11px;" class="mr-25"></i> Partial (' . $recv . '/' . $orderQty . ')</span>';
                            }
                            return '<span class="badge badge-light-warning font-weight-bold"><i data-feather="clock" style="width:11px;height:11px;" class="mr-25"></i> Pending</span>';
                        }
                    })
                    ->addColumn('action', function ($row) {
                        $html = '<div class="btn-group">';
                        $html .= '<a href="' . route('admin.manufacture.show', $row->id) . '" class="btn btn-sm btn-info p-50 rounded mr-25" title="View Order"><i data-feather="eye"></i></a>';
                        if ($row->status >= 1) {
                            $html .= '<a href="' . route('admin.manufacture.print', $row->id) . '" target="_blank" class="btn btn-sm btn-outline-primary p-50 rounded" title="Print"><i data-feather="printer"></i></a>';
                        }
                        $html .= '</div>';
                        return $html;
                    })
                    ->rawColumns(['manufacture_image', 'invoice_no', 'product', 'part_type', 'manufacture_qty', 'received_qty', 'pending_qty', 'total_price', 'collection_status', 'action'])
                    ->make(true);
            }

            $payments = \App\Models\WorkerPayment::with(['manufacture.items.product', 'manufacture.product', 'creator'])
                        ->where('worker_id', $worker->id)
                        ->latest();

            return datatables()->of($payments)
                ->addIndexColumn()
                ->addColumn('date', function ($row) {
                    return $row->date ? date('d M Y', strtotime($row->date)) : 'N/A';
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
                ->addColumn('status', function ($row) {
                    if ($row->status == 'paid' || $row->due_amount <= 0) {
                        return '<span class="badge badge-light-success font-weight-bold">Paid</span>';
                    } elseif ($row->status == 'partially_paid' || ($row->paid_amount > 0 && $row->due_amount > 0)) {
                        return '<span class="badge badge-light-info font-weight-bold">Partially Paid</span>';
                    } else {
                        return '<span class="badge badge-light-warning font-weight-bold">Pending</span>';
                    }
                })
                ->addColumn('total_amount', function ($row) {
                    return '৳ ' . number_format($row->total_amount, 2);
                })
                ->rawColumns(['status', 'invoice_no', 'product', 'part_type'])
                ->make(true);
        }
        
        $totalOrders = \App\Models\Manufacture::where('worker_id', $id)->count();
        $completedOrders = \App\Models\Manufacture::where('worker_id', $id)->whereNotNull('collected_by')->count();
        $pendingOrders = \App\Models\Manufacture::where('worker_id', $id)->whereNull('collected_by')->count();

        $totalAmount = \App\Models\WorkerPayment::where('worker_id', $id)->sum('total_amount');
        $totalPaid = \App\Models\WorkerPayment::where('worker_id', $id)->where('status', 'paid')->sum('total_amount');
        $totalDue = \App\Models\WorkerPayment::where('worker_id', $id)->where('status', 'pending')->sum('total_amount');

        return view('admin.worker.show', compact(
            'worker', 
            'totalOrders', 'completedOrders', 'pendingOrders',
            'totalAmount', 'totalPaid', 'totalDue'
        ));
    }

    public function edit($id)
    {
        $worker = Worker::findOrFail($id);
        return response()->json($worker);
    }

    public function update(Request $request, $id)
    {
        $worker = Worker::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email|unique:workers,email,' . $id,
            'type' => 'required|in:1,2',
            'password' => 'nullable|confirmed',
            'status' => 'required|in:0,1',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['password', 'password_confirmation', 'profile_image']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            if ($worker->profile_image) {
                Storage::disk('public')->delete($worker->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('workers', 'public');
        }

        $worker->update($data);

        return response()->json(['success' => 'Worker updated successfully.']);
    }

    public function destroy($id, SoftDeleteService $softDeleteService)
    {
        return $this->softDeleteRecord($id, $softDeleteService);
    }
}
