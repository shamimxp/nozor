<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Variation;
use App\Models\VariationValue;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VariationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $variations = Variation::with('variationValues')->latest()->get();
            return DataTables::of($variations)
                ->addColumn('values', function ($row) {
                    return $row->variationValues->pluck('value')->implode(', ');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-primary btn-sm editVariation"><i data-feather="edit"></i></a>';
                    $btn .= ' <a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteVariation"><i data-feather="trash"></i></a>';
                    return $btn;
                })
                ->rawColumns(['values', 'action'])
                ->make(true);
        }
        return view('admin.variation.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:variations,name',
            'values' => 'required|array',
            'values.*' => 'required|string',
        ]);

        $variation = Variation::create([
            'name' => $request->name,
        ]);

        foreach ($request->values as $value) {
            VariationValue::create([
                'variation_id' => $variation->id,
                'value' => $value
            ]);
        }

        return response()->json(['success' => 'Variation saved successfully.']);
    }

    public function edit($id)
    {
        $variation = Variation::with('variationValues')->find($id);
        return response()->json($variation);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:variations,name,' . $id,
            'values' => 'required|array',
            'values.*' => 'required|string',
        ]);

        $variation = Variation::findOrFail($id);
        $variation->update([
            'name' => $request->name,
        ]);

        // Replace all values
        $variation->variationValues()->delete();
        foreach ($request->values as $value) {
            VariationValue::create([
                'variation_id' => $variation->id,
                'value' => $value
            ]);
        }

        return response()->json(['success' => 'Variation updated successfully.']);
    }

    public function destroy($id)
    {
        Variation::find($id)->delete();
        return response()->json(['success' => 'Variation deleted successfully.']);
    }
}
