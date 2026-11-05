<?php

namespace App\Http\Controllers\Admin\Concerns;

trait AuthorizesAdminModule
{
    /**
     * Wire Spatie can:* middleware for a module using standard CRUD + trash verbs.
     *
     * @param  array<string, array<int, string>>  $extra  permission => methods
     */
    protected function authorizeAdminModule(string $module, array $extra = []): void
    {
        $map = [
            "{$module}.list" => ['index'],
            "{$module}.create" => ['create', 'store'],
            "{$module}.edit" => ['edit', 'update'],
            "{$module}.show" => ['show'],
            "{$module}.delete" => ['destroy'],
            "{$module}.deleted-list" => ['trash'],
            "{$module}.restore" => ['restoreTrash', 'bulkRestoreTrash'],
            "{$module}.force-delete" => ['forceDeleteTrash', 'bulkForceDeleteTrash'],
        ];

        foreach ($map as $permission => $methods) {
            $existing = array_values(array_filter($methods, fn ($method) => method_exists($this, $method)));
            if ($existing !== []) {
                $this->middleware('can:' . $permission)->only($existing);
            }
        }

        foreach ($extra as $permission => $methods) {
            $this->middleware('can:' . $permission)->only((array) $methods);
        }
    }
}
