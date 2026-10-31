@extends('layouts.dealer')
@section('title', 'POS System')

@section('content')
<div class="content-header row">
</div>
<div class="content-body">
    <div class="row">
        <!-- Main POS Area (Products) -->
        <div class="col-xl-7 col-lg-7">
            <div class="card shadow-sm" style="border-radius: 10px; min-height: 85vh;">
                <div class="card-body p-2">
                    <!-- Search & Filter -->
                    <form id="filter-form" class="row mb-2 px-1">
                        <div class="col-md-5 mb-1 mb-md-0 px-1">
                            <input type="text" name="search" id="search-input" class="form-control bg-light" placeholder="Search product..." style="border:1px solid #eee; border-radius: 6px;">
                        </div>
                        <div class="col-md-3 mb-1 mb-md-0 px-1">
                            <select name="category_id" id="category-select" class="form-control select2 bg-light" style="width:100%;">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 px-1">
                            <select name="subcategory_id" id="subcategory-select" class="form-control select2 bg-light" style="width:100%;">
                                <option value="">All Subcategories</option>
                            </select>
                        </div>
                    </form>

                    <hr class="mt-1 mb-2">

                    <!-- Product Grid -->
                    <div id="product-scroll-area" style="overflow-y: auto; max-height: calc(85vh - 100px); overflow-x: hidden;">
                        <div class="row match-height" id="product-grid">
                            <!-- Products will be loaded here via AJAX -->
                        </div>
                        <div id="load-more-btn-container" class="text-center mt-2 mb-3" style="display: none;">
                            <button id="load-more-btn" class="btn btn-outline-dark" onclick="loadProducts(true)" style="border-radius: 8px; padding: 8px 24px; font-weight: bold;">
                                Load More
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Area -->
        <div class="col-xl-5 col-lg-5">
            <div class="card shadow-sm" style="border-radius: 10px; min-height: 85vh; display: flex; flex-direction: column;">
                <div class="card-header p-2 border-bottom d-flex justify-content-between align-items-center" style="background-color: #FDFAF5; border-color: #efeae2 !important; border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0 text-dark font-weight-bolder d-flex align-items-center">
                        <span class="fieldora-title-badge me-2" style="display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: #111C38; color: #ffffff; border-radius: 6px; margin-right: 8px;">
                            <i data-feather="shopping-bag" style="width: 14px; height: 14px; stroke: #ffffff;"></i>
                        </span>
                        <span>Current Order</span>
                    </h5>
                    <button class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 4px 12px; font-size: 0.82rem; font-weight: 600;" onclick="clearCart()">
                        <i data-feather="trash-2" style="width: 13px; margin-right: 4px;"></i> Clear
                    </button>
                </div>

                <div class="card-body p-0 d-flex flex-column" style="flex: 1; overflow: hidden; background-color: #FDFAF5;">
                    <div class="table-responsive" style="flex: 1; overflow-y: auto; max-height: calc(85vh - 280px);">
                        <table class="table table-borderless text-center align-middle mb-0" id="cart-table">
                            <thead class="sticky-top shadow-sm" style="font-size: 0.8rem; background-color: #001f3f !important; color: #ffffff !important; text-transform: uppercase;">
                                <tr>
                                    <th class="text-left py-2 px-3" style="width: 40%; background-color: #001f3f !important; color: #ffffff !important; font-weight: 700; font-size: 13px; letter-spacing: 0.5px; border-top-left-radius: 6px;">PRODUCT</th>
                                    <th class="py-2 text-center" style="width: 27%; background-color: #001f3f !important; color: #ffffff !important; font-weight: 700; font-size: 13px; letter-spacing: 0.5px;">QUANTITY</th>
                                    <th class="py-2 text-center" style="width: 23%; background-color: #001f3f !important; color: #ffffff !important; font-weight: 700; font-size: 13px; letter-spacing: 0.5px;">SUBTOTAL</th>
                                    <th class="py-2 text-center" style="width: 10%; background-color: #001f3f !important; color: #ffffff !important; font-weight: 700; font-size: 13px; letter-spacing: 0.5px; border-top-right-radius: 6px;">ACTION</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items" style="background-color: #FDFAF5;">
                                <tr id="empty-cart-row">
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i data-feather="shopping-cart" style="width: 40px; height: 40px; opacity: 0.2; margin-bottom: 10px;"></i><br>
                                        Cart is empty
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Order Summary Area -->
                    <div class="p-2 border-top" style="background-color: #FDFAF5; border-color: #efeae2 !important; border-radius: 0 0 10px 10px;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold" style="font-size: 0.9rem; color: #4a5568;">Sub Total</span>
                            <div class="text-primary font-weight-bolder text-right" style="font-size: 1rem;" id="cart-subtotal">
                                ৳ 0.00
                            </div>
                        </div>
                        <div class="mb-1">
                            <textarea class="form-control bg-white" id="cart-notes" rows="2" placeholder="Order Note..." style="border-radius: 8px; border: 1px solid #efeae2;"></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-1 mb-2" style="border-color: #efeae2 !important;">
                            <h4 class="font-weight-bolder mb-0 text-dark">Total</h4>
                            <h4 class="font-weight-bolder mb-0" style="font-size: 1.4rem; color: #111C38;" id="cart-total">৳ 0.00</h4>
                        </div>
                        <button class="btn btn-block font-weight-bolder py-2 place-order-btn" onclick="placeOrder()">
                            <i data-feather="check-circle" style="margin-right: 5px; margin-top: -2px;"></i> Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('admin/pos/assets/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/pos/assets/css/fontawesome.min.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* 5 products per row on desktop */
.col-pos-5 {
    position: relative;
    width: 100%;
}
@media (min-width: 992px) {
    .col-pos-5 {
        flex: 0 0 20% !important;
        max-width: 20% !important;
    }
}
@media (min-width: 768px) and (max-width: 991.98px) {
    .col-pos-5 {
        flex: 0 0 33.333333% !important;
        max-width: 33.333333% !important;
    }
}
@media (max-width: 767.98px) {
    .col-pos-5 {
        flex: 0 0 50% !important;
        max-width: 50% !important;
    }
}

.product-card:hover {
    border-color: #001f3f !important;
    box-shadow: 0 4px 15px 0 rgba(0, 31, 63, 0.15) !important;
    transform: translateY(-2px);
}
.product-card {
    transition: all 0.2s ease-in-out;
    position: relative !important;
    overflow: hidden !important;
}
.product-card.stock__out {
    cursor: not-allowed !important;
}
.product-card.active {
    border-color: #001f3f !important;
    background-color: #f0f2f5;
    border-width: 2px;
}

/* Diagonal Corner Ribbon Badge (Top-Left) */
.corner-ribbon-wrap {
    width: 85px;
    height: 85px;
    overflow: hidden;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 12;
    pointer-events: none;
    border-top-left-radius: 12px;
}

.corner-ribbon-wrap .corner-ribbon {
    position: absolute;
    display: block;
    width: 125px;
    padding: 3px 0;
    background: #f5365c;
    background: linear-gradient(135deg, #f5365c 0%, #e11d48 100%);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.25);
    color: #ffffff !important;
    font-size: 9.5px;
    font-weight: 800;
    text-transform: uppercase;
    text-align: center;
    letter-spacing: 0.8px;
    line-height: 14px;
    top: 17px;
    left: -33px;
    transform: rotate(-45deg);
    -webkit-transform: rotate(-45deg);
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
    user-select: none;
    pointer-events: none;
}

.stock-out-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.55);
    z-index: 8;
    border-radius: 12px;
    pointer-events: none;
}
.select2-container--default .select2-selection--single {
    border: 1px solid #eee !important;
    border-radius: 6px !important;
    height: 38px !important;
    position: relative !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px !important;
    padding-right: 30px !important;
}
/* Hide the broken default arrow — theme CSS corrupts the <b> tag */
.select2-container--default .select2-selection--single .select2-selection__arrow {
    display: none !important;
}
/* Draw a clean arrow using ::after pseudo-element instead */
.select2-container--default .select2-selection--single::after {
    content: '' !important;
    position: absolute !important;
    right: 10px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 0 !important;
    height: 0 !important;
    border-left: 5px solid transparent !important;
    border-right: 5px solid transparent !important;
    border-top: 6px solid #888 !important;
    pointer-events: none !important;
    z-index: 1 !important;
}

