@extends('layouts.admin')
@section('title', 'Return Orders - Central Return Management')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css') }}">
<style>
    .kpi-stat-card {
        background-color: #FDFAF5;
        border: 1px solid #efeae2;
        border-radius: 10px;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .kpi-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(17, 28, 56, 0.06);
    }
    .filter-card {
        background-color: #FDFAF5;
        border: 1px solid #efeae2;
        border-radius: 10px;
    }
    .returns-table thead th {
        background-color: #111C38 !important;
        color: #ffffff !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px;
        border: none !important;
        padding: 12px 10px !important;
        vertical-align: middle !important;
    }
    .returns-table tbody td {
        font-size: 13px !important;
        vertical-align: middle !important;
        padding: 10px !important;
        border-bottom: 1px solid #f1f5f9;
    }
    .returns-table tbody tr:hover {
        background-color: #fafbfc !important;
    }
</style>
@endpush

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Return Orders</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Return Orders</li>
    </ol>
</div>
@endsection

@section('content')
<div class="content-body">
    <!-- Header Action Bar -->
    <div class="card mb-2 shadow-none" style="background-color: #FDFAF5; border: 1px solid #efeae2; border-radius: 10px;">
        <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <span class="fieldora-title-badge me-2" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; background-color: #111C38; color: #ffffff; border-radius: 8px; margin-right: 12px;">
                    <i data-feather="rotate-ccw" style="width: 20px; height: 20px; stroke: #ffffff;"></i>
                </span>
                <div>
                    <h4 class="mb-0 font-weight-bolder" style="color: #111C38;">Return Orders &amp; Refunds</h4>
                    <small class="text-muted">Multi-invoice product returns, cash refunds, and dealer wallet auto-credits</small>
                </div>
            </div>
            <div class="mt-1 mt-md-0 d-flex align-items-center" style="gap: 8px;">
                <a href="{{ route('admin.returns.export-list-pdf') }}" id="exportReturnsPdf" class="btn btn-sm d-inline-flex align-items-center" style="background-color: #b91c1c; color: #ffffff; height: 36px; border-radius: 6px; font-weight: 600; padding: 0 14px;">
                    <i data-feather="download" style="width: 15px; height: 15px; margin-right: 6px;"></i> Download PDF
                </a>
                @can('return_order.create')
                        <a href="{{ route('admin.returns.create') }}" class="btn btn-sm d-inline-flex align-items-center" style="background-color: #111C38; color: #ffffff; height: 36px; border-radius: 6px; font-weight: 600; padding: 0 16px;">
                    <i data-feather="plus-circle" style="width: 15px; height: 15px; margin-right: 6px;"></i> Create Return
                </a>
                        @endcan
            </div>
        </div>
    </div>

    <!-- Summary KPI Metrics -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 mb-1">
            <div class="card kpi-stat-card shadow-none mb-0">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-weight-bold font-small-2 text-uppercase">Total Returns</span>
                        <h3 class="font-weight-bolder mb-0 mt-25" style="color: #111C38;">{{ number_format($totalReturnsCount) }}</h3>
                        <small class="text-muted font-small-1">Settled: ৳{{ number_format($totalRefundSum, 2) }}</small>
                    </div>
                    <div style="width: 42px; height: 42px; background: rgba(17, 28, 56, 0.08); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="rotate-ccw" style="color: #111C38; width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-1">
            <div class="card kpi-stat-card shadow-none mb-0">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-weight-bold font-small-2 text-uppercase">Pending Review</span>
                        <h3 class="font-weight-bolder mb-0 mt-25 text-warning">{{ number_format($pendingCount) }}</h3>
                        <small class="text-warning font-small-1 font-weight-bold">Amount: ৳{{ number_format($pendingAmount, 2) }}</small>
                    </div>
                    <div style="width: 42px; height: 42px; background: rgba(255, 159, 67, 0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="clock" style="color: #ff9f43; width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-1">
            <div class="card kpi-stat-card shadow-none mb-0">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-weight-bold font-small-2 text-uppercase">Approved &amp; Restocked</span>
                        <h3 class="font-weight-bolder mb-0 mt-25 text-success">৳{{ number_format($totalRefundSum, 2) }}</h3>
                        <small class="text-muted font-small-1">Inventory synchronized</small>
                    </div>
                    <div style="width: 42px; height: 42px; background: rgba(40, 199, 111, 0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="check-circle" style="color: #28c76f; width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-1">
            <div class="card kpi-stat-card shadow-none mb-0">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-weight-bold font-small-2 text-uppercase">Wallet Credits</span>
                        <h3 class="font-weight-bolder mb-0 mt-25" style="color: #512DA8;">৳{{ number_format($walletCreditSum, 2) }}</h3>
                        <small class="text-muted font-small-1">Credited to dealer wallets</small>
                    </div>
                    <div style="width: 42px; height: 42px; background: rgba(115, 103, 240, 0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="credit-card" style="color: #7367f0; width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card filter-card mb-2 shadow-none">
        <div class="card-body p-2">
            <form id="returnsFilterForm" class="row gx-2 gy-1 align-items-end">
                <div class="col-md-2 col-sm-6">
                    <label class="form-label font-weight-bold font-small-2 text-dark mb-25">Channel:</label>
                    <select name="channel" id="filterChannel" class="form-select form-control" style="height: 36px; border-radius: 6px;">
                        <option value="all">All Channels</option>
                        <option value="dealer">Dealer Order</option>
                        <option value="pos">POS Sale</option>
                        <option value="web">Web Order</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label class="form-label font-weight-bold font-small-2 text-dark mb-25">Status:</label>
                    <select name="status" id="filterStatus" class="form-select form-control" style="height: 36px; border-radius: 6px;">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label class="form-label font-weight-bold font-small-2 text-dark mb-25">Refund Type:</label>
                    <select name="refund_type" id="filterRefundType" class="form-select form-control" style="height: 36px; border-radius: 6px;">
                        <option value="all">All Types</option>
                        <option value="wallet">Dealer Wallet</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>

                <div class="col-md-2 col-sm-6">
                    <label class="form-label font-weight-bold font-small-2 text-dark mb-25">From Date:</label>
                    <input type="date" name="from_date" id="filterFromDate" class="form-control" style="height: 36px; border-radius: 6px;" />
                </div>

                <div class="col-md-2 col-sm-6">
                    <label class="form-label font-weight-bold font-small-2 text-dark mb-25">To Date:</label>
                    <input type="date" name="to_date" id="filterToDate" class="form-control" style="height: 36px; border-radius: 6px;" />
                </div>

                <div class="col-md-2 col-sm-6 d-flex align-items-center" style="gap: 6px;">
                    <button type="submit" class="btn btn-sm btn-primary flex-grow-1" style="background-color: #111C38 !important; border-color: #111C38 !important; height: 36px; border-radius: 6px; font-weight: 600;">
                        <i data-feather="filter" style="width: 13px; height: 13px; margin-right: 3px;"></i> Filter
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnResetFilters" style="height: 36px; border-radius: 6px; font-weight: 600;">
                        <i data-feather="rotate-ccw" style="width: 13px; height: 13px;"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- DataTable Card -->
    <div class="card shadow-none" style="background-color: #FDFAF5; border: 1px solid #efeae2; border-radius: 10px;">
        <div class="card-header border-bottom py-1 px-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 font-weight-bolder" style="color: #111C38;">
                <i data-feather="list" class="me-1"></i> Return Orders List
            </h5>
            <small class="text-muted">Live server-side paginated list</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table returns-table w-100" id="returnsTable">
                    <thead>
                        <tr>
                            <th style="width: 45px;">#</th>
                            <th style="width: 140px;">RETURN NO</th>
                            <th style="width: 105px;">DATE</th>
                            <th>PARTY / CUSTOMER</th>
                            <th>INVOICES</th>
                            <th style="width: 90px;">QTY</th>
                            <th style="width: 110px;">REFUND</th>
                            <th style="width: 120px;">TYPE</th>
                            <th style="width: 120px;">STATUS</th>
                            <th>RETURN NOTE</th>
                            <th style="width: 175px;" class="text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Quick Approve Modal -->
