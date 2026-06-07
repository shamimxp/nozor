@extends('frontend.layouts.app')
@section('content')
    <main class="main">
        <div class="page-header breadcrumb-wrap">
            <div class="container">
                <div class="breadcrumb">
                    <a href="{{Url('/')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                    <span></span> Shop
                    <span></span> Checkout
                </div>
            </div>
        </div>
        <div class="container mb-80 mt-50">
            <div class="row">
                <div class="col-lg-8 mb-40">
                    <div class="d-flex justify-content-between">
                        <h6 class="text-body">There are <span class="text-brand">{{ $cartItems->sum('quantity') }}</span> products in your cart</h6>
                    </div>
                </div>
            </div>
            <form method="post" id="checkout-form" action="{{ route('place.order') }}">
                @csrf
                <div class="row">
                    <div class="col-lg-7">
                        <div class="row">
                            <h4 class="mb-30">Billing Details</h4>
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <input type="text" required name="name" placeholder="Full name *" value="{{ auth()->check() ? auth()->user()->name : '' }}">
                                </div>
                                <div class="form-group col-lg-6">
                                    <input type="text" required name="phone" placeholder="Phone *" value="{{ auth()->check() ? auth()->user()->phone : '' }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-lg-12">
                                    <input type="text" name="billing_address" required placeholder="Address *">
                                </div>
                            </div>
                            <div class="form-group mb-30">
                                <textarea name="note" rows="5" placeholder="Additional information"></textarea>
                            </div>
                    </div>

                    <div class="payment mt-30">
                        <h4 class="mb-30">Payment</h4>
                        <div class="payment_option">
                            <div class="custome-radio">
                                <input class="form-check-input" required type="radio" name="payment_method" id="exampleRadios4" value="cod" checked>
                                <label class="form-check-label" for="exampleRadios4" data-bs-toggle="collapse" data-target="#checkPayment" aria-controls="checkPayment">Cash on delivery</label>
                            </div>
                        </div>
                        <div class="payment-logo d-flex mt-20">
                            <img class="mr-15" src="{{ asset('web/assets/imgs/theme/icons/payment-paypal.svg') }}" alt="">
                            <img class="mr-15" src="{{ asset('web/assets/imgs/theme/icons/payment-visa.svg') }}" alt="">
                            <img class="mr-15" src="{{ asset('web/assets/imgs/theme/icons/payment-master.svg') }}" alt="">
                            <img src="{{ asset('web/assets/imgs/theme/icons/payment-zapper.svg') }}" alt="">
                        </div>
                        <button type="submit" class="btn btn-fill-out btn-block mt-30">Place an Order<i class="fi-rs-sign-out ml-15"></i></button>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="border p-40 cart-totals ml-30 mb-50">
                        <div class="d-flex align-items-end justify-content-between mb-30">
                            <h4>Your Order</h4>
                            <h6 class="text-muted">Subtotal</h6>
                        </div>
                        <div class="divider-2 mb-30"></div>
                        <div class="table-responsive order_table checkout">
                            <table class="table no-border">
                                <tbody>
                                @foreach($cartItems as $cItem)
                                    @php
                                        $imageUrl = $cItem->product->featured_image ? asset(config('imagepath.product') . $cItem->product->featured_image) : asset('images/no-image.png');
                                        $detailUrl = route('product.details', encrypt($cItem->product->id));
                                    @endphp
                                <tr>
                                    <td class="image product-thumbnail"><img style="max-width: 70px!important;" src="{{ $imageUrl }}" alt="#"></td>
                                    <td>
                                        <h6 class="w-160 mb-5"><a href="{{ $detailUrl }}" class="text-heading" style="display: block; white-space: normal; word-wrap: break-word;">{{ \Illuminate\Support\Str::limit($cItem->product->name, 45) }}</a></h6>
                                        @if($cItem->size || $cItem->color)
                                            <div class="text-muted font-small">
                                                @if($cItem->size) Size: {{ $cItem->size }} <br> @endif
                                                @if($cItem->color) Color: {{ $cItem->color }} @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <h6 class="text-muted pl-20 pr-20">x {{ $cItem->quantity }}</h6>
                                    </td>
                                    <td>
                                        <h4 class="text-brand">{{ $settings->currency_symbol ?? 'TK' }} {{ number_format($cItem->price * $cItem->quantity, 2) }}</h4>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="divider-2 mb-30 mt-30"></div>

                            @php
                                $discount = 0;
                                if(session()->has('coupon')) {
                                    $cpn = session()->get('coupon');
                                    if($cpn['type'] == 'percent') {
                                        $discount = ($cartTotal * $cpn['amount']) / 100;
                                    } else {
                                        $discount = $cpn['amount'];
                                    }
                                    if($discount > $cartTotal) $discount = $cartTotal;
                                }
                                // If you want to force them to select shipping on checkout page,
                                // we can add a simple shipping selector here or rely on cart page shipping area.
                                // But let's add shipping cost dynamically.
                            @endphp
                            <table class="table no-border">
                                <tbody>
                                    <tr>
                                        <td><h6 class="text-muted">Subtotal</h6></td>
                                        <td class="text-end"><h5 class="text-heading">{{ $settings->currency_symbol ?? 'TK' }} {{ number_format($cartTotal, 2) }}</h5></td>
                                    </tr>
                                    <tr>
                                        <td><h6 class="text-muted">Discount</h6></td>
                                        <td class="text-end"><h5 class="text-heading">{{ $settings->currency_symbol ?? 'TK' }} {{ number_format($discount, 2) }}</h5></td>
                                    </tr>
                                    <tr>
                                        <td><h6 class="text-muted">Shipping Area</h6></td>
                                        <td class="text-end">
                                            <select name="shipping_area" class="form-control" style="padding: 5px; height: auto;" id="checkout_shipping_area">
                                                <option value="{{ $settings->inside_dhaka ?? 0 }}">Inside Dhaka ({{ $settings->currency_symbol ?? 'TK' }} {{ $settings->inside_dhaka ?? 0 }})</option>
                                                <option value="{{ $settings->subcity ?? 0 }}">Sub City ({{ $settings->currency_symbol ?? 'TK' }} {{ $settings->subcity ?? 0 }})</option>
                                                <option value="{{ $settings->outside_dhaka ?? 0 }}">Outside Dhaka ({{ $settings->currency_symbol ?? 'TK' }} {{ $settings->outside_dhaka ?? 0 }})</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><h4 class="text-muted">Total</h4></td>
                                        <td class="text-end"><h4 class="text-brand" id="checkout-total">{{ $settings->currency_symbol ?? 'TK' }} {{ number_format($cartTotal - $discount + ($settings->inside_dhaka ?? 0), 2) }}</h4></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                </div>
            </form>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let subTotal = {{ $cartTotal - $discount }};
                let currency = '{{ $settings->currency_symbol ?? 'TK' }}';

                $('#checkout_shipping_area').on('change', function() {
                    let shippingCost = parseFloat($(this).val()) || 0;
                    let finalTotal = subTotal + shippingCost;
                    $('#checkout-total').text(currency + ' ' + finalTotal.toFixed(2));
                });
            });
        </script>
    </main>
@endsection
