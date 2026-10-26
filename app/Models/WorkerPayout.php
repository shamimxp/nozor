<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkerPayout extends Model
{
    use HasFactory;

    protected $table = 'worker_payouts';

    protected $fillable = [
        'payment_number',
        'worker_id',
        'payment_date',
        'total_balance',
        'amount',
        'remaining_balance',
        'payment_method',
        'account_details',
        'note',
        'created_by',
    ];

    protected $casts = [
        'payment_date'      => 'date',
        'total_balance'     => 'decimal:2',
        'amount'            => 'decimal:2',
        'remaining_balance' => 'decimal:2',
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(WorkerPayoutItem::class, 'worker_payout_id');
    }

    /**
     * Generate unique payment number: WP-YYYYMMDD-0001
     */
    public static function generatePaymentNumber()
    {
        $prefix = 'WP-' . date('Ymd') . '-';
        $last = self::where('payment_number', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->payment_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $prefix . $newNumber;
    }
}
