<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryPurchasePayment extends Model
{
    use HasFactory, TracksSoftDeletion;

    protected $fillable = [
        'inventory_purchase_id',
        'vendor_id',
        'amount',
        'payment_date',
        'payment_method',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function inventoryPurchase()
    {
        return $this->belongsTo(InventoryPurchase::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
