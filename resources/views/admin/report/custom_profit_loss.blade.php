@extends('layouts.admin')
@section('title', 'Custom Order Profit/Loss Report')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <h2 class="content-header-title float-left mb-0">Custom Order Profit/Loss Report</h2>
        </div>
    </div>
    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <form id="filterForm">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
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
                            <th>Revenue</th>
                            <th>Cost (COGS)</th>
                            <th>Profit/Loss</th>
                        </tr>
                    </thead>
                    <tfoot style="background-color: #f8f9fa; font-weight: bold;">
                        <tr>
                            <td colspan="3" class="text-right">Totals:</td>
                            <td id="totalRevenue">TK0.00</td>
                            <td id="totalCost">TK0.00</td>
                            <td id="totalProfit">TK0.00</td>
                        </tr>
                    </tfoot>
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
                url: "{{ route('admin.report.custom-profit-loss') }}",
                data: function (d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'date' },
                { data: 'order_number' },
                { data: 'revenue', render: $.fn.dataTable.render.number(',', '.', 2, 'TK ') },
                { data: 'cost', render: $.fn.dataTable.render.number(',', '.', 2, 'TK ') },
                { data: 'profit' }
            ],
            drawCallback: function(settings) {
                var api = this.api();
                var totalRev = api.column(3).data().reduce(function (a, b) { return parseFloat(a) + parseFloat(b); }, 0);
                var totalCost = api.column(4).data().reduce(function (a, b) { return parseFloat(a) + parseFloat(b); }, 0);
                
                $('#totalRevenue').text('TK ' + totalRev.toLocaleString(undefined, {minimumFractionDigits: 2}));
                $('#totalCost').text('TK ' + totalCost.toLocaleString(undefined, {minimumFractionDigits: 2}));
                
                var profit = totalRev - totalCost;
                $('#totalProfit').text('TK ' + profit.toLocaleString(undefined, {minimumFractionDigits: 2}))
                    .removeClass('text-success text-danger')
                    .addClass(profit >= 0 ? 'text-success' : 'text-danger');
            }
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
            window.location.href = "{{ route('admin.report.export-custom-profit-loss-excel') }}?start_date=" + start + "&end_date=" + end;
        });

        $('#exportPdf').click(function(e) {
            e.preventDefault();
            var start = $('#start_date').val();
            var end = $('#end_date').val();
            window.location.href = "{{ route('admin.report.export-custom-profit-loss-pdf') }}?start_date=" + start + "&end_date=" + end;
        });
    });
</script>
@endpush
