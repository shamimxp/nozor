<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductPriceCalculator;


class ProductPriceController extends Controller
{
     public function calculate(Request $request, ProductPriceCalculator $calculator)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'dealer_id'  => 'required|integer|exists:dealers,id',
        ]);

        $result = $calculator->calculate(
            $validated['product_id'],
            $validated['dealer_id']
        );

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }
}
