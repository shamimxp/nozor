<?php

namespace App\Models;

use App\Traits\TracksSoftDeletion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Worker extends Authenticatable
{
    use HasFactory, TracksSoftDeletion;

    protected $fillable = [
        'name', 'phone', 'email', 'address', 'profile_image',
        'nid', 'description', 'type', 'password', 'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'status' => 'integer',
        'type' => 'integer',
    ];

    public function setPasswordAttribute($value)
    {
        if (! empty($value)) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function invoices()
    {
        return $this->hasMany(WorkerPayment::class, 'worker_id');
    }

    public function payments()
    {
        return $this->hasMany(WorkerPayment::class, 'worker_id');
    }

    public function payouts()
    {
        return $this->hasMany(WorkerPayout::class, 'worker_id');
    }

    public function manufactures()
    {
        return $this->hasMany(Manufacture::class);
    }

    public function getDueBalanceAttribute()
    {
        return (float) $this->invoices()->where('due_amount', '>', 0)->sum('due_amount');
    }

    public function getTotalEarnedAttribute()
    {
        return (float) $this->invoices()->sum('total_amount');
    }

    public function getTotalPaidAttribute()
    {
        return (float) $this->invoices()->sum('paid_amount');
    }

    public function getTypeLabelAttribute(): string
    {
        return (int) $this->type === 1 ? 'Finishing Part Worker' : 'Body Part Worker';
    }
}
