@extends('layouts.admin')
@section('title', 'Custom Order Sales Report')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <h2 class="content-header-title float-left mb-0">Custom Order Sales Report</h2>
        </div>
    </div>
    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Order ID</label>
                                <input type="text" name="order_number" id="order_number" class="form-control" placeholder="ORD-XXXX">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="order_confirm">Order Confirm</option>
                                    <option value="delivered">Delivered</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mt-2">
                                <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                                <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Export
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" id="exportExcel">Excel</a>
                                        <a class="dropdown-item" href="#" id="exportPdf">PDF</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-datatable p-2">
                <table class="table" id="reportTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Financials</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        var table = $('#reportTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.report.custom-order-report') }}",
                data: function (d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.order_number = $('#order_number').val();
                    d.status = $('#status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'date' },
                { data: 'order_number' },
                { data: 'customer_info' },
                { data: 'financials' },
                { data: 'status' }
            ]
        });

        $('#filterBtn').click(function() {
            table.draw();
        });

        $('#resetBtn').click(function() {
            $('#filterForm')[0].reset();
            table.draw();
        });

        $('#exportExcel').click(function(e) {
            e.preventDefault();
            var start = $('#start_date').val();
            var end = $('#end_date').val();
            var order_number = $('#order_number').val();
            var status = $('#status').val();
            window.location.href = "{{ route('admin.report.export-custom-sales-excel') }}?start_date=" + start + "&end_date=" + end + "&order_number=" + order_number + "&status=" + status;
        });

        $('#exportPdf').click(function(e) {
            e.preventDefault();
            var start = $('#start_date').val();
            var end = $('#end_date').val();
            var order_number = $('#order_number').val();
            var status = $('#status').val();
            window.location.href = "{{ route('admin.report.export-custom-sales-pdf') }}?start_date=" + start + "&end_date=" + end + "&order_number=" + order_number + "&status=" + status;
        });
    });
</script>
@endpush
