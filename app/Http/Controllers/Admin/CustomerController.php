<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $customers = Customer::with('addresses')->latest()->get();
            return DataTables::of($customers)
                ->addIndexColumn()
                ->addColumn('addresses', function ($row) {
                    $addresses = $row->addresses->pluck('address')->toArray();
                    return implode(', ', $addresses);
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm editCustomer"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteCustomer"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.customer.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:customers,phone|unique:users,phone',
            'email' => 'nullable|email|unique:customers,email|unique:users,email',
            'address' => 'nullable|array',
            'address.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make('12345678'),
            ]);

            $customer = Customer::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            if ($request->address) {
                foreach ($request->address as $addr) {
                    if ($addr) {
                        CustomerAddress::create([
                            'customer_id' => $customer->id,
                            'address' => $addr,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => 'Customer created successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong. ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $customer = Customer::with('addresses')->find($id);
        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'phone' => 'required|unique:customers,phone,' . $id . '|unique:users,phone,' . $customer->user_id,
            'email' => 'nullable|email|unique:customers,email,' . $id . '|unique:users,email,' . $customer->user_id,
            'address' => 'nullable|array',
            'address.*' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($customer->user_id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
            ]);

            $customer->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            // Simple way: delete old addresses and add new ones
            $customer->addresses()->delete();
            if ($request->address) {
                foreach ($request->address as $addr) {
                    if ($addr) {
                        CustomerAddress::create([
                            'customer_id' => $customer->id,
                            'address' => $addr,
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => 'Customer updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong. ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $customer = Customer::findOrFail($id);
            if ($customer->user_id) {
                User::where('id', $customer->user_id)->delete();
            }
            $customer->addresses()->delete();
            $customer->delete();
            DB::commit();
            return response()->json(['success' => 'Customer deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }
}