<div class="modal fade" id="quickApproveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" id="quickApproveForm" class="modal-content" style="border-radius: 10px; border: 1px solid #e2e8f0;">
            @csrf
            <div class="modal-header py-1" style="background-color: #111C38; color: #ffffff; border-top-left-radius: 9px; border-top-right-radius: 9px; display: flex; align-items: center; justify-content: space-between;">
                <h5 class="modal-title text-white font-weight-bold mb-0">
                    <i data-feather="check-circle" style="width: 16px; height: 16px; margin-right: 6px;"></i> Approve Return Request
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 26px; line-height: 1; color: #ffffff; opacity: 0.9; cursor: pointer; padding: 0;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="alert alert-primary p-2 mb-2" style="background-color: #f0f4fa; border: 1px solid #d4e2f4; color: #111C38; border-radius: 8px;">
                    <div class="font-weight-bold font-small-3" id="approveModalTitle">Return #RET-0000</div>
                    <div class="font-small-2 mt-25">Refund Amount: <span class="font-weight-bolder text-success font-small-3" id="approveModalAmount">৳0.00</span></div>
                    <div class="font-small-2 mt-25" id="approveModalTypeInfo">Refund Method: Cash</div>
                </div>

                <div class="mb-2">
                    <label class="form-label font-weight-bold text-dark font-small-3 mb-25">Admin Approval Note (Optional):</label>
                    <textarea name="admin_review_note" class="form-control" rows="2" placeholder="e.g. Verified items returned to warehouse in good condition..."></textarea>
                </div>

                <p class="text-muted font-small-2 mb-0">
                    <i data-feather="info" style="width: 12px; height: 12px; margin-right: 3px;"></i>
                    Approving this return will update its status, synchronize inventory, and add funds to the dealer's wallet (if wallet refund).
                </p>
            </div>
            <div class="modal-footer py-1 border-top bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 6px; font-weight: 600;">Cancel</button>
                <button type="submit" class="btn btn-sm btn-success" style="background-color: #28c76f; border-color: #28c76f; border-radius: 6px; font-weight: 600;">
                    <i data-feather="check" style="width: 14px; height: 14px; margin-right: 4px;"></i> Confirm Approval
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendors/js/tables/datatable/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('admin/assets/vendors/js/tables/datatable/responsive.bootstrap5.js') }}"></script>

