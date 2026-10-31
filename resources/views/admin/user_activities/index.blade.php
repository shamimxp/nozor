@extends('layouts.admin')
@section('title', 'User Full Activity Log & Last Action Status')

@push('styles')
<style>
    .kpi-stat-card {
        background-color: #FDFAF5;
        border: 1px solid #efeae2;
        border-radius: 10px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(17, 28, 56, 0.08);
    }
    #userActivityTable tbody tr {
        background-color: #ffffff;
        transition: background-color 0.15s ease-in-out;
    }
    #userActivityTable tbody tr:hover {
        background-color: #f8f6f0 !important;
    }
    #userActivityTable_wrapper .page-item.active .page-link {
        background-color: #111C38 !important;
        border-color: #111C38 !important;
        color: #ffffff !important;
        font-weight: 700;
        border-radius: 4px;
    }
    #userActivityTable_wrapper .page-link {
        color: #111C38;
        font-weight: 600;
        border-radius: 4px;
        margin: 0 2px;
    }
    #userActivityTable_wrapper .dataTables_length select {
        height: 34px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        padding: 4px 8px;
    }
    #userActivityTable_wrapper .dataTables_info {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
    }
</style>
@endpush

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">User Activities</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">User Full Activity & Inactivity Status</li>
    </ol>
</div>
@endsection

