<header class="header-area header-style-1 header-height-2">
    <div class="mobile-promotion">
        <span>Grand opening, <strong>up to 15%</strong> off all items. Only <strong>3 days</strong> left</span>
    </div>
    <div class="header-middle header-middle-ptb-1 d-none d-lg-block">
        <div class="container">
            <div class="header-wrap">
                <div class="logo logo-width-1">
                    <a href="{{Url('/')}}"><img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="logo" /></a>
                </div>
                <div class="header-right">
                    <div class="search-style-2" style="position: relative;">
                        <form action="{{ route('shop') }}" method="GET">
                            <select class="select-active" name="category" id="search-category-desktop">
                                <option value="">All Categories</option>
                                @foreach(\App\Models\Category::where('status', 1)->get() as $cat)
                                    <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="q" id="search-input-desktop" placeholder="Search for items..." autocomplete="off" />
                            <div class="search-results-dropdown" id="search-results-desktop" style="display:none; position:absolute; top:100%; left:0; right:0; background:#fff; z-index:9999; border:1px solid #ececec; border-top:none; border-radius:0 0 10px 10px; max-height:400px; overflow-y:auto; box-shadow:0 10px 15px rgba(0,0,0,0.05); padding: 10px 0;">
                                <!-- Results go here -->
                            </div>
                        </form>
                    </div>
                    <div class="header-action-right">
                        <div class="header-action-2">
                            <div class="header-action-icon-2">
                                <a href="{{route('wishlist')}}">
                                    <img class="svgInject" alt="wishlist" src="{{asset('web')}}/assets/imgs/theme/icons/icon-heart.svg" />
                                    <span class="pro-count blue wishlist-count">{{ auth()->check() ? \App\Models\Wishlist::where('user_id', auth()->id())->count() : 0 }}</span>
                                </a>
                                <a href="{{route('wishlist')}}"><span class="lable">Wishlist</span></a>
                            </div>
                            <div class="header-action-icon-2 cart-drawer-container">
                                <a class="mini-cart-icon" href="javascript:void(0)" id="cart-drawer-trigger">
                                    <img alt="Nest" src="{{asset('web')}}/assets/imgs/theme/icons/icon-cart.svg" />
                                    <span class="pro-count blue cart-count">{{\App\Models\Cart::where('session_id', session()->getId())->sum('quantity')}}</span>
                                </a>
                                <a href="javascript:void(0)" id="cart-drawer-trigger-text"><span class="lable">Cart</span></a>

                                <!-- Overlay -->
                                <div id="cart-drawer-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 9998; transition: all 0.3s ease;"></div>

                                <!-- Drawer -->
                                <div id="cart-drawer" class="cart-drawer">
                                    <div class="cart-drawer-header">
                                        <h4>Your Cart</h4>
                                        <button class="cart-drawer-close" id="cart-drawer-close"><i class="fi-rs-cross-small"></i></button>
                                    </div>
                                    @php
                                        $cartItems = \App\Models\Cart::with('product')->where('session_id', session()->getId())->get();
                                        $cartTotal = $cartItems->sum(function($c) { return $c->price * $c->quantity; });
                                    @endphp
                                    <div class="cart-drawer-content">
                                        <ul class="dynamic-cart-list">
                                            @foreach($cartItems as $cItem)
                                            <li>
                                                <div class="shopping-cart-img">
                                                    <a href="{{ route('product.details', encrypt($cItem->product->id)) }}">
                                                        <img alt="Nest" src="{{ $cItem->product->featured_image ? asset(config('imagepath.product') . $cItem->product->featured_image) : asset('images/no-image.png') }}" />
                                                    </a>
                                                </div>
                                                <div class="shopping-cart-title">
                                                    <h4><a href="{{ route('product.details', encrypt($cItem->product->id)) }}">{{ \Illuminate\Support\Str::words($cItem->product->name, 2, '...') }}</a></h4>
                                                    <h4><span>{{ $cItem->quantity }} × </span>{{ $settings->currency_symbol ?? 'TK' }} {{ number_format($cItem->price, 2) }}</h4>
                                                </div>
                                                <div class="shopping-cart-delete">
                                                    <a href="javascript:void(0)" class="remove-cart-item" data-id="{{ $cItem->id }}" data-product-id="{{ $cItem->product_id }}"><i class="fi-rs-cross-small"></i></a>
                                                </div>
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="shopping-cart-footer cart-drawer-footer">
                                        <div class="shopping-cart-total">
                                            <h4>Total <span class="cart-total-amount">{{ $settings->currency_symbol ?? 'TK' }} {{ number_format($cartTotal, 2) }}</span></h4>
                                        </div>
                                        <div class="shopping-cart-button">
                                            <a href="{{route('cart')}}" class="outline">View cart</a>
                                            <a href="{{route('checkout')}}">Checkout</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           @guest
                                <div class="header-action-icon-2">
                                    <a href="{{route('login')}}">
                                        <img class="svgInject" alt="Nest" src="{{asset('web')}}/assets/imgs/theme/icons/icon-user.svg" />
                                    </a>
                                    <a href="{{route('login')}}"><span class="lable ml-0">Login</span></a>
                                </div>
                           @else
                            <div class="header-action-icon-2">
                                <a href="#">
                                    <img class="svgInject" alt="Nest" src="{{asset('web')}}/assets/imgs/theme/icons/icon-user.svg" />
                                </a>
                                <a href="#"><span class="lable ml-0">{{ auth()->user()->name ?? 'Account' }}</span></a>
                                <div class="cart-dropdown-wrap cart-dropdown-hm2 account-dropdown">
                                    <ul>
                                        <li>
                                            <a href="{{ route('my-account') }}"><i class="fi fi-rs-user mr-10"></i>My Account</a>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fi fi-rs-sign-out mr-10"></i>Sign out</a>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                         @endguest
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-bottom header-bottom-bg-color sticky-bar">
        <div class="container">
            <div class="header-wrap header-space-between position-relative">
                <div class="logo logo-width-1 d-block d-lg-none">
                    <a href="{{Url('/')}}"><img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="logo" /></a>
                </div>
                <div class="header-nav d-none d-lg-flex">
                    <div class="main-categori-wrap d-none d-lg-block">
                        <a class="categories-button-active" href="#">
                            <span class="fi-rs-apps"></span> <span class="et">Browse</span> All Categories
                            <i class="fi-rs-angle-down"></i>
                        </a>
                        <div class="categories-dropdown-wrap categories-dropdown-active-large font-heading">
                            <div class="d-flex categori-dropdown-inner">

                                <ul>
                                    @foreach($categories->take(5) as $category)
                                        <li>
                                            <a href="{{ route('category.products', $category->slug) }}">
                                                <img src="{{ !empty($category->image)
                                                        ? asset(config('imagepath.category') . $category->image)
                                                        : asset('images/no-image.png') }}"
                                                     alt="{{ $category->name }}">
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                                <ul class="end">
                                    @foreach($categories->skip(5)->take(5) as $category)
                                        <li>
                                            <a href="{{ route('category.products', $category->slug) }}">
                                                <img src="{{ !empty($category->image)
                                                        ? asset(config('imagepath.category') . $category->image)
                                                        : asset('images/no-image.png') }}"
                                                     alt="{{ $category->name }}">
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>

                            </div>

                            @if($categories->count() > 10)
                                <div class="more_slide_open" style="display: none">
                                    <div class="d-flex categori-dropdown-inner">

                                        <ul>
                                            @foreach($categories->skip(10)->take(5) as $category)
                                                <li>
                                                    <a href="{{ route('category.products', $category->slug) }}">
                                                        <img src="{{ !empty($category->image)
                                                        ? asset(config('imagepath.category') . $category->image)
                                                        : asset('images/no-image.png') }}"
                                                             alt="{{ $category->name }}">
                                                        {{ $category->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>

                                        <ul class="end">
                                            @foreach($categories->skip(15) as $category)
                                                <li>
                                                    <a href="{{ route('category.products', $category->slug) }}">
                                                        <img src="{{ !empty($category->image)
                                                        ? asset(config('imagepath.category') . $category->image)
                                                        : asset('images/no-image.png') }}"
                                                             alt="{{ $category->name }}">
                                                        {{ $category->name }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>

                                    </div>
                                </div>
                            @endif

                            <div class="more_categories">
                                <span class="icon"></span>
                                <span class="heading-sm-1">Show more...</span>
                            </div>
                        </div>
                    </div>
                    <div class="main-menu main-menu-padding-1 main-menu-lh-2 d-none d-lg-block font-heading">
                        <nav>
                            <ul>
                                <li>
                                    <a class="active" href="{{Url('/')}}">Home </a>
                                </li>
                                <li>
                                    <a href="{{route('shop')}}">Shop</a>
                                </li>
                                <li>
                                    <a href="{{route('about')}}">About</a>
                                </li>

                                <li>
                                    <a href="{{route('contact')}}">Contact</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="hotline d-none d-lg-flex">
                    <img src="{{asset('web')}}/assets/imgs/theme/icons/icon-headphone.svg" alt="hotline" />
                    <p>{{$settings->contact_number_1}}<span>24/7 Support Center</span></p>
                </div>
                <div class="header-action-icon-2 d-block d-lg-none">
                    <div class="burger-icon burger-icon-white">
                        <span class="burger-icon-top"></span>
                        <span class="burger-icon-mid"></span>
                        <span class="burger-icon-bottom"></span>
                    </div>
                </div>
                <div class="header-action-right d-block d-lg-none">
                    <div class="header-action-2">
                        <div class="header-action-icon-2">
                            <a href="{{route('wishlist')}}">
                                <img alt="Nest" src="{{asset('web')}}/assets/imgs/theme/icons/icon-heart.svg" />
                                <span class="pro-count white wishlist-count">{{ auth()->check() ? \App\Models\Wishlist::where('user_id', auth()->id())->count() : 0 }}</span>
                            </a>
                        </div>
                        <div class="header-action-icon-2">
                            <a class="mini-cart-icon" href="javascript:void(0)" id="cart-drawer-trigger-mobile">
                                <img alt="Nest" src="{{asset('web')}}/assets/imgs/theme/icons/icon-cart.svg" />
                                <span class="pro-count white cart-count">{{\App\Models\Cart::where('session_id', session()->getId())->sum('quantity')}}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="mobile-header-active mobile-header-wrapper-style">
    <div class="mobile-header-wrapper-inner">
        <div class="mobile-header-top">
            <div class="mobile-header-logo">
                <a href="{{Url('/')}}"><img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="logo" /></a>
            </div>
            <div class="mobile-menu-close close-style-wrap close-style-position-inherit">
                <button class="close-style search-close">
                    <i class="icon-top"></i>
                    <i class="icon-bottom"></i>
                </button>
            </div>
        </div>
        <div class="mobile-header-content-area">
            <div class="mobile-search search-style-3 mobile-header-border" style="position: relative;">
                <form action="{{ route('shop') }}" method="GET">
                    <input type="text" name="q" id="search-input-mobile" placeholder="Search for items…" autocomplete="off" />
                    <button type="submit"><i class="fi-rs-search"></i></button>
                    <div class="search-results-dropdown" id="search-results-mobile" style="display:none; position:absolute; top:100%; left:0; right:0; background:#fff; z-index:9999; border:1px solid #ececec; border-top:none; border-radius:0 0 10px 10px; max-height:400px; overflow-y:auto; box-shadow:0 10px 15px rgba(0,0,0,0.05); padding: 10px 0; text-align: left;">
                        <!-- Results go here -->
                    </div>
                </form>
            </div>
            <div class="mobile-menu-wrap mobile-header-border">
                <!-- mobile menu start -->
                <nav>
                    <ul class="mobile-menu font-heading">
                        <li class="menu-item-has-children">
                            <a href="index.html">Home</a>
                            <ul class="dropdown">
                                <li><a href="index.html">Home 1</a></li>
                                <li><a href="index-2.html">Home 2</a></li>
                                <li><a href="index-3.html">Home 3</a></li>
                                <li><a href="index-4.html">Home 4</a></li>
                                <li><a href="index-5.html">Home 5</a></li>
                                <li><a href="index-6.html">Home 6</a></li>
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="shop-grid-right.html">shop</a>
                            <ul class="dropdown">
                                <li><a href="shop-grid-right.html">Shop Grid – Right Sidebar</a></li>
                                <li><a href="shop-grid-left.html">Shop Grid – Left Sidebar</a></li>
                                <li><a href="shop-list-right.html">Shop List – Right Sidebar</a></li>
                                <li><a href="shop-list-left.html">Shop List – Left Sidebar</a></li>
                                <li><a href="shop-fullwidth.html">Shop - Wide</a></li>
                                <li class="menu-item-has-children">
                                    <a href="#">Single Product</a>
                                    <ul class="dropdown">
                                        <li><a href="shop-product-right.html">Product – Right Sidebar</a></li>
                                        <li><a href="shop-product-left.html">Product – Left Sidebar</a></li>
                                        <li><a href="shop-product-full.html">Product – No sidebar</a></li>
                                        <li><a href="shop-product-vendor.html">Product – Vendor Infor</a></li>
                                    </ul>
                                </li>
                                <li><a href="shop-filter.html">Shop – Filter</a></li>
                                <li><a href="shop-wishlist.html">Shop – Wishlist</a></li>
                                <li><a href="shop-cart.html">Shop – Cart</a></li>
                                <li><a href="shop-checkout.html">Shop – Checkout</a></li>
                                <li><a href="shop-compare.html">Shop – Compare</a></li>
                                <li class="menu-item-has-children">
                                    <a href="#">Shop Invoice</a>
                                    <ul class="dropdown">
                                        <li><a href="shop-invoice-1.html">Shop Invoice 1</a></li>
                                        <li><a href="shop-invoice-2.html">Shop Invoice 2</a></li>
                                        <li><a href="shop-invoice-3.html">Shop Invoice 3</a></li>
                                        <li><a href="shop-invoice-4.html">Shop Invoice 4</a></li>
                                        <li><a href="shop-invoice-5.html">Shop Invoice 5</a></li>
                                        <li><a href="shop-invoice-6.html">Shop Invoice 6</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="#">Vendors</a>
                            <ul class="dropdown">
                                <li><a href="vendors-grid.html">Vendors Grid</a></li>
                                <li><a href="vendors-list.html">Vendors List</a></li>
                                <li><a href="vendor-details-1.html">Vendor Details 01</a></li>
                                <li><a href="vendor-details-2.html">Vendor Details 02</a></li>
                                <li><a href="vendor-dashboard.html">Vendor Dashboard</a></li>
                                <li><a href="vendor-guide.html">Vendor Guide</a></li>
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="#">Mega menu</a>
                            <ul class="dropdown">
                                <li class="menu-item-has-children">
                                    <a href="#">Women's Fashion</a>
                                    <ul class="dropdown">
                                        <li><a href="shop-product-right.html">Dresses</a></li>
                                        <li><a href="shop-product-right.html">Blouses & Shirts</a></li>
                                        <li><a href="shop-product-right.html">Hoodies & Sweatshirts</a></li>
                                        <li><a href="shop-product-right.html">Women's Sets</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">Men's Fashion</a>
                                    <ul class="dropdown">
                                        <li><a href="shop-product-right.html">Jackets</a></li>
                                        <li><a href="shop-product-right.html">Casual Faux Leather</a></li>
                                        <li><a href="shop-product-right.html">Genuine Leather</a></li>
                                    </ul>
                                </li>
                                <li class="menu-item-has-children">
                                    <a href="#">Technology</a>
                                    <ul class="dropdown">
                                        <li><a href="shop-product-right.html">Gaming Laptops</a></li>
                                        <li><a href="shop-product-right.html">Ultraslim Laptops</a></li>
                                        <li><a href="shop-product-right.html">Tablets</a></li>
                                        <li><a href="shop-product-right.html">Laptop Accessories</a></li>
                                        <li><a href="shop-product-right.html">Tablet Accessories</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="blog-category-fullwidth.html">Blog</a>
                            <ul class="dropdown">
                                <li><a href="blog-category-grid.html">Blog Category Grid</a></li>
                                <li><a href="blog-category-list.html">Blog Category List</a></li>
                                <li><a href="blog-category-big.html">Blog Category Big</a></li>
                                <li><a href="blog-category-fullwidth.html">Blog Category Wide</a></li>
                                <li class="menu-item-has-children">
                                    <a href="#">Single Product Layout</a>
                                    <ul class="dropdown">
                                        <li><a href="blog-post-left.html">Left Sidebar</a></li>
                                        <li><a href="blog-post-right.html">Right Sidebar</a></li>
                                        <li><a href="blog-post-fullwidth.html">No Sidebar</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="#">Pages</a>
                            <ul class="dropdown">
                                <li><a href="page-about.html">About Us</a></li>
                                <li><a href="page-contact.html">Contact</a></li>
                                <li><a href="page-account.html">My Account</a></li>
                                <li><a href="page-login.html">Login</a></li>
                                <li><a href="page-register.html">Register</a></li>
                                <li><a href="page-forgot-password.html">Forgot password</a></li>
                                <li><a href="page-reset-password.html">Reset password</a></li>
                                <li><a href="page-purchase-guide.html">Purchase Guide</a></li>
                                <li><a href="page-privacy-policy.html">Privacy Policy</a></li>
                                <li><a href="page-terms.html">Terms of Service</a></li>
                                <li><a href="page-404.html">404 Page</a></li>
                            </ul>
                        </li>
                        <li class="menu-item-has-children">
                            <a href="#">Language</a>
                            <ul class="dropdown">
                                <li><a href="#">English</a></li>
                                <li><a href="#">French</a></li>
                                <li><a href="#">German</a></li>
                                <li><a href="#">Spanish</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- mobile menu end -->
            </div>
            <div class="mobile-header-info-wrap">
                <div class="single-mobile-header-info">
                    <a href="page-contact.html"><i class="fi-rs-marker"></i> Our location </a>
                </div>
                <div class="single-mobile-header-info">
                    <a href="page-login.html"><i class="fi-rs-user"></i>Log In / Sign Up </a>
                </div>
                <div class="single-mobile-header-info">
                    <a href="#"><i class="fi-rs-headphones"></i>(+01) - 2345 - 6789 </a>
                </div>
            </div>
            <div class="mobile-social-icon mb-50">
                <h6 class="mb-15">Follow Us</h6>
                <a href="#"><img src="{{asset('web')}}/assets/imgs/theme/icons/icon-facebook-white.svg" alt="" /></a>
                <a href="#"><img src="{{asset('web')}}/assets/imgs/theme/icons/icon-twitter-white.svg" alt="" /></a>
                <a href="#"><img src="{{asset('web')}}/assets/imgs/theme/icons/icon-instagram-white.svg" alt="" /></a>
                <a href="#"><img src="{{asset('web')}}/assets/imgs/theme/icons/icon-pinterest-white.svg" alt="" /></a>
                <a href="#"><img src="{{asset('web')}}/assets/imgs/theme/icons/icon-youtube-white.svg" alt="" /></a>
            </div>
{{--            <div class="site-copyright">Copyright 2022 © Nest. All rights reserved. Powered by AliThemes.</div>--}}
        </div>
    </div>
</div>
<style>
    .cart-drawer {
        position: fixed;
        top: 0;
        right: -400px;
        width: 350px;
        height: 100vh;
        background-color: #fff;
        z-index: 9999;
        box-shadow: -5px 0 15px rgba(0,0,0,0.1);
        transition: right 0.3s ease-in-out;
        display: flex;
        flex-direction: column;
    }
    .cart-drawer.open {
        right: 0;
    }
    .cart-drawer-header {
        padding: 20px;
        border-bottom: 1px solid #ececec;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .cart-drawer-header h4 {
        margin: 0;
        font-size: 18px;
    }
    .cart-drawer-close {
        background: transparent;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: #333;
    }
    .cart-drawer-close:hover {
        color: #f53f3f;
    }
    .cart-drawer-content {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }
    .cart-drawer-content ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .cart-drawer-content li {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f1f1f1;
    }
    .cart-drawer-content .shopping-cart-img {
        width: 70px;
        margin-right: 15px;
    }
    .cart-drawer-content .shopping-cart-img img {
        width: 100%;
        border-radius: 5px;
    }
    .cart-drawer-content .shopping-cart-title {
        flex: 1;
    }
    .cart-drawer-content .shopping-cart-title h4 {
        font-size: 14px;
        margin-bottom: 5px;
    }
    .cart-drawer-content .shopping-cart-title h4 span {
        font-weight: 400;
        color: #777;
    }
    .cart-drawer-footer {
        padding: 20px;
        border-top: 1px solid #ececec;
        background: #f9f9f9;
    }
    .cart-drawer-footer .shopping-cart-total h4 {
        display: flex;
        justify-content: space-between;
        font-size: 18px;
        margin-bottom: 20px;
    }
    .cart-drawer-footer .shopping-cart-button {
        display: flex;
        gap: 10px;
    }
    .cart-drawer-footer .shopping-cart-button a {
        flex: 1;
        text-align: center;
        padding: 10px 0;
        border-radius: 5px;
        font-weight: 600;
    }
    .cart-drawer-footer .shopping-cart-button a.outline {
        border: 1px solid #3bb77e;
        color: #3bb77e;
        background: transparent;
    }
    .cart-drawer-footer .shopping-cart-button a.outline:hover {
        background: #3bb77e;
        color: #fff;
    }
    .cart-drawer-footer .shopping-cart-button a:not(.outline) {
        background: #3bb77e;
        color: #fff;
        border: 1px solid #3bb77e;
    }
    .cart-drawer-footer .shopping-cart-button a:not(.outline):hover {
        background: #2a9461;
    }

    @media (max-width: 576px) {
        .cart-drawer {
            width: 300px;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trigger1 = document.getElementById('cart-drawer-trigger');
        const trigger2 = document.getElementById('cart-drawer-trigger-text');
        const trigger3 = document.getElementById('cart-drawer-trigger-mobile');
        const drawer = document.getElementById('cart-drawer');
        const overlay = document.getElementById('cart-drawer-overlay');
        const closeBtn = document.getElementById('cart-drawer-close');

        function openDrawer(e) {
            e.preventDefault();
            drawer.classList.add('open');
            overlay.style.display = 'block';
        }

        function closeDrawer() {
            drawer.classList.remove('open');
            overlay.style.display = 'none';
        }

        if(trigger1) trigger1.addEventListener('click', openDrawer);
        if(trigger2) trigger2.addEventListener('click', openDrawer);
        if(trigger3) trigger3.addEventListener('click', openDrawer);
        if(closeBtn) closeBtn.addEventListener('click', closeDrawer);
        if(overlay) overlay.addEventListener('click', closeDrawer);

        // Optional: close when mouse leaves the drawer completely
        if(drawer) drawer.addEventListener('mouseleave', closeDrawer);
    });
</script>
