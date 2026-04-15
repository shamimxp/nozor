<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'style_number', 'order_date', 'customer_id', 'type', 'sleeve',
        'customer_note', 'vendor_note', 'delivery_date', 'collected_date',
        'total_quantity', 'sub_total', 'carrying_charge', 'grand_total',
        'order_type', 'paid', 'due', 'vendor_id', 'status',
    ];

    protected $casts = [
        'order_date'     => 'date',
        'delivery_date'  => 'date',
        'collected_date' => 'date',
        'sub_total'      => 'decimal:2',
        'carrying_charge'=> 'decimal:2',
        'grand_total'    => 'decimal:2',
        'paid'           => 'decimal:2',
        'due'            => 'decimal:2',
    ];

    /**
     * Generate next style number like STY-0001, STY-0002 ...
     */
    public static function generateStyleNumber(): string
    {
        $last = self::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;
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
}
