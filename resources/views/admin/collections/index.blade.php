@extends('layouts.admin')
@section('title', 'Collections Ledger')

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Collections</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Collections Ledger</li>
    </ol>
</div>
@endsection

@section('content')
<!-- Metrics Overview -->
<div class="row">
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between pb-0">
                <h5 class="card-title text-muted font-small-3 font-weight-bolder">TOTAL COLLECTED</h5>
                <div class="avatar bg-light-success p-50 m-0">
                    <div class="avatar-content"><i data-feather="dollar-sign" class="font-medium-5"></i></div>
                </div>
            </div>
            <div class="card-body pt-1">
                <h3 class="font-weight-bolder mb-25 text-success">৳ {{ number_format($totalCollected ?? 0, 2) }}</h3>
                <span class="text-muted font-small-2">All incoming payment receipts</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between pb-0">
                <h5 class="card-title text-muted font-small-3 font-weight-bolder">DEALER COLLECTIONS</h5>
                <div class="avatar bg-light-primary p-50 m-0">
                    <div class="avatar-content"><i data-feather="briefcase" class="font-medium-5"></i></div>
                </div>
            </div>
            <div class="card-body pt-1">
                <h3 class="font-weight-bolder mb-25 text-primary">৳ {{ number_format($dealerCollected ?? 0, 2) }}</h3>
                <span class="text-muted font-small-2">Credited to Dealer Wallets</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between pb-0">
                <h5 class="card-title text-muted font-small-3 font-weight-bolder">POS COLLECTIONS</h5>
                <div class="avatar bg-light-info p-50 m-0">
                    <div class="avatar-content"><i data-feather="shopping-bag" class="font-medium-5"></i></div>
                </div>
            </div>
            <div class="card-body pt-1">
                <h3 class="font-weight-bolder mb-25 text-info">৳ {{ number_format($posCollected ?? 0, 2) }}</h3>
                <span class="text-muted font-small-2">Store walk-in order dues</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between pb-0">
                <h5 class="card-title text-muted font-small-3 font-weight-bolder">WEB ORDER COLLECTIONS</h5>
                <div class="avatar bg-light-warning p-50 m-0">
                    <div class="avatar-content"><i data-feather="globe" class="font-medium-5"></i></div>
                </div>
            </div>
            <div class="card-body pt-1">
                <h3 class="font-weight-bolder mb-25 text-warning">৳ {{ number_format($webCollected ?? 0, 2) }}</h3>
                <span class="text-muted font-small-2">Online web order receipts</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom py-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0"><i data-feather="file-text" class="mr-50 text-primary"></i> Collections Ledger</h4>
                    <div class="d-flex">
                        <a href="{{ route('admin.dealer-wallets.index') }}" class="btn btn-outline-primary btn-sm mr-1">
                            <i data-feather="credit-card" class="mr-25"></i> Dealer Wallets
                        </a>
                        <a href="{{ route('admin.collections.create') }}" class="btn btn-primary btn-sm">
                            <i data-feather="plus-circle" class="mr-25"></i> Record Collection
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-2">
                <!-- Filter Bar -->
                <div class="border rounded bg-light p-1 mb-2">
                    <div class="row align-items-end">
                        <div class="col-lg-3 col-md-6 col-12 mb-1 mb-lg-0">
                            <label class="font-small-2 font-weight-bold">Collection Type</label>
                            <select id="filterType" class="form-control form-control-sm">
                                <option value="">All Types</option>
                                <option value="dealer">Dealer Wallet Deposit</option>
                                <option value="pos_order">POS Order Due</option>
                                <option value="web_order">Web Order</option>
                                <option value="custom_order">Custom Order</option>
                            </select>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12 mb-1 mb-lg-0">
                            <label class="font-small-2 font-weight-bold">Payment Method</label>
                            <select id="filterMethod" class="form-control form-control-sm">
                                <option value="">All Methods</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank">Bank Transfer</option>
                                <option value="bKash">bKash</option>
                                <option value="Nagad">Nagad</option>
                                <option value="Rocket">Rocket</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-4 col-12 mb-1 mb-lg-0">
                            <label class="font-small-2 font-weight-bold">From Date</label>
                            <input type="date" id="filterFrom" class="form-control form-control-sm">
                        </div>

                        <div class="col-lg-2 col-md-4 col-12 mb-1 mb-lg-0">
                            <label class="font-small-2 font-weight-bold">To Date</label>
                            <input type="date" id="filterTo" class="form-control form-control-sm">
                        </div>

                        <div class="col-lg-2 col-md-4 col-12 text-md-right mt-1 mt-lg-0">
                            <button type="button" id="resetFilterBtn" class="btn btn-outline-secondary btn-sm btn-block" title="Reset Filters">
                                <i data-feather="refresh-cw" class="mr-25"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="collectionsTable" class="table table-bordered table-hover text-nowrap w-100">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>Receipt No</th>
                                <th>Party / Customer</th>
                                <th>Collection Type</th>
                                <th>Amount</th>
                                <th>Method & Reference</th>
                                <th>Payment Date</th>
                                <th>Received By</th>
                                <th style="width: 50px;">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewCollectionModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title text-white"><i data-feather="file-text" class="mr-50"></i> Collection Receipt Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2">
                <div class="text-center mb-2">
                    <span class="badge badge-pill badge-light-success font-medium-3 px-2 py-50" id="modalAmount">৳ 0.00</span>
                    <h6 class="font-weight-bolder text-muted mt-50" id="modalNo">#COL-000</h6>
                </div>
                <table class="table table-sm table-striped">
                    <tr>
                        <th class="text-muted">Payment Date:</th>
                        <td id="modalDate" class="font-weight-bold text-dark">--</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Payment Method:</th>
                        <td id="modalMethod" class="font-weight-bold">--</td>
                    </tr>
                    <tr id="modalTrxRow">
                        <th class="text-muted">Transaction ID:</th>
                        <td id="modalTrx">--</td>
                    </tr>
                    <tr id="modalBankRow">
                        <th class="text-muted">Bank Name:</th>
                        <td id="modalBank">--</td>
                    </tr>
                    <tr id="modalAccRow">
                        <th class="text-muted">Account No:</th>
                        <td id="modalAcc">--</td>
                    </tr>
                    <tr>
                        <th class="text-muted">Notes:</th>
                        <td id="modalNote" class="text-break">--</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
            </div>
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

        var table = $('#collectionsTable').DataTable({
            processing: true,
            serverSide: true,
            order: [[6, 'desc']],
            ajax: {
                url: "{{ route('admin.collections.index') }}",
                data: function(d) {
                    d.collection_type = $('#filterType').val();
                    d.payment_method = $('#filterMethod').val();
                    d.date_from = $('#filterFrom').val();
                    d.date_to = $('#filterTo').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'collection_no', name: 'collection_no'},
                {data: 'party_info', name: 'party_id'},
                {data: 'type_badge', name: 'collection_type'},
                {data: 'amount', name: 'amount'},
                {data: 'method_badge', name: 'payment_method'},
                {data: 'date', name: 'payment_date'},
                {data: 'creator', name: 'created_by', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        $('#filterType, #filterMethod, #filterFrom, #filterTo').on('change', function() {
            table.ajax.reload();
        });

        $('#resetFilterBtn').on('click', function() {
            $('#filterType, #filterMethod, #filterFrom, #filterTo').val('');
            table.ajax.reload();
        });

        $(document).on('click', '.viewCollectionBtn', function() {
            var btn = $(this);
            $('#modalNo').text(btn.data('no'));
            $('#modalAmount').text(btn.data('amount'));
            $('#modalDate').text(btn.data('date'));
            $('#modalMethod').text(btn.data('method'));
            $('#modalNote').text(btn.data('note'));

            var trx = btn.data('trx');
            var bank = btn.data('bank');
            var acc = btn.data('acc');

            if (trx && trx !== '-') {
                $('#modalTrx').text(trx);
                $('#modalTrxRow').show();
            } else {
                $('#modalTrxRow').hide();
            }

            if (bank && bank !== '-') {
                $('#modalBank').text(bank);
                $('#modalBankRow').show();
            } else {
                $('#modalBankRow').hide();
            }

            if (acc && acc !== '-') {
                $('#modalAcc').text(acc);
                $('#modalAccRow').show();
            } else {
                $('#modalAccRow').hide();
            }

            $('#viewCollectionModal').modal('show');
            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
        });
    });
</script>
@endpush
