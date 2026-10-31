<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrderItem extends Model
{
    use HasFactory;

    protected $table = 'return_items';

    protected $guarded = ['id'];

    protected $casts = [
        'ordered_qty'             => 'float',
        'previously_returned_qty' => 'float',
        'return_qty'              => 'float',
        'unit_price'              => 'float',
        'subtotal'                => 'float',
        'restock'                 => 'boolean',
    ];

    public function returnOrder()
    {
        return $this->belongsTo(ReturnOrder::class, 'return_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Polymorphic or direct relation to the source order
     */
    public function getOrderAttribute()
    {
        return match ($this->order_type) {
            'dealer' => DealerOrder::find($this->order_id),
            'pos'    => PosOrder::find($this->order_id),
            'web'    => WebOrder::find($this->order_id),
            default  => null,
        };
    }
}
