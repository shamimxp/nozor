@extends('frontend.layouts.app')
@section('content')
    <section class="home-slider position-relative mb-30">
        <div class="container">
            <div class="home-slide-cover mt-30">
                <div class="hero-slider-1 style-4 dot-style-1 dot-style-1-position-1">
                    @foreach($banners as $banner)
                            <div class="single-hero-slider single-animation-wrap"
                                 style="background-image: url('{{ $banner->image
                             ? asset(config('imagepath.banner') . $banner->image)
                             : asset('images/no-image.png') }}')">
{{--                            <div class="slider-content">--}}
{{--                                <h1 class="display-2 mb-40">--}}
{{--                                   {{$banner->title}}<br />--}}
{{--                                </h1>--}}
{{--                                <p class="mb-65">{{$banner->sub_title}}</p>--}}
{{--                            </div>--}}
                        </div>
                    @endforeach
                </div>
                <div class="slider-arrow hero-slider-1-arrow"></div>
            </div>
        </div>
    </section>
    <!--End hero slider-->
    <section class="popular-categories section-padding">
        <div class="container wow animate__animated animate__fadeIn">
            <div class="section-title">
                <div class="title">
                    <h3>Featured Categories</h3>
                </div>
                <div class="slider-arrow slider-arrow-2 flex-right carausel-10-columns-arrow" id="carausel-10-columns-arrows"></div>
            </div>
            <div class="carausel-10-columns-cover position-relative">
                <div class="carausel-10-columns" id="carausel-10-columns">
                   @foreach($categories as $category)
                    <div class="card-2 bg-9 wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                        <figure class="img-hover-scale overflow-hidden">
                            <a href="{{route('category.products',$category->slug)}}">
                                <img src="{{ $category->image
                             ? asset(config('imagepath.category') . $category->image)
                             : asset('images/no-image.png') }}" alt="" />
                            </a>
                        </figure>
                        <h6><a href="{{route('category.products',$category->slug)}}">{{$category->name}}</a></h6>
                        <span>{{$category->products_count}} items</span>
                    </div>
                   @endforeach
                </div>
            </div>
        </div>
    </section>
    <!--End category slider-->
    <section class="banners mb-25">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay="0">
                        <img  style="width: 461px;height: 270px"  src="{{asset('web')}}/assets/imgs/banner/1.jpg" alt="" />
                        <div class="banner-text">
                            <h4>
                                Everyday Fresh & <br />Clean with Our<br />
                                Products
                            </h4>
                            <a href="{{route('shop')}}" class="btn btn-xs">Shop Now <i class="fi-rs-arrow-small-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="banner-img wow animate__animated animate__fadeInUp" data-wow-delay=".2s">
                        <img style="width: 461px;height: 270px" src="{{asset('web')}}/assets/imgs/banner/2.jpg" alt="" />
                        <div class="banner-text">
                            <h4>
                                Make your Fashion<br />
                                Choice and Easy
                            </h4>
                            <a href="{{route('shop')}}" class="btn btn-xs">Shop Now <i class="fi-rs-arrow-small-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 d-md-none d-lg-flex">
                    <div class="banner-img mb-sm-0 wow animate__animated animate__fadeInUp" data-wow-delay=".4s">
                        <img style="width: 461px;height: 270px" src="{{asset('web')}}/assets/imgs/banner/1.jpg" alt="" />
                        <div class="banner-text">
                            <h4>The best trusted <br />Products Online</h4>
                            <a href="{{route('shop')}}" class="btn btn-xs">Shop Now <i class="fi-rs-arrow-small-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--End banners-->
    <section class="product-tabs section-padding position-relative">
        <div class="container">
            <div class="section-title style-2 wow animate__animated animate__fadeIn">
                <h3>Popular Products</h3>
                <ul class="nav nav-tabs links" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="{{route('shop')}}" class="nav-link active" id="nav-tab-one">All Items <i style="font-size: 12px" class="fi-rs-angle-right"></i></a>
                    </li>
                </ul>
            </div>
            <!--End nav-tabs-->
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="tab-one" role="tabpanel" aria-labelledby="tab-one">
                    <div class="row product-grid-4">
                        @foreach($products as $product)
                        <div class="col-lg-1-5 col-md-4 col-12 col-sm-6">
                            <div class="product-cart-wrap mb-30 wow animate__animated animate__fadeIn" data-wow-delay=".1s">
                                    <div class="product-img-action-wrap">
                                        <div class="product-img product-img-zoom">
                                            <a href="{{route('product.details',encrypt($product->id))}}">
                                                <img class="default-img" src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{$product->name ?? '-'}}" />
                                                <img class="hover-img" src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{$product->name ?? '-'}}" />
                                            </a>
                                        </div>
                                        <div class="product-action-1">
                                            <a aria-label="Add To Wishlist" class="action-btn" href="#"><i class="fi-rs-heart"></i></a>
                                            {{-- <a aria-label="Compare" class="action-btn" href="shop-compare.html"><i class="fi-rs-shuffle"></i></a>--}}
{{--                                            <a aria-label="Quick view" class="action-btn" data-bs-toggle="modal" data-bs-target="#quickViewModal"><i class="fi-rs-eye"></i></a>--}}
{{--                                            <a aria-label="Quick view" class="action-btn quick-view-btn"--}}
{{--                                               data-product-id="{{ encrypt($product->id) }}">--}}
{{--                                                <i class="fi-rs-eye"></i>--}}
{{--                                            </a>--}}

                                            <a href="javascript:void(0)"
                                               class="action-btn quick-view-btn"
                                               data-product="{{ encrypt($product->id) }}">
                                                <i class="fi-rs-eye"></i>
                                            </a>
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
                                        @if($product->discount_type)
                                            <div class="product-badges product-badges-position product-badges-mrg">
                                                <span class="new">{{$discount}}</span>
                                            </div>
                                        @endif
                                    </div>
                                <div class="product-content-wrap">
                                    <div class="product-category">
                                        <a href="{{route('category.products',$product->category->slug)}}">{{$product->category->name ?? ' '}}</a>
                                    </div>
                                    <h2><a href="{{route('product.details',encrypt($product->id))}}" class="product-title-shamim">{{$product->name ?? '-'}} </a></h2>
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
                                    <div>
                                        <span class="font-small text-muted">By <a href="{{route('shop')}}">Nozor</a></span>
                                    </div>
                                    <div class="product-card-bottom">
                                        <div class="product-price">
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
                                            <span>{{ $currency }} {{ number_format($finalPrice, 2) }}</span>
                                            @if($product->discount_type)
                                                <span class="old-price">{{ $currency }} {{ number_format($price, 2) }}</span>
                                            @endif

                                        </div>
                                        <div class="add-cart">
                                            <a class="add add-to-cart-btn" href="javascript:void(0)" data-id="{{ $product->id }}"><i class="fi-rs-shopping-cart mr-5"></i>Add </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <!--end product card-->
                        @endforeach
                    </div>
                    <!--End product-grid-4-->
                </div>
            </div>
            <!--End tab-content-->
        </div>
    </section>
    <!--Products Tabs-->
    <section class="section-padding pb-5">
        <div class="container">
            <div class="section-title wow animate__animated animate__fadeIn">
                <h3 class="">Daily Best Sells</h3>
                <ul class="nav nav-tabs links" id="myTab-2" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="nav-tab-one-1" data-bs-toggle="tab" data-bs-target="#tab-one-1" type="button" role="tab" aria-controls="tab-one" aria-selected="true">Featured</button>
                    </li>
{{--                    <li class="nav-item" role="presentation">--}}
{{--                        <button class="nav-link" id="nav-tab-two-1" data-bs-toggle="tab" data-bs-target="#tab-two-1" type="button" role="tab" aria-controls="tab-two" aria-selected="false">Popular</button>--}}
{{--                    </li>--}}
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="nav-tab-three-1" data-bs-toggle="tab" data-bs-target="#tab-three-1" type="button" role="tab" aria-controls="tab-three" aria-selected="false">New added</button>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-lg-3 d-none d-lg-flex wow animate__animated animate__fadeIn">
                    <div class="banner-img style-2">
                        <div class="banner-text">
                            <h2 class="mb-100">Bring nature into your home</h2>
                            <a href="{{route('shop')}}" class="btn btn-xs">Shop Now <i class="fi-rs-arrow-small-right"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-12 wow animate__animated animate__fadeIn" data-wow-delay=".4s">
                    <div class="tab-content" id="myTabContent-1">
                        <div class="tab-pane fade show active" id="tab-one-1" role="tabpanel" aria-labelledby="tab-one-1">
                            <div class="carausel-4-columns-cover arrow-center position-relative">
                                <div class="slider-arrow slider-arrow-2 carausel-4-columns-arrow" id="carausel-4-columns-arrows"></div>
                                <div class="carausel-4-columns carausel-arrow-center" id="carausel-4-columns">
                                    @foreach($featuredProducts as $featuredProduct)
                                        <div class="product-cart-wrap">
                                            <div class="product-img-action-wrap">
                                                <div class="product-img product-img-zoom">
                                                    <a href="{{route('product.details',encrypt($featuredProduct->id))}}">
                                                        <img class="default-img" src="{{ $featuredProduct->featured_image ? asset(config('imagepath.product') . $featuredProduct->featured_image) : asset('images/no-image.png') }}" alt="{{$featuredProduct->name ?? '-'}}" />
                                                        <img class="hover-img" src="{{ $featuredProduct->featured_image ? asset(config('imagepath.product') . $featuredProduct->featured_image) : asset('images/no-image.png') }}" alt="{{$featuredProduct->name ?? '-'}}" />
                                                    </a>
                                                </div>
                                                <div class="product-action-1">
                                                    <a aria-label="Add To Wishlist" class="action-btn small hover-up" href="#"><i class="fi-rs-heart"></i></a>
                                                    <a href="javascript:void(0)"
                                                       class="action-btn quick-view-btn"
                                                       data-product="{{ encrypt($featuredProduct->id) }}">
                                                        <i class="fi-rs-eye"></i>
                                                    </a>
                                                </div>

                                                @php
                                                    $currency = $settings->currency_symbol ?? 'TK';
                                                    $discount = null;

                                                    if ($featuredProduct->discount_type == 'amount' && !empty($featuredProduct->discount_amount)) {
                                                        $discount = '- ' . $currency . ' ' . number_format($featuredProduct->discount_amount, 2);
                                                    } elseif ($featuredProduct->discount_type == 'percent' && !empty($featuredProduct->discount_amount)) {
                                                        $discount = '- ' . number_format($featuredProduct->discount_amount, 0) . '%';
                                                    }
                                                @endphp
                                                @if($featuredProduct->discount_type)
                                                    <div class="product-badges product-badges-position product-badges-mrg">
                                                        <span class="new">{{$discount}}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="product-content-wrap">
                                                <div class="product-category">
                                                    <a href="{{route('category.products',$featuredProduct->category->slug)}}">{{$featuredProduct->category->name ?? ' '}}</a>
                                                </div>
                                                <h2><a href="{{route('product.details',encrypt($featuredProduct->id))}}" class="product-title-shamim">{{$featuredProduct->name ?? '-'}} </a></h2>
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
                                                <div class="product-price mt-10 mb-10">
                                                    @php
                                                        $price = $featuredProduct->selling_price ?? 0;
                                                        $currency = $settings->currency_symbol ?? 'TK';

                                                        if ($featuredProduct->discount_type == 'amount') {
                                                            $finalPrice = $price - ($featuredProduct->discount_amount ?? 0);
                                                        } elseif ($featuredProduct->discount_type == 'percent') {
                                                            $finalPrice = $price - ($price * ($featuredProduct->discount_amount ?? 0) / 100);
                                                        } else {
                                                            $finalPrice = $price;
                                                        }
                                                    @endphp
                                                    <span>{{ $currency }} {{ number_format($finalPrice, 2) }}</span>
                                                    @if($featuredProduct->discount_type)
                                                        <span class="old-price">{{ $currency }} {{ number_format($price, 2) }}</span>
                                                    @endif
                                                </div>
{{--                                                <div class="sold mt-15 mb-15">--}}
{{--                                                    <div class="progress mb-5">--}}
{{--                                                        <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuemin="0" aria-valuemax="100"></div>--}}
{{--                                                    </div>--}}
{{--                                                    <span class="font-xs text-heading"> Sold: 90/120</span>--}}
{{--                                                </div>--}}
                                                <a  href="javascript:void(0)" data-id="{{ $featuredProduct->id }}" class="btn w-100 hover-up  add-to-cart-btn"><i class="fi-rs-shopping-cart mr-5"></i>Add To Cart</a>
{{--                                                <a class="add add-to-cart-btn" href="javascript:void(0)" data-id="{{ $product->id }}"><i class="fi-rs-shopping-cart mr-5"></i>Add To Cart </a>--}}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
{{--                        <!--End tab-pane-->--}}
{{--                        <div class="tab-pane fade" id="tab-two-1" role="tabpanel" aria-labelledby="tab-two-1">--}}
{{--                            <div class="carausel-4-columns-cover arrow-center position-relative">--}}
{{--                                <div class="slider-arrow slider-arrow-2 carausel-4-columns-arrow" id="carausel-4-columns-2-arrows"></div>--}}
{{--                                <div class="carausel-4-columns carausel-arrow-center" id="carausel-4-columns-2">--}}
{{--                                    <div class="product-cart-wrap">--}}
{{--                                        <div class="product-img-action-wrap">--}}
{{--                                            <div class="product-img product-img-zoom">--}}
{{--                                                <a href="shop-product-right.html">--}}
{{--                                                    <img class="default-img" src="{{asset('web')}}/assets/imgs/shop/product-10-1.jpg" alt="" />--}}
{{--                                                    <img class="hover-img" src="{{asset('web')}}/assets/imgs/shop/product-10-2.jpg" alt="" />--}}
{{--                                                </a>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-action-1">--}}
{{--                                                <a aria-label="Quick view" class="action-btn small hover-up" data-bs-toggle="modal" data-bs-target="#quickViewModal"> <i class="fi-rs-eye"></i></a>--}}
{{--                                                <a aria-label="Add To Wishlist" class="action-btn small hover-up" href="shop-wishlist.html"><i class="fi-rs-heart"></i></a>--}}
{{--                                                <a aria-label="Compare" class="action-btn small hover-up" href="shop-compare.html"><i class="fi-rs-shuffle"></i></a>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-badges product-badges-position product-badges-mrg">--}}
{{--                                                <span class="hot">Save 15%</span>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="product-content-wrap">--}}
{{--                                            <div class="product-category">--}}
{{--                                                <a href="shop-grid-right.html">Hodo Foods</a>--}}
{{--                                            </div>--}}
{{--                                            <h2><a href="shop-product-right.html">Canada Dry Ginger Ale – 2 L Bottle</a></h2>--}}
{{--                                            <div class="product-rate d-inline-block">--}}
{{--                                                <div class="product-rating" style="width: 80%"></div>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-price mt-10">--}}
{{--                                                <span>$238.85 </span>--}}
{{--                                                <span class="old-price">$245.8</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="sold mt-15 mb-15">--}}
{{--                                                <div class="progress mb-5">--}}
{{--                                                    <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuemin="0" aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                                <span class="font-xs text-heading"> Sold: 90/120</span>--}}
{{--                                            </div>--}}
{{--                                            <a href="shop-cart.html" class="btn w-100 hover-up"><i class="fi-rs-shopping-cart mr-5"></i>Add To Cart</a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <!--End product Wrap-->--}}
{{--                                    <div class="product-cart-wrap">--}}
{{--                                        <div class="product-img-action-wrap">--}}
{{--                                            <div class="product-img product-img-zoom">--}}
{{--                                                <a href="shop-product-right.html">--}}
{{--                                                    <img class="default-img" src="{{asset('web')}}/assets/imgs/shop/product-15-1.jpg" alt="" />--}}
{{--                                                    <img class="hover-img" src="{{asset('web')}}/assets/imgs/shop/product-15-2.jpg" alt="" />--}}
{{--                                                </a>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-action-1">--}}
{{--                                                <a aria-label="Quick view" class="action-btn small hover-up" data-bs-toggle="modal" data-bs-target="#quickViewModal"> <i class="fi-rs-eye"></i></a>--}}
{{--                                                <a aria-label="Add To Wishlist" class="action-btn small hover-up" href="shop-wishlist.html"><i class="fi-rs-heart"></i></a>--}}
{{--                                                <a aria-label="Compare" class="action-btn small hover-up" href="shop-compare.html"><i class="fi-rs-shuffle"></i></a>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-badges product-badges-position product-badges-mrg">--}}
{{--                                                <span class="new">Save 35%</span>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="product-content-wrap">--}}
{{--                                            <div class="product-category">--}}
{{--                                                <a href="shop-grid-right.html">Hodo Foods</a>--}}
{{--                                            </div>--}}
{{--                                            <h2><a href="shop-product-right.html">Encore Seafoods Stuffed Alaskan</a></h2>--}}
{{--                                            <div class="product-rate d-inline-block">--}}
{{--                                                <div class="product-rating" style="width: 80%"></div>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-price mt-10">--}}
{{--                                                <span>$238.85 </span>--}}
{{--                                                <span class="old-price">$245.8</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="sold mt-15 mb-15">--}}
{{--                                                <div class="progress mb-5">--}}
{{--                                                    <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuemin="0" aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                                <span class="font-xs text-heading"> Sold: 90/120</span>--}}
{{--                                            </div>--}}
{{--                                            <a href="shop-cart.html" class="btn w-100 hover-up"><i class="fi-rs-shopping-cart mr-5"></i>Add To Cart</a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <!--End product Wrap-->--}}
{{--                                    <div class="product-cart-wrap">--}}
{{--                                        <div class="product-img-action-wrap">--}}
{{--                                            <div class="product-img product-img-zoom">--}}
{{--                                                <a href="shop-product-right.html">--}}
{{--                                                    <img class="default-img" src="{{asset('web')}}/assets/imgs/shop/product-12-1.jpg" alt="" />--}}
{{--                                                    <img class="hover-img" src="{{asset('web')}}/assets/imgs/shop/product-12-2.jpg" alt="" />--}}
{{--                                                </a>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-action-1">--}}
{{--                                                <a aria-label="Quick view" class="action-btn small hover-up" data-bs-toggle="modal" data-bs-target="#quickViewModal"> <i class="fi-rs-eye"></i></a>--}}
{{--                                                <a aria-label="Add To Wishlist" class="action-btn small hover-up" href="shop-wishlist.html"><i class="fi-rs-heart"></i></a>--}}
{{--                                                <a aria-label="Compare" class="action-btn small hover-up" href="shop-compare.html"><i class="fi-rs-shuffle"></i></a>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-badges product-badges-position product-badges-mrg">--}}
{{--                                                <span class="sale">Sale</span>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="product-content-wrap">--}}
{{--                                            <div class="product-category">--}}
{{--                                                <a href="shop-grid-right.html">Hodo Foods</a>--}}
{{--                                            </div>--}}
{{--                                            <h2><a href="shop-product-right.html">Gorton’s Beer Battered Fish </a></h2>--}}
{{--                                            <div class="product-rate d-inline-block">--}}
{{--                                                <div class="product-rating" style="width: 80%"></div>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-price mt-10">--}}
{{--                                                <span>$238.85 </span>--}}
{{--                                                <span class="old-price">$245.8</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="sold mt-15 mb-15">--}}
{{--                                                <div class="progress mb-5">--}}
{{--                                                    <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuemin="0" aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                                <span class="font-xs text-heading"> Sold: 90/120</span>--}}
{{--                                            </div>--}}
{{--                                            <a href="shop-cart.html" class="btn w-100 hover-up"><i class="fi-rs-shopping-cart mr-5"></i>Add To Cart</a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <!--End product Wrap-->--}}
{{--                                    <div class="product-cart-wrap">--}}
{{--                                        <div class="product-img-action-wrap">--}}
{{--                                            <div class="product-img product-img-zoom">--}}
{{--                                                <a href="shop-product-right.html">--}}
{{--                                                    <img class="default-img" src="{{asset('web')}}/assets/imgs/shop/product-13-1.jpg" alt="" />--}}
{{--                                                    <img class="hover-img" src="{{asset('web')}}/assets/imgs/shop/product-13-2.jpg" alt="" />--}}
{{--                                                </a>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-action-1">--}}
{{--                                                <a aria-label="Quick view" class="action-btn small hover-up" data-bs-toggle="modal" data-bs-target="#quickViewModal"> <i class="fi-rs-eye"></i></a>--}}
{{--                                                <a aria-label="Add To Wishlist" class="action-btn small hover-up" href="shop-wishlist.html"><i class="fi-rs-heart"></i></a>--}}
{{--                                                <a aria-label="Compare" class="action-btn small hover-up" href="shop-compare.html"><i class="fi-rs-shuffle"></i></a>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-badges product-badges-position product-badges-mrg">--}}
{{--                                                <span class="best">Best sale</span>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="product-content-wrap">--}}
{{--                                            <div class="product-category">--}}
{{--                                                <a href="shop-grid-right.html">Hodo Foods</a>--}}
{{--                                            </div>--}}
{{--                                            <h2><a href="shop-product-right.html">Haagen-Dazs Caramel Cone Ice</a></h2>--}}
{{--                                            <div class="product-rate d-inline-block">--}}
{{--                                                <div class="product-rating" style="width: 80%"></div>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-price mt-10">--}}
{{--                                                <span>$238.85 </span>--}}
{{--                                                <span class="old-price">$245.8</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="sold mt-15 mb-15">--}}
{{--                                                <div class="progress mb-5">--}}
{{--                                                    <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuemin="0" aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                                <span class="font-xs text-heading"> Sold: 90/120</span>--}}
{{--                                            </div>--}}
{{--                                            <a href="shop-cart.html" class="btn w-100 hover-up"><i class="fi-rs-shopping-cart mr-5"></i>Add To Cart</a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <!--End product Wrap-->--}}
{{--                                    <div class="product-cart-wrap">--}}
{{--                                        <div class="product-img-action-wrap">--}}
{{--                                            <div class="product-img product-img-zoom">--}}
{{--                                                <a href="shop-product-right.html">--}}
{{--                                                    <img class="default-img" src="{{asset('web')}}/assets/imgs/shop/product-14-1.jpg" alt="" />--}}
{{--                                                    <img class="hover-img" src="{{asset('web')}}/assets/imgs/shop/product-14-2.jpg" alt="" />--}}
{{--                                                </a>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-action-1">--}}
{{--                                                <a aria-label="Quick view" class="action-btn small hover-up" data-bs-toggle="modal" data-bs-target="#quickViewModal"> <i class="fi-rs-eye"></i></a>--}}
{{--                                                <a aria-label="Add To Wishlist" class="action-btn small hover-up" href="shop-wishlist.html"><i class="fi-rs-heart"></i></a>--}}
{{--                                                <a aria-label="Compare" class="action-btn small hover-up" href="shop-compare.html"><i class="fi-rs-shuffle"></i></a>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-badges product-badges-position product-badges-mrg">--}}
{{--                                                <span class="hot">Save 15%</span>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="product-content-wrap">--}}
{{--                                            <div class="product-category">--}}
{{--                                                <a href="shop-grid-right.html">Hodo Foods</a>--}}
{{--                                            </div>--}}
{{--                                            <h2><a href="shop-product-right.html">Italian-Style Chicken Meatball</a></h2>--}}
{{--                                            <div class="product-rate d-inline-block">--}}
{{--                                                <div class="product-rating" style="width: 80%"></div>--}}
{{--                                            </div>--}}
{{--                                            <div class="product-price mt-10">--}}
{{--                                                <span>$238.85 </span>--}}
{{--                                                <span class="old-price">$245.8</span>--}}
{{--                                            </div>--}}
{{--                                            <div class="sold mt-15 mb-15">--}}
{{--                                                <div class="progress mb-5">--}}
{{--                                                    <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuemin="0" aria-valuemax="100"></div>--}}
{{--                                                </div>--}}
{{--                                                <span class="font-xs text-heading"> Sold: 90/120</span>--}}
{{--                                            </div>--}}
{{--                                            <a href="shop-cart.html" class="btn w-100 hover-up"><i class="fi-rs-shopping-cart mr-5"></i>Add To Cart</a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <!--End product Wrap-->--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="tab-pane fade" id="tab-three-1" role="tabpanel" aria-labelledby="tab-three-1">
                            <div class="carausel-4-columns-cover arrow-center position-relative">
                                <div class="slider-arrow slider-arrow-2 carausel-4-columns-arrow" id="carausel-4-columns-3-arrows"></div>
                                <div class="carausel-4-columns carausel-arrow-center" id="carausel-4-columns-3">
                                    @foreach($products as $product)
                                        <div class="product-cart-wrap">
                                            <div class="product-img-action-wrap">
                                                <div class="product-img product-img-zoom">
                                                    <a href="{{route('product.details',encrypt($product->id))}}">
                                                        <img class="default-img" src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{$product->name ?? '-'}}" />
                                                        <img class="hover-img" src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{$product->name ?? '-'}}" />
                                                    </a>
                                                </div>
                                                <div class="product-action-1">
                                                    <a aria-label="Add To Wishlist" class="action-btn small hover-up" href="#"><i class="fi-rs-heart"></i></a>
                                                    <a href="javascript:void(0)"
                                                       class="action-btn quick-view-btn"
                                                       data-product="{{ encrypt($product->id) }}">
                                                        <i class="fi-rs-eye"></i>
                                                    </a>
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
                                                @if($product->discount_type)
                                                    <div class="product-badges product-badges-position product-badges-mrg">
                                                        <span class="new">{{$discount}}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="product-content-wrap">
                                                <div class="product-category">
                                                    <a href="{{route('category.products',$product->category->slug)}}">{{$product->category->name ?? ' '}}</a>
                                                </div>
                                                <h2><a href="{{route('product.details',encrypt($product->id))}}" class="product-title-shamim">{{$product->name ?? '-'}} </a></h2>
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
                                                <div class="product-price mt-10 mb-10">
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
                                                    <span>{{ $currency }} {{ number_format($finalPrice, 2) }}</span>
                                                    @if($product->discount_type)
                                                        <span class="old-price">{{ $currency }} {{ number_format($price, 2) }}</span>
                                                    @endif
                                                </div>
                                                {{--                                                <div class="sold mt-15 mb-15">--}}
                                                {{--                                                    <div class="progress mb-5">--}}
                                                {{--                                                        <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuemin="0" aria-valuemax="100"></div>--}}
                                                {{--                                                    </div>--}}
                                                {{--                                                    <span class="font-xs text-heading"> Sold: 90/120</span>--}}
                                                {{--                                                </div>--}}
                                                <a  href="javascript:void(0)" data-id="{{ $product->id }}" class="btn w-100 hover-up  add-to-cart-btn"><i class="fi-rs-shopping-cart mr-5"></i>Add To Cart</a>
                                                {{--                                                <a class="add add-to-cart-btn" href="javascript:void(0)" data-id="{{ $product->id }}"><i class="fi-rs-shopping-cart mr-5"></i>Add To Cart </a>--}}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--End tab-content-->
                </div>
                <!--End Col-lg-9-->
            </div>
        </div>
    </section>
    <!--End Best Sales-->
{{--    <section class="section-padding pb-5">--}}
{{--        <div class="container">--}}
{{--            <div class="section-title wow animate__animated animate__fadeIn" data-wow-delay="0">--}}
{{--                <h3 class="">Deals Of The Day</h3>--}}
{{--                <a class="show-all" href="shop-grid-right.html">--}}
{{--                    All Deals--}}
{{--                    <i class="fi-rs-angle-right"></i>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--                <div class="col-xl-3 col-lg-4 col-md-6">--}}
{{--                    <div class="product-cart-wrap style-2 wow animate__animated animate__fadeInUp" data-wow-delay="0">--}}
{{--                        <div class="product-img-action-wrap">--}}
{{--                            <div class="product-img">--}}
{{--                                <a href="shop-product-right.html">--}}
{{--                                    <img src="{{asset('web')}}/assets/imgs/banner/banner-5.png" alt="" />--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="product-content-wrap">--}}
{{--                            <div class="deals-countdown-wrap">--}}
{{--                                <div class="deals-countdown" data-countdown="2025/03/25 00:00:00"></div>--}}
{{--                            </div>--}}
{{--                            <div class="deals-content">--}}
{{--                                <h2><a href="shop-product-right.html">Seeds of Change Organic Quinoa, Brown, & Red Rice</a></h2>--}}
{{--                                @php--}}
{{--    $totalReviews = isset($product) && $product->approvedReviews ? $product->approvedReviews->count() : 0;--}}
{{--    $avgRating = $totalReviews > 0 ? $product->approvedReviews->avg('rating') : 0;--}}
{{--    $percentRating = $avgRating * 20;--}}
{{--@endphp--}}
{{--<div class="product-rate-cover">--}}
{{--    <div class="product-rate d-inline-block">--}}
{{--        <div class="product-rating" style="width: {{ $percentRating }}%"></div>--}}
{{--    </div>--}}
{{--    <span class="font-small ml-5 text-muted"> ({{ number_format($avgRating, 1) }})</span>--}}
{{--</div>--}}
{{--                                <div>--}}
{{--                                    <span class="font-small text-muted">By <a href="vendor-details-1.html">NestFood</a></span>--}}
{{--                                </div>--}}
{{--                                <div class="product-card-bottom">--}}
{{--                                    <div class="product-price">--}}
{{--                                        <span>$32.85</span>--}}
{{--                                        <span class="old-price">$33.8</span>--}}
{{--                                    </div>--}}
{{--                                    <div class="add-cart">--}}
{{--                                        <a class="add" href="shop-cart.html"><i class="fi-rs-shopping-cart mr-5"></i>Add </a>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-xl-3 col-lg-4 col-md-6">--}}
{{--                    <div class="product-cart-wrap style-2 wow animate__animated animate__fadeInUp" data-wow-delay=".1s">--}}
{{--                        <div class="product-img-action-wrap">--}}
{{--                            <div class="product-img">--}}
{{--                                <a href="shop-product-right.html">--}}
{{--                                    <img src="{{asset('web')}}/assets/imgs/banner/banner-6.png" alt="" />--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="product-content-wrap">--}}
{{--                            <div class="deals-countdown-wrap">--}}
{{--                                <div class="deals-countdown" data-countdown="2026/04/25 00:00:00"></div>--}}
{{--                            </div>--}}
{{--                            <div class="deals-content">--}}
{{--                                <h2><a href="shop-product-right.html">Perdue Simply Smart Organics Gluten Free</a></h2>--}}
{{--                                @php--}}
{{--    $totalReviews = isset($product) && $product->approvedReviews ? $product->approvedReviews->count() : 0;--}}
{{--    $avgRating = $totalReviews > 0 ? $product->approvedReviews->avg('rating') : 0;--}}
{{--    $percentRating = $avgRating * 20;--}}
{{--@endphp--}}
{{--<div class="product-rate-cover">--}}
{{--    <div class="product-rate d-inline-block">--}}
{{--        <div class="product-rating" style="width: {{ $percentRating }}%"></div>--}}
{{--    </div>--}}
{{--    <span class="font-small ml-5 text-muted"> ({{ number_format($avgRating, 1) }})</span>--}}
{{--</div>--}}
{{--                                <div>--}}
{{--                                    <span class="font-small text-muted">By <a href="vendor-details-1.html">Old El Paso</a></span>--}}
{{--                                </div>--}}
{{--                                <div class="product-card-bottom">--}}
{{--                                    <div class="product-price">--}}
{{--                                        <span>$24.85</span>--}}
{{--                                        <span class="old-price">$26.8</span>--}}
{{--                                    </div>--}}
{{--                                    <div class="add-cart">--}}
{{--                                        <a class="add" href="shop-cart.html"><i class="fi-rs-shopping-cart mr-5"></i>Add </a>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-xl-3 col-lg-4 col-md-6 d-none d-lg-block">--}}
{{--                    <div class="product-cart-wrap style-2 wow animate__animated animate__fadeInUp" data-wow-delay=".2s">--}}
{{--                        <div class="product-img-action-wrap">--}}
{{--                            <div class="product-img">--}}
{{--                                <a href="shop-product-right.html">--}}
{{--                                    <img src="{{asset('web')}}/assets/imgs/banner/banner-7.png" alt="" />--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="product-content-wrap">--}}
{{--                            <div class="deals-countdown-wrap">--}}
{{--                                <div class="deals-countdown" data-countdown="2027/03/25 00:00:00"></div>--}}
{{--                            </div>--}}
{{--                            <div class="deals-content">--}}
{{--                                <h2><a href="shop-product-right.html">Signature Wood-Fired Mushroom and Caramelized</a></h2>--}}
{{--                                @php--}}
{{--                                $totalReviews = isset($product) && $product->approvedReviews ? $product->approvedReviews->count() : 0;--}}
{{--                                $avgRating = $totalReviews > 0 ? $product->approvedReviews->avg('rating') : 0;--}}
{{--                                $percentRating = $avgRating * 20;--}}
{{--                            @endphp--}}
{{--                            <div class="product-rate-cover">--}}
{{--                                <div class="product-rate d-inline-block">--}}
{{--                                    <div class="product-rating" style="width: {{ $percentRating }}%"></div>--}}
{{--                                </div>--}}
{{--                                <span class="font-small ml-5 text-muted"> ({{ number_format($avgRating, 1) }})</span>--}}
{{--                            </div>--}}
{{--                                <div>--}}
{{--                                    <span class="font-small text-muted">By <a href="vendor-details-1.html">Progresso</a></span>--}}
{{--                                </div>--}}
{{--                                <div class="product-card-bottom">--}}
{{--                                    <div class="product-price">--}}
{{--                                        <span>$12.85</span>--}}
{{--                                        <span class="old-price">$13.8</span>--}}
{{--                                    </div>--}}
{{--                                    <div class="add-cart">--}}
{{--                                        <a class="add" href="shop-cart.html"><i class="fi-rs-shopping-cart mr-5"></i>Add </a>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-xl-3 col-lg-4 col-md-6 d-none d-xl-block">--}}
{{--                    <div class="product-cart-wrap style-2 wow animate__animated animate__fadeInUp" data-wow-delay=".3s">--}}
{{--                        <div class="product-img-action-wrap">--}}
{{--                            <div class="product-img">--}}
{{--                                <a href="shop-product-right.html">--}}
{{--                                    <img src="{{asset('web')}}/assets/imgs/banner/banner-8.png" alt="" />--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="product-content-wrap">--}}
{{--                            <div class="deals-countdown-wrap">--}}
{{--                                <div class="deals-countdown" data-countdown="2025/02/25 00:00:00"></div>--}}
{{--                            </div>--}}
{{--                            <div class="deals-content">--}}
{{--                                <h2><a href="shop-product-right.html">Simply Lemonade with Raspberry Juice</a></h2>--}}
{{--                                @php--}}
{{--    $totalReviews = isset($product) && $product->approvedReviews ? $product->approvedReviews->count() : 0;--}}
{{--    $avgRating = $totalReviews > 0 ? $product->approvedReviews->avg('rating') : 0;--}}
{{--    $percentRating = $avgRating * 20;--}}
{{--@endphp--}}
{{--<div class="product-rate-cover">--}}
{{--    <div class="product-rate d-inline-block">--}}
{{--        <div class="product-rating" style="width: {{ $percentRating }}%"></div>--}}
{{--    </div>--}}
{{--    <span class="font-small ml-5 text-muted"> ({{ number_format($avgRating, 1) }})</span>--}}
{{--</div>--}}
{{--                                <div>--}}
{{--                                    <span class="font-small text-muted">By <a href="vendor-details-1.html">Yoplait</a></span>--}}
{{--                                </div>--}}
{{--                                <div class="product-card-bottom">--}}
{{--                                    <div class="product-price">--}}
{{--                                        <span>$15.85</span>--}}
{{--                                        <span class="old-price">$16.8</span>--}}
{{--                                    </div>--}}
{{--                                    <div class="add-cart">--}}
{{--                                        <a class="add" href="shop-cart.html"><i class="fi-rs-shopping-cart mr-5"></i>Add </a>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </section>--}}
    <!--End Deals-->
    <section class="section-padding mb-30">
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-4 col-md-6 mb-sm-5 mb-md-0 wow animate__animated animate__fadeInUp" data-wow-delay="0">
                    <h4 class="section-title style-1 mb-30 animated animated">Top Selling</h4>
                    <div class="product-list-small animated animated">
                        @foreach($topSellingProducts as $product)
                            <article class="row align-items-center hover-up">
                                <figure class="col-md-4 mb-0">
                                    <a href="{{ route('product.details', encrypt($product->id)) }}"><img src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{ $product->name }}" /></a>
                                </figure>
                                <div class="col-md-8 mb-0">
                                    <h6>
                                        <a href="{{ route('product.details', encrypt($product->id)) }}">{{ Str::limit($product->name, 40) }}</a>
                                    </h6>
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
                                    <div class="product-price">
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
                                        <span>{{ $currency }} {{ number_format($finalPrice, 2) }}</span>
                                        @if($product->discount_type)
                                            <span class="old-price">{{ $currency }} {{ number_format($price, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-md-0 wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                    <h4 class="section-title style-1 mb-30 animated animated">Trending Products</h4>
                    <div class="product-list-small animated animated">
                        @foreach($featuredProducts3 as $product)
                            <article class="row align-items-center hover-up">
                                <figure class="col-md-4 mb-0">
                                    <a href="{{ route('product.details', encrypt($product->id)) }}"><img src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{ $product->name }}" /></a>
                                </figure>
                                <div class="col-md-8 mb-0">
                                    <h6>
                                        <a href="{{ route('product.details', encrypt($product->id)) }}">{{ Str::limit($product->name, 40) }}</a>
                                    </h6>
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
                                    <div class="product-price">
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
                                        <span>{{ $currency }} {{ number_format($finalPrice, 2) }}</span>
                                        @if($product->discount_type)
                                            <span class="old-price">{{ $currency }} {{ number_format($price, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-sm-5 mb-md-0 d-none d-lg-block wow animate__animated animate__fadeInUp" data-wow-delay=".2s">
                    <h4 class="section-title style-1 mb-30 animated animated">Recently added</h4>
                    <div class="product-list-small animated animated">
                        @foreach($recentProducts3 as $product)
                            <article class="row align-items-center hover-up">
                                <figure class="col-md-4 mb-0">
                                    <a href="{{ route('product.details', encrypt($product->id)) }}"><img src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{ $product->name }}" /></a>
                                </figure>
                                <div class="col-md-8 mb-0">
                                    <h6>
                                        <a href="{{ route('product.details', encrypt($product->id)) }}">{{ Str::limit($product->name, 40) }}</a>
                                    </h6>
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
                                    <div class="product-price">
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
                                        <span>{{ $currency }} {{ number_format($finalPrice, 2) }}</span>
                                        @if($product->discount_type)
                                            <span class="old-price">{{ $currency }} {{ number_format($price, 2) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-sm-5 mb-md-0 d-none d-xl-block wow animate__animated animate__fadeInUp" data-wow-delay=".3s">
                    <h4 class="section-title style-1 mb-30 animated animated">Top Rated</h4>
                    <div class="product-list-small animated animated">
                        <article class="row align-items-center hover-up">
                            <figure class="col-md-4 mb-0">
                                <a href="shop-product-right.html"><img src="{{asset('web')}}/assets/imgs/shop/thumbnail-10.jpg" alt="" /></a>
                            </figure>
                            <div class="col-md-8 mb-0">
                                <h6>
                                    <a href="shop-product-right.html">Foster Farms Takeout Crispy Classic Buffalo Wings</a>
                                </h6>
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
                                <div class="product-price">
                                    <span>$32.85</span>
                                    <span class="old-price">$33.8</span>
                                </div>
                            </div>
                        </article>
                        <article class="row align-items-center hover-up">
                            <figure class="col-md-4 mb-0">
                                <a href="shop-product-right.html"><img src="{{asset('web')}}/assets/imgs/shop/thumbnail-11.jpg" alt="" /></a>
                            </figure>
                            <div class="col-md-8 mb-0">
                                <h6>
                                    <a href="shop-product-right.html">Angie’s Boomchickapop Sweet & Salty Kettle Corn</a>
                                </h6>
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
                                <div class="product-price">
                                    <span>$32.85</span>
                                    <span class="old-price">$33.8</span>
                                </div>
                            </div>
                        </article>
                        <article class="row align-items-center hover-up">
                            <figure class="col-md-4 mb-0">
                                <a href="shop-product-right.html"><img src="{{asset('web')}}/assets/imgs/shop/thumbnail-12.jpg" alt="" /></a>
                            </figure>
                            <div class="col-md-8 mb-0">
                                <h6>
                                    <a href="shop-product-right.html">All Natural Italian-Style Chicken Meatballs</a>
                                </h6>
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
                                <div class="product-price">
                                    <span>$32.85</span>
                                    <span class="old-price">$33.8</span>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--End 4 columns-->
@endsection
