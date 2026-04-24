<?php

namespace App\Models;

use App\Models\Admin\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_number', 'custom_order_id', 'style_number', 'vendor_id',
        'sub_total', 'carrying_charge', 'grand_total', 'paid_amount', 'due_amount',
        'status', 'received_date', 'created_by'
    ];

    protected $casts = [
        'received_date' => 'date',
        'sub_total' => 'decimal:2',
        'carrying_charge' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public function customOrder()
    {
        return $this->belongsTo(CustomOrder::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(VendorPayment::class);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public static function generatePurchaseNumber()
    {
        $latest = self::latest()->first();
        if (!$latest) {
            return 'PUR-1001';
        }
        $number = intval(str_replace('PUR-', '', $latest->purchase_number));
        return 'PUR-' . ($number + 1);
    }
}
