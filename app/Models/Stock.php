<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'stocks';

    protected $fillable = [
        'product_id',
        'quantity',
        'type',              // 'in' or 'out'
        'purchase_id',
        'stock_type',        // 'production', 'purchase', 'adjustment', 'order'
        'before_stock',
        'after_stock',
        'source_type',       // 'purchase', 'manufacture_receive', 'dealer_order', 'pos_order', 'web_order', 'stock_adjustment'
        'source_id',
        'reference_no',
        'note',
        'created_by'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'before_stock' => 'decimal:2',
        'after_stock' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
