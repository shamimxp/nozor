<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecipeItem extends Model
{
    use HasFactory, TracksSoftDeletion;
    
    protected $fillable = [
        'product_recipe_id',
        'raw_material_product_id',
        'thickness',
        'width',
        'height',
        'area',
        'grade_value',
        'qty'
    ];

    public function rawMaterialProduct()
    {
        return $this->belongsTo(RawMaterialProduct::class);
    }
}
