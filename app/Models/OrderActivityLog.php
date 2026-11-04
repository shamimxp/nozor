<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderActivityLog extends Model
{
    use HasFactory;

    protected $table = 'order_activity_logs';

    protected $guarded = ['id'];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Map order_type string to Eloquent Model
     */
    public static array $typeMap = [
        'pos_order'     => PosOrder::class,
        'web_order'     => WebOrder::class,
        'dealer_order'  => DealerOrder::class,
        'order_request' => OrderRequest::class,
        'draft_order'   => DraftOrder::class,
    ];

    /**
     * Morph relation to order model
     */
    public function order()
    {
        $class = self::$typeMap[$this->order_type] ?? null;
        if ($class) {
            return $this->belongsTo($class, 'order_id');
        }
        return null;
    }

    /**
     * Formatted datetime (e.g., "03 Nov 2026, 11:45 PM")
     */
    public function getFormattedTimeAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('d M Y, h:i A') : '';
    }

    /**
     * Relative human-readable time (e.g., "2 hours ago")
     */
    public function getRelativeTimeAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }

    /**
     * Action badge configuration (color, label, icon)
     */
    public function getActionBadgeAttribute(): array
    {
        $action = strtolower($this->action ?? 'updated');

        return match ($action) {
            'created' => [
                'bg'     => '#E8F5E9',
                'color'  => '#1B5E20',
                'border' => '#A5D6A7',
                'label'  => 'Created',
                'icon'   => 'plus-circle',
            ],
            'status_change' => [
                'bg'     => '#FFF8E1',
                'color'  => '#E65100',
                'border' => '#FFE082',
                'label'  => 'Status Change',
                'icon'   => 'refresh-cw',
            ],
            'approved' => [
                'bg'     => '#E8F5E9',
                'color'  => '#2E7D32',
                'border' => '#81C784',
                'label'  => 'Approved',
                'icon'   => 'check-circle',
            ],
            'processing' => [
                'bg'     => '#E3F2FD',
                'color'  => '#1565C0',
                'border' => '#90CAF9',
                'label'  => 'Processing',
                'icon'   => 'package',
            ],
            'delivered' => [
                'bg'     => '#E0F2F1',
                'color'  => '#004D40',
                'border' => '#80CBC4',
                'label'  => 'Delivered',
                'icon'   => 'truck',
            ],
            'payment', 'payment_received' => [
                'bg'     => '#F3E5F5',
                'color'  => '#4A148C',
                'border' => '#CE93D8',
                'label'  => 'Payment',
                'icon'   => 'dollar-sign',
            ],
            'item_updated', 'updated' => [
                'bg'     => '#E3F2FD',
                'color'  => '#0D47A1',
                'border' => '#BBDEFB',
                'label'  => 'Updated',
                'icon'   => 'edit-3',
            ],
            'cancelled', 'rejected' => [
                'bg'     => '#FFEBEE',
                'color'  => '#B71C1C',
                'border' => '#FFCDD2',
                'label'  => ucfirst($action),
                'icon'   => 'x-circle',
            ],
            default => [
                'bg'     => '#F5F5F5',
                'color'  => '#424242',
                'border' => '#E0E0E0',
                'label'  => ucfirst(str_replace('_', ' ', $action)),
                'icon'   => 'activity',
            ]
        };
    }

    /**
     * Actor badge styling
     */
    public function getActorBadgeAttribute(): array
    {
        $type = strtolower($this->actor_type ?? 'system');

        return match ($type) {
            'admin' => [
                'bg'     => '#111C38',
                'color'  => '#FFFFFF',
                'label'  => 'Admin / Staff',
                'icon'   => 'shield',
            ],
            'worker' => [
                'bg'     => '#4A5568',
                'color'  => '#FFFFFF',
                'label'  => 'Worker',
                'icon'   => 'tool',
            ],
            'dealer' => [
                'bg'     => '#0284C7',
                'color'  => '#FFFFFF',
                'label'  => 'Dealer',
                'icon'   => 'briefcase',
            ],
            'customer' => [
                'bg'     => '#059669',
                'color'  => '#FFFFFF',
                'label'  => 'Customer',
                'icon'   => 'user',
            ],
            default => [
                'bg'     => '#6B7280',
                'color'  => '#FFFFFF',
                'label'  => 'System / Automated',
                'icon'   => 'cpu',
            ]
        };
    }
}
