@extends('layouts.admin')
@section('title', 'Purchase Due List')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title mb-0">Purchase Invoice Due List</h4>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="dueListTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Purchase Info</th>
                            <th>Vendor</th>
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
@endsection

@push('scripts')
<script>
    $(function () {
        $('#dueListTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.purchase.due-list') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'purchase_info', name: 'purchase_number'},
                {data: 'vendor_name', name: 'vendor.name'},
                {data: 'financials', name: 'grand_total'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) feather.replace({ width: 14, height: 14 });
            }
        });
    });
</script>
@endpush