/* POS Cart Table Styling - Exact match to Admin POS */
#cart-table {
    background-color: #FDFAF5 !important;
    border-collapse: collapse !important;
    width: 100% !important;
}

#cart-table thead tr th {
    background-color: #001f3f !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    letter-spacing: 0.5px !important;
    text-transform: uppercase !important;
    padding: 12px 14px !important;
    border: none !important;
    vertical-align: middle !important;
}

#cart-table thead tr th:first-child {
    border-top-left-radius: 6px !important;
}

#cart-table thead tr th:last-child {
    border-top-right-radius: 6px !important;
}

#cart-table tbody tr {
    background-color: #FDFAF5 !important;
    border-bottom: 1px solid #efeae2 !important;
    transition: background-color 0.15s ease;
}

#cart-table tbody tr:hover {
    background-color: #f7f1e4 !important;
}

#cart-table tbody tr td {
    padding: 10px 12px !important;
    vertical-align: middle !important;
    border-top: none !important;
    border-bottom: 1px solid #efeae2 !important;
    background: transparent !important;
}

/* Product Info */
.cart__product_title {
    font-size: 13px !important;
    font-weight: 600 !important;
    color: #333333 !important;
    line-height: 1.3 !important;
}

.product_cart_price {
    font-size: 12.5px !important;
    line-height: 1.2 !important;
}

