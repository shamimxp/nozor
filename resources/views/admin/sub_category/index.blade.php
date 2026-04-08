@extends('layouts.admin')
@section('title', 'Sub Categories')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title" id="formTitle">Add Sub Category</h4>
            </div>
            <div class="card-body pt-2">
                <form id="subCategoryForm">
                    @csrf
                    <input type="hidden" name="sub_category_id" id="sub_category_id">
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select name="category_id" id="category_id_select" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text category_id_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Sub Category Name" required>
                        <span class="text-danger error-text name_error"></span>
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
                <h4 class="card-title">Sub Category List</h4>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="subCategoryTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
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

        var table = $('#subCategoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.sub-category.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'category', name: 'category'},
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

        $('#subCategoryForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            let url = "{{ route('admin.sub-category.store') }}";
            let method = "POST";
            if($('#sub_category_id').val()){
                url = "{{ url('admin/sub-category') }}" + "/" + $('#sub_category_id').val();
                method = "PUT";
            }

            $.ajax({
                data: $(this).serialize(),
                url: url,
                type: method,
                dataType: 'json',
                success: function (data) {
                    $('#subCategoryForm').trigger("reset");
                    $('#sub_category_id').val('');
                    $('#formTitle').text('Add Sub Category');
                    $('#saveBtn').text('Save').attr('disabled', false);
                    $('#cancelBtn').addClass('d-none');
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

        $('body').on('click', '.editSubCategory', function () {
            var id = $(this).data('id');
            $.get("{{ url('admin/sub-category') }}" +'/' + id + '/edit', function (data) {
                $('#formTitle').text('Edit Sub Category');
                $('#saveBtn').text('Update');
                $('#cancelBtn').removeClass('d-none');
                $('#sub_category_id').val(data.id);
                $('#category_id_select').val(data.category_id);
                $('#name').val(data.name);
            })
        });

        $('#cancelBtn').on('click', function(){
            $('#subCategoryForm').trigger("reset");
            $('#sub_category_id').val('');
            $('#formTitle').text('Add Sub Category');
            $('#saveBtn').text('Save');
            $(this).addClass('d-none');
            $('.error-text').text('');
        });

        $('body').on('click', '.deleteSubCategory', function () {
            var id = $(this).data("id");
            if(confirm("Are You sure want to delete?")){
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('admin/sub-category') }}"+'/'+id,
                    success: function (data) {
                        table.draw();
                        toastr.success(data.success);
                    },
                    error: function (data) {
                        console.log('Error:', data);
                    }
                });
            }
        });

        $('body').on('change', '.changeStatus', function() {
            var id = $(this).data('id');
            var status = $(this).prop('checked') == true ? 1 : 0;
            $.ajax({
                type: "POST",
                url: "{{ route('admin.sub_category.status') }}",
                data: { 'id': id, 'status': status },
                success: function(data) {
                    toastr.success(data.success);
                }
            });
        });
    });
</script>
@endpush
