<?php

namespace App\Observers;

use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

class ActivityObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        try {
            $customDesc = $this->buildDescription('created', $model);
            ActivityLogger::logModelEvent('created', $model, $customDesc);
        } catch (\Throwable $e) {
            // Fail-safe: logging failure must never interrupt user flow
        }
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        try {
            $customDesc = $this->buildDescription('updated', $model);
            ActivityLogger::logModelEvent('updated', $model, $customDesc);
        } catch (\Throwable $e) {
            // Fail-safe
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        try {
            $customDesc = $this->buildDescription('deleted', $model);
            ActivityLogger::logModelEvent('deleted', $model, $customDesc);
        } catch (\Throwable $e) {
            // Fail-safe
        }
    }

    /**
     * Build rich human-readable descriptions for primary transactional models
     */
    protected function buildDescription(string $event, Model $model): ?string
    {
        $class = class_basename($model);

        switch ($class) {
            case 'WebOrder':
                $orderNo = $model->invoice_no ?? $model->order_number ?? $model->order_no ?? '#' . $model->id;
                if ($event === 'created') {
                    $amount = number_format($model->total ?? $model->grand_total ?? 0, 2);
                    $payment = $model->payment_method ?? 'COD';
                    return "Web Customer placed Order {$orderNo} (Amount: ৳{$amount}, Payment: {$payment})";
                }
                if ($event === 'updated' && $model->wasChanged(['status', 'order_status'])) {
                    $from = $model->getOriginal('status') ?? $model->getOriginal('order_status');
                    $to = $model->status ?? $model->order_status;
                    return "Web Order {$orderNo} status changed from '{$from}' to '{$to}'";
                }
                break;

            case 'WebOrderAddress':
                if ($event === 'created') {
                    return "Delivery address saved for Order #{$model->web_order_id} ({$model->phone}, {$model->address})";
                }
                break;

            case 'Cart':
                $productName = $model->product->name ?? 'Product #' . ($model->product_id ?? '');
                if ($event === 'created') {
                    return "Added '{$productName}' to Cart (Qty: {$model->quantity})";
                }
                if ($event === 'updated' && $model->wasChanged('quantity')) {
                    return "Updated Cart item '{$productName}' quantity to {$model->quantity}";
                }
                if ($event === 'deleted') {
                    return "Removed '{$productName}' from Cart";
                }
                break;

            case 'Wishlist':
                $productName = $model->product->name ?? 'Product #' . ($model->product_id ?? '');
                if ($event === 'created') {
                    return "Added '{$productName}' to Wishlist";
                }
                if ($event === 'deleted') {
                    return "Removed '{$productName}' from Wishlist";
                }
                break;

            case 'User':
                if ($event === 'created') {
                    return "New Customer account registered: {$model->name} ({$model->phone})";
                }
                if ($event === 'updated') {
                    return "Customer profile updated: {$model->name}";
                }
                break;

            case 'CustomerAddress':
                if ($event === 'created') {
                    return "Added delivery address: {$model->address}";
                }
                if ($event === 'updated') {
                    return "Updated delivery address: {$model->address}";
                }
                if ($event === 'deleted') {
                    return "Deleted delivery address: {$model->address}";
                }
                break;

            case 'ProductReview':
                $productName = $model->product->name ?? 'Product #' . ($model->product_id ?? '');
                if ($event === 'created') {
                    $rating = $model->rating ?? 5;
                    return "Submitted a {$rating}-star review on '{$productName}'";
                }
                if ($event === 'updated') {
                    if ($model->wasChanged('status')) {
                        return "Review on '{$productName}' status updated to '{$model->status}'";
                    }
                    if ($model->wasChanged('reply')) {
                        return "Replied to review on '{$productName}'";
                    }
                }
                break;

            case 'ContactMessage':
                if ($event === 'created') {
                    return "Submitted contact inquiry from {$model->name} ({$model->email})";
                }
                if ($event === 'deleted') {
                    return "Deleted contact message #{$model->id}";
                }
                break;

            case 'Subscriber':
                if ($event === 'created') {
                    return "New newsletter subscriber: {$model->email}";
                }
                if ($event === 'deleted') {
                    return "Removed subscriber: {$model->email}";
                }
                break;

            case 'Coupon':
                if ($event === 'created') {
                    return "Created discount coupon '{$model->code}' ({$model->amount} {$model->type})";
                }
                if ($event === 'updated') {
                    if ($model->wasChanged('status')) {
                        $st = $model->status ? 'Active' : 'Inactive';
                        return "Coupon '{$model->code}' status changed to {$st}";
                    }
                    return "Updated coupon '{$model->code}'";
                }
                if ($event === 'deleted') {
                    return "Deleted coupon '{$model->code}'";
                }
                break;

            case 'Dealer':
                if ($event === 'created') {
                    return "New Dealer registered: {$model->name} ({$model->phone})";
                }
                if ($event === 'updated') {
                    return "Dealer profile updated: {$model->name}";
                }
                break;

            case 'DealerOrder':
                $orderNo = $model->order_no ?? '#' . $model->id;
                if ($event === 'created') {
                    $amount = number_format($model->grand_total ?? 0, 2);
                    return "Dealer placed Order {$orderNo} (Amount: ৳{$amount})";
                }
                if ($event === 'updated' && $model->wasChanged(['order_status', 'status'])) {
                    $from = $model->getOriginal('order_status') ?? $model->getOriginal('status');
                    $to = $model->order_status ?? $model->status;
                    return "Dealer Order {$orderNo} status changed: '{$from}' → '{$to}'";
                }
                break;

            case 'DealerOrderPayment':
                if ($event === 'created') {
                    $amount = number_format($model->amount ?? 0, 2);
                    $method = $model->payment_method ?? 'Cash';
                    return "Dealer payment recorded: ৳{$amount} via {$method} for Order #{$model->dealer_order_id}";
                }
                break;

            case 'DealerWalletTransaction':
                if ($event === 'created') {
                    $amount = number_format($model->amount ?? 0, 2);
                    return "Dealer wallet transaction: {$model->type} ৳{$amount} ({$model->purpose})";
                }
                break;

            case 'DraftOrder':
                if ($event === 'created') {
                    return "Draft Order #{$model->id} created";
                }
                if ($event === 'updated') {
                    return "Draft Order #{$model->id} updated";
                }
                break;

            case 'OrderRequest':
                if ($event === 'created') {
                    return "Order request submitted #{$model->id} by {$model->name}";
                }
                if ($event === 'updated') {
                    return "Order request #{$model->id} status updated to '{$model->status}'";
                }
                break;

            case 'PosOrder':
                $orderNo = $model->order_no ?? '#' . $model->id;
                if ($event === 'created') {
                    $amount = number_format($model->grand_total ?? $model->total ?? 0, 2);
                    return "POS sale completed: Order {$orderNo} (Amount: ৳{$amount})";
                }
                if ($event === 'updated') {
                    return "POS Order {$orderNo} updated";
                }
                break;

            case 'CustomOrder':
                $orderNo = $model->order_no ?? '#' . $model->id;
                if ($event === 'created') {
                    $amount = number_format($model->total ?? $model->grand_total ?? 0, 2);
                    return "Custom Order created: {$orderNo} (Amount: ৳{$amount})";
                }
                if ($event === 'updated' && $model->wasChanged(['status', 'order_status'])) {
                    $from = $model->getOriginal('status') ?? $model->getOriginal('order_status');
                    $to = $model->status ?? $model->order_status;
                    return "Custom Order {$orderNo} status changed: '{$from}' → '{$to}'";
                }
                break;

            case 'Product':
                if ($event === 'created') {
                    $price = number_format($model->selling_price ?? $model->price ?? 0, 2);
                    return "Created new product: '{$model->name}' (Price: ৳{$price})";
                }
                if ($event === 'updated') {
                    if ($model->wasChanged('status')) {
                        $st = $model->status ? 'Active' : 'Inactive';
                        return "Product '{$model->name}' status changed to {$st}";
                    }
                    if ($model->wasChanged(['selling_price', 'price'])) {
                        $oldP = $model->getOriginal('selling_price') ?? $model->getOriginal('price');
                        $newP = $model->selling_price ?? $model->price;
                        return "Product '{$model->name}' price changed: ৳{$oldP} → ৳{$newP}";
                    }
                    return "Updated product details: '{$model->name}'";
                }
                if ($event === 'deleted') {
                    return "Deleted product: '{$model->name}'";
                }
                break;

            case 'Category':
                if ($event === 'created') return "Created category: '{$model->name}'";
                if ($event === 'updated') return "Updated category: '{$model->name}'";
                if ($event === 'deleted') return "Deleted category: '{$model->name}'";
                break;

            case 'SubCategory':
                if ($event === 'created') return "Created sub-category: '{$model->name}'";
                if ($event === 'updated') return "Updated sub-category: '{$model->name}'";
                if ($event === 'deleted') return "Deleted sub-category: '{$model->name}'";
                break;

            case 'StockAdjustment':
                if ($event === 'created') {
                    return "Created Stock Adjustment #{$model->id} ({$model->type}, Qty: {$model->quantity})";
                }
                break;

            case 'Collection':
                $colNo = $model->collection_no ?? '#' . $model->id;
                if ($event === 'created') {
                    $amt = number_format($model->amount ?? 0, 2);
                    $method = $model->payment_method ?? 'Cash';
                    return "Payment Collection {$colNo} recorded (৳{$amt} via {$method})";
                }
                if ($event === 'updated' && $model->wasChanged('status')) {
                    $from = $model->getOriginal('status');
                    $to = $model->status;
                    return "Collection {$colNo} status updated: '{$from}' → '{$to}'";
                }
                break;

            case 'CustomerPayment':
                if ($event === 'created') {
                    $amt = number_format($model->amount ?? 0, 2);
                    return "Customer payment received: ৳{$amt}";
                }
                break;

            case 'VendorPayment':
                if ($event === 'created') {
                    $amt = number_format($model->amount ?? 0, 2);
                    return "Vendor payment recorded: ৳{$amt}";
                }
                break;

            case 'Purchase':
            case 'InventoryPurchase':
                $orderNo = $model->invoice_no ?? $model->order_no ?? '#' . $model->id;
                if ($event === 'created') {
                    $amt = number_format($model->grand_total ?? $model->total ?? 0, 2);
                    return "Purchase order recorded {$orderNo} (Amount: ৳{$amt})";
                }
                if ($event === 'updated') {
                    return "Purchase order {$orderNo} updated";
                }
                break;

            case 'Manufacture':
                if ($event === 'created') return "Manufacture order started: #{$model->id}";
                if ($event === 'updated') return "Manufacture order #{$model->id} updated";
                break;

            case 'WorkerPayment':
                if ($event === 'created') {
                    $amt = number_format($model->amount ?? 0, 2);
                    return "Worker payment recorded: ৳{$amt}";
                }
                break;

            case 'Banner':
                if ($event === 'created') return "Added promotional banner: '{$model->title}'";
                if ($event === 'updated') return "Updated promotional banner: '{$model->title}'";
                if ($event === 'deleted') return "Deleted promotional banner: '{$model->title}'";
                break;

            case 'Customer':
                if ($event === 'created') return "New Customer profile created: {$model->name} ({$model->phone})";
                if ($event === 'updated') return "Customer profile updated: {$model->name}";
                break;

            case 'Vendor':
                if ($event === 'created') return "New Vendor added: {$model->name} ({$model->phone})";
                if ($event === 'updated') return "Vendor profile updated: {$model->name}";
                break;
        }

        return null;
    }
}
