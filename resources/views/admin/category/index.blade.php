@extends('layouts.admin')
@section('title', 'Categories')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title" id="formTitle">Add Category</h4>
            </div>
            <div class="card-body pt-2">
                <form id="categoryForm">
                    @csrf
                    <input type="hidden" name="category_id" id="category_id">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Category Name" required>
                        <span class="text-danger error-text name_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="image">Image</label>
                        <input type="file" name="image" id="imageInput" class="form-control" accept="image/*">
                        <span class="text-danger error-text image_error"></span>
                        <div class="mt-1">
                            <img id="imagePreview" src="{{ asset('images/no-image.png') }}" width="100" class="img-thumbnail" alt="">
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
    <div class="col-md-8">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Category List</h4>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="categoryTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Slug</th>
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

        var table = $('#categoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.category.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'image', name: 'image', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'slug', name: 'slug'},
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

        // Image preview
        $('#imageInput').on('change', function(){
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#imagePreview').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        $('#categoryForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            let id = $('#category_id').val();
            let url = "{{ route('admin.category.store') }}";
            let formData = new FormData(this);

            if(id){
                url = "{{ url('admin/category') }}" + "/" + id;
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST', // Use POST for both store and update when using FormData
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#categoryForm').trigger("reset");
                    $('#category_id').val('');
                    $('#formTitle').text('Add Category');
                    $('#saveBtn').text('Save').attr('disabled', false);
                    $('#cancelBtn').addClass('d-none');
                    $('#imagePreview').attr('src', "{{ asset('images/no-image.png') }}");
                    table.draw();
                    toastr.success(data.success);
                },
                error: function (data) {
                    $('#saveBtn').text('Save').attr('disabled', false);
                    if(data.status === 422){
                        let errors = data.responseJSON.errors;
                        $.each(errors, function(prefix, val){
                            $('span.'+prefix+'_error').text(val[0]);
                        });
                    }
                }
            });
        });

        $('body').on('click', '.editCategory', function () {
            var id = $(this).data('id');
            $.get("{{ url('admin/category') }}" +'/' + id + '/edit', function (data) {
                $('#formTitle').text('Edit Category');
                $('#saveBtn').text('Update');
                $('#cancelBtn').removeClass('d-none');
                $('#category_id').val(data.id);
                $('#name').val(data.name);
                $('#imagePreview').attr('src', data.image_url);
            })
        });

        $('#cancelBtn').on('click', function(){
            $('#categoryForm').trigger("reset");
            $('#category_id').val('');
            $('#formTitle').text('Add Category');
            $('#saveBtn').text('Save');
            $(this).addClass('d-none');
            $('.error-text').text('');
            $('#imagePreview').attr('src', "{{ asset('images/no-image.png') }}");
        });

        $('body').on('click', '.deleteCategory', function () {
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
                        url: "{{ url('admin/category') }}"+'/'+id,
                        success: function (data) {
                            table.draw();
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.success,
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });
                        },
                        error: function (data) {
                            console.log('Error:', data);
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
                url: "{{ route('admin.category.status') }}",
                data: { 'id': id, 'status': status },
                success: function(data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: data.success,
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
