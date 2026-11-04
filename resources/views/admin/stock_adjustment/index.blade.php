@extends('layouts.admin')
@section('title', 'Stock Adjustment List')
@section('breadcrumb')
<div class="content-header d-flex justify-content-between align-items-center">
    <div class="content-header-left">
        <h2 class="content-header-title float-left mb-0">Stock Adjustment</h2>
        <div class="breadcrumb-wrapper">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Stock Adjustments</li>
            </ol>
        </div>
    </div>
    <div>
        <a href="{{ route('admin.stock-adjustment.create') }}" class="btn btn-primary">
            <i data-feather="plus" class="mr-25"></i> Create Adjustment
        </a>
    </div>
</div>
@endsection

@section('content')
<!-- Metrics Overview Cards -->
<div class="row">
    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between pb-0">
                <h5 class="card-title text-muted font-small-3 font-weight-bolder">TOTAL ADJUSTMENTS</h5>
                <div class="avatar bg-light-primary p-50 m-0">
                    <div class="avatar-content"><i data-feather="sliders" class="font-medium-5"></i></div>
                </div>
            </div>
            <div class="card-body pt-1">
                <h3 class="font-weight-bolder mb-25 text-primary">{{ number_format($totalAdjustments ?? 0) }}</h3>
                <span class="text-muted font-small-2">All adjustment records</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between pb-0">
                <h5 class="card-title text-muted font-small-3 font-weight-bolder">TOTAL ADDITIONS</h5>
                <div class="avatar bg-light-success p-50 m-0">
                    <div class="avatar-content"><i data-feather="plus-circle" class="font-medium-5"></i></div>
                </div>
            </div>
            <div class="card-body pt-1">
                <h3 class="font-weight-bolder mb-25 text-success">{{ number_format($totalAdditions ?? 0) }}</h3>
                <span class="text-muted font-small-2">Surplus / Found / Return</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between pb-0">
                <h5 class="card-title text-muted font-small-3 font-weight-bolder">PENDING EXCHANGES</h5>
                <div class="avatar bg-light-warning p-50 m-0">
                    <div class="avatar-content"><i data-feather="clock" class="font-medium-5"></i></div>
                </div>
            </div>
            <div class="card-body pt-1">
                <h3 class="font-weight-bolder mb-25 text-warning">{{ number_format($pendingReceives ?? 0) }}</h3>
                <span class="text-muted font-small-2">Awaiting replacement receipt</span>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between pb-0">
                <h5 class="card-title text-muted font-small-3 font-weight-bolder">TOTAL DEDUCTIONS</h5>
                <div class="avatar bg-light-danger p-50 m-0">
                    <div class="avatar-content"><i data-feather="minus-circle" class="font-medium-5"></i></div>
                </div>
            </div>
            <div class="card-body pt-1">
                <h3 class="font-weight-bolder mb-25 text-danger">{{ number_format($totalDeductions ?? 0) }}</h3>
                <span class="text-muted font-small-2">Damage, loss, and exchange</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Table Card -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom py-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0"><i data-feather="list" class="mr-50"></i> Adjustment Records</h4>
                    <div class="d-flex" style="gap:6px;">
                        @include('admin.partials.show_deleted_button', ['route' => route('admin.stock-adjustment.trash')])
                        <a href="{{ route('admin.stock-adjustment.create') }}" class="btn btn-primary btn-sm">
                            <i data-feather="plus" class="mr-25"></i> Add Adjustment
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-2">
                <!-- Filters -->
                <div class="border rounded bg-light p-1 mb-2">
                    <div class="row align-items-end">
                        <div class="col-lg-3 col-md-6 col-12 mb-1 mb-lg-0">
                            <label class="font-small-2 font-weight-bold">Product</label>
                            <select id="filterProduct" class="form-control form-control-sm select2">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ number_format($product->stock, 2) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-6 col-12 mb-1 mb-lg-0">
                            <label class="font-small-2 font-weight-bold">Adjustment Type</label>
                            <select id="filterType" class="form-control form-control-sm">
                                <option value="">All Types</option>
                                <option value="addition">Addition (Surplus)</option>
                                <option value="normal">Normal (Exchange)</option>
                                <option value="abnormal">Abnormal (Damage)</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-4 col-12 mb-1 mb-lg-0">
                            <label class="font-small-2 font-weight-bold">Exchange Status</label>
                            <select id="filterStatus" class="form-control form-control-sm">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending Receipt</option>
                                <option value="received">Received</option>
                            </select>
                        </div>

                        <div class="col-lg-2 col-md-4 col-12 mb-1 mb-lg-0">
                            <label class="font-small-2 font-weight-bold">From Date</label>
                            <input type="date" id="filterDateFrom" class="form-control form-control-sm">
                        </div>

                        <div class="col-lg-2 col-md-4 col-12 mb-1 mb-lg-0">
                            <label class="font-small-2 font-weight-bold">To Date</label>
                            <input type="date" id="filterDateTo" class="form-control form-control-sm">
                        </div>

                        <div class="col-lg-1 col-12 text-lg-right mt-1 mt-lg-0">
                            <button type="button" id="resetFilterBtn" class="btn btn-outline-secondary btn-sm btn-block" title="Reset Filters">
                                <i data-feather="refresh-cw"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table id="adjustmentTable" class="table table-bordered table-hover text-nowrap w-100">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Type</th>
                                <th>Receive Status</th>
                                <th>Date</th>
                                <th>Created By</th>
                                <th>Reason / Note</th>
                                <th style="width: 100px;">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        if (feather) {
            feather.replace({ width: 14, height: 14 });
        }

        $('#filterProduct').select2({
            placeholder: "All Products",
            allowClear: true,
            width: '100%'
        });

        var adjustmentTable = $('#adjustmentTable').DataTable({
            processing: true,
            serverSide: true,
            order: [[5, 'desc']],
            ajax: {
                url: "{{ route('admin.stock-adjustment.index') }}",
                data: function(d) {
                    d.product_id = $('#filterProduct').val();
                    d.type = $('#filterType').val();
                    d.receive_status = $('#filterStatus').val();
                    d.date_from = $('#filterDateFrom').val();
                    d.date_to = $('#filterDateTo').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'product_name', name: 'product.name'},
                {data: 'quantity_formatted', name: 'quantity'},
                {data: 'type_badge', name: 'type'},
                {data: 'receive_status', name: 'is_received', orderable: false, searchable: false},
                {data: 'date', name: 'adjustment_date'},
                {data: 'creator_name', name: 'creator.name', orderable: false, searchable: false},
                {data: 'reason', name: 'reason', defaultContent: '-'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        $('#filterProduct, #filterType, #filterStatus, #filterDateFrom, #filterDateTo').on('change', function() {
            adjustmentTable.ajax.reload();
        });

        $('#resetFilterBtn').on('click', function() {
            $('#filterProduct').val('').trigger('change');
            $('#filterType, #filterStatus, #filterDateFrom, #filterDateTo').val('');
            adjustmentTable.ajax.reload();
        });

        // Receive exchange stock
        $(document).on('click', '.receiveAdjustmentBtn', function() {
            var button = $(this);
            var id = button.data('id');
            var product = button.data('product');
            var quantity = button.data('quantity');

            Swal.fire({
                title: 'Receive Exchange Stock?',
                text: 'This will add ' + quantity + ' unit(s) back into inventory for "' + product + '".',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Receive Stock',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-outline-secondary ml-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                button.attr('disabled', true);

                $.ajax({
                    url: "{{ url('/admin/stock-adjustment') }}/" + id + "/receive",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        toastr.success(response.success || 'Stock received successfully.');
                        adjustmentTable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        button.attr('disabled', false);
                        var errorMsg = 'Failed to receive stock.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errorMsg = xhr.responseJSON.error;
                        }
                        toastr.error(errorMsg);
                    }
                });
            });
        });

        // Soft-delete stock adjustment
        $(document).on('click', '.deleteAdjustmentBtn', function() {
            var button = $(this);
            var id = button.data('id');

            Swal.fire({
                title: 'Move to trash?',
                text: 'You can restore this later from Show Deleted Data.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-outline-secondary ml-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                button.attr('disabled', true);

                $.ajax({
                    url: "{{ url('/admin/stock-adjustment') }}/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(data) {
                        adjustmentTable.ajax.reload(null, false);
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message || data.success,
                            customClass: { confirmButton: 'btn btn-success' }
                        });
                    },
                    error: function(xhr) {
                        button.attr('disabled', false);
                        Swal.fire({
                            icon: 'error',
                            title: 'Cannot delete',
                            text: (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong!',
                            customClass: { confirmButton: 'btn btn-danger' }
                        });
                    }
                });
            });
        });
    });
</script>
@endpush
