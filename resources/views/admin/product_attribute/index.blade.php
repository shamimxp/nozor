@extends('layouts.admin')
@section('title', 'Product Attributes')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center p-1">
                <h4 class="card-title">Product Attribute List</h4>
                <button type="button" class="btn btn-primary" id="createNewAttribute">
                    <i data-feather="plus"></i> Add New Attribute
                </button>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="attributeTable" class="table table-bordered table-striped">
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

<!-- Modal -->
<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modelHeading"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="attributeForm" name="attributeForm" class="form-horizontal">
                    @csrf
                    <input type="hidden" name="attribute_id" id="attribute_id">
                    <div class="form-group">
                        <label for="name" class="control-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                        <span class="text-danger error-text name_error"></span>
                    </div>

                    <div class="col-sm-offset-2 col-sm-10 mt-2">
                        <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                        </button>
                    </div>
                </form>
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

        var table = $('#attributeTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.product-attribute.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
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

        $('#createNewAttribute').click(function () {
            $('#saveBtn').val("create-attribute");
            $('#attribute_id').val('');
            $('#attributeForm').trigger("reset");
            $('#modelHeading').html("Create New Attribute");
            $('#ajaxModel').modal('show');
            $('.error-text').text('');
        });

        $('body').on('click', '.editAttribute', function () {
            var id = $(this).data('id');
            $.get("{{ url('admin/product-attribute') }}" +'/' + id + '/edit', function (data) {
                $('#modelHeading').html("Edit Attribute");
                $('#saveBtn').val("edit-attribute");
                $('#ajaxModel').modal('show');
                $('#attribute_id').val(data.id);
                $('#name').val(data.name);
                $('.error-text').text('');
            })
        });

        $('#attributeForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            let id = $('#attribute_id').val();
            let url = id ? "{{ url('admin/product-attribute') }}" + "/" + id : "{{ route('admin.product-attribute.store') }}";
            let method = id ? "PATCH" : "POST";

            if (id) {
               // Use _method for PUT/PATCH in Laravel if sending via POST
               var formData = $(this).serialize() + "&_method=PATCH";
            } else {
               var formData = $(this).serialize();
            }

            $.ajax({
                data: formData,
                url: url,
                type: "POST", // Always use POST with _method spoofing for wider compatibility
                dataType: 'json',
                success: function (data) {
                    $('#attributeForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    $('#saveBtn').text('Save changes').attr('disabled', false);
                    table.draw();
                    toastr.success(data.success);
                },
                error: function (data) {
                    $('#saveBtn').text('Save changes').attr('disabled', false);
                    if(data.status === 422){
                        let errors = data.responseJSON.errors;
                        $.each(errors, function(prefix, val){
                            $('span.'+prefix+'_error').text(val[0]);
                        });
                    }
                }
            });
        });

        $('body').on('click', '.deleteAttribute', function () {
            var id = $(this).data("id");
            if(confirm("Are You sure want to delete?")){
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('admin/product-attribute') }}"+'/'+id,
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
                url: "{{ route('admin.product_attribute.status') }}",
                data: { 'id': id, 'status': status },
                success: function(data) {
                    toastr.success(data.success);
                }
            });
        });
    });
</script>
@endpush
