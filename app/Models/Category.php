<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, \App\Traits\UploadAble;

    protected $fillable = ['name', 'slug', 'image', 'status'];

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }
}
