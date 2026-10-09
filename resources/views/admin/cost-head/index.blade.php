@extends('layouts.admin')
@section('title', 'Cost Head')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title" id="form-title">Add Cost Head</h4>
            </div>
            <div class="card-body mt-2">
                <form id="saveForm">
                    @csrf
                    <input type="hidden" name="id" id="cost_head_id">
                    <div class="form-group mb-1">
                        <label for="title" class="font-weight-bold">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="title" placeholder="Ex: Body Charge, Wire Price, Pitol Price" required>
                        <span class="text-danger error-text title_error font-small-2"></span>
                    </div>
                    <div class="form-group mb-2">
                        <label class="font-weight-bold d-block">Status</label>
                        <div class="custom-control custom-switch custom-switch-success">
                            <input type="checkbox" class="custom-control-input" id="form_status" name="status" value="1" checked>
                            <label class="custom-control-label" for="form_status">
                                <span class="switch-icon-left"><i data-feather="check"></i></span>
                                <span class="switch-icon-right"><i data-feather="x"></i></span>
                            </label>
                        </div>
                    </div>
                    <div class="d-flex" style="gap: 10px;">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                        <button type="button" class="btn btn-outline-secondary d-none" id="cancelBtn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Cost Head List</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped data-table w-100">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">SL</th>
                                <th>Title</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 100px;">Action</th>
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
            ajax: "{{ route('admin.cost-head.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'title', name: 'title'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
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

        function resetForm() {
            $('#saveForm').trigger("reset");
            $('#cost_head_id').val('');
            $('#form_status').prop('checked', true);
            $('.error-text').text('');
            $('#form-title').text('Add Cost Head');
            $('#saveBtn').text('Save').attr('disabled', false);
            $('#cancelBtn').addClass('d-none');
        }

        $('#saveForm').submit(function(e) {
            e.preventDefault();
            $('.error-text').text('');
            $('#saveBtn').text('Saving...').attr('disabled', true);

            let id = $('#cost_head_id').val();
            let url = id ? "{{ url('admin/cost-head') }}/" + id : "{{ route('admin.cost-head.store') }}";
            let type = id ? "PUT" : "POST";

            let formData = {
                title: $('#title').val(),
                status: $('#form_status').is(':checked') ? 1 : 0
            };

            $.ajax({
                type: type,
                url: url,
                data: formData,
                success: function(response) {
                    resetForm();
                    table.draw();
                    toastr.success(response.success);
                },
                error: function(error) {
                    $('#saveBtn').text(id ? 'Update' : 'Save').attr('disabled', false);
                    if (error.responseJSON && error.responseJSON.errors) {
                        $.each(error.responseJSON.errors, function(key, value) {
                            $('.' + key + '_error').text(value[0]);
                            toastr.error(value[0]);
                        });
                    } else if (error.responseJSON && error.responseJSON.message) {
                        toastr.error(error.responseJSON.message);
                    }
                }
            });
        });

        $('body').on('click', '.editBtn', function() {
            let id = $(this).data('id');
            let title = $(this).data('title');
            let status = $(this).data('status');

            $('#cost_head_id').val(id);
            $('#title').val(title);
            $('#form_status').prop('checked', status == 1);
            $('.error-text').text('');
            $('#form-title').text('Edit Cost Head');
            $('#saveBtn').text('Update').attr('disabled', false);
            $('#cancelBtn').removeClass('d-none');

            $('html, body').animate({
                scrollTop: $("#saveForm").offset().top - 100
            }, 300);
        });

        $('#cancelBtn').click(function() {
            resetForm();
        });

        $('body').on('click', '.deleteBtn', function() {
            let id = $(this).data('id');
            if (confirm('Are you sure you want to delete this cost head?')) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('admin/cost-head') }}/" + id,
                    success: function(response) {
                        table.draw();
                        toastr.success(response.success);
                    },
                    error: function(xhr) {
                        toastr.error('Failed to delete.');
                    }
                });
            }
        });

        $('body').on('change', '.changeStatus', function() {
            let status = $(this).prop('checked') ? 1 : 0;
            let id = $(this).data('id');
            $.ajax({
                type: "POST",
                url: "{{ route('admin.cost-head.status') }}",
                data: {
                    'id': id,
                    'status': status
                },
                success: function(response) {
                    toastr.success(response.success);
                },
                error: function() {
                    toastr.error('Failed to update status.');
                }
            });
        });
    });
</script>
@endpush
