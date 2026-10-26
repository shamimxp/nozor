<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name', 'phone', 'email', 'address', 'profile_image',
        'nid', 'description', 'type', 'password', 'status'
    ];

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
}
