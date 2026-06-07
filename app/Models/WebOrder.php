<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'invoice_no',
        'user_id',
        'subtotal',
        'discount',
        'shipping_charge',
        'total',
        'payment_method',
        'status',
    ];

    public function items()
    {
        return $this->hasMany(WebOrderItem::class);
    }

    public function address()
    {
        return $this->hasOne(WebOrderAddress::class);
    }
}
