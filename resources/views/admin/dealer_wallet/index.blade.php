@extends('layouts.admin')
@section('title', 'Dealer Wallets')

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Dealer Wallets</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.dealer-order.index') }}">Dealer Orders</a></li>
        <li class="breadcrumb-item active">Dealer Wallets</li>
    </ol>
</div>
@endsection

@section('content')
<!-- Metrics Overview -->
<div class="row">
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between pb-0">
                <h5 class="card-title text-muted font-small-3 font-weight-bolder">TOTAL DEALERS</h5>
                <div class="avatar bg-light-primary p-50 m-0">
                    <div class="avatar-content"><i data-feather="users" class="font-medium-5"></i></div>
                </div>
            </div>
            <div class="card-body pt-1">
                <h3 class="font-weight-bolder mb-25 text-primary">{{ number_format($totalDealers ?? 0) }}</h3>
                <span class="text-muted font-small-2">Registered dealer accounts</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between pb-0">
                <h5 class="card-title text-muted font-small-3 font-weight-bolder">WALLET BALANCES HELD</h5>
                <div class="avatar bg-light-success p-50 m-0">
                    <div class="avatar-content"><i data-feather="credit-card" class="font-medium-5"></i></div>
                </div>
            </div>
            <div class="card-body pt-1">
                <h3 class="font-weight-bolder mb-25 text-success">৳ {{ number_format($totalWalletBalance ?? 0, 2) }}</h3>
                <span class="text-muted font-small-2">Total credit in dealer wallets</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between pb-0">
                <h5 class="card-title text-muted font-small-3 font-weight-bolder">DELIVERED ORDERS DUE</h5>
                <div class="avatar bg-light-danger p-50 m-0">
                    <div class="avatar-content"><i data-feather="alert-circle" class="font-medium-5"></i></div>
                </div>
            </div>
            <div class="card-body pt-1">
                <h3 class="font-weight-bolder mb-25 text-danger">৳ {{ number_format($totalDeliveredDue ?? 0, 2) }}</h3>
                <span class="text-muted font-small-2">Awaiting wallet settlement</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between pb-0">
                <h5 class="card-title text-muted font-small-3 font-weight-bolder">TOTAL DEALER ORDERS</h5>
                <div class="avatar bg-light-info p-50 m-0">
                    <div class="avatar-content"><i data-feather="package" class="font-medium-5"></i></div>
                </div>
            </div>
            <div class="card-body pt-1">
                <h3 class="font-weight-bolder mb-25 text-info">৳ {{ number_format($totalOrdersAmount ?? 0, 2) }}</h3>
                <span class="text-muted font-small-2">All-time order volume</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Wallets Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom py-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0"><i data-feather="credit-card" class="mr-50 text-primary"></i> Dealer Wallets & Due Summary</h4>
                    @can('collection.create')
                        <a href="{{ route('admin.collections.create') }}" class="btn btn-primary btn-sm">
                        <i data-feather="plus-circle" class="mr-25"></i> Add Collection / Deposit
                    </a>
                        @endcan
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="table-responsive">
                    <table id="walletsTable" class="table table-bordered table-hover text-nowrap w-100">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Dealer & Shop</th>
                                <th>Wallet Balance</th>
                                <th>Total Orders</th>
                                <th>Total Paid</th>
                                <th>Delivered Due</th>
                                <th style="width: 140px;">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="quickDepositForm">
                @csrf
                <input type="hidden" id="depositDealerId">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white"><i data-feather="plus-circle" class="mr-50"></i> Add Deposit to Dealer Wallet</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-2">
                    <div class="p-1 mb-2 bg-light rounded border text-center">
                        <span class="text-muted font-small-2 font-weight-bold text-uppercase d-block mb-25">Target Dealer</span>
                        <h5 class="font-weight-bolder text-dark mb-0" id="depositShopName">--</h5>
                        <small class="text-muted" id="depositDealerName">--</small>
                        <div class="mt-50 d-flex justify-content-around">
                            <span class="font-small-2">Wallet: <strong class="text-success" id="depositCurrentWallet">৳ 0.00</strong></span>
                            <span class="font-small-2">Delivered Due: <strong class="text-danger" id="depositDeliveredDue">৳ 0.00</strong></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold" for="depositAmount">Deposit Amount (৳) <span class="text-danger">*</span></label>
                        <div class="input-group input-group-merge">
                            <div class="input-group-prepend"><span class="input-group-text font-weight-bolder text-success">৳</span></div>
                            <input type="number" name="amount" id="depositAmount" class="form-control form-control-lg font-weight-bolder" step="0.01" min="0.01" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-12">
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
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="font-weight-bold" for="depositDate">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" id="depositDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div id="modalBankDetails" style="display: none;" class="p-1 mb-1 bg-light rounded border">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-0">
                                    <label class="font-small-2 font-weight-bold">Bank Name</label>
                                    <input type="text" name="bank_name" id="modalBankName" class="form-control form-control-sm" placeholder="Bank name">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-0">
                                    <label class="font-small-2 font-weight-bold">Account No</label>
                                    <input type="text" name="account_no" id="modalAccountNo" class="form-control form-control-sm" placeholder="Account no">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="modalOnlineDetails" style="display: none;" class="p-1 mb-1 bg-light rounded border">
                        <div class="form-group mb-0">
                            <label class="font-small-2 font-weight-bold">Transaction ID (TrxID)</label>
                            <input type="text" name="transaction_id" id="modalTrxId" class="form-control form-control-sm" placeholder="e.g. 9K72BXYZ10">
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="font-weight-bold" for="depositNote">Note / Remarks</label>
                        <input type="text" name="note" id="depositNote" class="form-control" placeholder="Optional reference notes...">
                    </div>

                    <div class="alert alert-info p-1 font-small-2 mt-1 mb-0">
                        <i data-feather="info" class="mr-25"></i>
                        <strong>FIFO Auto-Settlement:</strong> If this dealer has delivered orders with dues, the deposit will automatically pay the earliest delivered orders first (order 1, then 2, 3...).
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="submitDepositBtn">
                        <i data-feather="check" class="mr-25"></i> Confirm Deposit
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

        var table = $('#walletsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.dealer-wallets.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'dealer_info', name: 'shop_name'},
                {data: 'wallet_balance', name: 'wallet_balance'},
                {data: 'total_orders', name: 'id', orderable: false, searchable: false},
                {data: 'total_paid', name: 'id', orderable: false, searchable: false},
                {data: 'total_due', name: 'id', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        // Open deposit modal
        $(document).on('click', '.addDepositBtn', function() {
            var btn = $(this);
            $('#depositDealerId').val(btn.data('id'));
            $('#depositShopName').text(btn.data('shop'));
            $('#depositDealerName').text(btn.data('name'));
            $('#depositCurrentWallet').text('৳ ' + parseFloat(btn.data('balance')).toFixed(2));
            $('#depositDeliveredDue').text('৳ ' + parseFloat(btn.data('due')).toFixed(2));
            $('#depositAmount').val('');
            $('#depositNote').val('');
            $('#depositMethod').val('Cash').trigger('change');

            $('#depositModal').modal('show');
            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
        });

        $('#depositMethod').on('change', function() {
            var m = $(this).val();
            if (m === 'Bank') {
                $('#modalBankDetails').slideDown(150);
                $('#modalOnlineDetails').slideUp(150);
            } else if (['bKash', 'Nagad', 'Rocket'].includes(m)) {
                $('#modalOnlineDetails').slideDown(150);
                $('#modalBankDetails').slideUp(150);
            } else {
                $('#modalBankDetails, #modalOnlineDetails').slideUp(150);
            }
        });

        // Submit quick deposit
        $('#quickDepositForm').on('submit', function(e) {
            e.preventDefault();

            var dealerId = $('#depositDealerId').val();
            var amount = parseFloat($('#depositAmount').val()) || 0;

            if (amount <= 0) {
                toastr.warning('Please enter a valid deposit amount.');
                $('#depositAmount').focus();
                return;
            }

            $('#submitDepositBtn').html('<span class="spinner-border spinner-border-sm mr-25"></span> Depositing...').attr('disabled', true);

            $.ajax({
                url: "{{ url('/admin/dealer-wallets') }}/" + dealerId + "/deposit",
                type: "POST",
                data: $('#quickDepositForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#depositModal').modal('hide');
                    $('#submitDepositBtn').html('<i data-feather="check" class="mr-25"></i> Confirm Deposit').attr('disabled', false);
                    toastr.success(response.message || 'Deposit processed successfully!');
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    $('#submitDepositBtn').html('<i data-feather="check" class="mr-25"></i> Confirm Deposit').attr('disabled', false);
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
