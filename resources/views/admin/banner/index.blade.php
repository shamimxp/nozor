@extends('layouts.admin')
@section('title', 'Banners')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title" id="formTitle">Add Banner</h4>
            </div>
            <div class="card-body pt-2">
                <form id="bannerForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="banner_id" id="banner_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="title">Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" id="title" class="form-control" placeholder="Banner Title" required>
                                <span class="text-danger error-text title_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="sub_title">Sub Title</label>
                                <input type="text" name="sub_title" id="sub_title" class="form-control" placeholder="Banner Sub Title">
                                <span class="text-danger error-text sub_title_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="image">Image <span class="text-danger">*</span> (2378 x 807)</label>
                                <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
                                <span class="text-danger error-text image_error"></span>
                                <div id="imagePreview" class="mt-1 d-none">
                                    <img src="" width="150" class="img-thumbnail" alt="Image Preview">
                                </div>
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
                <h4 class="card-title">Banner List</h4>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="bannerTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Sub Title</th>
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

        var table = $('#bannerTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.banner.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'image_preview', name: 'image', orderable: false, searchable: false},
                {data: 'title', name: 'title'},
                {data: 'sub_title', name: 'sub_title'},
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

        $('#image').change(function(){
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#imagePreview').removeClass('d-none');
                $('#imagePreview img').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        $('#bannerForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            let id  = $('#banner_id').val();
            let url = "{{ route('admin.banner.store') }}";
            let formData = new FormData(this);

            if (id) {
                url = "{{ url('admin/banner') }}" + "/" + id;
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#bannerForm').trigger("reset");
                    $('#banner_id').val('');
                    $('#formTitle').text('Add Banner');
                    $('#saveBtn').text('Save').attr('disabled', false);
                    $('#cancelBtn').addClass('d-none');
                    $('#imagePreview').addClass('d-none');
                    $('#image').attr('required', true);
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

        $('body').on('click', '.editBanner', function () {
            var id = $(this).data('id');
            $.get("{{ url('admin/banner') }}" + '/' + id + '/edit', function (data) {
                $('#formTitle').text('Edit Banner');
                $('#saveBtn').text('Update');
                $('#cancelBtn').removeClass('d-none');
                $('#banner_id').val(data.id);
                $('#title').val(data.title);
                $('#sub_title').val(data.sub_title);
                $('#status').val(data.status);
                
                $('#image').removeAttr('required');
                $('#imagePreview').removeClass('d-none');
                $('#imagePreview img').attr('src', data.image_url);

                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            });
        });

        $('#cancelBtn').on('click', function () {
            $('#bannerForm').trigger("reset");
            $('#banner_id').val('');
            $('#formTitle').text('Add Banner');
            $('#saveBtn').text('Save');
            $('#image').attr('required', true);
            $('#imagePreview').addClass('d-none');
            $(this).addClass('d-none');
            $('.error-text').text('');
        });

        $('body').on('click', '.deleteBanner', function () {
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
                        url: "{{ url('admin/banner') }}" + '/' + id,
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

        $('body').on('change', '.changeStatus', function() {
            var id = $(this).data('id');
            var status = $(this).prop('checked') == true ? 1 : 0;
            $.ajax({
                type: "POST",
                url: "{{ route('admin.banner.status') }}",
                data: { 'id': id, 'status': status },
                success: function(data) {
                    toastr.success(data.success);
                }
            });
        });
    });
</script>
@endpush
