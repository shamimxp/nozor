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
        $query = Product::with(['recipe.costSetting'])
            ->select('id', 'name', 'category_id', 'sub_category_id', 'featured_image', 'selling_price', 'stock', 'discount_type', 'discount_amount', 'is_manufacturer', 'status')
            ->where('status', 1)
            ->forDealer();

        // Pre-filter: non-manufacturer products must have positive price, manufacturer products must have a recipe
        $query->where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('is_manufacturer', 0)
                    ->whereNotNull('selling_price')
                    ->where('selling_price', '>', 0);
            })->orWhere(function ($sub) {
                $sub->where('is_manufacturer', 1)
                    ->whereHas('recipe');
            });
        });

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('subcategory_id') && $request->subcategory_id != '') {
            $query->where('sub_category_id', $request->subcategory_id);
        }

        $dealerId = auth('dealer')->id();

        // Process prices and filter out products with price <= 0 or not set
        $allProducts = $query->orderBy('id', 'desc')->get()->map(function ($product) use ($calculator, $dealerId) {
            $product->is_out_of_stock = (!$product->is_manufacturer && $product->stock <= 0);

            $posPrice = 0;
            $posOriginalPrice = null;

            if ($product->is_manufacturer) {
                try {
                    $calc = $calculator->calculate($product->id, $dealerId);
                    $posPrice = (float) ($calc['dealer_price'] ?? 0);
                    $posOriginalPrice = (float) ($calc['retail_price'] ?? 0);
                } catch (\Exception $e) {
                    $posPrice = (float) ($product->selling_price ?? 0);
                    $posOriginalPrice = $product->discount_price > 0 ? (float) $product->selling_price : null;
                    if ($product->discount_price > 0) {
                        $posPrice = (float) $product->discount_price;
                    }
                }
            } else {
                $posOriginalPrice = $product->discount_price > 0 ? (float) $product->selling_price : null;
                $posPrice = (float) ($product->discount_price > 0 ? $product->discount_price : ($product->selling_price ?? 0));
            }

            $product->pos_price = $posPrice;
            $product->pos_original_price = $posOriginalPrice;
            return $product;
        })->filter(function ($product) {
            return (float) $product->pos_price > 0;
        })->values();

        $totalCount = $allProducts->count();
        $offset = (int) $request->input('offset', 0);
        $limit = (int) $request->input('limit', 35);

        $products = $allProducts->slice($offset, $limit);
        $remainingCount = max(0, $totalCount - ($offset + $limit));
        $hasMore = $remainingCount > 0;

        $html = view('dealer.pos.partials.product_grid', compact('products'))->render();

        return response()->json([
            'html' => $html,
            'total_count' => $totalCount,
            'remaining_count' => $remainingCount,
            'has_more' => $hasMore,
        ]);
    }

    public function getSubCategories($id)
    {
        $subcategories = SubCategory::where('category_id', $id)->get();
        return response()->json($subcategories);
    }

    public function submitOrderRequest(Request $request, \App\Services\OrderRequestService $orderRequestService)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.requested_qty' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        try {
            $dealerId = auth('dealer')->id();
            $orderRequest = $orderRequestService->createOrderRequest($dealerId, $request->items, $request->note);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Order request submitted successfully.',
                'order_request_id' => $orderRequest->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
