@extends('layouts.admin')
@section('title', 'Vendors')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title" id="formTitle">Add Vendor</h4>
            </div>
            <div class="card-body pt-2">
                <form id="vendorForm">
                    @csrf
                    <input type="hidden" name="vendor_id" id="vendor_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Vendor Name" required>
                                <span class="text-danger error-text name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="company_name">Company Name</label>
                                <input type="text" name="company_name" id="company_name" class="form-control" placeholder="Company Name">
                                <span class="text-danger error-text company_name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number" required>
                                <span class="text-danger error-text phone_error"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-1">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email Address">
                                <span class="text-danger error-text email_error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group mb-1">
                                <label for="opening_balance">Opening Balance</label>
                                <input type="number" name="opening_balance" id="opening_balance" class="form-control" placeholder="0.00" step="0.01" value="0">
                                <span class="text-danger error-text opening_balance_error"></span>
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
                <h4 class="card-title">Vendor List</h4>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="vendorTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Company</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Opening Balance</th>
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

        var table = $('#vendorTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.vendor.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'company_name', name: 'company_name'},
                {data: 'phone', name: 'phone'},
                {data: 'email', name: 'email'},
                {data: 'opening_balance', name: 'opening_balance'},
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

        $('#vendorForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            let id = $('#vendor_id').val();
            let url = "{{ route('admin.vendor.store') }}";
            let formData = new FormData(this);

            if(id){
                url = "{{ url('admin/vendor') }}" + "/" + id;
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#vendorForm').trigger("reset");
                    $('#vendor_id').val('');
                    $('#formTitle').text('Add Vendor');
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
                        toastr.error('Validation error. Please check fields.');
                    } else {
                        toastr.error(data.responseJSON.error || 'Something went wrong.');
                    }
                }
            });
        });

        $('body').on('click', '.editVendor', function () {
            var id = $(this).data('id');
            $.get("{{ url('admin/vendor') }}" +'/' + id + '/edit', function (data) {
                $('#formTitle').text('Edit Vendor');
                $('#saveBtn').text('Update');
                $('#cancelBtn').removeClass('d-none');
                $('#vendor_id').val(data.id);
                $('#name').val(data.name);
                $('#company_name').val(data.company_name);
                $('#phone').val(data.phone);
                $('#email').val(data.email);
                $('#opening_balance').val(data.opening_balance);
                
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            })
        });

        $('#cancelBtn').on('click', function(){
            $('#vendorForm').trigger("reset");
            $('#vendor_id').val('');
            $('#formTitle').text('Add Vendor');
            $('#saveBtn').text('Save');
            $(this).addClass('d-none');
            $('.error-text').text('');
        });

        $('body').on('click', '.deleteVendor', function () {
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
                        url: "{{ url('admin/vendor') }}"+'/'+id,
                        success: function (data) {
                            table.draw();
                            toastr.success(data.success);
                        },
                        error: function (data) {
                            toastr.error('Error deleting record.');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
