<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href=""><span class="brand-logo">
                            <svg viewbox="0 0 139 95" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="24">
                                <defs>
                                    <lineargradient id="linearGradient-1" x1="100%" y1="10.5120544%" x2="50%" y2="89.4879456%">
                                        <stop stop-color="#000000" offset="0%"></stop>
                                        <stop stop-color="#FFFFFF" offset="100%"></stop>
                                    </lineargradient>
                                    <lineargradient id="linearGradient-2" x1="64.0437835%" y1="46.3276743%" x2="37.373316%" y2="100%">
                                        <stop stop-color="#EEEEEE" stop-opacity="0" offset="0%"></stop>
                                        <stop stop-color="#FFFFFF" offset="100%"></stop>
                                    </lineargradient>
                                </defs>
                                <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g id="Artboard" transform="translate(-400.000000, -178.000000)">
                                        <g id="Group" transform="translate(400.000000, 178.000000)">
                                            <path class="text-primary" id="Path" d="M-5.68434189e-14,2.84217094e-14 L39.1816085,2.84217094e-14 L69.3453773,32.2519224 L101.428699,2.84217094e-14 L138.784583,2.84217094e-14 L138.784199,29.8015838 C137.958931,37.3510206 135.784352,42.5567762 132.260463,45.4188507 C128.736573,48.2809251 112.33867,64.5239941 83.0667527,94.1480575 L56.2750821,94.1480575 L6.71554594,44.4188507 C2.46876683,39.9813776 0.345377275,35.1089553 0.345377275,29.8015838 C0.345377275,24.4942122 0.230251516,14.560351 -5.68434189e-14,2.84217094e-14 Z" style="fill:currentColor"></path>
                                            <path id="Path1" d="M69.3453773,32.2519224 L101.428699,1.42108547e-14 L138.784583,1.42108547e-14 L138.784199,29.8015838 C137.958931,37.3510206 135.784352,42.5567762 132.260463,45.4188507 C128.736573,48.2809251 112.33867,64.5239941 83.0667527,94.1480575 L56.2750821,94.1480575 L32.8435758,70.5039241 L69.3453773,32.2519224 Z" fill="url(#linearGradient-1)" opacity="0.2"></path>
                                            <polygon id="Path-2" fill="#000000" opacity="0.049999997" points="69.3922914 32.4202615 32.8435758 70.5039241 54.0490008 16.1851325"></polygon>
                                            <polygon id="Path-21" fill="#000000" opacity="0.099999994" points="69.3922914 32.4202615 32.8435758 70.5039241 58.3683556 20.7402338"></polygon>
                                            <polygon id="Path-3" fill="url(#linearGradient-2)" opacity="0.099999994" points="101.428699 0 83.0667527 94.1480575 130.378721 47.0740288"></polygon>
                                        </g>
                                    </g>
                                </g>
                            </svg></span>
                    <h2 class="brand-text">Amar Code</h2>
                </a></li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="d-block d-xl-none text-primary toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4  text-primary" data-feather="disc" data-ticon="disc"></i></a></li>
        </ul>
    </div>
    <div class="shadow-bottom"></div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="{{ Route::currentRouteName()=='admin.dashboard'?'active':'' }} nav-item"><a class="d-flex align-items-center" href="{{route('admin.dashboard')}}"><i data-feather="home"></i><span class="menu-title text-truncate" data-i18n="Dashboards">Dashboard</span></a>
            </li>
            <li class=" navigation-header"><span data-i18n="Apps &amp; Pages">Apps &amp; Pages</span><i data-feather="more-horizontal"></i>
            </li>
            @if(Gate::check('user.show') || Gate::check('role.show'))
                <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="user"></i>
                        <span class="menu-title text-truncate" data-i18n="User">Access Control</span></a>
                    <ul class="menu-content">
                                @can('user.show')
                                    <li class="{{ Route::currentRouteName()=='admin.user'?'active':'' }} nav-item"><a class=" d-flex align-items-center" href="{{route('admin.user')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="List">User List</span></a>
                                    </li>
                                 @endcan
                                @can('role.show')
                                    <li class="{{ Route::currentRouteName()=='roles.index'?'active':'' }} nav-item"><a class="d-flex align-items-center" href="{{route('roles.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="View">Role</span></a>
                                    </li>
                                @endcan
                    </ul>
                </li>
            @endif
{{--            <li class=" nav-item"><a class="d-flex align-items-center" href="{{route('admin.pos')}}" target="_blank"><i data-feather="mail"></i><span class="menu-title text-truncate" data-i18n="Sale">POS</span></a>--}}
{{--            </li>--}}
            <li class=" nav-item @if(Route::is('admin.category*') || Route::is('admin.sub_category*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="grid"></i><span class="menu-title text-truncate" data-i18n="Category Setup">Category Setup</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.category*')?'active':'' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.category.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Categories">Categories</span></a>
                    </li>
                    <li class="{{ Route::is('admin.sub-category*')?'active':'' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.sub-category.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate" data-i18n="Sub Categories">Sub Categories</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item @if(Route::is('admin.product-attribute*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="box"></i><span class="menu-title text-truncate">Product Setup</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.product-attribute*')?'active':'' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.product-attribute.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Product Attribute</span></a>
                    </li>
                    <li class="{{ Route::is('admin.product.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.product.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Product List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.product.out-of-stock') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.product.out-of-stock')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Out of Stock</span></a>
                    </li>
                    <li class="{{ Route::is('admin.unit*') ? 'active':'' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.unit.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Units</span></a>
                    </li>
                </ul>
            </li>
            <li class="{{ Route::is('admin.customer*')?'active':'' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.customer.index')}}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate">Customer List</span>
                </a>
            </li>
            <li class="{{ Route::is('admin.vendor*')?'active':'' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.vendor.index')}}">
                    <i data-feather="truck"></i>
                    <span class="menu-title text-truncate">Vendor List</span>
                </a>
            </li>
            <li class="{{ Route::is('admin.fabric.index') || Route::is('admin.fabric.create') || Route::is('admin.fabric.edit') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.fabric.index')}}">
                    <i data-feather="scissors"></i>
                    <span class="menu-title text-truncate">Fabric List</span>
                </a>
            </li>
            <li class="{{ Route::is('admin.fabric-price*')?'active':'' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.fabric-price.index')}}">
                    <i data-feather="tag"></i>
                    <span class="menu-title text-truncate">Fabric Price Setup</span>
                </a>
            </li>
            <li class=" nav-item @if(Route::is('admin.custom-order*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="package"></i><span class="menu-title text-truncate">Custom Orders</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.custom-order.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.custom-order.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Order List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.custom-order.create') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.custom-order.create')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Create Order</span></a>
                    </li>
                    <li class="{{ Route::is('admin.custom-order.due-list') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.custom-order.due-list')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Due List</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item @if(Route::is('admin.purchase*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="shopping-cart"></i><span class="menu-title text-truncate">Purchases</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.purchase.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.purchase.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Purchase List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.purchase.create') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.purchase.create')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Create Purchase</span></a>
                    </li>
                    <li class="{{ Route::is('admin.purchase.vendor-history') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.purchase.vendor-history')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Vendor History</span></a>
                    </li>
                    <li class="{{ Route::is('admin.purchase.due-list') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.purchase.due-list')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Due List</span></a>
                    </li>
                </ul>
            </li>
            <li class="mb-3 nav-item @if(Route::is('admin.pos-order*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="shopping-bag"></i><span class="menu-title text-truncate">POS Orders</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.pos') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.pos')}}" target="_blank"><i data-feather="circle"></i><span class="menu-item text-truncate">Create Order</span></a>
                    </li>
                    <li class="{{ Route::is('admin.pos-order.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.pos-order.index')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Order List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.pos-order.analysis') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.pos-order.analysis')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Order Analysis</span></a>
                    </li>
                    <li class="{{ Route::is('admin.pos-order.due-list') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.pos-order.due-list')}}"><i data-feather="circle"></i><span class="menu-item text-truncate">Due List</span></a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
