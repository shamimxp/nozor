<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $dealer = Auth::guard('dealer')->user();
        return view('dealer.dashboard', compact('dealer'));
    }
}
