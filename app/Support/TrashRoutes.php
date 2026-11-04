<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class TrashRoutes
{
    /**
     * Register trash/restore/force-delete routes for a module.
     * Place these BEFORE the resource route so "trash" is not captured as {id}.
     */
    public static function register(string $uriPrefix, string $controller, string $namePrefix): void
    {
        Route::get("/{$uriPrefix}/trash", [$controller, 'trash'])->name("admin.{$namePrefix}.trash");
        Route::post("/{$uriPrefix}/trash/bulk-restore", [$controller, 'bulkRestoreTrash'])->name("admin.{$namePrefix}.bulk-restore");
        Route::delete("/{$uriPrefix}/trash/bulk-force-delete", [$controller, 'bulkForceDeleteTrash'])->name("admin.{$namePrefix}.bulk-force-delete");
        Route::post("/{$uriPrefix}/trash/{id}/restore", [$controller, 'restoreTrash'])->name("admin.{$namePrefix}.restore");
        Route::delete("/{$uriPrefix}/trash/{id}/force-delete", [$controller, 'forceDeleteTrash'])->name("admin.{$namePrefix}.force-delete");
    }
}
