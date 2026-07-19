@extends('layouts.admin')
@section('title', 'Workers')
@section('content')
<div class="row">
    <div class="col-12 d-none" id="workerFormSection">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <h4 class="card-title mb-25" id="formTitle">Add Worker</h4>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" id="closeFormBtn">Close</button>
                </div>
            </div>
            <div class="card-body pt-2">
                <form id="workerForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="worker_id" id="worker_id">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Worker Name" required>
                                <span class="text-danger error-text name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number">
                                <span class="text-danger error-text phone_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email Address">
                                <span class="text-danger error-text email_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="nid">NID</label>
                                <input type="text" name="nid" id="nid" class="form-control" placeholder="NID">
                                <span class="text-danger error-text nid_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="address">Address</label>
                                <input type="text" name="address" id="address" class="form-control" placeholder="Address">
                                <span class="text-danger error-text address_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="description">Description</label>
                                <input type="text" name="description" id="description" class="form-control" placeholder="Description">
                                <span class="text-danger error-text description_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="type">Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <option value="1">Finishing Part</option>
                                    <option value="2">Body Part</option>
                                </select>
                                <span class="text-danger error-text type_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="status">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <span class="text-danger error-text status_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="password">Password <span class="text-danger" id="password_req">*</span></label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                                <span class="text-danger error-text password_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="password_confirmation">Confirm Password <span class="text-danger" id="password_conf_req">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Confirm Password">
                                <span class="text-danger error-text password_confirmation_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="profile_image">Profile Image</label>
                                <input type="file" name="profile_image" id="profile_image" class="form-control-file">
                                <span class="text-danger error-text profile_image_error"></span>
                                <div id="preview_image" class="mt-1"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2 text-right">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Worker</button>
                        <button type="button" class="btn btn-outline-secondary" id="cancelBtn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Worker List</h4>
                    <div>
                        <button type="button" class="btn btn-outline-secondary mr-50" id="resetFilterBtn">
                            <i data-feather="refresh-cw"></i> Reset Filter
                        </button>
                        <button type="button" class="btn btn-primary" id="showAddWorkerBtn">
                            <i data-feather="plus"></i> Add Worker
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="card-body pt-2 pb-0">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" id="filter_name" class="form-control" placeholder="Search by Name">
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="filter_phone" class="form-control" placeholder="Search by Phone">
                    </div>
                    <div class="col-md-3">
                        <select id="filter_type" class="form-control">
                            <option value="">All Types</option>
                            <option value="1">Finishing Part</option>
                            <option value="2">Body Part</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filter_status" class="form-control">
                            <option value="">All Status</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-body table-responsive pt-2">
                <table id="workerTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Type</th>
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

        var table = $('#workerTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.worker.index') }}",
                data: function (d) {
                    d.name = $('#filter_name').val();
                    d.phone = $('#filter_phone').val();
                    d.type = $('#filter_type').val();
                    d.status = $('#filter_status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'image', name: 'image', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'type_label', name: 'type_label'},
                {data: 'status_label', name: 'status_label'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        $('#filter_name, #filter_phone, #filter_type, #filter_status').on('change keyup', function() {
            table.draw();
        });

        $('#resetFilterBtn').on('click', function() {
            $('#filter_name, #filter_phone, #filter_type, #filter_status').val('');
            table.draw();
        });

        $('#showAddWorkerBtn').on('click', function() {
            resetWorkerForm();
            openWorkerForm('add');
        });

        $('#closeFormBtn, #cancelBtn').on('click', function() {
            closeWorkerForm();
        });

        $('#workerForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            var id = $('#worker_id').val();
            var url = "{{ route('admin.worker.store') }}";
            var formData = new FormData(this);

            if (id) {
                url = "{{ url('admin/worker') }}" + "/" + id;
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    toastr.success(data.success);
                    table.draw(false);
                    closeWorkerForm();
                },
                error: function (data) {
                    $('#saveBtn').text($('#worker_id').val() ? 'Update Worker' : 'Save Worker').attr('disabled', false);
                    if (data.status === 422) {
                        $.each(data.responseJSON.errors, function(prefix, val){
                            var field = prefix.replace(/\./g, '_');
                            $('span.' + field + '_error').text(val[0]);
                        });
                        toastr.error('Validation error. Please check fields.');
                        return;
                    }

                    toastr.error((data.responseJSON && data.responseJSON.error) || 'Something went wrong.');
                }
            });
        });

        $('body').on('click', '.editWorker', function () {
            var id = $(this).data('id');

            $.get("{{ url('admin/worker') }}" + '/' + id + '/edit', function (data) {
                resetWorkerForm();
                openWorkerForm('edit');

                $('#worker_id').val(data.id);
                $('#name').val(data.name);
                $('#phone').val(data.phone);
                $('#email').val(data.email);
                $('#nid').val(data.nid);
                $('#address').val(data.address);
                $('#description').val(data.description);
                $('#type').val(data.type);
                $('#status').val(data.status);
                
                $('#password_req').hide();
                $('#password_conf_req').hide();
                $('#password').removeAttr('required');
                $('#password_confirmation').removeAttr('required');

                if (data.profile_image) {
                    $('#preview_image').html('<img src="' + "{{ asset('storage') }}/" + data.profile_image + '" width="100" style="object-fit:cover;">');
                } else {
                    $('#preview_image').html('');
                }

                refreshIcons();
            });
        });

        $('body').on('click', '.deleteWorker', function () {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Delete this worker?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-secondary ml-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    type: 'DELETE',
                    url: "{{ url('admin/worker') }}" + '/' + id,
                    success: function (data) {
                        table.draw(false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: data.success,
                            customClass: {
                                confirmButton: 'btn btn-success'
                            },
                            buttonsStyling: false
                        });
                    },
                    error: function (data) {
                        toastr.error((data.responseJSON && data.responseJSON.error) || 'Error deleting worker.');
                    }
                });
            });
        });

        function openWorkerForm(mode) {
            $('#workerFormSection').removeClass('d-none');
            $('#formTitle').text(mode === 'edit' ? 'Edit Worker' : 'Add Worker');
            $('#saveBtn')
                .text(mode === 'edit' ? 'Update Worker' : 'Save Worker')
                .attr('disabled', false);

            if(mode === 'add') {
                $('#password_req').show();
                $('#password_conf_req').show();
                $('#password').attr('required', 'required');
                $('#password_confirmation').attr('required', 'required');
            }

            $('html, body').animate({
                scrollTop: $('#workerFormSection').offset().top - 80
            }, 250);
            refreshIcons();
        }

        function closeWorkerForm() {
            resetWorkerForm();
            $('#workerFormSection').addClass('d-none');
        }

        function resetWorkerForm() {
            $('#workerForm').trigger('reset');
            $('#worker_id').val('');
            $('.error-text').text('');
            $('#preview_image').html('');
            $('#saveBtn').text('Save Worker').attr('disabled', false);
            
            $('#password_req').show();
            $('#password_conf_req').show();
            $('#password').attr('required', 'required');
            $('#password_confirmation').attr('required', 'required');
        }

        function refreshIcons() {
            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
        }

        $('#profile_image').on('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_image').html('<img src="' + e.target.result + '" width="100" style="object-fit:cover; margin-top: 10px;">');
                }
                reader.readAsDataURL(file);
            } else {
                $('#preview_image').html('');
            }
        });
    });
</script>
@endpush
