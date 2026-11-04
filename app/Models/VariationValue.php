<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariationValue extends Model
{
    use HasFactory, TracksSoftDeletion;

    protected $fillable = ['variation_id', 'value'];

    public function variation()
    {
        return $this->belongsTo(Variation::class);
    }
}
