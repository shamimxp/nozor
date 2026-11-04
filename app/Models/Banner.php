<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory, TracksSoftDeletion, \App\Traits\UploadAble;

    protected $fillable = [
        'title',
        'sub_title',
        'image',
        'status',
    ];
}
