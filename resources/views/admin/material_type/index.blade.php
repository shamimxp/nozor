@extends('layouts.admin')
@section('title', 'Material Types')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title" id="form-title">Add Material Type</h4>
            </div>
            <div class="card-body mt-2">
                <form id="saveForm">
                    @csrf
                    <input type="hidden" name="id" id="type_id">
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Name" required>
                    </div>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                    <button type="button" class="btn btn-secondary d-none" id="cancelBtn">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Material Type List</h4>
                @include('admin.partials.show_deleted_button', ['route' => route('admin.material-type.trash')])
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped data-table w-100">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Status</th>
{{--                                <th>Action</th>--}}
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.material-type.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'status', name: 'status'},
                // {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                }
            }
        });

        $('#saveForm').submit(function(e) {
            e.preventDefault();
            let id = $('#type_id').val();
            let url = id ? "{{ url('admin/material-type') }}/" + id : "{{ route('admin.material-type.store') }}";
            let type = id ? "PUT" : "POST";
            $.ajax({
                type: type,
                url: url,
                data: $(this).serialize(),
                success: function(response) {
                    $('#saveForm').trigger("reset");
                    $('#type_id').val('');
                    $('#form-title').text('Add Material Type');
                    $('#saveBtn').text('Save');
                    $('#cancelBtn').addClass('d-none');
                    table.draw();
                    toastr.success(response.success);
                },
                error: function(error) {
                    if (error.responseJSON.errors) {
                        $.each(error.responseJSON.errors, function(key, value) {
                            toastr.error(value);
                        });
                    }
                }
            });
        });

        $('body').on('click', '.editBtn', function() {
            $('#type_id').val($(this).data('id'));
            $('#name').val($(this).data('name'));
            $('#form-title').text('Edit Material Type');
            $('#saveBtn').text('Update');
            $('#cancelBtn').removeClass('d-none');
        });

        $('#cancelBtn').click(function() {
            $('#saveForm').trigger("reset");
            $('#type_id').val('');
            $('#form-title').text('Add Material Type');
            $('#saveBtn').text('Save');
            $(this).addClass('d-none');
        });

        $('body').on('click', '.deleteBtn', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Move to trash?',
                text: "You can restore this later from Show Deleted Data.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/material-type') }}/" + id,
                        success: function(data) {
                            table.draw();
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message || data.success,
                                customClass: { confirmButton: 'btn btn-success' }
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Cannot delete',
                                text: xhr.responseJSON?.message || 'Something went wrong!',
                                customClass: { confirmButton: 'btn btn-danger' }
                            });
                        }
                    });
                }
            });
        });

        $('body').on('change', '.changeStatus', function() {
            let status = $(this).prop('checked') == true ? 1 : 0;
            let id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "{{ route('admin.material_type.status') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'id': id,
                    'status': status
                },
                success: function(response) {
                    toastr.success(response.success);
                }
            });
        });
    });
</script>
@endpush
