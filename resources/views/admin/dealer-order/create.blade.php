@extends('layouts.admin')
@section('title', 'Create Dealer Order - ' . $orderNumber)

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Create Dealer Order</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.dealer-order.index') }}">Dealer Orders</a></li>
        <li class="breadcrumb-item active">{{ $orderNumber }}</li>
    </ol>
</div>
@endsection

@section('content-header-right')
<a href="{{ route('admin.dealer-order.index') }}" class="btn btn-outline-secondary">
    <i data-feather="arrow-left" class="mr-50"></i> Back to Orders
</a>
@endsection

@section('content')
<style>
    .invoice-preview-card {
        border-radius: 12px;
    }
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        border: 1px solid #d8d6de !important;
        border-radius: 6px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        padding-left: 8px !important;
        font-weight: 500;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px !important;
        top: 0 !important;
    }
</style>

<form action="{{ route('admin.dealer-order.store') }}" method="POST" enctype="multipart/form-data" id="dealerOrderForm">
    @csrf

    <div class="row">
        <!-- Full-Width Main Invoice Card -->
        <div class="col-12">
            <div class="card shadow-sm border-0 invoice-preview-card">
                <div class="card-body">

                    <!-- 1. Header Meta (Invoice Style) -->
                    <div class="d-flex justify-content-between flex-md-row flex-column pb-2 border-bottom">
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <h3 class="text-primary font-weight-bolder mb-0 mr-1">
                                    <i data-feather="file-text" class="mr-50"></i> DEALER ORDER INVOICE
                                </h3>
                                <span class="badge badge-light-primary font-weight-bold" style="font-size: 0.9rem;">{{ $orderNumber }}</span>
                                <input type="hidden" name="order_number" value="{{ $orderNumber }}">
                            </div>
                            <p class="mb-0 text-muted small">Create wholesale dealer order with live tier pricing</p>
                        </div>
                        <div class="mt-md-0 mt-2 text-md-right">
                            <div class="d-flex align-items-center justify-content-md-end mb-50">
                                <span class="font-weight-bold mr-1 text-dark">Status:</span>
                                <select name="status" id="orderStatus" class="form-control form-control-sm select2" style="width: 140px;">
                                    <option value="pending" selected>Pending</option>
                                    <option value="confirm">Confirm</option>
                                </select>
                            </div>
                            <div class="d-flex align-items-center justify-content-md-end">
                                <span class="font-weight-bold mr-1 text-dark">Order Date:</span>
                                <input type="date" name="order_date" class="form-control form-control-sm font-weight-bold" value="{{ date('Y-m-d') }}" style="width: 150px;" required>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Order Details: Full Width Section (Bill To & Delivery/Address) -->
                    <div class="row py-2 border-bottom">
                        <div class="col-md-6 col-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="text-uppercase text-muted font-weight-bold mb-0" style="font-size:0.8rem; letter-spacing:0.5px;">
                                    Dealer Information <span class="text-danger">*</span>
                                </h6>
                                <span id="dealerTierBadge" class="badge badge-light-secondary font-weight-bold">Select dealer</span>
                            </div>
                            <div class="form-group mb-1">
                                <select name="dealer_id" id="dealerSelect" class="form-control select2" required data-placeholder="-- Choose a Dealer --">
                                    <option value="" disabled selected>-- Choose a Dealer --</option>
                                    @foreach($dealers as $c)
                                        <option value="{{ $c->id }}" 
                                            data-name="{{ $c->name }}"
                                            data-shop="{{ $c->shop_name }}"
                                            data-phone="{{ $c->phone }}"
                                            data-address="{{ $c->address }}"
                                            data-is-special="{{ $c->is_special ? '1' : '0' }}">
                                            {{ $c->shop_name }} ({{ $c->name ?? '' }} - {{ $c->phone }}) {{ $c->is_special ? '⭐ [Special Dealer]' : '[Regular Dealer]' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Dynamic Dealer Detail Box -->
                            <div id="dealerDetailBox" class="p-1 rounded bg-light border d-none">
                                <p class="mb-25 font-weight-bolder text-dark" id="dispShopName" style="font-size:1.05rem;"></p>
                                <p class="mb-25"><strong>Contact Person:</strong> <span id="dispContactName"></span></p>
                                <p class="mb-0"><strong>Phone:</strong> <span id="dispPhone"></span></p>
                            </div>
                        </div>

                        <div class="col-md-6 col-12 mt-md-0 mt-1 border-left-md">
                            <h6 class="text-uppercase text-muted font-weight-bold mb-1" style="font-size:0.8rem; letter-spacing:0.5px;">Delivery & Address</h6>
                            <div class="p-1 rounded bg-light border" style="min-height: 105px;">
                                <p class="mb-25"><strong>Address:</strong> <span id="dispAddress" class="text-muted">Select a dealer above to view address details</span></p>
                                <p class="mb-0"><strong>Pricing Tier:</strong> <span id="dispTierText" class="text-primary font-weight-bold">Rates adjust according to selected dealer tier</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Cart Items: Full Width Section -->
                    <div class="my-2">
                        <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap" style="gap: 10px;">
                            <h5 class="font-weight-bolder text-dark mb-0">
                                <i data-feather="shopping-cart" class="mr-50 text-primary"></i> Order Items & Products
                            </h5>
                            <span class="badge badge-light-primary font-weight-bold" id="cartItemCountBadge">0 Products</span>
                        </div>

                        <!-- Search & Add Product Dropdown Bar -->
                        <div class="p-1 rounded bg-light border mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-50">
                                <label class="text-primary font-weight-bold mb-0">
                                    <i data-feather="search" style="width:14px;height:14px;" class="mr-25"></i> Search & Select Product To Add
                                </label>
                                <small class="text-muted font-weight-bold" id="dropdownTierIndicator">Tier: Regular Rate</small>
                            </div>
                            <select id="productSelect" class="form-control select2" data-placeholder="Type product name to search and add...">
                                <option value="" disabled selected>Type product name to search and add...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product['id'] }}" 
                                        data-name="{{ $product['name'] }}"
                                        data-regular-price="{{ $product['regular_dealer_price'] }}"
                                        data-special-price="{{ $product['special_dealer_price'] }}"
                                        data-image="{{ $product['featured_image'] ?? asset('images/no-image.png') }}"
                                        data-has-image="{{ $product['featured_image'] ? '1' : '0' }}"
                                        data-mfg-image="{{ $product['manufacture_image'] ?? '' }}"
                                        data-is-mfg="{{ $product['is_manufacturer'] }}"
                                        data-stock="{{ $product['stock'] }}">
                                        {{ $product['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted font-italic mt-25 d-block">
                                <i data-feather="info" style="width:12px;height:12px;"></i>
                                Products with ৳0 or no price are excluded. For manufacture products, rate is calculative according to materials and dealer tier.
                            </small>
                        </div>

                        <!-- Full Width Items Table -->
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="cartTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th style="width: 80px;">Product Img</th>
                                        <th style="width: 80px;">Manufacture Img</th>
                                        <th>Product Name</th>
                                        <th class="text-right" style="width: 140px;">Unit Price (৳)</th>
                                        <th class="text-center" style="width: 130px;">Quantity</th>
                                        <th class="text-right" style="width: 140px;">Total (৳)</th>
                                        <th class="text-center" style="width: 60px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="emptyCartRow">
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i data-feather="package" style="width:36px;height:36px;opacity:0.3;" class="mb-1"></i><br>
                                            <strong>No products added to this invoice yet.</strong><br>
                                            <small>Select a dealer above and search products in the dropdown to add them here.</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 4. Notes & Financial Summary (Invoice Style Bottom Row) -->
                    <div class="row mt-2 pt-2 border-top">
                        <!-- Left Side: Notes -->
                        <div class="col-md-7 col-12">
                            <div class="form-group mb-2">
                                <label class="font-weight-bold text-muted small text-uppercase">Customer / Delivery Note</label>
                                <textarea name="note" class="form-control" rows="3" placeholder="Special delivery instructions or order note..."></textarea>
                            </div>
                            <div class="form-group mb-2">
                                <label class="font-weight-bold text-muted small text-uppercase">Internal Admin Note</label>
                                <textarea name="admin_notes" class="form-control" rows="3" placeholder="Internal staff remarks..."></textarea>
                            </div>
                        </div>

                        <!-- Right Side: Invoice Financial Summary Table -->
                        <div class="col-md-5 col-12">
                            <div class="card bg-light border-0">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted font-weight-bold">Total Items / Qty:</span>
                                        <span class="font-weight-bold text-dark" id="summaryTotalQty">0 Pcs</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted font-weight-bold">Sub Total:</span>
                                        <span class="font-weight-bold text-dark" id="summarySubTotal" style="font-size:1.05rem;">৳0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted font-weight-bold">Discount (৳):</span>
                                        <input type="number" step="0.01" name="discount" id="discount" class="form-control form-control-sm text-right font-weight-bold text-danger" style="width: 130px;" value="0">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted font-weight-bold">Promotional Discount:</span>
                                        <span class="font-weight-bold text-danger" id="summaryPromotionalDiscount">৳0.00</span>
                                        <input type="hidden" name="promotional_discount" id="promotionalDiscount" value="0">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted font-weight-bold">Carrying Charge (৳):</span>
                                        <input type="number" step="0.01" name="carrying_charge" id="carryingCharge" class="form-control form-control-sm text-right font-weight-bold text-success" style="width: 130px;" value="0">
                                    </div>

                                    <hr class="my-1">

                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="font-weight-bolder text-dark" style="font-size:1.15rem;">Grand Total:</span>
                                        <span class="font-weight-bolder text-primary" id="summaryGrandTotal" style="font-size:1.35rem;">৳0.00</span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted font-weight-bold">Payment Received (৳):</span>
                                        <input type="number" step="0.01" name="paid" id="paidAmount" class="form-control form-control-sm text-right font-weight-bold text-dark" style="width: 130px;" value="0">
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center pt-1 border-top">
                                        <span class="font-weight-bold text-dark" style="font-size:1rem;">Balance Due:</span>
                                        <span class="font-weight-bolder" id="summaryDue" style="font-size:1.2rem; color: #ea5455;">৳0.00</span>
                                    </div>

                                    <!-- Action Submit Button -->
                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary btn-block shadow font-weight-bold py-1" id="btnSubmitOrder" style="font-size: 1.05rem;">
                                            <i data-feather="check-circle" class="mr-50"></i> CONFIRM & CREATE ORDER
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Inputs for Form Submission -->
    <div id="cartHiddenInputs"></div>
</form>

@include('admin.partials.image-preview-modal')
@endsection

@push('scripts')
<script>
    let cart = [];
    const promotionalOffers = @json($promotionalOffers ?? []);

    function calcDealerLinePromo(unitPrice, quantity, productId) {
        const qty = parseInt(quantity, 10) || 0;
        const price = parseFloat(unitPrice) || 0;
        const pid = parseInt(productId, 10);
        let best = 0;
        promotionalOffers.forEach(function (offer) {
            if (parseInt(offer.product_id, 10) !== pid) return;
            if (qty < parseInt(offer.from_qty, 10) || qty > parseInt(offer.to_qty, 10)) return;
            const lineTotal = price * qty;
            const value = parseFloat(offer.discount_value) || 0;
            let discount = offer.offer_type === 'percent'
                ? (lineTotal * value) / 100
                : value * qty;
            discount = Math.max(0, Math.min(discount, lineTotal));
            if (discount > best) best = discount;
        });
        return Math.round(best * 100) / 100;
    }

    function calcDealerPromotionalDiscount() {
        let total = 0;
        cart.forEach(function (item) {
            total += calcDealerLinePromo(item.price, item.qty, item.id);
        });
        return Math.round(total * 100) / 100;
    }
    let isSpecialDealer = false;

    // Custom Select2 formatters for products in dropdown
    function formatProductOption(product) {
        if (!product.id) {
            return product.text;
        }
        let el = $(product.element);
        let img = el.data('image');
        let mfgImg = el.data('mfg-image');
        let isMfg = el.data('is-mfg') == '1';
        let stock = el.data('stock');
        let hasImg = el.data('has-image') == '1';
        let regPrice = parseFloat(el.data('regular-price')) || 0;
        let specPrice = parseFloat(el.data('special-price')) || 0;
        let currentPrice = isSpecialDealer ? specPrice : regPrice;

        // Do not show products whose price is 0 or less
        if (currentPrice <= 0) {
            return null;
        }

        let pImgHtml = hasImg 
            ? `<img src="${img}" style="width:34px;height:34px;object-fit:cover;border-radius:4px;border:1px solid #e2e8f0;margin-right:8px;" onerror="this.src='/images/no-image.png'">`
            : `<img src="/images/no-image.png" style="width:34px;height:34px;object-fit:cover;border-radius:4px;border:1px solid #e2e8f0;margin-right:8px;">`;

        let mfgImgHtml = (isMfg && mfgImg)
            ? `<span title="Manufacture Blueprint"><img src="${mfgImg}" style="width:34px;height:34px;object-fit:cover;border-radius:4px;border:1px solid #c7d2fe;margin-right:8px;" onerror="this.src='/images/no-image.png'"></span>`
            : '';

        let badgeHtml = isMfg 
            ? `<span class="badge badge-light-primary ml-1" style="font-size:0.68rem; padding: 2px 6px;">Manufacture</span>`
            : `<span class="badge badge-light-secondary ml-1" style="font-size:0.68rem; padding: 2px 6px;">Stock: ${stock}</span>`;

        return $(`
            <div class="d-flex align-items-center justify-content-between py-25">
                <div class="d-flex align-items-center">
                    ${pImgHtml}
                    ${mfgImgHtml}
                    <div style="line-height:1.2;">
                        <div class="font-weight-bold text-dark" style="font-size:0.9rem;">${el.data('name') || product.text} ${badgeHtml}</div>
                        <div class="small text-muted" style="font-size:0.75rem;">${isMfg ? 'Dynamic Material Recipe' : 'Catalog Stock: ' + stock}</div>
                    </div>
                </div>
                <div class="text-right ml-2">
                    <span class="font-weight-bolder text-primary" style="font-size:0.95rem;">৳${currentPrice.toFixed(2)}</span>
                    <div class="small text-muted" style="font-size:0.68rem;">${isSpecialDealer ? 'Special Rate' : 'Regular Rate'}</div>
                </div>
            </div>
        `);
    }

    function formatProductSelection(product) {
        if (!product.id) return product.text;
        let el = $(product.element);
        return el.data('name') || product.text;
    }

    $(document).ready(function() {
        // Initialize Select2
        $('select[name="status"]').select2({ minimumResultsForSearch: Infinity });

        $('#dealerSelect').select2({
            placeholder: '-- Choose a Dealer --',
            allowClear: false
        });

        $('#productSelect').select2({
            placeholder: 'Type product name to search and add...',
            templateResult: formatProductOption,
            templateSelection: formatProductSelection,
            escapeMarkup: function(m) { return m; }
        });

        // Dealer Selection Handler
        $('#dealerSelect').on('change', function() {
            let option = $(this).find(':selected');
            let dealerId = $(this).val();

            if (!dealerId) {
                $('#dealerDetailBox').addClass('d-none');
                $('#dealerTierBadge').removeClass('badge-success badge-secondary').addClass('badge-light-secondary').text('Select dealer');
                $('#dispAddress').text('Select a dealer above to view address details');
                $('#dispTierText').text('Rates adjust according to selected dealer tier');
                return;
            }

            let name = option.data('name');
            let shop = option.data('shop');
            let phone = option.data('phone');
            let address = option.data('address');
            isSpecialDealer = option.data('is-special') == '1';

            $('#dispShopName').text(shop || 'Dealer Information');
            $('#dispContactName').text(name || 'N/A');
            $('#dispPhone').text(phone || 'N/A');
            $('#dispAddress').text(address || 'N/A');
            $('#dealerDetailBox').removeClass('d-none');

            if (isSpecialDealer) {
                $('#dealerTierBadge').removeClass('badge-secondary badge-light-secondary')
                    .addClass('badge-success')
                    .html('⭐ Special Dealer (Tier Rate)');
                $('#dispTierText').text('Special Dealer Tier Active (Discounted material calculation)');
                $('#dropdownTierIndicator').text('Tier: Special Rate');
            } else {
                $('#dealerTierBadge').removeClass('badge-success badge-light-secondary')
                    .addClass('badge-secondary')
                    .text('Regular Dealer Tier Active');
                $('#dispTierText').text('Regular Dealer Tier Active (Standard material calculation)');
                $('#dropdownTierIndicator').text('Tier: Regular Rate');
            }

            // Re-render select2 options to update tier rates
            $('#productSelect').select2('destroy').select2({
                placeholder: 'Type product name to search and add...',
                templateResult: formatProductOption,
                templateSelection: formatProductSelection,
                escapeMarkup: function(m) { return m; }
            });

            // Update cart items prices to match newly selected dealer tier
            if (cart.length > 0) {
                updateCartPricesToDealerTier();
            }
        });

        // Trigger dealer change if already selected
        if ($('#dealerSelect').val()) {
            $('#dealerSelect').trigger('change');
        }

        // Product Selection Handler: Add to cart when selected from dropdown
        $('#productSelect').on('change', function() {
            let productId = $(this).val();
            let dealerId = $('#dealerSelect').val();

            if (!productId) return;

            if (!dealerId) {
                toastr.warning('Please select a dealer first to calculate the correct pricing tier.');
                $(this).val('').trigger('change.select2');
                return;
            }

            let option = $(this).find(':selected');
            let productName = option.data('name') || option.text().trim();
            let productImage = option.data('image') || '';
            let productMfgImage = option.data('mfg-image') || '';
            let isMfg = option.data('is-mfg') == '1';
            let regPrice = parseFloat(option.data('regular-price')) || 0;
            let specPrice = parseFloat(option.data('special-price')) || 0;
            let finalPrice = isSpecialDealer ? specPrice : regPrice;

            if (finalPrice <= 0) {
                toastr.error('This product does not have a valid price.');
                $(this).val('').trigger('change.select2');
                return;
            }

            addToCart(productId, productName, finalPrice, productImage, productMfgImage, isMfg);

            // Clear selection so user can immediately add the next product
            $(this).val('').trigger('change.select2');
        });

        // Inputs triggering financial summary update
        $('#discount, #carryingCharge, #paidAmount').on('input change', function() {
            updateSummary();
        });

        // Form submit validation
        $('#dealerOrderForm').on('submit', function(e) {
            if (!$('#dealerSelect').val()) {
                e.preventDefault();
                toastr.error('Please select a dealer.');
                return false;
            }
            if (cart.length === 0) {
                e.preventDefault();
                toastr.error('Please add at least one product to the order.');
                return false;
            }
            $('#btnSubmitOrder').prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr-50"></span> Creating Order...');
        });
    });

    // Add item to cart
    function addToCart(id, name, price, image, mfgImage, isMfg) {
        let existing = cart.find(i => i.id == id);
        if (existing) {
            existing.qty += 1;
            toastr.info(`Increased quantity for ${name}.`);
        } else {
            cart.push({
                id: id,
                name: name,
                price: parseFloat(price) || 0,
                qty: 1,
                image: image,
                mfg_image: mfgImage,
                is_mfg: isMfg
            });
            toastr.success(`Added ${name} to order.`);
        }
        renderCart();
    }

    // Update cart item prices when dealer tier changes
    function updateCartPricesToDealerTier() {
        let updated = false;
        cart.forEach(item => {
            let option = $(`#productSelect option[value="${item.id}"]`);
            if (option.length) {
                let regPrice = parseFloat(option.data('regular-price')) || 0;
                let specPrice = parseFloat(option.data('special-price')) || 0;
                let newPrice = isSpecialDealer ? specPrice : regPrice;
                if (item.price !== newPrice && newPrice > 0) {
                    item.price = newPrice;
                    updated = true;
                }
            }
        });
        if (updated) {
            renderCart();
            toastr.info('Cart item prices updated to match dealer tier.');
        }
    }

    // Render cart items table
    function renderCart() {
        let tbody = $('#cartTable tbody');
        let hiddenInputs = $('#cartHiddenInputs');

        tbody.empty();
        hiddenInputs.empty();

        if (cart.length === 0) {
            tbody.html(`
                <tr id="emptyCartRow">
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i data-feather="package" style="width:36px;height:36px;opacity:0.3;" class="mb-1"></i><br>
                        <strong>No products added to this invoice yet.</strong><br>
                        <small>Select a dealer above and search products in the dropdown to add them here.</small>
                    </td>
                </tr>
            `);
            $('#cartItemCountBadge').text('0 Products');
            updateSummary();
            feather && feather.replace({ width: 14, height: 14 });
            return;
        }

        cart.forEach((item, index) => {
            let itemTotal = (parseFloat(item.price) || 0) * (parseInt(item.qty) || 0);

            // Product Image Cell (with click to zoom trigger)
            let prodImgHtml = (item.image && !item.image.includes('no-image.png'))
                ? `<img src="${item.image}" width="42" height="42" 
                        class="img-preview-trigger" 
                        data-preview-url="${item.image}" 
                        data-preview-title="${item.name} (Product Image)"
                        title="Click to zoom image"
                        style="border-radius:6px;object-fit:cover;border:1px solid #e5e7eb;" 
                        onerror="this.src='/images/no-image.png'">`
                : `<img src="/images/no-image.png" width="42" height="42" style="border-radius:6px;object-fit:cover;border:1px solid #e5e7eb;">`;

            // Manufacture Image Cell (with click to zoom trigger)
            let mfgImgHtml = (item.mfg_image && item.mfg_image !== '')
                ? `<img src="${item.mfg_image}" width="42" height="42" 
                        class="img-preview-trigger" 
                        data-preview-url="${item.mfg_image}" 
                        data-preview-title="${item.name} (Manufacture Blueprint)"
                        title="Click to zoom blueprint"
                        style="border-radius:6px;object-fit:cover;border:1px solid #c7d2fe;" 
                        onerror="this.src='/images/no-image.png'">`
                : `<span class="badge badge-light-secondary font-weight-normal" style="font-size:0.75rem;">N/A</span>`;

            let badgeHtml = item.is_mfg 
                ? `<span class="badge badge-light-primary ml-50" style="font-size: 0.7rem; padding: 2px 5px;">Manufacturer</span>` 
                : '';

            tbody.append(`
                <tr id="cartRow_${item.id}">
                    <td class="text-center font-weight-bold text-muted">${index + 1}</td>
                    <td>${prodImgHtml}</td>
                    <td>${mfgImgHtml}</td>
                    <td>
                        <div class="font-weight-bold text-dark">${item.name} ${badgeHtml}</div>
                    </td>
                    <td class="text-right">
                        <span class="font-weight-bold text-dark" style="font-size:0.95rem;">৳${parseFloat(item.price).toFixed(2)}</span>
                    </td>
                    <td class="text-center">
                        <input type="number" min="1" step="1" class="form-control form-control-sm text-center item-qty font-weight-bold" 
                            data-id="${item.id}" value="${item.qty}" style="width:80px; display:inline-block;">
                    </td>
                    <td class="text-right font-weight-bolder text-primary" style="font-size:0.95rem;">
                        ৳<span class="row-total">${itemTotal.toFixed(2)}</span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm p-25 remove-item" data-id="${item.id}" title="Remove item">
                            <i data-feather="trash-2" style="width:14px;height:14px;"></i>
                        </button>
                    </td>
                </tr>
            `);

            hiddenInputs.append(`
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][price]" class="hidden-price-${item.id}" value="${item.price}">
                <input type="hidden" name="items[${index}][qty]" class="hidden-qty-${item.id}" value="${item.qty}">
            `);
        });

        $('#cartItemCountBadge').text(`${cart.length} ${cart.length === 1 ? 'Product' : 'Products'}`);
        feather && feather.replace({ width: 14, height: 14 });
        updateSummary();
    }

    // Handle Quantity Changes
    $(document).on('input change', '.item-qty', function() {
        let id = $(this).data('id');
        let qty = parseInt($(this).val()) || 1;
        if (qty < 1) {
            qty = 1;
            $(this).val(1);
        }
        let item = cart.find(i => i.id == id);
        if (item) {
            item.qty = qty;
            let total = (parseFloat(item.price) || 0) * item.qty;
            $(this).closest('tr').find('.row-total').text(total.toFixed(2));
            $(`.hidden-qty-${id}`).val(item.qty);
            updateSummary();
        }
    });

    // Handle Remove Item
    $(document).on('click', '.remove-item', function() {
        let id = $(this).data('id');
        let item = cart.find(i => i.id == id);
        let name = item ? item.name : 'item';
        cart = cart.filter(i => i.id != id);
        renderCart();
        toastr.info(`Removed ${name} from order.`);
    });

    // Update Financial Totals
    function updateSummary() {
        let totalQty = 0;
        let subTotal = 0;

        cart.forEach(item => {
            let q = parseInt(item.qty) || 0;
            let p = parseFloat(item.price) || 0;
            totalQty += q;
            subTotal += (q * p);
        });

        let discount = parseFloat($('#discount').val()) || 0;
        let promotionalDiscount = calcDealerPromotionalDiscount();
        let carryingCharge = parseFloat($('#carryingCharge').val()) || 0;
        let paidAmount = parseFloat($('#paidAmount').val()) || 0;

        let grandTotal = Math.max(0, (subTotal - discount - promotionalDiscount) + carryingCharge);
        let due = grandTotal - paidAmount;

        $('#summaryTotalQty').text(`${totalQty} Pcs`);
        $('#summarySubTotal').text(`৳${subTotal.toFixed(2)}`);
        $('#summaryPromotionalDiscount').text(`৳${promotionalDiscount.toFixed(2)}`);
        $('#promotionalDiscount').val(promotionalDiscount.toFixed(2));
        $('#summaryGrandTotal').text(`৳${grandTotal.toFixed(2)}`);

        if (due <= 0) {
            $('#summaryDue').css('color', '#28c76f').text(`৳${due.toFixed(2)} (Paid)`);
        } else {
            $('#summaryDue').css('color', '#ea5455').text(`৳${due.toFixed(2)}`);
        }
    }
</script>
@endpush
