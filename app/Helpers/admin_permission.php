<?php

use Illuminate\Support\Facades\Gate;

if (!function_exists('admin_can')) {
    function admin_can(?string $permission): bool
    {
        if (!$permission) {
            return false;
        }

        try {
            return Gate::check($permission);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('admin_can_any')) {
    function admin_can_any(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (admin_can($permission)) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('perm_html')) {
    /**
     * Return HTML only when the current admin has the permission.
     */
    function perm_html(?string $permission, string $html): string
    {
        return admin_can($permission) ? $html : '';
    }
}

if (!function_exists('admin_module_actions')) {
    /**
     * Build gated action HTML for common CRUD buttons.
     *
     * @param  array<string, string>  $actions  permission-suffix => html (edit/delete/show/create/...)
     */
    function admin_module_actions(string $module, array $actions): string
    {
        $html = '';
        foreach ($actions as $action => $buttonHtml) {
            $permission = str_contains($action, '.') ? $action : ($module . '.' . $action);
            $html .= perm_html($permission, $buttonHtml);
        }

        return $html;
    }
}
