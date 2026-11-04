<?php

namespace App\Services\Deletion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SoftDeleteService
{
    public function __construct(
        protected DependencyChecker $checker,
        protected CascadeSoftDeleteService $cascade
    ) {
    }

    /**
     * Soft-delete a record when it has no active blockers.
     *
     * @return array{success:bool,message:string,batch_id?:string,blockers?:array}
     */
    public function delete(Model $model, string $moduleKey, ?string $batchId = null): array
    {
        $blockers = $this->checker->blockers($model, $moduleKey);
        if ($blockers->isNotEmpty()) {
            return [
                'success' => false,
                'message' => $this->checker->message($blockers),
                'blockers' => $blockers->all(),
            ];
        }

        $batchId = $batchId ?: (string) Str::uuid();

        DB::transaction(function () use ($model, $moduleKey, $batchId) {
            $visited = [];
            $this->cascade->softDeleteOwned($model, $moduleKey, $batchId, $visited);
            $model->deletion_batch_id = $batchId;
            $model->deleted_by = auth(config('deletion.guard', 'admin'))->id();
            $model->delete();
        });

        return [
            'success' => true,
            'message' => 'Record moved to trash successfully.',
            'batch_id' => $batchId,
        ];
    }
}
