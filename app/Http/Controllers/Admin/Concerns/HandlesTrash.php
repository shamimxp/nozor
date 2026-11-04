<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Services\Deletion\ForceDeleteService;
use App\Services\Deletion\RestoreService;
use App\Services\Deletion\SoftDeleteService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

trait HandlesTrash
{
    abstract protected function trashModuleKey(): string;

    /**
     * @return class-string<Model>
     */
    protected function trashModelClass(): string
    {
        return config('deletion.modules.' . $this->trashModuleKey() . '.model');
    }

    protected function trashDisplayColumn(): string
    {
        return config('deletion.modules.' . $this->trashModuleKey() . '.display', 'id');
    }

    public function trash(Request $request)
    {
        $class = $this->trashModelClass();

        if ($request->ajax()) {
            $query = $class::onlyTrashed()->with('deletedByUser')->latest('deleted_at');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="trash-row-check" value="' . $row->getKey() . '">';
                })
                ->addColumn('record_label', function ($row) {
                    $col = $this->trashDisplayColumn();

                    return e(data_get($row, $col, $row->getKey()));
                })
                ->addColumn('deleted_by_name', function ($row) {
                    return e(optional($row->deletedByUser)->name ?? '—');
                })
                ->addColumn('deleted_at_formatted', function ($row) {
                    return $row->deleted_at ? $row->deleted_at->format('d M Y h:i A') : '—';
                })
                ->addColumn('action', function ($row) {
                    return view('admin.partials.trash_row_actions', [
                        'id' => $row->getKey(),
                        'module' => $this->trashModuleKey(),
                    ])->render();
                })
                ->rawColumns(['checkbox', 'action'])
                ->make(true);
        }

        return view('admin.partials.trash_index', [
            'module' => $this->trashModuleKey(),
            'title' => config('deletion.modules.' . $this->trashModuleKey() . '.label') . ' Trash',
            'backRoute' => $this->trashBackRoute(),
            'trashRoute' => route('admin.' . $this->trashRoutePrefix() . '.trash'),
            'restoreRoute' => route('admin.' . $this->trashRoutePrefix() . '.restore', ['id' => '__ID__']),
            'forceRoute' => route('admin.' . $this->trashRoutePrefix() . '.force-delete', ['id' => '__ID__']),
            'bulkRestoreRoute' => route('admin.' . $this->trashRoutePrefix() . '.bulk-restore'),
            'bulkForceRoute' => route('admin.' . $this->trashRoutePrefix() . '.bulk-force-delete'),
        ]);
    }

    protected function trashBackRoute(): string
    {
        return route('admin.' . $this->trashRoutePrefix() . '.index');
    }

    protected function trashRoutePrefix(): string
    {
        return config(
            'deletion.modules.' . $this->trashModuleKey() . '.route',
            str_replace('_', '-', $this->trashModuleKey())
        );
    }

    public function restoreTrash($id, RestoreService $restoreService)
    {
        $model = $this->trashModelClass()::onlyTrashed()->findOrFail($id);
        $result = $restoreService->restore($model, $this->trashModuleKey());

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function forceDeleteTrash($id, ForceDeleteService $forceDeleteService)
    {
        $model = $this->trashModelClass()::withTrashed()->findOrFail($id);
        $result = $forceDeleteService->forceDelete($model, $this->trashModuleKey());

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function bulkRestoreTrash(Request $request, RestoreService $restoreService)
    {
        $ids = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ])['ids'];

        $result = $restoreService->restoreMany($this->trashModuleKey(), $ids);

        return response()->json($result);
    }

    public function bulkForceDeleteTrash(Request $request, ForceDeleteService $forceDeleteService)
    {
        $ids = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ])['ids'];

        $result = $forceDeleteService->forceDeleteMany($this->trashModuleKey(), $ids);

        return response()->json($result);
    }

    protected function softDeleteRecord($id, SoftDeleteService $softDeleteService)
    {
        $model = $this->trashModelClass()::findOrFail($id);
        $result = $softDeleteService->delete($model, $this->trashModuleKey());

        return response()->json($result, $result['success'] ? 200 : 422);
    }
}
