@extends('layouts.dealer')
@section('page-title', 'Order Payment History')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css') }}">
@endpush

@section('breadcrumb')
    <h2 class="content-header-title float-left mb-0">Order Payments</h2>
    <div class="breadcrumb-wrapper">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dealer.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dealer.wallet.index') }}">My Wallet</a></li>
            <li class="breadcrumb-item active">Payment History</li>
        </ol>
    </div>
@endsection

@section('content')
<!-- Financial Summary Cards -->
<div class="row">
    <div class="col-xl-4 col-md-6 col-12">
        <div class="card card-statistics shadow-sm border-0 border-left-success" style="border-left: 4px solid #28c76f !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bolder mb-50">TOTAL SETTLED PAYMENTS</h6>
                        <h3 class="font-weight-bolder text-success mb-0">৳ {{ number_format($summary['total_paid'] ?? $summary['total_debit'], 2) }}</h3>
                    </div>
                    <div class="avatar bg-light-success p-50 m-0">
                        <div class="avatar-content">
                            <i data-feather="check-circle" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
                <small class="text-muted">Total settled across all orders</small>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 col-12">
        <div class="card card-statistics shadow-sm border-0 border-left-danger" style="border-left: 4px solid #ea5455 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bolder mb-50">DELIVERED ORDERS DUE</h6>
                        <h3 class="font-weight-bolder text-danger mb-0">৳ {{ number_format($summary['delivered_due'], 2) }}</h3>
                    </div>
                    <div class="avatar bg-light-danger p-50 m-0">
                        <div class="avatar-content">
                            <i data-feather="alert-circle" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
                <small class="text-danger font-weight-bold">Awaiting wallet settlement</small>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 col-12">
        <div class="card card-statistics shadow-sm border-0 border-left-info" style="border-left: 4px solid #00cfe8 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bolder mb-50">CURRENT WALLET BALANCE</h6>
                        <h3 class="font-weight-bolder text-info mb-0">৳ {{ number_format($summary['wallet_balance'], 2) }}</h3>
                    </div>
                    <div class="avatar bg-light-info p-50 m-0">
                        <div class="avatar-content">
                            <i data-feather="credit-card" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
                <a href="{{ route('dealer.wallet.index') }}" class="small text-info font-weight-bold">View Wallet Statement &rarr;</a>
            </div>
        </div>
    </div>
</div>

<!-- Order Payment History Table -->
<div class="card shadow-sm border-0">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h4 class="card-title font-weight-bolder mb-25"><i data-feather="check-square" class="mr-50 text-success"></i>Order Settlement History</h4>
            <small class="text-muted">Itemized breakdown of wallet settlements applied directly to your delivered orders</small>
        </div>
        <div>
            <a href="{{ route('dealer.wallet.index') }}" class="btn btn-outline-info btn-sm">
                <i data-feather="credit-card" class="mr-25"></i> Full Wallet Statement
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="orderPaymentsTable">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">Order Number</th>
                        <th width="20%">Paid Amount</th>
                        <th width="20%">Payment Date</th>
                        <th width="30%">Settlement Note</th>
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
        let table = $('#orderPaymentsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('dealer.payments.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                { data: 'order_number', name: 'dealerOrder.order_number' },
                { data: 'amount', name: 'amount' },
                { data: 'date', name: 'payment_date' },
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
