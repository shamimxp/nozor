@extends('layouts.dealer')
@section('title', 'My Orders')

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/pickers/flatpickr/flatpickr.min.css') }}">
@endpush

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">My Orders</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dealer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">My Orders</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 col-12">
        <div class="card card-statistics shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bolder mb-50">TOTAL ORDERS VALUE</h6>
                        <h3 class="font-weight-bolder text-primary mb-0">৳ {{ number_format($totalOrdersAmount ?? 0, 2) }}</h3>
                    </div>
                    <div class="avatar bg-light-primary p-50 m-0">
                        <div class="avatar-content">
                            <i data-feather="shopping-bag" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
                <small class="text-muted">Total placed orders</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-12">
        <div class="card card-statistics shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bolder mb-50">TOTAL PAID</h6>
                        <h3 class="font-weight-bolder text-success mb-0">৳ {{ number_format($totalPaidAmount ?? 0, 2) }}</h3>
                    </div>
                    <div class="avatar bg-light-success p-50 m-0">
                        <div class="avatar-content">
                            <i data-feather="check-circle" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
                <small class="text-success font-weight-bold">Settled payments</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-12">
        <div class="card card-statistics shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bolder mb-50">TOTAL DUE</h6>
                        <h3 class="font-weight-bolder text-danger mb-0">৳ {{ number_format($totalDueAmount ?? 0, 2) }}</h3>
                    </div>
                    <div class="avatar bg-light-danger p-50 m-0">
                        <div class="avatar-content">
                            <i data-feather="alert-circle" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
                <small class="text-muted">Delivered Due: <strong class="text-danger">৳ {{ number_format($deliveredDueAmount ?? 0, 2) }}</strong></small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 col-12">
        <div class="card card-statistics shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bolder mb-50">WALLET BALANCE</h6>
                        <h3 class="font-weight-bolder text-info mb-0">৳ {{ number_format($walletBalance ?? 0, 2) }}</h3>
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

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="row align-items-end mb-3">
            <div class="col-md-2">
                <label class="small font-weight-bold">Order Number</label>
                <input type="text" id="filterOrderNumber" class="form-control form-control-sm" placeholder="Search...">
            </div>
            <div class="col-md-2">
                <label class="small font-weight-bold">Start Date</label>
                <input type="text" id="filterStartDate" class="form-control form-control-sm flatpickr" placeholder="mm/dd/yyyy">
            </div>
            <div class="col-md-2">
                <label class="small font-weight-bold">End Date</label>
                <input type="text" id="filterEndDate" class="form-control form-control-sm flatpickr" placeholder="mm/dd/yyyy">
            </div>
            <div class="col-md-3">
                <label class="small font-weight-bold">Status</label>
                <select id="filterStatus" class="form-control form-control-sm">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="confirm">Confirm</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-md-3 text-right">
                <button type="button" id="filterBtn" class="btn btn-sm btn-info shadow-sm mr-1"><i data-feather="filter"></i> Filter</button>
                <button type="button" id="resetBtn" class="btn btn-sm btn-secondary shadow-sm"><i data-feather="refresh-ccw"></i></button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="dealerOrderTable">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="30%">Order Info</th>
                        <th width="35%">Financials</th>
                        <th width="15%">Status</th>
                        <th width="15%" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/app-assets/vendors/js/pickers/flatpickr/flatpickr.min.js') }}"></script>
<script>
    $(document).ready(function() {
        if ($('.flatpickr').length) {
            $('.flatpickr').flatpickr({
                dateFormat: "Y-m-d",
                altFormat: "m/d/Y",
                altInput: true,
                allowInput: true
            });
        }

        let table = $('#dealerOrderTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dealer.orders.index') }}",
                data: function (d) {
                    d.order_number = $('#filterOrderNumber').val();
                    d.start_date   = $('#filterStartDate').val();
                    d.end_date     = $('#filterEndDate').val();
                    d.status       = $('#filterStatus').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                { data: 'order_info', name: 'order_number' },
                { data: 'totals', name: 'grand_total' },
                { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            ],
            drawCallback: function() {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        });

        $('#filterBtn').click(function() { table.draw(); });
        $('#resetBtn').click(function() {
            $('#filterOrderNumber, #filterStartDate, #filterEndDate, #filterStatus').val('');
            table.draw();
        });
    });
</script>
@endpush
