<?php

namespace App\Services\Deletion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CascadeSoftDeleteService
{
    public function __construct(protected DependencyChecker $checker)
    {
    }

    /**
     * Soft-delete owned child relations recursively.
     *
     * @param  array<string, bool>  $visited
     */
    public function softDeleteOwned(Model $model, string $moduleKey, string $batchId, array &$visited = []): void
    {
        $visitKey = $model::class . ':' . $model->getKey();
        if (isset($visited[$visitKey])) {
            return;
        }
        $visited[$visitKey] = true;

        $module = $this->checker->module($moduleKey);

        foreach ($module['owned'] ?? [] as $owned) {
            $relationName = $owned['relation'];
            if (! method_exists($model, $relationName)) {
                continue;
            }

            $results = $model->{$relationName}()->getResults();
            $children = $results instanceof Model ? collect([$results]) : collect($results ?? []);

            foreach ($children as $child) {
                if (! $child instanceof Model || ! $this->usesSoftDeletes($child)) {
                    continue;
                }
                if ($child->trashed()) {
                    continue;
                }

                $childModuleKey = $this->resolveModuleKey($child);
                if ($childModuleKey) {
                    $this->softDeleteOwned($child, $childModuleKey, $batchId, $visited);
                }

                $child->deletion_batch_id = $batchId;
                $child->deleted_by = auth(config('deletion.guard', 'admin'))->id();
                $child->delete();
            }
        }
    }

    /**
     * Soft-delete active blockers then ensure parent is soft-deleted (same batch).
     */
    public function softDeleteBlockers(Model $model, string $moduleKey, ?string $batchId = null): string
    {
        $batchId = $batchId ?: (string) Str::uuid();
        $module = $this->checker->module($moduleKey);
        $visited = [];

        foreach ($module['blockers'] ?? [] as $blocker) {
            $relationName = $blocker['relation'];
            if (! method_exists($model, $relationName)) {
                continue;
            }

            foreach ($model->{$relationName}()->get() as $child) {
                if (! $child instanceof Model || ! $this->usesSoftDeletes($child) || $child->trashed()) {
                    continue;
                }

                $childKey = $this->resolveModuleKey($child);
                if ($childKey) {
                    $this->softDeleteOwned($child, $childKey, $batchId, $visited);
                }

                $child->deletion_batch_id = $batchId;
                $child->deleted_by = auth(config('deletion.guard', 'admin'))->id();
                $child->delete();
            }
        }

        if (! $model->trashed()) {
            $this->softDeleteOwned($model, $moduleKey, $batchId, $visited);
            $model->deletion_batch_id = $batchId;
            $model->deleted_by = auth(config('deletion.guard', 'admin'))->id();
            $model->delete();
        }

        return $batchId;
    }

    public function usesSoftDeletes(Model $model): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($model), true)
            || in_array(\App\Traits\TracksSoftDeletion::class, class_uses_recursive($model), true);
    }

    protected function resolveModuleKey(Model $model): ?string
    {
        foreach (config('deletion.modules', []) as $key => $module) {
            if (($module['model'] ?? null) === $model::class) {
                return $key;
            }
        }

        return null;
    }
}
