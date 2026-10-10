<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostHead extends Model
{
    use HasFactory;

    protected $table = 'cost_heads';

    protected $fillable = [
        'title',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function costSettingItems()
    {
        return $this->hasMany(CostSettingItem::class, 'cost_head_id');
    }
}
