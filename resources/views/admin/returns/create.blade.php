@extends('layouts.admin')
@section('title', 'Create Return Order')

@push('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/app-assets/vendors/css/forms/select/select2.min.css') }}">
<style>
    .return-card {
        background-color: #FDFAF5;
        border: 1px solid #efeae2;
        border-radius: 10px;
    }
    .step-badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background-color: #111C38;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        margin-right: 8px;
    }
    .product-return-table {
        border-collapse: separate !important;
        border-spacing: 0 !important;
    }
    .product-return-table thead th {
        background-color: #111C38 !important;
        color: #ffffff !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        border: none !important;
        padding: 12px 10px !important;
        vertical-align: middle !important;
        letter-spacing: 0.3px;
    }
    .product-return-table thead tr th:first-child {
        border-top-left-radius: 8px;
        padding-left: 16px !important;
    }
    .product-return-table thead tr th:last-child {
        border-top-right-radius: 8px;
        padding-right: 16px !important;
    }
    .product-return-table tbody td {
        font-size: 13px !important;
        vertical-align: middle !important;
        padding: 10px 10px !important;
        border-bottom: 1px solid #f1f5f9;
    }
    .product-return-table tbody tr td:first-child {
        padding-left: 16px !important;
    }
    .product-return-table tbody tr td:last-child {
        padding-right: 16px !important;
    }
    .channel-select-btn {
        cursor: pointer;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #111C38;
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.15s ease;
        display: inline-flex;
        align-items: center;
    }
    .channel-select-btn.active {
        background: #111C38 !important;
        color: #ffffff !important;
        border-color: #111C38 !important;
    }
    /* Select2 Single Select Styling */
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1px solid #d8d6de !important;
        border-radius: 6px !important;
        display: flex !important;
        align-items: center !important;
        background-color: #ffffff !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: #111C38 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        padding-left: 12px !important;
        padding-right: 32px !important;
        color: #2d3748 !important;
        font-weight: 500;
        font-size: 13.5px;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        top: 0 !important;
        right: 10px !important;
        width: 20px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236e6b7b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-chevron-down'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: center !important;
        background-size: 16px 14px !important;
        border: none !important;
        border-style: none !important;
        border-width: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 16px !important;
        height: 16px !important;
        position: absolute !important;
        left: auto !important;
        right: 2px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        transition: transform 0.2s ease !important;
    }
    .select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b {
        transform: translateY(-50%) rotate(180deg) !important;
        border: none !important;
    }

    /* Select2 Multi-Select Styling */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d8d6de !important;
        border-radius: 6px !important;
        min-height: 38px !important;
        padding: 2px 6px !important;
        background-color: #ffffff !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #111C38 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #111C38 !important;
        border: 1px solid #111C38 !important;
        color: #ffffff !important;
        border-radius: 4px !important;
        padding: 3px 10px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        margin-top: 4px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #ffffff !important;
        margin-right: 6px !important;
        font-weight: bold !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #ff9f43 !important;
    }
    .product-row.row-unselected {
        opacity: 0.45 !important;
        background-color: #f8fafc !important;
    }
    .unit-price-display, .subtotal-display {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
</style>
@endpush

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Create Return</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.returns.index') }}">Return Orders</a></li>
        <li class="breadcrumb-item active">New Return</li>
    </ol>
</div>
@endsection

@section('content')
<div class="content-body">
    <form action="{{ route('admin.returns.store') }}" method="POST" id="createReturnForm">
        @csrf

        <!-- Top Header Card -->
        <div class="card return-card mb-2 shadow-none">
            <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <span class="fieldora-title-badge me-2" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; background-color: #111C38; color: #ffffff; border-radius: 8px; margin-right: 12px;">
                        <i data-feather="corner-down-left" style="width: 20px; height: 20px; stroke: #ffffff;"></i>
                    </span>
                    <div>
                        <h4 class="mb-0 font-weight-bolder" style="color: #111C38;">New Return Request</h4>
                        <small class="text-muted">Select channel, choose multiple invoices, pick items, and enter return notes</small>
                    </div>
                </div>
                <div>
                    <a href="{{ route('admin.returns.index') }}" class="btn btn-sm btn-outline-secondary" style="height: 36px; border-radius: 6px; font-weight: 600;">
                        <i data-feather="arrow-left" style="width: 13px; height: 13px; margin-right: 4px;"></i> Back to Returns
                    </a>
                </div>
            </div>
        </div>

        <!-- Step 1: Channel & Party Selection -->
        <div class="card return-card mb-2 shadow-none">
            <div class="card-header border-bottom py-1 px-3">
                <h5 class="card-title mb-0 font-weight-bolder d-flex align-items-center" style="color: #111C38;">
                    <span class="step-badge">1</span> Sales Channel &amp; Party Selection
                </h5>
            </div>
            <div class="card-body p-3">
                <div class="row gy-2">
                    <!-- Channel Selector -->
                    <div class="col-md-12 mb-1">
                        <label class="form-label font-weight-bold text-dark font-small-3 d-block mb-50">Select Sales Channel:</label>
                        <div class="d-flex flex-wrap" style="gap: 10px;">
                            <label class="channel-select-btn active" id="btnChannelDealer">
                                <input type="radio" name="channel" value="dealer" class="d-none" checked>
                                <i data-feather="package" style="width: 15px; height: 15px; margin-right: 6px;"></i> Dealer Order
                            </label>
                            <label class="channel-select-btn" id="btnChannelPos">
                                <input type="radio" name="channel" value="pos" class="d-none">
                                <i data-feather="shopping-bag" style="width: 15px; height: 15px; margin-right: 6px;"></i> POS Sale
                            </label>
                            <label class="channel-select-btn" id="btnChannelWeb">
                                <input type="radio" name="channel" value="web" class="d-none">
                                <i data-feather="globe" style="width: 15px; height: 15px; margin-right: 6px;"></i> Web Order
                            </label>
                        </div>
                    </div>

                    <!-- Party Dropdown (Dealer / POS Customer / Web Customer) -->
                    <div class="col-md-6 mb-1" id="dealerSelectContainer">
                        <label class="form-label font-weight-bold text-dark font-small-3">Select Dealer: <span class="text-danger">*</span></label>
                        <select name="party_id_dealer" id="dealerIdSelect" class="form-control select2 w-100">
                            <option value="">-- Choose Dealer --</option>
                            @foreach($dealers as $dlr)
                                <option value="{{ $dlr->id }}">{{ $dlr->name }} @if($dlr->shop_name)({{ $dlr->shop_name }})@endif - {{ $dlr->phone }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-1 d-none" id="customerSelectContainer">
                        <label class="form-label font-weight-bold text-dark font-small-3">Select POS Customer: <span class="text-danger">*</span></label>
                        <select name="party_id_customer" id="customerIdSelect" class="form-control select2 w-100">
                            <option value="">-- Choose Customer --</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->phone ?? 'No Phone' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-1 d-none" id="webUserSelectContainer">
                        <label class="form-label font-weight-bold text-dark font-small-3">Select Web Customer: <span class="text-danger">*</span></label>
                        <select name="party_id_web" id="webUserIdSelect" class="form-control select2 w-100">
                            <option value="">-- Choose Web Customer --</option>
                            @foreach($webUsers as $usr)
                                <option value="{{ $usr->id }}">{{ $usr->name }} ({{ $usr->phone ?: $usr->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="party_id" id="hiddenPartyId" value="">

                    <!-- Return Date -->
                    <div class="col-md-6 mb-1">
                        <label class="form-label font-weight-bold text-dark font-small-3">Return Date: <span class="text-danger">*</span></label>
                        <input type="date" name="return_date" class="form-control" value="{{ date('Y-m-d') }}" style="height: 38px; border-radius: 6px;" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Multi-Invoice Selection Dropdown -->
        <div class="card return-card mb-2 shadow-none">
            <div class="card-header border-bottom py-1 px-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 font-weight-bolder d-flex align-items-center" style="color: #111C38;">
                    <span class="step-badge">2</span> Reference Invoices (Select Multiple)
                </h5>
                <span class="text-muted font-small-2" id="invoicesSummaryText">Select delivered invoices from dropdown</span>
            </div>
            <div class="card-body p-3">
                <div id="invoicesLoadingSpinner" class="text-center py-2 d-none">
                    <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                    <span class="ms-1 font-small-2 text-muted">Loading party's invoices...</span>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-9 col-12 mb-1 mb-md-0">
                        <label class="form-label font-weight-bold text-dark font-small-3 mb-25">Delivered Invoices: <span class="text-danger">*</span></label>
                        <select name="selected_invoices[]" id="invoicesSelectDropdown" class="form-control select2 w-100" multiple="multiple" data-placeholder="Click to select one or multiple delivered invoices...">
                        </select>
                    </div>
                    <div class="col-md-3 col-12 d-flex align-items-center" style="gap: 8px; margin-top: 22px;">
                        <button type="button" class="btn btn-sm btn-outline-primary flex-grow-1 d-inline-flex align-items-center justify-content-center" id="btnSelectAllInvoices" style="height: 38px; border-radius: 6px; font-weight: 600; font-size: 12px;">
                            <i data-feather="check-square" style="width: 13px; height: 13px; margin-right: 4px;"></i> Select All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center justify-content-center" id="btnClearInvoices" style="height: 38px; border-radius: 6px; font-weight: 600; font-size: 12px; padding: 0 12px;" title="Clear Selected Invoices">
                            <i data-feather="x" style="width: 14px; height: 14px;"></i>
                        </button>
                    </div>
                </div>

                <div class="mt-1" id="invoicesHelpText">
                    <small class="text-muted font-small-2">
                        <i data-feather="info" style="width: 12px; height: 12px; margin-right: 3px;"></i>
                        You can select multiple invoices for this party. Products from all selected invoices will appear in Step 3 below.
                    </small>
                </div>
            </div>
        </div>

        <!-- Step 3: Product Line Items Selection & Item Notes -->
        <div class="card return-card mb-2 shadow-none">
            <div class="card-header border-bottom py-1 px-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 font-weight-bolder d-flex align-items-center" style="color: #111C38;">
                    <span class="step-badge">3</span> Products to Return &amp; Item Notes
                </h5>
                <span class="text-muted font-small-2" id="productsSummaryText">Check invoices above to view product items</span>
            </div>
            <div class="card-body p-0">
                <div id="productsLoadingSpinner" class="text-center py-3 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                    <span class="ms-2 font-small-2 text-muted">Fetching invoice products...</span>
                </div>

                <div class="table-responsive">
                    <table class="table product-return-table w-100 mb-0" id="productsTable">
                        <thead>
                            <tr>
                                <th style="width: 52px; text-align: center; vertical-align: middle;">
                                    <div class="d-flex justify-content-center align-items-center m-0">
                                        <input type="checkbox" id="checkAllProducts" class="form-check-input m-0" style="width: 17px; height: 17px; cursor: pointer;" checked>
                                    </div>
                                </th>
                                <th style="min-width: 220px;">PRODUCT</th>
                                <th style="width: 130px;" class="text-center">INVOICE</th>
                                <th style="width: 85px;" class="text-center">DELIVERED</th>
                                <th style="width: 85px;" class="text-center">PREV. RET.</th>
                                <th style="width: 85px;" class="text-center">REMAINING</th>
                                <th style="width: 105px;" class="text-center">RETURN QTY</th>
                                <th style="width: 115px;" class="text-end">UNIT PRICE</th>
                                <th style="width: 115px;" class="text-end">SUBTOTAL</th>
                                <th style="width: 110px;" class="text-center">CONDITION</th>
                                <th style="width: 75px;" class="text-center">RESTOCK</th>
                                <th style="min-width: 180px;">ITEM NOTE / REASON</th>
                            </tr>
                        </thead>
                        <tbody id="productsTableBody">
                            <tr>
                                <td colspan="12" class="text-center py-4 text-muted">
                                    <i data-feather="box" style="width: 28px; height: 28px; margin-bottom: 6px;"></i>
                                    <p class="mb-0 font-small-3">No products available. Please select one or more invoices from the dropdown above.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Step 4: Master Return Note & Refund Setup -->
        <div class="row">
            <div class="col-md-7 mb-2">
                <div class="card return-card shadow-none h-100">
                    <div class="card-header border-bottom py-1 px-3">
                        <h5 class="card-title mb-0 font-weight-bolder d-flex align-items-center" style="color: #111C38;">
                            <span class="step-badge">4</span> Master Return Note &amp; Remarks
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <label class="form-label font-weight-bold text-dark font-small-3 mb-50">
                            <i data-feather="file-text" style="width: 14px; height: 14px; margin-right: 4px;"></i> Return Note / Customer Explanation:
                        </label>
                        <textarea name="return_note" id="returnNoteInput" class="form-control" rows="4" placeholder="Enter general return reason, dealer agreement details, or authorizing staff remarks (e.g. Returned batch #401 due to defect; authorized by Admin)..." style="border-radius: 8px; font-size: 13px;"></textarea>
                        <small class="text-muted font-small-1 d-block mt-50">
                            <i data-feather="info" style="width: 11px; height: 11px; margin-right: 3px;"></i>
                            This note will be attached to the return record and automatically logged into the dealer wallet transaction history when approved.
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-5 mb-2">
                <div class="card return-card shadow-none h-100">
                    <div class="card-header border-bottom py-1 px-3">
                        <h5 class="card-title mb-0 font-weight-bolder" style="color: #111C38;">
                            <i data-feather="dollar-sign" class="me-1"></i> Refund &amp; Financial Summary
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <!-- Subtotal -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted font-small-3 font-weight-bold">Items Subtotal:</span>
                            <span class="font-weight-bolder text-dark font-medium-1 subtotal-display" id="summarySubtotal">৳ 0.00</span>
                        </div>

                        <!-- Deduction / Restocking Fee -->
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted font-small-3 font-weight-bold">Deduction / Fee:</span>
                            <div style="width: 130px;">
                                <input type="number" step="0.01" min="0" name="deduction_amount" id="deductionInput" class="form-control form-control-sm text-end" value="0.00" style="height: 32px; border-radius: 6px; font-weight: 600;">
                            </div>
                        </div>

                        <hr class="my-1">

                        <!-- Net Refund Amount -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-weight-bolder text-dark font-small-3 text-uppercase">Net Refund Amount:</span>
                            <span class="font-weight-bolder text-success font-medium-3 subtotal-display" id="summaryNetRefund">৳ 0.00</span>
                        </div>

                        <!-- Refund Type Selection -->
                        <div class="mb-2">
                            <label class="form-label font-weight-bold text-dark font-small-3 d-block mb-50">Refund Method: <span class="text-danger">*</span></label>
                            
                            <div class="form-check form-check-inline me-2" id="refundTypeWalletContainer">
                                <input class="form-check-input" type="radio" name="refund_type" id="refundTypeWallet" value="wallet" checked>
                                <label class="form-check-label font-weight-bold text-dark font-small-3" for="refundTypeWallet">
                                    <i data-feather="credit-card" style="width: 14px; height: 14px; color: #512DA8; margin-right: 3px;"></i> Dealer Wallet
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="refund_type" id="refundTypeCash" value="cash">
                                <label class="form-check-label font-weight-bold text-dark font-small-3" for="refundTypeCash">
                                    <i data-feather="dollar-sign" style="width: 14px; height: 14px; color: #004D40; margin-right: 3px;"></i> Cash Refund
                                </label>
                            </div>

                            <div id="walletHelpNotice" class="alert alert-light-primary p-1 mt-1 mb-0 font-small-1" style="background-color: #f3f0fc; color: #512DA8; border: 1px solid #dcd3f8; border-radius: 6px;">
                                <i data-feather="shield" style="width: 12px; height: 12px; margin-right: 3px;"></i>
                                Wallet funds will be credited to the dealer's wallet balance upon admin approval.
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 py-1" id="btnSubmitReturn" style="background-color: #111C38 !important; border-color: #111C38 !important; border-radius: 8px; font-weight: 700; font-size: 14px;">
                            <i data-feather="check" style="width: 16px; height: 16px; margin-right: 6px;"></i> Submit Return (Pending Approval)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{ asset('admin/app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script>
$(document).ready(function() {
    let currentChannel = 'dealer';
    let availableProducts = [];

    // Initialize Select2 dropdowns
    $('#dealerIdSelect').select2({
        placeholder: "-- Choose Dealer --",
        width: '100%'
    });

    $('#customerIdSelect').select2({
        placeholder: "-- Choose POS Customer --",
        width: '100%'
    });

    $('#webUserIdSelect').select2({
        placeholder: "-- Choose Web Customer --",
        width: '100%'
    });

    let invoicesSelect2 = $('#invoicesSelectDropdown').select2({
        placeholder: "Click to select one or multiple delivered invoices...",
        allowClear: true,
        width: '100%',
        closeOnSelect: false
    });

    // Initial load if dealer is pre-selected
    if ($('#dealerIdSelect').val()) {
        $('#hiddenPartyId').val($('#dealerIdSelect').val());
        triggerInvoiceLoad();
    }

    // Channel Selection Click
    $('.channel-select-btn').on('click', function() {
        $('.channel-select-btn').removeClass('active');
        $(this).addClass('active');
        currentChannel = $(this).find('input[type="radio"]').val();

        if (currentChannel === 'dealer') {
            $('#dealerSelectContainer').removeClass('d-none');
            $('#customerSelectContainer').addClass('d-none');
            $('#webUserSelectContainer').addClass('d-none');
            $('#refundTypeWalletContainer').show();
            $('#refundTypeWallet').prop('checked', true);
            $('#walletHelpNotice').show();
            $('#hiddenPartyId').val($('#dealerIdSelect').val());
        } else if (currentChannel === 'pos') {
            $('#dealerSelectContainer').addClass('d-none');
            $('#customerSelectContainer').removeClass('d-none');
            $('#webUserSelectContainer').addClass('d-none');
            $('#refundTypeWalletContainer').hide();
            $('#refundTypeCash').prop('checked', true);
            $('#walletHelpNotice').hide();
            $('#hiddenPartyId').val($('#customerIdSelect').val());
        } else {
            $('#dealerSelectContainer').addClass('d-none');
            $('#customerSelectContainer').addClass('d-none');
            $('#webUserSelectContainer').removeClass('d-none');
            $('#refundTypeWalletContainer').hide();
            $('#refundTypeCash').prop('checked', true);
            $('#walletHelpNotice').hide();
            $('#hiddenPartyId').val($('#webUserIdSelect').val());
        }

        // Ensure full width for select2 containers
        setTimeout(function() {
            $('.select2-container').css('width', '100%');
        }, 10);

        resetInvoicesAndProducts();
        triggerInvoiceLoad();
    });

    // Party Change Handlers
    $('#dealerIdSelect').on('change', function() {
        if (currentChannel === 'dealer') {
            $('#hiddenPartyId').val($(this).val());
            triggerInvoiceLoad();
        }
    });

    $('#customerIdSelect').on('change', function() {
        if (currentChannel === 'pos') {
            $('#hiddenPartyId').val($(this).val());
            triggerInvoiceLoad();
        }
    });

    $('#webUserIdSelect').on('change', function() {
        if (currentChannel === 'web') {
            $('#hiddenPartyId').val($(this).val());
            triggerInvoiceLoad();
        }
    });

    function resetInvoicesAndProducts() {
        $('#invoicesSelectDropdown').empty().trigger('change.select2');
        resetProductsTable();
    }

    function resetProductsTable() {
        $('#productsTableBody').html('<tr><td colspan="12" class="text-center py-4 text-muted"><i data-feather="box" style="width: 28px; height: 28px; margin-bottom: 6px;"></i><p class="mb-0 font-small-3">No products available. Please select one or more invoices from the dropdown above.</p></td></tr>');
        availableProducts = [];
        calculateTotals();
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    // Trigger AJAX Invoice Loader
    function triggerInvoiceLoad() {
        let partyId = $('#hiddenPartyId').val();
        if (!partyId) {
            resetInvoicesAndProducts();
            return;
        }

        $('#invoicesLoadingSpinner').removeClass('d-none');
        $('#invoicesSelectDropdown').empty().trigger('change.select2');

        $.ajax({
            url: "{{ route('admin.returns.ajax.invoices') }}",
            type: 'GET',
            data: {
                channel: currentChannel,
                party_id: partyId
            },
            success: function(res) {
                $('#invoicesLoadingSpinner').addClass('d-none');
                if (res.status && res.invoices && res.invoices.length > 0) {
                    $('#invoicesSummaryText').text(res.invoices.length + ' delivered invoice(s) found. Select invoice(s) from dropdown below.');
                    
                    res.invoices.forEach(function(inv) {
                        let text = `${inv.order_number} (Delivered: ৳ ${inv.grand_total.toFixed(2)} — Date: ${inv.order_date})`;
                        let newOption = new Option(text, inv.id, false, false);
                        $('#invoicesSelectDropdown').append(newOption);
                    });

                    // Do NOT pre-select invoices by default
                    $('#invoicesSelectDropdown').val(null).trigger('change.select2');
                    resetProductsTable();
                } else {
                    $('#invoicesSummaryText').text('No delivered invoices found for this party.');
                    resetInvoicesAndProducts();
                }
            },
            error: function() {
                $('#invoicesLoadingSpinner').addClass('d-none');
                toastr.error('Failed to load invoices.');
            }
        });
    }

    // Invoices Select2 Dropdown Change -> Fetch Products
    $('#invoicesSelectDropdown').on('change', function() {
        let selectedOrderIds = $(this).val() || [];

        if (selectedOrderIds.length === 0) {
            $('#productsTableBody').html('<tr><td colspan="12" class="text-center py-4 text-muted"><p class="mb-0 font-small-3">No products available. Please select at least one invoice from the dropdown.</p></td></tr>');
            availableProducts = [];
            calculateTotals();
            return;
        }

        $('#productsLoadingSpinner').removeClass('d-none');

        $.ajax({
            url: "{{ route('admin.returns.ajax.invoice-products') }}",
            type: 'GET',
            data: {
                channel: currentChannel,
                order_ids: selectedOrderIds
            },
            success: function(res) {
                $('#productsLoadingSpinner').addClass('d-none');
                if (res.status && res.products && res.products.length > 0) {
                    availableProducts = res.products;
                    renderProductsTable(res.products);
                } else {
                    $('#productsTableBody').html('<tr><td colspan="12" class="text-center py-4 text-muted"><p class="mb-0 font-small-3">No returnable products found in the selected invoice(s).</p></td></tr>');
                    availableProducts = [];
                    calculateTotals();
                }
            },
            error: function() {
                $('#productsLoadingSpinner').addClass('d-none');
                toastr.error('Failed to load products.');
            }
        });
    });

    // Select All Invoices Button
    $('#btnSelectAllInvoices').on('click', function() {
        let allValues = [];
        $('#invoicesSelectDropdown option').each(function() {
            allValues.push($(this).val());
        });
        if (allValues.length > 0) {
            $('#invoicesSelectDropdown').val(allValues).trigger('change');
        }
    });

    // Clear Invoices Button
    $('#btnClearInvoices').on('click', function() {
        $('#invoicesSelectDropdown').val(null).trigger('change');
    });

    // Render Product Items Table
    function renderProductsTable(products) {
        let tbody = $('#productsTableBody');
        tbody.empty();

        let returnableCount = 0;

        products.forEach(function(p, i) {
            let maxReturn = p.remaining_returnable;
            let isReturnable = maxReturn > 0;
            if (isReturnable) returnableCount++;

            let initialQty = isReturnable ? 1 : 0;
            let initialSubtotal = (initialQty * p.unit_price).toFixed(2);
            let disabledAttr = isReturnable ? '' : 'disabled';
            let checkedAttr = isReturnable ? 'checked' : '';
            let unselectedClass = isReturnable ? '' : 'row-unselected';

            // Clean code label without displaying 'null'
            let codeHtml = (p.product_code && p.product_code !== 'null' && p.product_code !== '-') 
                ? `<small class="text-muted font-small-1 d-block mt-25">Code: ${p.product_code}</small>` 
                : '';

            let rowHtml = `
            <tr class="product-row ${unselectedClass}" data-index="${i}">
                <td style="width: 52px; text-align: center; vertical-align: middle;">
                    <div class="d-flex justify-content-center align-items-center m-0">
                        <input type="checkbox" class="form-check-input item-select-check m-0" name="items[${i}][selected]" value="1" style="width: 17px; height: 17px; cursor: pointer;" ${checkedAttr} ${disabledAttr}>
                    </div>
                    <input type="hidden" name="items[${i}][order_id]" value="${p.order_id}" ${disabledAttr}>
                    <input type="hidden" name="items[${i}][order_number]" value="${p.order_number}" ${disabledAttr}>
                    <input type="hidden" name="items[${i}][order_item_id]" value="${p.order_item_id}" ${disabledAttr}>
                    <input type="hidden" name="items[${i}][product_id]" value="${p.product_id}" ${disabledAttr}>
                    <input type="hidden" name="items[${i}][ordered_qty]" value="${p.ordered_qty}" ${disabledAttr}>
                    <input type="hidden" name="items[${i}][previously_returned_qty]" value="${p.previously_returned_qty}" ${disabledAttr}>
                </td>
                <td class="align-middle">
                    <div class="d-flex align-items-center">
                        <img src="${p.product_image}" alt="${p.product_name}" class="rounded shadow-sm me-1" style="width: 44px; height: 44px; object-fit: cover; border: 1px solid #e2e8f0; margin-right: 12px; flex-shrink: 0; background-color: #ffffff;" onerror="this.src='{{ asset('images/no-image.png') }}';">
                        <div>
                            <span class="font-weight-bolder text-dark d-block font-small-3" style="font-size: 13.5px; line-height: 1.3;">${p.product_name}</span>
                            ${codeHtml}
                        </div>
                    </div>
                </td>
                <td class="text-center align-middle">
                    <span class="badge badge-light-primary font-small-1 font-weight-bold">${p.order_number}</span>
                </td>
                <td class="text-center align-middle font-weight-bold text-dark font-small-3">${p.ordered_qty}</td>
                <td class="text-center align-middle text-muted font-small-3">${p.previously_returned_qty}</td>
                <td class="text-center align-middle">
                    <span class="badge ${isReturnable ? 'badge-light-success' : 'badge-light-danger'} font-weight-bolder font-small-2 px-1">
                        ${maxReturn}
                    </span>
                </td>
                <td class="text-center align-middle">
                    <input type="number" step="1" min="0" max="${maxReturn}" data-max="${maxReturn}" name="items[${i}][return_qty]" value="${initialQty}" class="form-control form-control-sm text-center return-qty-input mx-auto" style="width: 80px; height: 32px; font-weight: 700; font-size: 13px; border-radius: 6px;" ${disabledAttr}>
                </td>
                <!-- READABLE UNIT PRICE -->
                <td class="text-end align-middle">
                    <span class="font-weight-bolder text-dark unit-price-display" style="font-size: 13.5px;">৳ ${p.unit_price.toFixed(2)}</span>
                    <input type="hidden" name="items[${i}][unit_price]" value="${p.unit_price.toFixed(2)}" class="unit-price-val" ${disabledAttr}>
                </td>
                <!-- READABLE SUBTOTAL -->
                <td class="text-end align-middle">
                    <span class="font-weight-bolder ${isReturnable ? 'text-success' : 'text-muted'} subtotal-display line-subtotal-text" style="font-size: 13.5px;">৳ ${initialSubtotal}</span>
                </td>
                <td class="align-middle">
                    <select name="items[${i}][condition]" class="form-select form-control-sm condition-select" style="height: 32px; font-size: 12px; border-radius: 6px;" ${disabledAttr}>
                        <option value="good" selected>Good</option>
                        <option value="damaged">Damaged</option>
                        <option value="expired">Expired</option>
                    </select>
                </td>
                <td class="text-center align-middle">
                    <input type="checkbox" name="items[${i}][restock]" value="1" class="form-check-input restock-check" checked title="Restock to warehouse inventory" ${disabledAttr}>
                </td>
                <td class="align-middle">
                    <input type="text" name="items[${i}][item_note]" class="form-control form-control-sm" placeholder="e.g. Wrong size / Defective box" style="height: 32px; font-size: 12px; border-radius: 6px;" ${disabledAttr}>
                </td>
            </tr>`;
            tbody.append(rowHtml);
        });

        $('#checkAllProducts').prop('checked', returnableCount > 0);
        calculateTotals();
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    }

    // Update row selection styling and input disabled state
    function updateRowSelectionState(row, isSelected) {
        let check = row.find('.item-select-check');
        let qtyInput = row.find('.return-qty-input');
        let max = parseFloat(qtyInput.data('max')) || 0;
        let priceVal = parseFloat(row.find('.unit-price-val').val()) || 0;
        let rowInputs = row.find('input:not(.item-select-check), select');

        if (max <= 0) {
            check.prop('checked', false).prop('disabled', true);
            row.addClass('row-unselected');
            qtyInput.val(0);
            rowInputs.prop('disabled', true);
            row.find('.line-subtotal-text').text('৳ 0.00').removeClass('text-success').addClass('text-muted');
            return;
        }

        if (isSelected) {
            check.prop('checked', true);
            row.removeClass('row-unselected');
            rowInputs.prop('disabled', false);

            let qty = parseFloat(qtyInput.val()) || 0;
            if (qty <= 0) {
                qty = 1;
                qtyInput.val(1);
            }
            let lineSubtotal = (qty * priceVal).toFixed(2);
            row.find('.line-subtotal-text').text('৳ ' + lineSubtotal).removeClass('text-muted').addClass('text-success');
        } else {
            check.prop('checked', false);
            row.addClass('row-unselected');
            qtyInput.val(0);
            rowInputs.prop('disabled', true);
            row.find('.line-subtotal-text').text('৳ 0.00').removeClass('text-success').addClass('text-muted');
        }
    }

    // Sync "Select All" checkbox state
    function syncCheckAllState() {
        let returnableRows = $('.product-row').filter(function() {
            return (parseFloat($(this).find('.return-qty-input').data('max')) || 0) > 0;
        });
        let checkedRows = returnableRows.filter(function() {
            return $(this).find('.item-select-check').is(':checked');
        });
        $('#checkAllProducts').prop('checked', returnableRows.length > 0 && checkedRows.length === returnableRows.length);
    }

    // Check All Products
    $('#checkAllProducts').on('change', function() {
        let isChecked = $(this).is(':checked');
        $('.product-row').each(function() {
            let row = $(this);
            let max = parseFloat(row.find('.return-qty-input').data('max')) || 0;
            if (max > 0) {
                updateRowSelectionState(row, isChecked);
            }
        });
        calculateTotals();
    });

    // Checkbox Click
    $(document).on('change', '.item-select-check', function() {
        let row = $(this).closest('tr');
        let isChecked = $(this).is(':checked');
        updateRowSelectionState(row, isChecked);
        syncCheckAllState();
        calculateTotals();
    });

    // Auto-clamp return quantity and sync checkbox
    $(document).on('input change', '.return-qty-input', function() {
        let row = $(this).closest('tr');
        let max = parseFloat($(this).data('max')) || 0;
        let qty = parseFloat($(this).val()) || 0;

        if (qty > max) {
            qty = max;
            $(this).val(max);
            toastr.warning('Return quantity cannot exceed remaining returnable quantity (' + max + ').');
        } else if (qty < 0) {
            qty = 0;
            $(this).val(0);
        }

        if (qty <= 0) {
            updateRowSelectionState(row, false);
        } else {
            let priceVal = parseFloat(row.find('.unit-price-val').val()) || 0;
            let lineSubtotal = (qty * priceVal).toFixed(2);
            row.find('.line-subtotal-text').text('৳ ' + lineSubtotal).removeClass('text-muted').addClass('text-success');
            row.find('.item-select-check').prop('checked', true);
            row.removeClass('row-unselected');
            row.find('input:not(.item-select-check), select').prop('disabled', false);
        }

        syncCheckAllState();
        calculateTotals();
    });

    // When condition is Damaged or Expired, automatically uncheck Restock
    $(document).on('change', '.condition-select', function() {
        let cond = $(this).val();
        let row = $(this).closest('tr');
        let restockBox = row.find('.restock-check');
        if (cond === 'damaged' || cond === 'expired') {
            restockBox.prop('checked', false);
        } else {
            restockBox.prop('checked', true);
        }
    });

    $('#deductionInput').on('input', function() {
        calculateTotals();
    });

    // Calculate Grand Summary
    function calculateTotals() {
        let subtotal = 0;

        $('.product-row').each(function() {
            let row = $(this);
            let isSelected = row.find('.item-select-check').is(':checked');
            if (isSelected) {
                let qty = parseFloat(row.find('.return-qty-input').val()) || 0;
                let price = parseFloat(row.find('.unit-price-val').val()) || 0;
                subtotal += (qty * price);
            }
        });

        let deduction = parseFloat($('#deductionInput').val()) || 0;
        let netRefund = Math.max(0, subtotal - deduction);

        $('#summarySubtotal').text('৳ ' + subtotal.toFixed(2));
        $('#summaryNetRefund').text('৳ ' + netRefund.toFixed(2));
    }

    // Form Submission Validation
    $('#createReturnForm').on('submit', function(e) {
        let partyId = $('#hiddenPartyId').val();
        if (!partyId) {
            e.preventDefault();
            toastr.error('Please select a Dealer or Customer.');
            return false;
        }

        let selectedCount = 0;
        let totalQty = 0;

        $('.product-row').each(function() {
            let row = $(this);
            let isChecked = row.find('.item-select-check').is(':checked');
            let qty = parseFloat(row.find('.return-qty-input').val()) || 0;

            if (isChecked && qty > 0) {
                selectedCount++;
                totalQty += qty;
                // Enable inputs in this row so they are submitted
                row.find('input, select').prop('disabled', false);
            } else {
                // Ensure all inputs in unselected row are disabled so browser DOES NOT post them!
                row.find('input:not(.item-select-check), select').prop('disabled', true);
            }
        });

        if (selectedCount === 0 || totalQty <= 0) {
            e.preventDefault();
            // Re-enable interactive elements for user
            $('.product-row').each(function() {
                let row = $(this);
                let max = parseFloat(row.find('.return-qty-input').data('max')) || 0;
                if (max > 0) {
                    row.find('.item-select-check').prop('disabled', false);
                    if (row.find('.item-select-check').is(':checked')) {
                        row.find('input, select').prop('disabled', false);
                    }
                }
            });
            toastr.error('Please select at least one product with return quantity greater than 0.');
            return false;
        }

        $('#btnSubmitReturn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Submitting Return...');
    });
});
</script>
@endpush
