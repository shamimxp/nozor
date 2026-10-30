<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Admin;

class DealerWalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'dealer_id',
        'transaction_no',
        'collection_id',
        'dealer_order_id',
        'type', // 'credit', 'debit'
        'amount',
        'before_balance',
        'after_balance',
        'payment_method',
        'reference_no',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'before_balance' => 'decimal:2',
        'after_balance'  => 'decimal:2',
    ];

    public static function generateTransactionNo(): string
    {
        $date = date('Ymd');
        $last = self::where('transaction_no', 'LIKE', "DWT-{$date}-%")
            ->latest('id')
            ->first();

        $nextSeq = 1;
        if ($last && preg_match('/-(\d+)$/', $last->transaction_no, $matches)) {
            $nextSeq = ((int) $matches[1]) + 1;
        }

        return 'DWT-' . $date . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    public function dealerOrder()
    {
        return $this->belongsTo(DealerOrder::class, 'dealer_order_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
