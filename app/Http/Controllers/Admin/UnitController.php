<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $units = Unit::latest()->get();
            return DataTables::of($units)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $status = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch custom-switch-primary">
                                <input type="checkbox" class="custom-control-input changeStatus" data-id="' . $row->id . '" id="status_' . $row->id . '" ' . $status . '>
                                <label class="custom-control-label" for="status_' . $row->id . '">
                                    <span class="switch-icon-left"><i data-feather="check"></i></span>
                                    <span class="switch-icon-right"><i data-feather="x"></i></span>
                                </label>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm editUnit"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteUnit"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.unit.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:units,name',
        ]);

        Unit::create([
            'name' => $request->name,
            'status' => 1,
        ]);

        return response()->json(['success' => 'Unit saved successfully.']);
    }

    public function edit($id)
    {
        $unit = Unit::find($id);
        return response()->json($unit);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:units,name,' . $id,
        ]);

        Unit::where('id', $id)->update([
            'name' => $request->name,
        ]);

        return response()->json(['success' => 'Unit updated successfully.']);
    }

    public function destroy($id)
    {
        Unit::find($id)->delete();
        return response()->json(['success' => 'Unit deleted successfully.']);
    }

    public function getStatus(Request $request)
    {
        $unit = Unit::find($request->id);
        $unit->status = $request->status;
        $unit->save();
        return response()->json(['success' => 'Status changed successfully.']);
    }
}
