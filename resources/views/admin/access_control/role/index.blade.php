@extends('layouts.admin')
@section('page-title','Role List')
@section('breadcrumb')
    <h2 class="content-header-title float-left mb-0">{{__('Role')}}</h2>
    <div class="breadcrumb-wrapper">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{__('Home')}}</a></li>
            <li class="breadcrumb-item active">{{__('Role')}}</li>
        </ol>
    </div>
@endsection
@section('content')
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom p-1">
                        <div class="head-label">
                            <h4 class="mb-0">{{__('Role List')}}</h4>
                        </div>
                        <div class="dt-action-buttons text-right">
                            <div class="dt-buttons d-inline-flex">
                                @can('role.create')
                                    <a href="{{ route('roles.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                                        <i data-feather="plus" class="mr-50" style="width:14px;height:14px;"></i>
                                        {{ __('Add New') }}
                                    </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="dataTable" class="datatables-basic table">
                            <thead>
                            <tr>
                                <th>Si</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Action')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#dataTable').dataTable({
                serverSide: true,
                processing: true,
                responsive: true,
                ajax: '{{ route('roles.index') }}',
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", searchable: false, orderable: false},
                    {data: "name"},
                    {data: "action", searchable: false, orderable: false},
                ],
                drawCallback: function () {
                    if (window.feather) feather.replace({ width: 14, height: 14 });
                }
            });
        })
    </script>
@endpush
