@extends('layouts.dealer')
@section('title', 'My Orders')

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

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="row align-items-end mb-3">
            <div class="col-md-2">
                <label class="small font-weight-bold">Order Number</label>
                <input type="text" id="filterOrderNumber" class="form-control form-control-sm" placeholder="Search...">
            </div>
            <div class="col-md-2">
                <label class="small font-weight-bold">Start Date</label>
                <input type="date" id="filterStartDate" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="small font-weight-bold">End Date</label>
                <input type="date" id="filterEndDate" class="form-control form-control-sm">
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
<script>
    $(document).ready(function() {
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
