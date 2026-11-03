<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row w-100 align-items-center">
            <li class="nav-item flex-grow-1"><a class="navbar-brand w-100" href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="Logo">
                </a>
            </li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pl-1 pr-0" data-toggle="collapse"><i class="d-block d-xl-none text-white toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-white" data-feather="disc" data-ticon="disc"></i></a></li>
        </ul>
    </div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="{{ Route::currentRouteName()=='admin.dashboard'?'active':'' }} nav-item"><a class="d-flex align-items-center" href="{{route('admin.dashboard')}}"><i data-feather="home"></i><span class="menu-title text-truncate" data-i18n="Dashboards">Dashboard</span></a>
            </li>
            <li class=" navigation-header"><span data-i18n="Apps &amp; Pages">Order Management</span><i data-feather="more-horizontal"></i>
            </li>
            <li class="nav-item @if(Route::is('admin.web-order*') || Route::is('admin.wishlist*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="shopping-cart"></i><span class="menu-title text-truncate">Web Orders</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.web-order.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.web-order.index')}}"><i data-feather="circle"></i><span class="menu-item">Order List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.wishlist.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.wishlist.index')}}"><i data-feather="circle"></i><span class="menu-item">Wishlist</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item @if(Route::is('admin.pos-order*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="shopping-bag"></i><span class="menu-title text-truncate">POS Orders</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.pos') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.pos')}}" target="_blank"><i data-feather="circle"></i><span class="menu-item">Create Order</span></a>
                    </li>
                    <li class="{{ Route::is('admin.pos-order.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.pos-order.index')}}"><i data-feather="circle"></i><span class="menu-item">Order List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.pos-order.analysis') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.pos-order.analysis')}}"><i data-feather="circle"></i><span class="menu-item">Order Analysis</span></a>
                    </li>
                    <li class="{{ Route::is('admin.pos-order.due-list') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.pos-order.due-list')}}"><i data-feather="circle"></i><span class="menu-item">Due List</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item @if(Route::is('admin.dealer-order*') || Route::is('admin.order-requests*') || Route::is('admin.draft-orders*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="package"></i><span class="menu-title text-truncate">Dealer Orders</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.order-requests*') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.order-requests.index')}}"><i data-feather="circle"></i><span class="menu-item">Order Requests</span></a>
                    </li>
                    <li class="{{ Route::is('admin.draft-orders*') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.draft-orders.index')}}"><i data-feather="circle"></i><span class="menu-item">Draft Orders</span></a>
                    </li>
                    <li class="{{ Route::is('admin.dealer-order.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.dealer-order.index')}}"><i data-feather="circle"></i><span class="menu-item">Order List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.dealer-order.create') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.dealer-order.create')}}"><i data-feather="circle"></i><span class="menu-item">Create Order</span></a>
                    </li>
                    <li class="{{ Route::is('admin.dealer-order.due-list') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.dealer-order.due-list')}}"><i data-feather="circle"></i><span class="menu-item">Due List</span></a>
                    </li>
                </ul>
            </li>
            <li class="nav-item @if(Route::is('admin.returns*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="rotate-ccw"></i><span class="menu-title text-truncate">Return Orders</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.returns.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('admin.returns.index') }}"><i data-feather="circle"></i><span class="menu-item">Return List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.returns.create') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{ route('admin.returns.create') }}"><i data-feather="circle"></i><span class="menu-item">Create Return</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item @if(Route::is('admin.manufacture*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="package"></i><span class="menu-title text-truncate">Manufacture</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.manufacture.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.manufacture.index')}}"><i data-feather="circle"></i><span class="menu-item">Manufacture Order List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.manufacture.create') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.manufacture.create')}}"><i data-feather="circle"></i><span class="menu-item">Manufacture Create Order</span></a>
                    </li>
                    <li class="{{ Route::is('admin.manufacture.pending') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.manufacture.pending')}}"><i data-feather="circle"></i><span class="menu-item">Pending List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.manufacture.confirm') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.manufacture.confirm')}}"><i data-feather="circle"></i><span class="menu-item">Confirm List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.manufacture.complete') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.manufacture.complete')}}"><i data-feather="circle"></i><span class="menu-item">Complete List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.manufacture.body-part') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.manufacture.body-part')}}"><i data-feather="circle"></i><span class="menu-item">Body Part Orders</span></a>
                    </li>
                    <li class="{{ Route::is('admin.manufacture.finishing-part') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.manufacture.finishing-part')}}"><i data-feather="circle"></i><span class="menu-item">Finishing Part Orders</span></a>
                    </li>
                    <li class="{{ Route::is('admin.manufacture.receive') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.manufacture.receive')}}"><i data-feather="circle"></i><span class="menu-item">Receive Orders</span></a>
                    </li>
                </ul>
            </li>

            <li class=" nav-item @if(Route::is('admin.inventory-purchase*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="truck"></i><span class="menu-title text-truncate">Product Purchases</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.inventory-purchase.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.inventory-purchase.index')}}"><i data-feather="circle"></i><span class="menu-item">Purchase List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.inventory-purchase.create') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.inventory-purchase.create')}}"><i data-feather="circle"></i><span class="menu-item">Create Purchase</span></a>
                    </li>
                    <li class="{{ Route::is('admin.inventory-purchase.vendor-history') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.inventory-purchase.vendor-history')}}"><i data-feather="circle"></i><span class="menu-item">Vendor History</span></a>
                    </li>
                </ul>
            </li>

            <li class=" navigation-header"><span data-i18n="Apps &amp; Pages">Apps &amp; Pages</span><i data-feather="more-horizontal"></i>
            </li>
            @if(Gate::check('user.show') || Gate::check('role.show'))
                <li class=" nav-item"><a class="d-flex align-items-center" href="#"><i data-feather="user"></i>
                        <span class="menu-title text-truncate" data-i18n="User">Access Control</span></a>
                    <ul class="menu-content">
                                @can('user.show')
                                    <li class="{{ Route::currentRouteName()=='admin.user'?'active':'' }} nav-item"><a class=" d-flex align-items-center" href="{{route('admin.user')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="List">User List</span></a>
                                    </li>
                                 @endcan
                                @can('role.show')
                                    <li class="{{ Route::currentRouteName()=='roles.index'?'active':'' }} nav-item"><a class="d-flex align-items-center" href="{{route('roles.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="View">Role</span></a>
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
                        <a class="d-flex align-items-center" href="{{route('admin.category.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Categories">Categories</span></a>
                    </li>
                    <li class="{{ Route::is('admin.sub-category*')?'active':'' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.sub-category.index')}}"><i data-feather="circle"></i><span class="menu-item" data-i18n="Sub Categories">Sub Categories</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item @if(Route::is('admin.product-attribute*') || Route::is('admin.variations*') || Route::is('admin.product*') || Route::is('admin.unit*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="box"></i><span class="menu-title text-truncate">Product Setup</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.product.create') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.product.create')}}"><i data-feather="circle"></i><span class="menu-item">Create Product</span></a>
                    </li>
                    <li class="{{ Route::is('admin.product.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.product.index')}}"><i data-feather="circle"></i><span class="menu-item">Product List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.product-attribute*')?'active':'' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.product-attribute.index')}}"><i data-feather="circle"></i><span class="menu-item">Product Attribute</span></a>
                    </li>
                    <li class="{{ Route::is('admin.variations*')?'active':'' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.variations.index')}}"><i data-feather="circle"></i><span class="menu-item">Product Variation</span></a>
                    </li>
                    <li class="{{ Route::is('admin.product.out-of-stock') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.product.out-of-stock')}}"><i data-feather="circle"></i><span class="menu-item">Out of Stock</span></a>
                    </li>
                    <li class="{{ Route::is('admin.unit*') ? 'active':'' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.unit.index')}}"><i data-feather="circle"></i><span class="menu-item">Units</span></a>
                    </li>
                </ul>
            </li>

            <li class=" nav-item @if(Route::is('admin.raw-material-product*') || Route::is('admin.material-type*') || Route::is('admin.product-recipe*') || Route::is('admin.cost-head*') || Route::is('admin.cost-setting*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="box"></i><span class="menu-title text-truncate">Manufacture Setting</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.cost-head*') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.cost-head.index')}}"><i data-feather="circle"></i><span class="menu-item">Cost Head</span></a>
                    </li>
                    <li class="{{ Route::is('admin.cost-setting*') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.cost-setting.index')}}"><i data-feather="circle"></i><span class="menu-item">Cost Setting</span></a>
                    </li>
                    <li class="{{ Route::is('admin.material-type.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.material-type.index')}}"><i data-feather="circle"></i><span class="menu-item">Material Type</span></a>
                    </li>
                    <li class="{{ Route::is('admin.raw-material-product.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.raw-material-product.index')}}"><i data-feather="circle"></i><span class="menu-item">Raw Material</span></a>
                    </li>
                    <li class="{{ Route::is('admin.product-recipe.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.product-recipe.index')}}"><i data-feather="circle"></i><span class="menu-item">Product Recipe</span></a>
                    </li>
                </ul>
            </li>
            <li class=" nav-item @if(Route::is('admin.stock-adjustment*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="sliders"></i><span class="menu-title text-truncate">Stock Adjustment</span></a>
                <ul class="menu-content">
                    <li class="{{ Route::is('admin.stock-adjustment.index') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.stock-adjustment.index')}}"><i data-feather="circle"></i><span class="menu-item">Adjustment List</span></a>
                    </li>
                    <li class="{{ Route::is('admin.stock-adjustment.create') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.stock-adjustment.create')}}"><i data-feather="circle"></i><span class="menu-item">Create Adjustment</span></a>
                    </li>
                </ul>
            </li>
            <li class="{{ Route::is('admin.customer.*') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.customer.index')}}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate">Customer List</span>
                </a>
            </li>
            <li class="{{ Route::is('admin.worker.*') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.worker.index')}}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate">Worker List</span>
                </a>
            </li>
            <li class="{{ Route::is('admin.dealer.*') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.dealer.index')}}">
                    <i data-feather="briefcase"></i>
                    <span class="menu-title text-truncate">Dealer List</span>
                </a>
            </li>
            <li class="{{ Route::is('admin.vendor.*') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.vendor.index')}}">
                    <i data-feather="truck"></i>
                    <span class="menu-title text-truncate">Vendor List</span>
                </a>
            </li>
            <li class="{{ Route::is('admin.user-activities.*') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{ route('admin.user-activities.index') }}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate">User Activities</span>
                </a>
            </li>

