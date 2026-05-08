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



}
