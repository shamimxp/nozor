@extends('layouts.dealer')
@section('page-title', 'My Wallet Statement')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css') }}">
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
                        <th width="4%">#</th>
                        <th width="12%">Txn No</th>
                        <th width="14%">Date & Time</th>
                        <th width="12%">Type</th>
                        <th width="12%">Amount</th>
                        <th width="14%">Balance Flow</th>
                        <th width="10%">Method</th>
                        <th width="10%">Reference</th>
                        <th width="8%">Status</th>
                        <th width="8%">Note</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
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
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
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
            order: [[0, 'desc']],
            drawCallback: function() {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        });
    });
</script>
@endpush
