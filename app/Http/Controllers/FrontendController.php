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
        $products = Product::with('gallery')
            ->where('status', 1)
            ->latest()
            ->take(20)
            ->get();
        $settings = WebSetting::first();

        $webSales = \App\Models\WebOrderItem::select('product_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total'))
            ->groupBy('product_id')
            ->pluck('total', 'product_id')->toArray();

        $posSales = \App\Models\PosOrderItem::select('product_id', \Illuminate\Support\Facades\DB::raw('SUM(quantity) as total'))
            ->groupBy('product_id')
            ->pluck('total', 'product_id')->toArray();

        $productSales = [];
        foreach ($webSales as $id => $total) {
            $productSales[$id] = ($productSales[$id] ?? 0) + $total;
        }
        foreach ($posSales as $id => $total) {
            $productSales[$id] = ($productSales[$id] ?? 0) + $total;
        }

        arsort($productSales);
        $topProductIds = array_slice(array_keys($productSales), 0, 3);

        $topSellingProducts = collect();
        if (!empty($topProductIds)) {
            $topSellingProductsResult = Product::with('category')->where('status', 1)->whereIn('id', $topProductIds)->get();
            $topSellingProducts = collect($topProductIds)->map(function ($id) use ($topSellingProductsResult) {
                return $topSellingProductsResult->where('id', $id)->first();
            })->filter();
        } else {
            $topSellingProducts = Product::with( 'category')->where('status', 1)->take(3)->get();
        }

        $featuredProducts3 = Product::with( 'category')->where('status', 1)->where('is_featured', 1)->inRandomOrder()->take(3)->get();

        $recentProducts3 = Product::with( 'category')->where('status', 1)->latest()->take(3)->get();

        $featuredProducts = Product::where('status', 1)->where('is_featured', 1)->latest()->take(20)->get();

        return view('frontend.index',compact('categories','banners','products','settings','featuredProducts', 'topSellingProducts', 'featuredProducts3', 'recentProducts3'));
    }

    public function cart(){
        $sessionId = session()->getId();
        $cartItems = \App\Models\Cart::with('product')->where('session_id', $sessionId)->get();
        $cartTotal = $cartItems->sum(function($c) { return $c->price * $c->quantity; });
        $settings = \App\Models\WebSetting::first();
        return view('frontend.page.cart', compact('cartItems', 'cartTotal', 'settings'));
    }
    public function checkout(){
        $sessionId = session()->getId();
        $cartItems = \App\Models\Cart::with('product')->where('session_id', $sessionId)->get();
        $cartTotal = $cartItems->sum(function($c) { return $c->price * $c->quantity; });
        $settings = \App\Models\WebSetting::first();
        return view('frontend.page.checkout', compact('cartItems', 'cartTotal', 'settings'));
    }
    public function contact(){
        return view('frontend.page.contact');
    }
    public function about(){
        return view('frontend.page.about');
    }

    public function ajaxSearch(Request $request)
    {
        $query = $request->get('q');
        $categoryId = $request->get('category');
        
        $productsQuery = \App\Models\Product::where('status', 1)->where('name', 'LIKE', '%' . $query . '%');
        
        if (!empty($categoryId)) {
            $category = \App\Models\Category::where('slug', $categoryId)->first();
            if ($category) {
                $productsQuery->where('category_id', $category->id);
            }
        }

        $products = $productsQuery->take(6)->get();

        $html = '';
        if ($products->count() > 0) {
            foreach ($products as $product) {
                $imageUrl = $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png');
                $detailUrl = route('product.details', encrypt($product->id));
                $price = $product->selling_price;
                if ($product->discount_type == 'amount') {
                    $finalPrice = $price - ($product->discount_amount ?? 0);
                } elseif ($product->discount_type == 'percent') {
                    $finalPrice = $price - ($price * ($product->discount_amount ?? 0) / 100);
                } else {
                    $finalPrice = $price;
                }
                $settings = \App\Models\WebSetting::first();
                $currency = $settings->currency_symbol ?? 'TK';
                $formattedPrice = number_format($finalPrice, 2);

                $html .= '<a href="'.$detailUrl.'" style="display:flex; align-items:center; padding:10px 15px; border-bottom:1px solid #f1f1f1; text-decoration:none; color:#333; transition:background 0.2s;">
                    <img src="'.$imageUrl.'" style="width:40px; height:40px; object-fit:cover; border-radius:4px; margin-right:15px;">
                    <div style="flex-grow:1;">
                        <h6 style="margin:0; font-size:14px; font-weight:600; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; max-width:200px;">'.$product->name.'</h6>
                        <span style="font-size:13px; color:#F15822; font-weight:700;">'.$currency.' '.$formattedPrice.'</span>
                    </div>
                </a>';
            }
            $html .= '<a href="'.route('shop').'?q='.$query.'&category='.$categoryId.'" style="display:block; text-align:center; padding:10px; font-size:13px; font-weight:600; color:#F15822;">View All Results</a>';
        } else {
            $html = '<div style="padding:15px; text-align:center; color:#999;">No products found</div>';
        }

        return response()->json(['html' => $html]);
    }
    public function shop(Request $request, $slug = null){
        $query = \App\Models\Product::with('category')->where('status', 1);

        if ($slug) {
            $category = \App\Models\Category::where('slug', $slug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->has('q') && !empty($request->get('q'))) {
            $query->where('name', 'LIKE', '%' . $request->get('q') . '%');
        }

        if ($request->has('category') && !empty($request->get('category'))) {
            $reqCat = \App\Models\Category::where('slug', $request->get('category'))->first();
            if ($reqCat) {
                $query->where('category_id', $reqCat->id);
            }
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        if ($sort == 'price_low') {
            $query->orderBy('selling_price', 'asc')->orderBy('id', 'desc');
        } elseif ($sort == 'price_high') {
            $query->orderBy('selling_price', 'desc')->orderBy('id', 'desc');
        } else {
            $query->latest()->orderBy('id', 'desc');
        }

        $settings = \App\Models\WebSetting::first();
        $totalProducts = $query->count();

        // AJAX: called by infinite scroll
        if ($request->ajax()) {
            $scrollPage  = (int) $request->get('scroll_page', 1); // 0 = reload initial 50; 1+ = batches of 10
            $perScroll   = 10;
            $initialLoad = 50;

            if ($scrollPage <= 0) {
                // Sort changed — reload first 50
                $products  = (clone $query)->take($initialLoad)->get();
                $hasMore   = $totalProducts > $initialLoad;
            } else {
                $offset   = $initialLoad + (($scrollPage - 1) * $perScroll);
                $products = (clone $query)->skip($offset)->take($perScroll)->get();
                $hasMore  = ($offset + $perScroll) < $totalProducts;
            }

            $html = view('frontend.partials.shop_products', compact('products', 'settings'))->render();
            return response()->json([
                'html'     => $html,
                'total'    => $totalProducts,
                'has_more' => $hasMore,
            ]);
        }

        // Initial page load: show first 50 products
        $products = (clone $query)->take(50)->get();
        $categories = \App\Models\Category::withCount('products')->where('status', 1)->take(10)->get();

        return view('frontend.page.shop', compact('products', 'categories', 'settings', 'totalProducts'));
    }
    public function deal(){
        return view('frontend.page.dealpage');
    }
    public function details($id){
        $product = Product::with(['gallery','category', 'variations.variationValue', 'variations.variation', 'reviews' => function($q) {
            $q->where('status', 1)->latest();
        }])->findOrFail(decrypt($id));

        $relatedProducts = Product::where('status', 1)
            ->where('category_id', $product->category_id);

        if ($product->sub_category_id) {
            $relatedProducts->where('sub_category_id', $product->sub_category_id);
        }

        $relatedProducts = $relatedProducts->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(8)
            ->get();

        $newProducts = Product::where('status', 1)
             ->where('id', '!=', $product->id)
            ->latest()
            ->take(6)
            ->get()
            ->shuffle();

        $categories = \App\Models\Category::withCount('products')->where('status', 1)->take(10)->get();

        return view('frontend.page.product_details',compact('product', 'relatedProducts', 'newProducts', 'categories'));
    }

    public function submitReview(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string|max:255',
            'comment' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        \App\Models\ProductReview::create([
            'product_id' => $request->product_id,
            'name' => $request->name,
            'comment' => $request->comment,
            'rating' => $request->rating,
            'status' => 0 // pending admin approval
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Review submitted successfully! It will be visible after admin approval.'
        ]);
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
    public function updateCart(Request $request)
    {
        $cartId = $request->cart_id;
        $quantity = $request->quantity;

        $cart = \App\Models\Cart::find($cartId);
        if($cart) {
            $cart->quantity = $quantity;
            $cart->save();
        }

        // Return updated generic header cart data
        $sessionId = session()->getId();
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
            'cart_count' => $cartCount,
            'cart_html' => $cartHtml,
            'cart_total' => $currency . ' ' . number_format($cartTotal, 2)
        ]);
    }

    public function updateVariation(Request $request)
    {
        $cartId = $request->cart_id;
        $type = $request->type; // 'color' or 'size'
        $value = $request->value;

        $cart = \App\Models\Cart::where('id', $cartId)
                    ->where('session_id', session()->getId())
                    ->first();

        if($cart && in_array($type, ['color', 'size'])) {
            $cart->{$type} = $value;
            $cart->save();
            return response()->json(['status' => 'success', 'message' => ucfirst($type) . ' updated successfully']);
        }

        return response()->json(['status' => 'error', 'message' => 'Cart item not found'], 404);
    }

    public function clearCart(Request $request)
    {
        $sessionId = session()->getId();
        \App\Models\Cart::where('session_id', $sessionId)->delete();

        $settings = \App\Models\WebSetting::first();
        $currency = $settings->currency_symbol ?? 'TK';

        return response()->json([
            'status' => 'success',
            'cart_count' => 0,
            'cart_html' => '',
            'cart_total' => $currency . ' 0.00'
        ]);
    }

    public function applyCoupon(Request $request)
    {
        $code = $request->coupon_code;
        $coupon = \App\Models\Coupon::where('code', $code)->where('status', 1)->first();

        if(!$coupon) {
            session()->forget('coupon');
            return response()->json(['status' => 'error', 'message' => 'Invalid Coupon Code.']);
        }

        if(date('Y-m-d') < $coupon->start_date || date('Y-m-d') > $coupon->end_date) {
            session()->forget('coupon');
            return response()->json(['status' => 'error', 'message' => 'Coupon has expired or is not active yet.']);
        }

        // Apply coupon to session
        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'amount' => $coupon->amount
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon applied successfully.',
            'coupon' => session()->get('coupon')
        ]);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'billing_address' => 'required',
            'payment_method' => 'required',
            'shipping_area' => 'required'
        ]);

        $sessionId = session()->getId();
        $cartItems = \App\Models\Cart::with('product')->where('session_id', $sessionId)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Your cart is empty!');
        }

        $cartTotal = $cartItems->sum(function($c) { return $c->price * $c->quantity; });

        $discount = 0;
        if(session()->has('coupon')) {
            $cpn = session()->get('coupon');
            if($cpn['type'] == 'percent') {
                $discount = ($cartTotal * $cpn['amount']) / 100;
            } else {
                $discount = $cpn['amount'];
            }
            if($discount > $cartTotal) $discount = $cartTotal;
        }

        $shippingCharge = $request->shipping_area;
        $total = $cartTotal - $discount + $shippingCharge;

        $order = \App\Models\WebOrder::create([
            'invoice_no' => 'INV-' . strtoupper(uniqid()),
            'user_id' => auth()->check() ? auth()->id() : null,
            'subtotal' => $cartTotal,
            'discount' => $discount,
            'shipping_charge' => $shippingCharge,
            'total' => $total,
            'payment_method' => $request->payment_method,
            'status' => 'pending'
        ]);

        \App\Models\WebOrderAddress::create([
            'web_order_id' => $order->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->billing_address,
            'note' => $request->note
        ]);

        foreach($cartItems as $item) {
            \App\Models\WebOrderItem::create([
                'web_order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'color' => $item->color,
                'size' => $item->size
            ]);
        }

        // Clear cart and coupon
        \App\Models\Cart::where('session_id', $sessionId)->delete();
        session()->forget('coupon');

        return redirect()->route('index')->with('success', 'Order placed successfully!');
    }

    public function removeCoupon(Request $request)
    {
        session()->forget('coupon');
        return response()->json(['status' => 'success', 'message' => 'Coupon removed.']);
    }

    public function wishlist()
    {
        $wishlists = collect();
        if (auth()->check()) {
            $wishlists = \App\Models\Wishlist::with('product')
                ->where('user_id', auth()->id())
                ->latest()
                ->get();
        }
        return view('frontend.page.wishlist', compact('wishlists'));
    }

    public function toggleWishlist(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please login to add items to your wishlist.'
            ]);
        }

        $productId = $request->product_id;
        $userId = auth()->id();

        $existing = \App\Models\Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $wishlistCount = \App\Models\Wishlist::where('user_id', $userId)->count();
            return response()->json([
                'status' => 'removed',
                'message' => 'Product removed from wishlist.',
                'wishlist_count' => $wishlistCount
            ]);
        }

        \App\Models\Wishlist::create([
            'user_id' => $userId,
            'product_id' => $productId,
        ]);

        $wishlistCount = \App\Models\Wishlist::where('user_id', $userId)->count();
        return response()->json([
            'status' => 'added',
            'message' => 'Product added to wishlist!',
            'wishlist_count' => $wishlistCount
        ]);
    }

    public function removeWishlist(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized.']);
        }

        \App\Models\Wishlist::where('user_id', auth()->id())
            ->where('id', $request->wishlist_id)
            ->delete();

        $wishlistCount = \App\Models\Wishlist::where('user_id', auth()->id())->count();
        return response()->json([
            'status' => 'success',
            'message' => 'Product removed from wishlist.',
            'wishlist_count' => $wishlistCount
        ]);
    }
}
