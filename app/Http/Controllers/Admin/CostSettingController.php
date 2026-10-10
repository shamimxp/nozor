<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CostHead;
use App\Models\CostSetting;
use App\Models\CostSettingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CostSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = CostSetting::with('items.costHead')->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('cost_title', function ($row) {
                    return '<strong>' . e($row->cost_title) . '</strong>';
                })
                ->addColumn('total_cost', function ($row) {
                    $total = $row->items->sum('cost_price');
                    return '৳' . number_format($total, 2);
                })
                ->addColumn('heads_count', function ($row) {
                    return '<span class="badge badge-light-primary">' . $row->items->count() . ' Heads</span>';
                })
                ->addColumn('status', function ($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch custom-switch-success">
                                <input type="checkbox" class="custom-control-input changeStatus" ' . $checked . ' data-id="' . $row->id . '" id="status' . $row->id . '">
                                <label class="custom-control-label" for="status' . $row->id . '">
                                    <span class="switch-icon-left"><i data-feather="check"></i></span>
                                    <span class="switch-icon-right"><i data-feather="x"></i></span>
                                </label>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('admin.cost-setting.show', $row->id) . '" class="btn btn-info btn-sm p-0 px-25 mr-50" title="View"><i data-feather="eye"></i></a>';
                    $btn .= '<a href="' . route('admin.cost-setting.edit', $row->id) . '" class="btn btn-primary btn-sm p-0 px-25 mr-50" title="Edit"><i data-feather="edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm p-0 px-25 deleteBtn" title="Delete"><i data-feather="trash-2"></i></a>';
                    return $btn;
                })
                ->rawColumns(['cost_title', 'total_cost', 'heads_count', 'status', 'action'])
                ->make(true);
        }

        return view('admin.cost-setting.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $costHeads = CostHead::where('status', 1)->orderBy('title', 'asc')->get();
        return view('admin.cost-setting.create', compact('costHeads'));
    }

    /**
     * Check if cost title already exists for real-time validation.
     */
    public function checkTitle(Request $request)
    {
        $title = trim($request->cost_title ?? '');
        $id = $request->id;

        if ($title === '') {
            return response()->json(['valid' => true]);
        }

        $query = CostSetting::where('cost_title', $title);
        if (!empty($id)) {
            $query->where('id', '!=', $id);
        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This cost title already exists. Please choose a different title.' : 'Cost title is available.'
        ]);
    }

    /**
     * Get item details of a specific Cost Setting for Product Recipe form (read-only auto details).
     */
    public function getDetails($id)
    {
        $costSetting = CostSetting::with(['items.costHead'])->findOrFail($id);

        $items = $costSetting->items->map(function ($item) {
            return [
                'cost_head_id' => $item->cost_head_id,
                'title' => $item->costHead->title ?? 'N/A',
                'cost_price' => (float) $item->cost_price,
            ];
        });

        return response()->json([
            'success' => true,
            'cost_setting_id' => $costSetting->id,
            'cost_title' => $costSetting->cost_title,
            'total_cost' => (float) $costSetting->items->sum('cost_price'),
            'items' => $items,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cost_title' => 'required|string|max:255|unique:cost_settings,cost_title',
            'status' => 'nullable|in:0,1',
            'heads' => 'required|array',
            'heads.*.cost_head_id' => 'required|exists:cost_heads,id',
            'heads.*.cost_price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $costSetting = CostSetting::create([
                'cost_title' => trim($request->cost_title),
                'status' => $request->has('status') ? (int) $request->status : 1,
            ]);

            foreach ($request->heads as $headData) {
                $costSetting->items()->create([
                    'cost_head_id' => $headData['cost_head_id'],
                    'cost_price' => isset($headData['cost_price']) && $headData['cost_price'] !== '' ? $headData['cost_price'] : 0,
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Cost Setting created successfully.',
                    'redirect' => route('admin.cost-setting.index')
                ]);
            }

            return redirect()->route('admin.cost-setting.index')->with('success', 'Cost Setting created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to save Cost Setting: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Failed to save Cost Setting: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $costSetting = CostSetting::with(['items.costHead'])->findOrFail($id);
        return view('admin.cost-setting.show', compact('costSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $costSetting = CostSetting::with('items')->findOrFail($id);

        // Fetch all cost heads (active ones, plus any currently attached even if inactive)
        $costHeads = CostHead::orderBy('title', 'asc')->get();

        // Index existing prices by cost_head_id for simple lookup
        $itemPrices = $costSetting->items->pluck('cost_price', 'cost_head_id')->toArray();

        return view('admin.cost-setting.edit', compact('costSetting', 'costHeads', 'itemPrices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $costSetting = CostSetting::findOrFail($id);

        $request->validate([
            'cost_title' => 'required|string|max:255|unique:cost_settings,cost_title,' . $id,
            'status' => 'nullable|in:0,1',
            'heads' => 'required|array',
            'heads.*.cost_head_id' => 'required|exists:cost_heads,id',
            'heads.*.cost_price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $costSetting->update([
                'cost_title' => trim($request->cost_title),
                'status' => $request->has('status') ? (int) $request->status : 1,
            ]);

            // Sync items: delete old items and re-create
            $costSetting->items()->delete();

            foreach ($request->heads as $headData) {
                $costSetting->items()->create([
                    'cost_head_id' => $headData['cost_head_id'],
                    'cost_price' => isset($headData['cost_price']) && $headData['cost_price'] !== '' ? $headData['cost_price'] : 0,
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Cost Setting updated successfully.',
                    'redirect' => route('admin.cost-setting.index')
                ]);
            }

            return redirect()->route('admin.cost-setting.index')->with('success', 'Cost Setting updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to update Cost Setting: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Failed to update Cost Setting: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $costSetting = CostSetting::findOrFail($id);
        $costSetting->delete();

        return response()->json(['success' => 'Cost Setting deleted successfully.']);
    }

    /**
     * Update status via ajax switch.
     */
    public function getStatus(Request $request)
    {
        $costSetting = CostSetting::findOrFail($request->id);
        $costSetting->status = $request->status;
        $costSetting->save();

        return response()->json(['success' => 'Status changed successfully.']);
    }
}
