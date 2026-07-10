<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DealerImpersonationController extends Controller
{
    public function loginAs($id)
    {
        $dealer = Dealer::findOrFail($id);

        // Store current admin ID in session to return back later
        session(['admin_id' => Auth::guard('admin')->id()]);

        // Login as dealer
        Auth::guard('dealer')->login($dealer);

        return redirect()->route('dealer.dashboard');
    }

    public function backToAdmin()
    {
        if (!session()->has('admin_id')) {
            return redirect()->route('dealer.dashboard');
        }

        $adminId = session('admin_id');

        Auth::guard('dealer')->logout();

        Auth::guard('admin')->loginUsingId($adminId);
        
        session()->forget('admin_id');

        return redirect()->route('admin.dealer.index');
    }
}
