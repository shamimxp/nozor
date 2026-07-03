<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaterialType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MaterialTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MaterialType::latest()->get();
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
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" data-name="' . $row->name . '" class="btn btn-primary p-0 px-25 editBtn" title="Edit"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger p-0 px-25 deleteBtn" title="Delete"><i data-feather="trash-2"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.material_type.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:material_types,name',
        ]);

        MaterialType::create([
            'name' => $request->name,
            'status' => 1,
        ]);

        return response()->json(['success' => 'Material Type saved successfully.']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:material_types,name,' . $id,
        ]);

        $materialType = MaterialType::findOrFail($id);
        $materialType->update([
            'name' => $request->name,
        ]);

        return response()->json(['success' => 'Material Type updated successfully.']);
    }

    public function destroy($id)
    {
        MaterialType::find($id)->delete();
        return response()->json(['success' => 'Material Type deleted successfully.']);
    }

    public function getStatus(Request $request)
    {
        $materialType = MaterialType::find($request->id);
        $materialType->status = $request->status;
        $materialType->save();
        return response()->json(['success' => 'Status changed successfully.']);
    }
}
