@extends('layouts.mypanel')
@section('title', 'Manufacture Orders')

@section('content')
<div class="row mb-2">
    <div class="col-md-4">
        <div class="card text-center border shadow-none filter-stat-card cursor-pointer" data-filter="" style="cursor:pointer;">
            <div class="card-body py-2">
                <h2 class="font-weight-bolder text-primary mb-0">{{ $totalOrders }}</h2>
                <p class="mb-0">Total Orders</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border shadow-none filter-stat-card cursor-pointer" data-filter="pending" style="cursor:pointer;">
            <div class="card-body py-2">
                <h2 class="font-weight-bolder text-warning mb-0">{{ $pendingOrders }}</h2>
                <p class="mb-0">Pending</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center border shadow-none filter-stat-card cursor-pointer" data-filter="collected" style="cursor:pointer;">
            <div class="card-body py-2">
                <h2 class="font-weight-bolder text-success mb-0">{{ $collectedOrders }}</h2>
                <p class="mb-0">Collected</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Assigned Manufacture Orders</h4>
        <select id="statusFilter" class="form-control form-control-sm" style="max-width:180px;">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="collected">Collected</option>
        </select>
    </div>
    <div class="card-body table-responsive pt-2">
        <table id="mfgTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Invoice</th>
                    <th>Product</th>
                    <th>Part</th>
                    <th>Qty</th>
                    <th>Received</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    var table = $('#mfgTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('mypanel.manufactures.index') }}",
            data: function (d) { d.status = $('#statusFilter').val(); }
        },
        columns: [
            {data: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'date'},
            {data: 'invoice_no'},
            {data: 'product'},
            {data: 'part_type'},
            {data: 'manufacture_qty'},
            {data: 'received_qty'},
            {data: 'total_price'},
            {data: 'collection_status'},
            {data: 'action', orderable: false, searchable: false},
        ],
        drawCallback: function () { if (window.feather) feather.replace({width:14,height:14}); }
    });

    $('#statusFilter').on('change', function () { table.draw(); });
    $('.filter-stat-card').on('click', function () {
        $('#statusFilter').val($(this).data('filter')).trigger('change');
    });
});
</script>
@endpush
