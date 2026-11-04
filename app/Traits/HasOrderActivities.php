<?php

namespace App\Traits;

use App\Models\OrderActivityLog;
use App\Services\OrderActivityService;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasOrderActivities
{
    /**
     * Get all activity logs for this order
     */
    public function orderActivities(): HasMany
    {
        $orderType = OrderActivityService::resolveOrderType($this);

        return $this->hasMany(OrderActivityLog::class, 'order_id')
            ->where('order_type', $orderType)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the latest activity log
     */
    public function latestOrderActivity()
    {
        return $this->orderActivities()->first();
    }
}
