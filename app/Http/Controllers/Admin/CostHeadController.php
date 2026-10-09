<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CostHead;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CostHeadController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = CostHead::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
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
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" data-title="' . e($row->title) . '" data-status="' . $row->status . '" class="btn btn-primary btn-sm p-0 px-25 editBtn" title="Edit"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm p-0 px-25 deleteBtn" title="Delete"><i data-feather="trash-2"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.cost-head.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:cost_heads,title',
            'status' => 'nullable|in:0,1',
        ]);

        CostHead::create([
            'title' => $request->title,
            'status' => $request->filled('status') ? (int) $request->status : 1,
        ]);

        return response()->json(['success' => 'Cost Head saved successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255|unique:cost_heads,title,' . $id,
            'status' => 'nullable|in:0,1',
        ]);

        $costHead = CostHead::findOrFail($id);
        $costHead->update([
            'title' => $request->title,
            'status' => $request->has('status') ? (int) $request->status : $costHead->status,
        ]);

        return response()->json(['success' => 'Cost Head updated successfully.']);
    }

    public function destroy($id)
    {
        $costHead = CostHead::findOrFail($id);
        $costHead->delete();

        return response()->json(['success' => 'Cost Head deleted successfully.']);
    }

    public function getStatus(Request $request)
    {
        $costHead = CostHead::findOrFail($request->id);
        $costHead->status = $request->status;
        $costHead->save();

        return response()->json(['success' => 'Status changed successfully.']);
    }
}
