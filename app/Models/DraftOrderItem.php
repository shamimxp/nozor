<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DraftOrderItem extends Model
{
    use HasFactory, TracksSoftDeletion;

    protected $fillable = [
        'draft_order_id',
        'product_id',
        'qty',
        'unit_price',
        'total',
        'note',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total'      => 'decimal:2',
        'qty'        => 'integer',
    ];

    public function draftOrder()
    {
        return $this->belongsTo(DraftOrder::class, 'draft_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
