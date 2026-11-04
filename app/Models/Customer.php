<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, TracksSoftDeletion;

    protected $fillable = ['user_id', 'name', 'phone', 'email'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function customOrders()
    {
        return $this->hasMany(CustomOrder::class);
    }

    public function posOrders()
    {
        return $this->hasMany(PosOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }
}
