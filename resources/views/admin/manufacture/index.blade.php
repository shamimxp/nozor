@extends('layouts.admin')
@section('title', 'Manufacture List')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Manufacture Order List</h4>
                <a href="{{ route('admin.manufacture.create') }}" class="btn btn-primary">Create New</a>
            </div>
            <div class="card-body mt-2">
                <div class="row mb-2">
                    <div class="col-md-3">
                        <label>Date Range</label>
                        <input type="text" id="daterange" class="form-control" placeholder="Select Date Range">
                    </div>
                    <div class="col-md-2">
                        <label>Worker</label>
                        <select id="filter_worker" class="form-control select2">
                            <option value="">All Workers</option>
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Dealer</label>
                        <select id="filter_dealer" class="form-control select2">
                            <option value="">All Dealers</option>
                            @foreach($dealers as $dealer)
                                <option value="{{ $dealer->id }}">{{ $dealer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Part Type</label>
                        <select id="filter_part" class="form-control">
                            <option value="">All Parts</option>
                            <option value="body">Body Part</option>
                            <option value="finishing">Finishing Part</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Status</label>
                        <select id="filter_status" class="form-control">
                            <option value="">All Status</option>
                            <option value="0">Pending</option>
                            <option value="1">Confirmed</option>
                            <option value="2">Completed</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <div class="btn-group w-100">
                            <button type="button" id="btn-filter" class="btn btn-primary" title="Search"><i data-feather="search"></i></button>
                            <button type="button" id="btn-reset" class="btn btn-danger" title="Reset Filters"><i data-feather="refresh-ccw"></i></button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered yajra-datatable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product Image</th>
                                <th>Manufacture Image</th>
                                <th>Manufacture No</th>
                                <th>Product</th>
                                <th>Worker</th>
                                <th>Customer</th>
                                <th>Quantity</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Totals</th>
                                <th>Confirm</th>
                                <th>Is Complete</th>
                                <th>Order Date</th>
                                <th>Update Date</th>
                                <th>Order By</th>
                                <th>Collected By</th>
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
                url: "{{ route('admin.manufacture.index') }}",
                data: function (d) {
                    d.from_date = from_date;
                    d.to_date = to_date;
                    d.worker_id = $('#filter_worker').val();
                    d.dealer_id = $('#filter_dealer').val();
                    d.part_type = $('#filter_part').val();
                    d.status = $('#filter_status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'product_image', name: 'product_image', orderable: false, searchable: false},
                {data: 'manufacture_image', name: 'manufacture_image', orderable: false, searchable: false},
                {data: 'invoice_no', name: 'invoice_no'},
                {data: 'product_name', name: 'product.name'},
                {data: 'worker_name', name: 'worker.name'},
                {data: 'dealer_name', name: 'dealer.name'},
                {data: 'manufacture_qty', name: 'manufacture_qty'},
                {data: 'part_type', name: 'part_type', orderable: false, searchable: false},
                {data: 'price', name: 'price', orderable: false, searchable: false},
                {data: 'totals', name: 'totals', orderable: false, searchable: false},
                {data: 'status', name: 'status'},
                {data: 'is_complete', name: 'is_complete', orderable: false, searchable: false},
                {data: 'created_at', name: 'created_at'},
                {data: 'updated_at', name: 'updated_at'},
                {data: 'creaded_by', name: 'completedBy.name'},
                {data: 'collected_by', name: 'collectedBy.name'},
                {
                    data: 'action', 
                    name: 'action', 
                    orderable: true, 
                    searchable: true
                },
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

        // Real-time filtering on select change
        $('#filter_worker, #filter_dealer, #filter_part, #filter_status').on('change', function() {
            table.draw();
        });

        // Update real-time for date picker
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
            $('#filter_dealer').val('').trigger('change.select2');
            $('#filter_part').val('');
            $('#filter_status').val('');
            table.draw();
        });

        $(document).on('change', '.change-status', function() {
            let id = $(this).data('id');
            let field = $(this).data('field');
            let value = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('admin.manufacture.change-status') }}",
                type: 'POST',
                data: {
                    id: id,
                    field: field,
                    value: value
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
                        url: "{{ url('admin/manufacture') }}/" + id,
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
