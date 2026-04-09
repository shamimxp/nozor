<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::latest()->get();
            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('image', function ($row) {
                    $url = $row->image ? asset(config('imagepath.category') . $row->image) : asset('images/no-image.png');
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
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm editCategory"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteCategory"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }
        return view('admin.category.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->status = 1;

        if ($request->hasFile('image')) {
            $filename = $category->uploadOne($request->image, 300, 300, config('imagepath.category'));
            $category->image = $filename;
        }

        $category->save();

        return response()->json(['success' => 'Category saved successfully.']);
    }

    public function edit($id)
    {
        $category = Category::find($id);
        if ($category->image) {
            $category->image_url = asset(config('imagepath.category') . $category->image);
        } else {
            $category->image_url = asset('images/no-image.png');
        }
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $category = Category::find($id);
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            if ($category->image) {
                $category->deleteOne(config('imagepath.category'), $category->image);
            }
            $filename = $category->uploadOne($request->image, 300, 300, config('imagepath.category'));
            $category->image = $filename;
        }

        $category->save();

        return response()->json(['success' => 'Category updated successfully.']);
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if ($category->image) {
            $category->deleteOne(config('imagepath.category'), $category->image);
        }
        $category->delete();
        return response()->json(['success' => 'Category deleted successfully.']);
    }

    public function getStatus(Request $request)
    {
        $category = Category::find($request->id);
        $category->status = $request->status;
        $category->save();
        return response()->json(['success' => 'Status changed successfully.']);
    }
}
