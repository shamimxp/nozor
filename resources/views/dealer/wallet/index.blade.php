@extends('layouts.dealer')
@section('page-title', 'My Wallet Statement')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css') }}">
    <style>
        .card-statistics {
            transition: transform 0.2s;
        }
        .card-statistics:hover {
            transform: translateY(-2px);
        }
        #dealerWalletTable th, #dealerWalletTable td {
            vertical-align: middle !important;
            font-size: 13px;
        }
        #dealerWalletTable td {
            white-space: nowrap;
        }
        #dealerWalletTable td:last-child {
            white-space: normal;
            min-width: 180px;
        }
        .note-cell-wrapper {
            min-width: 160px;
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
@endpush

@section('breadcrumb')
    <h2 class="content-header-title float-left mb-0">My Wallet</h2>
    <div class="breadcrumb-wrapper">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dealer.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Wallet Statement</li>
        </ol>
    </div>
@endsection

@section('content')
<!-- Financial Summary Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 col-12">
        <div class="card card-statistics shadow-sm border-0 border-left-info" style="border-left: 4px solid #00cfe8 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bolder mb-50">WALLET BALANCE</h6>
                        <h3 class="font-weight-bolder text-info mb-0">৳ {{ number_format($summary['wallet_balance'], 2) }}</h3>
                    </div>
                    <div class="avatar bg-light-info p-50 m-0">
                        <div class="avatar-content">
                            <i data-feather="credit-card" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
                <small class="text-muted">Available credit for orders</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12">
        <div class="card card-statistics shadow-sm border-0 border-left-success" style="border-left: 4px solid #28c76f !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bolder mb-50">TOTAL DEPOSITS</h6>
                        <h3 class="font-weight-bolder text-success mb-0">৳ {{ number_format($summary['total_credit'], 2) }}</h3>
                    </div>
                    <div class="avatar bg-light-success p-50 m-0">
                        <div class="avatar-content">
                            <i data-feather="arrow-down-left" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
                <small class="text-success font-weight-bold">Total funds added to wallet</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12">
        <div class="card card-statistics shadow-sm border-0 border-left-warning" style="border-left: 4px solid #ff9f43 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bolder mb-50">ORDER PAYMENTS</h6>
                        <h3 class="font-weight-bolder text-warning mb-0">৳ {{ number_format($summary['total_debit'], 2) }}</h3>
                    </div>
                    <div class="avatar bg-light-warning p-50 m-0">
                        <div class="avatar-content">
                            <i data-feather="shopping-bag" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
                <small class="text-muted">Auto-settled against delivered orders</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 col-12">
        <div class="card card-statistics shadow-sm border-0 border-left-danger" style="border-left: 4px solid #ea5455 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bolder mb-50">DELIVERED DUE</h6>
                        <h3 class="font-weight-bolder text-danger mb-0">৳ {{ number_format($summary['delivered_due'], 2) }}</h3>
                    </div>
                    <div class="avatar bg-light-danger p-50 m-0">
                        <div class="avatar-content">
                            <i data-feather="alert-circle" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
                <small class="text-danger font-weight-bold">Outstanding on delivered orders</small>
            </div>
        </div>
    </div>
</div>

<!-- Wallet Statement Ledger -->
<div class="card shadow-sm border-0">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h4 class="card-title font-weight-bolder mb-25"><i data-feather="list" class="mr-50 text-primary"></i>Wallet Statement Ledger</h4>
            <small class="text-muted">Complete historical statement of credits (deposits) and debits (order settlements)</small>
        </div>
        <div>
            <a href="{{ route('dealer.payments.index') }}" class="btn btn-outline-primary btn-sm">
                <i data-feather="dollar-sign" class="mr-25"></i> Order Payment Breakdown
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="dealerWalletTable">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 45px;" class="text-center">#</th>
                        <th style="min-width: 130px;">Txn No</th>
                        <th style="min-width: 145px;">Date &amp; Time</th>
                        <th style="min-width: 130px;">Type</th>
                        <th style="min-width: 110px;">Amount</th>
                        <th style="min-width: 165px;">Balance Flow</th>
                        <th style="min-width: 95px;">Method</th>
                        <th style="min-width: 120px;">Reference</th>
                        <th style="min-width: 95px;">Status</th>
                        <th style="min-width: 180px;">Note</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Show Note Modal -->
<div class="modal fade" id="showNoteModal" tabindex="-1" role="dialog" aria-labelledby="showNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 10px; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.15); overflow: hidden;">
            <div class="modal-header py-1 px-2" style="background-color: #111C38; color: #ffffff;">
                <div class="d-flex align-items-center">
                    <span class="mr-75 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background: rgba(255,255,255,0.15); border-radius: 6px;">
                        <i data-feather="file-text" style="width: 16px; height: 16px; stroke: #ffffff;"></i>
                    </span>
                    <div>
                        <h5 class="modal-title font-weight-bolder text-white mb-0" id="showNoteModalLabel">Transaction Note</h5>
                        <small class="text-white-50 font-small-1" id="modalTxnSubtitle">Txn Details</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.85;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2" style="background-color: #FDFAF5;">
                <!-- Transaction Meta Info Card -->
                <div class="bg-white p-1 rounded border mb-1 d-flex justify-content-between align-items-center flex-wrap" style="gap: 8px;">
                    <div>
                        <small class="text-muted d-block font-small-1 font-weight-bold text-uppercase">Transaction No</small>
                        <span class="font-weight-bolder text-primary font-small-3" id="modalTxnNo">#</span>
                    </div>
                    <div>
                        <small class="text-muted d-block font-small-1 font-weight-bold text-uppercase">Date &amp; Time</small>
                        <span class="font-weight-bold text-dark font-small-2" id="modalTxnDate">-</span>
                    </div>
                    <div>
                        <small class="text-muted d-block font-small-1 font-weight-bold text-uppercase">Type</small>
                        <span class="badge badge-pill font-small-1 font-weight-bold" id="modalTxnTypeBadge">-</span>
                    </div>
                </div>

                <!-- Full Note Content -->
                <label class="form-label font-weight-bold text-dark font-small-2 mb-50">
                    <i data-feather="message-square" style="width: 13px; height: 13px; margin-right: 4px;"></i> Complete Note &amp; Explanation:
                </label>
                <div class="bg-white p-2 rounded border" style="min-height: 100px; max-height: 250px; overflow-y: auto; font-size: 13.5px; line-height: 1.6; color: #2d3748; white-space: pre-wrap; font-family: inherit;" id="modalNoteContent">
                </div>
            </div>
            <div class="modal-footer py-1 px-2 border-top bg-white">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="border-radius: 6px; font-weight: 600;">
                    <i data-feather="x" style="width: 13px; height: 13px; margin-right: 4px;"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
<script>
    $(document).ready(function() {
        let table = $('#dealerWalletTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('dealer.wallet.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, className: 'text-center' },
                { data: 'transaction_no', name: 'transaction_no' },
                { data: 'date', name: 'created_at' },
                { data: 'type_badge', name: 'type' },
                { data: 'amount', name: 'amount' },
                { data: 'balance', name: 'after_balance', orderable: false, searchable: false },
                { data: 'method', name: 'payment_method' },
                { data: 'reference', name: 'reference_no' },
                { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false },
                { data: 'note', name: 'note' }
            ],
            order: [],
            pageLength: 10,
            drawCallback: function() {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        });

        // Show Note Modal Trigger
        $(document).on('click', '.btn-show-note', function() {
            let txn = $(this).data('txn');
            let date = $(this).data('date');
            let type = $(this).data('type');
            let typeBadge = $(this).data('type-badge');
            let note = $(this).data('note');

            $('#modalTxnNo').text(txn);
            $('#modalTxnSubtitle').text(txn + ' • ' + date);
            $('#modalTxnDate').text(date);
            $('#modalTxnTypeBadge').attr('class', 'badge badge-pill font-small-1 font-weight-bold ' + typeBadge).text(type);
            $('#modalNoteContent').text(note);

            $('#showNoteModal').modal('show');
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    });
</script>
@endpush
