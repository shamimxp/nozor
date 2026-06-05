<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = \App\Models\Coupon::latest()->get();
        return view('admin.page.coupon.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.page.coupon.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code',
            'type' => 'required|in:fixed,percent',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        \App\Models\Coupon::create($request->all());

        return redirect()->route('admin.coupon.index')->with('success', 'Coupon created successfully');
    }

    public function edit($id)
    {
        $coupon = \App\Models\Coupon::findOrFail($id);
        return view('admin.page.coupon.edit', compact('coupon'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code,'.$id,
            'type' => 'required|in:fixed,percent',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $coupon = \App\Models\Coupon::findOrFail($id);
        $coupon->update($request->all());

        return redirect()->route('admin.coupon.index')->with('success', 'Coupon updated successfully');
    }

    public function destroy($id)
    {
        \App\Models\Coupon::findOrFail($id)->delete();
        return redirect()->route('admin.coupon.index')->with('success', 'Coupon deleted successfully');
    }
}
