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
        $query = Product::select('id', 'name', 'category_id', 'sub_category_id', 'featured_image', 'selling_price', 'stock', 'discount_type', 'discount_amount', 'is_manufacturer', 'status')
            ->where('status', 1);

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('subcategory_id') && $request->subcategory_id != '') {
            $query->where('sub_category_id', $request->subcategory_id);
        }

        $totalCount = $query->count();
        
        $offset = (int) $request->input('offset', 0);
        $limit = (int) $request->input('limit', 36);

        $products = $query->orderBy('id', 'desc')
                          ->offset($offset)
                          ->limit($limit)
                          ->get();
                          
        $remainingCount = max(0, $totalCount - ($offset + $limit));
        $hasMore = $remainingCount > 0;
        
        $dealerId = auth('dealer')->id();
        
        // Process prices and stock
        foreach ($products as $product) {
            $product->is_out_of_stock = (!$product->is_manufacturer && $product->stock <= 0);
            
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
