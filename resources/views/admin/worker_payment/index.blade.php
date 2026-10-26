@extends('layouts.admin')
@section('title', isset($page_title) ? $page_title : 'Worker Invoice List')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">{{ isset($page_title) ? $page_title : 'Worker Invoice List' }}</h4>
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
                            <option value="partially_paid">Partially Paid</option>
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
                                <th class="text-center" style="width: 110px; min-width: 110px;">Manufacture Image</th>
                                <th>Manufacture No</th>
                                <th>Product</th>
                                <th>Worker</th>
                                <th class="text-center" style="min-width: 110px;">Order Type</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-right">Total Amount</th>
                                <th>Status</th>
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
    /* Manufacture Image Table Cell */
    .yajra-datatable td.mfg-image-cell {
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
        min-height: 75px !important;
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
                url: "{{ route('admin.worker-invoices.index') }}",
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
                {data: 'manufacture_image', name: 'manufacture_image', orderable: false, searchable: false, className: 'mfg-image-cell p-0 text-center'},
                {data: 'invoice_no', name: 'manufacture.invoice_no'},
                {data: 'product', name: 'manufacture.product.name'},
                {data: 'worker_name', name: 'worker.name'},
                {data: 'part_type', name: 'part_type', orderable: false, searchable: false, className: 'text-center'},
                {data: 'quantity', name: 'received_qty', className: 'text-center'},
                {data: 'total_amount', name: 'total_amount', className: 'text-right'},
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
                        url: "{{ url('admin/worker-invoices') }}/" + id,
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
    });
</script>
@endpush
