@extends('frontend.layouts.app')
@section('content')
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.html" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                <span></span> Shop
                <span></span> Cart
            </div>
        </div>
    </div>
    <div class="container mb-80 mt-50">
        <div class="row">
            <div class="col-lg-8 mb-40">
                <div class="d-flex justify-content-between">
                    <h6 class="text-body">There are <span class="text-brand cart-page-count">{{ $cartItems->sum('quantity') }}</span> products in your cart</h6>
                    <h6 class="text-body text-danger"><a href="javascript:void(0)" class="text-muted clear-cart-btn"><i class="fi-rs-trash mr-5 text-danger"></i>Clear Cart</a></h6>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8">
                <div class="table-responsive shopping-summery">
                    <table class="table table-wishlist">
                        <thead>
                        <tr class="main-heading">
                            <th class="custome-checkbox start pl-30">#</th>
                            <th scope="col">Unit Price</th>
                            <th scope="col" colspan="2">Product</th>
                            <th scope="col">Unit Price</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Subtotal</th>
                            <th scope="col" class="end">Remove</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($cartItems as $index => $cItem)
                        @php
                            $imageUrl = $cItem->product->featured_image ? asset(config('imagepath.product') . $cItem->product->featured_image) : asset('images/no-image.png');
                            $detailUrl = route('product.details', encrypt($cItem->product->id));
                        @endphp
                        <tr class="pt-30" data-cart-id="{{ $cItem->id }}" data-price="{{ $cItem->price }}">
                            <td class="custome-checkbox pl-30">
                                {{ $index+1 }}
                            </td>
                            <td class="image product-thumbnail pt-40"><a href="{{ $detailUrl }}"><img style="max-width: 80px!important;" src="{{ $imageUrl }}" alt="#"></a></td>
                            <td class="product-des product-name" style="max-width: 150px;">
                                <h6 class="mb-5"><a class="product-name mb-10 text-heading" href="{{ $detailUrl }}" style="display: block; white-space: normal; word-wrap: break-word;">{{ \Illuminate\Support\Str::limit($cItem->product->name, 45) }}</a></h6>
                                @if($cItem->product->variations && $cItem->product->variations->count() > 0)
                                    @php
                                        $groupedVariations = $cItem->product->variations->groupBy('variation_id');
                                    @endphp
                                    <div class="mt-2">
                                        @foreach($groupedVariations as $varId => $vars)
                                            @php $varName = strtolower($vars->first()->variation->name); @endphp
                                            @if(in_array($varName, ['size', 'color']))
                                                <select class="form-control form-control-sm variation-update mt-1" data-cart-id="{{ $cItem->id }}" data-type="{{ $varName }}" style="padding: 2px 5px; height: auto; font-size: 12px; width: 100px;">
                                                    <option value="">{{ ucfirst($varName) }}</option>
                                                    @foreach($vars as $var)
                                                        @if($var->variationValue)
                                                            <option value="{{ $var->variationValue->value }}" {{ $cItem->{$varName} == $var->variationValue->value ? 'selected' : '' }}>{{ $var->variationValue->value }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="price" data-title="Price">
                                <h4 class="text-body" style="white-space: nowrap;">{{ $settings->currency_symbol ?? 'TK' }} {{ number_format($cItem->price, 2) }} </h4>
                            </td>
                            <td class="text-center detail-info" data-title="Stock">
                                <div class="detail-extralink mr-15">
                                    <div class="detail-qty border radius mt-1">
                                        <input type="text" name="quantity[{{ $cItem->id }}]" class="qty-val cart-page-qty-val" value="{{ $cItem->quantity }}" min="1">
                                    </div>
                                </div>
                            </td>
                            <td class="price" data-title="Subtotal">
                                <h4 class="text-brand item-subtotal" style="white-space: nowrap;">{{ $settings->currency_symbol ?? 'TK' }} {{ number_format($cItem->price * $cItem->quantity, 2) }} </h4>
                            </td>
                            <td class="action text-center" data-title="Remove"><a href="javascript:void(0)" class="text-body remove-cart-item-page" data-id="{{ $cItem->id }}" data-product-id="{{ $cItem->product_id }}"><i class="fi-rs-trash text-danger"></i></a></td>
                        </tr>
                        @endforeach
                        @if($cartItems->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center">Your cart is empty!</td>
                        </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
                <div class="divider-2 mb-30"></div>
                <div class="cart-action d-flex justify-content-between">
                    <a href="{{route('shop')}}" class="btn"><i class="fi-rs-arrow-left mr-10"></i>Continue Shopping</a>
                    <div class="col-lg-5">
                            <form id="coupon-form" action="#">
                                <div class="d-flex justify-content-between">
                                    <input class="font-medium mr-15 coupon" id="coupon_code_input" name="Coupon" placeholder="Enter Your Coupon" value="{{ session()->has('coupon') ? session('coupon')['code'] : '' }}">
                                    <button class="btn"><i class="fi-rs-label mr-10"></i>Apply</button>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="border p-md-4 cart-totals ml-30">
                    <div class="table-responsive">
                        <table class="table no-border">
                            <tbody>
                            <tr>
                                <td class="cart_total_label">
                                    <h6 class="text-muted">Subtotal</h6>
                                </td>
                                <td class="cart_total_amount">
                                    <h4 class="text-brand text-end" id="cart-page-subtotal">{{ $settings->currency_symbol ?? 'TK' }} {{ number_format($cartTotal, 2) }}</h4>
                                </td>
                            </tr>
                            <tr>
                                <td class="cart_total_label">
                                    <h6 class="text-muted">Delivery for</h6>
                                </td>
                                <td class="cart_total_amount">
                                    <select id="shipping_area" class="form-control" style="padding: 5px; height: auto;">
                                        <option value="{{ $settings->inside_dhaka ?? 0 }}">Inside Dhaka ({{ $settings->currency_symbol ?? 'TK' }} {{ $settings->inside_dhaka ?? 0 }})</option>
                                        <option value="{{ $settings->subcity ?? 0 }}">Sub City ({{ $settings->currency_symbol ?? 'TK' }} {{ $settings->subcity ?? 0 }})</option>
                                        <option value="{{ $settings->outside_dhaka ?? 0 }}">Outside Dhaka ({{ $settings->currency_symbol ?? 'TK' }} {{ $settings->outside_dhaka ?? 0 }})</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="cart_total_label">
                                    <h6 class="text-muted">Shipping</h6>
                                </td>
                                <td class="cart_total_amount">
                                    <h5 class="text-heading text-end" id="cart-page-shipping">{{ $settings->currency_symbol ?? 'TK' }} {{ $settings->inside_dhaka ?? 50 }}</h5>
                                </td>
                            </tr>
                            <tr>
                                <td class="cart_total_label">
                                    <h6 class="text-muted">Discount</h6>
                                </td>
                                <td class="cart_total_amount">
                                    <h5 class="text-heading text-end" id="cart-page-discount" data-amount="{{ session()->has('coupon') ? (session('coupon')['type'] == 'fixed' ? session('coupon')['amount'] : 'percent_'.session('coupon')['amount']) : '0' }}">{{ $settings->currency_symbol ?? 'TK' }} 0.00</h5>
                                </td>
                            </tr>
                            <tr>
                                <td class="cart_total_label">
                                    <h6 class="text-muted">Total</h6>
                                </td>
                                <td class="cart_total_amount">
                                    <h4 class="text-brand text-end" id="cart-page-total">{{ $settings->currency_symbol ?? 'TK' }} {{ number_format($cartTotal + ($settings->inside_dhaka ?? 50), 2) }}</h4>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('checkout') }}" class="btn mb-20 w-100">Proceed To CheckOut<i class="fi-rs-sign-out ml-15"></i></a>
                </div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    let currency = "{{ $settings->currency_symbol ?? 'TK' }}";

                    function updateCartTotals() {
                        let subtotal = 0;
                        $('.item-subtotal').each(function() {
                            let text = $(this).text().replace(currency, '').replace(/,/g, '').trim();
                            subtotal += parseFloat(text) || 0;
                        });

                        let shipping = parseFloat($('#shipping_area').val()) || 0;
                        let discountData = $('#cart-page-discount').attr('data-amount') || '0';
                        let discount = 0;

                        if (discountData.toString().includes('percent_')) {
                            let percentage = parseFloat(discountData.toString().replace('percent_', ''));
                            discount = (subtotal * percentage) / 100;
                        } else {
                            discount = parseFloat(discountData);
                        }

                        // Prevent discount from being more than subtotal
                        if (discount > subtotal) {
                            discount = subtotal;
                        }

                        let total = subtotal + shipping - discount;

                        $('#cart-page-subtotal').text(currency + ' ' + subtotal.toFixed(2));
                        $('#cart-page-shipping').text(currency + ' ' + shipping.toFixed(2));
                        $('#cart-page-discount').text(currency + ' ' + discount.toFixed(2));
                        $('#cart-page-total').text(currency + ' ' + total.toFixed(2));
                    }

                    // Run once on load
                    updateCartTotals();

                    $('#shipping_area').on('change', function() {
                        updateCartTotals();
                    });

                    $('#coupon-form').on('submit', function(e) {
                        e.preventDefault();
                        let code = $('#coupon_code_input').val();
                        if (!code) {
                            toastr.error('Please enter a coupon code.');
                            return;
                        }

                        $.ajax({
                            url: "{{ route('cart.apply-coupon') }}",
                            type: "POST",
                            data: {
                                coupon_code: code,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    toastr.success(response.message);
                                    let cpn = response.coupon;
                                    if (cpn.type === 'fixed') {
                                        $('#cart-page-discount').attr('data-amount', cpn.amount);
                                    } else {
                                        $('#cart-page-discount').attr('data-amount', 'percent_' + cpn.amount);
                                    }
                                    couponApplied = true;
                                    updateCartTotals();
                                } else {
                                    toastr.error(response.message);
                                    $('#cart-page-discount').attr('data-amount', '0');
                                    couponApplied = false;
                                    updateCartTotals();
                                }
                            }
                        });
                    });

                    let couponApplied = {{ session()->has('coupon') ? 'true' : 'false' }};
                    $('#coupon_code_input').on('input', function() {
                        if (couponApplied) {
                            couponApplied = false;
                            $('#cart-page-discount').attr('data-amount', '0');
                            updateCartTotals();
                            $.ajax({
                                url: "{{ route('cart.remove-coupon') }}",
                                type: "POST",
                                data: { _token: '{{ csrf_token() }}' }
                            });
                        }
                    });

                    $('.variation-update').on('change', function() {
                        let select = $(this);
                        let cartId = select.data('cart-id');
                        let type = select.data('type');
                        let value = select.val();

                        $.ajax({
                            url: "{{ route('cart.update-variation') }}",
                            type: "POST",
                            data: {
                                cart_id: cartId,
                                type: type,
                                value: value,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if(response.status === 'success') {
                                    toastr.success(response.message);
                                } else {
                                    toastr.error('Failed to update variation');
                                }
                            }
                        });
                    });

                    function syncCartQuantity(cartId, quantity) {
                        $.ajax({
                            url: "{{ route('cart.update') }}",
                            type: "POST",
                            data: {
                                cart_id: cartId,
                                quantity: quantity,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if(response.status === 'success') {
                                    if($('.cart-count').length) {
                                        $('.cart-count').text(response.cart_count);
                                        $('.cart-page-count').text(response.cart_count);
                                    }
                                    if($('.dynamic-cart-list').length) {
                                        $('.dynamic-cart-list').html(response.cart_html);
                                    }
                                    if($('.cart-total-amount').length) {
                                        $('.cart-total-amount').text(response.cart_total);
                                    }
                                }
                            }
                        });
                    }

                    $(document).on('change keyup', '.cart-page-qty-val', function(e) {
                        var $input = $(this);
                        var val = parseInt($input.val()) || 1;
                        if (val < 1) val = 1;

                        let tr = $input.closest('tr');
                        let price = parseFloat(tr.data('price'));
                        let cartId = tr.data('cart-id');
                        let newSubtotal = price * val;
                        tr.find('.item-subtotal').text(currency + ' ' + newSubtotal.toFixed(2));

                        updateCartTotals();

                        // Debounce the ajax call slightly to prevent spamming if typing fast
                        clearTimeout($input.data('timer'));
                        $input.data('timer', setTimeout(function() {
                            syncCartQuantity(cartId, val);
                        }, 500));
                    });

                    $(document).on('click', '.remove-cart-item-page', function(e) {
                        e.preventDefault();
                        let btn = $(this);
                        let cart_id = btn.data('id');
                        let _token = '{{ csrf_token() }}';

                        $.ajax({
                            url: "{{ route('cart.remove') }}",
                            type: "POST",
                            data: {
                                cart_id: cart_id,
                                _token: _token
                            },
                            success: function(response) {
                                if(response.status === 'success') {
                                    if(typeof toastr !== 'undefined') {
                                        toastr.success(response.message);
                                    }

                                    // Remove the row
                                    btn.closest('tr').remove();
                                    updateCartTotals();

                                    // Optionally update header cart using response
                                    if($('.cart-count').length) {
                                        $('.cart-count').text(response.cart_count);
                                    }
                                    if($('.dynamic-cart-list').length) {
                                        $('.dynamic-cart-list').html(response.cart_html);
                                    }
                                    if($('.cart-total-amount').length) {
                                        $('.cart-total-amount').text(response.cart_total);
                                    }

                                    let product_id = btn.data('product-id');
                                    $(`.add-to-cart-btn[data-id="${product_id}"]`).removeClass('disabled').css('pointer-events', 'auto').html('<i class="fi-rs-shopping-cart mr-5"></i>Add');
                                }
                            }
                        });
                    });

                    $(document).on('click', '.clear-cart-btn', function(e) {
                        e.preventDefault();
                        if(confirm('Are you sure you want to clear your cart?')) {
                            $.ajax({
                                url: "{{ route('cart.clear') }}",
                                type: "POST",
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if(response.status === 'success') {
                                        if(typeof toastr !== 'undefined') {
                                            toastr.success('Cart cleared successfully.');
                                        }

                                        // Empty the table and add empty message
                                        $('table.table-wishlist tbody').html('<tr><td colspan="7" class="text-center">Your cart is empty!</td></tr>');
                                        updateCartTotals();

                                        if($('.cart-count').length) {
                                            $('.cart-count').text(response.cart_count);
                                            $('.cart-page-count').text(response.cart_count);
                                        }
                                        if($('.dynamic-cart-list').length) {
                                            $('.dynamic-cart-list').html(response.cart_html);
                                        }
                                        if($('.cart-total-amount').length) {
                                            $('.cart-total-amount').text(response.cart_total);
                                        }

                                        $('.add-to-cart-btn.disabled').removeClass('disabled').css('pointer-events', 'auto').html('<i class="fi-rs-shopping-cart mr-5"></i>Add');
                                    }
                                }
                            });
                        }
                    });
                });
            </script>
        </div>
    </div>
@endsection
