<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use App\Models\WebSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            if (Schema::hasTable('categories')) {
                // If they use products_count on the header, we can just fetch categories and let them use it.
                // Wait, products_count is used in frontend/index.blade.php.
                View::share('categories', Category::where('status', 1)->get());
            }
            if (Schema::hasTable('web_settings')) {
                View::share('settings', WebSetting::first());
            }
        } catch (\Exception $e) {
            // Ignore during migrations or when DB is unavailable
        }
    }
}
