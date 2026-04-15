@extends('layouts.admin')
@section('title', 'Fabrics')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title" id="formTitle">Add Fabric</h4>
            </div>
            <div class="card-body pt-2">
                <form id="fabricForm">
                    @csrf
                    <input type="hidden" name="fabric_id" id="fabric_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Fabric Name" required>
                                <span class="text-danger error-text name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <span class="text-danger error-text status_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="cancelBtn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Fabric List</h4>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="fabricTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#fabricTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.fabric.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'status_badge', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
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

        $('#fabricForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            let id  = $('#fabric_id').val();
            let url = "{{ route('admin.fabric.store') }}";
            let formData = new FormData(this);

            if (id) {
                url = "{{ url('admin/fabric') }}" + "/" + id;
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#fabricForm').trigger("reset");
                    $('#fabric_id').val('');
                    $('#formTitle').text('Add Fabric');
                    $('#saveBtn').text('Save').attr('disabled', false);
                    $('#cancelBtn').addClass('d-none');
                    table.draw();
                    toastr.success(data.success);
                },
                error: function (data) {
                    $('#saveBtn').text('Save').attr('disabled', false);
                    if (data.status === 422) {
                        let errors = data.responseJSON.errors;
                        $.each(errors, function(prefix, val) {
                            $('span.' + prefix + '_error').text(val[0]);
                        });
                        toastr.error('Validation error. Please check fields.');
                    } else {
                        toastr.error(data.responseJSON.error || 'Something went wrong.');
                    }
                }
            });
        });

        $('body').on('click', '.editFabric', function () {
            var id = $(this).data('id');
            $.get("{{ url('admin/fabric') }}" + '/' + id + '/edit', function (data) {
                $('#formTitle').text('Edit Fabric');
                $('#saveBtn').text('Update');
                $('#cancelBtn').removeClass('d-none');
                $('#fabric_id').val(data.id);
                $('#name').val(data.name);
                $('#status').val(data.status);

                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            });
        });

        $('#cancelBtn').on('click', function () {
            $('#fabricForm').trigger("reset");
            $('#fabric_id').val('');
            $('#formTitle').text('Add Fabric');
            $('#saveBtn').text('Save');
            $(this).addClass('d-none');
            $('.error-text').text('');
        });

        $('body').on('click', '.deleteFabric', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
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
                        url: "{{ url('admin/fabric') }}" + '/' + id,
                        success: function (data) {
                            table.draw();
                            toastr.success(data.success);
                        },
                        error: function () {
                            toastr.error('Error deleting record.');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
