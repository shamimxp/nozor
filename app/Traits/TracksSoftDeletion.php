<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

trait TracksSoftDeletion
{
    use SoftDeletes;

    public static function bootTracksSoftDeletion(): void
    {
        static::restoring(function ($model) {
            $model->deleted_by = null;
        });
    }

    protected function runSoftDelete(): void
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery())->useWritePdo();

        $time = $this->freshTimestamp();
        $columns = [$this->getDeletedAtColumn() => $this->fromDateTime($time)];

        $this->{$this->getDeletedAtColumn()} = $time;

        if ($this->timestamps && ! is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;
            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        if (empty($this->deleted_by)) {
            $this->deleted_by = auth(config('deletion.guard', 'admin'))->id();
        }
        $columns['deleted_by'] = $this->deleted_by;

        if (empty($this->deletion_batch_id)) {
            $this->deletion_batch_id = (string) Str::uuid();
        }
        $columns['deletion_batch_id'] = $this->deletion_batch_id;

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));
        $this->fireModelEvent('trashed', false);
    }

    public function deletedByUser()
    {
        return $this->belongsTo(\App\Models\Admin\Admin::class, 'deleted_by');
    }
}
