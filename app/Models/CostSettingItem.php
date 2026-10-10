<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostSettingItem extends Model
{
    use HasFactory;

    protected $table = 'cost_setting_items';

    protected $fillable = [
        'cost_setting_id',
        'cost_head_id',
        'cost_price',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
    ];

    public function costSetting()
    {
        return $this->belongsTo(CostSetting::class, 'cost_setting_id');
    }

    public function costHead()
    {
        return $this->belongsTo(CostHead::class, 'cost_head_id');
    }
}
