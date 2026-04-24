@extends('layouts.admin')
@section('title', 'Vendor Payment History Summary')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title mb-0">Vendor History & Due Summary</h4>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="vendorHistoryTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Vendor Name</th>
                            <th>Company</th>
                            <th>Total Purchased</th>
                            <th>Total Paid</th>
                            <th>Total Due</th>
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
        var table = $('#vendorHistoryTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.purchase.vendor-history') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name'},
                {data: 'company', name: 'company'},
                {data: 'total_purchased', name: 'total_purchased'},
                {data: 'total_paid', name: 'total_paid'},
                {data: 'total_due', name: 'total_due'},
                {
                    data: 'id',
                    name: 'action',
                    render: function(data) {
                        return '<a href="{{ url("admin/purchase/vendor-payments") }}/' + data + '" class="btn btn-sm btn-info">View Details</a>';
                    }
                },
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                }
            }
        });
    });
</script>
@endpush
