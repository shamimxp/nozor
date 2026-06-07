<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if ($user) {
            Auth::login($user);
            toastr()->success('Logged in successfully.');
            return redirect()->route('index');
        } else {
            toastr()->error('Invalid phone number.');
            return back()->withInput();
        }
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->phone),
            ]);

            Auth::login($user);
            toastr()->success('Registered successfully.');
            return redirect()->route('index');
        } catch (\Exception $e) {
            toastr()->error('Error: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        toastr()->success('Logged out successfully.');
        return redirect()->route('index');
    }

    public function myAccount()
    {
        $user = Auth::user();
        $orders = \App\Models\WebOrder::where('user_id', Auth::id())->latest()->get();
        return view('frontend.user.my_account', compact('user', 'orders'));
    }

    public function updateAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with(['success' => 'Account details updated successfully.', 'active_tab' => 'account-detail']);
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'invoice_no' => 'required|string',
        ]);

        $trackedOrder = \App\Models\WebOrder::with(['items.product', 'address'])
            ->where('invoice_no', $request->invoice_no)
            ->first();

        if (!$trackedOrder) {
            toastr()->error('Order not found. Please check your invoice number.');
            return back()->with('active_tab', 'track-orders');
        }

        return back()->with([
            'trackedOrder' => $trackedOrder,
            'active_tab' => 'track-orders'
        ]);
    }
}
