<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory, TracksSoftDeletion;
    
    protected $fillable = [
        'product_id',
        'name',
        'comment',
        'rating',
        'status',
        'reply',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}