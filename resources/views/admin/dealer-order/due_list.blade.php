@extends('layouts.admin')
@section('title', 'Dealer Order Due List')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Dealer Order Due List</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Dealer Order Due List</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom p-1">
                        <h4 class="card-title mb-0">Orders with Pending Due</h4>
                    </div>
                    <div class="card-body border-bottom p-3">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label class="small font-weight-bold">Order Number</label>
                                <input type="text" id="filterOrderNumber" class="form-control form-control-sm" placeholder="Search by order #...">
                            </div>
                            <div class="col-md-3">
                                <label class="small font-weight-bold">Shop Name</label>
                                <input type="text" id="filterShopName" class="form-control form-control-sm" placeholder="Search by shop name...">
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold">Dealer Phone</label>
                                <input type="text" id="filterDealerPhone" class="form-control form-control-sm" placeholder="Phone...">
                            </div>
                            <div class="col-md-2">
                                <label class="small font-weight-bold">Status</label>
                                <select id="filterStatus" class="form-control form-control-sm">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="confirm">Confirm</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div class="col-md-2 text-right">
                                <button type="button" id="filterBtn" class="btn btn-sm btn-info shadow-sm mr-1"><i data-feather="filter"></i> Filter</button>
                                <button type="button" id="resetBtn" class="btn btn-sm btn-secondary shadow-sm"><i data-feather="refresh-ccw"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive pt-2">
                        <table id="dealerDueTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Order Info</th>
                                    <th>Dealer</th>
                                    <th>Financial Summary</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        let table = $('#dealerDueTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.dealer-order.due-list') }}",
                data: function (d) {
                    d.order_number = $('#filterOrderNumber').val();
                    d.shop_name    = $('#filterShopName').val();
                    d.dealer_phone = $('#filterDealerPhone').val();
                    d.status       = $('#filterStatus').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false},
                {data: 'order_info', name: 'order_number'},
                {data: 'dealer_name', name: 'dealer.name'},
                {data: 'totals', name: 'grand_total'},
                {data: 'status_badge', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            drawCallback: function() {
                if (typeof feather !== 'undefined') feather.replace({ width: 14, height: 14 });
            }
        });

        $('#filterBtn').click(function() { table.draw(); });
        $('#resetBtn').click(function() {
            $('#filterOrderNumber, #filterShopName, #filterDealerPhone, #filterStatus').val('');
            table.draw();
        });
    });
</script>
@endpush
