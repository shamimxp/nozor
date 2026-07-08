<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'dealer_order_id', 'product_id', 'qty', 'price', 'total'
    ];

    public function dealerOrder()
    {
        return $this->belongsTo(DealerOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
