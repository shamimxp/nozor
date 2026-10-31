<?php

namespace App\Services;

use App\Models\UserActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Cache of logged events in current request lifecycle to prevent duplicates
     */
    protected static array $loggedEvents = [];

    /**
     * Keys to automatically redact from logged diffs/properties
     */
    protected static array $redactedKeys = [
        'password',
        'password_confirmation',
        'remember_token',
        'token',
        'secret',
        'card_number',
        'cvv',
        'cvc',
    ];

    /**
     * Primary logging method for staff user activity and database mutations
     *
     * @param string $description Human-friendly description of the event
     * @param string $event Event type ('created', 'updated', 'deleted', 'status_change', 'login', 'logout')
     * @param Model|null $subject The entity affected
     * @param array $properties Additional attributes or old/new diff
     * @param string|null $logName Module or channel name ('admin', 'auth')
     * @param string|null $actionType Specific action category ('order', 'product', 'payment', etc.)
     * @return UserActivity|null
     */
    public static function log(
        string $description,
        string $event,
        ?Model $subject = null,
        array $properties = [],
        ?string $logName = null,
        ?string $actionType = null
    ): ?UserActivity {
        try {
            $causer = self::resolveCauser();

            // Exclude customers and dealers per requirements (staff only: admin, worker, system)
            if (!in_array($causer['role'], ['admin', 'worker'], true)) {
                return null;
            }

            $subjectRef = self::resolveSubjectRef($subject);

            // Deduplication in the same request lifecycle
            $subjectKey = $subject ? get_class($subject) . '_' . $subject->getKey() : substr(md5($description), 0, 8);
            $dedupKey = "{$causer['role']}_{$causer['id']}_{$event}_{$subjectKey}";
            if (isset(static::$loggedEvents[$dedupKey])) {
                return null;
            }
            static::$loggedEvents[$dedupKey] = true;

            $sanitizedProps = self::sanitizeProperties($properties);

            $hasRequest = app()->bound('request') && request();
            $ip = $hasRequest ? request()->ip() : '127.0.0.1';
            $ua = $hasRequest ? request()->userAgent() : (app()->runningInConsole() ? 'CLI' : 'System');
            $route = $hasRequest && request()->route() ? request()->route()->getName() : null;
            $url = $hasRequest ? request()->fullUrl() : (app()->runningInConsole() ? 'CLI Task' : null);
            $method = $hasRequest ? request()->method() : 'CLI';

            $now = now();
            $resolvedActionType = $actionType ?: self::detectActionType($subject, $description, $event);

            return UserActivity::create([
                'event' => $event,
                'action_type' => $resolvedActionType,
                'description' => $description,
                'eventDetails' => $description,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject ? $subject->getKey() : null,
                'subject_ref' => $subjectRef,
                'user_id' => $causer['id'],
                'user_name' => $causer['name'],
                'user_role' => $causer['role'],
                'properties' => !empty($sanitizedProps) ? $sanitizedProps : null,
                'date' => $now->toDateString(),
                'time' => $now->toTimeString(),
                'date_time' => $now,
                'from' => 'web',
                'ip_address' => $ip,
                'user_agent' => $ua,
                'route_name' => $route,
                'url' => $url,
                'method' => $method,
            ]);
        } catch (\Throwable $e) {
            Log::warning('ActivityLogger failed to record event: ' . $e->getMessage(), [
                'event' => $event,
                'description' => $description,
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Log Eloquent Model mutations automatically with diff
     */
    public static function logModelEvent(string $event, Model $model, ?string $customDesc = null): ?UserActivity
    {
        try {
            $modelClass = class_basename($model);
            $subjectRef = self::resolveSubjectRef($model);

            $properties = [];
            $description = $customDesc;

            if ($event === 'created') {
                $attributes = $model->attributesToArray();
                $properties = ['attributes' => self::sanitizeProperties($attributes)];
                $description = $description ?? "Created {$modelClass} ({$subjectRef})";
            } elseif ($event === 'updated') {
                $changes = $model->getChanges();
                unset($changes['updated_at']);

                if (empty($changes)) {
                    return null; // Nothing changed
                }

                $old = [];
                $new = [];
                foreach ($changes as $key => $val) {
                    $old[$key] = $model->getOriginal($key);
                    $new[$key] = $val;
                }

                $properties = [
                    'old' => self::sanitizeProperties($old),
                    'new' => self::sanitizeProperties($new),
                ];

                // Detect status change specifically
                if (isset($changes['status']) || isset($changes['order_status'])) {
                    $event = 'status_change';
                    $statusKey = isset($changes['status']) ? 'status' : 'order_status';
                    $from = $old[$statusKey] ?? 'unknown';
                    $to = $new[$statusKey] ?? 'unknown';
                    $description = $description ?? "Changed {$modelClass} status from '{$from}' to '{$to}' ({$subjectRef})";
                } else {
                    $description = $description ?? "Updated {$modelClass} ({$subjectRef})";
                }
            } elseif ($event === 'deleted') {
                $properties = ['attributes' => self::sanitizeProperties($model->attributesToArray())];
                $description = $description ?? "Deleted {$modelClass} ({$subjectRef})";
            }

            return self::log($description, $event, $model, $properties);
        } catch (\Throwable $e) {
            Log::warning('ActivityLogger::logModelEvent failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Resolve active actor across auth guards (Staff only: Admin or Worker)
     */
    protected static function resolveCauser(): array
    {
        try {
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                return [
                    'type' => get_class($user),
                    'id' => $user->getKey(),
                    'name' => $user->name ?? $user->email ?? 'Admin #' . $user->getKey(),
                    'role' => 'admin',
                ];
            }
        } catch (\Throwable $e) {}

        try {
            // Check if worker session or guard exists
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
                if (isset($user->is_worker) && $user->is_worker) {
                    return [
                        'type' => get_class($user),
                        'id' => $user->getKey(),
                        'name' => $user->name ?? 'Worker #' . $user->getKey(),
                        'role' => 'worker',
                    ];
                }
            }
        } catch (\Throwable $e) {}

        if (app()->runningInConsole()) {
            return [
                'type' => 'System/CLI',
                'id' => 1,
                'name' => 'System Admin (CLI)',
                'role' => 'admin',
            ];
        }

        return [
            'type' => null,
            'id' => null,
            'name' => 'Guest / Other',
            'role' => 'guest',
        ];
    }

    /**
     * Extract human identifier for a subject
     */
    protected static function resolveSubjectRef(?Model $subject): string
    {
        if (!$subject) {
            return '';
        }

        // Check common identifier fields
        $fields = ['order_no', 'order_number', 'collection_no', 'invoice_no', 'title', 'name', 'code', 'slug'];
        foreach ($fields as $field) {
            if (!empty($subject->{$field})) {
                return (string) $subject->{$field};
            }
        }

        return '#' . $subject->getKey();
    }

    /**
     * Auto-detect action type category from subject, description, and event
     */
    public static function detectActionType($subject, string $description, string $event): string
    {
        $text = strtolower($description . ' ' . $event);

        if (str_contains($text, 'login')) return 'login';
        if (str_contains($text, 'logout')) return 'logout';
        if (str_contains($text, 'order') || str_contains($text, 'invoice')) return 'order';
        if (str_contains($text, 'cart')) return 'cart';
        if (str_contains($text, 'wishlist')) return 'wishlist';
        if (str_contains($text, 'review')) return 'review';
        if (str_contains($text, 'payment') || str_contains($text, 'collection')) return 'payment';
        if (str_contains($text, 'product') || str_contains($text, 'price') || str_contains($text, 'stock')) return 'product';
        if (str_contains($text, 'category')) return 'category';
        if (str_contains($text, 'coupon')) return 'coupon';
        if (str_contains($text, 'banner')) return 'banner';
        if (str_contains($text, 'dealer')) return 'dealer';
        if (str_contains($text, 'customer') || str_contains($text, 'account') || str_contains($text, 'user') || str_contains($text, 'address')) return 'customer';
        if (str_contains($text, 'pos')) return 'pos';
        if (str_contains($text, 'manufacture')) return 'manufacture';
        if (str_contains($text, 'purchase')) return 'purchase';
        if (str_contains($text, 'contact') || str_contains($text, 'subscriber')) return 'inquiry';

        if ($subject instanceof Model) {
            $class = class_basename($subject);
            return match ($class) {
                'WebOrder', 'DealerOrder', 'PosOrder', 'CustomOrder', 'DraftOrder', 'OrderRequest' => 'order',
                'Cart' => 'cart',
                'Wishlist' => 'wishlist',
                'Product', 'StockAdjustment' => 'product',
                'Category', 'SubCategory' => 'category',
                'Collection', 'CustomerPayment', 'VendorPayment', 'DealerOrderPayment', 'DealerWalletTransaction' => 'payment',
                'User', 'Customer', 'CustomerAddress', 'Vendor', 'Worker' => 'customer',
                'ProductReview' => 'review',
                'ContactMessage', 'Subscriber' => 'inquiry',
                'Coupon', 'Banner' => 'marketing',
                'Manufacture' => 'manufacture',
                'Purchase', 'InventoryPurchase' => 'purchase',
                default => 'database'
            };
        }

        return 'database';
    }

    /**
     * Sanitize array to prevent passwords and secrets from being stored
     */
    protected static function sanitizeProperties(array $props): array
    {
        foreach ($props as $key => $value) {
            if (is_array($value)) {
                $props[$key] = self::sanitizeProperties($value);
            } elseif (in_array(strtolower($key), self::$redactedKeys, true)) {
                $props[$key] = '[REDACTED]';
            }
        }
        return $props;
    }
}
