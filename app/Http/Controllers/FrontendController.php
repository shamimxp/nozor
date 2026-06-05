<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\WebSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Sabberworm\CSS\Settings;

class FrontendController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 1)
            ->withCount(['products' => function ($query) {
                $query->where('status', 1);
            }])
            ->get();

        $banners = Banner::where('status',1)->get();
        $products = Product::with('gallery')->where('status',1)->get();
        $settings = WebSetting::first();
        return view('frontend.index',compact('categories','banners','products','settings'));
    }
    public function wishlist(){
        return view('frontend.page.wishlist');
    }
    public function cart(){
     return view('frontend.page.cart');
    }
    public function checkout(){
        return view('frontend.page.checkout');
    }
    public function contact(){
        return view('frontend.page.contact');
    }
    public function about(){
        return view('frontend.page.about');
    }
    public function shop(){
        return view('frontend.page.shop');
    }
    public function deal(){
        return view('frontend.page.dealpage');
    }
    public function details($id){
        $product = Product::with('gallery','category', 'variations.variationValue', 'variations.variation')->findOrFail(decrypt($id));
        
        $relatedProducts = Product::where('status', 1)
            ->where('category_id', $product->category_id);
            
        if ($product->sub_category_id) {
            $relatedProducts->where('sub_category_id', $product->sub_category_id);
        }
        
        $relatedProducts = $relatedProducts->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(4)
            ->get();
            
        $newProducts = Product::where('status', 1)
             ->where('id', '!=', $product->id)
            ->latest()
            ->take(6)
            ->get()
            ->shuffle();

        return view('frontend.page.product_details',compact('product', 'relatedProducts', 'newProducts'));
    }

//    public function quickView($id)
//    {
//        $product = Product::with('gallery', 'category')->findOrFail(decrypt($id));
//        $settings = \App\Models\WebSetting::first(); // or however you load settings
//
//        // Calculate final price
//        $price = $product->selling_price ?? 0;
//        if ($product->discount_type == 'amount') {
//            $finalPrice = $price - ($product->discount_amount ?? 0);
//        } elseif ($product->discount_type == 'percent') {
//            $finalPrice = $price - ($price * ($product->discount_amount ?? 0) / 100);
//        } else {
//            $finalPrice = $price;
//        }
//
//        $currency = $settings->currency_symbol ?? 'TK';
//
//        // Discount label
//        $discount = null;
//        if ($product->discount_type == 'amount' && !empty($product->discount_amount)) {
//            $discount = '- ' . $currency . ' ' . number_format($product->discount_amount, 2);
//        } elseif ($product->discount_type == 'percent' && !empty($product->discount_amount)) {
//            $discount = '- ' . number_format($product->discount_amount, 0) . '%';
//        }
//
//        return response()->json([
//            'name'          => $product->name,
//            'category'      => $product->category->name ?? '',
//            'featured_image'=> $product->featured_image
//                ? asset(config('imagepath.product') . $product->featured_image)
//                : asset('images/no-image.png'),
//            'gallery'       => $product->gallery->map(fn($g) => asset(config('imagepath.product') . $g->image)),
//            'price'         => $currency . ' ' . number_format($price, 2),
//            'final_price'   => $currency . ' ' . number_format($finalPrice, 2),
//            'discount'      => $discount,
//            'discount_type' => $product->discount_type,
//            'detail_url'    => route('product.details', encrypt($product->id)),
//        ]);
//    }



