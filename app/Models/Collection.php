<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Admin;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'collection_no',
        'collection_type',
        'party_type',
        'party_id',
        'source_type',
        'source_id',
        'amount',
        'payment_method',
        'status',
        'accepted_at',
        'accepted_by',
        'rejected_at',
        'rejected_by',
        'reject_reason',
        'transaction_id',
        'bank_name',
        'account_no',
        'payment_date',
        'note',
        'created_by',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'payment_date' => 'date',
        'accepted_at'  => 'datetime',
        'rejected_at'  => 'datetime',
    ];

    public static function generateCollectionNo(): string
    {
        $date = date('Ymd');
        $last = self::where('collection_no', 'LIKE', "COL-{$date}-%")
            ->latest('id')
            ->first();

        $nextSeq = 1;
        if ($last && preg_match('/-(\d+)$/', $last->collection_no, $matches)) {
            $nextSeq = ((int) $matches[1]) + 1;
        }

        return 'COL-' . $date . '-' . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);
    }

    public function party()
    {
        return $this->morphTo();
    }

    public function source()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function acceptedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'accepted_by');
    }

    public function walletTransaction()
    {
        return $this->hasOne(DealerWalletTransaction::class, 'collection_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}
