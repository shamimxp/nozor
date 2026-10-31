<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $table = 'user_activities';

    protected $guarded = ['id'];

    protected $casts = [
        'properties' => 'array',
        'date' => 'date:Y-m-d',
        'date_time' => 'datetime',
    ];

    /**
     * Relationship to causer / user (Admin or Worker)
     */
    public function user()
    {
        if ($this->user_role === 'worker') {
            return $this->belongsTo(Worker::class, 'user_id');
        }
        return $this->belongsTo(Admin\Admin::class, 'user_id');
    }

    /**
     * Polymorphic relation to subject if model exists
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Formatted 12-hour time (e.g., 02:45 PM)
     */
    public function getFormattedTimeAttribute(): string
    {
        if ($this->date_time) {
            return Carbon::parse($this->date_time)->format('h:i:s A');
        }
        if ($this->time) {
            return Carbon::parse($this->time)->format('h:i:s A');
        }
        return $this->created_at ? $this->created_at->format('h:i:s A') : '';
    }

    /**
     * Relative human-readable time (e.g., "5 mins ago")
     */
    public function getRelativeTimeAttribute(): string
    {
        $dt = $this->date_time ?? $this->created_at;
        return $dt ? Carbon::parse($dt)->diffForHumans() : '';
    }

    /**
     * Action type badge HTML
     */
    public function getActionTypeBadgeAttribute(): string
    {
        $type = strtoupper($this->action_type ?? $this->event ?? 'OTHER');
        $map = [
            'CREATE' => ['bg' => '#E8F5E9', 'color' => '#1B5E20', 'label' => 'CREATE'],
            'CREATED' => ['bg' => '#E8F5E9', 'color' => '#1B5E20', 'label' => 'CREATE'],
            'UPDATE' => ['bg' => '#E3F2FD', 'color' => '#0D47A1', 'label' => 'UPDATE'],
            'UPDATED' => ['bg' => '#E3F2FD', 'color' => '#0D47A1', 'label' => 'UPDATE'],
            'DELETE' => ['bg' => '#FFEBEE', 'color' => '#B71C1C', 'label' => 'DELETE'],
            'DELETED' => ['bg' => '#FFEBEE', 'color' => '#B71C1C', 'label' => 'DELETE'],
            'STATUS_CHANGE' => ['bg' => '#FFF3E0', 'color' => '#E65100', 'label' => 'STATUS'],
            'STATUS' => ['bg' => '#FFF3E0', 'color' => '#E65100', 'label' => 'STATUS'],
            'LOGIN' => ['bg' => '#F3E5F5', 'color' => '#4A148C', 'label' => 'LOGIN'],
            'LOGOUT' => ['bg' => '#ECEFF1', 'color' => '#37474F', 'label' => 'LOGOUT'],
            'AUTH' => ['bg' => '#F3E5F5', 'color' => '#4A148C', 'label' => 'AUTH'],
        ];

        $config = $map[$type] ?? ['bg' => '#F5F5F5', 'color' => '#424242', 'label' => $type];

        return sprintf(
            '<span class="badge" style="background-color: %s; color: %s; font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 4px; text-transform: uppercase;">%s</span>',
            $config['bg'],
            $config['color'],
            e($config['label'])
        );
    }

    /**
     * Event badge HTML
     */
    public function getEventBadgeAttribute(): string
    {
        return $this->getActionTypeBadgeAttribute();
    }
}
