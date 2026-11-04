<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory, TracksSoftDeletion;

    protected $fillable = ['name', 'status'];

    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
