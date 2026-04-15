<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fabric;
use App\Models\FabricPrice;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FabricPriceController extends Controller
{
    /**
     * Display a listing of fabric prices.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $prices = FabricPrice::with('fabric')->latest()->get();
            return DataTables::of($prices)
                ->addIndexColumn()
                ->addColumn('fabric_name', fn($row) => $row->fabric ? $row->fabric->name : '-')
                ->addColumn('type_label', fn($row) => ucfirst($row->type))
                ->addColumn('sleeve_label', fn($row) => ucfirst($row->sleeve))
                ->addColumn('price_formatted', fn($row) => number_format($row->price, 2))
                ->addColumn('status_badge', function ($row) {
                    return $row->status == 1
                        ? '<span class="badge badge-light-success">Active</span>'
                        : '<span class="badge badge-light-danger">Inactive</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn  = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm editFabricPrice"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteFabricPrice"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        $fabrics = Fabric::where('status', 1)->orderBy('name')->get();
        return view('admin.fabric-price.index', compact('fabrics'));
    }

    /**
     * Store a newly created fabric price in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'fabric_id' => 'required|exists:fabrics,id',
            'type'      => 'required|in:polo,t-shirt',
            'sleeve'    => 'required|in:half,full',
            'price'     => 'required|numeric|min:0',
            'status'    => 'required|in:0,1',
        ]);

        try {
            FabricPrice::create($request->only('fabric_id', 'type', 'sleeve', 'price', 'status'));
            return response()->json(['success' => 'Fabric price created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified fabric price.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        $fabricPrice = FabricPrice::findOrFail($id);
        return response()->json($fabricPrice);
    }

    /**
     * Update the specified fabric price in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $fabricPrice = FabricPrice::findOrFail($id);

        $request->validate([
            'fabric_id' => 'required|exists:fabrics,id',
            'type'      => 'required|in:polo,t-shirt',
            'sleeve'    => 'required|in:half,full',
            'price'     => 'required|numeric|min:0',
            'status'    => 'required|in:0,1',
        ]);

        try {
            $fabricPrice->update($request->only('fabric_id', 'type', 'sleeve', 'price', 'status'));
            return response()->json(['success' => 'Fabric price updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified fabric price from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            FabricPrice::findOrFail($id)->delete();
            return response()->json(['success' => 'Fabric price deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }
}
