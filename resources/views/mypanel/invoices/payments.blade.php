@extends('layouts.mypanel')
@section('title', 'Payment History')

@section('content')
<div class="card">
    <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Payment History</h4>
        <a href="{{ route('mypanel.invoices.index') }}" class="btn btn-sm btn-outline-secondary">Back to Invoices</a>
    </div>
    <div class="card-body table-responsive pt-2">
        <table id="paymentTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Payment No</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Note</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#paymentTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('mypanel.payments.index') }}",
        columns: [
            {data: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'date'},
            {data: 'payment_number'},
            {data: 'amount'},
            {data: 'method'},
            {data: 'note'},
        ]
    });
});
</script>
@endpush
