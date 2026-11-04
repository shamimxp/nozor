<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
    use HasFactory, TracksSoftDeletion;

    protected $fillable = ['name'];

    public function variationValues()
    {
        return $this->hasMany(VariationValue::class);
    }

    public function productVariations()
    {
        return $this->hasMany(ProductVariation::class);
    }
}
