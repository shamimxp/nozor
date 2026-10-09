<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Models\RawMaterialProduct;
use App\Services\ProductPriceCalculator;
use App\Services\ProductRecipeWriter;
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
            $query = Product::with(['category', 'subCategory'])->latest();

            if ($request->name) {
                $query->where('name', 'LIKE', '%' . $request->name . '%');
            }
            if ($request->category_id) {
                $query->where('category_id', $request->category_id);
            }
            if ($request->sub_category_id) {
                $query->where('sub_category_id', $request->sub_category_id);
            }
            if ($request->status != '') {
                $query->where('status', $request->status);
            }

            return $this->productDataTable($query);
        }
        $categories = Category::where('status', 1)->get();
        return view('admin.product.index', compact('categories'));
    }

    public function outOfStock(Request $request)
    {
        if ($request->ajax()) {
            $products = Product::with(['category', 'subCategory'])
                               ->where('stock', '<=', 0)
                               ->where('is_manufacturer', 0)
                               ->latest()
                               ->get();
            return $this->productDataTable($products);
        }
        return view('admin.product.out_of_stock');
    }

    private function productDataTable($products)
    {
        $calculator = app(ProductPriceCalculator::class);

        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('product', function ($row) {
                $url = $row->featured_image ? asset(config('imagepath.product') . $row->featured_image) : asset('images/no-image.png');
                $model = $row->model_no ? '<small class="text-muted d-block">Model: ' . e($row->model_no) . '</small>' : '';
                return '<div class="d-flex align-items-center">
                            <img src="' . $url . '" width="40" class="img-thumbnail mr-1" alt="">
                            <div>
                                <a href="'.route('admin.product.show', $row->id).'" class="text-body font-weight-bold">' . e($row->name) . '</a>
                                ' . $model . '
                            </div>
                        </div>';
            })
            ->addColumn('category', function ($row) {
                $cat = $row->category ? $row->category->name : 'N/A';
                $sub = $row->subCategory ? ' > ' . $row->subCategory->name : '';
                return $cat . $sub;
            })
            ->addColumn('price', function ($row) use ($calculator) {
                if ($row->is_manufacturer) {
                    try {
                        $price = $calculator->calculate($row->id)['retail_price'];
                    } catch (\Exception $e) {
                        $price = $row->selling_price;
                    }

                    return '৳' . number_format($price, 2);
                }

                $editBtn = ' <a href="javascript:void(0)" class="btn btn-primary p-0 px-25 editPriceStock" data-id="'.$row->id.'" data-type="price" data-value="'.$row->selling_price.'"><i data-feather="edit"></i></a>';
                return '৳' . $row->selling_price . $editBtn;
            })
            ->addColumn('stock', function ($row) {
                if ($row->is_manufacturer) {
                    return $row->stock;
                }

                $editBtn = ' <a href="javascript:void(0)" class="btn btn-primary p-0 px-25 editPriceStock" data-id="'.$row->id.'" data-type="stock" data-value="'.$row->stock.'"><i data-feather="edit"></i></a>';
                return $row->stock . $editBtn;
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
            ->addColumn('model_no', function ($row) {
                return $row->model_no ? e($row->model_no) : '—';
            })
            ->addColumn('dealer_show', function ($row) {
                return $this->visibilityToggle($row->id, 'dealer_show', (bool) $row->dealer_show);
            })
            ->addColumn('customer_show', function ($row) {
                return $this->visibilityToggle($row->id, 'customer_show', (bool) $row->customer_show);
            })
            ->addColumn('manufacturer', function ($row) {
                if($row->is_manufacturer == 1){
                    return '<span class="badge badge-info">Yes</span>';
                } else {
                    return '<span class="badge badge-secondary">No</span>';
                }
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
                $btn = '<a href="' . route('admin.product.show', $row->id) . '" class="btn btn-info btn-sm mr-1" title="View"><i data-feather="eye"></i></a>';
                $btn .= '<a href="' . route('admin.product.edit', $row->id) . '" class="btn btn-primary btn-sm mr-1" title="Edit"><i data-feather="edit"></i></a>';
                $btn .= '<a href="' . route('admin.product.duplicate', $row->id) . '" class="btn btn-warning btn-sm mr-1" title="Duplicate"><i data-feather="copy"></i></a>';
                $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteProduct" title="Delete"><i data-feather="trash"></i></a>';
                return $btn;
            })
            ->rawColumns(['product', 'price', 'stock', 'featured', 'manufacturer', 'dealer_show', 'customer_show', 'status', 'action'])
            ->make(true);
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();
        $attributes = ProductAttribute::where('status', 1)->get();
        $variations = \App\Models\Variation::with('variationValues')->get();
        $units = \App\Models\Unit::where('status', 1)->get();
        $raw_material = RawMaterialProduct::with(['materialType', 'unit'])->get();
        return view('admin.product.create', compact('categories', 'attributes', 'variations', 'units', 'raw_material'));
    }

    public function store(Request $request, ProductRecipeWriter $writer)
    {
        $request->validate($this->productRules($request));

        $product = new Product();
        $product->name = $request->name;
        $product->model_no = $request->model_no;
        $product->slug = Str::slug($request->name);
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->unit_id = $request->unit_id;
        $product->short_description = $request->short_description;
        $product->max_order_qty = $request->max_order_qty ?? 0;
        $product->is_featured = $request->is_featured ? 1 : 0;
        $product->is_manufacturer = $request->is_manufacturer ? 1 : 0;
        $product->dealer_show = $request->boolean('dealer_show') ? 1 : 0;
        $product->customer_show = $request->boolean('customer_show') ? 1 : 0;
        $product->status = $request->status ?? 1;
        $product->selling_price = $request->filled('selling_price') ? $request->selling_price : null;
        $product->cost_price = $request->cost_price ?? 0;
        $product->stock = $request->stock ?? 0;
        $product->discount_type = $request->discount_type;
        $product->discount_amount = $request->discount_amount ?? 0;

        if ($request->hasFile('featured_image')) {
            $filename = $this->uploadOne($request->featured_image, 600, 600, config('imagepath.product'));
            $product->featured_image = $filename;
        }

        $product->save();

        if ($request->boolean('is_manufacturer')) {
            $writer->sync($product, $request);
        }

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

        // Variations
        if ($request->variation_values) {
            foreach ($request->variation_values as $variation_id => $values) {
                foreach ($values as $value_id) {
                    \App\Models\ProductVariation::create([
                        'product_id' => $product->id,
                        'variation_id' => $variation_id,
                        'variation_value_id' => $value_id,
                    ]);
                }
            }
        }

        return response()->json(['success' => 'Product saved successfully.']);
    }

    public function edit($id)
    {
        $product = Product::with(['gallery', 'attributes.attribute', 'variations', 'recipe.items.rawMaterialProduct.materialType', 'recipe.items.rawMaterialProduct.unit'])->findOrFail($id);
        $categories = Category::where('status', 1)->get();
        $subcategories = SubCategory::where('category_id', $product->category_id)->get();
        $attributes = ProductAttribute::where('status', 1)->get();
        $variations = \App\Models\Variation::with('variationValues')->get();
        $units = \App\Models\Unit::where('status', 1)->get();
        $raw_material = RawMaterialProduct::with(['materialType', 'unit'])->get();
        return view('admin.product.edit', compact('product', 'categories', 'subcategories', 'attributes', 'variations', 'units', 'raw_material'));
    }

    public function duplicate($id)
    {
        $product = Product::with(['gallery', 'attributes.attribute', 'variations', 'recipe.items.rawMaterialProduct.materialType', 'recipe.items.rawMaterialProduct.unit'])->findOrFail($id);
        $categories = Category::where('status', 1)->get();
        $subcategories = SubCategory::where('category_id', $product->category_id)->get();
        $attributes = ProductAttribute::where('status', 1)->get();
        $variations = \App\Models\Variation::with('variationValues')->get();
        $units = \App\Models\Unit::where('status', 1)->get();
        $raw_material = RawMaterialProduct::with(['materialType', 'unit'])->get();

        return view('admin.product.duplicate', compact('product', 'categories', 'subcategories', 'attributes', 'variations', 'units', 'raw_material'));
    }

    public function duplicateStore(Request $request, $id, ProductRecipeWriter $writer)
    {
        $source = Product::with(['gallery', 'recipe'])->findOrFail($id);
        $request->validate($this->productRules($request));

        $name = $this->uniqueProductName(trim($request->name));

        $product = new Product();
        $product->name = $name;
        $product->model_no = $request->model_no;
        $product->slug = $this->uniqueProductSlug($name);
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->unit_id = $request->unit_id;
        $product->short_description = $request->short_description;
        $product->max_order_qty = $request->max_order_qty ?? 0;
        $product->is_featured = $request->is_featured ? 1 : 0;
        $product->is_manufacturer = $request->is_manufacturer ? 1 : 0;
        $product->dealer_show = $request->boolean('dealer_show') ? 1 : 0;
        $product->customer_show = $request->boolean('customer_show') ? 1 : 0;
        $product->status = $request->status ?? 1;
        $product->selling_price = $request->filled('selling_price') ? $request->selling_price : null;
        $product->cost_price = $request->cost_price ?? 0;
        $product->stock = $request->stock ?? 0;
        $product->discount_type = $request->discount_type;
        $product->discount_amount = $request->discount_amount ?? 0;

        if ($request->hasFile('featured_image')) {
            $product->featured_image = $this->uploadOne($request->featured_image, 600, 600, config('imagepath.product'));
        } elseif ($source->featured_image) {
            $copied = $this->copyPublicFile(
                trim(config('imagepath.product'), '/') . '/' . ltrim($source->featured_image, '/'),
                config('imagepath.product')
            );
            $product->featured_image = $copied ? '/' . $copied : null;
        }

        $product->save();

        if ($request->boolean('is_manufacturer')) {
            $copiedImages = [];
            foreach ((array) $request->input('existing_images', []) as $path) {
                $copied = $this->copyPublicFile($path, 'uploads/product_recipes');
                if ($copied) {
                    $copiedImages[] = 'uploads/product_recipes/' . $copied;
                }
            }
            $request->merge(['existing_images' => $copiedImages]);
            $writer->sync($product, $request);
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $this->uploadOne($image, 600, 600, config('imagepath.product')),
                ]);
            }
        }

        foreach ($source->gallery as $image) {
            $copied = $this->copyPublicFile(
                trim(config('imagepath.product'), '/') . '/' . ltrim($image->image, '/'),
                config('imagepath.product')
            );
            if ($copied) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => '/' . $copied,
                ]);
            }
        }

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

        if ($request->variation_values) {
            foreach ($request->variation_values as $variation_id => $values) {
                foreach ($values as $value_id) {
                    \App\Models\ProductVariation::create([
                        'product_id' => $product->id,
                        'variation_id' => $variation_id,
                        'variation_value_id' => $value_id,
                    ]);
                }
            }
        }

        return response()->json(['success' => 'Product duplicated successfully.']);
    }

    private function uniqueProductName(string $name): string
    {
        if (! Product::where('name', $name)->exists()) {
            return $name;
        }

        $candidate = $name . ' duplicate';
        $i = 2;
        while (Product::where('name', $candidate)->exists()) {
            $candidate = $name . ' duplicate ' . $i;
            $i++;
        }

        return $candidate;
    }

    private function uniqueProductSlug(string $name): string
    {
        $slug = Str::slug($name) ?: 'product';
        $base = $slug;
        $i = 2;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function copyPublicFile(?string $relativePath, string $destinationDir): ?string
    {
        if (! $relativePath) {
            return null;
        }

        $source = public_path(ltrim($relativePath, '/'));
        if (! is_file($source)) {
            return null;
        }

        $extension = pathinfo($source, PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . ($extension ? '.' . $extension : '');
        $destinationDir = trim($destinationDir, '/');
        $destination = public_path($destinationDir . '/' . $filename);

        if (! is_dir(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }

        copy($source, $destination);

        return $filename;
    }

    public function show($id, ProductPriceCalculator $calculator)
    {
        $product = Product::with([
            'category',
            'subCategory',
            'unit',
            'gallery',
            'attributes.attribute',
            'variations.variation',
            'variations.variationValue',
            'recipe.items.rawMaterialProduct.materialType',
            'recipe.items.rawMaterialProduct.unit',
        ])->findOrFail($id);

        $pricing = $this->productPricing($product, $calculator);

        return view('admin.product.show', compact('product', 'pricing'));
    }

    public function update(Request $request, $id, ProductRecipeWriter $writer)
    {
        $request->validate($this->productRules($request));

        $product = Product::findOrFail($id);
        $product->name = $request->name;
        $product->model_no = $request->model_no;
        $product->slug = Str::slug($request->name);
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->unit_id = $request->unit_id;
        $product->short_description = $request->short_description;
        $product->max_order_qty = $request->max_order_qty ?? 0;
        $product->is_featured = $request->is_featured ? 1 : 0;
        $product->is_manufacturer = $request->is_manufacturer ? 1 : 0;
        $product->dealer_show = $request->boolean('dealer_show') ? 1 : 0;
        $product->customer_show = $request->boolean('customer_show') ? 1 : 0;
        $product->status = $request->status ?? 1;
        $product->selling_price = $request->filled('selling_price') ? $request->selling_price : null;
        $product->cost_price = $request->cost_price ?? 0;
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

        if ($request->boolean('is_manufacturer')) {
            $writer->sync($product, $request);
        }

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

        // Update Variations
        \App\Models\ProductVariation::where('product_id', $product->id)->delete();
        if ($request->variation_values) {
            foreach ($request->variation_values as $variation_id => $values) {
                foreach ($values as $value_id) {
                    \App\Models\ProductVariation::create([
                        'product_id' => $product->id,
                        'variation_id' => $variation_id,
                        'variation_value_id' => $value_id,
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

    public function updateVisibility(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'field' => 'required|in:dealer_show,customer_show',
            'value' => 'required|in:0,1',
        ]);

        $product = Product::findOrFail($request->id);
        $product->{$request->field} = (int) $request->value;
        $product->save();

        $label = $request->field === 'dealer_show' ? 'Dealer show' : 'Customer show';

        return response()->json(['success' => $label . ' updated successfully.']);
    }

    private function visibilityToggle($id, string $field, bool $on): string
    {
        $class = $on ? 'badge-success' : 'badge-danger';
        $label = $on ? 'Active' : 'Disable';

        return '<button type="button" class="badge ' . $class . ' border-0 toggleVisibility" data-id="' . $id . '" data-field="' . $field . '" data-value="' . ($on ? 1 : 0) . '" style="cursor:pointer;">' . $label . '</button>';
    }

    public function getManufacturerStatus(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->is_manufacturer = $request->status;
        $product->save();
        return response()->json(['success' => 'Manufacturer status changed successfully.']);
    }

    public function getSubCategory($category_id)
    {
        $subcategories = SubCategory::where('category_id', $category_id)->where('status', 1)->get();
        return response()->json($subcategories);
    }

    public function updatePriceStock(Request $request)
    {
        $product = Product::findOrFail($request->id);
        if ($product->is_manufacturer) {
            return response()->json(['error' => 'Manufacture product price and quantity cannot be updated from the product list.'], 422);
        }
        if($request->type == 'stock'){
            $product->stock = $request->value;
        }else{
            $product->selling_price = $request->value;
        }
        $product->save();
        return response()->json(['success' => 'Product updated successfully.']);
    }

    private function productPricing(Product $product, ProductPriceCalculator $calculator): array
    {
        if ($product->is_manufacturer) {
            if (!$product->recipe) {
                return ['type' => 'manufacture', 'available' => false];
            }

            $quote = $calculator->quote($product->id);

            return [
                'type' => 'manufacture',
                'available' => true,
                'cost_price' => $quote['total_cost_price'],
                'regular_dealer_percent' => $quote['regular_dealer_percent'],
                'regular_dealer_price' => $quote['regular_dealer_price'],
                'special_dealer_percent' => $quote['special_dealer_percent'],
                'special_dealer_price' => $quote['special_dealer_price'],
                'regular_price' => $quote['regular_price'],
                'discount_price' => $this->discountedPrice($quote['regular_price'], $product),
            ];
        }

        $wholesale = $product->selling_price;

        return [
            'type' => 'regular',
            'cost_price' => $product->cost_price,
            'wholesale_price' => $wholesale,
            'discount_price' => $this->discountedPrice($wholesale, $product),
        ];
    }

    private function discountedPrice($base, Product $product): ?float
    {
        if (!$product->discount_type || $product->discount_amount === null || (float) $product->discount_amount <= 0 || $base === null) {
            return null;
        }

        $base = (float) $base;
        if ($product->discount_type === 'percent') {
            return round(max(0, $base - ($base * ((float) $product->discount_amount / 100))), 2);
        }

        if ($product->discount_type === 'amount') {
            return round(max(0, $base - (float) $product->discount_amount), 2);
        }

        return null;
    }

    private function productRules(Request $request): array
    {
        $isManufacturer = $request->boolean('is_manufacturer');

        return [
            'name' => 'required|max:255',
            'model_no' => 'nullable|string|max:100',
            'category_id' => 'required',
            'selling_price' => [$isManufacturer ? 'nullable' : 'required', 'numeric'],
            'cost_price' => 'required|numeric',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'gallery.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'raw_material_id' => 'nullable|array',
            'raw_material_id.*' => 'exists:raw_material_products,id',
            'body_charge' => 'nullable|numeric|min:0',
            'finishing_charge' => 'nullable|numeric|min:0',
            'bearing' => 'nullable|numeric|min:0',
            'stone' => 'nullable|numeric|min:0',
            'electric_bill' => 'nullable|numeric|min:0',
            'gas_bill' => 'nullable|numeric|min:0',
            'box_price' => 'nullable|numeric|min:0',
            'carrying_charge' => 'nullable|numeric|min:0',
            'wire_price' => 'nullable|numeric|min:0',
            'manufacture_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ];
    }
}
