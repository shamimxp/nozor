@extends('frontend.layouts.app')
@section('content')
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.html" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                <span></span> Shop <span></span> Wishlist
            </div>
        </div>
    </div>
    <div class="container mb-30 mt-10">
        <div class="row">
            <div class="col-xl-10 col-lg-12 m-auto">
                <div class="mb-1">
                    <h6 class="text-body">There are <span class="text-brand">5</span> products in this list</h6>
                </div>
                <div class="table-responsive shopping-summery">
                    <table class="table table-wishlist">
                        <thead>
                        <tr class="main-heading">
                            <th class="custome-checkbox start pl-30">
                                <input class="form-check-input" type="checkbox" name="checkbox" id="exampleCheckbox11" value="" />
                                <label class="form-check-label" for="exampleCheckbox11"></label>
                            </th>
                            <th scope="col" colspan="2">Product</th>
                            <th scope="col">Price</th>
                            <th scope="col">Stock Status</th>
                            <th scope="col">Action</th>
                            <th scope="col" class="end">Remove</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="pt-30">
                            <td class="custome-checkbox pl-30">
                                <input class="form-check-input" type="checkbox" name="checkbox" id="exampleCheckbox1" value="" />
                                <label class="form-check-label" for="exampleCheckbox1"></label>
                            </td>
                            <td class="image product-thumbnail pt-40"><img src="{{asset('web')}}/assets/imgs/shop/product-1-1.jpg" alt="#" /></td>
                            <td class="product-des product-name">
                                <h6><a class="product-name mb-10" href="shop-product-right.html">Field Roast Chao Cheese Creamy Original</a></h6>
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
                            </td>
                            <td class="price" data-title="Price">
                                <h3 class="text-brand">$2.51</h3>
                            </td>
                            <td class="text-center detail-info" data-title="Stock">
                                <span class="stock-status in-stock mb-0"> In Stock </span>
                            </td>
                            <td class="text-right" data-title="Cart">
                                <button class="btn btn-sm">Add to cart</button>
                            </td>
                            <td class="action text-center" data-title="Remove">
                                <a href="#" class="text-body"><i class="fi-rs-trash"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="custome-checkbox pl-30">
                                <input class="form-check-input" type="checkbox" name="checkbox" id="exampleCheckbox2" value="" />
                                <label class="form-check-label" for="exampleCheckbox2"></label>
                            </td>
                            <td class="image product-thumbnail"><img src="{{asset('web')}}/assets/imgs/shop/product-2-1.jpg" alt="#" /></td>
                            <td class="product-des product-name">
                                <h6><a class="product-name mb-10" href="shop-product-right.html">Blue Diamond Almonds Lightly Salted</a></h6>
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
                            </td>
                            <td class="price" data-title="Price">
                                <h3 class="text-brand">$3.2</h3>
                            </td>
                            <td class="text-center detail-info" data-title="Stock">
                                <span class="stock-status in-stock mb-0"> In Stock </span>
                            </td>
                            <td class="text-right" data-title="Cart">
                                <button class="btn btn-sm">Add to cart</button>
                            </td>
                            <td class="action text-center" data-title="Remove">
                                <a href="#" class="text-body"><i class="fi-rs-trash"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="custome-checkbox pl-30">
                                <input class="form-check-input" type="checkbox" name="checkbox" id="exampleCheckbox3" value="" />
                                <label class="form-check-label" for="exampleCheckbox3"></label>
                            </td>
                            <td class="image product-thumbnail"><img src="{{asset('web')}}/assets/imgs/shop/product-3-1.jpg" alt="#" /></td>
                            <td class="product-des product-name">
                                <h6><a class="product-name mb-10" href="shop-product-right.html">Fresh Organic Mustard Leaves Bell Pepper</a></h6>
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
                            </td>
                            <td class="price" data-title="Price">
                                <h3 class="text-brand">$2.43</h3>
                            </td>
                            <td class="text-center detail-info" data-title="Stock">
                                <span class="stock-status in-stock mb-0"> In Stock </span>
                            </td>
                            <td class="text-right" data-title="Cart">
                                <button class="btn btn-sm">Add to cart</button>
                            </td>
                            <td class="action text-center" data-title="Remove">
                                <a href="#" class="text-body"><i class="fi-rs-trash"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="custome-checkbox pl-30">
                                <input class="form-check-input" type="checkbox" name="checkbox" id="exampleCheckbox4" value="" />
                                <label class="form-check-label" for="exampleCheckbox4"></label>
                            </td>
                            <td class="image product-thumbnail"><img src="{{asset('web')}}/assets/imgs/shop/product-4-1.jpg" alt="#" /></td>
                            <td class="product-des product-name">
                                <h6><a class="product-name mb-10" href="shop-product-right.html">Angie’s Boomchickapop Sweet & Salty </a></h6>
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
                            </td>
                            <td class="price" data-title="Price">
                                <h3 class="text-brand">$3.21</h3>
                            </td>
                            <td class="text-center detail-info" data-title="Stock">
                                <span class="stock-status out-stock mb-0"> Out Stock </span>
                            </td>
                            <td class="text-right" data-title="Cart">
                                <button class="btn btn-sm btn-secondary">Contact Us</button>
                            </td>
                            <td class="action text-center" data-title="Remove">
                                <a href="#" class="text-body"><i class="fi-rs-trash"></i></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="custome-checkbox pl-30">
                                <input class="form-check-input" type="checkbox" name="checkbox" id="exampleCheckbox5" value="" />
                                <label class="form-check-label" for="exampleCheckbox5"></label>
                            </td>
                            <td class="image product-thumbnail"><img src="{{asset('web')}}/assets/imgs/shop/product-5-1.jpg" alt="#" /></td>
                            <td class="product-des product-name">
                                <h6><a class="product-name mb-10" href="shop-product-right.html">Foster Farms Takeout Crispy Classic</a></h6>
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
                            </td>
                            <td class="price" data-title="Price">
                                <h3 class="text-brand">$3.17</h3>
                            </td>
                            <td class="text-center detail-info" data-title="Stock">
                                <span class="stock-status in-stock mb-0"> In Stock </span>
                            </td>
                            <td class="text-right" data-title="Cart">
                                <button class="btn btn-sm">Add to cart</button>
                            </td>
                            <td class="action text-center" data-title="Remove">
                                <a href="#" class="text-body"><i class="fi-rs-trash"></i></a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
