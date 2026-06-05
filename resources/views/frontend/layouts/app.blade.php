<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <title>NOZOR-Fashion</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:title" content="" />
    <meta property="og:type" content="" />
    <meta property="og:url" content="" />
    <meta property="og:image" content="" />
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/imgs/theme/favicon.svg" />
    <!-- Template CSS -->
    <link rel="stylesheet" href="{{asset('web')}}/assets/css/plugins/animate.min.css" />
    <link rel="stylesheet" href="{{asset('web')}}/assets/css/main.css?v=5.5" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
</head>

<body>
<!-- Modal -->
{{--<div class="modal fade custom-modal" id="onloadModal" tabindex="-1" aria-labelledby="onloadModalLabel" aria-hidden="true">--}}
{{--    <div class="modal-dialog">--}}
{{--        <div class="modal-content">--}}
{{--            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
{{--            <div class="modal-body">--}}
{{--                <div class="deal" style="background-image: url('{{asset('web')}}/assets/imgs/banner/popup-1.png')">--}}
{{--                    <div class="deal-top">--}}
{{--                        <h6 class="mb-10 text-brand-2">Deal of the Day</h6>--}}
{{--                    </div>--}}
{{--                    <div class="deal-content detail-info">--}}
{{--                        <h4 class="product-title"><a href="shop-product-right.html" class="text-heading">Organic fruit for your family's health</a></h4>--}}
{{--                        <div class="clearfix product-price-cover">--}}
{{--                            <div class="product-price primary-color float-left">--}}
{{--                                <span class="current-price text-brand">$38</span>--}}
{{--                                <span>--}}
{{--                                        <span class="save-price font-md color3 ml-15">26% Off</span>--}}
{{--                                        <span class="old-price font-md ml-15">$52</span>--}}
{{--                                    </span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="deal-bottom">--}}
{{--                        <p class="mb-20">Hurry Up! Offer End In:</p>--}}
{{--                        <div class="deals-countdown pl-5" data-countdown="2025/03/25 00:00:00">--}}
{{--                            <span class="countdown-section"><span class="countdown-amount hover-up">03</span><span class="countdown-period"> days </span></span><span class="countdown-section"><span class="countdown-amount hover-up">02</span><span class="countdown-period"> hours </span></span><span class="countdown-section"><span class="countdown-amount hover-up">43</span><span class="countdown-period"> mins </span></span><span class="countdown-section"><span class="countdown-amount hover-up">29</span><span class="countdown-period"> sec </span></span>--}}
{{--                        </div>--}}
{{--                        <div class="product-detail-rating">--}}
{{--                            <div class="product-rate-cover text-end">--}}
{{--                                <div class="product-rate d-inline-block">--}}
{{--                                    <div class="product-rating" style="width: 90%"></div>--}}
{{--                                </div>--}}
{{--                                <span class="font-small ml-5 text-muted"> (32 rates)</span>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <a href="shop-grid-right.html" class="btn hover-up">Shop Now <i class="fi-rs-arrow-right"></i></a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<div class="modal fade custom-modal" id="quickViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            <div class="modal-body" id="quick-view-content">
                <!-- AJAX content will load here -->
                <div class="text-center p-5">Loading...</div>
            </div>

        </div>
    </div>
</div>


@include('frontend.inc.header')
<!--End header-->
<main class="main">
     @yield('content')
</main>
<footer class="main">
   @include('frontend.inc.footer')
</footer>
<!-- Preloader Start -->
<style>
    .custom-preloader {
        width: 60px;
        height: 60px;
        border: 6px solid rgba(241, 88, 34, 0.2);
        border-top-color: #F15822; /* Primary Color */
        border-radius: 50%;
        animation: custom-spin 2.5s linear infinite; /* 2.5 second duration */
    }
    @keyframes custom-spin {
        to { transform: rotate(360deg); }
    }
</style>
{{--<div id="preloader-active">--}}
{{--    <div class="preloader d-flex align-items-center justify-content-center">--}}
{{--        <div class="preloader-inner position-relative">--}}
{{--            <div class="text-center">--}}
{{--                <div class="custom-preloader"></div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<!-- Vendor JS-->
<script src="{{asset('web')}}/assets/js/vendor/modernizr-3.6.0.min.js"></script>
<script src="{{asset('web')}}/assets/js/vendor/jquery-3.6.0.min.js"></script>
<script src="{{asset('web')}}/assets/js/vendor/jquery-migrate-3.3.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{asset('web')}}/assets/js/vendor/bootstrap.bundle.min.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/slick.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/jquery.syotimer.min.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/waypoints.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/wow.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/perfect-scrollbar.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/magnific-popup.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/select2.min.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/counterup.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/jquery.countdown.min.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/images-loaded.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/isotope.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/scrollup.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/jquery.vticker-min.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/jquery.theia.sticky.js"></script>
<script src="{{asset('web')}}/assets/js/plugins/jquery.elevatezoom.js"></script>
<!-- Template  JS -->
<script src="{{asset('web')}}/assets/js/main.js?v=5.5"></script>
<script src="{{asset('web')}}/assets/js/shop.js?v=5.5"></script>
<script>
    $(document).on('click', '.quick-view-btn', function(e) {
        e.preventDefault();

        let product_id = $(this).data('product');

        if (!product_id) {
            alert('Invalid product ID');
            return;
        }

        // Show modal first
        $('#quickViewModal').modal('show');

        // Show loading spinner
        $('#quick-view-content').html('<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

        $.ajax({
            url: "{{ route('quick-view') }}",
            type: "GET",
            data: { id: product_id },
            timeout: 10000,

            success: function(response) {
                $('#quick-view-content').html(response);

                // Re-initialize Slick sliders — scoped ONLY to the modal content
                setTimeout(function() {
                    var $modal        = $('#quick-view-content');
                    var $mainSlider   = $modal.find('.product-image-slider');
                    var $thumbSlider  = $modal.find('.slider-nav-thumbnails');

                    if ($mainSlider.length) {
                        // Destroy only if already initialized (safety guard)
                        if ($mainSlider.hasClass('slick-initialized')) {
                            $mainSlider.slick('unslick');
                        }
                        if ($thumbSlider.hasClass('slick-initialized')) {
                            $thumbSlider.slick('unslick');
                        }

                        $mainSlider.slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            arrows: false,
                            fade: true,
                            asNavFor: $thumbSlider[0]   // pass DOM node to avoid global selector conflict
                        });

                        if ($thumbSlider.length) {
                            $thumbSlider.slick({
                                slidesToShow: 5,
                                slidesToScroll: 1,
                                asNavFor: $mainSlider[0],
                                dots: false,
                                arrows: false,
                                focusOnSelect: true
                            });
                        }
                        
                        disableCartButtons();
                    }
                }, 300);
            },

            error: function(xhr, status, error) {
                console.error('Quick view error:', status, error);
                $('#quick-view-content').html('<div class="alert alert-danger m-3">Failed to load product. Please try again.</div>');
            }
        });
    });

    // ── Quantity stepper for dynamically loaded content (Quick View modal) ──
    $(document).on('click', '.qty-up', function(e) {
        e.preventDefault();
        var $input = $(this).closest('.detail-qty').find('.qty-val');
        var val = parseInt($input.val()) || 1;
        $input.val(val + 1);
    });

    $(document).on('click', '.qty-down', function(e) {
        e.preventDefault();
        var $input = $(this).closest('.detail-qty').find('.qty-val');
        var val = parseInt($input.val()) || 1;
        if (val > 1) {
            $input.val(val - 1);
        }
    });

    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.preventDefault();
        let btn = $(this);
        let product_id = btn.data('id');
        
        // Find quantity if available (e.g., from quick view modal)
        let qty = 1;
        let qtyInput = btn.closest('.detail-info').find('.qty-val');
        if(qtyInput.length) {
            qty = qtyInput.val();
        }

        let _token = '{{ csrf_token() }}';

        $.ajax({
            url: "{{ route('cart.add') }}",
            type: "POST",
            data: {
                product_id: product_id,
                quantity: qty,
                _token: _token
            },
            success: function(response) {
                if(response.status === 'success') {
                    toastr.success(response.message);
                    if($('.cart-count').length) {
                        $('.cart-count').text(response.cart_count);
                    }
                    if($('.dynamic-cart-list').length) {
                        $('.dynamic-cart-list').html(response.cart_html);
                    }
                    if($('.cart-total-amount').length) {
                        $('.cart-total-amount').text(response.cart_total);
                    }
                    
                    btn.addClass('disabled').css('pointer-events', 'none').html('<i class="fi-rs-check mr-5"></i>Added');
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('An error occurred. Please try again.');
            }
        });
    });

    $(document).on('click', '.remove-cart-item', function(e) {
        e.preventDefault();
        let cart_id = $(this).data('id');
        let product_id = $(this).data('product-id');
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
                    toastr.success(response.message);
                    if($('.cart-count').length) {
                        $('.cart-count').text(response.cart_count);
                    }
                    if($('.dynamic-cart-list').length) {
                        $('.dynamic-cart-list').html(response.cart_html);
                    }
                    if($('.cart-total-amount').length) {
                        $('.cart-total-amount').text(response.cart_total);
                    }
                    
                    $(`.add-to-cart-btn[data-id="${product_id}"]`).removeClass('disabled').css('pointer-events', 'auto').html('<i class="fi-rs-shopping-cart mr-5"></i>Add');
                }
            }
        });
    });

    function disableCartButtons() {
        $('.remove-cart-item').each(function() {
            let pid = $(this).data('product-id');
            $(`.add-to-cart-btn[data-id="${pid}"]`).addClass('disabled').css('pointer-events', 'none').html('<i class="fi-rs-check mr-5"></i>Added');
        });
    }

    $(document).ready(function() {
        disableCartButtons();
    });
</script>


{{--<script>--}}
{{--    $(document).on('click', '.quick-view-btn', function() {--}}
{{--        let product_id = $(this).data('product');--}}

{{--        // Show modal first--}}
{{--        $('#quickViewModal').modal('show');--}}

{{--        // Show loading--}}
{{--        $('#quick-view-content').html('<div class="text-center p-5">Loading...</div>');--}}

{{--        $.ajax({--}}
{{--            url: "{{ route('quick-view') }}",--}}
{{--            type: "GET",--}}
{{--            data: { id: product_id },--}}

{{--            success: function(response) {--}}
{{--                $('#quick-view-content').html(response);--}}
{{--            },--}}

{{--            error: function() {--}}
{{--                $('#quick-view-content').html('<p class="text-danger">Failed to load product.</p>');--}}
{{--            }--}}
{{--        });--}}
{{--    });--}}
{{--</script>--}}

</body>

</html>