//    public function quickView($id)
//    {
//        $product = Product::with('gallery', 'category')->findOrFail(decrypt($id));
//        $settings = \App\Models\WebSetting::first();
//
//        // Calculate final price
//        $price = $product->selling_price ?? 0;
//        if ($product->discount_type == 'amount') {
//            $finalPrice = $price - ($product->discount_amount ?? 0);
//        } elseif ($product->discount_type == 'percent') {
//            $finalPrice = $price - ($price * ($product->discount_amount ?? 0) / 100);
//        } else {
//            $finalPrice = $price;
//        }
//
//        $currency = $settings->currency_symbol ?? 'TK';
//
//        // Discount label
//        $discount = null;
//        if ($product->discount_type == 'amount' && !empty($product->discount_amount)) {
//            $discount = '- ' . $currency . ' ' . number_format($product->discount_amount, 2);
//        } elseif ($product->discount_type == 'percent' && !empty($product->discount_amount)) {
//            $discount = '- ' . number_format($product->discount_amount, 0) . '%';
//        }
//
//        return response()->json([
//            'id'            => $product->id, // Add this line
//            'name'          => $product->name,
//            'category'      => $product->category->name ?? '',
//            'featured_image'=> $product->featured_image
//                ? asset(config('imagepath.product') . $product->featured_image)
//                : asset('images/no-image.png'),
//            'gallery'       => $product->gallery->map(fn($g) => asset(config('imagepath.product') . $g->image)),
//            'price'         => $currency . ' ' . number_format($price, 2),
//            'final_price'   => $currency . ' ' . number_format($finalPrice, 2),
//            'discount'      => $discount,
//            'discount_type' => $product->discount_type,
//            'detail_url'    => route('product.details', encrypt($product->id)),
//        ]);
//    }



//    public function quickView(Request $request)
//    {
//        $id = $request->id;
//        $product = Product::with('gallery', 'category')->findOrFail($id);
//        if(!$product){
//            abort('404');
//        }
//        $categories = Category::all();
//        return view('frontend.quick_view', compact('product','categories'));
//    }


