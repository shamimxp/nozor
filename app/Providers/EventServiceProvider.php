<?php

namespace App\Providers;

use App\Observers\ActivityObserver;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Models tracked by the ActivityObserver for database mutations
     */
    protected array $trackedModels = [
        // Web E-Commerce & Customer Database Operations
        \App\Models\WebOrder::class,
        \App\Models\WebOrderAddress::class,
        \App\Models\Cart::class,
        \App\Models\Wishlist::class,
        \App\Models\User::class,
        \App\Models\CustomerAddress::class,
        \App\Models\ProductReview::class,
        \App\Models\ContactMessage::class,
        \App\Models\Subscriber::class,
        \App\Models\Coupon::class,

        // Dealer Orders & Transactions
        \App\Models\Dealer::class,
        \App\Models\DealerOrder::class,
        \App\Models\DealerOrderPayment::class,
        \App\Models\DealerWalletTransaction::class,
        \App\Models\DraftOrder::class,
        \App\Models\OrderRequest::class,

        // POS & Custom Orders
        \App\Models\PosOrder::class,
        \App\Models\CustomOrder::class,

        // Catalog & Inventory
        \App\Models\Product::class,
        \App\Models\Category::class,
        \App\Models\SubCategory::class,
        \App\Models\StockAdjustment::class,
        \App\Models\Banner::class,

        // Financial & Operational
        \App\Models\Collection::class,
        \App\Models\CustomerPayment::class,
        \App\Models\VendorPayment::class,
        \App\Models\Purchase::class,
        \App\Models\InventoryPurchase::class,
        \App\Models\Manufacture::class,
        \App\Models\WorkerPayment::class,
        \App\Models\Customer::class,
        \App\Models\Vendor::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // 1. Register Activity Observers across all transactional database models
        foreach ($this->trackedModels as $modelClass) {
            if (class_exists($modelClass)) {
                $modelClass::observe(ActivityObserver::class);
            }
        }

        // 2. Register Auth Login & Logout Activity Listeners
        Event::listen(Login::class, function ($event) {
            $user = $event->user;
            $guard = $event->guard ?? 'web';
            $name = $user->name ?? $user->email ?? 'User #' . $user->id;
            ActivityLogger::log("User logged in ({$name})", 'login', $user, [
                'guard' => $guard,
                'email' => $user->email ?? null,
            ], 'auth');
        });

        Event::listen(Logout::class, function ($event) {
            $user = $event->user;
            if ($user) {
                $guard = $event->guard ?? 'web';
                $name = $user->name ?? $user->email ?? 'User #' . $user->id;
                ActivityLogger::log("User logged out ({$name})", 'logout', $user, [
                    'guard' => $guard,
                ], 'auth');
            }
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
