<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor;
use App\Models\Admin\Admin;

class InventoryPurchase extends Model
{
    use HasFactory;

    protected $guarded= [
        'id'
    ];

    protected $casts = [
        'purchase_date' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryPurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(InventoryPurchasePayment::class);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public static function generatePurchaseNumber()
    {
        $latest = self::latest()->first();
        if (!$latest) {
            return 'STK-PUR-1001';
        }
        $number = intval(str_replace('STK-PUR-', '', $latest->purchase_number));
        return 'STK-PUR-' . ($number + 1);
    }
}
