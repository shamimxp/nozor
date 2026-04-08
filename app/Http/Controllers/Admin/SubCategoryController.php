<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $subCategories = SubCategory::with('category')->latest()->get();
            return DataTables::of($subCategories)
                ->addIndexColumn()
                ->addColumn('category', function ($row) {
                    return $row->category->name ?? 'N/A';
                })
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
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm editSubCategory"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteSubCategory"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        $categories = Category::where('status', 1)->get();
        return view('admin.sub_category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
        ]);

        SubCategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'status' => 1,
        ]);

        return response()->json(['success' => 'Sub Category saved successfully.']);
    }

    public function edit($id)
    {
        $subCategory = SubCategory::find($id);
        return response()->json($subCategory);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
        ]);

        SubCategory::where('id', $id)->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json(['success' => 'Sub Category updated successfully.']);
    }

    public function destroy($id)
    {
        SubCategory::find($id)->delete();
        return response()->json(['success' => 'Sub Category deleted successfully.']);
    }

    public function getStatus(Request $request)
    {
        $subCategory = SubCategory::find($request->id);
        $subCategory->status = $request->status;
        $subCategory->save();
        return response()->json(['success' => 'Status changed successfully.']);
    }
}
