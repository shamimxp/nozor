@extends('layouts.admin')
@section('title', 'Dealer Orders')

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Dealer Orders</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Dealer Orders</li>
    </ol>
</div>
@endsection

@section('content-header-right')
@include('admin.partials.show_deleted_button', ['route' => route('admin.dealer-order.trash')])
<a href="{{ route('admin.dealer-order.create') }}" class="btn btn-primary shadow-sm font-weight-bold">
    <i data-feather="plus"></i> Create New Order
</a>
@endsection

@section('content')



<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="row align-items-end mb-3">
            <div class="col-md-3">
                <label class="small font-weight-bold">Order Number</label>
                <input type="text" id="filterOrderNumber" class="form-control form-control-sm" placeholder="Search by order #...">
            </div>
            <div class="col-md-2">
                <label class="small font-weight-bold">Start Date</label>
                <input type="date" id="filterStartDate" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="small font-weight-bold">End Date</label>
                <input type="date" id="filterEndDate" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="small font-weight-bold">Status</label>
                <select id="filterStatus" class="form-control form-control-sm">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirm">Confirm</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-md-3 text-right">
                <button type="button" id="filterBtn" class="btn btn-sm btn-info shadow-sm mr-1"><i data-feather="filter"></i> Filter</button>
                <button type="button" id="resetBtn" class="btn btn-sm btn-secondary shadow-sm mr-1"><i data-feather="refresh-ccw"></i></button>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-success dropdown-toggle shadow-sm" data-toggle="dropdown">
                        <i data-feather="download"></i> Export
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#" id="exportExcel"><i data-feather="file-text" class="mr-1"></i> Excel</a>
                        <a class="dropdown-item" href="#" id="exportPdf"><i data-feather="file" class="mr-1"></i> PDF</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="dealerOrderTable">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="15%">Order Info</th>
                        <th width="15%">Dealer</th>
                        <th width="15%">Financials</th>
                        <th width="15%">Status</th>
                        <th width="10%" class="text-center">Action</th>
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
        let table = $('#dealerOrderTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.dealer-order.index') }}",
                data: function (d) {
                    d.order_number = $('#filterOrderNumber').val();
                    d.start_date   = $('#filterStartDate').val();
                    d.end_date     = $('#filterEndDate').val();
                    d.status       = $('#filterStatus').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                { data: 'order_info', name: 'order_number' },
                { data: 'dealer_name', name: 'dealer.name' },
                { data: 'totals', name: 'grand_total' },
                { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            ],
            drawCallback: function() {
                feather.replace();
                $('.updateStatusSelect').select2({ minimumResultsForSearch: Infinity });
            }
        });

        $('#filterBtn').click(function() { table.draw(); });
        $('#resetBtn').click(function() {
            $('#filterOrderNumber, #filterStartDate, #filterEndDate, #filterStatus').val('');
            table.draw();
        });

        $(document).on('change', '.updateStatusSelect', function() {
            let orderId = $(this).data('order-id');
            let status = $(this).val();

            $.post("{{ route('admin.dealer-order.status') }}", {
                _token: '{{ csrf_token() }}',
                order_id: orderId,
                status: status
            }, function(res) {
                if(res.success) {
                    toastr.success(res.message);
                    table.draw();
                } else {
                    toastr.error(res.message);
                }
            }).fail(function(xhr) {
                toastr.error(xhr.responseJSON?.message || 'Error updating status');
                table.draw();
            });
        });

        $(document).on('click', '.deleteOrder', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Move to trash?',
                text: "You can restore this later from Show Deleted Data.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/admin/dealer-order/" + id,
                        type: "DELETE",
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(data) {
                            table.draw();
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message || data.success,
                                customClass: { confirmButton: 'btn btn-success' }
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Cannot delete',
                                text: xhr.responseJSON?.message || xhr.responseJSON?.error || 'Something went wrong!',
                                customClass: { confirmButton: 'btn btn-danger' }
                            });
                        }
                    });
                }
            });
        });

        $('#exportExcel').click(function(e) {
            e.preventDefault();
            let qs = $.param({
                order_number: $('#filterOrderNumber').val(),
                start_date: $('#filterStartDate').val(),
                end_date: $('#filterEndDate').val(),
                status: $('#filterStatus').val(),
            });
            window.location.href = "{{ route('admin.dealer-order.export-list-excel') }}?" + qs;
        });

        $('#exportPdf').click(function(e) {
            e.preventDefault();
            let qs = $.param({
                order_number: $('#filterOrderNumber').val(),
                start_date: $('#filterStartDate').val(),
                end_date: $('#filterEndDate').val(),
                status: $('#filterStatus').val(),
            });
            window.location.href = "{{ route('admin.dealer-order.export-list-pdf') }}?" + qs;
        });
    });
</script>
@endpush
