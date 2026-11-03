@extends('layouts.admin')
@section('title', $page_title ?? 'Worker Balance Sheet')

@push('styles')
<style>
    /* Payment Method Cards */
    .payment-method-card {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 6px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background: #fff;
    }
    .payment-method-card:hover {
        border-color: #6366f1;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
    }
    .payment-method-card.selected {
        border-color: #4f46e5;
        background: #eef2ff;
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
    }
    .payment-method-card img {
        width: 36px;
        height: 36px;
        object-fit: contain;
        margin-bottom: 6px;
    }
    .payment-method-card span {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: #334155;
    }

    /* Balance Overview Cards */
    .amount-display-box {
        background: #ffffff;
        border: 1.5px dashed #cbd5e1;
        border-radius: 10px;
        padding: 14px 12px;
        text-align: center;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }

    /* Modal Custom Styling & Close Button Fix */
    #makePaymentModal .modal-header {
        background: #1e293b !important;
        border-bottom: 1px solid #334155 !important;
        padding: 1.1rem 1.4rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }
    #makePaymentModal .modal-header .close {
        background: rgba(255, 255, 255, 0.12) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        border-radius: 8px !important;
        width: 32px !important;
        height: 32px !important;
        color: #ffffff !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        margin: 0 !important;
        padding: 0 !important;
        opacity: 0.85 !important;
        transform: none !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
        outline: none !important;
    }
    #makePaymentModal .modal-header .close:hover {
        background: #ea5455 !important;
        border-color: #ea5455 !important;
        color: #ffffff !important;
        opacity: 1 !important;
        transform: rotate(90deg) !important;
    }
    #makePaymentModal .modal-header .close span {
        font-size: 22px !important;
        font-weight: 300 !important;
        line-height: 1 !important;
        color: #ffffff !important;
        display: block !important;
        margin-top: -2px !important;
    }
    /* Worker Balance Sheet Make Payment Action Button */
    .make-payment-btn {
        height: 32px !important;
        padding: 5px 14px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        border-radius: 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        white-space: nowrap !important;
        box-shadow: none !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
    }
    .make-payment-btn::after,
    .make-payment-btn::before {
        display: none !important;
        content: none !important;
    }
    .make-payment-btn i,
    .make-payment-btn svg {
        width: 14px !important;
        height: 14px !important;
        stroke-width: 2 !important;
        margin-right: 5px !important;
    }
    .make-payment-btn.btn-pay-active {
        background-color: #111C38 !important;
        border: 1px solid #111C38 !important;
        color: #ffffff !important;
        cursor: pointer !important;
        box-shadow: 0 2px 4px rgba(17, 28, 56, 0.15) !important;
    }
    .make-payment-btn.btn-pay-active:hover {
        background-color: #1e293b !important;
        border-color: #1e293b !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 8px rgba(17, 28, 56, 0.22) !important;
    }
    .make-payment-btn.btn-pay-active i,
    .make-payment-btn.btn-pay-active svg {
        color: #10B981 !important;
        stroke: #10B981 !important;
    }
    .make-payment-btn.btn-pay-clear {
        background-color: #f1f5f9 !important;
        border: 1px solid #e2e8f0 !important;
        color: #64748b !important;
        cursor: not-allowed !important;
        opacity: 0.9 !important;
    }
    .make-payment-btn.btn-pay-clear i,
    .make-payment-btn.btn-pay-clear svg {
        color: #10B981 !important;
        stroke: #10B981 !important;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i data-feather="users" class="mr-50 text-primary"></i> Worker Balance Sheet
                </h4>
                <a href="{{ route('admin.worker-payouts.index') }}" class="btn btn-outline-primary btn-sm">
                    <i data-feather="credit-card" class="mr-25"></i> Worker Payment List
                </a>
            </div>
            <div class="card-body mt-2">
                <div class="table-responsive">
                    <table class="table table-bordered yajra-datatable w-100">
                        <thead>
                            <tr>
                                <th style="width: 50px;">SL</th>
                                <th>Worker Name</th>
                                <th>Phone Number</th>
                                <th class="text-right">Total Invoices</th>
                                <th class="text-right">Total Paid</th>
                                <th class="text-right">Balance (Due)</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="width: 155px; min-width: 155px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Make Payment Modal -->
<div class="modal fade" id="makePaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title text-white font-weight-bolder mb-25">
                        <i data-feather="dollar-sign" style="width: 18px; height: 18px;"></i> Make Worker Payment
                    </h5>
                    <span class="small" id="modalWorkerSubtitle" style="color: #94a3b8; font-weight: 500;"></span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="workerPaymentForm">
                @csrf
                <input type="hidden" name="worker_id" id="modal_worker_id">

                <div class="modal-body p-2" style="background: #fafbfc;">
                    <!-- Worker Balance Overview Cards -->
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <div class="amount-display-box">
                                <small class="text-muted text-uppercase font-weight-bold">Total Balance</small>
                                <h4 class="font-weight-bolder text-danger mb-0 mt-25" id="display_total_balance">৳ 0.00</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="amount-display-box" style="border-color: #6366f1; background: #eef2ff;">
                                <small class="text-primary text-uppercase font-weight-bold">Paying Amount</small>
                                <h4 class="font-weight-bolder text-primary mb-0 mt-25" id="display_paying_amount">৳ 0.00</h4>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="amount-display-box">
                                <small class="text-muted text-uppercase font-weight-bold">Remaining Balance</small>
                                <h4 class="font-weight-bolder text-dark mb-0 mt-25" id="display_remaining_balance">৳ 0.00</h4>
                            </div>
                        </div>
                    </div>

                    <!-- Input Fields -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark">Total Balance (Due):</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light font-weight-bold">৳</span>
                                </div>
                                <input type="text" class="form-control bg-light font-weight-bold text-danger" id="modal_total_balance_input" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark">Total Paid Amount: <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light font-weight-bold">৳</span>
                                </div>
                                <input type="number" step="0.01" min="0.01" name="amount" id="modal_amount_input" class="form-control font-weight-bolder text-primary" placeholder="Enter amount to pay" required style="font-size: 1.1rem;">
                            </div>
                            <small class="text-muted">Enter the amount you wish to disburse to this worker.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark">Remaining Balance:</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light font-weight-bold">৳</span>
                                </div>
                                <input type="text" class="form-control bg-light font-weight-bold" id="modal_remaining_balance_input" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark">Payment Date: <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <!-- Payment Method Selector with Logos -->
                    <div class="form-group mb-2">
                        <label class="font-weight-bold text-dark mb-1 d-block">Pay By (Payment Method): <span class="text-danger">*</span></label>
                        <input type="hidden" name="payment_method" id="modal_payment_method" value="Cash">
                        <div class="row">
                            <div class="col">
                                <div class="payment-method-card selected" data-method="Cash">
                                    <img src="{{ asset('images/payments/cash.svg') }}" alt="Cash">
                                    <span>Cash</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="payment-method-card" data-method="Bank">
                                    <img src="{{ asset('images/payments/bank.svg') }}" alt="Bank">
                                    <span>Bank</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="payment-method-card" data-method="bKash">
                                    <img src="{{ asset('images/payments/bkash.svg') }}" alt="bKash">
                                    <span>bKash</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="payment-method-card" data-method="Nagad">
                                    <img src="{{ asset('images/payments/nagad.svg') }}" alt="Nagad">
                                    <span>Nagad</span>
                                </div>
                            </div>
                            <div class="col">
                                <div class="payment-method-card" data-method="Rocket">
                                    <img src="{{ asset('images/payments/rocket.svg') }}" alt="Rocket">
                                    <span>Rocket</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark">Account / Transaction Info (Optional):</label>
                            <input type="text" name="account_details" class="form-control" placeholder="e.g. Trx ID, Cheque No, Bank A/C...">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark">Note / Remarks (Optional):</label>
                            <input type="text" name="note" class="form-control" placeholder="Payment note...">
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light py-1 d-flex justify-content-between align-items-center">
                    <span class="small text-muted font-italic">
                        * Payment will settle unpaid manufacture invoices sequentially (date asc).
                    </span>
                    <div>
                        <button type="button" class="btn btn-secondary mr-50" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitPayment">
                            <i data-feather="check-circle" style="width: 14px; height: 14px;" class="mr-25"></i> Submit Payment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    var table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.worker-balance-sheet.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'worker_info', name: 'name'},
            {data: 'phone', name: 'phone'},
            {data: 'total_earned', name: 'total_earned', className: 'text-right'},
            {data: 'total_paid', name: 'total_paid', className: 'text-right'},
            {data: 'balance', name: 'balance', className: 'text-right'},
            {data: 'status', name: 'status', className: 'text-center'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
        ],
        drawCallback: function (settings) {
            if (typeof feather !== 'undefined') {
                feather.replace({ width: 14, height: 14 });
            }
        }
    });

    var currentWorkerBalance = 0;

    // Open Payment Modal
    $(document).on('click', '.make-payment-btn', function () {
        var workerId = $(this).data('id');
        var workerName = $(this).data('name');
        var workerPhone = $(this).data('phone') || '';
        currentWorkerBalance = parseFloat($(this).data('balance')) || 0;

        $('#modal_worker_id').val(workerId);
        $('#modalWorkerSubtitle').text(workerName + (workerPhone ? ' (' + workerPhone + ')' : ''));
        $('#modal_total_balance_input').val(currentWorkerBalance.toFixed(2));
        $('#display_total_balance').text('৳ ' + currentWorkerBalance.toLocaleString('en-US', {minimumFractionDigits: 2}));

        $('#modal_amount_input').val('').attr('max', currentWorkerBalance);
        $('#modal_remaining_balance_input').val(currentWorkerBalance.toFixed(2));
        $('#display_paying_amount').text('৳ 0.00');
        $('#display_remaining_balance').text('৳ ' + currentWorkerBalance.toLocaleString('en-US', {minimumFractionDigits: 2}));

        // Reset payment method to Cash
        $('.payment-method-card').removeClass('selected');
        $('.payment-method-card[data-method="Cash"]').addClass('selected');
        $('#modal_payment_method').val('Cash');

        $('#makePaymentModal').modal('show');
    });

    // Payment method selector click
    $(document).on('click', '.payment-method-card', function () {
        $('.payment-method-card').removeClass('selected');
        $(this).addClass('selected');
        $('#modal_payment_method').val($(this).data('method'));
    });

    // Real-time calculation on typing paid amount
    $('#modal_amount_input').on('input change keyup', function () {
        var paying = parseFloat($(this).val()) || 0;
        if (paying > currentWorkerBalance) {
            paying = currentWorkerBalance;
            $(this).val(paying);
        }
        var remaining = Math.max(0, currentWorkerBalance - paying);

        $('#display_paying_amount').text('৳ ' + paying.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#display_remaining_balance').text('৳ ' + remaining.toLocaleString('en-US', {minimumFractionDigits: 2}));
        $('#modal_remaining_balance_input').val(remaining.toFixed(2));
    });

    // Submit Payment
    $('#workerPaymentForm').on('submit', function (e) {
        e.preventDefault();

        var paying = parseFloat($('#modal_amount_input').val()) || 0;
        if (paying <= 0) {
            toastr.error('Please enter a valid payment amount greater than 0.');
            return;
        }

        var $btn = $('#btnSubmitPayment');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-25"></span> Processing...');

        $.ajax({
            url: "{{ route('admin.worker-payouts.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function (res) {
                if (res.success) {
                    $('#makePaymentModal').modal('hide');
                    table.draw(false);

                    Swal.fire({
                        title: 'Payment Successful!',
                        text: res.message,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fa fa-print"></i> View Payment Token (Cheque)',
                        cancelButtonText: 'Done',
                        confirmButtonColor: '#4f46e5',
                    }).then((result) => {
                        if (result.isConfirmed && res.show_url) {
                            window.location.href = res.show_url;
                        }
                    });
                } else {
                    toastr.error(res.message || 'Failed to record payment.');
                }
                $btn.prop('disabled', false).html(originalText);
            },
            error: function (xhr) {
                var msg = 'An error occurred while processing payment.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endpush
