@extends('layouts.admin')
@section('title', 'Dealer Order Requests')

@section('content')
<div class="content-header mb-3 mt-n1">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="font-weight-bolder mb-0">Dealer Order Requests</h2>
            <p class="text-muted mb-0">Manage and track dealer order requests</p>
        </div>
    </div>
</div>



<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="row align-items-end mb-3">
            <div class="col-md-3">
                <label class="small font-weight-bold">Request Number</label>
                <input type="text" id="filterRequestNumber" class="form-control form-control-sm" placeholder="Search by request #...">
            </div>
            <div class="col-md-3">
                <label class="small font-weight-bold">Start Date</label>
                <input type="date" id="filterStartDate" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="small font-weight-bold">End Date</label>
                <input type="date" id="filterEndDate" class="form-control form-control-sm">
            </div>
            <div class="col-md-3 text-right">
                <button type="button" id="filterBtn" class="btn btn-sm btn-info shadow-sm mr-1"><i data-feather="filter"></i> Filter</button>
                <button type="button" id="resetBtn" class="btn btn-sm btn-secondary shadow-sm mr-1"><i data-feather="refresh-ccw"></i></button>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="order-requests-table">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">Request Info</th>
                        <th width="20%">Dealer Info</th>
                        <th width="15%">Total Items</th>
                        <th width="15%">Total Amount</th>
                        <th width="15%">Status</th>
                        <th width="15%" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let table = $('#order-requests-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('admin.order-requests.index') }}',
                data: function (d) {
                    d.request_number = $('#filterRequestNumber').val();
                    d.start_date   = $('#filterStartDate').val();
                    d.end_date     = $('#filterEndDate').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                { data: 'request_info', name: 'id' },
                { data: 'dealer_info', name: 'dealer.name' },
                { data: 'total_items', name: 'total_items', searchable: false },
                { data: 'total_amount', name: 'total_amount', searchable: false, orderable: false },
                { data: 'status_badge', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            drawCallback: function() {
                if(typeof feather !== 'undefined') {
                    feather.replace();
                }
            }
        });

        $('#filterBtn').click(function() { table.draw(); });
        $('#resetBtn').click(function() {
            $('#filterRequestNumber, #filterStartDate, #filterEndDate').val('');
            table.draw();
        });
    });
</script>
@endpush
