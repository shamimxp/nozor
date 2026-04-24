<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\Admin\Admin::class, 'created_by');
    }
}
