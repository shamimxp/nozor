@extends('layouts.admin')
@section('title', 'Purchase List')
@section('content')
<div class="row">
    {{-- Filter Card --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0"><i data-feather="filter"></i> Filter Purchases</h4>
                    <button class="btn btn-sm btn-outline-secondary" id="toggleFilterBtn">
                        <i data-feather="chevron-down"></i>
                    </button>
                </div>
            </div>
            <div class="card-body pt-2" id="filterBody">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <div class="form-group mb-1">
                            <label for="filter_purchase_number">Purchase No</label>
                            <input type="text" id="filter_purchase_number" class="form-control form-control-sm" placeholder="e.g. PUR-1001">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-1">
                            <label for="filter_order_number">Order No</label>
                            <input type="text" id="filter_order_number" class="form-control form-control-sm" placeholder="e.g. ORD-1001">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-1">
                            <label for="filter_vendor_phone">Vendor Phone</label>
                            <input type="text" id="filter_vendor_phone" class="form-control form-control-sm" placeholder="Phone number...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-1">
                            <label for="filter_status">Status</label>
                            <select id="filter_status" class="form-control form-control-sm">
                                <option value="">-- All --</option>
                                <option value="pending">Pending</option>
                                <option value="confirm">Confirm</option>
                                <option value="received">Received</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-1">
                            <label>&nbsp;</label>
                            <div class="d-flex" style="gap:6px;">
                                <button id="applyFilterBtn" class="btn btn-primary btn-sm w-100">
                                    <i data-feather="search"></i> Filter
                                </button>
                                <button id="clearFilterBtn" class="btn btn-outline-secondary btn-sm w-100">
                                    <i data-feather="x"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Purchase List Card --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Purchase List</h4>
                    <div class="d-flex" style="gap:6px;">
                        <button class="btn btn-success btn-sm" id="exportExcelBtn" title="Export to Excel (CSV)">
                            <i data-feather="file-text"></i> Excel
                        </button>
                        <button class="btn btn-danger btn-sm" id="exportPdfBtn" title="Export to PDF">
                            <i data-feather="file"></i> PDF
                        </button>
                        <a href="{{ route('admin.purchase.create') }}" class="btn btn-primary btn-sm">
                            <i data-feather="plus"></i> Create Purchase
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="purchaseTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Purchase Info</th>
                            <th>Vendor</th>
                            <th>Financial Summary</th>
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

        // ---- Toggle Filter Card ----
        $('#toggleFilterBtn').on('click', function () {
            $('#filterBody').slideToggle(200);
            var icon = $(this).find('[data-feather]');
            if ($('#filterBody').is(':visible')) {
                icon.attr('data-feather', 'chevron-up');
            } else {
                icon.attr('data-feather', 'chevron-down');
            }
            if (feather) feather.replace({ width: 14, height: 14 });
        });

        // ---- DataTable ----
        var table = $('#purchaseTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.purchase.index') }}",
                data: function (d) {
                    d.purchase_number = $('#filter_purchase_number').val();
                    d.order_number    = $('#filter_order_number').val();
                    d.vendor_phone    = $('#filter_vendor_phone').val();
                    d.status          = $('#filter_status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'purchase_info', name: 'purchase_number'},
                {data: 'vendor_info', name: 'vendor.name'},
                {data: 'financials', name: 'grand_total'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        // ---- Apply / Reset Filters ----
        $('#applyFilterBtn').on('click', function () {
            table.draw();
        });

        $('#clearFilterBtn').on('click', function () {
            $('#filter_purchase_number').val('');
            $('#filter_order_number').val('');
            $('#filter_vendor_phone').val('');
            $('#filter_status').val('');
            table.draw();
        });

        // Enter key triggers filter on inputs
        $('#filter_purchase_number, #filter_order_number, #filter_vendor_phone').on('keypress', function (e) {
            if (e.which === 13) table.draw();
        });

        // ---- Export Helpers ----
        function buildExportParams() {
            var params = new URLSearchParams();
            var pNum   = $('#filter_purchase_number').val();
            var oNum   = $('#filter_order_number').val();
            var phone  = $('#filter_vendor_phone').val();
            var status = $('#filter_status').val();
            if (pNum)   params.append('purchase_number', pNum);
            if (oNum)   params.append('order_number', oNum);
            if (phone)  params.append('vendor_phone', phone);
            if (status) params.append('status', status);
            return params.toString();
        }

        $('#exportExcelBtn').on('click', function () {
            var qs = buildExportParams();
            window.location.href = "{{ route('admin.purchase.export-excel') }}" + (qs ? '?' + qs : '');
        });

        $('#exportPdfBtn').on('click', function () {
            var qs = buildExportParams();
            window.location.href = "{{ route('admin.purchase.export-list-pdf') }}" + (qs ? '?' + qs : '');
        });

        // ---- Delete Purchase ----
        $('body').on('click', '.deletePurchase', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
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
                        url: "{{ url('admin/purchase') }}" + '/' + id,
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
