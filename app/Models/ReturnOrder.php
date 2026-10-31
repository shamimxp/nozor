<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrder extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $guarded = ['id'];

    protected $casts = [
        'return_date'      => 'date:Y-m-d',
        'approved_at'      => 'datetime',
        'rejected_at'      => 'datetime',
        'stock_restocked'  => 'boolean',
        'total_qty'        => 'float',
        'subtotal'         => 'float',
        'deduction_amount' => 'float',
        'refund_amount'    => 'float',
    ];

    /**
     * Line items in this return
     */
    public function items()
    {
        return $this->hasMany(ReturnOrderItem::class, 'return_id');
    }

    /**
     * Polymorphic relation to party (Dealer, Customer, User)
     */
    public function party()
    {
        return $this->morphTo(__FUNCTION__, 'party_type', 'party_id');
    }

    /**
     * Link to dealer wallet transaction if credited to wallet
     */
    public function walletTransaction()
    {
        return $this->belongsTo(DealerWalletTransaction::class, 'dealer_wallet_transaction_id');
    }

    /**
     * Admin who approved
     */
    public function approver()
    {
        return $this->belongsTo(Admin\Admin::class, 'approved_by');
    }

    /**
     * Admin who rejected
     */
    public function rejector()
    {
        return $this->belongsTo(Admin\Admin::class, 'rejected_by');
    }

    /**
     * Admin who created
     */
    public function creator()
    {
        return $this->belongsTo(Admin\Admin::class, 'created_by');
    }

    /**
     * Unique Return Number generator (e.g. RET-20261031-0001)
     */
    public static function generateReturnNo(): string
    {
        $prefix = 'RET-' . date('Ymd') . '-';
        $latest = static::where('return_no', 'like', $prefix . '%')->orderBy('id', 'desc')->first();

        if ($latest) {
            $lastNum = (int) substr($latest->return_no, -4);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . $nextNum;
    }

    /**
     * Status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $status = strtolower($this->status);

        return match ($status) {
            'approved' => '<span class="badge" style="background-color: #E8F5E9; color: #1B5E20; border: 1px solid #A5D6A7; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px;"><i data-feather="check-circle" style="width: 11px; height: 11px; margin-right: 3px;"></i> Approved</span>',
            'rejected' => '<span class="badge" style="background-color: #FFEBEE; color: #B71C1C; border: 1px solid #FFCDD2; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px;"><i data-feather="x-circle" style="width: 11px; height: 11px; margin-right: 3px;"></i> Rejected</span>',
            'cancelled' => '<span class="badge" style="background-color: #ECEFF1; color: #455A64; border: 1px solid #CFD8DC; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px;">Cancelled</span>',
            default => '<span class="badge" style="background-color: #FFF3E0; color: #E65100; border: 1px solid #FFE0B2; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px;"><i data-feather="clock" style="width: 11px; height: 11px; margin-right: 3px;"></i> Pending Review</span>',
        };
    }

    /**
     * Refund type badge HTML
     */
    public function getRefundTypeBadgeAttribute(): string
    {
        $type = strtolower($this->refund_type);

        if ($type === 'wallet') {
            return '<span class="badge" style="background-color: #EDE7F6; color: #512DA8; border: 1px solid #D1C4E9; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px;"><i data-feather="credit-card" style="width: 11px; height: 11px; margin-right: 3px;"></i> Dealer Wallet</span>';
        }

        return '<span class="badge" style="background-color: #E0F2F1; color: #004D40; border: 1px solid #B2DFDB; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px;"><i data-feather="dollar-sign" style="width: 11px; height: 11px; margin-right: 3px;"></i> Cash</span>';
    }

    /**
     * Channel badge HTML
     */
    public function getChannelBadgeAttribute(): string
    {
        $channel = strtolower($this->channel);

        return match ($channel) {
            'dealer' => '<span class="badge" style="background-color: #E3F2FD; color: #0D47A1; border: 1px solid #BBDEFB; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 4px;">Dealer Order</span>',
            'pos' => '<span class="badge" style="background-color: #FBE9E7; color: #BF360C; border: 1px solid #FFCCBC; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 4px;">POS Sale</span>',
            'web' => '<span class="badge" style="background-color: #E8EAF6; color: #1A237E; border: 1px solid #C5CAE9; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 4px;">Web Order</span>',
            default => '<span class="badge badge-light-secondary font-small-1 font-weight-bold">' . ucfirst($channel) . '</span>',
        };
    }
}
