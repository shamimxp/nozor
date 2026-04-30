@extends('layouts.admin')
@section('title', 'Customers')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Customer List</h4>
                    <button type="button" class="btn btn-primary" id="showAddCustomerBtn">
                        <i data-feather="plus"></i> Add Customer
                    </button>
                </div>
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

    <div class="col-12 d-none" id="customerFormSection">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <h4 class="card-title mb-25" id="formTitle">Add Customer</h4>
                        <small class="text-muted">Default password will be 12345678.</small>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" id="closeFormBtn">Close</button>
                </div>
            </div>
            <div class="card-body pt-2">
                <form id="customerForm">
                    @csrf
                    <input type="hidden" name="customer_id" id="customer_id">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Customer Name" required>
                                <span class="text-danger error-text name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number" required>
                                <span class="text-danger error-text phone_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email Address">
                                <span class="text-danger error-text email_error"></span>
                            </div>
                        </div>
                    </div>

                    <div id="addressWrapper"></div>

                    <div class="mt-2 text-right">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Customer</button>
                        <button type="button" class="btn btn-outline-secondary" id="cancelBtn">Cancel</button>
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
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        resetAddressFields();

        $('#showAddCustomerBtn').on('click', function() {
            resetCustomerForm();
            openCustomerForm('add');
        });

        $('#closeFormBtn, #cancelBtn').on('click', function() {
            closeCustomerForm();
        });

        $(document).on('click', '#addAddress', function(){
            addAddressField('');
        });

        $(document).on('click', '.removeAddress', function(){
            $(this).closest('.address-item').remove();
        });

        $('#customerForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            var id = $('#customer_id').val();
            var url = "{{ route('admin.customer.store') }}";
            var formData = new FormData(this);

            if (id) {
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
                    toastr.success(data.success);
                    table.draw(false);
                    closeCustomerForm();
                },
                error: function (data) {
                    $('#saveBtn').text($('#customer_id').val() ? 'Update Customer' : 'Save Customer').attr('disabled', false);
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

        $('body').on('click', '.editCustomer', function () {
            var id = $(this).data('id');

            $.get("{{ url('admin/customer') }}" + '/' + id + '/edit', function (data) {
                resetCustomerForm();
                openCustomerForm('edit');

                $('#customer_id').val(data.id);
                $('#name').val(data.name);
                $('#phone').val(data.phone);
                $('#email').val(data.email);

                $('#addressWrapper').html('');
                if (data.addresses && data.addresses.length) {
                    data.addresses.forEach(function(addr, index) {
                        addAddressField(addr.address || '', index === 0);
                    });
                } else {
                    resetAddressFields();
                }

                refreshIcons();
            });
        });

        $('body').on('click', '.deleteCustomer', function () {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Delete this customer?',
                text: 'This will also delete the customer login account.',
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
                    url: "{{ url('admin/customer') }}" + '/' + id,
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
                        toastr.error((data.responseJSON && data.responseJSON.error) || 'Error deleting customer.');
                    }
                });
            });
        });

        function openCustomerForm(mode) {
            $('#customerFormSection').removeClass('d-none');
            $('#formTitle').text(mode === 'edit' ? 'Edit Customer' : 'Add Customer');
            $('#saveBtn')
                .text(mode === 'edit' ? 'Update Customer' : 'Save Customer')
                .attr('disabled', false);

            $('html, body').animate({
                scrollTop: $('#customerFormSection').offset().top - 80
            }, 250);
            refreshIcons();
        }

        function closeCustomerForm() {
            resetCustomerForm();
            $('#customerFormSection').addClass('d-none');
        }

        function resetCustomerForm() {
            $('#customerForm').trigger('reset');
            $('#customer_id').val('');
            $('.error-text').text('');
            $('#saveBtn').text('Save Customer').attr('disabled', false);
            resetAddressFields();
        }

        function resetAddressFields() {
            $('#addressWrapper').html('');
            addAddressField('', true);
        }

        function addAddressField(value, isFirst) {
            var button = isFirst ?
                '<button class="btn btn-outline-primary" type="button" id="addAddress"><i data-feather="plus"></i></button>' :
                '<button class="btn btn-outline-danger removeAddress" type="button"><i data-feather="minus"></i></button>';

            var label = isFirst ? '<label>Address <span class="text-danger">*</span></label>' : '';
            var html = '' +
                '<div class="form-group mb-1 address-item">' +
                    label +
                    '<div class="input-group">' +
                        '<input type="text" name="address[]" class="form-control address-input" value="' + escapeHtml(value) + '" placeholder="Address" required>' +
                        '<div class="input-group-append">' + button + '</div>' +
                    '</div>' +
                    (isFirst ? '<span class="text-danger error-text address_0_error"></span>' : '') +
                '</div>';

            $('#addressWrapper').append(html);
            refreshIcons();
        }

        function refreshIcons() {
            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
        }

        function escapeHtml(value) {
            return $('<div>').text(value || '').html();
        }
    });
</script>
@endpush
