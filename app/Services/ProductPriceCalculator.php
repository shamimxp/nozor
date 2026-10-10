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

        // 2. Fixed charges (dynamically loaded from linked CostSetting if available, otherwise legacy fields)
        if ($recipe->cost_setting_id && $recipe->relationLoaded('costSetting') === false) {
            $recipe->load('costSetting.items');
        }

        if ($recipe->costSetting && $recipe->costSetting->items->isNotEmpty()) {
            $charges = [];
            foreach ($recipe->costSetting->items as $csItem) {
                $charges[] = (float) $csItem->cost_price;
            }
            $chargesTotal = array_sum($charges);
        } else {
            $charges = [
                'body_charge'      => (float) $recipe->body_charge,
                'finishing_charge' => (float) $recipe->finishing_charge,
                'bearing'          => (float) $recipe->bearing,
                'stone'            => (float) $recipe->stone,
                'electric_bill'    => (float) $recipe->electric_bill,
                'gas_bill'         => (float) $recipe->gas_bill,
                'box_price'        => (float) $recipe->box_price,
                'carrying_charge'  => (float) $recipe->carrying_charge,
                'wire_price'       => (float) $recipe->wire_price,
                'pitol_price'      => (float) ($recipe->pitol_price ?? 0),
            ];
            $chargesTotal = array_sum($charges);
        }

        // 3. Total cost price
        $totalCostPrice = round($materialCost + $chargesTotal, 2);

        // 4–5. Dealer and retail prices use the profit calculation (percentage or fixed)
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
            'total_cost_price'  => round($totalCostPrice),
            'profit_percent'    => round($profitPercent),
            'profit_amount'     => round($profitAmount),
            'dealer_price'      => round($dealerPrice),
            'retail_price'      => round($retailPrice),
        ];
    }

    /**
     * Regular-sale prices from a cost amount.
     */
    public function pricesFromCost(float $cost, ?Dealer $dealer = null, ?ProductRecipe $recipe = null): array
    {
        $settings = $this->profitSettings();
        if ($dealer && $dealer->is_special) {
            $profitVal = ($recipe && $recipe->special_dealer_profit_percent > 0)
                ? (float) $recipe->special_dealer_profit_percent
                : $settings['special'];
            $profitType = $recipe ? ($recipe->special_dealer_profit_type ?? 'percentage') : 'percentage';
        } else {
            $profitVal = ($recipe && $recipe->dealer_profit_percent > 0)
                ? (float) $recipe->dealer_profit_percent
                : $settings['regular'];
            $profitType = $recipe ? ($recipe->dealer_profit_type ?? 'percentage') : 'percentage';
        }

        $retailVal = ($recipe && $recipe->normal_customer_profit_percent > 0)
            ? (float) $recipe->normal_customer_profit_percent
            : null;
        $retailType = $recipe ? ($recipe->normal_customer_profit_type ?? 'percentage') : 'percentage';

        return $this->applyProfit($cost, $profitVal, $profitType, $retailVal, $retailType);
    }

    /**
     * Cost, both dealer prices, and the regular retail price for a recipe product.
     */
    public function quote(int $productId): array
    {
        $recipe = ProductRecipe::with('costSetting.items')->where('product_id', $productId)->first();
        $result = $this->calculate($productId, null);
        $settings = $this->profitSettings();

        $regularDealerVal = ($recipe && $recipe->dealer_profit_percent > 0)
            ? (float) $recipe->dealer_profit_percent
            : $settings['regular'];
        $regularDealerType = $recipe ? ($recipe->dealer_profit_type ?? 'percentage') : 'percentage';

        $specialDealerVal = ($recipe && $recipe->special_dealer_profit_percent > 0)
            ? (float) $recipe->special_dealer_profit_percent
            : $settings['special'];
        $specialDealerType = $recipe ? ($recipe->special_dealer_profit_type ?? 'percentage') : 'percentage';

        $retailVal = ($recipe && $recipe->normal_customer_profit_percent > 0)
            ? (float) $recipe->normal_customer_profit_percent
            : null;
        $retailType = $recipe ? ($recipe->normal_customer_profit_type ?? 'percentage') : 'percentage';

        $special = $this->applyProfit((float) $result['total_cost_price'], $specialDealerVal, $specialDealerType, $retailVal, $retailType);

        $result['regular_dealer_percent'] = $regularDealerVal;
        $result['regular_dealer_type'] = $regularDealerType;
        $result['regular_dealer_price'] = round($result['dealer_price']);
        $result['special_dealer_percent'] = $specialDealerVal;
        $result['special_dealer_type'] = $specialDealerType;
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

    private function applyProfit(float $cost, float $profitValue, string $profitType = 'percentage', ?float $retailProfitValue = null, string $retailProfitType = 'percentage'): array
    {
        // Dealer profit amount
        if ($profitType === 'fixed') {
            $profitAmount = round($profitValue, 2);
            $dealerPrice = round($cost + $profitAmount, 2);
        } else {
            $profitAmount = round($cost * ($profitValue / 100), 2);
            $dealerPrice = round($cost + $profitAmount, 2);
        }

        // Retail / Normal Customer price
        if ($retailProfitValue !== null && $retailProfitValue > 0) {
            if ($retailProfitType === 'fixed') {
                $retailPrice = round($cost + $retailProfitValue, 2);
            } else {
                $retailPrice = round($cost + ($cost * ($retailProfitValue / 100)), 2);
            }
        } else {
            $retailPrice = round(($dealerPrice * (self::RETAIL_MULTIPLIER / 100)) + $dealerPrice, 2);
        }

        return [
            'profit_percent' => $profitValue,
            'profit_type'    => $profitType,
            'profit_amount'  => $profitAmount,
            'dealer_price'   => $dealerPrice,
            'retail_price'   => $retailPrice,
        ];
    }
}
