<?php

namespace App\Models;

use App\Models\Admin\Admin;
use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOffer extends Model
{
    use HasFactory, TracksSoftDeletion;

    protected $fillable = [
        'product_id',
        'from_date',
        'to_date',
        'from_qty',
        'to_qty',
        'offer_type',
        'discount_value',
        'status',
        'created_by',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'from_qty' => 'integer',
        'to_qty' => 'integer',
        'discount_value' => 'decimal:2',
        'status' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
