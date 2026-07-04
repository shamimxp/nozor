<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'body_charge',
        'finishing_charge',
        'bearing',
        'stone',
        'electric_bill',
        'gas_bill',
        'box_price',
        'carrying_charge',
        'wire_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function items()
    {
        return $this->hasMany(ProductRecipeItem::class);
    }
}
