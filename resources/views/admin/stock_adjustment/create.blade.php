@extends('layouts.admin')
@section('title', 'Create Stock Adjustment')

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Stock Adjustment</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.stock-adjustment.index') }}">Stock Adjustments</a></li>
        <li class="breadcrumb-item active">Create Adjustment</li>
    </ol>
</div>
@endsection

@section('content')
<style>
    /* Premium Page & Card Styles */
    .card-premium {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 20px 0 rgba(34, 41, 47, 0.05);
        margin-bottom: 1.5rem;
        background: #ffffff;
    }
    .card-premium .card-header {
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }
    .card-premium .card-body {
        padding: 1.5rem;
    }

    /* Interactive Type Radio Cards */
    .type-card-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    @media (max-width: 768px) {
        .type-card-grid {
            grid-template-columns: 1fr;
        }
    }
    .type-radio-card {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
    }
    .type-radio-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        transform: translateY(-2px);
    }
    .type-radio-card.active-addition {
        border-color: #28c76f !important;
        background: #f0fdf4 !important;
        box-shadow: 0 4px 14px rgba(40, 199, 111, 0.18);
    }
    .type-radio-card.active-normal {
        border-color: #00cfe8 !important;
        background: #f0fbfc !important;
        box-shadow: 0 4px 14px rgba(0, 207, 232, 0.18);
    }
    .type-radio-card.active-abnormal {
        border-color: #ea5455 !important;
        background: #fef2f2 !important;
        box-shadow: 0 4px 14px rgba(234, 84, 85, 0.18);
    }

    .type-icon-wrapper {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 8px;
    }

    /* Live Preview Ticker */
    .preview-card-sticky {
        position: sticky;
        top: 90px;
    }
    .metric-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    .metric-item:last-child {
        border-bottom: none;
    }

    /* Quick Fill Reason Tags */
    .reason-pill {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        background: #f1f5f9;
        color: #475569;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        margin-right: 6px;
        margin-bottom: 6px;
        border: 1px solid #e2e8f0;
        transition: all 0.15s ease-in-out;
    }
    .reason-pill:hover {
        background: #e2e8f0;
        color: #0f172a;
        border-color: #cbd5e1;
        transform: scale(1.02);
    }

    /* Select2 Tweaks */
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d8d6de !important;
        border-radius: 6px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        padding-left: 10px !important;
        font-weight: 500;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px !important;
        top: 0 !important;
    }
</style>

<!-- Top Header Row -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
    <div>
        <h3 class="font-weight-bolder text-dark mb-25">Create Stock Adjustment</h3>
        <p class="text-muted mb-0 small">Adjust inventory levels for surplus, physical exchange, or damaged scrap goods.</p>
    </div>
    <div class="mt-1 mt-sm-0">
        <a href="{{ route('admin.stock-adjustment.index') }}" class="btn btn-outline-secondary">
            <i data-feather="arrow-left" class="mr-50"></i> Back to Adjustment List
        </a>
    </div>
</div>

