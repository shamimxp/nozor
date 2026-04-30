<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryPurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_purchase_id', 'product_id', 'quantity', 
        'purchase_price', 'received_quantity'
    ];

    public function purchase()
    {
        return $this->belongsTo(InventoryPurchase::class, 'inventory_purchase_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
