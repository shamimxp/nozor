@extends('layouts.admin')
@section('title', 'Purchase Due List')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Purchase Due List</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.purchase.index') }}">Purchases</a></li>
                            <li class="breadcrumb-item active">Invoice Due List</li>
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
                        <h4 class="card-title mb-0">Purchase Invoice Due List</h4>
                    </div>
                    <div class="card-body table-responsive pt-2">
                        <table id="dueListTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Purchase Info</th>
                                    <th>Vendor</th>
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

<!-- Purchase Payment Modal -->
<div class="modal fade text-left" id="purchasePaymentModal" tabindex="-1" role="dialog" aria-labelledby="purchasePayModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="purchasePayModalLabel">Add Purchase Payment</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="purchasePaymentForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="purchase_id" id="modal_purchase_id">
                    
                    <div class="form-group">
                        <label>Vendor:</label>
                        <input type="text" class="form-control" id="modal_vendor_name" readonly>
                    </div>
                    <div class="form-group">
                        <label>Purchase Number:</label>
                        <input type="text" class="form-control" id="modal_purchase_number" readonly>
                    </div>
                    <div class="form-group">
                        <label>Due Amount:</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">৳</span>
                            </div>
                            <input type="text" class="form-control" id="modal_due_amount" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Payment Amount: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">৳</span>
                            </div>
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
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Note:</label>
                        <textarea name="note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="purchasePaySubmitBtn">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        var table = $('#dueListTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.purchase.due-list') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'purchase_info', name: 'purchase_number'},
                {data: 'vendor_name', name: 'vendor.name'},
                {data: 'financials', name: 'grand_total'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
            }
        });

        $(document).on('click', '.payBtn', function() {
            var id = $(this).data('id');
            var vendorName = $(this).data('vendor-name');
            var purchaseNumber = $(this).data('purchase-number');
            var due = $(this).data('due');

            $('#modal_purchase_id').val(id);
            $('#modal_vendor_name').val(vendorName);
            $('#modal_purchase_number').val(purchaseNumber);
            $('#modal_due_amount').val(due);
            $('#modal_payment_amount').val(due).attr('max', due);
            
            $('#purchasePaymentModal').modal('show');
        });

        $('#purchasePaymentForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            var submitBtn = $('#purchasePaySubmitBtn');
            submitBtn.prop('disabled', true).text('Processing...');

            $.ajax({
                url: "{{ route('admin.purchase.add-payment') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#purchasePaymentModal').modal('hide');
                        table.ajax.reload();
                    } else {
                        toastr.error(response.message);
                    }
                    submitBtn.prop('disabled', false).text('Save Payment');
                },
                error: function(xhr) {
                    var msg = 'Something went wrong';
                    if (xhr.status === 422) {
                        msg = xhr.responseJSON.message;
                    }
                    toastr.error(msg);
                    submitBtn.prop('disabled', false).text('Save Payment');
                }
            });
        });
    });
</script>
@endpush
