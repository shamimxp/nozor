<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Vendor Model
 *
 * Represents a vendor in the system.
 */
class Vendor extends Model
{
    use HasFactory, TracksSoftDeletion;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'company_name',
        'phone',
        'email',
        'opening_balance',
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments()
    {
        return $this->hasMany(VendorPayment::class);
    }

    public function inventoryPurchases()
    {
        return $this->hasMany(InventoryPurchase::class);
    }

    public function customOrders()
    {
        return $this->hasMany(CustomOrder::class);
    }
}
