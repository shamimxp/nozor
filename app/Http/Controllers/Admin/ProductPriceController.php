<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ProductPriceCalculator;


class ProductPriceController extends Controller
{
     public function calculate(Request $request, ProductPriceCalculator $calculator)
    {
        $request->product_id = 59;
        $request->dealer_id = 1;
//        $validated = $request->validate([
//            'product_id' => 'required|integer|exists:products,id',
//            'dealer_id'  => 'required|integer|exists:dealers,id',
//        ]);


        $result = $calculator->calculate(59,1
//            $validated['product_id'],
//            $validated['dealer_id']
        );

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }
}
