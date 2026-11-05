<div class="dropdown">
    <button type="button" class="btn btn-sm dropdown-toggle hide-arrow py-0" data-toggle="dropdown">
        <i data-feather="more-vertical"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-right">
        @can('manufacture.show')
        <a class="dropdown-item" href="{{ route('admin.manufacture.show', $manufacture->id) }}">
            <i data-feather="eye" class="mr-50"></i>
            <span>View</span>
        </a>
        @endcan
        @if(!$manufacture->is_confirm)
        @can('manufacture.edit')
        <a class="dropdown-item" href="{{ route('admin.manufacture.edit', $manufacture->id) }}">
            <i data-feather="edit-2" class="mr-50"></i>
            <span>Edit</span>
        </a>
        @endcan
        @can('manufacture.delete')
        <a class="dropdown-item delete-record" href="javascript:void(0);" data-id="{{ $manufacture->id }}">
            <i data-feather="trash" class="mr-50"></i>
            <span>Delete</span>
        </a>
        @endcan
        @endif
    </div>
</div>
