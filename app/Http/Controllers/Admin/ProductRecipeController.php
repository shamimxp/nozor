<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RawMaterialProduct;
use Illuminate\Http\Request;
use Ramsey\Collection\Queue;

class ProductRecipeController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {
            $data = \App\Models\ProductRecipe::with(['product', 'items.rawMaterialProduct'])->latest()->get();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('product_name', function ($row) {
                    return $row->product ? $row->product->name : 'N/A';
                })
                ->addColumn('material_items', function ($row) {
                    return $row->items->map(function ($item) {
                        return $item->rawMaterialProduct ? '<span class="badge badge-light-primary">' . $item->rawMaterialProduct->name . '</span>' : '';
                    })->filter()->implode(' ');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('admin.product-recipe.show', $row->id).'" class="btn btn-info btn-sm mr-1 viewBtn" title="View"><i data-feather="eye"></i></a>';
                    $btn .= '<a href="'.route('admin.product-recipe.edit', $row->id).'" class="btn btn-dark btn-sm mr-1 editBtn" title="Edit"><i data-feather="edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteBtn"><i data-feather="trash-2"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'material_items'])
                ->make(true);
        }
        return view('admin.product-recipe.index');
    }
    public function create(){
        $products = Product::where('status',1)->get();
        $raw_material = RawMaterialProduct::all();
        return view('admin.product-recipe.create', compact('products', 'raw_material'));
    }
    public function store(Request $request){
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'raw_materials' => 'required|array',
            'raw_material_id' => 'required|array',
        ]);

        $recipe = \App\Models\ProductRecipe::create([
            'product_id' => $request->product_id,
            'body_charge' => $request->body_charge ?? 0,
            'finishing_charge' => $request->finishing_charge ?? 0,
            'bearing' => $request->bearing ?? 0,
            'stone' => $request->stone ?? 0,
            'electric_bill' => $request->electric_bill ?? 0,
            'gas_bill' => $request->gas_bill ?? 0,
            'box_price' => $request->box_price ?? 0,
            'carrying_charge' => $request->carrying_charge ?? 0,
            'wire_price' => $request->wire_price ?? 0,
        ]);

        if ($request->has('raw_material_id')) {
            foreach ($request->raw_material_id as $key => $raw_material_id) {
                \App\Models\ProductRecipeItem::create([
                    'product_recipe_id' => $recipe->id,
                    'raw_material_product_id' => $raw_material_id,
                    'thickness' => $request->thickness[$key] ?? 0,
                    'width' => $request->width[$key] ?? 0,
                    'height' => $request->height[$key] ?? 0,
                    'area' => $request->area[$key] ?? 0,
                    'grade_value' => $request->grade_value[$key] ?? 0,
                    'qty' => $request->qty[$key] ?? 0,
                ]);
            }
        }

        return redirect()->route('admin.product-recipe.index')->with('success', 'Product Recipe saved successfully.');
    }
    public function show($id){
        $recipe = \App\Models\ProductRecipe::with(['product', 'items.rawMaterialProduct'])->findOrFail($id);
        return view('admin.product-recipe.show', compact('recipe'));
    }

    public function edit($id){
        $recipe = \App\Models\ProductRecipe::with(['items.rawMaterialProduct.materialType'])->findOrFail($id);
        $products = Product::where('status',1)->get();
        $raw_material = RawMaterialProduct::all();
        $selected_materials = $recipe->items->pluck('raw_material_product_id')->toArray();
        return view('admin.product-recipe.edit', compact('recipe', 'products', 'raw_material', 'selected_materials'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'raw_materials' => 'required|array',
            'raw_material_id' => 'required|array',
        ]);

        $recipe = \App\Models\ProductRecipe::findOrFail($id);
        $recipe->update([
            'product_id' => $request->product_id,
            'body_charge' => $request->body_charge ?? 0,
            'finishing_charge' => $request->finishing_charge ?? 0,
            'bearing' => $request->bearing ?? 0,
            'stone' => $request->stone ?? 0,
            'electric_bill' => $request->electric_bill ?? 0,
            'gas_bill' => $request->gas_bill ?? 0,
            'box_price' => $request->box_price ?? 0,
            'carrying_charge' => $request->carrying_charge ?? 0,
        ]);

        // Re-create items
        $recipe->items()->delete();

        if ($request->has('raw_material_id')) {
            foreach ($request->raw_material_id as $key => $raw_material_id) {
                \App\Models\ProductRecipeItem::create([
                    'product_recipe_id' => $recipe->id,
                    'raw_material_product_id' => $raw_material_id,
                    'thickness' => $request->thickness[$key] ?? 0,
                    'width' => $request->width[$key] ?? 0,
                    'height' => $request->height[$key] ?? 0,
                    'area' => $request->area[$key] ?? 0,
                    'grade_value' => $request->grade_value[$key] ?? 0,
                    'qty' => $request->qty[$key] ?? 0,
                ]);
            }
        }

        return redirect()->route('admin.product-recipe.index')->with('success', 'Product Recipe updated successfully.');
    }

    public function destroy($id){
        $recipe = \App\Models\ProductRecipe::findOrFail($id);
        $recipe->delete();
        return response()->json(['success' => 'Product Recipe deleted successfully.']);
    }

}
