<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fabric;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FabricController extends Controller
{
    /**
     * Display a listing of the fabrics.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $fabrics = Fabric::latest()->get();
            return DataTables::of($fabrics)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($row) {
                    if ($row->status == 1) {
                        return '<span class="badge badge-light-success">Active</span>';
                    }
                    return '<span class="badge badge-light-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn  = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm editFabric"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteFabric"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }
        return view('admin.fabric.index');
    }

    /**
     * Store a newly created fabric in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255|unique:fabrics,name',
            'status' => 'required|in:0,1',
        ]);

        try {
            Fabric::create([
                'name'   => $request->name,
                'status' => $request->status,
            ]);

            return response()->json(['success' => 'Fabric created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified fabric.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $fabric = Fabric::findOrFail($id);
        return response()->json($fabric);
    }

    /**
     * Update the specified fabric in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $fabric = Fabric::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:255|unique:fabrics,name,' . $id,
            'status' => 'required|in:0,1',
        ]);

        try {
            $fabric->update([
                'name'   => $request->name,
                'status' => $request->status,
            ]);

            return response()->json(['success' => 'Fabric updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified fabric from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $fabric = Fabric::findOrFail($id);
            $fabric->delete();
            return response()->json(['success' => 'Fabric deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }
}
