<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Super Admin bypasses every ability check for their guard.
        Gate::before(function ($user, $ability) {
            if (!method_exists($user, 'hasRole')) {
                return null;
            }

            $role = config('admin_permissions.super_admin_role', 'Super Admin');

            try {
                if ($user->hasRole($role)) {
                    return true;
                }
            } catch (\Throwable $e) {
                return null;
            }

            return null;
        });
    }
}
