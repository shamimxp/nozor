<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostSetting extends Model
{
    use HasFactory, TracksSoftDeletion;

    protected $table = 'cost_settings';

    protected $fillable = [
        'cost_title',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(CostSettingItem::class, 'cost_setting_id');
    }

    public function recipes()
    {
        return $this->hasMany(ProductRecipe::class, 'cost_setting_id');
    }

    public function manufactureOrderItems()
    {
        return $this->hasMany(ManufactureOrderItem::class, 'cost_setting_id');
    }

    public function getBodyChargeAttribute(): float
    {
        $item = $this->items->first(function ($it) {
            return $it->cost_head_id == 1 || (isset($it->costHead) && stripos($it->costHead->title, 'Body') !== false);
        });
        return $item ? (float) $item->cost_price : 0.0;
    }

    public function getFinishingChargeAttribute(): float
    {
        $item = $this->items->first(function ($it) {
            return $it->cost_head_id == 2 || (isset($it->costHead) && stripos($it->costHead->title, 'Finishing') !== false);
        });
        return $item ? (float) $item->cost_price : 0.0;
    }
}
