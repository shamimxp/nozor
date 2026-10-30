@extends('layouts.admin')
@section('title', 'Dealer Wallet Statement - ' . $dealer->shop_name)

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Dealer Wallet</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.dealer-wallets.index') }}">Dealer Wallets</a></li>
        <li class="breadcrumb-item active">{{ $dealer->shop_name }}</li>
    </ol>
</div>
@endsection

@section('content')
<!-- Header Summary Card -->
<div class="card shadow-sm border-0 mb-2">
    <div class="card-body p-2">
        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <div>
                <h3 class="font-weight-bolder text-dark mb-25">{{ $dealer->shop_name }}</h3>
                <p class="text-muted mb-0">
                    <span class="mr-1"><i data-feather="user" class="font-small-3 mr-25"></i> {{ $dealer->name }}</span>
                    <span class="mr-1"><i data-feather="phone" class="font-small-3 mr-25"></i> {{ $dealer->phone }}</span>
                    @if($dealer->address)
                        <span><i data-feather="map-pin" class="font-small-3 mr-25"></i> {{ $dealer->address }}</span>
                    @endif
                </p>
            </div>
            <div class="mt-1 mt-sm-0 d-flex">
                <a href="{{ route('admin.dealer-wallets.index') }}" class="btn btn-outline-secondary btn-sm mr-1">
                    <i data-feather="arrow-left" class="mr-25"></i> Back to Wallets
                </a>
                <button type="button" class="btn btn-primary btn-sm" id="openDepositModalBtn">
                    <i data-feather="plus-circle" class="mr-25"></i> Add Deposit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Metrics Row -->
<div class="row">
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-1 text-center">
                <span class="text-muted font-small-2 font-weight-bold text-uppercase d-block mb-25">Current Wallet Balance</span>
                <h3 class="font-weight-bolder text-success mb-0">৳ {{ number_format($summary['wallet_balance'], 2) }}</h3>
                <small class="text-muted">Available credit</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-1 text-center">
                <span class="text-muted font-small-2 font-weight-bold text-uppercase d-block mb-25">Delivered Orders Due</span>
                <h3 class="font-weight-bolder text-danger mb-0">৳ {{ number_format($summary['delivered_due'], 2) }}</h3>
                <small class="text-muted">Pending settlement</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-1 text-center">
                <span class="text-muted font-small-2 font-weight-bold text-uppercase d-block mb-25">Total Paid</span>
                <h3 class="font-weight-bolder text-info mb-0">৳ {{ number_format($summary['total_paid'], 2) }}</h3>
                <small class="text-muted">Paid for orders</small>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-1 text-center">
                <span class="text-muted font-small-2 font-weight-bold text-uppercase d-block mb-25">Total Orders</span>
                <h3 class="font-weight-bolder text-dark mb-0">৳ {{ number_format($summary['total_orders'], 2) }}</h3>
                <small class="text-muted">{{ $summary['total_orders_count'] }} order(s)</small>
            </div>
        </div>
    </div>
</div>

<!-- Transactions Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header border-bottom py-1">
        <h4 class="card-title mb-0"><i data-feather="file-text" class="mr-50 text-primary"></i> Wallet Statement Ledger</h4>
    </div>
    <div class="card-body pt-2">
        <div class="table-responsive">
            <table id="statementTable" class="table table-bordered table-hover text-nowrap w-100">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Trx Number</th>
                        <th>Date & Time</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Running Balance</th>
                        <th>Payment Method</th>
                        <th>Order / Ref</th>
                        <th>Note</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="depositForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white"><i data-feather="plus-circle" class="mr-50"></i> Deposit to {{ $dealer->shop_name }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-2">
                    <div class="form-group">
                        <label class="font-weight-bold" for="depositAmount">Deposit Amount (৳) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <div class="input-group-prepend"><span class="input-group-text font-weight-bolder text-success">৳</span></div>
                            <input type="number" name="amount" id="depositAmount" class="form-control form-control-lg font-weight-bolder" step="0.01" min="0.01" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold" for="depositMethod">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" id="depositMethod" class="form-control font-weight-bold" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Bank">Bank Transfer</option>
                                    <option value="bKash">bKash</option>
                                    <option value="Nagad">Nagad</option>
                                    <option value="Rocket">Rocket</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="font-weight-bold" for="depositDate">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" id="depositDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div id="bankDetails" style="display: none;" class="p-1 mb-1 bg-light rounded border">
                        <div class="row">
                            <div class="col-6">
                                <label class="font-small-2 font-weight-bold">Bank Name</label>
                                <input type="text" name="bank_name" class="form-control form-control-sm" placeholder="Bank name">
                            </div>
                            <div class="col-6">
                                <label class="font-small-2 font-weight-bold">Account No</label>
                                <input type="text" name="account_no" class="form-control form-control-sm" placeholder="Account no">
                            </div>
                        </div>
                    </div>

                    <div id="onlineDetails" style="display: none;" class="p-1 mb-1 bg-light rounded border">
                        <label class="font-small-2 font-weight-bold">Transaction ID (TrxID)</label>
                        <input type="text" name="transaction_id" class="form-control form-control-sm" placeholder="TrxID">
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold" for="depositNote">Note / Remarks</label>
                        <input type="text" name="note" id="depositNote" class="form-control" placeholder="Optional notes...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="confirmDepositBtn">
                        <i data-feather="check" class="mr-25"></i> Save Deposit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        if (feather) {
            feather.replace({ width: 14, height: 14 });
        }

        var table = $('#statementTable').DataTable({
            processing: true,
            serverSide: true,
            order: [[2, 'desc']],
            ajax: "{{ route('admin.dealer-wallets.show', $dealer->id) }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'transaction_no', name: 'transaction_no'},
                {data: 'date', name: 'created_at'},
                {data: 'type_badge', name: 'type'},
                {data: 'amount', name: 'amount'},
                {data: 'balance', name: 'after_balance'},
                {data: 'method', name: 'payment_method'},
                {data: 'reference', name: 'reference_no'},
                {data: 'note', name: 'note'},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        $('#openDepositModalBtn').on('click', function() {
            $('#depositModal').modal('show');
            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
        });

        $('#depositMethod').on('change', function() {
            var m = $(this).val();
            if (m === 'Bank') {
                $('#bankDetails').slideDown(150);
                $('#onlineDetails').slideUp(150);
            } else if (['bKash', 'Nagad', 'Rocket'].includes(m)) {
                $('#onlineDetails').slideDown(150);
                $('#bankDetails').slideUp(150);
            } else {
                $('#bankDetails, #onlineDetails').slideUp(150);
            }
        });

        $('#depositForm').on('submit', function(e) {
            e.preventDefault();
            var amount = parseFloat($('#depositAmount').val()) || 0;
            if (amount <= 0) {
                toastr.warning('Please enter a valid amount.');
                return;
            }

            $('#confirmDepositBtn').html('<span class="spinner-border spinner-border-sm mr-25"></span> Saving...').attr('disabled', true);

            $.ajax({
                url: "{{ route('admin.dealer-wallets.deposit', $dealer->id) }}",
                type: "POST",
                data: $('#depositForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#depositModal').modal('hide');
                    $('#confirmDepositBtn').html('<i data-feather="check" class="mr-25"></i> Save Deposit').attr('disabled', false);
                    toastr.success(response.message || 'Deposit processed successfully!');
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                },
                error: function(xhr) {
                    $('#confirmDepositBtn').html('<i data-feather="check" class="mr-25"></i> Save Deposit').attr('disabled', false);
                    var msg = 'Failed to process deposit.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    toastr.error(msg);
                }
            });
        });
    });
</script>
@endpush
