@extends('layouts.admin')
@section('title', 'Record Payment Collection')

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Collections</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.collections.index') }}">Collections</a></li>
        <li class="breadcrumb-item active">Record Collection</li>
    </ol>
</div>
@endsection

@section('content')
<style>
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

    .channel-card-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    @media (max-width: 768px) {
        .channel-card-grid {
            grid-template-columns: 1fr;
        }
    }
    .channel-card {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .channel-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
        transform: translateY(-2px);
    }
    .channel-card.active-dealer {
        border-color: #7367f0 !important;
        background: #f5f3ff !important;
        box-shadow: 0 4px 14px rgba(115, 103, 240, 0.18);
    }
    .channel-card.active-pos {
        border-color: #00cfe8 !important;
        background: #f0fbfc !important;
        box-shadow: 0 4px 14px rgba(0, 207, 232, 0.18);
    }
    .channel-card.active-web {
        border-color: #ff9f43 !important;
        background: #fffaf0 !important;
        box-shadow: 0 4px 14px rgba(255, 159, 67, 0.18);
    }

    .method-pill-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .method-pill {
        border: 1.5px solid #d8d6de;
        border-radius: 20px;
        padding: 7px 16px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        background: #ffffff;
        color: #4b4b4b;
        transition: all 0.2s;
    }
    .method-pill:hover {
        border-color: #7367f0;
        color: #7367f0;
    }
    .method-pill.active {
        background: #7367f0 !important;
        color: #ffffff !important;
        border-color: #7367f0 !important;
        box-shadow: 0 2px 8px rgba(115, 103, 240, 0.3);
    }

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
        <h3 class="font-weight-bolder text-dark mb-25">Record Payment Collection</h3>
        <p class="text-muted mb-0 small">Collect payments for Dealer Wallets, POS Store Dues, and Web Orders.</p>
    </div>
    <div class="mt-1 mt-sm-0">
        <a href="{{ route('admin.collections.index') }}" class="btn btn-outline-secondary">
            <i data-feather="arrow-left" class="mr-50"></i> Back to Collections
        </a>
    </div>
</div>

