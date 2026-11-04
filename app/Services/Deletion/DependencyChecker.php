<?php

namespace App\Services\Deletion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DependencyChecker
{
    public function module(string $key): array
    {
        $module = config("deletion.modules.{$key}");
        if (! $module) {
            throw new \InvalidArgumentException("Deletion module [{$key}] is not configured.");
        }

        return $module;
    }

    public function blockers(Model $model, string $moduleKey): Collection
    {
        $module = $this->module($moduleKey);
        $found = collect();

        foreach ($module['blockers'] ?? [] as $blocker) {
            $relation = $blocker['relation'];
            if (! method_exists($model, $relation)) {
                continue;
            }

            $related = $model->{$relation}();
            $count = $related->count();
            if ($count > 0) {
                $found->push([
                    'relation' => $relation,
                    'label' => $blocker['label'] ?? $relation,
                    'count' => $count,
                ]);
            }
        }

        return $found;
    }

    public function message(Collection $blockers): string
    {
        if ($blockers->isEmpty()) {
            return '';
        }

        $parts = $blockers->map(fn ($b) => "{$b['label']} ({$b['count']})")->implode(', ');

        return "You cannot delete this record because it is currently associated with other records: {$parts}. Please review the related data before proceeding.";
    }
}
