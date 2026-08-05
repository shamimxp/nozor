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
        'total_amount',
        'created_by',
        'status',
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
}
