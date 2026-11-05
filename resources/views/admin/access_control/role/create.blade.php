@extends('layouts.admin')
@section('page-title','Role Create')
@section('breadcrumb')
    <h2 class="content-header-title float-left mb-0">{{__('Role')}}</h2>
    <div class="breadcrumb-wrapper">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('Home')}}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">{{__('Role')}}</a></li>
            <li class="breadcrumb-item active">{{__('Add New')}}</li>
        </ol>
    </div>
@endsection

@section('content')
    <section class="validations" id="validation">
        <div class="d-flex align-items-center justify-content-between flex-wrap mb-1" style="gap:.75rem;">
            <div>
                <h3 class="mb-25 font-weight-bolder" style="color:#111C38;">{{__('Add Role')}}</h3>
                <p class="mb-0 text-muted font-small-3">Name the role, filter modules, then pick only the permissions this role needs.</p>
            </div>
        </div>

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            @include('admin.access_control.role.partials.permissions_matrix', [
                'permissions' => $permissions,
                'submitLabel' => __('Create Role'),
            ])
        </form>
    </section>
@endsection
