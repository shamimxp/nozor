<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebOrderItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'web_order_id',
        'product_id',
        'quantity',
        'price',
        'color',
        'size',
    ];

    public function order()
    {
        return $this->belongsTo(WebOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