@section('content')
<div class="content-body">
    <!-- Top FieldOra Header Banner -->
    <div class="card mb-2" style="background-color: #FDFAF5; border: 1px solid #efeae2; border-radius: 10px;">
        <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center flex-wrap">
            <div class="d-flex align-items-center">
                <span class="fieldora-title-badge me-2" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; background-color: #111C38; color: #ffffff; border-radius: 8px; margin-right: 12px;">
                    <i data-feather="users" style="width: 20px; height: 20px; stroke: #ffffff;"></i>
                </span>
                <div>
                    <h4 class="mb-0 font-weight-bolder" style="color: #111C38;">User Full Activity Log & Last Action Status</h4>
                    <small class="text-muted">Real-time status monitor tracking database operations, idle times, and day-by-day staff activity</small>
                </div>
            </div>

        </div>
    </div>

    <!-- KPI Summary Metrics -->
    <div class="row">
        <div class="col-xl-3 col-sm-6 col-12 mb-1">
            <div class="card kpi-stat-card shadow-none mb-0">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-weight-bold" style="font-size: 0.8rem; text-transform: uppercase;">Active (&lt; 30 mins)</span>
                        <h3 class="font-weight-bolder mb-0 mt-25 text-success">{{ number_format($activeCount) }}</h3>
                    </div>
                    <div style="width: 44px; height: 44px; background: rgba(40, 199, 111, 0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="check-circle" style="color: #28c76f; width: 22px; height: 22px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-1">
            <div class="card kpi-stat-card shadow-none mb-0">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-weight-bold" style="font-size: 0.8rem; text-transform: uppercase;">Idle (30 - 59 mins)</span>
                        <h3 class="font-weight-bolder mb-0 mt-25 text-warning">{{ number_format($idleCount) }}</h3>
                    </div>
                    <div style="width: 44px; height: 44px; background: rgba(255, 159, 67, 0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="clock" style="color: #ff9f43; width: 22px; height: 22px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-1">
            <div class="card kpi-stat-card shadow-none mb-0">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-weight-bold" style="font-size: 0.8rem; text-transform: uppercase;">Inactive Alert (&ge; 60m)</span>
                        <h3 class="font-weight-bolder mb-0 mt-25 text-danger">{{ number_format($alertCount) }}</h3>
                    </div>
                    <div style="width: 44px; height: 44px; background: rgba(234, 84, 85, 0.12); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="alert-triangle" style="color: #ea5455; width: 22px; height: 22px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 col-12 mb-1">
            <div class="card kpi-stat-card shadow-none mb-0">
                <div class="card-body p-2 d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted font-weight-bold" style="font-size: 0.8rem; text-transform: uppercase;">Activities Today</span>
                        <h3 class="font-weight-bolder mb-0 mt-25" style="color: #111C38;">{{ number_format($totalActivitiesCount) }}</h3>
                    </div>
                    <div style="width: 44px; height: 44px; background: rgba(17, 28, 56, 0.08); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                        <i data-feather="activity" style="color: #111C38; width: 22px; height: 22px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card mb-2" style="background-color: #FDFAF5; border: 1px solid #efeae2; border-radius: 10px;">
        <div class="card-body p-2">
            <form id="filterForm" onsubmit="return false;">
                <div class="row align-items-end">
                    <div class="col-lg-3 col-md-6 col-12 mb-1 mb-lg-0">
                        <label class="form-label font-weight-bold text-dark font-small-2 mb-25">Activity Date</label>
                        <input type="date" id="filterDate" class="form-control" value="{{ $selectedDate }}" style="height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #ffffff;">
                    </div>

                    <div class="col-lg-3 col-md-6 col-12 mb-1 mb-lg-0">
                        <label class="form-label font-weight-bold text-dark font-small-2 mb-25">Inactivity Status</label>
                        <select id="filterStatus" class="form-control" style="height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #ffffff;">
                            <option value="all">All Statuses</option>
                            <option value="active">Active (&lt; 30 mins)</option>
                            <option value="idle">Idle (30 - 59 mins)</option>
                            <option value="alert">Inactive Alert (&ge; 60 mins)</option>
                            <option value="no_activity">No Activity Today</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 col-12 mb-1 mb-lg-0">
                        <label class="form-label font-weight-bold text-dark font-small-2 mb-25">Staff Role</label>
                        <select id="filterRole" class="form-control" style="height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #ffffff;">
                            @foreach($rolesList as $key => $lbl)
                                <option value="{{ $key }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 col-12 mb-1 mb-lg-0">
                        <label class="form-label font-weight-bold text-dark font-small-2 mb-25">Search Keyword</label>
                        <input type="text" id="filterSearch" class="form-control" placeholder="Search name, phone..." style="height: 38px; border: 1px solid #cbd5e1; border-radius: 6px; background-color: #ffffff;">
                    </div>

                    <div class="col-lg-2 col-12 d-flex justify-content-end align-items-end" style="gap: 8px;">
                        <button type="button" id="filterBtn" class="btn btn-primary d-inline-flex align-items-center justify-content-center shadow-none w-100" style="height: 38px; background-color: #111C38 !important; border-color: #111C38 !important; border-radius: 6px; font-weight: 600; font-size: 13px;" title="Apply Filter">
                            <i data-feather="filter" style="width: 14px; height: 14px; margin-right: 4px;"></i> Filter
                        </button>
                        <button type="button" id="resetBtn" class="btn btn-secondary d-inline-flex align-items-center justify-content-center shadow-none" style="height: 38px; min-width: 42px; background-color: #64748b !important; border-color: #64748b !important; border-radius: 6px; font-weight: 600; font-size: 13px;" title="Reset Filters">
                            <i data-feather="rotate-ccw" style="width: 14px; height: 14px;"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- User Activities DataTable Card -->
    <div class="card shadow-none" style="background-color: #FDFAF5; border: 1px solid #efeae2; border-radius: 10px;">
        <div class="card-header border-bottom py-1 px-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 font-weight-bolder" style="color: #111C38;">
                <i data-feather="activity" class="me-1"></i> User Full Activity Log &amp; Last Action Status
            </h5>
            <div>
                <span class="badge badge-light-danger font-weight-bold px-2 py-1" style="font-size: 11px;">
                    <i data-feather="alert-triangle" style="width: 12px; height: 12px; margin-right: 3px;"></i> &ge; 60m Inactivity = Alert State
                </span>
            </div>
        </div>
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 w-100" id="userActivityTable" style="background-color: #FDFAF5;">
                    <thead style="background-color: #111C38; color: #ffffff;">
                        <tr>
                            <th class="py-2 px-2 text-center" style="width: 4%; background-color: #111C38; color: #ffffff !important; font-weight: 700; font-size: 12px; letter-spacing: 0.5px; border-top-left-radius: 8px;">#</th>
                            <th class="py-2 px-3 text-left" style="width: 20%; background-color: #111C38; color: #ffffff !important; font-weight: 700; font-size: 12px; letter-spacing: 0.5px;">USER & ACTOR</th>
                            <th class="py-2 text-center" style="width: 12%; background-color: #111C38; color: #ffffff !important; font-weight: 700; font-size: 12px; letter-spacing: 0.5px;">ROLE / ACCOUNT</th>
                            <th class="py-2 text-center" style="width: 10%; background-color: #111C38; color: #ffffff !important; font-weight: 700; font-size: 12px; letter-spacing: 0.5px;">ACTIVITIES</th>
                            <th class="py-2 text-left" style="width: 28%; background-color: #111C38; color: #ffffff !important; font-weight: 700; font-size: 12px; letter-spacing: 0.5px;">LAST ACTIONABLE ACTIVITY</th>
                            <th class="py-2 text-center" style="width: 14%; background-color: #111C38; color: #ffffff !important; font-weight: 700; font-size: 12px; letter-spacing: 0.5px;">CURRENT STATUS</th>
                            <th class="py-2 text-center" style="width: 12%; background-color: #111C38; color: #ffffff !important; font-weight: 700; font-size: 12px; letter-spacing: 0.5px; border-top-right-radius: 8px;">ACTIONS</th>
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
$(document).ready(function() {
    let table = $('#userActivityTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.user-activities.index') }}",
            data: function (d) {
                d.date            = $('#filterDate').val();
                d.status          = $('#filterStatus').val();
                d.role            = $('#filterRole').val();
                d.search_keyword  = $('#filterSearch').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false, className: 'text-center' },
            { data: 'user_info', name: 'name' },
            { data: 'role_badge', name: 'role', className: 'text-center' },
            { data: 'total_activities', name: 'total_activities', className: 'text-center' },
            { data: 'last_activity', name: 'last_activity' },
            { data: 'status', name: 'status', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
        ],
        pageLength: 25,
        searching: false,
        dom: '<"d-flex justify-content-between align-items-center mx-0 py-1 row"<"col-sm-12 col-md-6"l>>t<"d-flex justify-content-between align-items-center mx-0 py-1 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6 d-flex justify-content-end"p>>',
        drawCallback: function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
    });

    $('#filterBtn').on('click', function(e) {
        e.preventDefault();
        table.draw();
    });

    $('#resetBtn').on('click', function(e) {
        e.preventDefault();
        $('#filterDate').val('{{ $selectedDate }}');
        $('#filterStatus').val('all');
        $('#filterRole').val('all');
        $('#filterSearch').val('');
        table.draw();
    });

    $('#filterSearch').on('keyup', function(e) {
        if (e.key === 'Enter') {
            table.draw();
        }
    });

    $('#filterDate, #filterStatus, #filterRole').on('change', function() {
        table.draw();
    });
});
</script>
@endpush
