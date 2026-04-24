<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href=""><span class="brand-logo">
                    <h2 class="brand-text">NOZOR Fashion</h2>
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
            <li class="nav-item @if(Route::is('admin.pos-order*')) open @endif">
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
            <li class="navigation-header"><span>Account & Finance</span><i data-feather="more-horizontal"></i></li>
            <li class="nav-item {{ Route::is('admin.due-collection*') ? 'active' : '' }}">
                <a class="d-flex align-items-center" href="{{route('admin.due-collection.index')}}">
                    <i data-feather="dollar-sign"></i>
                    <span class="menu-title text-truncate">Collection</span>
                </a>
            </li>
        </ul>
    </div>
</div>
