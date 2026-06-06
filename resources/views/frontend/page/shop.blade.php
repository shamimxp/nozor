@extends('frontend.layouts.app')
@section('content')
    <div class="container mb-30 mt-50">
        <div class="row">
            <div class="col-12">
                <div class="shop-product-fillter">
                    <div class="totall-product">
                        <p>We found <strong class="text-brand" id="total-product-count">{{ $totalProducts }}</strong> items for you!</p>
                    </div>
                    <div class="sort-by-product-area">
                        <div class="sort-by-cover mr-10 d-none">
                            <div class="sort-by-product-wrap">
                                <div class="sort-by">
                                    <span><i class="fi-rs-apps"></i>Show:</span>
                                </div>
                                <div class="sort-by-dropdown-wrap">
                                    <span> 50 <i class="fi-rs-angle-small-down"></i></span>
                                </div>
                            </div>
                            <div class="sort-by-dropdown">
                                <ul id="limit-dropdown">
                                    <li><a class="active limit-option" data-value="50" href="#">50</a></li>
                                    <li><a class="limit-option" data-value="100" href="#">100</a></li>
                                    <li><a class="limit-option" data-value="150" href="#">150</a></li>
                                    <li><a class="limit-option" data-value="200" href="#">200</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="sort-by-cover">
                            <div class="sort-by-product-wrap">
                                <div class="sort-by">
                                    <span><i class="fi-rs-apps-sort"></i>Sort by:</span>
                                </div>
                                <div class="sort-by-dropdown-wrap">
                                    <span> Featured <i class="fi-rs-angle-small-down"></i></span>
                                </div>
                            </div>
                            <div class="sort-by-dropdown">
                                <ul id="sort-dropdown">
                                    <li><a class="active sort-option" data-value="latest" href="#">Featured</a></li>
                                    <li><a class="sort-option" data-value="price_low" href="#">Price: Low to High</a></li>
                                    <li><a class="sort-option" data-value="price_high" href="#">Price: High to Low</a></li>
                                    <li><a class="sort-option" data-value="rating" href="#">Avg. Rating</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row product-grid" id="product-list-container">
                    @include('frontend.partials.shop_products')
                </div>
                
                <div class="row mt-30 mb-30 text-center" id="load-more-container" style="display: {{ $products->count() < $totalProducts ? 'block' : 'none' }}">
                    <div class="col-12">
                        <button id="load-more-btn" class="btn btn-brand">Load More</button>
                    </div>
                </div>
                <section class="section-padding pb-5">
                    <div class="section-title">
                        <h3 class="">Deals Of The Day</h3>
                        <a class="show-all" href="shop-grid-right.html">
                            All Deals
                            <i class="fi-rs-angle-right"></i>
                        </a>
                    </div>
                    <div class="row">
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="product-cart-wrap style-2">
                                <div class="product-img-action-wrap">
                                    <div class="product-img">
                                        <a href="shop-product-right.html">
                                            <img src="assets/imgs/banner/banner-5.png" alt="" />
                                        </a>
                                    </div>
                                </div>
                                <div class="product-content-wrap">
                                    <div class="deals-countdown-wrap">
                                        <div class="deals-countdown" data-countdown="2025/03/25 00:00:00"></div>
                                    </div>
                                    <div class="deals-content">
                                        <h2><a href="shop-product-right.html">Seeds of Change Organic Quinoa, Brown</a></h2>
                                        <div class="product-rate-cover">
                                            <div class="product-rate d-inline-block">
                                                <div class="product-rating" style="width: 90%"></div>
                                            </div>
                                            <span class="font-small ml-5 text-muted"> (4.0)</span>
                                        </div>
                                        <div>
                                            <span class="font-small text-muted">By <a href="vendor-details-1.html">NestFood</a></span>
                                        </div>
                                        <div class="product-card-bottom">
                                            <div class="product-price">
                                                <span>$32.85</span>
                                                <span class="old-price">$33.8</span>
                                            </div>
                                            <div class="add-cart">
                                                <a class="add" href="shop-cart.html"><i class="fi-rs-shopping-cart mr-5"></i>Add </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="product-cart-wrap style-2">
                                <div class="product-img-action-wrap">
                                    <div class="product-img">
                                        <a href="shop-product-right.html">
                                            <img src="assets/imgs/banner/banner-6.png" alt="" />
                                        </a>
                                    </div>
                                </div>
                                <div class="product-content-wrap">
                                    <div class="deals-countdown-wrap">
                                        <div class="deals-countdown" data-countdown="2026/04/25 00:00:00"></div>
                                    </div>
                                    <div class="deals-content">
                                        <h2><a href="shop-product-right.html">Perdue Simply Smart Organics Gluten</a></h2>
                                        <div class="product-rate-cover">
                                            <div class="product-rate d-inline-block">
                                                <div class="product-rating" style="width: 90%"></div>
                                            </div>
                                            <span class="font-small ml-5 text-muted"> (4.0)</span>
                                        </div>
                                        <div>
                                            <span class="font-small text-muted">By <a href="vendor-details-1.html">Old El Paso</a></span>
                                        </div>
                                        <div class="product-card-bottom">
                                            <div class="product-price">
                                                <span>$24.85</span>
                                                <span class="old-price">$26.8</span>
                                            </div>
                                            <div class="add-cart">
                                                <a class="add" href="shop-cart.html"><i class="fi-rs-shopping-cart mr-5"></i>Add </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 d-none d-lg-block">
                            <div class="product-cart-wrap style-2">
                                <div class="product-img-action-wrap">
                                    <div class="product-img">
                                        <a href="shop-product-right.html">
                                            <img src="assets/imgs/banner/banner-7.png" alt="" />
                                        </a>
                                    </div>
                                </div>
                                <div class="product-content-wrap">
                                    <div class="deals-countdown-wrap">
                                        <div class="deals-countdown" data-countdown="2027/03/25 00:00:00"></div>
                                    </div>
                                    <div class="deals-content">
                                        <h2><a href="shop-product-right.html">Signature Wood-Fired Mushroom</a></h2>
                                        <div class="product-rate-cover">
                                            <div class="product-rate d-inline-block">
                                                <div class="product-rating" style="width: 80%"></div>
                                            </div>
                                            <span class="font-small ml-5 text-muted"> (3.0)</span>
                                        </div>
                                        <div>
                                            <span class="font-small text-muted">By <a href="vendor-details-1.html">Progresso</a></span>
                                        </div>
                                        <div class="product-card-bottom">
                                            <div class="product-price">
                                                <span>$12.85</span>
                                                <span class="old-price">$13.8</span>
                                            </div>
                                            <div class="add-cart">
                                                <a class="add" href="shop-cart.html"><i class="fi-rs-shopping-cart mr-5"></i>Add </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-6 d-none d-xl-block">
                            <div class="product-cart-wrap style-2">
                                <div class="product-img-action-wrap">
                                    <div class="product-img">
                                        <a href="shop-product-right.html">
                                            <img src="assets/imgs/banner/banner-8.png" alt="" />
                                        </a>
                                    </div>
                                </div>
                                <div class="product-content-wrap">
                                    <div class="deals-countdown-wrap">
                                        <div class="deals-countdown" data-countdown="2025/02/25 00:00:00"></div>
                                    </div>
                                    <div class="deals-content">
                                        <h2><a href="shop-product-right.html">Simply Lemonade with Raspberry Juice</a></h2>
                                        <div class="product-rate-cover">
                                            <div class="product-rate d-inline-block">
                                                <div class="product-rating" style="width: 80%"></div>
                                            </div>
                                            <span class="font-small ml-5 text-muted"> (3.0)</span>
                                        </div>
                                        <div>
                                            <span class="font-small text-muted">By <a href="vendor-details-1.html">Yoplait</a></span>
                                        </div>
                                        <div class="product-card-bottom">
                                            <div class="product-price">
                                                <span>$15.85</span>
                                                <span class="old-price">$16.8</span>
                                            </div>
                                            <div class="add-cart">
                                                <a class="add" href="shop-cart.html"><i class="fi-rs-shopping-cart mr-5"></i>Add </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--End Deals-->
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let currentSort = '{{ request("sort", "latest") }}';
        let scrollPage  = 0;          // 0 = no scroll yet; increments on each scroll fetch
        let isLoading   = false;
        let hasMorePages = {{ $products->count() < $totalProducts ? 'true' : 'false' }};

        // Called by Load More button
        function loadMore() {
            if (isLoading || !hasMorePages) return;
            isLoading = true;
            scrollPage++;

            let btn = $('#load-more-btn');
            btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            let url = window.location.pathname
                    + '?scroll_page=' + scrollPage
                    + '&sort=' + currentSort;

            $.ajax({
                url: url,
                type: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    $('#product-list-container').append(response.html);
                    $('#total-product-count').text(response.total);
                    hasMorePages = response.has_more;
                    
                    if (hasMorePages) {
                        $('#load-more-container').show();
                    } else {
                        $('#load-more-container').hide();
                    }
                    
                    btn.html('Load More');
                    isLoading = false;
                },
                error: function() {
                    btn.html('Load More');
                    isLoading = false;
                }
            });
        }

        // Reset and reload all (used by sort change)
        function reloadAll() {
            isLoading    = false;
            hasMorePages = true;
            scrollPage   = 0;

            $('#product-list-container').html(
                '<div class="col-12 text-center mt-50"><div class="spinner-border text-brand" role="status"></div></div>'
            );

            // Reload page=initial via AJAX (scroll_page=0 means no offset, return 50)
            // We use scroll_page=0 special case: backend returns first 50
            $.ajax({
                url: window.location.pathname + '?scroll_page=0&sort=' + currentSort,
                type: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function(response) {
                    $('#product-list-container').html(response.html);
                    $('#total-product-count').text(response.total);
                    hasMorePages = response.has_more;
                    
                    if (hasMorePages) {
                        $('#load-more-container').show();
                    } else {
                        $('#load-more-container').hide();
                    }
                    
                    isLoading = false;
                },
                error: function() { isLoading = false; }
            });
        }

        // Load More button click event
        $(document).on('click', '#load-more-btn', function(e) {
            e.preventDefault();
            loadMore();
        });

        // Sort change
        $('.sort-option').on('click', function(e) {
            e.preventDefault();
            $('.sort-option').removeClass('active');
            $(this).addClass('active');
            currentSort = $(this).data('value');
            $(this).closest('.sort-by-cover').find('.sort-by-dropdown-wrap span')
                   .html($(this).text() + ' <i class="fi-rs-angle-small-down"></i>');
            reloadAll();
        });
    });
</script>
@endpush