.product_price_amount {
    color: #28c76f !important;
    font-weight: 700 !important;
}

/* Quantity Stepper (Exact Admin POS input-group) */
#cart-table .input-group {
    display: inline-flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    align-items: stretch !important;
    justify-content: center !important;
    width: 112px !important;
    margin: 0 auto !important;
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    overflow: hidden !important;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
    height: 32px !important;
}

#cart-table .input-group .add_btn {
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    max-width: 32px !important;
    flex: 0 0 32px !important;
    background-color: #001f3f !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 0 !important;
    padding: 0 !important;
    margin: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 13px !important;
    cursor: pointer !important;
    transition: background-color 0.15s ease !important;
}

#cart-table .input-group .add_btn:hover {
    background-color: #0b2b52 !important;
}

#cart-table .input-group .add_btn i {
    color: #ffffff !important;
    font-size: 12px !important;
}

#cart-table .input-group .quantity_input {
    width: 48px !important;
    height: 32px !important;
    flex: 1 1 auto !important;
    border: none !important;
    border-left: 1px solid #ced4da !important;
    border-right: 1px solid #ced4da !important;
    border-radius: 0 !important;
    text-align: center !important;
    font-weight: 600 !important;
    font-size: 13.5px !important;
    color: #333333 !important;
    background: #ffffff !important;
    padding: 0 !important;
    margin: 0 !important;
    -moz-appearance: textfield !important;
}

#cart-table .input-group .quantity_input::-webkit-outer-spin-button,
#cart-table .input-group .quantity_input::-webkit-inner-spin-button {
    -webkit-appearance: none !important;
    margin: 0 !important;
}

/* Subtotal text */
.product_item_subtotal {
    font-size: 13.5px !important;
    font-weight: 600 !important;
    color: #333333 !important;
}

