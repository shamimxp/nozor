@extends('layouts.admin')
@section('title', 'Customers')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title" id="formTitle">Add Customer</h4>
            </div>
            <div class="card-body pt-2">
                <form id="customerForm">
                    @csrf
                    <input type="hidden" name="customer_id" id="customer_id">
                    <div class="form-group mb-1">
                        <label for="name">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Customer Name" required>
                        <span class="text-danger error-text name_error"></span>
                    </div>
                    <div class="form-group mb-1">
                        <label for="phone">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number" required>
                        <span class="text-danger error-text phone_error"></span>
                    </div>
                    <div class="form-group mb-1">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Email Address">
                        <span class="text-danger error-text email_error"></span>
                    </div>
                    
                    <div id="addressWrapper">
                        <div class="form-group mb-1 address-item">
                            <label>Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="address[]" class="form-control address-input" placeholder="Address" required>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" id="addAddress"><i data-feather="plus"></i></button>
                                </div>
                            </div>
                            <span class="text-danger error-text address_0_error"></span>
                        </div>
                    </div>

                    <div class="mt-2 text-info">
                        <small>Note: Default password will be 12345678</small>
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
                <h4 class="card-title">Customer List</h4>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="customerTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Addresses</th>
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

        var table = $('#customerTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.customer.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'phone', name: 'phone'},
                {data: 'email', name: 'email'},
                {data: 'addresses', name: 'addresses'},
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

        // Add Address Field
        $(document).on('click', '#addAddress', function(){
            var html = `
                <div class="form-group mb-1 address-item">
                    <div class="input-group">
                        <input type="text" name="address[]" class="form-control address-input" placeholder="Address" required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-danger removeAddress" type="button"><i data-feather="minus"></i></button>
                        </div>
                    </div>
                </div>
            `;
            $('#addressWrapper').append(html);
            feather.replace({ width: 14, height: 14 });
        });

        // Remove Address Field
        $(document).on('click', '.removeAddress', function(){
            $(this).closest('.address-item').remove();
        });

        $('#customerForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            let id = $('#customer_id').val();
            let url = "{{ route('admin.customer.store') }}";
            let formData = new FormData(this);

            if(id){
                url = "{{ url('admin/customer') }}" + "/" + id;
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    $('#customerForm').trigger("reset");
                    $('#customer_id').val('');
                    $('#formTitle').text('Add Customer');
                    $('#saveBtn').text('Save').attr('disabled', false);
                    $('#cancelBtn').addClass('d-none');
                    $('.removeAddress').closest('.address-item').remove();
                    table.draw();
                    toastr.success(data.success);
                },
                error: function (data) {
                    $('#saveBtn').text('Save').attr('disabled', false);
                    if(data.status === 422){
                        let errors = data.responseJSON.errors;
                        $.each(errors, function(prefix, val){
                            let field = prefix.replace(/\./g, '_');
                            $('span.'+field+'_error').text(val[0]);
                        });
                        toastr.error('Validation error. Please check fields.');
                    } else {
                        toastr.error(data.responseJSON.error || 'Something went wrong.');
                    }
                }
            });
        });

        $('body').on('click', '.editCustomer', function () {
            var id = $(this).data('id');
            $.get("{{ url('admin/customer') }}" +'/' + id + '/edit', function (data) {
                $('#formTitle').text('Edit Customer');
                $('#saveBtn').text('Update');
                $('#cancelBtn').removeClass('d-none');
                $('#customer_id').val(data.id);
                $('#name').val(data.name);
                $('#phone').val(data.phone);
                $('#email').val(data.email);
                
                // Clear and refill addresses
                $('#addressWrapper').html('');
                data.addresses.forEach((addr, index) => {
                    let btn = index === 0 ? 
                        `<button class="btn btn-outline-primary" type="button" id="addAddress"><i data-feather="plus"></i></button>` : 
                        `<button class="btn btn-outline-danger removeAddress" type="button"><i data-feather="minus"></i></button>`;
                    
                    let html = `
                        <div class="form-group mb-1 address-item">
                            <label>${index === 0 ? 'Address <span class="text-danger">*</span>' : ''}</label>
                            <div class="input-group">
                                <input type="text" name="address[]" class="form-control address-input" value="${addr.address}" placeholder="Address" required>
                                <div class="input-group-append">
                                    ${btn}
                                </div>
                            </div>
                        </div>
                    `;
                    $('#addressWrapper').append(html);
                });
                feather.replace({ width: 14, height: 14 });
            })
        });

        $('#cancelBtn').on('click', function(){
            $('#customerForm').trigger("reset");
            $('#customer_id').val('');
            $('#formTitle').text('Add Customer');
            $('#saveBtn').text('Save');
            $(this).addClass('d-none');
            $('.error-text').text('');
            
            // Reset addresses
            $('#addressWrapper').html(`
                <div class="form-group mb-1 address-item">
                    <label>Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" name="address[]" class="form-control address-input" placeholder="Address" required>
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="button" id="addAddress"><i data-feather="plus"></i></button>
                        </div>
                    </div>
                </div>
            `);
            feather.replace({ width: 14, height: 14 });
        });

        $('body').on('click', '.deleteCustomer', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Deleting a customer will also delete their login account!",
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
                        url: "{{ url('admin/customer') }}"+'/'+id,
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
                            toastr.error('Error deleting record.');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
