<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesAdminModule;
use App\Http\Controllers\Admin\Concerns\HandlesTrash;
use App\Http\Controllers\Controller;
use App\Models\RawMaterialProduct;
use App\Models\Unit;
use App\Services\Deletion\SoftDeleteService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RawMaterialProductController extends Controller
{
    use HandlesTrash, AuthorizesAdminModule;

    protected function trashModuleKey(): string
    {
        return 'raw_material_product';
    }

    public function __construct()
    {
        $this->authorizeAdminModule('raw_material_product', [
            'raw_material_product.edit' => ['getStatus'],
        ]);
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = RawMaterialProduct::with(['unit', 'materialType'])->latest();

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
                ->addColumn('material_type', function ($row) {
                    return $row->materialType ? $row->materialType->name : 'N/A';
                })
                ->addColumn('cost_price', function ($row) {
                    return '৳' . number_format($row->cost_price ?? 0, 2);
                })
                ->addColumn('price', function ($row) {
                    return '৳' . ($row->price_per_unit ?? 0);
                })
                ->addColumn('grade_value', function ($row) {
                    return $row->grade_value ?? 0.00;
                })
                ->addColumn('status', function ($row) {
                    $status = $row->status == 1 ? 'checked' : '';
                    return perm_html('raw_material_product.edit', '<div class="custom-control custom-switch custom-switch-success">
                                <input type="checkbox" class="custom-control-input changeStatus" data-id="' . $row->id . '" id="status_' . $row->id . '" ' . $status . '>
                                <label class="custom-control-label" for="status_' . $row->id . '">
                                    <span class="switch-icon-left"><i data-feather="check"></i></span>
                                    <span class="switch-icon-right"><i data-feather="x"></i></span>
                                </label>
                            </div>');
                })
                ->addColumn('action', function ($row) {
                    $btn = perm_html('raw_material_product.edit', '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm mr-1 editData"><i data-feather="edit"></i></a>');
                    $btn .= perm_html('raw_material_product.delete', '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteData"><i data-feather="trash"></i></a>');
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        $units = Unit::where('status', 1)->get();
        $material_types = \App\Models\MaterialType::where('status', 1)->get();
        $default_gm_unit = Unit::where('name', 'gm')->first();
        return view('admin.raw_material_product.index', compact('units', 'material_types', 'default_gm_unit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:raw_material_products,name|max:255',
            'cost_price' => 'required|numeric|min:0',
            'unit_id' => 'nullable|integer',
            'material_type_id' => 'nullable|integer',
            'price_per_unit' => 'required|numeric|min:0',
            'grade_value' => 'required|numeric',
        ]);

        $gmUnit = Unit::where('name', 'gm')->first();
        $unitId = $request->unit_id ?: ($gmUnit ? $gmUnit->id : null);
        $costPrice = (float) $request->cost_price;
        $pricePerUnit = (float) $request->price_per_unit;
        if ($pricePerUnit == 0 && $costPrice > 0) {
            $pricePerUnit = round($costPrice / 1000, 4);
        }

        RawMaterialProduct::create([
            'name' => $request->name,
            'unit_id' => $unitId,
            'material_type_id' => $request->material_type_id,
            'cost_price' => $costPrice,
            'price_per_unit' => $pricePerUnit,
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
            'cost_price' => 'required|numeric|min:0',
            'unit_id' => 'nullable|integer',
            'material_type_id' => 'nullable|integer',
            'price_per_unit' => 'required|numeric|min:0',
            'grade_value' => 'required|numeric',
        ]);

        $gmUnit = Unit::where('name', 'gm')->first();
        $unitId = $request->unit_id ?: ($gmUnit ? $gmUnit->id : null);
        $costPrice = (float) $request->cost_price;
        $pricePerUnit = (float) $request->price_per_unit;
        if ($pricePerUnit == 0 && $costPrice > 0) {
            $pricePerUnit = round($costPrice / 1000, 4);
        }

        $data = RawMaterialProduct::findOrFail($id);
        $data->update([
            'name' => $request->name,
            'unit_id' => $unitId,
            'material_type_id' => $request->material_type_id,
            'cost_price' => $costPrice,
            'price_per_unit' => $pricePerUnit,
            'grade_value' => $request->grade_value,
        ]);

        return response()->json(['success' => 'Raw Material Product updated successfully.']);
    }

    public function destroy($id, SoftDeleteService $softDeleteService)
    {
        return $this->softDeleteRecord($id, $softDeleteService);
    }

    public function getStatus(Request $request)
    {
        $data = RawMaterialProduct::findOrFail($request->id);
        $data->status = $request->status;
        $data->save();
        return response()->json(['success' => 'Status changed successfully.']);
    }
}
