@extends('layouts.admin')
@section('title', 'Manufacture Report')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Manufacture Report</h4>
            </div>
            <div class="card-body mt-2">
                <form id="filterForm">
                    <div class="row mb-2">
                        <div class="col-md-3">
                            <label>Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Product</label>
                            <select name="product_id" id="product_id" class="form-control select2">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Dealer</label>
                            <select name="dealer_id" id="dealer_id" class="form-control select2">
                                <option value="">All Dealers</option>
                                @foreach($dealers as $dealer)
                                <option value="{{ $dealer->id }}">{{ $dealer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mt-1">
                            <label>Status</label>
                            <select name="is_confirm" id="is_confirm" class="form-control">
                                <option value="">All</option>
                                <option value="1">Confirmed</option>
                                <option value="0">Pending</option>
                            </select>
                        </div>
                        <div class="col-md-3 mt-1 align-self-end">
                            <button type="button" class="btn btn-primary" id="filterBtn">Filter</button>
                            <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered datatable">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Dealer</th>
                                <th>Status</th>
                                <th>Quantity</th>
                                <th>Body Cost</th>
                                <th>Finishing Cost</th>
                                <th>Grand Total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" style="text-align:right">Total:</th>
                                <th>0</th>
                                <th>0.00</th>
                                <th>0.00</th>
                                <th>0.00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        $('.select2').select2();

        var table = $('.datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.report.manufacture-report') }}",
                data: function (d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.product_id = $('#product_id').val();
                    d.dealer_id = $('#dealer_id').val();
                    d.is_confirm = $('#is_confirm').val();
                }
            },
            columns: [
                {data: 'invoice_no', name: 'invoice_no'},
                {data: 'date', name: 'date'},
                {data: 'product_name', name: 'product.name'},
                {data: 'dealer_name', name: 'dealer.name'},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'manufacture_qty', name: 'manufacture_qty'},
                {data: 'body_total', name: 'body_total'},
                {data: 'finishing_total', name: 'finishing_total'},
                {data: 'grand_total', name: 'grand_total'},
            ],
            dom: 'Bfrtip',
            buttons: [
                'excelHtml5',
                'pdfHtml5'
            ],
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                var intVal = function (i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                            i : 0;
                };

                var totalQty = api
                    .column(5)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var totalBody = api
                    .column(6)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var totalFinishing = api
                    .column(7)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var totalGrand = api
                    .column(8)
                    .data()
                    .reduce(function (a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                $(api.column(5).footer()).html(totalQty);
                $(api.column(6).footer()).html(totalBody.toFixed(2));
                $(api.column(7).footer()).html(totalFinishing.toFixed(2));
                $(api.column(8).footer()).html(totalGrand.toFixed(2));
            }
        });

        $('#filterBtn').click(function () {
            table.draw();
        });

        $('#resetBtn').click(function () {
            $('#filterForm')[0].reset();
            $('.select2').val('').trigger('change');
            table.draw();
        });
    });
</script>
@endpush
