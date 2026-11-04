<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory, TracksSoftDeletion;
    
    protected $fillable = ['code', 'type', 'amount', 'start_date', 'end_date', 'status'];
}
