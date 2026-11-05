<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesAdminModule;
use App\Http\Controllers\Admin\Concerns\HandlesTrash;
use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Services\Deletion\SoftDeleteService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductAttributeController extends Controller
{
    use HandlesTrash, AuthorizesAdminModule;

    protected function trashModuleKey(): string
    {
        return 'product_attribute';
    }

    public function __construct()
    {
        $this->authorizeAdminModule('product_attribute', [
            'product_attribute.edit' => ['getStatus'],
        ]);
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $attributes = ProductAttribute::latest()->get();
            return DataTables::of($attributes)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $status = $row->status == 1 ? 'checked' : '';
                    return perm_html('product_attribute.edit', '<div class="custom-control custom-switch custom-switch-primary">
                                <input type="checkbox" class="custom-control-input changeStatus" data-id="' . $row->id . '" id="status_' . $row->id . '" ' . $status . '>
                                <label class="custom-control-label" for="status_' . $row->id . '">
                                    <span class="switch-icon-left"><i data-feather="check"></i></span>
                                    <span class="switch-icon-right"><i data-feather="x"></i></span>
                                </label>
                            </div>');
                })
                ->addColumn('action', function ($row) {
                    $btn = perm_html('product_attribute.edit', '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm editAttribute"><i data-feather="edit"></i></a>');
                    $btn .= perm_html('product_attribute.delete', ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteAttribute"><i data-feather="trash"></i></a>');
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.product_attribute.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:product_attributes,name',
        ]);

        ProductAttribute::create([
            'name' => $request->name,
            'status' => 1,
        ]);

        return response()->json(['success' => 'Product Attribute saved successfully.']);
    }

    public function edit($id)
    {
        $attribute = ProductAttribute::find($id);
        return response()->json($attribute);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:product_attributes,name,' . $id,
        ]);

        ProductAttribute::where('id', $id)->update([
            'name' => $request->name,
        ]);

        return response()->json(['success' => 'Product Attribute updated successfully.']);
    }

    public function destroy($id, SoftDeleteService $softDeleteService)
    {
        return $this->softDeleteRecord($id, $softDeleteService);
    }

    public function getStatus(Request $request)
    {
        $attribute = ProductAttribute::find($request->id);
        $attribute->status = $request->status;
        $attribute->save();
        return response()->json(['success' => 'Status changed successfully.']);
    }
}