<form id="collectionForm">
    @csrf
    <input type="hidden" name="collection_type" id="collectionType" value="dealer">
    <input type="hidden" name="payment_method" id="paymentMethod" value="Cash">

    <div class="row">
        <!-- Main Form (8 Cols) -->
        <div class="col-lg-8 col-12">
            <!-- 1. Collection Channel Selection -->
            <div class="card card-premium">
                <div class="card-header">
                    <span class="font-weight-bolder font-medium-1 text-dark">
                        <i data-feather="grid" class="mr-50 text-primary"></i> 1. Select Collection Category
                    </span>
                </div>
                <div class="card-body">
                    <div class="channel-card-grid">
                        <div class="channel-card active-dealer" data-type="dealer" id="cardDealer">
                            <div>
                                <div class="badge badge-light-primary p-50 mb-75 font-medium-1">
                                    <i data-feather="briefcase"></i>
                                </div>
                                <h6 class="font-weight-bolder mb-25 text-dark">Dealer Wallet</h6>
                                <p class="text-muted font-small-2 mb-0">Credits wallet & FIFO settles delivered orders</p>
                            </div>
                            <div class="mt-1 pt-75 border-top">
                                <span class="badge badge-light-primary font-small-1">Wallet & FIFO Auto-Pay</span>
                            </div>
                        </div>

                        <div class="channel-card" data-type="pos_order" id="cardPos">
                            <div>
                                <div class="badge badge-light-info p-50 mb-75 font-medium-1">
                                    <i data-feather="shopping-bag"></i>
                                </div>
                                <h6 class="font-weight-bolder mb-25 text-dark">POS Order Due</h6>
                                <p class="text-muted font-small-2 mb-0">Pay outstanding due for store POS order</p>
                            </div>
                            <div class="mt-1 pt-75 border-top">
                                <span class="badge badge-light-info font-small-1">Direct Order Pay</span>
                            </div>
                        </div>

                        <div class="channel-card" data-type="web_order" id="cardWeb">
                            <div>
                                <div class="badge badge-light-warning p-50 mb-75 font-medium-1">
                                    <i data-feather="globe"></i>
                                </div>
                                <h6 class="font-weight-bolder mb-25 text-dark">Web Order</h6>
                                <p class="text-muted font-small-2 mb-0">Record payment for online customer order</p>
                            </div>
                            <div class="mt-1 pt-75 border-top">
                                <span class="badge badge-light-warning font-small-1">Online Sales Pay</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Target Party & Order Selection -->
            <div class="card card-premium">
                <div class="card-header">
                    <span class="font-weight-bolder font-medium-1 text-dark" id="partyHeaderTitle">
                        <i data-feather="user-check" class="mr-50 text-primary"></i> 2. Target Dealer & Account
                    </span>
                </div>
                <div class="card-body">
                    <!-- Dealer Section -->
                    <div id="dealerSection">
                        <div class="form-group mb-1">
                            <label class="font-weight-bold" for="dealerId">Select Dealer <span class="text-danger">*</span></label>
                            <select name="dealer_id" id="dealerId" class="form-control select2">
                                <option value="">-- Choose Dealer --</option>
                                @foreach($dealers as $dealer)
                                    @php
                                        $delDue = $dealer->orders->where('status', 'delivered')->sum('due');
                                    @endphp
                                    <option value="{{ $dealer->id }}"
                                            data-name="{{ $dealer->name }}"
                                            data-shop="{{ $dealer->shop_name }}"
                                            data-phone="{{ $dealer->phone }}"
                                            data-wallet="{{ (float) $dealer->wallet_balance }}"
                                            data-due="{{ (float) $delDue }}">
                                        {{ $dealer->shop_name }} - {{ $dealer->name }} (Wallet: ৳{{ number_format($dealer->wallet_balance, 2) }}, Due: ৳{{ number_format($delDue, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="alert alert-primary p-1 font-small-3 mb-0" id="dealerWalletNotice">
                            <i data-feather="info" class="mr-50"></i>
                            <strong>Dealer Wallet Rule:</strong> Every collection credits the dealer's wallet first. Any delivered orders with pending dues are automatically settled in ascending FIFO order (earliest delivered order #1, then #2, #3...).
                        </div>
                    </div>

                    <!-- POS Order Section -->
                    <div id="posSection" style="display: none;">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold" for="posOrderId">Select POS Order (with Due) <span class="text-danger">*</span></label>
                            <select name="pos_order_id" id="posOrderId" class="form-control select2">
                                <option value="">-- Choose POS Order --</option>
                                @foreach($posOrders as $order)
                                    <option value="{{ $order->id }}"
                                            data-no="{{ $order->order_number }}"
                                            data-customer="{{ $order->customer->name ?? 'Walk-in' }}"
                                            data-due="{{ (float) $order->due_amount }}">
                                        Order #{{ $order->order_number }} - {{ $order->customer->name ?? 'Walk-in' }} (Due: ৳{{ number_format($order->due_amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Web Order Section -->
                    <div id="webSection" style="display: none;">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold" for="webOrderId">Select Web Order <span class="text-danger">*</span></label>
                            <select name="web_order_id" id="webOrderId" class="form-control select2">
                                <option value="">-- Choose Web Order --</option>
                                @foreach($webOrders as $wOrder)
                                    <option value="{{ $wOrder->id }}"
                                            data-invoice="{{ $wOrder->invoice_no }}"
                                            data-total="{{ (float) $wOrder->total }}"
                                            data-method="{{ $wOrder->payment_method }}">
                                        Invoice #{{ $wOrder->invoice_no }} (Total: ৳{{ number_format($wOrder->total, 2) }} - {{ ucfirst($wOrder->status) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Payment Method & Transaction Details -->
            <div class="card card-premium">
                <div class="card-header">
                    <span class="font-weight-bolder font-medium-1 text-dark">
                        <i data-feather="credit-card" class="mr-50 text-primary"></i> 3. Payment Method & Reference
                    </span>
                </div>
                <div class="card-body">
                    <label class="font-weight-bold d-block mb-1">Select Payment Method <span class="text-danger">*</span></label>
                    <div class="method-pill-grid mb-2">
                        <span class="method-pill active" data-method="Cash"><i data-feather="dollar-sign" class="mr-25"></i> Cash</span>
                        <span class="method-pill" data-method="Bank"><i data-feather="credit-card" class="mr-25"></i> Bank Transfer</span>
                        <span class="method-pill" data-method="bKash"><i data-feather="smartphone" class="mr-25"></i> bKash</span>
                        <span class="method-pill" data-method="Nagad"><i data-feather="smartphone" class="mr-25"></i> Nagad</span>
                        <span class="method-pill" data-method="Rocket"><i data-feather="smartphone" class="mr-25"></i> Rocket</span>
                    </div>

                    <!-- Conditional Bank Fields -->
                    <div id="bankFields" style="display: none;" class="p-1 mb-2 bg-light rounded border">
                        <h6 class="font-weight-bolder text-dark mb-1"><i data-feather="home" class="mr-50 text-primary"></i> Bank Account Information</h6>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group mb-1">
                                    <label class="font-small-2 font-weight-bold" for="bankName">Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" name="bank_name" id="bankName" class="form-control form-control-sm" placeholder="e.g. Dutch Bangla Bank, City Bank">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group mb-1">
                                    <label class="font-small-2 font-weight-bold" for="accountNo">Account Number</label>
                                    <input type="text" name="account_no" id="accountNo" class="form-control form-control-sm" placeholder="e.g. 123.105.XXXXX">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conditional Online MFS (bKash, Nagad, Rocket) Fields -->
                    <div id="onlineFields" style="display: none;" class="p-1 mb-2 bg-light rounded border">
                        <h6 class="font-weight-bolder text-dark mb-1"><i data-feather="hash" class="mr-50 text-primary"></i> Mobile Transaction Reference</h6>
                        <div class="row">
                            <div class="col-md-12 col-12">
                                <div class="form-group mb-0">
                                    <label class="font-small-2 font-weight-bold" for="transactionId">Transaction ID (TrxID) / Ref <span class="text-danger">*</span></label>
                                    <input type="text" name="transaction_id" id="transactionId" class="form-control form-control-sm" placeholder="e.g. 9K72BXYZ10">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amount, Date & Status -->
                    <div class="row">
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="font-weight-bold" for="amount">Collection Amount (৳) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-merge">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bolder text-success">৳</span>
                                    </div>
                                    <input type="number" name="amount" id="amount" class="form-control form-control-lg font-weight-bolder" step="0.01" min="0.01" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="font-weight-bold" for="paymentDate">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" id="paymentDate" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="font-weight-bold" for="collectionStatus">Collection Status <span class="text-danger">*</span></label>
                                <select name="status" id="collectionStatus" class="form-control font-weight-bold">
                                    <option value="pending" selected>Pending (Requires Acceptance)</option>
                                    <option value="accepted">Accepted (Credit Immediately)</option>
                                </select>
                                <small class="text-muted font-small-1">Default is <strong>Pending</strong>.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Note -->
                    <div class="form-group mb-0">
                        <label class="font-weight-bold" for="note">Reference Note / Remarks</label>
                        <textarea name="note" id="note" class="form-control" rows="2" placeholder="e.g. Received via cheque, advance deposit, store cashier collection..."></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Live Summary (4 Cols) -->
        <div class="col-lg-4 col-12">
            <div class="card card-premium" style="position: sticky; top: 90px;">
                <div class="card-header bg-light-primary py-1">
                    <span class="font-weight-bolder text-primary">
                        <i data-feather="check-square" class="mr-50"></i> Receipt Preview
                    </span>
                </div>
                <div class="card-body">
                    <div class="p-1 mb-1 rounded bg-light border text-center">
                        <span class="text-muted font-small-2 font-weight-bold text-uppercase d-block mb-25">Collection Amount</span>
                        <h2 class="font-weight-bolder text-success mb-0" id="previewAmount">৳ 0.00</h2>
                    </div>

                    <div class="p-1 mb-1 rounded bg-light border">
                        <span class="text-muted font-small-2 font-weight-bold text-uppercase d-block mb-25">Category & Target</span>
                        <h6 class="font-weight-bolder text-dark mb-25" id="previewTargetName">--</h6>
                        <span class="badge badge-light-primary font-small-1" id="previewCategoryBadge">Dealer Wallet</span>
                    </div>

                    <div id="dealerBalanceBox" class="p-1 mb-1 rounded bg-light border">
                        <div class="d-flex justify-content-between mb-50">
                            <span class="text-muted font-small-2">Current Wallet:</span>
                            <strong class="text-dark font-small-2" id="previewDealerWallet">৳ 0.00</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted font-small-2">Delivered Orders Due:</span>
                            <strong class="text-danger font-small-2" id="previewDealerDue">৳ 0.00</strong>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-75 border-bottom">
                        <span class="text-muted font-small-3">Payment Method:</span>
                        <strong class="text-dark font-small-3" id="previewMethod">Cash</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-75 border-bottom">
                        <span class="text-muted font-small-3">Initial Status:</span>
                        <span id="previewStatus" class="badge badge-pill badge-light-warning font-weight-bold">Pending</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-75 mb-2">
                        <span class="text-muted font-small-3">Payment Date:</span>
                        <span class="text-dark font-small-3 font-weight-bold" id="previewDate">{{ date('d M, Y') }}</span>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block py-75 font-weight-bold" id="saveCollectionBtn">
                        <i data-feather="check-circle" class="mr-50"></i> Confirm & Save Collection
                    </button>
                    <a href="{{ route('admin.collections.index') }}" class="btn btn-outline-secondary btn-block mt-50">
                        Cancel
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

        $('.select2').select2({ width: '100%' });

        // Category Card Selector
        $('.channel-card').on('click', function() {
            var type = $(this).data('type');
            $('#collectionType').val(type);

            $('.channel-card').removeClass('active-dealer active-pos active-web');

            if (type === 'dealer') {
                $(this).addClass('active-dealer');
                $('#partyHeaderTitle').html('<i data-feather="briefcase" class="mr-50 text-primary"></i> 2. Target Dealer & Account');
                $('#previewCategoryBadge').text('Dealer Wallet').removeClass('badge-light-info badge-light-warning').addClass('badge-light-primary');
                $('#dealerSection').show();
                $('#posSection, #webSection').hide();
                $('#dealerBalanceBox').show();
            } else if (type === 'pos_order') {
                $(this).addClass('active-pos');
                $('#partyHeaderTitle').html('<i data-feather="shopping-bag" class="mr-50 text-info"></i> 2. Target POS Order');
                $('#previewCategoryBadge').text('POS Order Due').removeClass('badge-light-primary badge-light-warning').addClass('badge-light-info');
                $('#posSection').show();
                $('#dealerSection, #webSection').hide();
                $('#dealerBalanceBox').hide();
            } else {
                $(this).addClass('active-web');
                $('#partyHeaderTitle').html('<i data-feather="globe" class="mr-50 text-warning"></i> 2. Target Web Order');
                $('#previewCategoryBadge').text('Web Order').removeClass('badge-light-primary badge-light-info').addClass('badge-light-warning');
                $('#webSection').show();
                $('#dealerSection, #posSection').hide();
                $('#dealerBalanceBox').hide();
            }

            if (feather) {
                feather.replace({ width: 14, height: 14 });
            }
            updatePreview();
        });

        // Payment Method Pills
        $('.method-pill').on('click', function() {
            var method = $(this).data('method');
            $('#paymentMethod').val(method);
            $('.method-pill').removeClass('active');
            $(this).addClass('active');

            if (method === 'Bank') {
                $('#bankFields').slideDown(200);
                $('#onlineFields').slideUp(200);
            } else if (['bKash', 'Nagad', 'Rocket'].includes(method)) {
                $('#onlineFields').slideDown(200);
                $('#bankFields').slideUp(200);
            } else {
                $('#bankFields, #onlineFields').slideUp(200);
            }

            updatePreview();
        });

        function updatePreview() {
            var type = $('#collectionType').val();
            var amount = parseFloat($('#amount').val()) || 0;
            var method = $('#paymentMethod').val();
            var date = $('#paymentDate').val();

            $('#previewAmount').text('৳ ' + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#previewMethod').text(method);

            if (date) {
                var d = new Date(date);
                $('#previewDate').text(d.toLocaleDateString('en-US', { day: '2-digit', month: 'short', year: 'numeric' }));
            }

            if (type === 'dealer') {
                var opt = $('#dealerId option:selected');
                var shop = opt.data('shop') || '';
                var name = opt.data('name') || '';
                var wallet = parseFloat(opt.data('wallet')) || 0;
                var due = parseFloat(opt.data('due')) || 0;

                $('#previewTargetName').text(shop ? shop + ' (' + name + ')' : '-- Choose Dealer --');
                $('#previewDealerWallet').text('৳ ' + wallet.toFixed(2));
                $('#previewDealerDue').text('৳ ' + due.toFixed(2));
            } else if (type === 'pos_order') {
                var pOpt = $('#posOrderId option:selected');
                var pNo = pOpt.data('no') || '';
                var pCust = pOpt.data('customer') || '';
                var pDue = parseFloat(pOpt.data('due')) || 0;

                $('#previewTargetName').text(pNo ? 'POS #' + pNo + ' - ' + pCust : '-- Choose POS Order --');
                if (pDue > 0 && amount === 0) {
                    $('#amount').val(pDue.toFixed(2));
                    updatePreview();
                }
            } else if (type === 'web_order') {
                var wOpt = $('#webOrderId option:selected');
                var wInv = wOpt.data('invoice') || '';
                var wTot = parseFloat(wOpt.data('total')) || 0;

                $('#previewTargetName').text(wInv ? 'Web Order #' + wInv : '-- Choose Web Order --');
                if (wTot > 0 && amount === 0) {
                    $('#amount').val(wTot.toFixed(2));
                    updatePreview();
                }
            }
            var status = $('#collectionStatus').val();
            if (status === 'accepted') {
                $('#previewStatus').removeClass('badge-light-warning').addClass('badge-light-success').text('Accepted');
            } else {
                $('#previewStatus').removeClass('badge-light-success').addClass('badge-light-warning').text('Pending');
            }
        }

        $('#dealerId, #posOrderId, #webOrderId, #amount, #paymentDate, #collectionStatus').on('change input', function() {
            updatePreview();
        });

        // Form Submit
        $('#collectionForm').on('submit', function(e) {
            e.preventDefault();

            var type = $('#collectionType').val();
            var amount = parseFloat($('#amount').val()) || 0;

            if (amount <= 0) {
                toastr.warning('Please enter a valid amount.');
                $('#amount').focus();
                return;
            }

            if (type === 'dealer' && !$('#dealerId').val()) {
                toastr.warning('Please choose a dealer.');
                $('#dealerId').select2('open');
                return;
            }

            if (type === 'pos_order' && !$('#posOrderId').val()) {
                toastr.warning('Please choose a POS order.');
                $('#posOrderId').select2('open');
                return;
            }

            if (type === 'web_order' && !$('#webOrderId').val()) {
                toastr.warning('Please choose a Web order.');
                $('#webOrderId').select2('open');
                return;
            }

            var method = $('#paymentMethod').val();
            if (method === 'Bank' && !$('#bankName').val()) {
                toastr.warning('Please provide the Bank Name.');
                $('#bankName').focus();
                return;
            }

            if (['bKash', 'Nagad', 'Rocket'].includes(method) && !$('#transactionId').val()) {
                toastr.warning('Please provide the Transaction ID (TrxID).');
                $('#transactionId').focus();
                return;
            }

            $('#saveCollectionBtn').html('<span class="spinner-border spinner-border-sm mr-50" role="status"></span> Saving...').attr('disabled', true);

            $.ajax({
                url: "{{ route('admin.collections.store') }}",
                type: "POST",
                data: $('#collectionForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    toastr.success(response.message || 'Collection recorded successfully!');
                    setTimeout(function() {
                        window.location.href = "{{ route('admin.collections.index') }}";
                    }, 600);
                },
                error: function(xhr) {
                    $('#saveCollectionBtn').html('<i data-feather="check-circle" class="mr-50"></i> Confirm & Save Collection').attr('disabled', false);
                    if (feather) {
                        feather.replace({ width: 14, height: 14 });
                    }
                    var msg = 'Failed to record collection.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    toastr.error(msg);
                }
            });
        });
    });
</script>
@endpush
