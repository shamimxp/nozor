<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class WorkerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Worker::query();
            
            if ($request->filled('name')) {
                $query->where('name', 'like', '%' . $request->name . '%');
            }
            if ($request->filled('phone')) {
                $query->where('phone', 'like', '%' . $request->phone . '%');
            }
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $workers = $query->latest()->get();
            return DataTables::of($workers)
                ->addIndexColumn()
                ->addColumn('type_label', function($row){
                    return $row->type == 1 ? 'Finishing Part' : 'Body Part';
                })
                ->addColumn('status_label', function($row){
                    return $row->status == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
                })
                ->addColumn('image', function($row){
                    if($row->profile_image) {
                        return '<img src="'.asset('storage/'.$row->profile_image).'" width="50" height="50" style="object-fit:cover; border-radius:50%;">';
                    }
                    return 'N/A';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm editWorker"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteWorker"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status_label', 'image'])
                ->make(true);
        }
        return view('admin.worker.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email|unique:workers,email',
            'type' => 'required|in:1,2',
            'password' => 'required|confirmed',
            'status' => 'required|in:0,1',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['password', 'password_confirmation', 'profile_image']);
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('workers', 'public');
        }

        Worker::create($data);

        return response()->json(['success' => 'Worker created successfully.']);
    }

    public function edit($id)
    {
        $worker = Worker::findOrFail($id);
        return response()->json($worker);
    }

    public function update(Request $request, $id)
    {
        $worker = Worker::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable',
            'email' => 'nullable|email|unique:workers,email,' . $id,
            'type' => 'required|in:1,2',
            'password' => 'nullable|confirmed',
            'status' => 'required|in:0,1',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except(['password', 'password_confirmation', 'profile_image']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            if ($worker->profile_image) {
                Storage::disk('public')->delete($worker->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('workers', 'public');
        }

        $worker->update($data);

        return response()->json(['success' => 'Worker updated successfully.']);
    }

    public function destroy($id)
    {
        $worker = Worker::findOrFail($id);
        if ($worker->profile_image) {
            Storage::disk('public')->delete($worker->profile_image);
        }
        $worker->delete();

        return response()->json(['success' => 'Worker deleted successfully.']);
    }
}
