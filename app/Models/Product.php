<?php

namespace App\Models;

use App\Traits\UploadAble;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, UploadAble;

    protected $fillable = [
        'name', 'slug', 'category_id', 'sub_category_id', 'unit_id', 
        'short_description', 'max_order_qty', 'is_featured', 
        'status', 'selling_price', 'cost_price', 'featured_image', 
        'stock', 'discount_type', 'discount_amount'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function gallery()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
