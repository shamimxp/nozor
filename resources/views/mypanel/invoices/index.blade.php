@extends('layouts.mypanel')
@section('title', 'Invoices')

@section('content')
<div class="row mb-2">
    <div class="col-md-3">
        <div class="card text-center border shadow-none">
            <div class="card-body py-2">
                <h2 class="font-weight-bolder text-info mb-0">৳{{ number_format($totalAmount, 2) }}</h2>
                <p class="mb-0">Total Billed</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border shadow-none">
            <div class="card-body py-2">
                <h2 class="font-weight-bolder text-success mb-0">৳{{ number_format($totalPaid, 2) }}</h2>
                <p class="mb-0">Total Paid</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border shadow-none">
            <div class="card-body py-2">
                <h2 class="font-weight-bolder text-danger mb-0">৳{{ number_format($totalDue, 2) }}</h2>
                <p class="mb-0">Due</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center border shadow-none">
            <div class="card-body py-2">
                <h2 class="font-weight-bolder text-primary mb-0">{{ $payoutCount }}</h2>
                <p class="mb-0">Payout Records</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Invoice / Payment List</h4>
        <a href="{{ route('mypanel.payments.index') }}" class="btn btn-sm btn-outline-primary">Payment History</a>
    </div>
    <div class="card-body table-responsive pt-2">
        <table id="invoiceTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Invoice</th>
                    <th>Product</th>
                    <th>Part</th>
                    <th>Total</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>Status</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#invoiceTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('mypanel.invoices.index') }}",
        columns: [
            {data: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'date'},
            {data: 'invoice_no'},
            {data: 'product'},
            {data: 'part_type'},
            {data: 'total_amount'},
            {data: 'paid_amount'},
            {data: 'due_amount'},
            {data: 'status'},
        ],
        drawCallback: function () { if (window.feather) feather.replace({width:14,height:14}); }
    });
});
</script>
@endpush
