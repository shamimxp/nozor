<?php

namespace App\Services;

use App\Models\ProductRecipe;
use App\Models\Dealer;
use Illuminate\Support\Facades\DB;

class ProductPriceCalculator
{
    const NORMAL_DEALER_PROFIT_PERCENT  = 15;
    const SPECIAL_DEALER_PROFIT_PERCENT = 25;
    const RETAIL_MULTIPLIER             = 42.85714;

    public function calculate(int $productId, ?int $dealerId = null): array
    {
        $recipe = ProductRecipe::with('items.rawMaterialProduct')
            ->where('product_id', $productId)
            ->firstOrFail();

        $dealer = $dealerId ? Dealer::find($dealerId) : null;

        // 1. Material cost (recomputed live from current material price/grade)
        $itemBreakdown = [];
        $materialCost = 0;

        foreach ($recipe->items as $item) {
            $material = $item->rawMaterialProduct;


            $qty  = round($item->area * $material->grade_value, 2);
            $cost = round($qty * $material->price_per_unit, 2);

            $materialCost += round($cost);

            $itemBreakdown[] = [
                'material'   => $material->name,
                'area'       => $item->area,
                'grade_value'=> $material->grade_value,
                'qty'        => round($qty),
                'unit_price' => $material->price_per_unit,
                'cost'       => round($cost),
            ];
        }

        // 2. Fixed charges
        $charges = [
            'body_charge'      => $recipe->body_charge,
            'finishing_charge' => $recipe->finishing_charge,
            'bearing'          => $recipe->bearing,
            'stone'            => $recipe->stone,
            'electric_bill'    => $recipe->electric_bill,
            'gas_bill'         => $recipe->gas_bill,
            'box_price'        => $recipe->box_price,
            'carrying_charge'  => $recipe->carrying_charge,
            'wire_price'       => $recipe->wire_price,
        ];
        $chargesTotal = array_sum($charges);

        // 3. Total cost price
        $totalCostPrice = round($materialCost + $chargesTotal, 2);

        // 4. Dealer price (profit % depends on dealer type)
        $profitPercent = ($dealer && $dealer->is_special)
            ? self::SPECIAL_DEALER_PROFIT_PERCENT
            : self::NORMAL_DEALER_PROFIT_PERCENT;

        $profitAmount = round($totalCostPrice * ($profitPercent / 100), 2);
        $dealerPrice  = round($totalCostPrice + $profitAmount, 2);

        // 5. Retail price = dealer_price * 42.85714% + dealer_price
        $retailPrice = round(($dealerPrice * (self::RETAIL_MULTIPLIER / 100)) + $dealerPrice, 2);

        return [
            'product_id'        => $productId,
            'dealer_id'         => $dealerId,
            'dealer_type'       => ($dealer && $dealer->is_special) ? 'special' : 'normal',
            'items'             => $itemBreakdown,
//            'material_cost'     => round($materialCost, 2),
//            'charges'           => $charges,
//            'charges_total'     => round($chargesTotal, 2),
            'total_cost_price'  => round($totalCostPrice),
            'profit_percent'    => round($profitPercent),
            'profit_amount'     => round($profitAmount),
            'dealer_price'      => round($dealerPrice),
            'retail_price'      => round($retailPrice),
        ];
    }
}
