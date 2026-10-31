@extends('layouts.admin')
@section('title', 'Daily Activity Log - ' . $userObj->name)

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/vendors/css/tables/datatable/dataTables.bootstrap5.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/vendors/css/tables/datatable/responsive.bootstrap5.min.css') }}">
<style>
    .daily-log-header {
        background-color: #FDFAF5;
        border: 1px solid #efeae2;
        border-radius: 10px;
    }
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
    .action-filter-btn {
        border-radius: 20px;
        padding: 6px 16px;
        font-weight: 600;
        font-size: 12.5px;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
        display: inline-flex;
        align-items: center;
        margin-right: 8px;
        margin-bottom: 6px;
        border: 1px solid #d0deec;
        background-color: #ffffff;
        color: #111C38;
    }
    .action-filter-btn:hover {
        background-color: #f1f5f9;
        color: #111C38;
    }
    .action-filter-btn.active {
        background-color: #111C38 !important;
        color: #ffffff !important;
        border-color: #111C38 !important;
    }
    .daily-log-table thead th {
        background-color: #111C38 !important;
        color: #ffffff !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px;
        border: none !important;
        padding: 12px 14px !important;
        vertical-align: middle !important;
    }
    .daily-log-table tbody td {
        font-size: 13px !important;
        vertical-align: middle !important;
        padding: 10px 14px !important;
        border-bottom: 1px solid #f1f5f9;
    }
    .daily-log-table tbody tr:hover {
        background-color: #fafbfc !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
        background: #111C38 !important;
        border-color: #111C38 !important;
        color: #ffffff !important;
    }
    .diff-table th {
        background: #f8fafc;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
    }
    .diff-table td {
        font-size: 12px;
        font-family: monospace;
    }
</style>
@endpush

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Daily Activity Log</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.user-activities.index') }}">User Activities</a></li>
        <li class="breadcrumb-item active">{{ $userObj->name }}</li>
    </ol>
</div>
@endsection

