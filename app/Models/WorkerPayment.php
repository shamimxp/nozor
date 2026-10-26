<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'manufacture_order_id',
        'date',
        'worker_id',
        'received_qty',
        'unit_price',
        'total_amount',
        'paid_amount',
        'due_amount',
        'created_by',
        'status',
    ];

    protected $casts = [
        'date'         => 'date',
        'unit_price'   => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount'  => 'decimal:2',
        'due_amount'   => 'decimal:2',
    ];

    public function manufacture()
    {
        return $this->belongsTo(Manufacture::class, 'manufacture_order_id');
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by'); // Admin user typically
    }

    public function payoutItems()
    {
        return $this->hasMany(WorkerPayoutItem::class, 'worker_payment_id');
    }
}
