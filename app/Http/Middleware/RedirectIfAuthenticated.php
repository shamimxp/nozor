<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if ($guard == "admin" && Auth::guard($guard)->check()) {
            return redirect(RouteServiceProvider::ADMIN_HOME);
        }
        if ($guard == "seller" && Auth::guard($guard)->check()) {
            return redirect(RouteServiceProvider::SELLER_HOME);
        }
        if ($guard == "dealer" && Auth::guard($guard)->check()) {
            return redirect()->route('dealer.dashboard');
        }
        if ($guard == "worker" && Auth::guard($guard)->check()) {
            return redirect(RouteServiceProvider::WORKER_HOME);
        }
        if (Auth::guard($guard)->check()) {
            return redirect(RouteServiceProvider::HOME);
        }
        return $next($request);
    }
}
