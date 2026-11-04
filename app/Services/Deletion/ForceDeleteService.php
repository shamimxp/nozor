<?php

namespace App\Services\Deletion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ForceDeleteService
{
    public function __construct(
        protected DependencyChecker $checker,
        protected CascadeSoftDeleteService $cascade
    ) {
    }

    /**
     * Permanently delete when safe. If active blockers exist, cascade soft-delete them instead.
     *
     * @return array{success:bool,message:string,mode:string,batch_id?:string}
     */
    public function forceDelete(Model $model, string $moduleKey): array
    {
        $fresh = $model->newQuery()->withTrashed()->findOrFail($model->getKey());
        $blockers = $this->checker->blockers($fresh, $moduleKey);

        if ($blockers->isNotEmpty()) {
            $batchId = DB::transaction(function () use ($fresh, $moduleKey) {
                return $this->cascade->softDeleteBlockers(
                    $fresh,
                    $moduleKey,
                    $fresh->deletion_batch_id ?: (string) Str::uuid()
                );
            });

            return [
                'success' => true,
                'mode' => 'cascade_soft',
                'batch_id' => $batchId,
                'message' => 'This record still has related data. Related records were soft-deleted together instead of permanent deletion.',
            ];
        }

        DB::transaction(function () use ($fresh, $moduleKey) {
            $visited = [];
            $this->forceDeleteOwned($fresh, $moduleKey, $visited);
            $fresh->forceDelete();
        });

        return [
            'success' => true,
            'mode' => 'permanent',
            'message' => 'Record permanently deleted.',
        ];
    }

    /**
     * @param  array<int, int|string>  $ids
     * @return array{success:bool,message:string,permanent:int,cascaded:int}
     */
    public function forceDeleteMany(string $moduleKey, array $ids): array
    {
        $module = $this->checker->module($moduleKey);
        /** @var class-string<Model> $class */
        $class = $module['model'];
        $permanent = 0;
        $cascaded = 0;

        foreach ($class::withTrashed()->whereIn((new $class)->getKeyName(), $ids)->get() as $model) {
            $result = $this->forceDelete($model, $moduleKey);
            if (($result['mode'] ?? '') === 'permanent') {
                $permanent++;
            } else {
                $cascaded++;
            }
        }

        return [
            'success' => true,
            'permanent' => $permanent,
            'cascaded' => $cascaded,
            'message' => "Permanent: {$permanent}, cascade soft-deleted: {$cascaded}.",
        ];
    }

    /**
     * @param  array<string, bool>  $visited
     */
    protected function forceDeleteOwned(Model $model, string $moduleKey, array &$visited): void
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

            $relation = $model->{$relationName}();
            $relatedModel = $relation->getRelated();
            $children = $this->cascade->usesSoftDeletes($relatedModel)
                ? $relation->withTrashed()->get()
                : $relation->get();

            foreach ($children as $child) {
                if (! $child instanceof Model) {
                    continue;
                }

                $childKey = null;
                foreach (config('deletion.modules', []) as $key => $cfg) {
                    if (($cfg['model'] ?? null) === $child::class) {
                        $childKey = $key;
                        break;
                    }
                }

                if ($childKey) {
                    $this->forceDeleteOwned($child, $childKey, $visited);
                }

                if ($this->cascade->usesSoftDeletes($child)) {
                    $child->forceDelete();
                } else {
                    $child->delete();
                }
            }
        }
    }
}