<script>
$(document).ready(function() {
    let table = $('#returnsTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        ajax: {
            url: "{{ route('admin.returns.index') }}",
            data: function(d) {
                d.channel = $('#filterChannel').val();
                d.status = $('#filterStatus').val();
                d.refund_type = $('#filterRefundType').val();
                d.from_date = $('#filterFromDate').val();
                d.to_date = $('#filterToDate').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, className: 'text-center' },
            { data: 'return_identity', name: 'return_no', orderable: true, searchable: true },
            { data: 'formatted_date', name: 'return_date', orderable: true, searchable: false },
            { data: 'customer_info', name: 'customer_name', orderable: true, searchable: true },
            { data: 'invoices_list', name: 'items.order_number', orderable: false, searchable: true },
            { data: 'total_qty_formatted', name: 'total_qty', orderable: true, searchable: false, className: 'text-center' },
            { data: 'refund_amount_formatted', name: 'refund_amount', orderable: true, searchable: false },
            { data: 'refund_type_badge', name: 'refund_type', orderable: true, searchable: true },
            { data: 'status_badge', name: 'status', orderable: true, searchable: true },
            { data: 'note_preview', name: 'return_note', orderable: false, searchable: true },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        dom: '<"d-flex justify-content-between align-items-center mx-1 my-1 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between align-items-center mx-1 mb-1 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
            search: "_INPUT_",
            searchPlaceholder: "Search return no, customer, invoice...",
            lengthMenu: "Show _MENU_ entries",
            emptyTable: "No return orders found",
            info: "Showing _START_ to _END_ of _TOTAL_ returns",
            paginate: {
                previous: '&nbsp;',
                next: '&nbsp;'
            }
        },
        drawCallback: function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
    });

    function syncReturnsPdfLink() {
        const params = $.param({
            channel: $('#filterChannel').val(),
            status: $('#filterStatus').val(),
            refund_type: $('#filterRefundType').val(),
            from_date: $('#filterFromDate').val(),
            to_date: $('#filterToDate').val()
        });
        $('#exportReturnsPdf').attr('href', "{{ route('admin.returns.export-list-pdf') }}?" + params);
    }

    syncReturnsPdfLink();

    // Filter Form Submit
    $('#returnsFilterForm').on('submit', function(e) {
        e.preventDefault();
        syncReturnsPdfLink();
        table.ajax.reload();
    });

    // Reset Filters
    $('#btnResetFilters').on('click', function() {
        $('#filterChannel').val('all');
        $('#filterStatus').val('all');
        $('#filterRefundType').val('all');
        $('#filterFromDate').val('');
        $('#filterToDate').val('');
        syncReturnsPdfLink();
        table.ajax.reload();
    });

    // Quick Approve Modal Handler
    $(document).on('click', '.btn-quick-approve', function() {
        let id = $(this).data('id');
        let no = $(this).data('no');
        let amount = $(this).data('amount');
        let type = $(this).data('type');

        $('#approveModalTitle').text('Return #' + no);
        $('#approveModalAmount').text('৳' + amount);
        $('#approveModalTypeInfo').text(type === 'wallet' ? 'Refund Method: Dealer Wallet Credit (Will be added immediately)' : 'Refund Method: Direct Cash Refund');
        $('#quickApproveForm').attr('action', "{{ url('admin/returns') }}/" + id + "/approve");

        $('#quickApproveModal').modal('show');
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

    // Ensure modal close buttons always work in BS4 / BS5
    $(document).on('click', '#quickApproveModal [data-dismiss="modal"], #quickApproveModal [data-bs-dismiss="modal"], #quickApproveModal .close', function(e) {
        e.preventDefault();
        $('#quickApproveModal').modal('hide');
    });
});
</script>
@endpush
