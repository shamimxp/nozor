@extends('layouts.admin')
@section('title', 'Dealers')
@section('content')
<div class="row">
    <div class="col-12 d-none" id="dealerFormSection">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <h4 class="card-title mb-25" id="formTitle">Add Dealer</h4>
                        <small class="text-muted">Default password will be dealer phone number.</small>
                    </div>
                    <button type="button" class="btn btn-outline-secondary" id="closeFormBtn">Close</button>
                </div>
            </div>
            <div class="card-body pt-2">
                <form id="dealerForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="dealer_id" id="dealer_id">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="Dealer Name" required>
                                <span class="text-danger error-text name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="shop_name">Shop Name <span class="text-danger">*</span></label>
                                <input type="text" name="shop_name" id="shop_name" class="form-control" placeholder="Shop Name" required>
                                <span class="text-danger error-text shop_name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="phone">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Phone Number" required>
                                <span class="text-danger error-text phone_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="Email Address">
                                <span class="text-danger error-text email_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="nid_number">NID Number</label>
                                <input type="text" name="nid_number" id="nid_number" class="form-control" placeholder="NID Number">
                                <span class="text-danger error-text nid_number_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="bank_name">Bank Name</label>
                                <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Bank Name">
                                <span class="text-danger error-text bank_name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="account_no">Account No</label>
                                <input type="text" name="account_no" id="account_no" class="form-control" placeholder="Account No">
                                <span class="text-danger error-text account_no_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-1">
                                <label for="address">Address</label>
                                <textarea name="address" id="address" class="form-control" placeholder="Dealer Address" rows="2"></textarea>
                                <span class="text-danger error-text address_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="profile_image">Profile Image</label>
                                <input type="file" name="profile_image" id="profile_image" class="form-control-file">
                                <span class="text-danger error-text profile_image_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="nid_image">NID Image</label>
                                <input type="file" name="nid_image" id="nid_image" class="form-control-file">
                                <span class="text-danger error-text nid_image_error"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="trade_license">Trade License</label>
                                <input type="file" name="trade_license" id="trade_license" class="form-control-file">
                                <span class="text-danger error-text trade_license_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-1">
                        <div class="col-md-4">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="status" name="status" checked>
                                <label class="custom-control-label" for="status">Active Status</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_special" name="is_special">
                                <label class="custom-control-label" for="is_special">Is Special</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Removed password field to match requirement -->
                        </div>
                    </div>

                    <div class="mt-2 text-right">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Dealer</button>
                        <button type="button" class="btn btn-outline-secondary" id="cancelBtn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Dealer List</h4>
                    <button type="button" class="btn btn-primary" id="showAddDealerBtn">
                        <i data-feather="plus"></i> Add Dealer
                    </button>
                </div>
            </div>
            
            <div class="card-body border-bottom">
                <div class="row">
                    <div class="col-md-2">
                        <label>Phone Number</label>
                        <input type="text" id="filter_phone" class="form-control" placeholder="Search phone...">
                    </div>
                    <div class="col-md-2">
                        <label>Shop Name</label>
                        <input type="text" id="filter_shop_name" class="form-control" placeholder="Search shop...">
                    </div>
                    <div class="col-md-2">
                        <label>Filter by Status</label>
                        <select id="filter_status" class="form-control">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Filter by Special</label>
                        <select id="filter_is_special" class="form-control">
                            <option value="">All</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end mt-1 mt-md-0">
                        <button id="filterBtn" class="btn btn-info mr-1">Filter</button>
                        <button id="resetFilterBtn" class="btn btn-outline-secondary">Reset</button>
                    </div>
                </div>
            </div>

            <div class="card-body table-responsive pt-2">
                <table id="dealerTable" class="table table-bordered table-striped w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Shop Name</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Special</th>
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

        var table = $('#dealerTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.dealer.index') }}",
                data: function(d) {
                    d.status = $('#filter_status').val();
                    d.is_special = $('#filter_is_special').val();
                    d.phone = $('#filter_phone').val();
                    d.shop_name = $('#filter_shop_name').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'profile_image', name: 'profile_image', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'shop_name', name: 'shop_name'},
                {data: 'phone', name: 'phone'},
                {data: 'status', name: 'status'},
                {data: 'is_special', name: 'is_special'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        $('#filterBtn').on('click', function() {
            table.draw();
        });

        $('#resetFilterBtn').on('click', function() {
            $('#filter_status').val('');
            $('#filter_is_special').val('');
            $('#filter_phone').val('');
            $('#filter_shop_name').val('');
            table.draw();
        });

        $('#showAddDealerBtn').on('click', function() {
            resetDealerForm();
            openDealerForm('add');
        });

        $('#closeFormBtn, #cancelBtn').on('click', function() {
            closeDealerForm();
        });

        $('#dealerForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Sending...').attr('disabled', true);
            $('.error-text').text('');

            var id = $('#dealer_id').val();
            var url = "{{ route('admin.dealer.store') }}";
            var formData = new FormData(this);

            if (id) {
                url = "{{ url('admin/dealer') }}" + "/" + id;
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
                    closeDealerForm();
                },
                error: function (data) {
                    $('#saveBtn').text($('#dealer_id').val() ? 'Update Dealer' : 'Save Dealer').attr('disabled', false);
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

        $('body').on('click', '.editDealer', function () {
            var id = $(this).data('id');

            $.get("{{ url('admin/dealer') }}" + '/' + id + '/edit', function (data) {
                resetDealerForm();
                openDealerForm('edit');

                $('#dealer_id').val(data.id);
                $('#name').val(data.name);
                $('#shop_name').val(data.shop_name);
                $('#phone').val(data.phone);
                $('#email').val(data.email);
                $('#nid_number').val(data.nid_number);
                $('#bank_name').val(data.bank_name);
                $('#account_no').val(data.account_no);
                $('#address').val(data.address);
                
                $('#status').prop('checked', data.status == 1);
                $('#is_special').prop('checked', data.is_special == 1);

                refreshIcons();
            });
        });

        $('body').on('click', '.deleteDealer', function () {
            var id = $(this).data('id');

            Swal.fire({
                title: 'Delete this dealer?',
                text: 'This action cannot be undone.',
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
                    url: "{{ url('admin/dealer') }}" + '/' + id,
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
                        toastr.error((data.responseJSON && data.responseJSON.error) || 'Error deleting dealer.');
                    }
                });
            });
        });

        function openDealerForm(mode) {
            $('#dealerFormSection').removeClass('d-none');
            $('#formTitle').text(mode === 'edit' ? 'Edit Dealer' : 'Add Dealer');
            $('#saveBtn')
                .text(mode === 'edit' ? 'Update Dealer' : 'Save Dealer')
                .attr('disabled', false);

            $('html, body').animate({
                scrollTop: $('#dealerFormSection').offset().top - 80
            }, 250);
            refreshIcons();
        }

        function closeDealerForm() {
            resetDealerForm();
            $('#dealerFormSection').addClass('d-none');
        }

        function resetDealerForm() {
            $('#dealerForm').trigger('reset');
            $('#dealer_id').val('');
            $('.error-text').text('');
            $('#status').prop('checked', true);
            $('#is_special').prop('checked', false);
            $('#saveBtn').text('Save Dealer').attr('disabled', false);
        }

        function refreshIcons() {
            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
        }
    });
</script>
@endpush
