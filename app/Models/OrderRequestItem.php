<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_request_id',
        'product_id',
        'requested_qty',
        'confirmed_qty',
    ];

    public function orderRequest()
    {
        return $this->belongsTo(OrderRequest::class);
    }

    public function product()
    {
        // Assuming there is a Product model
        return $this->belongsTo(Product::class);
    }

    // Dynamic accessor for pending_qty
    public function getPendingQtyAttribute()
    {
        return $this->requested_qty - $this->confirmed_qty;
    }
}
