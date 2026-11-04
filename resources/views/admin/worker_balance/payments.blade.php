@extends('layouts.admin')
@section('title', $page_title ?? 'Worker Payment List')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    <i data-feather="credit-card" class="mr-50 text-primary"></i> Worker Payment List
                </h4>
                <div class="d-flex" style="gap:0.4rem;">
                    @include('admin.partials.show_deleted_button', ['route' => route('admin.worker-payouts.trash')])
                    <a href="{{ route('admin.worker-balance-sheet.index') }}" class="btn btn-outline-primary btn-sm">
                        <i data-feather="users" class="mr-25"></i> Worker Balance Sheet
                    </a>
                </div>
            </div>
            <div class="card-body mt-2">
                <div class="row mb-2">
                    <div class="col-md-3">
                        <label>Date Range</label>
                        <input type="text" id="daterange" class="form-control" placeholder="Select Date Range">
                    </div>
                    <div class="col-md-3">
                        <label>Worker</label>
                        <select id="filter_worker" class="form-control select2">
                            <option value="">All Workers</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }} ({{ $worker->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Payment Method</label>
                        <select id="filter_method" class="form-control">
                            <option value="">All Methods</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                            <option value="bKash">bKash</option>
                            <option value="Nagad">Nagad</option>
                            <option value="Rocket">Rocket</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <div class="btn-group w-100">
                            <button type="button" id="btn-filter" class="btn btn-primary" title="Search"><i data-feather="search"></i> Search</button>
                            <button type="button" id="btn-reset" class="btn btn-danger" title="Reset Filters"><i data-feather="refresh-ccw"></i></button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered yajra-datatable w-100">
                        <thead>
                            <tr>
                                <th style="width: 40px;">SL</th>
                                <th>Token / Cheque No</th>
                                <th>Date</th>
                                <th>Worker</th>
                                <th class="text-right">Total Balance</th>
                                <th class="text-right">Paid Amount</th>
                                <th class="text-right">Remaining Balance</th>
                                <th>Payment Method</th>
                                <th>Created By</th>
                                <th class="text-center" style="width: 140px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
$(document).ready(function () {
    if ($('.select2').length > 0) {
        $('.select2').select2();
    }

    var from_date = '';
    var to_date = '';

    $('#daterange').daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear' }
    });

    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        from_date = picker.startDate.format('YYYY-MM-DD');
        to_date = picker.endDate.format('YYYY-MM-DD');
        table.draw();
    });

    $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
        from_date = '';
        to_date = '';
        table.draw();
    });

    var table = $('.yajra-datatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.worker-payouts.index') }}",
            data: function (d) {
                d.from_date = from_date;
                d.to_date = to_date;
                d.worker_id = $('#filter_worker').val();
                d.payment_method = $('#filter_method').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'token_no', name: 'payment_number'},
            {data: 'date', name: 'payment_date'},
            {data: 'worker_info', name: 'worker.name'},
            {data: 'total_balance', name: 'total_balance', className: 'text-right'},
            {data: 'amount', name: 'amount', className: 'text-right'},
            {data: 'remaining_balance', name: 'remaining_balance', className: 'text-right'},
            {data: 'method', name: 'payment_method'},
            {data: 'created_by', name: 'creator.name'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
        ],
        drawCallback: function (settings) {
            if (typeof feather !== 'undefined') {
                feather.replace({ width: 14, height: 14 });
            }
        }
    });

    $('#btn-filter').click(function () {
        table.draw();
    });

    $('#filter_worker, #filter_method').on('change', function () {
        table.draw();
    });

    $('#btn-reset').click(function () {
        $('#daterange').val('');
        from_date = '';
        to_date = '';
        $('#filter_worker').val('').trigger('change.select2');
        $('#filter_method').val('');
        table.draw();
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Delete Payment Record
    $(document).on('click', '.delete-payout', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Delete Payment History?',
            text: "This payment record will be permanently deleted and the paid amount will be restored back to the worker's balance!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Delete and Restore Balance'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('admin/worker-payments') }}/" + id,
                    type: 'DELETE',
                    success: function (res) {
                        if (res.success) {
                            toastr.success(res.message);
                            table.draw(false);
                        } else {
                            toastr.error(res.message || 'Failed to delete payment history.');
                        }
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON.message || 'Error deleting payment record.');
                    }
                });
            }
        });
    });
});
</script>
@endpush
