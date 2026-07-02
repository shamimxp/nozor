@extends('layouts.admin')
@section('title', 'Raw Material Products')
@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title" id="formTitle">Add Raw Material</h4>
            </div>
            <div class="card-body pt-2">
                <form id="dataForm">
                    @csrf
                    <input type="hidden" name="id" id="data_id">
                    <div class="form-group">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Raw Material Name" required>
                        <span class="text-danger error-text name_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="unit_id">Unit</label>
                        <select name="unit_id" id="unit_id" class="form-control select2">
                            <option value="">Select Unit</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-text unit_id_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="price_per_unit">Price Per Unit (৳) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price_per_unit" id="price_per_unit" class="form-control" value="0" required>
                        <span class="text-danger error-text price_per_unit_error"></span>
                    </div>
                    <div class="form-group">
                        <label for="grade_value">Grade Value<span class="text-danger">*</span></label>
                        <input type="text" name="grade_value" id="grade_value" class="form-control" value="0" required>
                        <span class="text-danger error-text grade_value_error"></span>
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
                <h4 class="card-title">Raw Material List</h4>
            </div>
            <div class="card-body border-bottom p-1">
                <form id="filterForm">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label>Name</label>
                                <input type="text" id="filter_name" class="form-control" placeholder="Search by name...">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label>Unit</label>
                                <select id="filter_unit" class="form-control select2">
                                    <option value="">All Units</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                            <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="dataTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Unit</th>
                            <th>Grade Value</th>
                            <th>Price</th>
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

        $('.select2').select2();

        var table = $('#dataTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.raw-material-product.index') }}",
                data: function (d) {
                    d.name = $('#filter_name').val();
                    d.unit_id = $('#filter_unit').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'unit_name', name: 'unit_name'},
                {data: 'grade_value', name: 'grade_value'},
                {data: 'price', name: 'price'},
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

        $('#filterBtn').click(function() {
            table.draw();
        });

        $('#resetBtn').click(function() {
            $('#filterForm')[0].reset();
            $('#filter_unit').val('').trigger('change');
            table.draw();
        });

        $('#dataForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            let id = $('#data_id').val();
            let url = "{{ route('admin.raw-material-product.store') }}";
            let formData = new FormData(this);

            if(id){
                url = "{{ url('admin/raw-material-product') }}" + "/" + id;
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#dataForm').trigger("reset");
                    $('#unit_id').val('').trigger('change');
                    $('#data_id').val('');
                    $('#formTitle').text('Add Raw Material');
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

        $('body').on('click', '.editData', function () {
            var id = $(this).data('id');
            $.get("{{ url('admin/raw-material-product') }}" +'/' + id + '/edit', function (data) {
                $('#formTitle').text('Edit Raw Material');
                $('#saveBtn').text('Update');
                $('#cancelBtn').removeClass('d-none');
                $('#data_id').val(data.id);
                $('#name').val(data.name);
                $('#unit_id').val(data.unit_id).trigger('change');
                $('#price_per_unit').val(data.price_per_unit);
                $('#grade_value').val(data.grade_value);
            })
        });

        $('#cancelBtn').on('click', function(){
            $('#dataForm').trigger("reset");
            $('#unit_id').val('').trigger('change');
            $('#data_id').val('');
            $('#formTitle').text('Add Raw Material');
            $('#saveBtn').text('Save');
            $(this).addClass('d-none');
            $('.error-text').text('');
        });

        $('body').on('click', '.deleteData', function () {
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
                        url: "{{ url('admin/raw-material-product') }}"+'/'+id,
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
                url: "{{ route('admin.raw_material_product.status') }}",
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
