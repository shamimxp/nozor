<?php

namespace App\Models;

use App\Traits\HasOrderActivities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DraftOrder extends Model
{
    use HasFactory, HasOrderActivities;

    protected $fillable = [
        'draft_number',
        'order_request_id',
        'dealer_id',
        'sub_total',
        'discount',
        'carrying_charge',
        'grand_total',
        'note',
        'admin_notes',
        'status',
        'dealer_order_id',
        'creator_id',
    ];

    protected $casts = [
        'sub_total'       => 'decimal:2',
        'discount'        => 'decimal:2',
        'carrying_charge' => 'decimal:2',
        'grand_total'     => 'decimal:2',
    ];

    /**
     * Generate next draft number like DRF-2026-0001
     */
    public static function generateDraftNumber(): string
    {
        $year   = date('Y');
        $last   = self::whereYear('created_at', $year)->latest('id')->first();
        $nextId = $last ? ($last->id + 1) : 1;
        return 'DRF-' . $year . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    public function orderRequest()
    {
        return $this->belongsTo(OrderRequest::class, 'order_request_id');
    }

    public function dealerOrder()
    {
        return $this->belongsTo(DealerOrder::class, 'dealer_order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function items()
    {
        return $this->hasMany(DraftOrderItem::class, 'draft_order_id');
    }
}
