@extends('layouts.admin')
@section('title', 'POS Order List')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">POS Order List</h4>
                    <div>
                        <a href="{{ route('admin.pos-order.export-list-excel') }}" class="btn btn-success" id="exportExcelBtn"><i data-feather="file-text"></i> Excel</a>
                        <a href="{{ route('admin.pos-order.export-list-pdf') }}" class="btn btn-danger" id="exportPdfBtn"><i data-feather="file"></i> PDF</a>
                        @if(Route::has('admin.pos'))
                        <a href="{{ route('admin.pos') }}" target="_blank" class="btn btn-primary ml-1">Go to POS</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body border-bottom pt-2 pb-2">
                <form id="filterForm">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <label>Order Number</label>
                            <input type="text" name="order_number" id="order_number" class="form-control" placeholder="Search Order No">
                        </div>
                        <div class="col-md-3">
                            <label>Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" id="end_date" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label>Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex">
                            <button type="button" id="filterBtn" class="btn btn-primary w-50 mr-1"><i data-feather="filter"></i></button>
                            <button type="button" id="resetBtn" class="btn btn-outline-secondary w-50"><i data-feather="refresh-cw"></i></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="posOrderTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order Info</th>
                            <th>Customer</th>
                            <th>Payment Summary</th>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#posOrderTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.pos-order.index') }}",
                data: function(d) {
                    d.order_number = $('#order_number').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.status = $('#status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'order_info', name: 'order_number'},
                {data: 'customer_info', name: 'customer.name'},
                {data: 'payment_summary', name: 'total_amount'},
                {data: 'status', name: 'order_status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
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

        $('#filterBtn').on('click', function() {
            table.draw();
            updateExportLinks();
        });

        $('#resetBtn').on('click', function() {
            $('#order_number').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#status').val('');
            table.draw();
            updateExportLinks();
        });

        function updateExportLinks() {
            let orderNum = $('#order_number').val() || '';
            let start = $('#start_date').val() || '';
            let end = $('#end_date').val() || '';
            let stat = $('#status').val() || '';

            let queryParams = $.param({
                order_number: orderNum,
                start_date: start,
                end_date: end,
                status: stat
            });

            $('#exportExcelBtn').attr('href', "{{ route('admin.pos-order.export-list-excel') }}?" + queryParams);
            $('#exportPdfBtn').attr('href', "{{ route('admin.pos-order.export-list-pdf') }}?" + queryParams);
        }

        // Call once on load
        updateExportLinks();

        // Cancel Order
        $('body').on('click', '.cancelOrder', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Cancel Order?',
                text: "Stock will be restored!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Yes, cancel it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route('admin.pos-order.cancel', ':id') }}".replace(':id', id), {
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            table.draw();
                        } else {
                            toastr.error(res.message);
                        }
                    });
                }
            });
        });

        // Delete Order
        $('body').on('click', '.deleteOrder', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this POS order? (Items will be removed)",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/pos-order') }}"+'/'+id,
                        success: function (data) {
                            table.draw();
                            toastr.success(data.message);
                        },
                        error: function (data) {
                            toastr.error('Something went wrong!');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
