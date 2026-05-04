<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\WebSetting;
use Illuminate\Http\Request;
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
        $products = Product::where('status',1)->get();
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

}
