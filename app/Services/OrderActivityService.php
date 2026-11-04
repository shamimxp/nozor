<?php

namespace App\Services;

use App\Models\DealerOrder;
use App\Models\DraftOrder;
use App\Models\OrderActivityLog;
use App\Models\OrderRequest;
use App\Models\PosOrder;
use App\Models\WebOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderActivityService
{
    /**
     * Map model classes to order_type identifiers
     */
    protected static array $classTypeMap = [
        PosOrder::class     => 'pos_order',
        WebOrder::class     => 'web_order',
        DealerOrder::class  => 'dealer_order',
        OrderRequest::class => 'order_request',
        DraftOrder::class   => 'draft_order',
    ];

    /**
     * Log a general order activity
     */
    public static function log(
        Model $order,
        string $action,
        string $description,
        ?string $fromStatus = null,
        ?string $toStatus = null,
        ?string $remarks = null,
        array $properties = [],
        ?array $actor = null
    ): ?OrderActivityLog {
        try {
            $orderType = self::resolveOrderType($order);
            $orderNumber = self::resolveOrderNumber($order);
            $actorData = $actor ?: self::resolveActor();

            $hasRequest = app()->bound('request') && request();
            $ip = $hasRequest ? request()->ip() : null;
            $ua = $hasRequest ? request()->userAgent() : null;

            return OrderActivityLog::create([
                'order_type'   => $orderType,
                'order_id'     => $order->getKey(),
                'order_number' => $orderNumber,
                'action'       => $action,
                'description'  => $description,
                'from_status'  => $fromStatus,
                'to_status'    => $toStatus,
                'actor_id'     => $actorData['id'] ?? null,
                'actor_name'   => $actorData['name'] ?? 'System',
                'actor_type'   => $actorData['type'] ?? 'system',
                'remarks'      => $remarks,
                'properties'   => !empty($properties) ? $properties : null,
                'ip_address'   => $ip,
                'user_agent'   => $ua,
            ]);
        } catch (\Throwable $e) {
            Log::warning('OrderActivityService failed to log activity: ' . $e->getMessage(), [
                'action'    => $action,
                'order_id'  => $order ? $order->getKey() : null,
                'trace'     => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Log order creation
     */
    public static function logCreated(
        Model $order,
        ?string $remarks = null,
        array $properties = [],
        ?array $customActor = null
    ): ?OrderActivityLog {
        $actor = $customActor ?: self::resolveActor();
        $orderNumber = self::resolveOrderNumber($order);
        $orderTypeName = self::humanOrderType($order);

        $description = "{$orderTypeName} {$orderNumber} was created by {$actor['name']}.";

        $status = $order->order_status ?? $order->status ?? null;

        return self::log(
            order: $order,
            action: 'created',
            description: $description,
            fromStatus: null,
            toStatus: $status ? (string)$status : null,
            remarks: $remarks,
            properties: $properties,
            actor: $actor
        );
    }

    /**
     * Log order status change
     */
    public static function logStatusChange(
        Model $order,
        string $fromStatus,
        string $toStatus,
        ?string $remarks = null,
        array $properties = [],
        ?array $customActor = null
    ): ?OrderActivityLog {
        $actor = $customActor ?: self::resolveActor();
        $orderNumber = self::resolveOrderNumber($order);
        $orderTypeName = self::humanOrderType($order);

        $fromLabel = ucfirst(str_replace('_', ' ', $fromStatus));
        $toLabel = ucfirst(str_replace('_', ' ', $toStatus));

        $action = match (strtolower($toStatus)) {
            'delivered'           => 'delivered',
            'processing'          => 'processing',
            'confirm', 'approved' => 'approved',
            'cancelled'           => 'cancelled',
            'rejected'            => 'rejected',
            default               => 'status_change'
        };

        $description = "Status changed from '{$fromLabel}' to '{$toLabel}' for {$orderTypeName} {$orderNumber} by {$actor['name']}.";

        return self::log(
            order: $order,
            action: $action,
            description: $description,
            fromStatus: $fromStatus,
            toStatus: $toStatus,
            remarks: $remarks,
            properties: $properties,
            actor: $actor
        );
    }

    /**
     * Log order update (details, items, address, financial changes)
     */
    public static function logUpdated(
        Model $order,
        string $description,
        array $changes = [],
        ?string $remarks = null,
        ?array $customActor = null
    ): ?OrderActivityLog {
        $actor = $customActor ?: self::resolveActor();

        return self::log(
            order: $order,
            action: 'updated',
            description: $description,
            fromStatus: null,
            toStatus: null,
            remarks: $remarks,
            properties: !empty($changes) ? ['changes' => $changes] : [],
            actor: $actor
        );
    }

    /**
     * Log item quantity or line-item adjustments
     */
    public static function logItemUpdated(
        Model $order,
        string $description,
        array $itemData = [],
        ?string $remarks = null,
        ?array $customActor = null
    ): ?OrderActivityLog {
        $actor = $customActor ?: self::resolveActor();

        return self::log(
            order: $order,
            action: 'item_updated',
            description: $description,
            fromStatus: null,
            toStatus: null,
            remarks: $remarks,
            properties: !empty($itemData) ? ['item' => $itemData] : [],
            actor: $actor
        );
    }

    /**
     * Log payment received
     */
    public static function logPayment(
        Model $order,
        float|string $amount,
        string $method = 'Cash',
        ?string $remarks = null,
        array $properties = [],
        ?array $customActor = null
    ): ?OrderActivityLog {
        $actor = $customActor ?: self::resolveActor();
        $orderNumber = self::resolveOrderNumber($order);
        $formattedAmount = number_format((float)$amount, 2);

        $description = "Payment of ৳{$formattedAmount} received via {$method} for Order {$orderNumber}.";

        return self::log(
            order: $order,
            action: 'payment',
            description: $description,
            fromStatus: null,
            toStatus: null,
            remarks: $remarks,
            properties: array_merge(['amount' => (float)$amount, 'method' => $method], $properties),
            actor: $actor
        );
    }

    /**
     * Log approval / conversion (e.g. Order Request -> Dealer Order or Draft)
     */
    public static function logApproval(
        Model $order,
        string $description,
        ?string $remarks = null,
        array $properties = [],
        ?array $customActor = null
    ): ?OrderActivityLog {
        $actor = $customActor ?: self::resolveActor();

        return self::log(
            order: $order,
            action: 'approved',
            description: $description,
            fromStatus: null,
            toStatus: 'approved',
            remarks: $remarks,
            properties: $properties,
            actor: $actor
        );
    }

    /**
     * Log order cancellation
     */
    public static function logCancellation(
        Model $order,
        ?string $reason = null,
        array $properties = [],
        ?array $customActor = null
    ): ?OrderActivityLog {
        $actor = $customActor ?: self::resolveActor();
        $orderNumber = self::resolveOrderNumber($order);
        $orderTypeName = self::humanOrderType($order);

        $description = "{$orderTypeName} {$orderNumber} was cancelled by {$actor['name']}.";

        $oldStatus = $order->order_status ?? $order->status ?? 'active';

        return self::log(
            order: $order,
            action: 'cancelled',
            description: $description,
            fromStatus: (string)$oldStatus,
            toStatus: 'cancelled',
            remarks: $reason,
            properties: $properties,
            actor: $actor
        );
    }

    /**
     * Log order delivery
     */
    public static function logDelivery(
        Model $order,
        ?string $remarks = null,
        array $properties = [],
        ?array $customActor = null
    ): ?OrderActivityLog {
        $actor = $customActor ?: self::resolveActor();
        $orderNumber = self::resolveOrderNumber($order);
        $orderTypeName = self::humanOrderType($order);

        $description = "{$orderTypeName} {$orderNumber} was marked as Delivered by {$actor['name']}.";

        $oldStatus = $order->order_status ?? $order->status ?? 'processing';

        return self::log(
            order: $order,
            action: 'delivered',
            description: $description,
            fromStatus: (string)$oldStatus,
            toStatus: 'delivered',
            remarks: $remarks,
            properties: $properties,
            actor: $actor
        );
    }

    /**
     * Automatically resolve current actor across guards
     */
    public static function resolveActor(): array
    {
        // 1. Check Admin guard
        try {
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                return [
                    'id'   => $user->getKey(),
                    'name' => $user->name ?? $user->email ?? ('Admin #' . $user->getKey()),
                    'type' => 'admin',
                ];
            }
        } catch (\Throwable $e) {}

        // 2. Check Dealer guard
        try {
            if (Auth::guard('dealer')->check()) {
                $dealer = Auth::guard('dealer')->user();
                $label = $dealer->name ?? ('Dealer #' . $dealer->getKey());
                if (!empty($dealer->shop_name)) {
                    $label .= " ({$dealer->shop_name})";
                }
                return [
                    'id'   => $dealer->getKey(),
                    'name' => $label,
                    'type' => 'dealer',
                ];
            }
        } catch (\Throwable $e) {}

        // 3. Check Web guard (Customer or Worker)
        try {
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
                if (isset($user->is_worker) && $user->is_worker) {
                    return [
                        'id'   => $user->getKey(),
                        'name' => $user->name ?? ('Worker #' . $user->getKey()),
                        'type' => 'worker',
                    ];
                }
                return [
                    'id'   => $user->getKey(),
                    'name' => $user->name ?? ('Customer #' . $user->getKey()),
                    'type' => 'customer',
                ];
            }
        } catch (\Throwable $e) {}

        // 4. Console / Cron / CLI
        if (app()->runningInConsole()) {
            return [
                'id'   => null,
                'name' => 'System / Automated (CLI)',
                'type' => 'system',
            ];
        }

        // 5. Fallback System
        return [
            'id'   => null,
            'name' => 'System / Automated Activity',
            'type' => 'system',
        ];
    }

    /**
     * Resolve order type string
     */
    public static function resolveOrderType(Model|string $order): string
    {
        if (is_string($order)) {
            return $order;
        }

        return self::$classTypeMap[get_class($order)] ?? strtolower(class_basename($order));
    }

    /**
     * Resolve human order type name
     */
    public static function humanOrderType(Model|string $order): string
    {
        $type = self::resolveOrderType($order);

        return match ($type) {
            'pos_order'     => 'POS Order',
            'web_order'     => 'Web Order',
            'dealer_order'  => 'Dealer Order',
            'order_request' => 'Dealer Order Request',
            'draft_order'   => 'Draft Order',
            default         => ucfirst(str_replace('_', ' ', $type)),
        };
    }

    /**
     * Resolve order human identifier
     */
    public static function resolveOrderNumber(Model $order): string
    {
        $fields = ['order_number', 'invoice_no', 'request_number', 'draft_number', 'order_no'];
        foreach ($fields as $field) {
            if (!empty($order->{$field})) {
                return (string) $order->{$field};
            }
        }

        return '#' . $order->getKey();
    }
}
