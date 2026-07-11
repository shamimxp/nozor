<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Services\ProductPriceCalculator;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $subCategories = SubCategory::all();
        return view('dealer.pos.index', compact('categories', 'subCategories'));
    }

    public function getProducts(Request $request, ProductPriceCalculator $calculator)
    {
        $query = Product::where('status', 1);

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('subcategory_id') && $request->subcategory_id != '') {
            $query->where('sub_category_id', $request->subcategory_id);
        }


        $products = $query->inRandomOrder()->get();
        $total = $query->count();
        
        $dealerId = auth('dealer')->id();
        
        // Process prices
        foreach ($products as $product) {
            if ($product->is_manufacturer) {
                try {
                    $calc = $calculator->calculate($product->id, $dealerId);
                    $product->pos_price = $calc['dealer_price'];
                    $product->pos_original_price = $calc['retail_price'];
                } catch (\Exception $e) {
                    $product->pos_price = $product->selling_price;
                    $product->pos_original_price = $product->discount_price > 0 ? $product->selling_price : null;
                    if($product->discount_price > 0) {
                        $product->pos_price = $product->discount_price;
                    }
                }
            } else {
                $product->pos_original_price = $product->discount_price > 0 ? $product->selling_price : null;
                $product->pos_price = $product->discount_price > 0 ? $product->discount_price : $product->selling_price;
            }
        }

        $html = view('dealer.pos.partials.product_grid', compact('products'))->render();

        return response()->json([
            'html' => $html,
        ]);
    }

    public function getSubCategories($id)
    {
        $subcategories = SubCategory::where('category_id', $id)->get();
        return response()->json($subcategories);
    }
}
