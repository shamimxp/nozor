<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class DealerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $dealers = Dealer::latest();
            
            // Apply filtering if provided in request
            if ($request->status != '') {
                $dealers->where('status', $request->status);
            }
            if ($request->is_special != '') {
                $dealers->where('is_special', $request->is_special);
            }
            if (!empty($request->phone)) {
                $dealers->where('phone', 'like', '%' . $request->phone . '%');
            }
            if (!empty($request->shop_name)) {
                $dealers->where('shop_name', 'like', '%' . $request->shop_name . '%');
            }

            return DataTables::of($dealers)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge badge-light-success">Active</span>';
                    }
                    return '<span class="badge badge-light-danger">Inactive</span>';
                })
                ->editColumn('is_special', function ($row) {
                    if ($row->is_special == 1) {
                        return '<span class="badge badge-light-info">Yes</span>';
                    }
                    return '<span class="badge badge-light-secondary">No</span>';
                })
                ->editColumn('profile_image', function($row){
                    if($row->profile_image) {
                        return '<img src="'.asset($row->profile_image).'" alt="Profile" style="width:50px;height:50px;object-fit:cover;border-radius:50%;">';
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('admin.dealer.show', $row->id).'" class="btn btn-info btn-sm mr-1 viewDealer" title="View"><i data-feather="eye"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm mr-1 editDealer" title="Edit"><i data-feather="edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteDealer" title="Delete"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status', 'is_special', 'profile_image', 'action'])
                ->make(true);
        }
        return view('admin.dealer.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'shop_name' => 'required',
            'phone' => 'required|unique:dealers,phone',
            'email' => 'nullable|email',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nid_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'trade_license' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['profile_image', 'nid_image', 'trade_license', '_token', 'dealer_id', '_method', 'password', 'status', 'is_special']);
            $data['password'] = Hash::make($request->phone); // default password is phone number
            $data['status'] = $request->has('status') ? 1 : 0;
            $data['is_special'] = $request->has('is_special') ? 1 : 0;

            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $name = time().'_profile.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/dealers');
                $image->move($destinationPath, $name);
                $data['profile_image'] = 'uploads/dealers/'.$name;
            }
            if ($request->hasFile('nid_image')) {
                $image = $request->file('nid_image');
                $name = time().'_nid.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/dealers');
                $image->move($destinationPath, $name);
                $data['nid_image'] = 'uploads/dealers/'.$name;
            }
            if ($request->hasFile('trade_license')) {
                $image = $request->file('trade_license');
                $name = time().'_trade.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/dealers');
                $image->move($destinationPath, $name);
                $data['trade_license'] = 'uploads/dealers/'.$name;
            }

            $dealer = Dealer::create($data);

            DB::commit();
            return response()->json([
                'success' => 'Dealer created successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong. ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $dealer = Dealer::findOrFail($id);
        return view('admin.dealer.show', compact('dealer'));
    }

    public function edit($id)
    {
        $dealer = Dealer::find($id);
        return response()->json($dealer);
    }

    public function update(Request $request, $id)
    {
        $dealer = Dealer::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'shop_name' => 'required',
            'phone' => 'required|unique:dealers,phone,' . $id,
            'email' => 'nullable|email',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nid_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'trade_license' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->except(['profile_image', 'nid_image', 'trade_license', '_token', 'dealer_id', '_method', 'password', 'status', 'is_special']);
            $data['status'] = $request->has('status') ? 1 : 0;
            $data['is_special'] = $request->has('is_special') ? 1 : 0;

            if ($request->hasFile('profile_image')) {
                if ($dealer->profile_image && file_exists(public_path($dealer->profile_image))) {
                    unlink(public_path($dealer->profile_image));
                }
                $image = $request->file('profile_image');
                $name = time().'_profile.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/dealers');
                $image->move($destinationPath, $name);
                $data['profile_image'] = 'uploads/dealers/'.$name;
            }
            if ($request->hasFile('nid_image')) {
                if ($dealer->nid_image && file_exists(public_path($dealer->nid_image))) {
                    unlink(public_path($dealer->nid_image));
                }
                $image = $request->file('nid_image');
                $name = time().'_nid.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/dealers');
                $image->move($destinationPath, $name);
                $data['nid_image'] = 'uploads/dealers/'.$name;
            }
            if ($request->hasFile('trade_license')) {
                if ($dealer->trade_license && file_exists(public_path($dealer->trade_license))) {
                    unlink(public_path($dealer->trade_license));
                }
                $image = $request->file('trade_license');
                $name = time().'_trade.'.$image->getClientOriginalExtension();
                $destinationPath = public_path('/uploads/dealers');
                $image->move($destinationPath, $name);
                $data['trade_license'] = 'uploads/dealers/'.$name;
            }

            $dealer->update($data);

            DB::commit();
            return response()->json(['success' => 'Dealer updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong. ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $dealer = Dealer::findOrFail($id);
            if ($dealer->profile_image && file_exists(public_path($dealer->profile_image))) {
                unlink(public_path($dealer->profile_image));
            }
            if ($dealer->nid_image && file_exists(public_path($dealer->nid_image))) {
                unlink(public_path($dealer->nid_image));
            }
            if ($dealer->trade_license && file_exists(public_path($dealer->trade_license))) {
                unlink(public_path($dealer->trade_license));
            }
            $dealer->delete();
            DB::commit();
            return response()->json(['success' => 'Dealer deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }
}
