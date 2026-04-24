<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'customer_id', 'total_amount', 'discount_amount',
        'payable_amount', 'paid_amount', 'due_amount', 'payment_method',
        'payment_status', 'order_status', 'note', 'staff_note', 'created_by', 'order_date'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\Admin\Admin::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PosOrderItem::class);
    }

    public static function generateOrderNumber()
    {
        $latest = self::latest()->first();
        if (!$latest) {
            return 'ORD-1001';
        }
        $number = intval(str_replace('ORD-', '', $latest->order_number));
        return 'ORD-' . ($number + 1);
    }
    public function payments()
    {
        return $this->morphMany(CustomerPayment::class, 'payable');
    }
}
