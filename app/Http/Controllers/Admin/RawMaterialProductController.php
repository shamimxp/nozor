<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RawMaterialProduct;
use App\Models\Unit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RawMaterialProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = RawMaterialProduct::with('unit')->latest();

            if ($request->name) {
                $query->where('name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->unit_id) {
                $query->where('unit_id', $request->unit_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('unit_name', function ($row) {
                    return $row->unit ? $row->unit->name : 'N/A';
                })
                ->addColumn('price', function ($row) {
                    return '৳' . $row->price_per_unit;
                })
                ->addColumn('grade_value', function ($row) {
                    return $row->grade_value ?? 0.00;
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch custom-switch-success">
                                <input type="checkbox" class="custom-control-input changeStatus" data-id="' . $row->id . '" id="status_' . $row->id . '" ' . $status . '>
                                <label class="custom-control-label" for="status_' . $row->id . '">
                                    <span class="switch-icon-left"><i data-feather="check"></i></span>
                                    <span class="switch-icon-right"><i data-feather="x"></i></span>
                                </label>
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm mr-1 editData"><i data-feather="edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteData"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        $units = Unit::where('status', 1)->get();
        return view('admin.raw_material_product.index', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:raw_material_products,name|max:255',
            'unit_id' => 'nullable|integer',
            'price_per_unit' => 'required|numeric',
            'grade_value' => 'required|numeric',
        ]);

        RawMaterialProduct::create([
            'name' => $request->name,
            'unit_id' => $request->unit_id,
            'price_per_unit' => $request->price_per_unit,
            'grade_value' => $request->grade_value,
            'status' => 1,
        ]);

        return response()->json(['success' => 'Raw Material Product added successfully.']);
    }

    public function edit($id)
    {
        $data = RawMaterialProduct::findOrFail($id);
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255|unique:raw_material_products,name,' . $id,
            'unit_id' => 'nullable|integer',
            'price_per_unit' => 'required|numeric',
            'grade_value' => 'required|numeric',
        ]);

        $data = RawMaterialProduct::findOrFail($id);
        $data->update([
            'name' => $request->name,
            'unit_id' => $request->unit_id,
            'price_per_unit' => $request->price_per_unit,
            'grade_value' => $request->grade_value,
        ]);

        return response()->json(['success' => 'Raw Material Product updated successfully.']);
    }

    public function destroy($id)
    {
        RawMaterialProduct::findOrFail($id)->delete();
        return response()->json(['success' => 'Raw Material Product deleted successfully.']);
    }

    public function getStatus(Request $request)
    {
        $data = RawMaterialProduct::findOrFail($request->id);
        $data->status = $request->status;
        $data->save();
        return response()->json(['success' => 'Status changed successfully.']);
    }
}
