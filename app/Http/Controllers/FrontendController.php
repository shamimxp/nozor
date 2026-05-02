<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function wishlist(){
        return view('frontend.page.wishlist');
    }
}
