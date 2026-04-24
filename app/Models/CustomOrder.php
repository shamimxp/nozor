<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'style_number', 'order_date', 'customer_id', 'type', 'sleeve',
        'customer_note', 'vendor_note', 'delivery_date', 'delivered_date', 'collected_date',
        'total_quantity', 'sub_total', 'carrying_charge', 'grand_total',
        'order_type', 'paid', 'due', 'vendor_id', 'status',
    ];

    protected $casts = [
        'order_date'     => 'date',
        'delivery_date'  => 'date',
        'delivered_date' => 'date',
        'collected_date' => 'date',
        'sub_total'      => 'decimal:2',
        'carrying_charge'=> 'decimal:2',
        'grand_total'    => 'decimal:2',
        'paid'           => 'decimal:2',
        'due'            => 'decimal:2',
    ];

    // Status Constants
    const STATUS_PENDING        = 'pending';
    const STATUS_PURCHASE_ORDER = 'purchase_order';
    const STATUS_ORDER_CONFIRM  = 'order_confirm';
    const STATUS_RECEIVED       = 'received';
    const STATUS_DELIVERED      = 'delivered';
    const STATUS_CANCELLED      = 'cancelled';

    /**
     * Generate next order number like ORD-2026-0001
     */
    public static function generateOrderNumber(): string
    {
        $year   = date('Y');
        $last   = self::whereYear('order_date', $year)->latest('id')->first();
        $nextId = $last ? ($last->id + 1) : 1;
        return 'ORD-' . $year . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate next style number like STY-0001
     */
    public static function generateStyleNumber(): string
    {
        $last   = self::latest('id')->first();
        $nextId = $last ? ($last->id + 1) : 1;
        return 'STY-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(CustomOrderItem::class);
    }

    public function images()
    {
        return $this->hasMany(CustomOrderImage::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments()
    {
        return $this->morphMany(CustomerPayment::class, 'payable');
    }
}
