<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('dealer.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $dealer = Dealer::where('phone', $request->phone)->first();

        if (!$dealer) {
            return back()->withInput($request->only('phone'))->withErrors([
                'phone' => 'These credentials do not match our records.',
            ]);
        }

        $passwordMatches = false;

        if ($dealer->password) {
            $passwordMatches = Hash::check($request->password, $dealer->password);
        } else {
            $passwordMatches = ($request->password === $dealer->phone);
        }

        if ($passwordMatches) {
            if ($dealer->status == 0) {
                return back()->withInput($request->only('phone'))->withErrors([
                    'phone' => 'Your account is inactive.',
                ]);
            }

            Auth::guard('dealer')->login($dealer);
            return redirect()->route('dealer.dashboard');
        }

        return back()->withInput($request->only('phone'))->withErrors([
            'phone' => 'These credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('dealer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dealer.login');
    }
}
