@extends('layouts.admin')
@section('title', 'Worker Profile')
@section('content')
<div class="row">
    <!-- Worker Details Profile Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                @if($worker->profile_image)
                    <img src="{{ asset('storage/'.$worker->profile_image) }}" class="rounded-circle mb-1" width="100" height="100" style="object-fit:cover;" alt="Worker Profile">
                @else
                    <div class="rounded-circle mb-1 bg-light d-flex align-items-center justify-content-center mx-auto" style="width:100px; height:100px;">
                        <i data-feather="user" style="width:40px; height:40px; color:#aaa;"></i>
                    </div>
                @endif
                <h4 class="mb-0">{{ $worker->name }}</h4>
                <p class="text-muted">{{ $worker->type == 1 ? 'Finishing Part Worker' : 'Body Part Worker' }}</p>

                <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-1">
                    <span class="font-weight-bold">Phone:</span>
                    <span>{{ $worker->phone ?? 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1 border-top pt-1">
                    <span class="font-weight-bold">Email:</span>
                    <span>{{ $worker->email ?? 'N/A' }}</span>
                </div>
                 <div class="d-flex justify-content-between align-items-center mt-1 border-top pt-1">
                    <span class="font-weight-bold">Address:</span>
                    <span>{{ $worker->address ?? 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1 border-top pt-1">
                    <span class="font-weight-bold">Status:</span>
                    <span>
                        @if($worker->status == 1)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-md-8">
        <div class="row">
            <!-- Manufacturing Stats -->
            <div class="col-sm-4 col-12">
                <div class="card text-center text-primary filter-stat-card cursor-pointer shadow-none border" data-filter="" title="Click to show all orders" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;">
                    <div class="card-body py-2">
                        <div class="avatar bg-light-primary p-50 mb-1">
                            <div class="avatar-content">
                                <i data-feather="box" class="font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="font-weight-bolder">{{ $totalOrders }}</h2>
                        <p class="card-text">Total Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12">
                <div class="card text-center text-warning filter-stat-card cursor-pointer shadow-none border" data-filter="pending" title="Click to show pending orders" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;">
                    <div class="card-body py-2">
                        <div class="avatar bg-light-warning p-50 mb-1">
                            <div class="avatar-content">
                                <i data-feather="clock" class="font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="font-weight-bolder">{{ $pendingOrders }}</h2>
                        <p class="card-text">Pending Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12">
                <div class="card text-center text-success filter-stat-card cursor-pointer shadow-none border" data-filter="collected" title="Click to show collected orders" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;">
                    <div class="card-body py-2">
                        <div class="avatar bg-light-success p-50 mb-1">
                            <div class="avatar-content">
                                <i data-feather="check-circle" class="font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="font-weight-bolder">{{ $completedOrders }}</h2>
                        <p class="card-text">Collected Orders</p>
                    </div>
                </div>
            </div>

            <!-- Financial Stats -->
            <div class="col-sm-4 col-12">
                <div class="card text-center">
                    <div class="card-body py-2">
                        <div class="avatar bg-light-info p-50 mb-1">
                            <div class="avatar-content">
                                <i data-feather="dollar-sign" class="font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="font-weight-bolder">{{ number_format($totalAmount, 2) }}</h2>
                        <p class="card-text">Total Billed</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12">
                <div class="card text-center text-success">
                    <div class="card-body py-2">
                        <div class="avatar bg-light-success p-50 mb-1">
                            <div class="avatar-content">
                                <i data-feather="credit-card" class="font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="font-weight-bolder">{{ number_format($totalPaid, 2) }}</h2>
                        <p class="card-text">Total Paid</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12">
                <div class="card text-center text-danger">
                    <div class="card-body py-2">
                        <div class="avatar bg-light-danger p-50 mb-1">
                            <div class="avatar-content">
                                <i data-feather="alert-circle" class="font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="font-weight-bolder">{{ number_format($totalDue, 2) }}</h2>
                        <p class="card-text">Total Due</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Worker Orders Table -->
    <div class="col-12 mt-2" id="worker-orders-section">
        <div class="card">
            <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <div class="d-flex align-items-center">
                    <h4 class="card-title mb-0 mr-1"><i data-feather="package" class="mr-50 text-primary"></i> Worker Orders</h4>
                    <span class="badge badge-light-primary font-weight-bold" id="orders-badge-count">{{ $totalOrders }} Orders</span>
                </div>
                <div class="d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <label class="mb-0 text-muted small font-weight-bold d-flex align-items-center">
                        <i data-feather="filter" style="width: 13px; height: 13px;" class="mr-25"></i> Filter Status:
                    </label>
                    <select id="filter_order_status" class="form-control form-control-sm" style="width: 180px;">
                        <option value="">All Orders ({{ $totalOrders }})</option>
                        <option value="pending">Pending ({{ $pendingOrders }})</option>
                        <option value="collected">Collected ({{ $completedOrders }})</option>
                    </select>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-1">
                    <table class="table table-bordered table-hover w-100" id="worker-orders-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 45px;">SL</th>
                                <th>Date</th>
                                <th class="text-center" style="width: 110px; min-width: 110px;">Manufacture Image</th>
                                <th>Invoice No</th>
                                <th>Product</th>
                                <th class="text-center" style="min-width: 110px;">Order Type</th>
                                <th class="text-center">Order Qty</th>
                                <th class="text-center">Already Recv</th>
                                <th class="text-center">Pending Qty</th>
                                <th class="text-right">Total Bill</th>
                                <th class="text-center">Collection Status</th>
                                <th class="text-center" style="width: 80px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="col-12 mt-2">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title mb-0"><i data-feather="dollar-sign" class="mr-50 text-success"></i> Payment History</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-1">
                    <table class="table table-striped table-bordered w-100" id="worker-payments-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Product</th>
                                <th class="text-center" style="min-width: 110px;">Order Type</th>
                                <th class="text-center">Status</th>
                                <th class="text-right">Amount</th>
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

<!-- Manufacture Blueprint Preview Modal -->
<div class="modal fade" id="manufactureImageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light py-1">
                <div>
                    <h5 class="modal-title font-weight-bolder text-primary mb-25" id="mfgModalTitleText">
                        Manufacture Blueprint
                    </h5>
                    <span class="small text-muted font-weight-bold" id="mfgModalInvoice"></span>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-2" style="background: #f8fafc; max-height: 75vh; overflow-y: auto;">
                <div class="d-inline-block p-1 bg-white shadow-sm rounded" style="border: 1px solid #e2e8f0; max-width: 100%;">
                    <img id="mfgModalImage" src="" alt="Manufacture Blueprint" style="max-width: 100%; max-height: 60vh; object-fit: contain; border-radius: 4px; display: block; margin: 0 auto;">
                </div>
                <div id="mfgModalThumbs" class="mt-1 d-flex justify-content-center align-items-center flex-wrap" style="display: none; gap: 8px;"></div>
            </div>
            <div class="modal-footer py-1 d-flex justify-content-between align-items-center">
                <span class="small text-muted"><i data-feather="info" style="width: 13px; height: 13px;"></i> Click thumbnails to switch images</span>
                <div>
                    <a id="mfgModalPrintBtn" href="#" target="_blank" class="btn btn-sm btn-outline-primary mr-50" style="display: none;">
                        <i data-feather="printer" style="width: 13px; height: 13px;" class="mr-25"></i> Print Order
                    </a>
                    <a id="mfgModalDownloadBtn" href="#" download class="btn btn-sm btn-primary mr-50" target="_blank">
                        <i data-feather="download" style="width: 13px; height: 13px;" class="mr-25"></i> Open Full Image
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .filter-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }

    /* Manufacture Image Table Cell */
    #worker-orders-table td.mfg-image-cell {
        padding: 0 !important;
        margin: 0 !important;
        vertical-align: middle !important;
        text-align: center !important;
        height: 1px !important;
        width: 110px !important;
        min-width: 110px !important;
        position: relative !important;
    }

    .btn-preview-mfg-img {
        width: 100% !important;
        height: 100% !important;
        min-height: 70px !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        border-radius: 0 !important;
        display: block !important;
        position: relative !important;
        overflow: hidden !important;
        cursor: pointer !important;
        background: #f1f2f6 !important;
    }

    .btn-preview-mfg-img img {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        transition: transform 0.25s ease, filter 0.2s ease;
    }

    .btn-preview-mfg-img:hover img {
        transform: scale(1.06);
        filter: brightness(0.92);
    }

    .btn-preview-mfg-img .mfg-zoom-badge {
        position: absolute;
        bottom: 4px;
        right: 4px;
        background: rgba(0, 0, 0, 0.65);
        color: #ffffff;
        border-radius: 3px;
        padding: 2px 5px;
        font-size: 9px;
        line-height: 1;
        display: flex;
        align-items: center;
        pointer-events: none;
        z-index: 2;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        transition: background-color 0.2s ease;
    }

    .btn-preview-mfg-img:hover .mfg-zoom-badge {
        background: #7367f0;
    }
</style>
@endsection

@push('scripts')
<script>
    $(function () {
        // Worker Orders DataTable
        var ordersTable = $('#worker-orders-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.worker.show', $worker->id) }}",
                data: function(d) {
                    d.type = 'orders';
                    d.status = $('#filter_order_status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
                {data: 'date', name: 'created_at'},
                {data: 'manufacture_image', name: 'manufacture_image', orderable: false, searchable: false, className: 'mfg-image-cell p-0 text-center'},
                {data: 'invoice_no', name: 'invoice_no'},
                {data: 'product', name: 'product.name'},
                {data: 'part_type', name: 'part_type', orderable: false, searchable: false, className: 'text-center'},
                {data: 'manufacture_qty', name: 'manufacture_qty', className: 'text-center'},
                {data: 'received_qty', name: 'received_qty', className: 'text-center'},
                {data: 'pending_qty', name: 'pending_qty', orderable: false, searchable: false, className: 'text-center'},
                {data: 'total_price', name: 'grand_total', className: 'text-right'},
                {data: 'collection_status', name: 'collected_by', className: 'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            order: [[1, 'desc']],
            drawCallback: function(settings) {
                if (typeof feather !== 'undefined') {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        $('#filter_order_status').on('change', function() {
            ordersTable.draw();
        });

        // Click on stats cards to filter orders
        $('.filter-stat-card').on('click', function() {
            var filterVal = $(this).data('filter');
            $('#filter_order_status').val(filterVal).trigger('change');
            $('html, body').animate({
                scrollTop: $("#worker-orders-section").offset().top - 80
            }, 400);
        });

        // Worker Payments DataTable
        var paymentsTable = $('#worker-payments-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.worker.show', $worker->id) }}",
                data: function(d) {
                    d.type = 'payments';
                }
            },
            columns: [
                {data: 'date', name: 'date'},
                {data: 'invoice_no', name: 'manufacture.invoice_no'},
                {data: 'product', name: 'manufacture.product.name'},
                {data: 'part_type', name: 'part_type', orderable: false, searchable: false, className: 'text-center'},
                {data: 'status', name: 'status', className: 'text-center'},
                {data: 'total_amount', name: 'total_amount', className: 'text-right font-weight-bold'},
            ],
            order: [[0, 'desc']],
            drawCallback: function(settings) {
                if (typeof feather !== 'undefined') {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        // Manufacture Image Popup Preview
        $(document).on('click', '.btn-preview-mfg-img', function(e) {
            e.preventDefault();
            let mainImg = $(this).data('img');
            let title = $(this).data('title');
            let invoice = $(this).data('invoice');
            let printUrl = $(this).data('print-url');
            let allImages = $(this).data('all-images');

            if (typeof allImages === 'string') {
                try {
                    allImages = JSON.parse(allImages);
                } catch(e) {
                    allImages = [mainImg];
                }
            }

            $('#mfgModalTitleText').text(title || 'Manufacture Blueprint');
            $('#mfgModalInvoice').text(invoice ? 'Invoice No: ' + invoice : '');
            $('#mfgModalImage').attr('src', mainImg);
            $('#mfgModalDownloadBtn').attr('href', mainImg);

            if (printUrl) {
                $('#mfgModalPrintBtn').attr('href', printUrl).show();
            } else {
                $('#mfgModalPrintBtn').hide();
            }

            let thumbsContainer = $('#mfgModalThumbs');
            thumbsContainer.empty();

            if (Array.isArray(allImages) && allImages.length > 1) {
                allImages.forEach(function(imgSrc) {
                    let activeClass = (imgSrc === mainImg) ? 'border-primary' : 'border-secondary';
                    let thumb = $('<img src="' + imgSrc + '" class="mfg-thumb-nav ' + activeClass + '" style="width: 52px; height: 52px; object-fit: contain; cursor: pointer; border: 2px solid; border-radius: 6px; background: #fff; padding: 2px;">');
                    thumbsContainer.append(thumb);
                });
                thumbsContainer.show();
            } else {
                thumbsContainer.hide();
            }

            if (typeof feather !== 'undefined') {
                feather.replace({ width: 14, height: 14 });
            }

            $('#manufactureImageModal').modal('show');
        });

        $(document).on('click', '.mfg-thumb-nav', function() {
            let src = $(this).attr('src');
            $('#mfgModalImage').attr('src', src);
            $('#mfgModalDownloadBtn').attr('href', src);
            $('.mfg-thumb-nav').removeClass('border-primary').addClass('border-secondary');
            $(this).removeClass('border-secondary').addClass('border-primary');
        });

        if (typeof feather !== 'undefined') {
            feather.replace({ width: 14, height: 14 });
        }
    });
</script>
@endpush
