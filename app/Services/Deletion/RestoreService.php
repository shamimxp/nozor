<?php

namespace App\Services\Deletion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RestoreService
{
    public function __construct(protected DependencyChecker $checker)
    {
    }

    /**
     * Restore a model and every soft-deleted record sharing its deletion_batch_id.
     *
     * @return array{success:bool,message:string,restored:int}
     */
    public function restore(Model $model, string $moduleKey): array
    {
        if (! method_exists($model, 'trashed') || ! $model->trashed()) {
            return ['success' => false, 'message' => 'Record is not in trash.', 'restored' => 0];
        }

        $batchId = $model->deletion_batch_id;
        $restored = 0;

        DB::transaction(function () use ($model, $batchId, &$restored) {
            $model->restore();
            $model->deletion_batch_id = null;
            $model->saveQuietly();
            $restored++;

            if (! $batchId) {
                return;
            }

            foreach (config('deletion.modules', []) as $module) {
                /** @var class-string<Model> $class */
                $class = $module['model'];
                if (! class_exists($class) || ! method_exists($class, 'onlyTrashed')) {
                    continue;
                }

                $class::onlyTrashed()
                    ->where('deletion_batch_id', $batchId)
                    ->get()
                    ->each(function (Model $related) use (&$restored) {
                        $related->restore();
                        $related->deletion_batch_id = null;
                        $related->saveQuietly();
                        $restored++;
                    });
            }
        });

        return [
            'success' => true,
            'message' => 'Record restored successfully.',
            'restored' => $restored,
        ];
    }

    /**
     * @param  array<int, int|string>  $ids
     * @return array{success:bool,message:string,restored:int}
     */
    public function restoreMany(string $moduleKey, array $ids): array
    {
        $module = $this->checker->module($moduleKey);
        /** @var class-string<Model> $class */
        $class = $module['model'];
        $total = 0;

        foreach ($class::onlyTrashed()->whereIn((new $class)->getKeyName(), $ids)->get() as $model) {
            $result = $this->restore($model, $moduleKey);
            $total += $result['restored'];
        }

        return [
            'success' => true,
            'message' => "{$total} record(s) restored successfully.",
            'restored' => $total,
        ];
    }
}
