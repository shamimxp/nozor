<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row w-100 align-items-center">
            <li class="nav-item flex-grow-1"><a class="navbar-brand w-100" href="{{ route('dealer.dashboard') }}">
                    <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="Logo">
                </a>
            </li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pl-1 pr-0" data-toggle="collapse"><i class="d-block d-xl-none text-white toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-white" data-feather="disc" data-ticon="disc"></i></a></li>
        </ul>
    </div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="{{ Route::currentRouteName()=='dealer.dashboard'?'active':'' }} nav-item"><a class="d-flex align-items-center" href="{{route('dealer.dashboard')}}"><i data-feather="home"></i><span class="menu-title text-truncate" data-i18n="Dashboards">Dashboard</span></a>
            </li>
            <li class="{{ Route::currentRouteName()=='dealer.pos'?'active':'' }} nav-item"><a class="d-flex align-items-center" href="{{route('dealer.pos')}}"><i data-feather="shopping-cart"></i><span class="menu-title text-truncate">POS</span></a>
            </li>
            <li class="{{ Route::currentRouteName()=='dealer.order-requests.index' || Route::currentRouteName()=='dealer.order-requests.show' ? 'active':'' }} nav-item"><a class="d-flex align-items-center" href="{{route('dealer.order-requests.index')}}"><i data-feather="list"></i><span class="menu-title text-truncate">Order Requests</span></a>
            </li>
            <li class="{{ Route::currentRouteName()=='dealer.orders.index' || Route::currentRouteName()=='dealer.orders.show' ? 'active':'' }} nav-item"><a class="d-flex align-items-center" href="{{route('dealer.orders.index')}}"><i data-feather="box"></i><span class="menu-title text-truncate">Orders</span></a>
            </li>
            <li class="{{ Route::is('dealer.wallet*') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{ route('dealer.wallet.index') }}">
                    <i data-feather="credit-card"></i>
                    <span class="menu-title text-truncate">My Wallet</span>
                </a>
            </li>
            <li class="{{ Route::is('dealer.payments*') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{ route('dealer.payments.index') }}">
                    <i data-feather="dollar-sign"></i>
                    <span class="menu-title text-truncate">Payment History</span>
                </a>
            </li>
        </ul>
    </div>
</div>
