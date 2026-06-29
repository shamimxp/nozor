<div class="row">
    <div class="col-md-6 col-sm-12 col-xs-12 mb-md-0 mb-sm-5">
        <div class="detail-gallery">
            <span class="zoom-icon"><i class="fi-rs-search"></i></span>

            <!-- MAIN SLIDES -->
            <div class="product-image-slider">
                <figure class="border-radius-10">
                    <img src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{ $product->name }}" />
                </figure>
                @foreach($product->gallery ?? [] as $image)
                    <figure class="border-radius-10">
                        <img src="{{ asset(config('imagepath.product') . $image->image) }}" alt="{{ $product->name }}" />
                    </figure>
                @endforeach
            </div>

            <!-- THUMBNAILS -->
            <div class="slider-nav-thumbnails">
                <div><img src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{ $product->name }}" /></div>
                @foreach($product->gallery ?? [] as $image)
                    <div><img src="{{ asset(config('imagepath.product') . $image->image) }}" alt="{{ $product->name }}" /></div>
                @endforeach
            </div>
        </div>
        <!-- End Gallery -->
    </div>

    <div class="col-md-6 col-sm-12 col-xs-12">
        <div class="detail-info pr-30 pl-30">

            @if($discount)
                <span class="stock-status out-stock">
                    {{ $discount }}
                </span>
            @endif

            <h3 class="title-detail">
                <a href="{{ route('product.details', encrypt($product->id)) }}" class="text-heading">{{ $product->name }}</a>
            </h3>

            @php
                $reviewCount = $product->approvedReviews->count();
                $averageRating = $reviewCount > 0 ? $product->approvedReviews->avg('rating') : 0;
                $ratingPercent = ($averageRating / 5) * 100;
            @endphp
            <div class="product-detail-rating">
                <div class="product-rate-cover text-end">
                    <div class="product-rate d-inline-block">
                        <div class="product-rating" style="width: {{ $ratingPercent }}%"></div>
                    </div>
                    <span class="font-small ml-5 text-muted"> ({{ $reviewCount }} reviews)</span>
                </div>
            </div>

            <div class="clearfix product-price-cover">
                <div class="product-price primary-color float-left">
                    <span class="current-price text-brand">{{ $currency }} {{ number_format($finalPrice, 2) }}</span>
                    @if($discount)
                        <span>
                            <span class="save-price font-md color3 ml-15">{{ $discount }}</span>
                            <span class="old-price font-md ml-15">{{ $currency }} {{ number_format($price, 2) }}</span>
                        </span>
                    @endif
                </div>
            </div>

            @if($product->variations && $product->variations->count() > 0)
                @php
                    $groupedVariations = $product->variations->groupBy('variation_id');
                @endphp
                <div class="product-variations mb-20">
                    @foreach($groupedVariations as $varId => $vars)
                    <div class="attr-detail attr-size mb-2 d-flex align-items-center">
                        <strong class="mr-10">{{ $vars->first()->variation->name }}: </strong>
                        <select class="form-control form-control-sm" style="max-width: 150px; display: inline-block;">
                            <option value="">Select {{ $vars->first()->variation->name }}</option>
                            @foreach($vars as $var)
                                @if($var->variationValue)
                                    <option value="{{ $var->variationValue->id }}">{{ $var->variationValue->value }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                </div>
            @endif

            <div class="detail-extralink mb-30">
                <div class="detail-qty border radius">
                    <a href="#" class="qty-down"><i class="fi-rs-angle-small-down"></i></a>
                    <input type="text" name="quantity" class="qty-val" value="1" min="1">
                    <a href="#" class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
                </div>
                <div class="product-extra-link2">
                    <button type="button" class="button button-add-to-cart add-to-cart-btn" data-id="{{ $product->id }}"><i class="fi-rs-shopping-cart"></i>Add to cart</button>
                </div>
            </div>

            <div class="font-xs">
                <ul>
                    <li class="mb-5">Category: <span class="text-brand">{{ $product->category->name ?? '-' }}</span></li>
                    <li class="mb-5">Stock: <span class="text-brand">In Stock</span></li>
                </ul>
            </div>

        </div>
        <!-- Detail Info -->
    </div>
</div>
<script>
    (function ($) {
        // Initialize Slick Slider for Quick View
        $('.product-image-slider').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: false,
            asNavFor: '.slider-nav-thumbnails',
        });

        $('.slider-nav-thumbnails').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.product-image-slider',
            dots: false,
            focusOnSelect: true,
            prevArrow: '<button type="button" class="slick-prev"><i class="fi-rs-arrow-small-left"></i></button>',
            nextArrow: '<button type="button" class="slick-next"><i class="fi-rs-arrow-small-right"></i></button>'
        });

        // Remove active class from all thumbnail slides
        $('.slider-nav-thumbnails .slick-slide').removeClass('slick-active');
        $('.slider-nav-thumbnails .slick-slide').eq(0).addClass('slick-active');

        // On before slide change match active thumbnail to current slide
        $('.product-image-slider').on('beforeChange', function (event, slick, currentSlide, nextSlide) {
            var mySlideNumber = nextSlide;
            $('.slider-nav-thumbnails .slick-slide').removeClass('slick-active');
            $('.slider-nav-thumbnails .slick-slide').eq(mySlideNumber).addClass('slick-active');
        });

        // Initialize Qty Up-Down for Quick View
        $('.detail-qty').each(function () {
            var $this = $(this);
            var qtyval = parseInt($this.find(".qty-val").val(), 10) || 1;

            $this.find('.qty-up').off('click').on('click', function (event) {
                event.preventDefault();
                qtyval = parseInt($this.find(".qty-val").val(), 10) || 1;
                qtyval = qtyval + 1;   
                $(this).prev().val(qtyval).trigger('change');
            });

             $this.find(".qty-down").off("click").on("click", function (event) {
                 event.preventDefault(); 
                 qtyval = parseInt($this.find(".qty-val").val(), 10) || 1;
                 qtyval = qtyval - 1;
                 if (qtyval > 0) {
                     $(this).next().val(qtyval).trigger('change');
                 } else {
                     qtyval = 1;
                     $(this).next().val(qtyval).trigger('change');
                 }
             });
        });
    })(jQuery);
</script>


