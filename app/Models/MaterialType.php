<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialType extends Model
{
    use HasFactory, TracksSoftDeletion;

    protected $fillable = ['name', 'status'];

    public function rawMaterialProducts()
    {
        return $this->hasMany(RawMaterialProduct::class);
    }
}
