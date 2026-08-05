@extends('layouts.admin')
@section('title', isset($page_title) ? $page_title : 'Worker Payments')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">{{ isset($page_title) ? $page_title : 'Worker Payments' }}</h4>
            </div>
            <div class="card-body mt-2">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label>Date Range</label>
                        <input type="text" id="daterange" class="form-control" placeholder="Select Date Range">
                    </div>
                    <div class="col-md-3">
                        <label>Worker</label>
                        <select id="filter_worker" class="form-control select2">
                            <option value="">All Workers</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }} ==> {{ $worker->phone }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Status</label>
                        <select id="filter_status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <div class="btn-group w-100">
                            <button type="button" id="btn-filter" class="btn btn-primary" title="Search"><i data-feather="search"></i> Search</button>
                            <button type="button" id="btn-reset" class="btn btn-danger" title="Reset Filters"><i data-feather="refresh-ccw"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered yajra-datatable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Date</th>
                                <th>Manufacture No</th>
                                <th>Product</th>
                                <th>Worker</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Created By</th>
                                <th>Actions</th>
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
    $(function () {
        if ($('.select2').length > 0) {
            $('.select2').select2();
        }

        let from_date = '';
        let to_date = '';

        $('#daterange').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            }
        });

        $('#daterange').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            from_date = picker.startDate.format('YYYY-MM-DD');
            to_date = picker.endDate.format('YYYY-MM-DD');
        });

        $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            from_date = '';
            to_date = '';
        });

        var table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.worker-payment.index') }}",
                data: function (d) {
                    d.from_date = from_date;
                    d.to_date = to_date;
                    d.worker_id = $('#filter_worker').val();
                    d.status = $('#filter_status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'date', name: 'date'},
                {data: 'invoice_no', name: 'manufacture.invoice_no'},
                {data: 'product', name: 'manufacture.product.name'},
                {data: 'worker_name', name: 'worker.name'},
                {data: 'total_amount', name: 'total_amount'},
                {data: 'status', name: 'status'},
                {data: 'created_by', name: 'creator.name'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function (settings) {
                if (typeof feather !== 'undefined') {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        $('#btn-filter').click(function(){
            table.draw();
        });

        $('#filter_worker, #filter_status').on('change', function() {
            table.draw();
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

        $('#btn-reset').click(function(){
            $('#daterange').val('');
            from_date = '';
            to_date = '';
            $('#filter_worker').val('').trigger('change.select2');
            $('#filter_status').val('');
            table.draw();
        });

        $(document).on('change', '.change-status', function() {
            let id = $(this).data('id');
            let field = $(this).data('field');
            let value = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('admin.worker-payment.change-status') }}",
                type: 'POST',
                data: {
                    id: id,
                    field: field,
                    value: value,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.success);
                    table.draw(false);
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON.error || 'Something went wrong.');
                    table.draw(false);
                }
            });
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('click', '.delete-record', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/worker-payment') }}/" + id,
                        type: 'DELETE',
                        success: function(response) {
                            toastr.success(response.success);
                            table.draw();
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.error || 'Something went wrong.');
                        }
                    });
                }
            })
        });
    });
</script>
@endpush
