<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerPayoutItem extends Model
{
    use HasFactory;

    protected $table = 'worker_payout_items';

    protected $fillable = [
        'worker_payout_id',
        'worker_payment_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function payout()
    {
        return $this->belongsTo(WorkerPayout::class, 'worker_payout_id');
    }

    public function invoice()
    {
        return $this->belongsTo(WorkerPayment::class, 'worker_payment_id');
    }
}
