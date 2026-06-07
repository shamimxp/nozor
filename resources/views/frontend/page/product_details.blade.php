@extends('frontend.layouts.app')
@section('content')
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{Url('/')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                <span></span> <a href="{{route('shop')}}">shop</a> <span></span> product details
            </div>
        </div>
    </div>
    <div class="container mb-30">
        <div class="row">
            <div class="col-xl-11 col-lg-12 m-auto">
                <div class="row flex-row-reverse">
                    <div class="col-xl-9">
                        <div class="product-detail accordion-detail">
                            <div class="row mb-50 mt-30">
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

                                @php
                                    $currency = $settings->currency_symbol ?? 'TK';
                                    $discount = null;

                                    if ($product->discount_type == 'amount' && !empty($product->discount_amount)) {
                                        $discount = '- ' . $currency . ' ' . number_format($product->discount_amount, 2);
                                    } elseif ($product->discount_type == 'percent' && !empty($product->discount_amount)) {
                                        $discount = '- ' . number_format($product->discount_amount, 0) . '%';
                                    }
                                @endphp
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="detail-info pr-30 pl-30">
                                        @if($product->discount_type)
                                        <span class="stock-status out-stock"> {{ $discount }} Off</span>
                                        @endif
                                        <h2 class="title-detail">{{$product->name ?? ' '}}</h2>
                                        <div class="product-detail-rating">
                                            <div class="product-rate-cover text-end">
                                                @php
    $totalReviews = isset($product) && $product->approvedReviews ? $product->approvedReviews->count() : 0;
    $avgRating = $totalReviews > 0 ? $product->approvedReviews->avg('rating') : 0;
    $percentRating = $avgRating * 20;
@endphp
<div class="product-rate-cover">
    <div class="product-rate d-inline-block">
        <div class="product-rating" style="width: {{ $percentRating }}%"></div>
    </div>
    <span class="font-small ml-5 text-muted"> ({{ number_format($avgRating, 1) }})</span>
</div>
                                                <!-- <span class="font-small ml-5 text-muted"> (32 reviews)</span> -->
                                            </div>
                                        </div>
                                        @php
                                            $price = $product->selling_price ?? 0;
                                            $currency = $settings->currency_symbol ?? 'TK';

                                            if ($product->discount_type == 'amount') {
                                                $finalPrice = $price - ($product->discount_amount ?? 0);
                                            } elseif ($product->discount_type == 'percent') {
                                                $finalPrice = $price - ($price * ($product->discount_amount ?? 0) / 100);
                                            } else {
                                                $finalPrice = $price;
                                            }
                                        @endphp


                                        <div class="clearfix product-price-cover">
                                            <div class="product-price primary-color float-left">
                                                <span class="current-price text-brand">{{ $currency }} {{ number_format($finalPrice, 2) }}</span>
                                                @if($product->discount_type)
                                                <span>
                                                        <span class="save-price font-md color3 ml-15">{{$discount}} Off</span>
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
                                                <div class="attr-detail attr-size mb-20 d-flex align-items-center">
                                                    <strong class="mr-10">{{ $vars->first()->variation->name }}: </strong>
                                                    <select class="form-control" style="max-width: 150px; display: inline-block;">
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
                                        <div class="detail-extralink mb-50">
                                            <div class="detail-qty border radius">
                                                <a href="#" class="qty-down"><i class="fi-rs-angle-small-down"></i></a>
                                                <input type="text" name="quantity" class="qty-val" value="1" min="1">
                                                <a href="#" class="qty-up"><i class="fi-rs-angle-small-up"></i></a>
                                            </div>
                                            <div class="product-extra-link2">
                                                <button type="submit" class="button button-add-to-cart add-to-cart-btn" data-id="{{ $product->id }}"><i class="fi-rs-shopping-cart"></i>Add to cart</button>
                                                <a aria-label="Add To Wishlist" class="action-btn hover-up wishlist-toggle-btn" href="javascript:void(0)" data-product-id="{{ $product->id }}"><i class="fi-rs-heart"></i></a>
                                            </div>
                                        </div>
                                        <div class="font-xs">
                                            <ul class="mr-50 float-start">
                                                <li class="mb-5">Category: <span class="text-brand">{{ $product->category->name ?? '-' }}</span></li>
                                                <li class="mb-5">SKU: <a href="#">{{ $product->slug ?? '-' }}</a></li>
                                            </ul>
                                            <ul class="float-start">
                                                <li>Stock:<span class="in-stock text-brand ml-5">{{$product->stock ?? 0}} Items In Stock</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Detail Info -->
                                </div>
                            </div>
                            <div class="product-info">
                                <div class="tab-style3">
                                    <ul class="nav nav-tabs text-uppercase">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="Description-tab" data-bs-toggle="tab" href="#Description">Description</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="Reviews-tab" data-bs-toggle="tab" href="#Reviews">Reviews ({{ $product->reviews->count() }})</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content shop_info_tab entry-main-content">
                                        <div class="tab-pane fade show active" id="Description">
                                            <div class="">
                                                <p>{!! $product->short_description  !!} </p>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="Reviews">
                                            <!--Comments-->
                                            <div class="comments-area">
                                                <div class="row">
                                                    <div class="col-lg-8">
                                                        <h4 class="mb-30">Customer questions & answers</h4>
                                                        <div class="comment-list">
                                                            @foreach($product->reviews as $review)
                                                            <div class="single-comment justify-content-between d-flex mb-30 p-4 border radius shadow-sm">
                                                                <div class="user d-flex w-100">
                                                                    <div class="thumb text-center" style="min-width: 80px; margin-right: 20px;">
                                                                        <img src="{{asset('web/assets/imgs/blog/author-2.png')}}" alt="" class="rounded-circle" style="width: 70px; height: 70px; object-fit: cover; border: 2px solid #e1e8f2;" />
                                                                    </div>
                                                                    <div class="desc w-100">
                                                                        <div class="d-flex justify-content-between align-items-center mb-10">
                                                                            <div>
                                                                                <h5 class="font-heading text-brand mb-1">{{ $review->name }}</h5>
                                                                                <span class="font-xs text-muted">{{ $review->created_at->format('F j, Y \a\t g:i a') }}</span>
                                                                            </div>
                                                                            <div class="product-rate d-inline-block">
                                                                                <div class="product-rating" style="width: {{ $review->rating * 20 }}%"></div>
                                                                            </div>
                                                                        </div>
                                                                        <p class="mb-10 text-body">{{ $review->comment }}</p>
                                                                        @if($review->reply)
                                                                            <div class="mt-15 p-15 border radius bg-light">
                                                                                <strong class="text-brand">Admin Reply:</strong>
                                                                                <p class="mb-0 mt-5 text-body">{{ $review->reply }}</p>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                            @if($product->reviews->isEmpty())
                                                                <p>No reviews yet. Be the first to review this product!</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <h4 class="mb-30">Customer reviews</h4>
                                                        @php
                                                            $totalReviews = $product->reviews->count();
                                                            $avgRating = $totalReviews > 0 ? $product->reviews->avg('rating') : 0;
                                                        @endphp
                                                        <div class="d-flex mb-30">
                                                            <div class="product-rate d-inline-block mr-15">
                                                                <div class="product-rating" style="width: {{ $avgRating * 20 }}%"></div>
                                                            </div>
                                                            <h6>{{ number_format($avgRating, 1) }} out of 5</h6>
                                                        </div>
                                                        @for($i=5; $i>=1; $i--)
                                                            @php
                                                                $count = $product->reviews->where('rating', $i)->count();
                                                                $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                                                            @endphp
                                                            <div class="progress mb-10">
                                                                <span>{{ $i }} star</span>
                                                                <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">{{ number_format($percent, 0) }}%</div>
                                                            </div>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            <!--comment form-->
                                            <div class="comment-form">
                                                <h4 class="mb-15">Add a review</h4>
                                                <div class="row">
                                                    <div class="col-lg-8 col-md-12">
                                                        <form class="form-contact comment_form" action="#" id="reviewForm">
                                                            @csrf
                                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                            <div class="row">
                                                                <div class="col-sm-12 mb-20">
                                                                    <div class="form-group">
                                                                        <label>Rating <span class="text-danger">*</span></label>
                                                                        <div class="star-rating" style="font-size: 24px; cursor: pointer;">
                                                                            <i class="fi-rs-star rating-star" data-val="1" style="color: #ffb300;"></i>
                                                                            <i class="fi-rs-star rating-star" data-val="2" style="color: #ffb300;"></i>
                                                                            <i class="fi-rs-star rating-star" data-val="3" style="color: #ffb300;"></i>
                                                                            <i class="fi-rs-star rating-star" data-val="4" style="color: #ffb300;"></i>
                                                                            <i class="fi-rs-star rating-star" data-val="5" style="color: #ffb300;"></i>
                                                                        </div>
                                                                        <input type="hidden" name="rating" id="ratingInput" value="5">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <div class="form-group">
                                                                        <input class="form-control" name="name" id="name" type="text" placeholder="Name" required />
                                                                    </div>
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <textarea class="form-control w-100" name="comment" id="comment" cols="30" rows="9" placeholder="Write Comment" required></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <button type="submit" class="button button-contactForm" id="submitReviewBtn">Submit Review</button>
                                                            </div>
                                                            <div id="reviewMessage" class="mt-15"></div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-60">
                                <div class="col-12">
                                    <h2 class="section-title style-1 mb-30">Related products</h2>
                                </div>
                                <div class="col-12">
                                    <div class="row related-products">
                                        @foreach($relatedProducts as $relProduct)
                                        <div class="col-lg-3 col-md-4 col-12 col-sm-6">
                                            <div class="product-cart-wrap mb-30 hover-up">
                                                <div class="product-img-action-wrap">
                                                    <div class="product-img product-img-zoom">
                                                        <a href="{{route('product.details',encrypt($relProduct->id))}}" tabindex="0">
                                                            <img class="default-img" src="{{ $relProduct->featured_image ? asset(config('imagepath.product') . $relProduct->featured_image) : asset('images/no-image.png') }}" alt="{{$relProduct->name ?? '-'}}" />
                                                            <img class="hover-img" src="{{ $relProduct->featured_image ? asset(config('imagepath.product') . $relProduct->featured_image) : asset('images/no-image.png') }}" alt="{{$relProduct->name ?? '-'}}" />
                                                        </a>
                                                    </div>
                                                    <div class="product-action-1">
                                                        <a href="javascript:void(0)"
                                                           class="action-btn quick-view-btn"
                                                           data-product="{{ encrypt($relProduct->id) }}">
                                                            <i class="fi-rs-eye"></i>
                                                        </a>
                                                        <a aria-label="Add To Wishlist" class="action-btn wishlist-toggle-btn" href="javascript:void(0)" data-product-id="{{ $product->id }}" tabindex="0"><i class="fi-rs-heart"></i></a>
                                                    </div>
                                                    @php
                                                        $currency = $settings->currency_symbol ?? 'TK';
                                                        $discountLabel = null;
                                                        if ($relProduct->discount_type == 'amount' && !empty($relProduct->discount_amount)) {
                                                            $discountLabel = '- ' . $currency . ' ' . number_format($relProduct->discount_amount, 2);
                                                        } elseif ($relProduct->discount_type == 'percent' && !empty($relProduct->discount_amount)) {
                                                            $discountLabel = '- ' . number_format($relProduct->discount_amount, 0) . '%';
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
                                                        <a href="{{ isset($relProduct->category) ? route('category.products', $relProduct->category->slug) : '#' }}">{{ $relProduct->category->name ?? '-' }}</a>
                                                    </div>
                                                    <h2><a href="{{route('product.details',encrypt($relProduct->id))}}" class="product-title-shamim" tabindex="0">{{$relProduct->name}}</a></h2>
@php
    $totalReviews = $relProduct->approvedReviews->count() ?? 0;
    $avgRating = $totalReviews > 0 ? $relProduct->approvedReviews->avg('rating') : 0;
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
                                                        $relPrice = $relProduct->selling_price ?? 0;
                                                        if ($relProduct->discount_type == 'amount') {
                                                            $relFinalPrice = $relPrice - ($relProduct->discount_amount ?? 0);
                                                        } elseif ($relProduct->discount_type == 'percent') {
                                                            $relFinalPrice = $relPrice - ($relPrice * ($relProduct->discount_amount ?? 0) / 100);
                                                        } else {
                                                            $relFinalPrice = $relPrice;
                                                        }
                                                    @endphp
                                                    <div class="product-card-bottom">
                                                        <div class="product-price">
                                                            <span>{{$currency}} {{ number_format($relFinalPrice, 2) }}</span>
                                                            @if($relFinalPrice < $relPrice)
                                                            <span class="old-price">{{$currency}} {{ number_format($relPrice, 2) }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="add-cart">
                                                            <a  class="add add-to-cart-btn" href="javascript:void(0)" data-id="{{ $relProduct->id }}"><i class="fi-rs-shopping-cart mr-5"></i>Add </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 primary-sidebar sticky-sidebar mt-30">
                        <div class="sidebar-widget widget-category-2 mb-30">
                            <h5 class="section-title style-1 mb-30">Category</h5>
                            <ul>
                                @foreach($categories as $cat)
                                    <li>
                                        <a href="{{route('category.products',$cat->slug)}}">
                                            <img src="{{ $cat->image
                             ? asset(config('imagepath.category') . $cat->image)
                             : asset('images/no-image.png') }}" alt=""  />{{$cat->name ?? '-'}}</a><span class="count">{{$cat->products_count}}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Product sidebar Widget -->
                        <div class="sidebar-widget product-sidebar mb-30 p-30 bg-grey border-radius-10">
                            <h5 class="section-title style-1 mb-30">New products</h5>
                            @foreach($newProducts as $newProd)
                            <div class="single-post clearfix">
                                <div class="image">
                                    <img src="{{ $newProd->featured_image ? asset(config('imagepath.product') . $newProd->featured_image) : asset('images/no-image.png') }}" alt="{{$newProd->name ?? '-'}}" />
                                </div>
                                <div class="content pt-10">
                                    <h5><a href="{{route('product.details',encrypt($newProd->id))}}" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">{{$newProd->name}}</a></h5>
                                    @php
                                        $newPrice = $newProd->selling_price ?? 0;
                                        if ($newProd->discount_type == 'amount') {
                                            $newFinalPrice = $newPrice - ($newProd->discount_amount ?? 0);
                                        } elseif ($newProd->discount_type == 'percent') {
                                            $newFinalPrice = $newPrice - ($newPrice * ($newProd->discount_amount ?? 0) / 100);
                                        } else {
                                            $newFinalPrice = $newPrice;
                                        }
                                        $currency = $settings->currency_symbol ?? 'TK';
                                    @endphp
                                    <p class="price mb-0 mt-5">{{$currency}} {{ number_format($newFinalPrice, 2) }}</p>
                                    @php
                                        $newTotalReviews = $newProd->approvedReviews->count() ?? 0;
                                        $newAvgRating = $newTotalReviews > 0 ? $newProd->approvedReviews->avg('rating') : 0;
                                        $newPercentRating = $newAvgRating * 20;
                                    @endphp
                                    <div class="product-rate">
                                        <div class="product-rating" style="width: {{ $newPercentRating }}%"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#reviewForm').on('submit', function(e) {
            e.preventDefault();
            let btn = $('#submitReviewBtn');
            let originalText = btn.text();
            btn.prop('disabled', true).text('Submitting...');
            $('#reviewMessage').html('');

            $.ajax({
                url: "{{ route('review.submit') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    if(response.status === 'success') {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        } else {
                            $('#reviewMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                        }
                        $('#reviewForm')[0].reset();
                        // Reset stars visually
                        $('.rating-star').css('color', '#ffb300');
                        $('#ratingInput').val(5);
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Something went wrong. Please try again.';
                    if(xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).map(err => err.join(', ')).join('<br>');
                    }
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else {
                        $('#reviewMessage').html('<div class="alert alert-danger">' + errorMessage + '</div>');
                    }
                },
                complete: function() {
                    btn.prop('disabled', false).text(originalText);
                }
            });
        });

        // Star rating click and hover logic
        $('.rating-star').on('click', function() {
            let val = $(this).data('val');
            $('#ratingInput').val(val);
            $('.rating-star').each(function() {
                if ($(this).data('val') <= val) {
                    $(this).css('color', '#ffb300');
                } else {
                    $(this).css('color', '#ccc');
                }
            });
        });

        $('.rating-star').hover(
            function() {
                let val = $(this).data('val');
                $('.rating-star').each(function() {
                    if ($(this).data('val') <= val) {
                        $(this).css('color', '#ffb300');
                    } else {
                        $(this).css('color', '#ccc');
                    }
                });
            },
            function() {
                let val = $('#ratingInput').val();
                $('.rating-star').each(function() {
                    if ($(this).data('val') <= val) {
                        $(this).css('color', '#ffb300');
                    } else {
                        $(this).css('color', '#ccc');
                    }
                });
            }
        );
    });
</script>
@endpush
