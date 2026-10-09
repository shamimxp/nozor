<?php

namespace App\Services;

use App\Models\ProductRecipe;
use App\Models\Dealer;
use App\Models\WebSetting;

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
            'pitol_price'      => $recipe->pitol_price ?? 0,
        ];
        $chargesTotal = array_sum($charges);

        // 3. Total cost price
        $totalCostPrice = round($materialCost + $chargesTotal, 2);

        // 4–5. Dealer and retail prices use the same formula as a single charge.
        $priced = $this->pricesFromCost($totalCostPrice, $dealer, $recipe);
        $profitPercent = $priced['profit_percent'];
        $profitAmount = $priced['profit_amount'];
        $dealerPrice = $priced['dealer_price'];
        $retailPrice = $priced['retail_price'];

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

    /**
     * Regular-sale prices from a cost amount.
     * Manufacture body and finishing prices use Body Charge and Finishing Charge here.
     */
    public function pricesFromCost(float $cost, ?Dealer $dealer = null, ?ProductRecipe $recipe = null): array
    {
        $settings = $this->profitSettings();
        if ($dealer && $dealer->is_special) {
            $profitPercent = ($recipe && $recipe->special_dealer_profit_percent > 0)
                ? (float) $recipe->special_dealer_profit_percent
                : $settings['special'];
        } else {
            $profitPercent = ($recipe && $recipe->dealer_profit_percent > 0)
                ? (float) $recipe->dealer_profit_percent
                : $settings['regular'];
        }

        $retailPercent = ($recipe && $recipe->normal_customer_profit_percent > 0)
            ? (float) $recipe->normal_customer_profit_percent
            : null;

        return $this->applyProfit($cost, $profitPercent, $retailPercent);
    }

    /**
     * Cost, both dealer prices, and the regular retail price for a recipe product.
     */
    public function quote(int $productId): array
    {
        $recipe = ProductRecipe::where('product_id', $productId)->first();
        $result = $this->calculate($productId, null);
        $settings = $this->profitSettings();

        $regularDealerPercent = ($recipe && $recipe->dealer_profit_percent > 0)
            ? (float) $recipe->dealer_profit_percent
            : $settings['regular'];

        $specialDealerPercent = ($recipe && $recipe->special_dealer_profit_percent > 0)
            ? (float) $recipe->special_dealer_profit_percent
            : $settings['special'];

        $retailPercent = ($recipe && $recipe->normal_customer_profit_percent > 0)
            ? (float) $recipe->normal_customer_profit_percent
            : null;

        $special = $this->applyProfit((float) $result['total_cost_price'], $specialDealerPercent, $retailPercent);

        $result['regular_dealer_percent'] = $regularDealerPercent;
        $result['regular_dealer_price'] = round($result['dealer_price']);
        $result['special_dealer_percent'] = $specialDealerPercent;
        $result['special_dealer_price'] = round($special['dealer_price']);
        $result['regular_price'] = round($result['retail_price']);

        return $result;
    }

    public function profitSettings(): array
    {
        $setting = WebSetting::first();

        return [
            'regular' => ($setting && $setting->dealer_profit_percent !== null)
                ? (float) $setting->dealer_profit_percent
                : (float) self::NORMAL_DEALER_PROFIT_PERCENT,
            'special' => ($setting && $setting->special_dealer_profit_percent !== null)
                ? (float) $setting->special_dealer_profit_percent
                : (float) self::SPECIAL_DEALER_PROFIT_PERCENT,
        ];
    }

    private function applyProfit(float $cost, float $profitPercent, ?float $retailProfitPercent = null): array
    {
        $profitAmount = round($cost * ($profitPercent / 100), 2);
        $dealerPrice = round($cost + $profitAmount, 2);
        if ($retailProfitPercent !== null && $retailProfitPercent > 0) {
            $retailPrice = round($cost + ($cost * ($retailProfitPercent / 100)), 2);
        } else {
            $retailPrice = round(($dealerPrice * (self::RETAIL_MULTIPLIER / 100)) + $dealerPrice, 2);
        }

        return [
            'profit_percent' => $profitPercent,
            'profit_amount' => $profitAmount,
            'dealer_price' => $dealerPrice,
            'retail_price' => $retailPrice,
        ];
    }
}
