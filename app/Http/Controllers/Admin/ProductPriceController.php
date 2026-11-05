<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductPriceCalculator;


class ProductPriceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:product.list');
    }

    public function calculate(Request $request, ProductPriceCalculator $calculator)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'dealer_id'  => 'required|integer|exists:dealers,id',
        ]);

        $product = \App\Models\Product::with('recipe')->findOrFail($validated['product_id']);

        if ($product->is_manufacturer && $product->recipe) {
            $result = $calculator->calculate(
                $validated['product_id'],
                $validated['dealer_id']
            );
        } else {
            $dealer = \App\Models\Dealer::find($validated['dealer_id']);
            $dealerPrice = (float) ($product->selling_price ?? 0);
            $result = [
                'product_id'        => $product->id,
                'dealer_id'         => $dealer ? $dealer->id : null,
                'dealer_type'       => ($dealer && $dealer->is_special) ? 'special' : 'normal',
                'items'             => [],
                'material_cost'     => 0,
                'charges_total'     => 0,
                'total_cost_price'  => (float) ($product->cost_price ?? 0),
                'profit_percent'    => 0,
                'profit_amount'     => 0,
                'dealer_price'      => $dealerPrice,
                'retail_price'      => $dealerPrice,
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }
}
