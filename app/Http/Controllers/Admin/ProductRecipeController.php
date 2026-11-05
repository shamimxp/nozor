<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\AuthorizesAdminModule;
use App\Http\Controllers\Admin\Concerns\HandlesTrash;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RawMaterialProduct;
use App\Services\Deletion\SoftDeleteService;
use App\Services\ProductRecipeWriter;
use Illuminate\Http\Request;

class ProductRecipeController extends Controller
{
    use HandlesTrash, AuthorizesAdminModule;

    protected function trashModuleKey(): string
    {
        return 'product_recipe';
    }

    public function __construct()
    {
        $this->authorizeAdminModule('product_recipe');
    }


    public function index(Request $request){
        if ($request->ajax()) {
            $data = \App\Models\ProductRecipe::with(['product', 'items.rawMaterialProduct', 'costSetting'])->latest()->get();
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('product_name', function ($row) {
                    return $row->product ? $row->product->name : 'N/A';
                })
                ->addColumn('cost_title', function ($row) {
                    if ($row->costSetting) {
                        return '<span class="badge badge-light-info font-weight-bold">' . e($row->costSetting->cost_title) . '</span>';
                    }
                    return '<span class="badge badge-light-secondary">N/A</span>';
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
                    $btn = perm_html('product_recipe.show', '<a href="'.route('admin.product-recipe.show', $row->id).'" class="btn btn-info btn-sm mr-1 viewBtn" title="View"><i data-feather="eye"></i></a>');
                    $btn .= perm_html('product_recipe.edit', '<a href="'.route('admin.product-recipe.edit', $row->id).'" class="btn btn-dark btn-sm mr-1 editBtn" title="Edit"><i data-feather="edit"></i></a>');
                    $btn .= perm_html('product_recipe.delete', '<a href="javascript:void(0)" data-id="' . $row->id . '" class="btn btn-danger btn-sm deleteBtn"><i data-feather="trash-2"></i></a>');

                    return $btn;
                })
                ->rawColumns(['action', 'material_items', 'images', 'cost_title'])
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
        $recipe = \App\Models\ProductRecipe::with([
            'product',
            'items.rawMaterialProduct.materialType',
            'items.rawMaterialProduct.unit',
            'costSetting.items.costHead'
        ])->findOrFail($id);

        $totalMaterialCost = 0;
        $recipeItemsData = [];
        foreach ($recipe->items as $item) {
            $material = $item->rawMaterialProduct;
            $unitPrice = $material ? (float)$material->price_per_unit : 0;
            $grade = (float)($item->grade_value ?? ($material ? $material->grade_value : 0));
            $area = (float)$item->area;
            $qty = (float)$item->qty;
            if ($qty == 0 && $area > 0 && $grade > 0) {
                $qty = round($area * $grade);
            }
            $cost = round($qty * $unitPrice);
            $totalMaterialCost += $cost;

            $recipeItemsData[] = [
                'item' => $item,
                'unit_price' => $unitPrice,
                'qty' => $qty,
                'cost' => $cost,
            ];
        }

        $totalCharges = 0;
        if ($recipe->costSetting && $recipe->costSetting->items->isNotEmpty()) {
            $totalCharges = (float)$recipe->costSetting->items->sum('cost_price');
        } else {
            $totalCharges = (float)(
                $recipe->body_charge + $recipe->finishing_charge + $recipe->bearing +
                $recipe->stone + $recipe->electric_bill + $recipe->gas_bill +
                $recipe->box_price + $recipe->carrying_charge + $recipe->wire_price +
                $recipe->pitol_price
            );
        }

        $totalProductCost = round($totalMaterialCost + $totalCharges, 2);

        // Dealer pricing
        $dealerType = $recipe->dealer_profit_type ?? 'percentage';
        $dealerVal = (float)($recipe->dealer_profit_percent ?? 0);
        $dealerProfit = ($dealerType === 'fixed') ? $dealerVal : round($totalProductCost * ($dealerVal / 100), 2);
        $dealerPrice = round($totalProductCost + $dealerProfit, 2);

        // Special dealer pricing
        $specialType = $recipe->special_dealer_profit_type ?? 'percentage';
        $specialVal = (float)($recipe->special_dealer_profit_percent ?? 0);
        $specialProfit = ($specialType === 'fixed') ? $specialVal : round($totalProductCost * ($specialVal / 100), 2);
        $specialPrice = round($totalProductCost + $specialProfit, 2);

        // Normal customer pricing
        $customerType = $recipe->normal_customer_profit_type ?? 'percentage';
        $customerVal = (float)($recipe->normal_customer_profit_percent ?? 0);
        $customerProfit = ($customerType === 'fixed') ? $customerVal : round($totalProductCost * ($customerVal / 100), 2);
        $customerPrice = round($totalProductCost + $customerProfit, 2);

        $pricingSummary = [
            'material_cost' => $totalMaterialCost,
            'charges_total' => $totalCharges,
            'product_cost' => $totalProductCost,
            'dealer_profit_type' => $dealerType,
            'dealer_profit_val' => $dealerVal,
            'dealer_profit_amount' => $dealerProfit,
            'dealer_price' => $dealerPrice,
            'special_profit_type' => $specialType,
            'special_profit_val' => $specialVal,
            'special_profit_amount' => $specialProfit,
            'special_dealer_price' => $specialPrice,
            'customer_profit_type' => $customerType,
            'customer_profit_val' => $customerVal,
            'customer_profit_amount' => $customerProfit,
            'customer_price' => $customerPrice,
            'items_data' => $recipeItemsData,
        ];

        return view('admin.product-recipe.show', compact('recipe', 'pricingSummary'));
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

    public function destroy($id, SoftDeleteService $softDeleteService)
    {
        return $this->softDeleteRecord($id, $softDeleteService);
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