//    public function quickView(Request $request)
//    {
//        $product = Product::with('gallery', 'category')->findOrFail($request->id);
//
//        return view('frontend.quick_view', compact('product'));
//    }



    public function quickView(Request $request)
    {
        try {
            $id = decrypt($request->id);
        } catch (\Exception $e) {
            abort(404, 'Invalid product.');
        }

        $product = Product::with('gallery', 'category', 'variations.variationValue', 'variations.variation')->findOrFail($id);
        $settings = \App\Models\WebSetting::first();

        // Calculate final price
        $price = $product->selling_price ?? 0;
        if ($product->discount_type == 'amount') {
            $finalPrice = $price - ($product->discount_amount ?? 0);
        } elseif ($product->discount_type == 'percent') {
            $finalPrice = $price - ($price * ($product->discount_amount ?? 0) / 100);
        } else {
            $finalPrice = $price;
        }

        $currency = $settings->currency_symbol ?? 'TK';

        // Discount label
        $discount = null;
        if ($product->discount_type == 'amount' && !empty($product->discount_amount)) {
            $discount = '- ' . $currency . ' ' . number_format($product->discount_amount, 2);
        } elseif ($product->discount_type == 'percent' && !empty($product->discount_amount)) {
            $discount = '- ' . number_format($product->discount_amount, 0) . '%';
        }

        return view('frontend.quick_view', compact('product', 'settings', 'price', 'finalPrice', 'currency', 'discount'));
    }

    public function addToCart(Request $request)
    {
        // Delete records older than 10 days to cleanup guest carts
        \App\Models\Cart::where('created_at', '<', now()->subDays(10))->delete();

        $productId = $request->product_id;
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found']);
        }

        // We use session()->getId() to track guest users' individual data
        $sessionId = session()->getId();

        // Calculate final price
        $price = $product->selling_price ?? 0;
        if ($product->discount_type == 'amount') {
            $finalPrice = $price - ($product->discount_amount ?? 0);
        } elseif ($product->discount_type == 'percent') {
            $finalPrice = $price - ($price * ($product->discount_amount ?? 0) / 100);
        } else {
            $finalPrice = $price;
        }

        // Check if product already exists in cart for this session
        $cart = \App\Models\Cart::where('session_id', $sessionId)
                    ->where('product_id', $productId)
                    ->first();

        $qty = $request->quantity ?? 1;

        if ($cart) {
            $cart->quantity += $qty;
            $cart->save();
        } else {
            \App\Models\Cart::create([
                'session_id' => $sessionId,
                'product_id' => $productId,
                'quantity' => $qty,
                'price' => $finalPrice
            ]);
        }

        $cartItems = \App\Models\Cart::with('product')->where('session_id', $sessionId)->get();
        $cartCount = $cartItems->sum('quantity');
        $cartTotal = $cartItems->sum(function($c) { return $c->price * $c->quantity; });
        
        $settings = \App\Models\WebSetting::first();
        $currency = $settings->currency_symbol ?? 'TK';

        $cartHtml = '';
        foreach($cartItems as $cItem) {
            $imageUrl = $cItem->product->featured_image ? asset(config('imagepath.product') . $cItem->product->featured_image) : asset('images/no-image.png');
            $detailUrl = route('product.details', encrypt($cItem->product->id));
            $shortName = \Illuminate\Support\Str::words($cItem->product->name, 2, '...');
            $cartHtml .= '<li>
                <div class="shopping-cart-img">
                    <a href="'.$detailUrl.'"><img alt="Nest" src="'.$imageUrl.'" /></a>
                </div>
                <div class="shopping-cart-title">
                    <h4><a href="'.$detailUrl.'">'.$shortName.'</a></h4>
                    <h4><span>'.$cItem->quantity.' × </span>'.$currency.' '.number_format($cItem->price, 2).'</h4>
                </div>
                <div class="shopping-cart-delete">
                    <a href="javascript:void(0)" class="remove-cart-item" data-id="'.$cItem->id.'" data-product-id="'.$cItem->product_id.'"><i class="fi-rs-cross-small"></i></a>
                </div>
            </li>';
        }

        return response()->json([
            'status' => 'success', 
            'message' => 'Product added to cart successfully',
            'cart_count' => $cartCount,
            'cart_html' => $cartHtml,
            'cart_total' => $currency . ' ' . number_format($cartTotal, 2)
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $cartId = $request->cart_id;
        $sessionId = session()->getId();

        \App\Models\Cart::where('id', $cartId)->where('session_id', $sessionId)->delete();

        $cartItems = \App\Models\Cart::with('product')->where('session_id', $sessionId)->get();
        $cartCount = $cartItems->sum('quantity');
        $cartTotal = $cartItems->sum(function($c) { return $c->price * $c->quantity; });
        
        $settings = \App\Models\WebSetting::first();
        $currency = $settings->currency_symbol ?? 'TK';

        $cartHtml = '';
        foreach($cartItems as $cItem) {
            $imageUrl = $cItem->product->featured_image ? asset(config('imagepath.product') . $cItem->product->featured_image) : asset('images/no-image.png');
            $detailUrl = route('product.details', encrypt($cItem->product->id));
            $shortName = \Illuminate\Support\Str::words($cItem->product->name, 2, '...');
            $cartHtml .= '<li>
                <div class="shopping-cart-img">
                    <a href="'.$detailUrl.'"><img alt="Nest" src="'.$imageUrl.'" /></a>
                </div>
                <div class="shopping-cart-title">
                    <h4><a href="'.$detailUrl.'">'.$shortName.'</a></h4>
                    <h4><span>'.$cItem->quantity.' × </span>'.$currency.' '.number_format($cItem->price, 2).'</h4>
                </div>
                <div class="shopping-cart-delete">
                    <a href="javascript:void(0)" class="remove-cart-item" data-id="'.$cItem->id.'" data-product-id="'.$cItem->product_id.'"><i class="fi-rs-cross-small"></i></a>
                </div>
            </li>';
        }

        return response()->json([
            'status' => 'success', 
            'message' => 'Product removed from cart successfully',
            'cart_count' => $cartCount,
            'cart_html' => $cartHtml,
            'cart_total' => $currency . ' ' . number_format($cartTotal, 2)
        ]);
    }
}
