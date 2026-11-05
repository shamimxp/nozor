@php
    $deletedPermission = $permission ?? null;
@endphp
@if(!$deletedPermission || admin_can($deletedPermission))
<a href="{{ $route }}" class="btn btn-outline-danger btn-sm">
    <i data-feather="trash-2" style="width:14px;height:14px;"></i>
    Show Deleted Data
</a>
@endif
