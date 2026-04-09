<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    use HasFactory, \App\Traits\UploadAble;

    protected $fillable = ['category_id', 'name', 'slug', 'image', 'status'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