@section('content')
<div class="content-body">
    <!-- User Profile Header Card -->
    <div class="card daily-log-header mb-2 shadow-none">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-7 col-12 d-flex align-items-center mb-1 mb-md-0">
                    <div style="width: 52px; height: 52px; border-radius: 50%; background-color: #111C38; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 20px; margin-right: 15px; flex-shrink: 0; box-shadow: 0 4px 10px rgba(17, 28, 56, 0.15);">
                        {{ strtoupper(substr($userObj->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-0 font-weight-bolder text-dark me-2" style="font-size: 18px; margin-right: 8px;">{{ $userObj->name }}</h4>
                            <span class="badge" style="background-color: #111C38; color: #ffffff; font-size: 11px; padding: 3px 8px; border-radius: 6px;">{{ $roleLabel }}</span>
                        </div>
                        <div class="text-muted font-small-3 mt-25">
                            <span><i data-feather="phone" style="width: 12px; height: 12px; margin-right: 3px;"></i> {{ $userObj->phone ?? '-' }}</span>
                            <span class="ms-2" style="margin-left: 12px;"><i data-feather="mail" style="width: 12px; height: 12px; margin-right: 3px;"></i> {{ $userObj->email ?? '-' }}</span>
                            <span class="ms-2" style="margin-left: 12px;"><i data-feather="hash" style="width: 12px; height: 12px; margin-right: 3px;"></i> ID: #{{ $userObj->id }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-5 col-12 text-md-right text-end">
                    <form method="GET" action="{{ route('admin.user-activities.daily-log', ['role' => $role, 'id' => $userObj->id]) }}" class="d-inline-flex align-items-center" id="dateFilterForm">
                        <label class="mb-0 font-weight-bold text-dark me-1" style="margin-right: 6px; font-size: 13px;"><i data-feather="calendar" style="width: 14px; height: 14px;"></i> Date:</label>
                        <input type="date" name="date" id="selectedDateInput" class="form-control me-1" value="{{ $selectedDate }}" style="width: 150px; height: 36px; border-radius: 6px; margin-right: 6px;" />
                        <button type="submit" class="btn btn-sm btn-primary" style="background-color: #111C38 !important; border-color: #111C38 !important; height: 36px; border-radius: 6px; font-weight: 600;">Go</button>
                    </form>
                    <a href="{{ route('admin.user-activities.index', ['date' => $selectedDate]) }}" class="btn btn-sm btn-secondary ms-1" style="background-color: #64748b; border-color: #64748b; height: 36px; border-radius: 6px; font-weight: 600; margin-left: 6px;">
                        <i data-feather="arrow-left" style="width: 13px; height: 13px; margin-right: 3px;"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Day Summary KPI Breakdown -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 mb-1">
            <div class="card kpi-stat-card shadow-none mb-0">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-weight-bold font-small-2 text-uppercase">Total Activities</span>
                        <h3 class="font-weight-bolder mb-0 mt-25" style="color: #111C38;">{{ $breakdown['total'] }}</h3>
                    </div>
                    <div style="width: 42px; height: 42px; background: rgba(17, 28, 56, 0.08); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="activity" style="color: #111C38; width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-1">
            <div class="card kpi-stat-card shadow-none mb-0">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-weight-bold font-small-2 text-uppercase">Database Mutations</span>
                        <h3 class="font-weight-bolder mb-0 mt-25 text-success">{{ $breakdown['mutations'] }}</h3>
                    </div>
                    <div style="width: 42px; height: 42px; background: rgba(40, 199, 111, 0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="database" style="color: #28c76f; width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-1">
            <div class="card kpi-stat-card shadow-none mb-0">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-weight-bold font-small-2 text-uppercase">Orders Placed/Modified</span>
                        <h3 class="font-weight-bolder mb-0 mt-25 text-warning">{{ $breakdown['orders'] }}</h3>
                    </div>
                    <div style="width: 42px; height: 42px; background: rgba(255, 159, 67, 0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="shopping-bag" style="color: #ff9f43; width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-1">
            <div class="card kpi-stat-card shadow-none mb-0">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-weight-bold font-small-2 text-uppercase">Payments &amp; Collections</span>
                        <h3 class="font-weight-bolder mb-0 mt-25 text-primary">{{ $breakdown['payments'] }}</h3>
                    </div>
                    <div style="width: 42px; height: 42px; background: rgba(115, 103, 240, 0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="dollar-sign" style="color: #7367f0; width: 20px; height: 20px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Pills Section -->
    <div class="card mb-2 shadow-none" style="background-color: #FDFAF5; border: 1px solid #efeae2; border-radius: 10px;">
        <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex flex-wrap align-items-center">
                <span class="text-muted font-weight-bold font-small-2 me-2" style="margin-right: 12px; text-transform: uppercase;">FILTER TYPE:</span>
                
                <button type="button" class="action-filter-btn active" data-filter="all">
                    <i data-feather="list" style="width: 13px; height: 13px; margin-right: 4px;"></i> All ({{ $breakdown['total'] }})
                </button>

                <button type="button" class="action-filter-btn" data-filter="mutations">
                    <i data-feather="database" style="width: 13px; height: 13px; margin-right: 4px;"></i> DB Changes ({{ $breakdown['mutations'] }})
                </button>

                <button type="button" class="action-filter-btn" data-filter="order">
                    <i data-feather="shopping-bag" style="width: 13px; height: 13px; margin-right: 4px;"></i> Orders ({{ $breakdown['orders'] }})
                </button>

                <button type="button" class="action-filter-btn" data-filter="product">
                    <i data-feather="box" style="width: 13px; height: 13px; margin-right: 4px;"></i> Product &amp; Stock
                </button>

                <button type="button" class="action-filter-btn" data-filter="payment">
                    <i data-feather="dollar-sign" style="width: 13px; height: 13px; margin-right: 4px;"></i> Payments ({{ $breakdown['payments'] }})
                </button>

                <button type="button" class="action-filter-btn" data-filter="auth">
                    <i data-feather="lock" style="width: 13px; height: 13px; margin-right: 4px;"></i> Login / Logout ({{ $breakdown['auth'] }})
                </button>
            </div>

            <div class="mt-1 mt-md-0">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnReloadTable" style="border-radius: 6px; font-weight: 600;">
                    <i data-feather="refresh-cw" style="width: 12px; height: 12px; margin-right: 4px;"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- DataTable Card -->
    <div class="card shadow-none" style="background-color: #FDFAF5; border: 1px solid #efeae2; border-radius: 10px;">
        <div class="card-header border-bottom py-1 px-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 font-weight-bolder" style="color: #111C38;">
                <i data-feather="clock" class="me-1"></i> Chronological Activity DataTable ({{ $selectedDate }})
            </h5>
            <small class="text-muted">Live server-side paginated activity history</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table daily-log-table w-100" id="dailyActivityTable">
                    <thead>
                        <tr>
                            <th style="width: 45px;">#</th>
                            <th style="width: 130px;">TIME</th>
                            <th style="width: 110px;">TYPE</th>
                            <th>ACTIVITY / EVENT</th>
                            <th style="width: 150px;">SUBJECT / REF</th>
                            <th style="width: 120px;">IP / METHOD</th>
                            <th style="width: 100px;" class="text-center">CHANGES</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal to View Changed JSON Properties / Diff -->
<div class="modal fade" id="activityDiffModal" tabindex="-1" aria-labelledby="activityDiffModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 10px; border: 1px solid #e2e8f0;">
            <div class="modal-header py-1" style="background-color: #111C38; color: #ffffff; border-top-left-radius: 9px; border-top-right-radius: 9px; display: flex; align-items: center; justify-content: space-between;">
                <h5 class="modal-title text-white font-weight-bold mb-0" id="activityDiffModalLabel">
                    <i data-feather="eye" style="width: 16px; height: 16px; margin-right: 6px;"></i> Activity Payload &amp; Changes
                </h5>
                <button type="button" class="close text-white modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 26px; line-height: 1; color: #ffffff; opacity: 0.9; cursor: pointer; padding: 0; margin: 0;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-2 d-flex align-items-center justify-content-between pb-1 border-bottom">
                    <div>
                        <span class="text-muted font-small-2 text-uppercase">Target Entity:</span>
                        <h6 class="mb-0 font-weight-bold" id="modalSubjectRef">-</h6>
                    </div>
                    <div>
                        <span class="text-muted font-small-2 text-uppercase">Event:</span>
                        <span class="badge bg-primary text-white" id="modalEventBadge">-</span>
                    </div>
                </div>

                <!-- Structured Diff Container -->
                <div id="modalDiffContainer">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>
            <div class="modal-footer py-1 border-top bg-light">
                <button type="button" class="btn btn-sm btn-secondary modal-close-btn" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 6px; font-weight: 600; padding: 6px 14px;">
                    <i data-feather="x" style="width: 14px; height: 14px; margin-right: 4px;"></i> Close
                </button>
            </div>
        </div>
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
    let currentActionType = 'all';

    // Initialize Server-side Yajra DataTable
    let table = $('#dailyActivityTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        ajax: {
            url: "{{ route('admin.user-activities.daily-log', ['role' => $role, 'id' => $userObj->id]) }}",
            type: 'GET',
            data: function(d) {
                d.date = $('#selectedDateInput').val();
                d.action_type = currentActionType;
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, className: 'text-center' },
            { data: 'formatted_time', name: 'id', orderable: true, searchable: false },
            { data: 'action_badge', name: 'action_type', orderable: true, searchable: true },
            { data: 'event_description', name: 'description', orderable: false, searchable: true },
            { data: 'subject_info', name: 'subject_ref', orderable: false, searchable: true },
            { data: 'ip_method', name: 'ip_address', orderable: false, searchable: true },
            { data: 'details_btn', name: 'details_btn', orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[1, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        dom: '<"d-flex justify-content-between align-items-center mx-1 my-1 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between align-items-center mx-1 mb-1 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
        language: {
            processing: '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>',
            search: "_INPUT_",
            searchPlaceholder: "Search activity, route, subject...",
            lengthMenu: "Show _MENU_ entries",
            emptyTable: "No activities recorded for this date and filter criteria",
            info: "Showing _START_ to _END_ of _TOTAL_ activities",
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

    // Action filter pill clicks
    $('.action-filter-btn').on('click', function() {
        $('.action-filter-btn').removeClass('active');
        $(this).addClass('active');
        currentActionType = $(this).data('filter');
        table.ajax.reload();
    });

    // Manual Refresh button
    $('#btnReloadTable').on('click', function() {
        table.ajax.reload(null, false);
    });

    // View Diff Modal Handler
    $(document).on('click', '.btn-view-diff', function() {
        let btn = $(this);
        let event = btn.data('event') || 'Event';
        let subject = btn.data('subject') || 'Entity';
        let payload = btn.data('payload');

        $('#modalSubjectRef').text(subject);
        $('#modalEventBadge').text(event.toUpperCase());

        let container = $('#modalDiffContainer');
        container.empty();

        if (!payload || typeof payload !== 'object') {
            container.html('<div class="alert alert-secondary p-2 mb-0">No diff attributes recorded for this event.</div>');
            $('#activityDiffModal').modal('show');
            return;
        }

        // Case 1: Old vs New updates
        if (payload.old || payload.new) {
            let oldVals = payload.old || {};
            let newVals = payload.new || {};
            let allKeys = Array.from(new Set([...Object.keys(oldVals), ...Object.keys(newVals)]));

            let html = '<div class="table-responsive"><table class="table table-bordered table-sm diff-table mb-0">';
            html += '<thead><tr><th style="width: 25%;">Field</th><th style="width: 37.5%;">Previous (Old)</th><th style="width: 37.5%;">Updated (New)</th></tr></thead><tbody>';

            allKeys.forEach(function(key) {
                let oldVal = oldVals[key] !== undefined ? JSON.stringify(oldVals[key]) : '<span class="text-muted font-italic">undefined</span>';
                let newVal = newVals[key] !== undefined ? JSON.stringify(newVals[key]) : '<span class="text-muted font-italic">undefined</span>';
                html += '<tr>';
                html += '<td class="font-weight-bold text-dark">' + key + '</td>';
                html += '<td class="bg-light-danger text-danger">' + oldVal + '</td>';
                html += '<td class="bg-light-success text-success font-weight-bold">' + newVal + '</td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';
            container.html(html);
        } else if (payload.attributes) {
            // Case 2: Created or Deleted attributes snapshot
            let html = '<div class="table-responsive"><table class="table table-bordered table-sm diff-table mb-0">';
            html += '<thead><tr><th style="width: 30%;">Attribute</th><th>Value</th></tr></thead><tbody>';

            Object.entries(payload.attributes).forEach(function([k, v]) {
                let displayVal = typeof v === 'object' ? JSON.stringify(v) : (v === null ? '<span class="text-muted font-italic">null</span>' : v);
                html += '<tr>';
                html += '<td class="font-weight-bold text-dark">' + k + '</td>';
                html += '<td>' + displayVal + '</td>';
                html += '</tr>';
            });

            html += '</tbody></table></div>';
            container.html(html);
        } else {
            // General JSON dump
            let jsonString = JSON.stringify(payload, null, 2);
            container.html('<pre class="p-2 bg-light text-dark mb-0 font-small-2" style="border-radius: 6px; max-height: 350px; overflow-y: auto;"><code>' + jsonString + '</code></pre>');
        }

        $('#activityDiffModal').modal('show');
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });

    // Explicit close handler to guarantee close button and X always work seamlessly
    $(document).on('click', '#activityDiffModal .modal-close-btn, #activityDiffModal [data-dismiss="modal"], #activityDiffModal [data-bs-dismiss="modal"]', function(e) {
        e.preventDefault();
        $('#activityDiffModal').modal('hide');
    });
});
</script>
@endpush
