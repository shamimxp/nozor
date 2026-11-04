<?php

namespace App\Http\Controllers\WorkerPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit()
    {
        $worker = Auth::guard('worker')->user();

        return view('mypanel.profile.edit', compact('worker'));
    }

    public function update(Request $request)
    {
        $worker = Auth::guard('worker')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => ['required', 'string', 'max:30', Rule::unique('workers', 'phone')->ignore($worker->id)->whereNull('deleted_at')],
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|confirmed|min:6',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $worker->name = $request->name;
        $worker->phone = $request->phone;
        $worker->email = $request->email;
        $worker->address = $request->address;

        if ($request->filled('password')) {
            $worker->password = $request->password;
        }

        if ($request->hasFile('profile_image')) {
            if ($worker->profile_image) {
                Storage::disk('public')->delete($worker->profile_image);
            }
            $worker->profile_image = $request->file('profile_image')->store('workers', 'public');
        }

        $worker->save();

        toastr()->success('Profile updated successfully.');

        return back();
    }
}
