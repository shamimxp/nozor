<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufactureOrderItem extends Model
{
    use HasFactory, TracksSoftDeletion;

    protected $table = 'manufacture_order_items';

    protected $fillable = [
        'manufacture_id',
        'product_id',
        'reff_invoice',
        'cost_setting_id',
        'cost_title',
        'part_type',
        'body_part_price',
        'finishing_part_price',
        'unit_price',
        'qty',
        'received_qty',
        'total_price',
        'note',
    ];

    protected $casts = [
        'body_part_price' => 'decimal:2',
        'finishing_part_price' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'qty' => 'integer',
    ];

    public function manufacture()
    {
        return $this->belongsTo(Manufacture::class, 'manufacture_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function costSetting()
    {
        return $this->belongsTo(CostSetting::class, 'cost_setting_id');
    }
}
