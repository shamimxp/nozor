@extends('layouts.admin')
@section('content')
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom p-1">
                        <div class="head-label">
                            <h4 class="mb-0">{{__('User List')}}</h4>
                        </div>
                        <div class="dt-action-buttons text-right">
                            <div class="dt-buttons d-inline-flex">
                                @can('user.add')
                                    <a href="{{route('admin.user.create')}}" class="btn btn-primary"><i
                                            class="fas fa-plus"></i>{{__('Add New')}}</a>
                                @endcan
                                @can('user.delete')
                                    <a href="{{route('admin-user.deleted')}}" class="btn btn-danger ml-1"><i
                                            class="far fa-trash-alt pr-1"></i>{{__('Deleted Data')}}</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="dataTable" class="datatables-basic table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Phone Number')}}</th>
                                <th>{{__('Email')}}</th>
                                <th>{{__('Roles')}}</th>
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
            var table = $('#dataTable').DataTable({
                serverSide: true,
                processing: true,
                responsive: true,
                ajax: '{{ route('admin.user') }}',
                columns: [
                    {data: "DT_RowIndex", name: "DT_RowIndex", searchable: false, orderable: false},
                    {data: "name"},
                    {data: "phone_number"},
                    {data: "email"},
                    {data: "roles", searchable: false, orderable: false},
                    {data: "action", searchable: false, orderable: false},
                ],
            });

            $(document).on('click', '.delete-user-btn', function (e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var name = $(this).data('name');
                Swal.fire({
                    title: 'Delete user?',
                    text: 'Are you sure you want to delete "' + name + '"? This will move the user to the trash.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-outline-primary ml-1'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        })
    </script>
@endpush
