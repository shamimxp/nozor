@extends('layouts.admin')
@section('title', 'Receive Manufacture Orders')

@push('styles')
<style>
    .custom-dark-btn {
        background-color: #283046 !important;
        border-color: #283046 !important;
        color: #fff !important;
        transition: all 0.2s ease;
    }
    .custom-dark-btn:hover {
        background-color: #1a233a !important;
        border-color: #1a233a !important;
        color: #fff !important;
    }
    .input-select-group {
        display: flex;
        align-items: stretch;
        width: 100%;
        position: relative;
    }
    /* Select Container Box */
    .select-container {
        flex: 1 1 auto;
        min-width: 0 !important;
        max-width: 100%;
        position: relative !important;
    }
    .select-container .select2-container {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        display: block !important;
    }
    .select-container .select2-container .select2-selection--single {
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        height: 40px !important; 
        display: flex !important;
        align-items: center !important;
        border: 1px solid #d8d6de !important;
        background-color: #fff !important;
        padding-left: 10px !important;
    }
    .select-container .select2-container--open .select2-selection--single {
        border-color: #7367f0 !important;
        box-shadow: 0 2px 8px rgba(115, 103, 240, 0.15) !important;
    }
    .select-container .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 0 !important;
        padding-right: 28px !important;
        display: flex !important;
        align-items: center !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        width: 100% !important;
        color: #5e5873 !important;
        font-size: 0.9rem !important;
    }
    .select-container .select2-container .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        right: 8px !important;
    }
    /* Select2 Dropdown Styling - Contained strictly within the card */
    .select-container .select2-dropdown {
        border: 1px solid #7367f0 !important;
        border-radius: 0.357rem !important;
        box-shadow: 0 8px 25px rgba(47, 43, 61, 0.15) !important;
        z-index: 1060 !important;
        background-color: #fff !important;
        overflow: hidden !important;
        margin-top: 2px !important;
        min-width: 100% !important;
        width: 100% !important;
        left: 0 !important;
    }
    .select-container .select2-search--dropdown {
        padding: 8px 10px !important;
        background-color: #f8f8fb !important;
        border-bottom: 1px solid #ebe9f1 !important;
    }
    .select-container .select2-search--dropdown .select2-search__field {
        height: 36px !important;
        border: 1px solid #d8d6de !important;
        border-radius: 0.357rem !important;
        padding: 6px 12px !important;
        font-size: 0.88rem !important;
        background-color: #fff !important;
        outline: none !important;
        box-shadow: none !important;
        width: 100% !important;
        color: #5e5873 !important;
    }
    .select-container .select2-search--dropdown .select2-search__field:focus {
        border-color: #7367f0 !important;
        box-shadow: 0 0 0 2px rgba(115, 103, 240, 0.2) !important;
    }
    .select-container .select2-results__options {
        max-height: 280px !important;
        overflow-y: auto !important;
        padding: 6px !important;
    }
    .select-container .select2-results__option {
        padding: 8px 12px !important;
        border-radius: 0.25rem !important;
        margin-bottom: 2px !important;
        font-size: 0.88rem !important;
        color: #5e5873 !important;
        border-bottom: 1px solid #f8f8fb !important;
    }
    .select-container .select2-results__option--highlighted {
        background-color: #7367f0 !important;
        color: #fff !important;
    }
    .select-container .select2-results__option--highlighted strong,
    .select-container .select2-results__option--highlighted small {
        color: #fff !important;
    }
    .select-container .select2-results__option--highlighted .badge-light-primary {
        background-color: #fff !important;
        color: #7367f0 !important;
    }
    .select-container .select2-results__option--highlighted .badge-light-danger {
        background-color: #ea5455 !important;
        color: #fff !important;
    }
    .select-container .select2-results__option[aria-selected=true] {
        background-color: #f3f2fe !important;
        color: #7367f0 !important;
    }

    /* Buttons & Barcode Input */
    #btn-add-manual, #btn-add-barcode {
        flex-shrink: 0 !important;
        white-space: nowrap !important;
        height: 40px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 16px !important;
        font-weight: 600;
    }
    .barcode-input-group .form-control {
        height: 40px !important;
    }
    .barcode-input-group .input-group-text {
        height: 40px !important;
    }

    /* Table Styling */
    .receive-table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        border: 1px solid #ebe9f1;
        border-radius: 0.428rem;
        background: #fff;
        width: 100%;
    }
    .receive-table {
        width: 100%;
        margin-bottom: 0;
    }
    .receive-table th {
        background-color: #f8f8fb !important;
        color: #5e5873;
        font-weight: 700;
        font-size: 0.8rem;
        letter-spacing: 0.3px;
        white-space: nowrap;
        vertical-align: middle !important;
        padding: 0.75rem 0.6rem !important;
        border-bottom: 2px solid #ebe9f1;
    }
    .receive-table td {
        vertical-align: middle !important;
        padding: 0.75rem 0.6rem !important;
    }
    .input-receive-qty {
        font-weight: 700;
        text-align: center;
        width: 85px;
        max-width: 95px;
        margin: 0 auto;
        border-radius: 0.357rem;
    }
    .receive-scan-card {
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .receive-scan-card:hover {
        border-color: #7367f0 !important;
    }
    @media (max-width: 767.98px) {
        .receive-table th, .receive-table td {
            padding: 0.5rem 0.4rem !important;
            font-size: 0.85rem;
        }
        .input-receive-qty {
            width: 75px;
        }
    }

    /* Manufacture Image Table Cell */
    .receive-table td.mfg-image-cell {
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
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm mb-2">
            <div class="card-header border-bottom py-1 d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                <div>
                    <h4 class="card-title mb-25 font-weight-bolder">Receive Manufacture Orders</h4>
                    <small class="text-muted">Receive partial or full quantities from workers and auto-generate worker payments</small>
                </div>
                <a href="{{ route('admin.manufacture.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="arrow-left" class="mr-25"></i> Back to Orders
                </a>
            </div>

            <div class="card-body mt-2">
                <!-- Top Scan & Manual Selection Toolbar -->
                <div class="row align-items-stretch">
                    <!-- Scan Barcode Box -->
                    <div class="col-12 col-lg-6 mb-1 mb-lg-0">
                        <div class="p-1 p-md-2 border rounded bg-light h-100 d-flex flex-column justify-content-between shadow-none receive-scan-card">
                            <div>
                                <label class="font-weight-bold text-dark d-flex align-items-center mb-50">
                                    <i data-feather="maximize" class="mr-50 text-primary"></i> Scan Barcode / Enter Invoice No
                                </label>
                                <div class="input-group barcode-input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white"><i data-feather="camera"></i></span>
                                    </div>
                                    <input type="text" id="barcode_input" class="form-control" placeholder="Scan or type Invoice No..." autofocus>
                                    <div class="input-group-append">
                                        <button class="btn custom-dark-btn px-2" type="button" id="btn-add-barcode">
                                            <i data-feather="plus" class="mr-25"></i> Add
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted mt-50 d-block">
                                <i data-feather="corner-down-right" style="width: 13px; height: 13px;" class="mr-25"></i> Press <strong>Enter</strong> or click <strong>Add</strong> to scan barcode directly.
                            </small>
                        </div>
                    </div>

                    <!-- Manual Selection Box -->
                    <div class="col-12 col-lg-6">
                        <div class="p-1 p-md-2 border rounded bg-light h-100 d-flex flex-column justify-content-between shadow-none receive-scan-card">
                            <div>
                                <label class="font-weight-bold text-dark d-flex align-items-center mb-50">
                                    <i data-feather="list" class="mr-50 text-primary"></i> Select Confirmed Order to Receive
                                </label>
                                <div class="input-select-group">
                                    <div class="select-container" id="manual_select_container">
                                        <select id="manual_select" class="form-control select2">
                                            <option value="">-- Choose a Confirmed Order with Pending Qty --</option>
                                            @foreach($pending_orders as $order)
                                                @php
                                                    $firstItem = $order->items->first();
                                                    $prod = $firstItem ? $firstItem->product : $order->product;
                                                    $prodName = $prod ? $prod->name : 'N/A';
                                                    $workerName = $order->worker ? $order->worker->name : 'Not Assigned';
                                                    $orderQty = (int) $order->manufacture_qty;
                                                    $receivedQty = (int) ($order->received_qty ?? 0);
                                                    $pendingQty = max(0, $orderQty - $receivedQty);
                                                    $unitPrice = $firstItem ? (float) $firstItem->unit_price : (float) ($order->body_part_price > 0 ? $order->body_part_price : $order->finishing_part_price);

                                                    $rawPartType = strtolower($firstItem ? ($firstItem->part_type ?? '') : '');
                                                    if (!$rawPartType) {
                                                        if ($order->body_total > 0 && $order->finishing_total > 0) $rawPartType = 'both';
                                                        elseif ($order->body_total > 0) $rawPartType = 'body';
                                                        elseif ($order->finishing_total > 0) $rawPartType = 'finishing';
                                                    }

                                                    if ($rawPartType === 'body' || $rawPartType === 'body part') {
                                                        $partTypeBadge = '<span class="badge badge-light-info font-weight-bold">Body Part</span>';
                                                        $partTypeText = 'Body Part';
                                                    } elseif ($rawPartType === 'finishing' || $rawPartType === 'finishing part') {
                                                        $partTypeBadge = '<span class="badge badge-light-primary font-weight-bold">Finishing Part</span>';
                                                        $partTypeText = 'Finishing Part';
                                                    } elseif ($rawPartType === 'both') {
                                                        $partTypeBadge = '<span class="badge badge-light-success font-weight-bold">Both</span>';
                                                        $partTypeText = 'Both';
                                                    } else {
                                                        $partTypeBadge = '<span class="badge badge-light-secondary font-weight-bold">' . e(ucfirst($rawPartType ?: 'N/A')) . '</span>';
                                                        $partTypeText = ucfirst($rawPartType ?: 'N/A');
                                                    }

                                                    $recipe = $prod ? $prod->recipe : null;
                                                    $imgs = $recipe && !empty($recipe->manufacture_images) ? $recipe->manufacture_images : [];
                                                    $mfgImg = !empty($imgs) && isset($imgs[0]) ? asset($imgs[0]) : ($prod && $prod->featured_image ? asset(config('imagepath.product') . $prod->featured_image) : asset('images/no-image.png'));
                                                    $allMfgImgs = !empty($imgs) ? array_map(function($im) { return asset($im); }, $imgs) : [$mfgImg];
                                                    $printUrl = ($order->status >= 1) ? route('admin.manufacture.print', $order->id) : '';
                                                @endphp
                                                <option value="{{ $order->id }}" 
                                                    data-invoice="{{ $order->invoice_no }}" 
                                                    data-product="{{ $prodName }}" 
                                                    data-worker="{{ $workerName }}"
                                                    data-order-qty="{{ $orderQty }}"
                                                    data-received-qty="{{ $receivedQty }}"
                                                    data-pending-qty="{{ $pendingQty }}"
                                                    data-unit-price="{{ $unitPrice }}"
                                                    data-part-type="{{ $rawPartType }}"
                                                    data-part-type-text="{{ $partTypeText }}"
                                                    data-mfg-img="{{ $mfgImg }}"
                                                    data-all-images="{{ json_encode($allMfgImgs) }}"
                                                    data-print-url="{{ $printUrl }}">
                                                    {{ $order->invoice_no }} - {{ $prodName }} [Pending: {{ $pendingQty }} pcs | Worker: {{ $workerName }} | Type: {{ $partTypeText }}]
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button class="btn custom-dark-btn px-2" type="button" id="btn-add-manual" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                        <i data-feather="plus" class="mr-25"></i> Add
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted mt-50 d-block">
                                <i data-feather="check-circle" style="width: 13px; height: 13px;" class="mr-25 text-success"></i> Only confirmed orders with uncollected pending units are listed.
                            </small>
                        </div>
                    </div>
                </div>

                <hr class="my-2">

                <!-- Receiving Items Table Section -->
                <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap" style="gap: 8px;">
                    <div>
                        <h5 class="mb-0 font-weight-bold text-dark">
                            <i data-feather="inbox" class="mr-50 text-primary"></i> Orders to Receive in this Batch
                        </h5>
                    </div>
                    <div>
                        <span class="badge badge-light-secondary font-weight-normal">
                            Edit <strong class="text-primary font-weight-bold">Receive Qty</strong> to record partial deliveries
                        </span>
                    </div>
                </div>

                <div class="receive-table-container">
                    <table class="table table-hover table-bordered receive-table" id="receive-table">
                        <thead>
                            <tr class="text-center font-weight-bold text-uppercase">
                                <th style="min-width: 130px;">Invoice No</th>
                                <th class="text-center" style="width: 110px; min-width: 110px;">Manufacture Image</th>
                                <th class="text-left" style="min-width: 160px;">Product</th>
                                <th class="text-left" style="min-width: 140px;">Worker</th>
                                <th class="text-center" style="min-width: 110px;">Order Type</th>
                                <th style="min-width: 80px;">Order Qty</th>
                                <th style="min-width: 85px;">Already Recv</th>
                                <th style="min-width: 80px;">Pending</th>
                                <th style="min-width: 110px;">Receive Qty</th>
                                <th style="min-width: 95px;" class="text-right">Unit Rate</th>
                                <th style="min-width: 120px;" class="text-right">Worker Payable</th>
                                <th style="min-width: 55px; width: 55px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr id="emptyReceiveRow">
                                <td colspan="12" class="text-center py-4 text-muted">
                                    <i data-feather="package" style="width: 38px; height: 38px;" class="text-secondary mb-50"></i>
                                    <h6 class="text-muted font-weight-bold mb-25">No orders added to receive list yet</h6>
                                    <small>Scan barcode or select confirmed orders above to begin receiving.</small>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-light font-weight-bold d-none" id="receiveTableFooter">
                            <tr>
                                <td colspan="8" class="text-right text-uppercase small font-weight-bold">Batch Totals:</td>
                                <td class="text-center text-primary font-weight-bolder" id="batchTotalUnits" style="font-size: 15px;">0</td>
                                <td class="text-right small text-muted font-weight-bold">Total Payment:</td>
                                <td class="text-right text-success font-weight-bolder" id="batchTotalPayment" style="font-size: 15px;">৳ 0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Footer Action Bar -->
                <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap" style="gap: 12px;">
                    <div class="text-muted small">
                        <i data-feather="info" style="width: 14px; height: 14px;" class="mr-25 text-primary align-middle"></i>
                        When all pending products are received, order status automatically transitions to <span class="badge badge-light-success">Complete</span>.
                    </div>
                    <div class="w-100 w-md-auto text-md-right text-center">
                        <button type="button" class="btn btn-success btn-lg font-weight-bold shadow w-100 w-md-auto px-2" id="btn-process-receive" disabled>
                            <i data-feather="check-circle" class="mr-50"></i> Process Receiving & Generate Payments
                        </button>
                    </div>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 with strict container attachment and custom templates
        $('#manual_select').select2({
            width: '100%',
            dropdownParent: $('#manual_select_container'),
            placeholder: '-- Choose a Confirmed Order with Pending Qty --',
            allowClear: true,
            escapeMarkup: function(m) { return m; },
            templateResult: function(data) {
                if (!data.id) return data.text;
                var opt = $(data.element);
                var invoice = opt.data('invoice') || '';
                var product = opt.data('product') || '';
                var worker = opt.data('worker') || '';
                var pending = opt.data('pending-qty') || 0;
                var rate = opt.data('unit-price') || 0;
                var mfgImg = opt.data('mfg-img') || '';
                var partTypeText = opt.data('part-type-text') || opt.data('part-type') || '';

                return `
                    <div class="d-flex justify-content-between align-items-center py-25">
                        <div class="d-flex align-items-center text-truncate mr-1">
                            ${mfgImg ? `<img src="${mfgImg}" style="width: 34px; height: 34px; object-fit: cover; border-radius: 4px; margin-right: 8px; flex-shrink: 0; border: 1px solid #ebe9f1;">` : ''}
                            <div class="text-truncate">
                                <div class="d-flex align-items-center text-truncate">
                                    <span class="badge badge-light-primary mr-50 font-weight-bold flex-shrink-0">${invoice}</span>
                                    <strong class="text-dark text-truncate">${product}</strong>
                                </div>
                                <small class="text-muted d-block mt-25">
                                    Worker: <strong>${worker}</strong> &bull; Type: <strong>${partTypeText}</strong> &bull; Rate: ৳ ${parseFloat(rate).toFixed(2)}
                                </small>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="badge badge-light-danger font-weight-bold px-50 py-25" style="font-size: 11px;">
                                ${pending} pcs pending
                            </span>
                        </div>
                    </div>
                `;
            },
            templateSelection: function(data) {
                if (!data.id) return data.text;
                var opt = $(data.element);
                var invoice = opt.data('invoice') || '';
                var product = opt.data('product') || '';
                var pending = opt.data('pending-qty') || 0;

                return `
                    <span class="badge badge-light-primary mr-50 font-weight-bold flex-shrink-0">${invoice}</span>
                    <span class="text-dark font-weight-bold text-truncate mr-50">${product}</span>
                    <span class="badge badge-light-danger flex-shrink-0" style="font-size: 11px;">${pending} pending</span>
                `;
            }
        });

        let addedIds = [];

        function renderFeather() {
            if (typeof feather !== 'undefined') {
                feather.replace({ width: 14, height: 14 });
            }
        }

        function calculateTotals() {
            let totalUnits = 0;
            let totalPayment = 0;

            $('.input-receive-qty').each(function() {
                let qty = parseInt($(this).val()) || 0;
                let rate = parseFloat($(this).data('rate')) || 0;
                let rowPayment = qty * rate;

                let rowId = $(this).data('id');
                $('#payment-val-' + rowId).text('৳ ' + rowPayment.toFixed(2));

                totalUnits += qty;
                totalPayment += rowPayment;
            });

            $('#batchTotalUnits').text(totalUnits);
            $('#batchTotalPayment').text('৳ ' + totalPayment.toFixed(2));

            if (addedIds.length > 0 && totalUnits > 0) {
                $('#receiveTableFooter').removeClass('d-none');
                $('#btn-process-receive').prop('disabled', false);
            } else {
                $('#receiveTableFooter').addClass('d-none');
                $('#btn-process-receive').prop('disabled', true);
            }
        }

        function getPartTypeBadge(type) {
            type = (type || '').toLowerCase().trim();
            if (type === 'body' || type === 'body part') {
                return '<span class="badge badge-light-info font-weight-bold">Body Part</span>';
            } else if (type === 'finishing' || type === 'finishing part') {
                return '<span class="badge badge-light-primary font-weight-bold">Finishing Part</span>';
            } else if (type === 'both') {
                return '<span class="badge badge-light-success font-weight-bold">Both</span>';
            }
            let label = type ? (type.charAt(0).toUpperCase() + type.slice(1)) : 'N/A';
            return '<span class="badge badge-light-secondary font-weight-bold">' + label + '</span>';
        }

        function addOrderToTable(id, invoice, product, worker, orderQty, receivedQty, pendingQty, unitPrice, mfgImg, allImages, printUrl, partType) {
            id = id.toString();
            if (addedIds.includes(id)) {
                toastr.warning('Order #' + invoice + ' is already added to the batch list.');
                return;
            }

            if (pendingQty <= 0) {
                toastr.info('Order #' + invoice + ' is already fully received.');
                return;
            }

            mfgImg = mfgImg || "{{ asset('images/no-image.png') }}";
            allImages = allImages || [mfgImg];
            let allImagesJson = typeof allImages === 'string' ? allImages : JSON.stringify(allImages);

            $('#emptyReceiveRow').hide();
            addedIds.push(id);

            let initialPayable = (pendingQty * unitPrice).toFixed(2);
            let partTypeBadge = getPartTypeBadge(partType);

            let tr = `
                <tr id="row-${id}">
                    <td class="text-center font-weight-bold">
                        <span class="badge badge-light-primary font-weight-bold">${invoice}</span>
                    </td>
                    <td class="mfg-image-cell p-0 text-center">
                        <div class="btn-preview-mfg-img" 
                             data-img="${mfgImg}" 
                             data-all-images='${allImagesJson}' 
                             data-title="${$('<div>').text(product).html()}" 
                             data-invoice="${$('<div>').text(invoice).html()}" 
                             data-print-url="${printUrl || ''}" 
                             title="Click to view full blueprint">
                            <img src="${mfgImg}" alt="Blueprint">
                            <div class="mfg-zoom-badge">
                                <i data-feather="zoom-in" style="width: 10px; height: 10px;"></i>
                            </div>
                        </div>
                    </td>
                    <td><strong class="text-dark">${product}</strong></td>
                    <td><strong>${worker}</strong></td>
                    <td class="text-center">${partTypeBadge}</td>
                    <td class="text-center font-weight-bold">${orderQty}</td>
                    <td class="text-center text-success font-weight-bold">${receivedQty}</td>
                    <td class="text-center text-danger font-weight-bold">${pendingQty}</td>
                    <td class="text-center">
                        <input type="number" 
                               class="form-control form-control-sm input-receive-qty shadow-none" 
                               data-id="${id}" 
                               data-rate="${unitPrice}" 
                               data-pending="${pendingQty}" 
                               min="1" 
                               max="${pendingQty}" 
                               value="${pendingQty}">
                    </td>
                    <td class="text-right font-weight-bold">৳ ${unitPrice.toFixed(2)}</td>
                    <td class="text-right text-success font-weight-bolder" id="payment-val-${id}">
                        ৳ ${initialPayable}
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove p-50 rounded" data-id="${id}" title="Remove order from batch">
                            <i data-feather="trash-2"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#receive-table tbody').append(tr);
            renderFeather();
            calculateTotals();
        }

        // Add from manual select
        $('#btn-add-manual').click(function() {
            let option = $('#manual_select').find(':selected');
            let id = option.val();
            
            if (!id) {
                toastr.error('Please select an order from the list first.');
                return;
            }

            let invoice = option.data('invoice');
            let product = option.data('product');
            let worker = option.data('worker');
            let orderQty = parseInt(option.data('order-qty')) || 0;
            let receivedQty = parseInt(option.data('received-qty')) || 0;
            let pendingQty = parseInt(option.data('pending-qty')) || 0;
            let unitPrice = parseFloat(option.data('unit-price')) || 0;
            let mfgImg = option.data('mfg-img');
            let allImages = option.data('all-images');
            let printUrl = option.data('print-url');
            let partType = option.data('part-type') || '';

            addOrderToTable(id, invoice, product, worker, orderQty, receivedQty, pendingQty, unitPrice, mfgImg, allImages, printUrl, partType);
            $('#manual_select').val('').trigger('change');
        });

        // Add from barcode input
        $('#barcode_input').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#btn-add-barcode').click();
            }
        });

        $('#btn-add-barcode').click(function() {
            let query = $('#barcode_input').val().trim();
            if (!query) return;

            let matchedOption = null;
            $('#manual_select option').each(function() {
                let optVal = $(this).val();
                let optInvoice = $(this).data('invoice');
                if (optVal == query || (optInvoice && optInvoice.toLowerCase() === query.toLowerCase())) {
                    matchedOption = $(this);
                    return false;
                }
            });

            if (matchedOption && matchedOption.val()) {
                let id = matchedOption.val();
                let invoice = matchedOption.data('invoice');
                let product = matchedOption.data('product');
                let worker = matchedOption.data('worker');
                let orderQty = parseInt(matchedOption.data('order-qty')) || 0;
                let receivedQty = parseInt(matchedOption.data('received-qty')) || 0;
                let pendingQty = parseInt(matchedOption.data('pending-qty')) || 0;
                let unitPrice = parseFloat(matchedOption.data('unit-price')) || 0;
                let mfgImg = matchedOption.data('mfg-img');
                let allImages = matchedOption.data('all-images');
                let printUrl = matchedOption.data('print-url');
                let partType = matchedOption.data('part-type') || '';

                addOrderToTable(id, invoice, product, worker, orderQty, receivedQty, pendingQty, unitPrice, mfgImg, allImages, printUrl, partType);
                $('#barcode_input').val('').focus();
            } else {
                toastr.error('Order "' + query + '" not found or has no pending units to receive.');
                $('#barcode_input').val('').focus();
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

            renderFeather();
            $('#manufactureImageModal').modal('show');
        });

        $(document).on('click', '.mfg-thumb-nav', function() {
            let src = $(this).attr('src');
            $('#mfgModalImage').attr('src', src);
            $('#mfgModalDownloadBtn').attr('href', src);
            $('.mfg-thumb-nav').removeClass('border-primary').addClass('border-secondary');
            $(this).removeClass('border-secondary').addClass('border-primary');
        });

        // Quantity change recalculation
        $(document).on('input change', '.input-receive-qty', function() {
            let val = parseInt($(this).val()) || 0;
            let max = parseInt($(this).data('pending')) || 1;

            if (val > max) {
                toastr.warning('Quantity cannot exceed pending quantity of ' + max);
                $(this).val(max);
                val = max;
            } else if (val < 1) {
                $(this).val(1);
                val = 1;
            }

            calculateTotals();
        });

        // Remove row
        $(document).on('click', '.btn-remove', function() {
            let id = $(this).data('id').toString();
            addedIds = addedIds.filter(item => item !== id);
            $('#row-' + id).remove();

            if (addedIds.length === 0) {
                $('#emptyReceiveRow').show();
            }

            calculateTotals();
        });

        // Process Receiving
        $('#btn-process-receive').click(function() {
            if (addedIds.length === 0) return;

            let ordersPayload = [];
            let hasError = false;

            $('.input-receive-qty').each(function() {
                let id = $(this).data('id');
                let qty = parseInt($(this).val()) || 0;
                let max = parseInt($(this).data('pending')) || 1;

                if (qty <= 0 || qty > max) {
                    hasError = true;
                    toastr.error('Invalid quantity entered for one of the orders.');
                    return false;
                }

                ordersPayload.push({
                    id: id,
                    receive_qty: qty
                });
            });

            if (hasError || ordersPayload.length === 0) return;

            Swal.fire({
                title: 'Confirm Order Receiving',
                text: "Process receiving for " + ordersPayload.length + " order(s)? Worker payments will be generated automatically.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28c76f',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Yes, receive now!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let btn = $('#btn-process-receive');
                    let originalHtml = btn.html();
                    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing Receiving...');

                    $.ajax({
                        url: "{{ route('admin.manufacture.receive.process') }}",
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}",
                            orders: ordersPayload
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Received Successfully!',
                                text: response.success,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                window.location.reload();
                            }, 1500);
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).html(originalHtml);
                            toastr.error(xhr.responseJSON.error || 'An error occurred while processing receiving.');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