{{--            <li class="{{ Route::is('admin.fabric.index') || Route::is('admin.fabric.create') || Route::is('admin.fabric.edit') ? 'active' : '' }} nav-item">--}}
{{--                <a class="d-flex align-items-center" href="{{route('admin.fabric.index')}}">--}}
{{--                    <i data-feather="scissors"></i>--}}
{{--                    <span class="menu-title text-truncate">Fabric List</span>--}}
{{--                </a>--}}
{{--            </li>--}}
{{--            <li class="{{ Route::is('admin.fabric-price*')?'active':'' }} nav-item">--}}
{{--                <a class="d-flex align-items-center" href="{{route('admin.fabric-price.index')}}">--}}
{{--                    <i data-feather="tag"></i>--}}
{{--                    <span class="menu-title text-truncate">Fabric Price Setup</span>--}}
{{--                </a>--}}
{{--            </li>--}}

            <li class="navigation-header"><span>Account & Finance</span><i data-feather="more-horizontal"></i></li>
            <li class="nav-item {{ (Route::is('admin.collections*') || Route::is('admin.due-collection*')) ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.collections.index') }}">
                    <i data-feather="dollar-sign"></i>
                    <span class="menu-title text-truncate">Collections</span>
                </a>
            </li>
            <li class="nav-item {{ Route::is('admin.dealer-wallets*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{ route('admin.dealer-wallets.index') }}">
                    <i data-feather="credit-card"></i>
                    <span class="menu-title text-truncate">Dealer Wallets</span>
                </a>
            </li>
            <li class="nav-item {{ Route::is('admin.worker-balance-sheet*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{route('admin.worker-balance-sheet.index')}}">
                    <i data-feather="users"></i>
                    <span class="menu-title text-truncate">Worker Balance Sheet</span>
                </a>
            </li>
            <li class="nav-item {{ Route::is('admin.worker-payouts*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{route('admin.worker-payouts.index')}}">
                    <i data-feather="credit-card"></i>
                    <span class="menu-title text-truncate">Worker Payment List</span>
                </a>
            </li>
            <li class="nav-item {{ (Route::is('admin.worker-invoices*') || Route::is('admin.worker-payment*')) ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{route('admin.worker-invoices.index')}}">
                    <i data-feather="file-text"></i>
                    <span class="menu-title text-truncate">Worker Invoice List</span>
                </a>
            </li>
            <li class="nav-item @if(Route::is('admin.report*')) open @endif">
                <a class="d-flex align-items-center" href="#"><i data-feather="file-text"></i><span class="menu-title text-truncate">Reports</span></a>
                <ul class="menu-content">
                    {{--
                    <li class="{{ Route::is('admin.report.custom-order-report') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.report.custom-order-report')}}"><i data-feather="circle"></i><span class="menu-item">Custom Order Sales</span></a>
                    </li>
                    --}}
                    <li class="{{ Route::is('admin.report.pos-order-report') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.report.pos-order-report')}}"><i data-feather="circle"></i><span class="menu-item">POS Order Sales</span></a>
                    </li>
                    {{--
                    <li class="{{ Route::is('admin.report.custom-profit-loss') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.report.custom-profit-loss')}}"><i data-feather="circle"></i><span class="menu-item">Custom Profit/Loss</span></a>
                    </li>
                    --}}
                    <li class="{{ Route::is('admin.report.pos-profit-loss') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.report.pos-profit-loss')}}"><i data-feather="circle"></i><span class="menu-item">POS Profit/Loss</span></a>
                    </li>
                    <li class="{{ Route::is('admin.report.product-stock') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.report.product-stock')}}"><i data-feather="circle"></i><span class="menu-item">Product Stock Report</span></a>
                    </li>
                    <li class="{{ Route::is('admin.report.manufacture-report') ? 'active' : '' }}">
                        <a class="d-flex align-items-center" href="{{route('admin.report.manufacture-report')}}"><i data-feather="circle"></i><span class="menu-item">Manufacture Report</span></a>
                    </li>
                </ul>
            </li>
            <li class="navigation-header"><span>Web Setting</span><i data-feather="more-horizontal"></i></li>
            <li class="{{ Route::is('admin.web-setting.index') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.web-setting.index')}}">
                    <i data-feather="settings"></i>
                    <span class="menu-title text-truncate">Web Setting</span>
                </a>
            </li>
            <li class="{{ Route::is('admin.banner.index') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.banner.index')}}">
                    <i data-feather="image"></i>
                    <span class="menu-title text-truncate">Banner</span>
                </a>
            </li>
            <li class="{{ Route::is('admin.coupon*') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.coupon.index')}}">
                    <i data-feather="tag"></i>
                    <span class="menu-title text-truncate">Coupon</span>
                </a>
            </li>
            <li class="{{ Route::is('admin.product_reviews*') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.product_reviews.index')}}">
                    <i data-feather="star"></i>
                    <span class="menu-title text-truncate">Product Reviews</span>
                </a>
            </li>
            <li class="{{ Route::is('admin.contact-messages*') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.contact-messages.index')}}">
                    <i data-feather="mail"></i>
                    <span class="menu-title text-truncate">Contact Messages</span>
                </a>
            </li>
            <li class="{{ Route::is('admin.subscribers*') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{route('admin.subscribers.index')}}">
                    <i data-feather="user-plus"></i>
                    <span class="menu-title text-truncate">Subscribers</span>
                </a>
            </li>
        </ul>
    </div>
</div>
