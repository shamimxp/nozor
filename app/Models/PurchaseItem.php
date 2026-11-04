<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory, TracksSoftDeletion;

    protected $fillable = [
        'purchase_id', 'fabric_price_id', 'quantity', 'unit_cost', 'subtotal'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function fabricPrice()
    {
        return $this->belongsTo(FabricPrice::class);
    }
}
