<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    /**
     * Check if a product has sufficient available stock for an order.
     * Manufacture products always return true because they are made-to-order.
     */
    public function canFulfill(int $productId, float $quantity): bool
    {
        $product = Product::find($productId);
        if (!$product) {
            return false;
        }

        // Manufacture products are made-to-order: no stock constraint on order
        if ($product->is_manufacturer) {
            return true;
        }

        return (float) ($product->stock ?? 0) >= $quantity;
    }

    /**
     * Validate an array of items for stock availability.
     * Items format: [['product_id' => ..., 'qty' => ...], ...]
     * Returns array of errors if any non-manufacture product lacks sufficient stock.
     */
    public function validateAvailability(array $items): array
    {
        $errors = [];
        foreach ($items as $item) {
            $productId = $item['product_id'] ?? $item['id'] ?? null;
            $qty = (float) ($item['qty'] ?? $item['quantity'] ?? 0);

            if (!$productId || $qty <= 0) {
                continue;
            }

            $product = Product::find($productId);
            if (!$product) {
                $errors[] = "Product ID #{$productId} not found.";
                continue;
            }

            if (!$product->is_manufacturer) {
                $currentStock = (float) ($product->stock ?? 0);
                if ($currentStock < $qty) {
                    $errors[] = "Insufficient stock for '{$product->name}'. Available: {$currentStock}, Requested: {$qty}.";
                }
            }
        }

        return $errors;
    }

    /**
     * Record Stock IN (supply: purchase receive, manufacture receive, returns, positive adjustment).
     */
    public function recordIn(
        int $productId,
        float $quantity,
        string $sourceType,
        ?int $sourceId = null,
        ?string $reference = null,
        ?string $note = null,
        ?int $userId = null
    ): Stock {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException("Quantity must be greater than 0.");
        }

        return DB::transaction(function () use ($productId, $quantity, $sourceType, $sourceId, $reference, $note, $userId) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            $beforeStock = (float) ($product->stock ?? 0);
            $afterStock = $beforeStock + $quantity;

            $product->stock = $afterStock;
            $product->save();

            $stockType = match ($sourceType) {
                'manufacture_receive' => 'production',
                'purchase' => 'purchase',
                'stock_adjustment' => 'adjustment',
                default => 'other',
            };

            return Stock::create([
                'product_id'   => $product->id,
                'quantity'     => $quantity,
                'type'         => 'in',
                'purchase_id'  => $sourceType === 'purchase' ? $sourceId : null,
                'stock_type'   => $stockType,
                'before_stock' => $beforeStock,
                'after_stock'  => $afterStock,
                'source_type'  => $sourceType,
                'source_id'    => $sourceId,
                'reference_no' => $reference,
                'note'         => $note,
                'created_by'   => $userId ?? auth()->id(),
            ]);
        });
    }

    /**
     * Record Stock OUT (demand: dealer order confirm/delivery, POS sale, web order delivery, negative adjustment).
     */
    public function recordOut(
        int $productId,
        float $quantity,
        string $sourceType,
        ?int $sourceId = null,
        ?string $reference = null,
        ?string $note = null,
        ?int $userId = null,
        bool $enforceAvailability = true
    ): Stock {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException("Quantity must be greater than 0.");
        }

        return DB::transaction(function () use ($productId, $quantity, $sourceType, $sourceId, $reference, $note, $userId, $enforceAvailability) {
            $product = Product::lockForUpdate()->findOrFail($productId);

            $beforeStock = (float) ($product->stock ?? 0);

            // For non-manufacture products, enforce available stock if requested
            if ($enforceAvailability && !$product->is_manufacturer && $beforeStock < $quantity) {
                throw ValidationException::withMessages([
                    'stock' => "Insufficient stock for '{$product->name}'. Available: {$beforeStock}, Requested: {$quantity}."
                ]);
            }

            $afterStock = $beforeStock - $quantity;

            $product->stock = $afterStock;
            $product->save();

            return Stock::create([
                'product_id'   => $product->id,
                'quantity'     => $quantity,
                'type'         => 'out',
                'purchase_id'  => null,
                'stock_type'   => 'order',
                'before_stock' => $beforeStock,
                'after_stock'  => $afterStock,
                'source_type'  => $sourceType,
                'source_id'    => $sourceId,
                'reference_no' => $reference,
                'note'         => $note,
                'created_by'   => $userId ?? auth()->id(),
            ]);
        });
    }

    /**
     * Revert / Return Stock (e.g. order cancelled after previous stock deduction).
     */
    public function recordReversal(
        int $productId,
        float $quantity,
        string $sourceType,
        ?int $sourceId = null,
        ?string $reference = null,
        ?string $note = null,
        ?int $userId = null
    ): Stock {
        return $this->recordIn(
            $productId,
            $quantity,
            $sourceType,
            $sourceId,
            $reference,
            $note ?? "Order cancelled - stock returned",
            $userId
        );
    }
}
