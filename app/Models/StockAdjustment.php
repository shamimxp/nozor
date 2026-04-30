<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Admin;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'quantity', 'type', 'adjustment_date', 'reason', 'is_received', 'received_at', 'received_by', 'created_by'
    ];

    protected $casts = [
        'adjustment_date' => 'date',
        'is_received' => 'boolean',
        'received_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
