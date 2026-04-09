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
                ->addColumn('image', function ($row) {
                    $url = $row->image ? asset(config('imagepath.sub_category') . $row->image) : asset('images/no-image.png');
                    return '<img src="' . $url . '" width="50" class="img-thumbnail" alt="">';
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
                ->rawColumns(['image', 'status', 'action'])
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $subCategory = new SubCategory();
        $subCategory->category_id = $request->category_id;
        $subCategory->name = $request->name;
        $subCategory->slug = Str::slug($request->name);
        $subCategory->status = 1;

        if ($request->hasFile('image')) {
            $filename = $subCategory->uploadOne($request->image, 300, 300, config('imagepath.sub_category'));
            $subCategory->image = $filename;
        }

        $subCategory->save();

        return response()->json(['success' => 'Sub Category saved successfully.']);
    }

    public function edit($id)
    {
        $subCategory = SubCategory::find($id);
        if ($subCategory->image) {
            $subCategory->image_url = asset(config('imagepath.sub_category') . $subCategory->image);
        } else {
            $subCategory->image_url = asset('images/no-image.png');
        }
        return response()->json($subCategory);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $subCategory = SubCategory::find($id);
        $subCategory->category_id = $request->category_id;
        $subCategory->name = $request->name;
        $subCategory->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            if ($subCategory->image) {
                $subCategory->deleteOne(config('imagepath.sub_category'), $subCategory->image);
            }
            $filename = $subCategory->uploadOne($request->image, 300, 300, config('imagepath.sub_category'));
            $subCategory->image = $filename;
        }

        $subCategory->save();

        return response()->json(['success' => 'Sub Category updated successfully.']);
    }

    public function destroy($id)
    {
        $subCategory = SubCategory::find($id);
        if ($subCategory->image) {
            $subCategory->deleteOne(config('imagepath.sub_category'), $subCategory->image);
        }
        $subCategory->delete();
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
