@can(($module ?? 'category') . '.restore')
<button type="button" class="btn btn-success btn-sm trash-restore-btn mr-25" data-id="{{ $id }}" title="Restore">
    <i data-feather="rotate-ccw"></i>
</button>
@endcan
@can(($module ?? 'category') . '.force-delete')
<button type="button" class="btn btn-danger btn-sm trash-force-btn" data-id="{{ $id }}" title="Delete Forever">
    <i data-feather="trash-2"></i>
</button>
@endcan
