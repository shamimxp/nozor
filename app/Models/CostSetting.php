<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostSetting extends Model
{
    use HasFactory;

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
}
