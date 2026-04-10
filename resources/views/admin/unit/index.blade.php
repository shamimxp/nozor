@extends('layouts.admin')
@section('title', 'Units')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title" id="formTitle">Add Unit</h4>
            </div>
            <div class="card-body pt-2">
                <form id="unitForm">
                    @csrf
                    <input type="hidden" name="unit_id" id="unit_id">
                    <div class="form-group">
                        <label for="name">Unit Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Ex: KG, PC, Ltr" required>
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
                <h4 class="card-title">Unit List</h4>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="unitTable" class="table table-bordered table-striped">
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

        var table = $('#unitTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.unit.index') }}",
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

        $('#unitForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            let id = $('#unit_id').val();
            let url = "{{ route('admin.unit.store') }}";
            let type = 'POST';

            if(id){
                url = "{{ url('admin/unit') }}" + "/" + id;
                type = 'PUT';
            }

            $.ajax({
                url: url,
                type: type,
                data: $(this).serialize(),
                success: function (data) {
                    $('#unitForm').trigger("reset");
                    $('#unit_id').val('');
                    $('#formTitle').text('Add Unit');
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

        $('body').on('click', '.editUnit', function () {
            var id = $(this).data('id');
            $.get("{{ url('admin/unit') }}" +'/' + id + '/edit', function (data) {
                $('#formTitle').text('Edit Unit');
                $('#saveBtn').text('Update');
                $('#cancelBtn').removeClass('d-none');
                $('#unit_id').val(data.id);
                $('#name').val(data.name);
            })
        });

        $('#cancelBtn').on('click', function(){
            $('#unitForm').trigger("reset");
            $('#unit_id').val('');
            $('#formTitle').text('Add Unit');
            $('#saveBtn').text('Save');
            $(this).addClass('d-none');
            $('.error-text').text('');
        });

        $('body').on('click', '.deleteUnit', function () {
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
                        url: "{{ url('admin/unit') }}"+'/'+id,
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
        });

        $('body').on('change', '.changeStatus', function() {
            var id = $(this).data('id');
            var status = $(this).prop('checked') == true ? 1 : 0;
            $.ajax({
                type: "POST",
                url: "{{ route('admin.unit.status') }}",
                data: { 'id': id, 'status': status },
                success: function(data) {
                    toastr.success(data.success);
                }
            });
        });
    });
</script>
@endpush
