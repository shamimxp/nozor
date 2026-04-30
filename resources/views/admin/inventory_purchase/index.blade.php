@extends('layouts.admin')
@section('title', 'Stock Purchase List')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Stock Purchase List</h4>
                    <a href="{{ route('admin.inventory-purchase.create') }}" class="btn btn-primary">Create Purchase</a>
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="border rounded p-1 mb-2">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Purchase ID</label>
                                <input type="text" id="filterPurchaseNumber" class="form-control" placeholder="STK-PUR-1001">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Vendor</label>
                                <select id="filterVendor" class="form-control select2">
                                    <option value="">All Vendors</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }} ({{ $vendor->company_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" id="filterDateFrom" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" id="filterDateTo" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select id="filterStatus" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirm">Confirm</option>
                                    <option value="received">Received</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" id="filterBtn" class="btn btn-primary mr-1">Filter</button>
                        <button type="button" id="resetFilterBtn" class="btn btn-outline-secondary">Reset</button>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="border rounded p-1">
                            <div class="text-muted">Total Amount</div>
                            <h4 class="mb-0">৳<span id="summaryTotalAmount">0.00</span></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-1">
                            <div class="text-muted">Total Paid Amount</div>
                            <h4 class="mb-0 text-success">৳<span id="summaryPaidAmount">0.00</span></h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-1">
                            <div class="text-muted">Total Due Amount</div>
                            <h4 class="mb-0 text-danger">৳<span id="summaryDueAmount">0.00</span></h4>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                <table id="purchaseTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Purchase ID</th>
                            <th>Vendor</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="paymentForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Add Purchase Payment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="paymentPurchaseId">
                <div class="mb-1">
                    <strong id="paymentPurchaseNumber"></strong><br>
                    <small class="text-muted" id="paymentVendorName"></small>
                </div>
                <div class="alert alert-warning py-1">
                    Due Amount: ৳<strong id="paymentDueAmount">0.00</strong>
                </div>
                <div class="form-group">
                    <label>Payment Amount</label>
                    <input type="number" name="amount" id="paymentAmount" class="form-control" min="0.01" step="0.01" required>
                </div>
                <div class="form-group">
                    <label>Payment Date</label>
                    <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label>Payment Method</label>
                    <select name="payment_method" class="form-control">
                        <option value="Cash">Cash</option>
                        <option value="Bank">Bank</option>
                        <option value="Mobile Banking">Mobile Banking</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label>Note</label>
                    <textarea name="note" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" id="paymentSubmitBtn">Save Payment</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="paymentHistoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment History</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-1">
                    <strong id="historyPurchaseNumber"></strong><br>
                    <small class="text-muted" id="historyVendorName"></small>
                </div>
                <div class="row mb-1">
                    <div class="col-md-4">Total: ৳<strong id="historyTotalAmount">0.00</strong></div>
                    <div class="col-md-4">Paid: ৳<strong id="historyPaidAmount">0.00</strong></div>
                    <div class="col-md-4">Due: ৳<strong id="historyDueAmount">0.00</strong></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody id="historyPaymentRows">
                            <tr>
                                <td colspan="4" class="text-center">No payment history found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        var purchaseTable = $('#purchaseTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.inventory-purchase.index') }}",
                data: function (d) {
                    d.purchase_number = $('#filterPurchaseNumber').val();
                    d.vendor_id = $('#filterVendor').val();
                    d.date_from = $('#filterDateFrom').val();
                    d.date_to = $('#filterDateTo').val();
                    d.status = $('#filterStatus').val();
                },
                dataSrc: function(json) {
                    updateSummary(json.summary);
                    return json.data;
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'purchase_number', name: 'purchase_number'},
                {data: 'vendor_name', name: 'vendor_name'},
                {data: 'purchase_date', name: 'purchase_date'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'paid_amount', name: 'paid_amount'},
                {data: 'due_amount', name: 'due_amount'},
                {data: 'status_badge', name: 'status_badge'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        function updateSummary(summary) {
            if (!summary) {
                return;
            }

            $('#summaryTotalAmount').text(summary.total_amount);
            $('#summaryPaidAmount').text(summary.paid_amount);
            $('#summaryDueAmount').text(summary.due_amount);
        }

        $('#filterBtn').on('click', function() {
            purchaseTable.ajax.reload();
        });

        $('#resetFilterBtn').on('click', function() {
            $('#filterPurchaseNumber, #filterDateFrom, #filterDateTo').val('');
            $('#filterVendor, #filterStatus').val('').trigger('change');
            purchaseTable.ajax.reload();
        });

        $('#filterPurchaseNumber, #filterDateFrom, #filterDateTo').on('keyup change', function(e) {
            if (e.type === 'change' || e.key === 'Enter') {
                purchaseTable.ajax.reload();
            }
        });

        $('#filterVendor, #filterStatus').on('change', function() {
            purchaseTable.ajax.reload();
        });

        $(document).on('click', '.payPurchaseBtn', function() {
            var due = parseFloat($(this).data('due')) || 0;
            $('#paymentPurchaseId').val($(this).data('id'));
            $('#paymentPurchaseNumber').text('Purchase #' + $(this).data('purchase-number'));
            $('#paymentVendorName').text($(this).data('vendor-name'));
            $('#paymentDueAmount').text(due.toFixed(2));
            $('#paymentAmount').attr('max', due).val(due.toFixed(2));
            $('#paymentModal').modal('show');
        });

        $('#paymentForm').on('submit', function(e) {
            e.preventDefault();

            var purchaseId = $('#paymentPurchaseId').val();
            var due = parseFloat($('#paymentAmount').attr('max')) || 0;
            var amount = parseFloat($('#paymentAmount').val()) || 0;

            if (amount > due) {
                toastr.error('Payment amount cannot exceed due amount.');
                return;
            }

            $('#paymentSubmitBtn').text('Saving...').attr('disabled', true);

            $.ajax({
                url: "{{ url('/admin/inventory-purchase') }}/" + purchaseId + "/payment",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    $('#paymentModal').modal('hide');
                    toastr.success(response.message);
                    purchaseTable.ajax.reload(null, false);
                },
                error: function(xhr) {
                    toastr.error(getAjaxErrorMessage(xhr));
                },
                complete: function() {
                    $('#paymentSubmitBtn').text('Save Payment').attr('disabled', false);
                }
            });
        });

        $(document).on('click', '.paymentHistoryBtn', function() {
            var purchaseId = $(this).data('id');
            $('#historyPurchaseNumber').text('Loading...');
            $('#historyVendorName').text('');
            $('#historyTotalAmount, #historyPaidAmount, #historyDueAmount').text('0.00');
            $('#historyPaymentRows').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
            $('#paymentHistoryModal').modal('show');

            $.get("{{ url('/admin/inventory-purchase') }}/" + purchaseId + "/payment-history", function(response) {
                $('#historyPurchaseNumber').text('Purchase #' + response.purchase_number);
                $('#historyVendorName').text(response.vendor_name);
                $('#historyTotalAmount').text(response.total_amount);
                $('#historyPaidAmount').text(response.paid_amount);
                $('#historyDueAmount').text(response.due_amount);

                if (!response.payments.length) {
                    $('#historyPaymentRows').html('<tr><td colspan="4" class="text-center">No payment history found.</td></tr>');
                    return;
                }

                var rows = response.payments.map(function(payment) {
                    return '<tr>' +
                        '<td>' + escapeHtml(payment.date) + '</td>' +
                        '<td>৳' + escapeHtml(payment.amount) + '</td>' +
                        '<td>' + escapeHtml(payment.method) + '</td>' +
                        '<td>' + escapeHtml(payment.note) + '</td>' +
                    '</tr>';
                }).join('');
                $('#historyPaymentRows').html(rows);
            }).fail(function(xhr) {
                $('#historyPaymentRows').html('<tr><td colspan="4" class="text-center text-danger">' + getAjaxErrorMessage(xhr) + '</td></tr>');
            });
        });

        function getAjaxErrorMessage(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.message) {
                return xhr.responseJSON.message;
            }

            if (xhr.responseJSON && xhr.responseJSON.error) {
                return xhr.responseJSON.error;
            }

            if (xhr.responseJSON && xhr.responseJSON.errors) {
                var firstMessage = 'Something went wrong!';
                $.each(xhr.responseJSON.errors, function(field, messages) {
                    firstMessage = messages[0];
                    return false;
                });
                return firstMessage;
            }

            return 'Something went wrong!';
        }

        function escapeHtml(value) {
            return $('<div>').text(value || '').html();
        }
    });
</script>
@endpush
