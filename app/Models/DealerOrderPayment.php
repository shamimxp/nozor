<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Admin;

class DealerOrderPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'dealer_order_id',
        'dealer_wallet_transaction_id',
        'collection_id',
        'amount',
        'payment_date',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function dealerOrder()
    {
        return $this->belongsTo(DealerOrder::class, 'dealer_order_id');
    }

    public function walletTransaction()
    {
        return $this->belongsTo(DealerWalletTransaction::class, 'dealer_wallet_transaction_id');
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
