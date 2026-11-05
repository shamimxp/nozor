<?php

namespace Database\Seeders;

use App\Models\Admin\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = config('admin_permissions.guard', 'admin');
        $superAdminName = config('admin_permissions.super_admin_role', 'Super Admin');
        $allActions = config('admin_permissions.actions', []);
        $modules = config('admin_permissions.modules', []);

        // Keep roles; rebuild admin permissions cleanly.
        $this->wipeAdminPermissions($guard);

        Role::firstOrCreate(['name' => $superAdminName, 'guard_name' => $guard]);
        Role::firstOrCreate(['name' => $superAdminName, 'guard_name' => 'seller']);

        $created = [];
        foreach ($modules as $moduleKey => $module) {
            $moduleName = $module['label'] ?? ucfirst(str_replace('_', ' ', $moduleKey));
            $actions = $module['actions'] ?? $allActions;

            foreach ($actions as $action) {
                $name = $moduleKey . '.' . $action;
                $permission = Permission::create([
                    'name' => $name,
                    'guard_name' => $guard,
                    'module_name' => $moduleName,
                ]);
                $created[] = $permission;
            }
        }

        $superAdmin = Role::findByName($superAdminName, $guard);
        $superAdmin->syncPermissions(collect($created)->pluck('name')->all());

        // Ensure every existing Super Admin user keeps full access.
        Admin::query()
            ->whereHas('roles', function ($q) use ($superAdminName, $guard) {
                $q->where('name', $superAdminName)->where('guard_name', $guard);
            })
            ->each(function (Admin $admin) use ($superAdmin) {
                $admin->syncRoles([$superAdmin]);
            });

        // Also grant Super Admin role to first admin if nobody has it yet.
        if (!Admin::role($superAdminName)->exists()) {
            $first = Admin::query()->orderBy('id')->first();
            if ($first) {
                $first->assignRole($superAdmin);
            }
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command?->info('Admin permissions seeded: ' . count($created) . ' permissions → Super Admin.');
    }

    protected function wipeAdminPermissions(string $guard): void
    {
        $permissionIds = Permission::where('guard_name', $guard)->pluck('id');

        if ($permissionIds->isEmpty()) {
            return;
        }

        DB::table(config('permission.table_names.role_has_permissions'))
            ->whereIn('permission_id', $permissionIds)
            ->delete();

        DB::table(config('permission.table_names.model_has_permissions'))
            ->whereIn('permission_id', $permissionIds)
            ->delete();

        Permission::where('guard_name', $guard)->delete();
    }
}