<form id="adjustmentForm">
    @csrf
    <!-- Hidden input bound to visual cards -->
    <input type="hidden" name="type" id="adjustmentType" value="addition">

    <div class="row">
        <!-- Main Form Column (8 Cols) -->
        <div class="col-lg-8 col-12">
            <!-- 1. Product Selection -->
            <div class="card card-premium">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="font-weight-bolder font-medium-1 text-dark">
                        <i data-feather="box" class="mr-50 text-primary"></i> 1. Select Product to Adjust
                    </span>
                    <span class="badge badge-light-primary font-small-2" id="productCountBadge">
                        {{ $products->count() }} Products Available
                    </span>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label class="font-weight-bold" for="productId">Target Product <span class="text-danger">*</span></label>
                        <select name="product_id" id="productId" class="form-control select2" required>
                            <option value="">-- Search & Choose Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}"
                                        data-stock="{{ (float) $product->stock }}"
                                        data-name="{{ $product->name }}"
                                        data-is-mfg="{{ $product->is_manufacturer ? '1' : '0' }}">
                                    {{ $product->name }} {{ $product->is_manufacturer ? ' [Manufacture Product]' : ' [Regular Inventory]' }} (Stock: {{ number_format($product->stock, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted mt-50 d-block">Search by product name to immediately view current in-stock balance and details.</small>
                    </div>
                </div>
            </div>

            <!-- 2. Adjustment Nature (Visual Cards) -->
            <div class="card card-premium">
                <div class="card-header">
                    <span class="font-weight-bolder font-medium-1 text-dark">
                        <i data-feather="sliders" class="mr-50 text-primary"></i> 2. Adjustment Type
                    </span>
                </div>
                <div class="card-body">
                    <div class="type-card-grid">
                        <!-- Addition Card -->
                        <div class="type-radio-card active-addition" data-type="addition" id="cardAddition">
                            <div>
                                <div class="type-icon-wrapper bg-light-success text-success">
                                    <i data-feather="plus-circle"></i>
                                </div>
                                <h6 class="font-weight-bolder mb-25 text-dark">Addition (+)</h6>
                                <p class="text-muted font-small-2 mb-0">Surplus / Found / Return</p>
                            </div>
                            <div class="mt-1 pt-75 border-top">
                                <span class="badge badge-light-success font-small-1">Increases In-Stock</span>
                            </div>
                        </div>

                        <!-- Normal (Exchange) Card -->
                        <div class="type-radio-card" data-type="normal" id="cardNormal">
                            <div>
                                <div class="type-icon-wrapper bg-light-info text-info">
                                    <i data-feather="repeat"></i>
                                </div>
                                <h6 class="font-weight-bolder mb-25 text-dark">Exchange (-)</h6>
                                <p class="text-muted font-small-2 mb-0">Temporary Normal Out</p>
                            </div>
                            <div class="mt-1 pt-75 border-top">
                                <span class="badge badge-light-info font-small-1">Receivable Later</span>
                            </div>
                        </div>

                        <!-- Abnormal (Damage) Card -->
                        <div class="type-radio-card" data-type="abnormal" id="cardAbnormal">
                            <div>
                                <div class="type-icon-wrapper bg-light-danger text-danger">
                                    <i data-feather="alert-triangle"></i>
                                </div>
                                <h6 class="font-weight-bolder mb-25 text-dark">Damage / Scrap (-)</h6>
                                <p class="text-muted font-small-2 mb-0">Permanent Abnormal Out</p>
                            </div>
                            <div class="mt-1 pt-75 border-top">
                                <span class="badge badge-light-danger font-small-1">Permanent Deduction</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Quantity & Date -->
            <div class="card card-premium">
                <div class="card-header">
                    <span class="font-weight-bolder font-medium-1 text-dark">
                        <i data-feather="edit-3" class="mr-50 text-primary"></i> 3. Quantity & Date
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="font-weight-bold" id="qtyInputLabel" for="quantity">Quantity to Add (+) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bolder text-success" id="qtySignPrefix">+</span>
                                    </div>
                                    <input type="number" name="quantity" id="quantity" class="form-control font-weight-bolder" step="0.01" min="0.01" placeholder="Enter quantity..." required>
                                    <div class="input-group-append">
                                        <span class="input-group-text font-small-2">Units</span>
                                    </div>
                                </div>
                                <small class="text-muted mt-50 d-block" id="qtyHelper">Enter the exact quantity to adjust.</small>
                            </div>
                        </div>

                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="font-weight-bold" for="adjustmentDate">Adjustment Date <span class="text-danger">*</span></label>
                                <input type="date" name="adjustment_date" id="adjustmentDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                                <small class="text-muted mt-50 d-block">Transaction date for stock ledger reporting.</small>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Reason & Quick Fill Tags -->
                    <div class="form-group mb-0 mt-1">
                        <label class="font-weight-bold" for="reason">Reason / Reference Note</label>
                        <div class="mb-50">
                            <span class="font-small-2 text-muted mr-50">Quick Fill:</span>
                            <span class="reason-pill" data-text="Annual physical inventory audit count surplus">Physical Audit Surplus</span>
                            <span class="reason-pill" data-text="Damaged in transit during delivery">Damaged in Transit</span>
                            <span class="reason-pill" data-text="Defective product sent to scrap">Scrap / Defective</span>
                            <span class="reason-pill" data-text="Customer replacement exchange product">Customer Exchange</span>
                            <span class="reason-pill" data-text="Found extra units during warehouse reorganizing">Found in Warehouse</span>
                        </div>
                        <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Provide details explaining this adjustment..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Summary & Live Impact (4 Cols) -->
        <div class="col-lg-4 col-12">
            <div class="card card-premium preview-card-sticky">
                <div class="card-header bg-light-primary py-1">
                    <span class="font-weight-bolder text-primary">
                        <i data-feather="activity" class="mr-50"></i> Live Inventory Preview
                    </span>
                </div>
                <div class="card-body">
                    <!-- Selected Product Overview -->
                    <div class="p-1 mb-1 rounded bg-light border">
                        <span class="text-muted font-small-2 text-uppercase font-weight-bold d-block mb-25">Selected Product</span>
                        <h6 class="font-weight-bolder text-dark mb-25" id="previewProductName">None Selected</h6>
                        <span class="badge badge-light-secondary font-small-1" id="previewProductType">--</span>
                    </div>

                    <!-- Metrics Breakdown -->
                    <div class="metric-item">
                        <span class="text-muted font-small-3">Current In-Stock</span>
                        <span class="font-weight-bolder text-dark font-medium-1" id="previewCurrentStock">0.00 Units</span>
                    </div>

                    <div class="metric-item">
                        <span class="text-muted font-small-3">Adjustment Delta</span>
                        <span class="font-weight-bolder text-success font-medium-1" id="previewDelta">+ 0.00 Units</span>
                    </div>

                    <div class="metric-item bg-light p-1 rounded my-1 border">
                        <div>
                            <span class="text-dark font-small-3 font-weight-bolder d-block">Projected New Stock</span>
                            <small class="text-muted font-small-1" id="previewDeltaSummary">After applying this transaction</small>
                        </div>
                        <span class="badge badge-pill badge-light-success font-medium-2 px-1 py-50 font-weight-bolder" id="previewNewStock">
                            0.00 Units
                        </span>
                    </div>

                    <!-- Dynamic Notice Box -->
                    <div class="alert alert-success p-1 mb-2 font-small-3" id="previewAlertBox" role="alert">
                        <i data-feather="check-circle" class="mr-50"></i>
                        <span id="previewAlertText">Addition (+) immediately increases inventory in-stock balance and logs an audit ledger entry.</span>
                    </div>

                    <!-- Action Buttons -->
                    <button type="submit" class="btn btn-primary btn-block py-75 font-weight-bold" id="saveBtn">
                        <i data-feather="check" class="mr-50"></i> Save Stock Adjustment
                    </button>
                    <a href="{{ route('admin.stock-adjustment.index') }}" class="btn btn-outline-secondary btn-block mt-50">
                        Cancel & Return
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if (feather) {
            feather.replace({ width: 14, height: 14 });
        }

        // Initialize Select2 with clear placeholder
        $('#productId').select2({
            placeholder: "-- Search & Choose Product --",
            allowClear: true,
            width: '100%'
        });

        // Quick fill reason pills
        $('.reason-pill').on('click', function() {
            var text = $(this).data('text');
            var current = $('#reason').val().trim();
            if (current === '') {
                $('#reason').val(text);
            } else {
                $('#reason').val(current + ' | ' + text);
            }
        });

        // Visual Radio Cards Selection
        $('.type-radio-card').on('click', function() {
            var type = $(this).data('type');
            $('#adjustmentType').val(type);

            $('.type-radio-card').removeClass('active-addition active-normal active-abnormal');

            if (type === 'addition') {
                $(this).addClass('active-addition');
                $('#qtyInputLabel').html('Quantity to Add (+) <span class="text-danger">*</span>');
                $('#qtySignPrefix').removeClass('text-danger text-info').addClass('text-success').text('+');
                $('#qtyHelper').text('Units to increase into in-stock inventory.');
                $('#previewAlertBox').removeClass('alert-danger alert-info').addClass('alert-success');
                $('#previewAlertText').html('<strong>Addition (+):</strong> Immediately adds stock to inventory and records incoming audit ledger.');
            } else if (type === 'normal') {
                $(this).addClass('active-normal');
                $('#qtyInputLabel').html('Quantity to Deduct (Exchange) (-) <span class="text-danger">*</span>');
                $('#qtySignPrefix').removeClass('text-success text-danger').addClass('text-info').text('-');
                $('#qtyHelper').text('Temporary deduction. You can receive the item back anytime from the list.');
                $('#previewAlertBox').removeClass('alert-success alert-danger').addClass('alert-info');
                $('#previewAlertText').html('<strong>Normal Exchange (-):</strong> Deducts stock now and creates a pending receivable item for replacement.');
            } else {
                $(this).addClass('active-abnormal');
                $('#qtyInputLabel').html('Quantity to Deduct (Damage/Scrap) (-) <span class="text-danger">*</span>');
                $('#qtySignPrefix').removeClass('text-success text-info').addClass('text-danger').text('-');
                $('#qtyHelper').text('Permanent removal of damaged, expired, or lost goods.');
                $('#previewAlertBox').removeClass('alert-success alert-info').addClass('alert-danger');
                $('#previewAlertText').html('<strong>Abnormal Damage (-):</strong> Permanently write off damaged or scrapped goods from stock.');
            }

            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
            updateLivePreview();
        });

        function updateLivePreview() {
            var selectedOption = $('#productId option:selected');
            var productName = selectedOption.data('name') || '';
            var isMfg = selectedOption.data('is-mfg');
            var currentStock = parseFloat(selectedOption.data('stock')) || 0;
            var type = $('#adjustmentType').val();
            var qty = parseFloat($('#quantity').val()) || 0;

            if (productName) {
                $('#previewProductName').text(productName);
                if (isMfg == '1') {
                    $('#previewProductType').removeClass('badge-light-secondary').addClass('badge-light-primary').text('Manufacture Product');
                } else {
                    $('#previewProductType').removeClass('badge-light-primary').addClass('badge-light-secondary').text('Regular Inventory');
                }
                $('#previewCurrentStock').text(currentStock.toFixed(2) + ' Units');
            } else {
                $('#previewProductName').text('None Selected');
                $('#previewProductType').removeClass('badge-light-primary').addClass('badge-light-secondary').text('--');
                $('#previewCurrentStock').text('0.00 Units');
            }

            if (type === 'addition') {
                var newStock = currentStock + qty;
                $('#previewDelta').removeClass('text-danger text-info').addClass('text-success').text('+ ' + qty.toFixed(2) + ' Units');
                $('#previewNewStock').removeClass('badge-light-danger badge-light-info badge-light-warning').addClass('badge-light-success').text(newStock.toFixed(2) + ' Units');
                $('#quantity').removeAttr('max');
            } else {
                var newStock = currentStock - qty;
                var signColor = type === 'normal' ? 'text-info' : 'text-danger';
                $('#previewDelta').removeClass('text-success text-info text-danger').addClass(signColor).text('- ' + qty.toFixed(2) + ' Units');

                if (newStock < 0) {
                    $('#previewNewStock').removeClass('badge-light-success badge-light-info badge-light-warning').addClass('badge-light-danger').text('Exceeds Stock (' + currentStock.toFixed(2) + ' max)');
                } else {
                    var badgeClass = type === 'normal' ? 'badge-light-info' : 'badge-light-warning';
                    $('#previewNewStock').removeClass('badge-light-success badge-light-danger badge-light-info badge-light-warning').addClass(badgeClass).text(newStock.toFixed(2) + ' Units');
                }
                $('#quantity').attr('max', currentStock);
            }
        }

        $('#productId').on('change', function() {
            updateLivePreview();
        });

        $('#quantity').on('input change', function() {
            updateLivePreview();
        });

        // Form Submit
        $('#adjustmentForm').on('submit', function(e) {
            e.preventDefault();

            var productId = $('#productId').val();
            if (!productId) {
                toastr.warning('Please select a product first.');
                $('#productId').select2('open');
                return;
            }

            var type = $('#adjustmentType').val();
            var quantity = parseFloat($('#quantity').val()) || 0;
            var currentStock = parseFloat($('#productId option:selected').data('stock')) || 0;

            if (quantity <= 0) {
                toastr.warning('Please enter a valid quantity greater than zero.');
                $('#quantity').focus();
                return;
            }

            if (type !== 'addition' && quantity > currentStock) {
                toastr.error('Cannot deduct ' + quantity.toFixed(2) + ' units. Available stock is only ' + currentStock.toFixed(2) + '.');
                return;
            }

            var confirmText = '';
            var confirmIcon = 'warning';
            var confirmBtnClass = 'btn btn-primary';

            if (type === 'addition') {
                confirmText = 'This will immediately add +' + quantity.toFixed(2) + ' units to the inventory stock.';
                confirmIcon = 'success';
                confirmBtnClass = 'btn btn-success';
            } else if (type === 'normal') {
                confirmText = 'This will deduct -' + quantity.toFixed(2) + ' units for exchange. You can receive it back later.';
                confirmIcon = 'info';
                confirmBtnClass = 'btn btn-info';
            } else {
                confirmText = 'This will permanently deduct -' + quantity.toFixed(2) + ' units for damage/loss.';
                confirmIcon = 'warning';
                confirmBtnClass = 'btn btn-danger';
            }

            Swal.fire({
                title: 'Confirm Stock Adjustment?',
                text: confirmText,
                icon: confirmIcon,
                showCancelButton: true,
                confirmButtonText: 'Yes, Confirm Adjustment',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: confirmBtnClass,
                    cancelButton: 'btn btn-outline-secondary ml-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $('#saveBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...').attr('disabled', true);

                $.ajax({
                    url: "{{ route('admin.stock-adjustment.store') }}",
                    type: "POST",
                    data: $('#adjustmentForm').serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        toastr.success(response.success || 'Stock adjustment saved successfully.');
                        setTimeout(function() {
                            window.location.href = "{{ route('admin.stock-adjustment.index') }}";
                        }, 500);
                    },
                    error: function(xhr) {
                        $('#saveBtn').html('<i data-feather="check" class="mr-50"></i> Save Stock Adjustment').attr('disabled', false);
                        if (feather) {
                            feather.replace({ width: 14, height: 14 });
                        }

                        var errorMsg = 'Failed to save stock adjustment.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.error) {
                                errorMsg = xhr.responseJSON.error;
                            } else if (xhr.responseJSON.message) {
                                errorMsg = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                var firstKey = Object.keys(xhr.responseJSON.errors)[0];
                                errorMsg = xhr.responseJSON.errors[firstKey][0];
                            }
                        }
                        toastr.error(errorMsg);
                    }
                });
            });
        });
    });
</script>
@endpush
