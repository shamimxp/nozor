<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Traits\UploadAble;

class ProductController extends Controller
{
    use UploadAble;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category', 'subCategory'])->latest()->get();
            return DataTables::of($products)
                ->addIndexColumn()
                ->addColumn('product', function ($row) {
                    $url = $row->featured_image ? asset(config('imagepath.product') . $row->featured_image) : asset('images/no-image.png');
                    return '<div class="d-flex align-items-center">
                                <img src="' . $url . '" width="40" class="img-thumbnail mr-1" alt="">
                                <a href="'.route('admin.product.show', $row->id).'" class="text-body font-weight-bold">' . $row->name . '</a>
                            </div>';
                })
                ->addColumn('category', function ($row) {
                    $cat = $row->category ? $row->category->name : 'N/A';
                    $sub = $row->subCategory ? ' > ' . $row->subCategory->name : '';
                    return $cat . $sub;
                })
                ->addColumn('price', function ($row) {
                    return '৳' . $row->selling_price;
                })
                ->addColumn('featured', function ($row) {
                    $featured = $row->is_featured == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch custom-switch-success">
                                <input type="checkbox" class="custom-control-input changeFeatured" data-id="' . $row->id . '" id="featured_' . $row->id . '" ' . $featured . '>
                                <label class="custom-control-label" for="featured_' . $row->id . '">
                                    <span class="switch-icon-left"><i data-feather="check"></i></span>
                                    <span class="switch-icon-right"><i data-feather="x"></i></span>
                                </label>
                            </div>';
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
                    $btn = '<a href="' . route('admin.product.show', $row->id) . '" class="btn btn-info btn-sm mr-1"><i data-feather="eye"></i></a>';
                    $btn .= '<a href="' . route('admin.product.edit', $row->id) . '" class="btn btn-primary btn-sm mr-1"><i data-feather="edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteProduct"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['product', 'featured', 'status', 'action'])
                ->make(true);
        }
        return view('admin.product.index');
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $attributes = ProductAttribute::where('status', 1)->get();
        $units = \App\Models\Unit::where('status', 1)->get();
        return view('admin.product.create', compact('categories', 'attributes', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required',
            'selling_price' => 'required|numeric',
            'cost_price' => 'required|numeric',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->unit_id = $request->unit_id;
        $product->short_description = $request->short_description;
        $product->max_order_qty = $request->max_order_qty ?? 0;
        $product->is_featured = $request->is_featured ? 1 : 0;
        $product->status = $request->status ?? 1;
        $product->selling_price = $request->selling_price;
        $product->cost_price = $request->cost_price;
        $product->stock = $request->stock ?? 0;
        $product->discount_type = $request->discount_type;
        $product->discount_amount = $request->discount_amount ?? 0;

        if ($request->hasFile('featured_image')) {
            $filename = $this->uploadOne($request->featured_image, 600, 600, config('imagepath.product'));
            $product->featured_image = $filename;
        }

        $product->save();

        // Multi images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $filename = $this->uploadOne($image, 600, 600, config('imagepath.product'));
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $filename,
                ]);
            }
        }

        // Attributes
        if ($request->attributes_id) {
            foreach ($request->attributes_id as $key => $attr_id) {
                if ($request->attribute_values[$key]) {
                    ProductAttributeValue::create([
                        'product_id' => $product->id,
                        'product_attribute_id' => $attr_id,
                        'attribute_value' => $request->attribute_values[$key],
                    ]);
                }
            }
        }

        return response()->json(['success' => 'Product saved successfully.']);
    }

    public function edit($id)
    {
        $product = Product::with(['gallery', 'attributes.attribute'])->findOrFail($id);
        $categories = Category::where('status', 1)->get();
        $subcategories = SubCategory::where('category_id', $product->category_id)->get();
        $attributes = ProductAttribute::where('status', 1)->get();
        $units = \App\Models\Unit::where('status', 1)->get();
        return view('admin.product.edit', compact('product', 'categories', 'subcategories', 'attributes', 'units'));
    }

    public function show($id)
    {
        $product = Product::with(['category', 'subCategory', 'unit', 'gallery', 'attributes.attribute'])->findOrFail($id);
        return view('admin.product.show', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required',
            'selling_price' => 'required|numeric',
            'cost_price' => 'required|numeric',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->unit_id = $request->unit_id;
        $product->short_description = $request->short_description;
        $product->max_order_qty = $request->max_order_qty ?? 0;
        $product->is_featured = $request->is_featured ? 1 : 0;
        $product->status = $request->status ?? 1;
        $product->selling_price = $request->selling_price;
        $product->cost_price = $request->cost_price;
        $product->stock = $request->stock ?? 0;
        $product->discount_type = $request->discount_type;
        $product->discount_amount = $request->discount_amount ?? 0;

        if ($request->hasFile('featured_image')) {
            if ($product->featured_image) {
                $this->deleteOne(config('imagepath.product'), $product->featured_image);
            }
            $filename = $this->uploadOne($request->featured_image, 600, 600, config('imagepath.product'));
            $product->featured_image = $filename;
        }

        $product->save();

        // Multi images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $filename = $this->uploadOne($image, 600, 600, config('imagepath.product'));
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $filename,
                ]);
            }
        }

        // Update Attributes
        ProductAttributeValue::where('product_id', $product->id)->delete();
        if ($request->attributes_id) {
            foreach ($request->attributes_id as $key => $attr_id) {
                if (isset($request->attribute_values[$key]) && $request->attribute_values[$key]) {
                    ProductAttributeValue::create([
                        'product_id' => $product->id,
                        'product_attribute_id' => $attr_id,
                        'attribute_value' => $request->attribute_values[$key],
                    ]);
                }
            }
        }

        return response()->json(['success' => 'Product updated successfully.']);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->featured_image) {
            $this->deleteOne(config('imagepath.product'), $product->featured_image);
        }
        foreach ($product->gallery as $img) {
            $this->deleteOne(config('imagepath.product'), $img->image);
            $img->delete();
        }
        $product->delete();
        return response()->json(['success' => 'Product deleted successfully.']);
    }

    public function getStatus(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->status = $request->status;
        $product->save();
        return response()->json(['success' => 'Status changed successfully.']);
    }

    public function getFeaturedStatus(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->is_featured = $request->status;
        $product->save();
        return response()->json(['success' => 'Featured status changed successfully.']);
    }

    public function getSubCategory($category_id)
    {
        $subcategories = SubCategory::where('category_id', $category_id)->where('status', 1)->get();
        return response()->json($subcategories);
    }
}
