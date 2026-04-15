<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_order_id', 'fabric_price_id', 'fabric_name',
        'type', 'sleeve', 'unit_price', 'quantity', 'total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total'      => 'decimal:2',
    ];

    public function customOrder()
    {
        return $this->belongsTo(CustomOrder::class);
    }

    public function fabricPrice()
    {
        return $this->belongsTo(FabricPrice::class);
    }
}
