@can('role.edit')
    <a href="{{ route('roles.edit', encrypt($role->id)) }}" class="btn btn-sm btn-success d-inline-flex align-items-center">
        <i data-feather="edit-2" class="mr-50" style="width:14px;height:14px;"></i>
        {{ __('Edit') }}
    </a>
@endcan
