@extends('layouts.admin')
@section('title', 'POS Order Due List')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title mb-0">POS Order Due List</h4>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="posDueTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order Info</th>
                            <th>Customer</th>
                            <th>Financial Summary</th>
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

@push('scripts')
<script>
    $(function () {
        $('#posDueTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.pos-order.due-list') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'order_info', name: 'order_number'},
                {data: 'customer_info', name: 'customer.name'},
                {data: 'financials', name: 'payable_amount'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) feather.replace({ width: 14, height: 14 });
            }
        });
    });
</script>
@endpush
