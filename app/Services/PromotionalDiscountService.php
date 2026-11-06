<?php

namespace App\Services;

use App\Models\ProductOffer;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PromotionalDiscountService
{
    /**
     * Active offers for a given date (default: today).
     */
    public function activeOffers(?Carbon $date = null): Collection
    {
        $date = ($date ?? now())->copy()->startOfDay();

        return ProductOffer::query()
            ->where('status', 1)
            ->whereDate('from_date', '<=', $date)
            ->whereDate('to_date', '>=', $date)
            ->orderByDesc('id')
            ->get([
                'id',
                'product_id',
                'from_date',
                'to_date',
                'from_qty',
                'to_qty',
                'offer_type',
                'discount_value',
                'status',
            ]);
    }

    /**
     * Compact payload for frontend (POS / dealer order pages).
     */
    public function activeOffersForFrontend(?Carbon $date = null): array
    {
        return $this->activeOffers($date)->map(function (ProductOffer $offer) {
            return [
                'id' => $offer->id,
                'product_id' => (int) $offer->product_id,
                'from_qty' => (int) $offer->from_qty,
                'to_qty' => (int) $offer->to_qty,
                'offer_type' => $offer->offer_type,
                'discount_value' => (float) $offer->discount_value,
                'from_date' => optional($offer->from_date)->format('Y-m-d'),
                'to_date' => optional($offer->to_date)->format('Y-m-d'),
            ];
        })->values()->all();
    }

    /**
     * Best matching offer for a product + qty on a date.
     * Preference: highest discount amount, then newest offer.
     */
    public function findOffer(int $productId, int $quantity, ?Carbon $date = null, ?float $unitPrice = null, ?Collection $offers = null): ?ProductOffer
    {
        $quantity = max(0, $quantity);
        if ($quantity < 1 || $productId < 1) {
            return null;
        }

        $offers = $offers ?? $this->activeOffers($date);

        $matches = $offers
            ->where('product_id', $productId)
            ->filter(function (ProductOffer $offer) use ($quantity) {
                return $quantity >= (int) $offer->from_qty && $quantity <= (int) $offer->to_qty;
            })
            ->values();

        if ($matches->isEmpty()) {
            return null;
        }

        if ($unitPrice === null) {
            return $matches->sortByDesc('id')->first();
        }

        return $matches
            ->sortByDesc(function (ProductOffer $offer) use ($unitPrice, $quantity) {
                return $this->lineDiscount($unitPrice, $quantity, $offer);
            })
            ->first();
    }

    /**
     * Discount amount for one line.
     * percent = % of (unit_price * qty)
     * fixed   = per-unit discount * qty
     */
    public function lineDiscount(float $unitPrice, int $quantity, ?ProductOffer $offer): float
    {
        if (!$offer || $quantity < 1 || $unitPrice < 0) {
            return 0.0;
        }

        $lineTotal = $unitPrice * $quantity;
        $value = (float) $offer->discount_value;

        if ($offer->offer_type === 'percent') {
            $discount = round($lineTotal * ($value / 100), 2);
        } else {
            $discount = round($value * $quantity, 2);
        }

        return max(0, min($discount, $lineTotal));
    }

    /**
     * Calculate promotional discount for a cart/order.
     *
     * @param  array<int, array{product_id:int|string, quantity?:int|string, qty?:int|string, unit_price?:float|string, price?:float|string}>  $items
     * @return array{total: float, lines: array<int, array>}
     */
    public function calculateForItems(array $items, ?Carbon $date = null): array
    {
        $offers = $this->activeOffers($date);
        $lines = [];
        $total = 0.0;

        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? $item['id'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? $item['qty'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? $item['price'] ?? 0);

            $offer = $this->findOffer($productId, $quantity, $date, $unitPrice, $offers);
            $discount = $this->lineDiscount($unitPrice, $quantity, $offer);
            $total += $discount;

            $lines[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'offer_id' => $offer?->id,
                'offer_type' => $offer?->offer_type,
                'discount_value' => $offer ? (float) $offer->discount_value : 0,
                'discount' => $discount,
            ];
        }

        return [
            'total' => round($total, 2),
            'lines' => $lines,
        ];
    }
}
