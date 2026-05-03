<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
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