/* Action button: Solid Red square with white trash (Admin POS exact match) */
.cart_actionBtn.misty-color {
    width: 32px !important;
    height: 32px !important;
    min-width: 32px !important;
    max-width: 32px !important;
    background-color: #f5365c !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 6px !important;
    padding: 0 !important;
    margin: 0 auto !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

.cart_actionBtn.misty-color:hover {
    background-color: #d9254c !important;
    transform: scale(1.05) !important;
}

.cart_actionBtn.misty-color i {
    color: #ffffff !important;
    font-size: 13px !important;
}

.place-order-btn {
    border-radius: 8px;
    font-size: 1.05rem;
    background: #001f3f;
    color: white !important;
    border: none;
    box-shadow: 0 4px 12px rgba(0, 31, 63, 0.25);
    transition: all 0.2s ease;
    letter-spacing: 0.5px;
}
.place-order-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 31, 63, 0.35);
    background: #0b2b52;
}
/* Scrollbar styling */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
::-webkit-scrollbar-track {
    background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let cart = [];
    let currentOffset = 0;
    let currentLimit = 35;
    let isLoading = false;

    function loadProducts(append = false) {
        if (isLoading) return;
        isLoading = true;

        if (!append) {
            currentOffset = 0;
            currentLimit = 35;
            $('#product-grid').html('<div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 300px;"><div class="spinner-border" style="color: #0F172A; width: 2.5rem; height: 2.5rem;" role="status"><span class="sr-only">Loading...</span></div></div>');
            $('#load-more-btn-container').hide();
        } else {
            $('#load-more-btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...').prop('disabled', true);
        }

        const data = {
            search: $('#search-input').val(),
            category_id: $('#category-select').val(),
            subcategory_id: $('#subcategory-select').val(),
            offset: currentOffset,
            limit: currentLimit
        };

        $.ajax({
            url: '{{ route("dealer.pos.products") }}',
            type: 'GET',
            data: data,
            success: function(response) {
                if (!append) {
                    $('#product-grid').html(response.html);
                } else {
                    $('#product-grid').append(response.html);
                }

                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }

                if (response.has_more) {
                    $('#load-more-btn-container').show();
                    $('#load-more-btn').html('Load More').prop('disabled', false);
                    currentOffset += currentLimit;
                    currentLimit = 15; // Subsequent loads (3 rows of 5)
                } else {
                    $('#load-more-btn-container').hide();
                }

                isLoading = false;
            },
            error: function() {
                if (!append) {
                    $('#product-grid').html('<div class="col-12 text-center py-5 text-danger">Failed to load products</div>');
                } else {
                    $('#load-more-btn').html('Load More').prop('disabled', false);
                    alert('Failed to load more products.');
                }
                isLoading = false;
            }
        });
    }

    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();

        // Initial load
        loadProducts();

        // Filter events
        $('#search-input').on('keyup', function() {
            clearTimeout($.data(this, 'timer'));
            var wait = setTimeout(function() {
                loadProducts();
            }, 500);
            $(this).data('timer', wait);
        });

        $('#category-select').on('change', function() {
            let catId = $(this).val();
            let subSelect = $('#subcategory-select');

            subSelect.html('<option value="">All Subcategories</option>');

            if(catId) {
                $.ajax({
                    url: '{{ url("dealer/pos/subcategories") }}/' + catId,
                    type: 'GET',
                    success: function(response) {
                        response.forEach(function(sub) {
                            subSelect.append(`<option value="${sub.id}">${sub.name}</option>`);
                        });
                        // Trigger select2 update and load products
                        subSelect.trigger('change.select2');
                        loadProducts();
                    }
                });
            } else {
                subSelect.trigger('change.select2');
                loadProducts();
            }
        });

        $('#subcategory-select').on('change', function() {
            loadProducts();
        });
    });

    // Cart Logic
    function addToCart(element) {
        let id = $(element).data('id');
        let title = $(element).data('title');
        let price = parseFloat($(element).data('price'));
        let image = $(element).data('image') || $(element).find('img').attr('src') || '';

        if (isNaN(price) || price <= 0) {
            if (typeof toastr !== 'undefined') toastr.warning('This product cannot be ordered because price is not set.');
            else alert('This product cannot be ordered because price is not set.');
            return;
        }

        let existing = cart.find(item => item.id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id, title, price, image, qty: 1 });
        }

        // Add active class animation
        $(element).addClass('active');
        setTimeout(() => $(element).removeClass('active'), 200);

        renderCart();
    }

    function updateQty(id, change) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty += change;
            if (item.qty <= 0) {
                removeFromCart(id);
            } else {
                renderCart();
            }
        }
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function renderCart() {
        const tbody = $('#cart-items');
        tbody.empty();

        let subtotal = 0;

        if (cart.length === 0) {
            tbody.html('<tr id="empty-cart-row"><td colspan="4" class="text-center py-5 text-muted"><i data-feather="shopping-cart" style="width: 40px; height: 40px; opacity: 0.2; margin-bottom: 10px;"></i><br>Cart is empty</td></tr>');
        } else {
            cart.forEach(item => {
                let total = item.qty * item.price;
                subtotal += total;

                let imgHtml = item.image ?
                    `<img src="${item.image}" alt="${item.title}" style="width: 42px; height: 42px; object-fit: cover; border-radius: 6px; margin-right: 10px; border: 1px solid #e2e8f0; flex-shrink: 0;">` :
                    `<div style="width: 42px; height: 42px; background: #f8f9fa; border-radius: 6px; margin-right: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border: 1px solid #e2e8f0;">
                        <i class="fa-regular fa-image text-muted" style="font-size: 18px; opacity: 0.4;"></i>
                    </div>`;

                tbody.append(`
                    <tr class="cart__product" data-id="${item.id}" style="background-color: #FDFAF5;">
                        <td style="width: 40%; vertical-align: middle;" class="text-left py-2 px-3">
                            <div class="d-flex align-items-center">
                                ${imgHtml}
                                <div class="overflow-hidden" style="max-width: 140px;">
                                    <h4 class="cart__product_title text-truncate mb-0" style="font-size: 13px; font-weight: 600; color: #333333;" title="${item.title}">${item.title}</h4>
                                    <p class="product_cart_price mb-0">
                                        <span class="product_price_amount text-success font-weight-bold" style="font-size: 13px; color: #28c76f !important;">৳ ${item.price.toFixed(2)}</span>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td style="width: 27%; vertical-align: middle;" class="text-center py-2">
                            <div class="input-group">
                                <button class="input-group-text rounded-0 bg-navy add_btn decress_quantity" type="button" onclick="updateQty(${item.id}, -1)">
                                    <i class="fa-solid fa-minus text-white"></i>
                                </button>
                                <input class="form-control text-center quantity_input" type="number" min="1" value="${item.qty}" onchange="setQty(${item.id}, this.value)">
                                <button class="input-group-text rounded-0 bg-navy add_btn incress_quantity" type="button" onclick="updateQty(${item.id}, 1)">
                                    <i class="fa-solid fa-plus text-white"></i>
                                </button>
                            </div>
                        </td>
                        <td style="width: 23%; vertical-align: middle;" class="text-center py-2">
                            <p class="product_item_subtotal text-center mb-0" style="font-size: 13.5px; font-weight: 600; color: #333333;">
                              ৳ <span class="subtotal__amount">${total.toFixed(2)}</span>
                            </p>
                        </td>
                        <td style="width: 10%; vertical-align: middle;" class="text-center py-2">
                            <button class="cart_actionBtn btn_main misty-color remove_item" type="button" onclick="removeFromCart(${item.id})" title="Remove item">
                                <i class="fa-solid fa-trash text-white"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        if (feather) { feather.replace({ width: 14, height: 14 }); }

        $('#cart-subtotal').text('৳ ' + subtotal.toFixed(2));
        $('#cart-total').text('৳ ' + subtotal.toFixed(2));
    }

    function setQty(id, value) {
        let qty = parseInt(value);
        if (isNaN(qty) || qty < 1) {
            qty = 1;
        }
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty = qty;
            renderCart();
        }
    }

    function placeOrder() {
        if(cart.length === 0) {
            if (typeof toastr !== 'undefined') toastr.error('Cart is empty!');
            else alert('Cart is empty!');
            return;
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Place Order?',
                text: "Are you sure you want to place this order request?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0F172A',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Place Order'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitOrder();
                }
            });
        } else {
            if(confirm("Are you sure you want to place this order request?")) {
                submitOrder();
            }
        }
    }

    function submitOrder() {
        // Format items for backend
        const items = cart.map(item => ({
            product_id: item.id,
            requested_qty: item.qty
        }));
        const note = $('#cart-notes').val();

        $.ajax({
            url: '{{ route("dealer.pos.order-request") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                items: items,
                note: note
            },
            success: function(response) {
                if (response.status === 'success') {
                    if (typeof toastr !== 'undefined') toastr.success(response.message);
                    else alert(response.message);

                    cart = [];
                    $('#cart-notes').val('');
                    renderCart();
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(response.message);
                    else alert(response.message);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to submit order request.';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') toastr.error(errorMsg);
                else alert(errorMsg);
            }
        });
    }
</script>
@endpush
