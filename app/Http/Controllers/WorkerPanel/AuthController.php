<?php

namespace App\Http\Controllers\WorkerPanel;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('worker')->check()) {
            return redirect()->route('mypanel.dashboard');
        }

        return view('mypanel.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);

        $worker = Worker::where('phone', $request->phone)->first();

        if (! $worker) {
            return back()->withInput($request->only('phone'))->withErrors([
                'phone' => 'These credentials do not match our records.',
            ]);
        }

        $passwordMatches = false;
        if ($worker->password) {
            $passwordMatches = Hash::check($request->password, $worker->password);
        } else {
            $passwordMatches = ($request->password === $worker->phone);
        }

        if (! $passwordMatches) {
            return back()->withInput($request->only('phone'))->withErrors([
                'phone' => 'These credentials do not match our records.',
            ]);
        }

        if ((int) $worker->status !== 1) {
            return back()->withInput($request->only('phone'))->withErrors([
                'phone' => 'Your account is inactive. Please contact admin.',
            ]);
        }

        Auth::guard('worker')->login($worker, $request->boolean('remember'));

        return redirect()->intended(route('mypanel.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('worker')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mypanel.login');
    }
}
