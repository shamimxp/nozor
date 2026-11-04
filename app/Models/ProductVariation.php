<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use TracksSoftDeletion;

    protected $table = 'product_variation';
    
    protected $fillable = ['product_id', 'variation_id', 'variation_value_id'];

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }

    public function variationValue()
    {
        return $this->belongsTo(VariationValue::class);
    }
}
