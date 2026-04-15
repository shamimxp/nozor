@extends('layouts.admin')
@section('title', 'Fabric Price Setup')
@section('content')
<div class="row">
    {{-- ===================== FORM CARD ===================== --}}
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title" id="formTitle">Add Fabric Price</h4>
            </div>
            <div class="card-body pt-2">
                <form id="fabricPriceForm">
                    @csrf
                    <input type="hidden" name="fabric_price_id" id="fabric_price_id">
                    <div class="row">
                        {{-- Fabric --}}
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="fabric_id">Fabric <span class="text-danger">*</span></label>
                                <select name="fabric_id" id="fabric_id" class="form-control" required>
                                    <option value="">-- Select Fabric --</option>
                                    @foreach($fabrics as $fabric)
                                        <option value="{{ $fabric->id }}">{{ $fabric->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text fabric_id_error"></span>
                            </div>
                        </div>

                        {{-- Type --}}
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="type">Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="polo">Polo</option>
                                    <option value="t-shirt">T-Shirt</option>
                                </select>
                                <span class="text-danger error-text type_error"></span>
                            </div>
                        </div>

                        {{-- Sleeve --}}
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="sleeve">Sleeve <span class="text-danger">*</span></label>
                                <select name="sleeve" id="sleeve" class="form-control" required>
                                    <option value="">-- Select Sleeve --</option>
                                    <option value="half">Half Sleeve</option>
                                    <option value="full">Full Sleeve</option>
                                </select>
                                <span class="text-danger error-text sleeve_error"></span>
                            </div>
                        </div>

                        {{-- Price --}}
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="price">Price <span class="text-danger">*</span></label>
                                <input type="number" name="price" id="price" class="form-control"
                                       placeholder="0.00" step="0.01" min="0" required>
                                <span class="text-danger error-text price_error"></span>
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="col-md-4">
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

    {{-- ===================== TABLE CARD ===================== --}}
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Fabric Price List</h4>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="fabricPriceTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Fabric</th>
                            <th>Type</th>
                            <th>Sleeve</th>
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
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        // ── DataTable ────────────────────────────────────────────
        var table = $('#fabricPriceTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.fabric-price.index') }}",
            columns: [
                {data: 'DT_RowIndex',      name: 'DT_RowIndex',  orderable: false, searchable: false},
                {data: 'fabric_name',      name: 'fabric.name'},
                {data: 'type_label',       name: 'type'},
                {data: 'sleeve_label',     name: 'sleeve'},
                {data: 'price_formatted',  name: 'price'},
                {data: 'status_badge',     name: 'status',        orderable: false, searchable: false},
                {data: 'action',           name: 'action',        orderable: false, searchable: false},
            ],
            drawCallback: function () {
                if (typeof feather !== 'undefined') feather.replace({width: 14, height: 14});
            }
        });

        // ── Form Submit (Create / Update) ────────────────────────
        $('#fabricPriceForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            let id       = $('#fabric_price_id').val();
            let url      = "{{ route('admin.fabric-price.store') }}";
            let formData = new FormData(this);

            if (id) {
                url = "{{ url('admin/fabric-price') }}" + '/' + id;
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url, type: 'POST', data: formData,
                processData: false, contentType: false,
                success: function (data) {
                    resetForm();
                    table.draw();
                    toastr.success(data.success);
                },
                error: function (data) {
                    $('#saveBtn').text('Save').attr('disabled', false);
                    if (data.status === 422) {
                        let errors = data.responseJSON.errors;
                        $.each(errors, function (prefix, val) {
                            $('span.' + prefix + '_error').text(val[0]);
                        });
                        toastr.error('Validation error. Please check fields.');
                    } else {
                        toastr.error(data.responseJSON.error || 'Something went wrong.');
                    }
                }
            });
        });

        // ── Edit Button ──────────────────────────────────────────
        $('body').on('click', '.editFabricPrice', function () {
            var id = $(this).data('id');
            $.get("{{ url('admin/fabric-price') }}" + '/' + id + '/edit', function (data) {
                $('#formTitle').text('Edit Fabric Price');
                $('#saveBtn').text('Update');
                $('#cancelBtn').removeClass('d-none');
                $('#fabric_price_id').val(data.id);
                $('#fabric_id').val(data.fabric_id);
                $('#type').val(data.type);
                $('#sleeve').val(data.sleeve);
                $('#price').val(data.price);
                $('#status').val(data.status);
                if (typeof feather !== 'undefined') feather.replace({width: 14, height: 14});
            });
        });

        // ── Cancel Button ────────────────────────────────────────
        $('#cancelBtn').on('click', function () { resetForm(); });

        // ── Delete Button ────────────────────────────────────────
        $('body').on('click', '.deleteFabricPrice', function () {
            var id = $(this).data('id');
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
                        type: 'DELETE',
                        url: "{{ url('admin/fabric-price') }}" + '/' + id,
                        success: function (data) { table.draw(); toastr.success(data.success); },
                        error:   function ()     { toastr.error('Error deleting record.'); }
                    });
                }
            });
        });

        // ── Helper ───────────────────────────────────────────────
        function resetForm() {
            $('#fabricPriceForm').trigger('reset');
            $('#fabric_price_id').val('');
            $('#formTitle').text('Add Fabric Price');
            $('#saveBtn').text('Save').attr('disabled', false);
            $('#cancelBtn').addClass('d-none');
            $('.error-text').text('');
        }
    });
</script>
@endpush
