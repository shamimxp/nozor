<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerOrderItem extends Model
{
    use HasFactory, TracksSoftDeletion;

    protected $fillable = [
        'dealer_order_id', 'product_id', 'qty', 'price', 'total', 'confirm_qty', 'note'
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
