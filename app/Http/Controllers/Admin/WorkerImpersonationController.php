<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;

class WorkerImpersonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:worker.edit')->only('loginAs');
    }

    public function loginAs($id)
    {
        $worker = Worker::findOrFail($id);

        session(['admin_id' => Auth::guard('admin')->id()]);

        Auth::guard('worker')->login($worker);

        return redirect()->route('mypanel.dashboard');
    }

    public function backToAdmin()
    {
        if (! session()->has('admin_id')) {
            return redirect()->route('mypanel.dashboard');
        }

        $adminId = session('admin_id');

        Auth::guard('worker')->logout();

        Auth::guard('admin')->loginUsingId($adminId);

        session()->forget('admin_id');

        return redirect()->route('admin.worker.index');
    }
}
