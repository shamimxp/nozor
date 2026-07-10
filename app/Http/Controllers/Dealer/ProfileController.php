<?php

namespace App\Http\Controllers\Dealer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $dealer = Auth::guard('dealer')->user();
        return view('dealer.profile.edit', compact('dealer'));
    }

    public function update(Request $request)
    {
        $dealer = Auth::guard('dealer')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:dealers,phone,' . $dealer->id,
            'password' => 'nullable|confirmed|min:6',
        ]);

        $dealer->name = $request->name;
        $dealer->phone = $request->phone;

        if ($request->filled('password')) {
            $dealer->password = $request->password;
        }

        $dealer->save();

        toastr()->success('Profile updated successfully.');
        return back();
    }
}
