@extends('layouts.admin')
@section('title', 'Custom Order Due List')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Custom Order Due List</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Custom Order Due List</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom p-1">
                        <h4 class="card-title mb-0">Orders with Pending Due</h4>
                    </div>
                    <div class="card-body table-responsive pt-2">
                        <table id="customDueTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order Info</th>
                                    <th>Customer</th>
                                    <th>Financial Summary</th>
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
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade text-left" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="paymentModalLabel">Add Payment</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="paymentForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="payable_id" id="modal_payable_id">
                    <input type="hidden" name="payable_type" id="modal_payable_type">
                    <input type="hidden" name="customer_id" id="modal_customer_id">

                    <div class="form-group">
                        <label>Customer:</label>
                        <input type="text" class="form-control" id="modal_customer_name" readonly>
                    </div>
                    <div class="form-group">
                        <label>Order Number:</label>
                        <input type="text" class="form-control" id="modal_order_number" readonly>
                    </div>
                    <div class="form-group">
                        <label>Due Amount:</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">৳</span></div>
                            <input type="text" class="form-control" id="modal_due_amount" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Payment Amount: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">৳</span></div>
                            <input type="number" step="0.01" name="amount" class="form-control" id="modal_payment_amount" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Payment Date: <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Payment Method: <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-control" required>
                            <option value="Cash">Cash</option>
                            <option value="bKash">bKash</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Bank">Bank Transfer</option>
                            <option value="Card">Card</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Note:</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="paymentSubmitBtn">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('#customDueTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.custom-order.due-list') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'order_info', name: 'order_number'},
                {data: 'customer_name', name: 'customer.name'},
                {data: 'financials', name: 'grand_total'},
                {data: 'status_badge', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
            }
        });
    });

    $(document).on('click', '.payBtn', function() {
        var id = $(this).data('id');
        var customerId = $(this).data('customer-id');
        var customerName = $(this).data('customer-name');
        var orderNumber = $(this).data('order-number');
        var due = $(this).data('due');
        var type = $(this).data('type');

        $('#modal_payable_id').val(id);
        $('#modal_payable_type').val(type);
        $('#modal_customer_id').val(customerId);
        $('#modal_customer_name').val(customerName);
        $('#modal_order_number').val(orderNumber);
        $('#modal_due_amount').val(due);
        $('#modal_payment_amount').val(due).attr('max', due);

        $('#paymentModal').modal('show');
    });

    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var submitBtn = $('#paymentSubmitBtn');
        submitBtn.prop('disabled', true).text('Processing...');

        $.ajax({
            url: "{{ route('admin.due-collection.store') }}",
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    $('#paymentModal').modal('hide');
                    $('#customDueTable').DataTable().ajax.reload();
                } else {
                    toastr.error(response.message);
                }
                submitBtn.prop('disabled', false).text('Save Payment');
            },
            error: function(xhr) {
                var msg = 'Something went wrong';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
                submitBtn.prop('disabled', false).text('Save Payment');
            }
        });
    });
</script>
@endpush
