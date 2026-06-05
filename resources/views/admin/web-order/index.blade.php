@extends('layouts.admin')
@section('title', 'Web Order List')
@section('content')
<div class="row">
    {{-- Filter Card --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0"><i data-feather="filter"></i> Filter Orders</h4>
                    <button class="btn btn-sm btn-outline-secondary" id="toggleFilterBtn">
                        <i data-feather="chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body pt-2" id="filterBody">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group mb-1">
                            <label for="filter_invoice_no">Invoice No</label>
                            <input type="text" id="filter_invoice_no" class="form-control form-control-sm" placeholder="Search by invoice no...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-1">
                            <label for="filter_daterange">Date Range</label>
                            <input type="text" id="filter_daterange" class="form-control form-control-sm" placeholder="Select date range..." autocomplete="off" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-1">
                            <label for="filter_status">Status</label>
                            <select id="filter_status" class="form-control form-control-sm">
                                <option value="">-- All Statuses --</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-1">
                            <label>&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button id="applyFilterBtn" class="btn btn-primary btn-sm w-100">
                                    <i data-feather="search"></i> Filter
                                </button>
                                <button id="clearFilterBtn" class="btn btn-outline-secondary btn-sm w-100">
                                    <i data-feather="x"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Orders Table Card --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Web Order List</h4>
                    <div class="d-flex gap-1">
                        <button class="btn btn-success btn-sm" id="exportExcelBtn" title="Export to Excel (CSV)">
                            <i data-feather="file-text"></i> Excel
                        </button>
                        <button class="btn btn-danger btn-sm" id="exportPdfBtn" title="Export to PDF">
                            <i data-feather="file"></i> PDF
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="webOrderTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice Info</th>
                            <th>Customer</th>
                            <th>Qty Summary</th>
                            <th>Totals</th>
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
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    .gap-1 { gap: 0.5rem; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/moment/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ---- Date Range Picker ----
        var startDate = null, endDate = null;

        $('#filter_daterange').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear',
                format: 'YYYY-MM-DD'
            },
            ranges: {
                'Today':        [moment(), moment()],
                'Last 7 Days':  [moment().subtract(6, 'days'), moment()],
                'This Month':   [moment().startOf('month'), moment().endOf('month')],
                'Last Month':   [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            }
        });

        $('#filter_daterange').on('apply.daterangepicker', function(ev, picker) {
            startDate = picker.startDate.format('YYYY-MM-DD');
            endDate   = picker.endDate.format('YYYY-MM-DD');
            $(this).val(picker.startDate.format('MMM D, YYYY') + ' - ' + picker.endDate.format('MMM D, YYYY'));
        });

        $('#filter_daterange').on('cancel.daterangepicker', function() {
            startDate = null;
            endDate   = null;
            $(this).val('');
        });

        // ---- Toggle Filter Card ----
        $('#toggleFilterBtn').on('click', function () {
            $('#filterBody').slideToggle(200);
            var icon = $(this).find('[data-feather]');
            if ($('#filterBody').is(':visible')) {
                icon.attr('data-feather', 'chevron-up');
            } else {
                icon.attr('data-feather', 'chevron-down');
            }
            if (feather) feather.replace({ width: 14, height: 14 });
        });

        // ---- DataTable ----
        var table = $('#webOrderTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.web-order.index') }}",
                data: function (d) {
                    d.invoice_no   = $('#filter_invoice_no').val();
                    d.start_date   = startDate;
                    d.end_date     = endDate;
                    d.status       = $('#filter_status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'order_info', name: 'invoice_no'},
                {data: 'customer_name', name: 'customer_name', searchable: false, orderable: false},
                {data: 'items_summary', name: 'items_summary', searchable: false, orderable: false},
                {data: 'totals', name: 'total'},
                {data: 'status_badge', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        // ---- Apply / Reset Filters ----
        $('#applyFilterBtn').on('click', function () {
            table.draw();
        });

        $('#clearFilterBtn').on('click', function () {
            $('#filter_invoice_no').val('');
            $('#filter_status').val('');
            $('#filter_daterange').val('');
            startDate = null;
            endDate   = null;
            table.draw();
        });

        // Also trigger on pressing Enter in order number input
        $('#filter_invoice_no').on('keypress', function (e) {
            if (e.which === 13) table.draw();
        });

        // ---- Export Helpers ----
        function buildExportParams() {
            var params = new URLSearchParams();
            var invoiceNo = $('#filter_invoice_no').val();
            var status    = $('#filter_status').val();
            if (invoiceNo) params.append('invoice_no', invoiceNo);
            if (startDate) params.append('start_date', startDate);
            if (endDate)   params.append('end_date', endDate);
            if (status)    params.append('status', status);
            return params.toString();
        }

        $('#exportExcelBtn').on('click', function () {
            var qs = buildExportParams();
            window.location.href = "{{ route('admin.web-order.export-excel') }}" + (qs ? '?' + qs : '');
        });

        $('#exportPdfBtn').on('click', function () {
            var qs = buildExportParams();
            window.location.href = "{{ route('admin.web-order.export-list-pdf') }}" + (qs ? '?' + qs : '');
        });

        // ---- Quick Status Update ----
        $('body').on('change', '.updateStatusSelect', function() {
            var id = $(this).data('order-id');
            var status = $(this).val();
            $.ajax({
                type: "POST",
                url: "{{ route('admin.web-order.update-status') }}",
                data: { 'order_id': id, 'status': status },
                success: function(data) {
                    if (data.success) {
                        toastr.success(data.message);
                        table.draw(false);
                    } else {
                        toastr.error(data.message);
                        table.draw(false);
                    }
                },
                error: function(data) {
                    toastr.error(data.responseJSON.message || 'Failed to update status.');
                    table.draw(false);
                }
            });
        });
    });
</script>
@endpush
