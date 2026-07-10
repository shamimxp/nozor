<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'order_date', 'dealer_id',
        'sub_total', 'discount', 'carrying_charge', 'grand_total',
        'paid', 'due', 'note', 'admin_notes', 'status',
    ];

    protected $casts = [
        'order_date'      => 'date',
        'sub_total'       => 'decimal:2',
        'discount'        => 'decimal:2',
        'carrying_charge' => 'decimal:2',
        'grand_total'     => 'decimal:2',
        'paid'            => 'decimal:2',
        'due'             => 'decimal:2',
    ];

    /**
     * Generate next order number like D-ORD-2026-0001
     */
    public static function generateOrderNumber(): string
    {
        $year   = date('Y');
        $last   = self::whereYear('order_date', $year)->latest('id')->first();
        $nextId = $last ? ($last->id + 1) : 1;
        return 'D-ORD-' . $year . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    public function items()
    {
        return $this->hasMany(DealerOrderItem::class);
    }
}
