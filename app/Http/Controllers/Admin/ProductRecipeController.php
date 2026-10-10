<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RawMaterialProduct;
use App\Services\ProductRecipeWriter;
use Illuminate\Http\Request;

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
                ->addColumn('images', function ($row) {
                    $html = '';
                    if(is_array($row->manufacture_images) && count($row->manufacture_images) > 0) {
                        $html .= '<div class="d-flex" style="gap: 5px;">';
                        foreach(array_slice($row->manufacture_images, 0, 3) as $img) {
                            $html .= '<img src="' . asset($img) . '" width="40" height="40" class="rounded border" style="object-fit: cover;">';
                        }
                        if(count($row->manufacture_images) > 3) {
                            $html .= '<span class="badge badge-light-secondary align-self-center">+'.(count($row->manufacture_images)-3).'</span>';
                        }
                        $html .= '</div>';
                    }
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('admin.product-recipe.show', $row->id).'" class="btn btn-info btn-sm mr-1 viewBtn" title="View"><i data-feather="eye"></i></a>';
                    $btn .= '<a href="'.route('admin.product-recipe.edit', $row->id).'" class="btn btn-dark btn-sm mr-1 editBtn" title="Edit"><i data-feather="edit"></i></a>';
                    $btn .= '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteBtn"><i data-feather="trash-2"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action', 'material_items', 'images'])
                ->make(true);
        }
        return view('admin.product-recipe.index');
    }
    public function create(){
        $products = Product::where('status',1)->get();
        $raw_material = RawMaterialProduct::with(['materialType', 'unit'])->get();
        $cost_settings = \App\Models\CostSetting::where('status', 1)->orderBy('cost_title')->get();
        return view('admin.product-recipe.create', compact('products', 'raw_material', 'cost_settings'));
    }
    public function store(Request $request, ProductRecipeWriter $writer){
        $request->validate($this->recipeRules(true));

        $product = Product::findOrFail($request->product_id);
        $writer->sync($product, $request);

        toastr()->success('Product Recipe saved successfully.');
        return redirect()->route('admin.product-recipe.index');
    }
    public function show($id, \App\Services\ProductPriceCalculator $calculator){
        $recipe = \App\Models\ProductRecipe::with(['product', 'items.rawMaterialProduct', 'costSetting.items.costHead'])->findOrFail($id);
        $pricing = null;
        try {
            $pricing = $calculator->quote($recipe->product_id);
        } catch (\Exception $e) {
            $pricing = null;
        }
        return view('admin.product-recipe.show', compact('recipe', 'pricing'));
    }

    public function edit($id){
        $recipe = \App\Models\ProductRecipe::with(['items.rawMaterialProduct.materialType', 'items.rawMaterialProduct.unit', 'costSetting.items.costHead'])->findOrFail($id);
        $products = Product::where('status',1)->get();
        $raw_material = RawMaterialProduct::with(['materialType', 'unit'])->get();
        $cost_settings = \App\Models\CostSetting::orderBy('cost_title')->get();
        return view('admin.product-recipe.edit', compact('recipe', 'products', 'raw_material', 'cost_settings'));
    }

    public function update(Request $request, $id, ProductRecipeWriter $writer){
        $request->validate($this->recipeRules(true));

        $recipe = \App\Models\ProductRecipe::findOrFail($id);
        $product = Product::findOrFail($request->product_id);
        $writer->sync($product, $request, $recipe);

        toastr()->success('Product Recipe updated successfully.');
        return redirect()->route('admin.product-recipe.index');
    }

    public function destroy($id){
        $recipe = \App\Models\ProductRecipe::findOrFail($id);
        
        if (is_array($recipe->manufacture_images)) {
            foreach ($recipe->manufacture_images as $old_img) {
                if (file_exists(public_path($old_img))) {
                    @unlink(public_path($old_img));
                }
            }
        }
        
        $recipe->delete();
        return response()->json(['success' => 'Product Recipe deleted successfully.']);
    }

    private function recipeRules(bool $requireItems): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'cost_setting_id' => 'nullable|exists:cost_settings,id',
            'raw_materials' => $requireItems ? 'required|array|min:1' : 'nullable|array',
            'raw_material_id' => $requireItems ? 'required|array|min:1' : 'nullable|array',
            'raw_material_id.*' => 'exists:raw_material_products,id',
            'body_charge' => 'nullable|numeric|min:0',
            'finishing_charge' => 'nullable|numeric|min:0',
            'bearing' => 'nullable|numeric|min:0',
            'stone' => 'nullable|numeric|min:0',
            'electric_bill' => 'nullable|numeric|min:0',
            'gas_bill' => 'nullable|numeric|min:0',
            'box_price' => 'nullable|numeric|min:0',
            'carrying_charge' => 'nullable|numeric|min:0',
            'wire_price' => 'nullable|numeric|min:0',
            'pitol_price' => 'nullable|numeric|min:0',
            'dealer_profit_percent' => 'nullable|numeric|min:0',
            'dealer_profit_type' => 'nullable|in:percentage,fixed',
            'special_dealer_profit_percent' => 'nullable|numeric|min:0',
            'special_dealer_profit_type' => 'nullable|in:percentage,fixed',
            'normal_customer_profit_percent' => 'nullable|numeric|min:0',
            'normal_customer_profit_type' => 'nullable|in:percentage,fixed',
            'manufacture_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ];
    }

}
