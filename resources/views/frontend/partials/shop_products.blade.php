@if($products->count() > 0)
    @foreach($products as $product)
    <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
        <div class="product-cart-wrap mb-30 hover-up">
            <div class="product-img-action-wrap">
                <div class="product-img product-img-zoom">
                    <a href="{{ route('product.details', encrypt($product->id)) }}">
                        <img class="default-img" src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{ $product->name }}" />
                        <img class="hover-img" src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{ $product->name }}" />
                    </a>
                </div>
                <div class="product-action-1">
                    <a aria-label="Add To Wishlist" class="action-btn wishlist-toggle-btn" href="javascript:void(0)" data-product-id="{{ $product->id }}"><i class="fi-rs-heart"></i></a>
                    <a aria-label="Quick view" class="action-btn quick-view-btn" data-product="{{ encrypt($product->id) }}"><i class="fi-rs-eye"></i></a>
                </div>
                @php
                    $currency = $settings->currency_symbol ?? 'TK';
                    $discountLabel = null;
                    if ($product->discount_type == 'amount' && !empty($product->discount_amount)) {
                        $discountLabel = '- ' . $currency . ' ' . number_format($product->discount_amount, 2);
                    } elseif ($product->discount_type == 'percent' && !empty($product->discount_amount)) {
                        $discountLabel = '- ' . number_format($product->discount_amount, 0) . '%';
                    }
                @endphp
                @if($discountLabel)
                <div class="product-badges product-badges-position product-badges-mrg">
                    <span class="new">{{$discountLabel}}</span>
                </div>
                @endif
            </div>
            <div class="product-content-wrap">
                <div class="product-category">
                    <a href="{{ isset($product->category) ? route('category.products', $product->category->slug) : '#' }}">{{ $product->category->name ?? '-' }}</a>
                </div>
                <h2><a href="{{ route('product.details', encrypt($product->id)) }}" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;" class="product-title-shamim">{{ $product->name }}</a></h2>
                @php
                    $totalReviews = $product->approvedReviews->count();
                    $avgRating = $totalReviews > 0 ? $product->approvedReviews->avg('rating') : 0;
                    $percentRating = $avgRating * 20;
                @endphp
                <div class="product-rate-cover">
                    <div class="product-rate d-inline-block">
                        <div class="product-rating" style="width: {{ $percentRating }}%"></div>
                    </div>
                    <span class="font-small ml-5 text-muted"> ({{ number_format($avgRating, 1) }})</span>
                </div>
                <div>
                    <span class="font-small text-muted">By <a href="{{ route('shop') }}">Nozor</a></span>
                </div>
                @php
                    $price = $product->selling_price ?? 0;
                    if ($product->discount_type == 'amount') {
                        $finalPrice = $price - ($product->discount_amount ?? 0);
                    } elseif ($product->discount_type == 'percent') {
                        $finalPrice = $price - ($price * ($product->discount_amount ?? 0) / 100);
                    } else {
                        $finalPrice = $price;
                    }
                @endphp
                <div class="product-card-bottom">
                    <div class="product-price">
                        <span>{{$currency}} {{ number_format($finalPrice, 2) }}</span>
                        @if($finalPrice < $price)
                        <span class="old-price">{{$currency}} {{ number_format($price, 2) }}</span>
                        @endif
                    </div>
                    <div class="add-cart">
                        <a class="add add-to-cart-btn" href="javascript:void(0)" data-id="{{ $product->id }}"><i class="fi-rs-shopping-cart mr-5"></i>Add </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach


@else
    <div class="col-12 text-center mt-50">
        <img src="{{ asset('web/assets/imgs/page/no_product_found.png') }}" alt="No products found" style="max-width: 300px; opacity: 0.8; margin-bottom: 20px;">
        <h4 class="text-muted">Oops! No products found in this category.</h4>
        <a href="{{ route('shop') }}" class="btn btn-brand mt-15">Back to Shop</a>
    </div>
@endif
