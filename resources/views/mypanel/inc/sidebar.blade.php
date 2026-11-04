<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row w-100 align-items-center">
            <li class="nav-item flex-grow-1"><a class="navbar-brand w-100" href="{{ route('mypanel.dashboard') }}">
                    <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="Logo">
                </a>
            </li>
            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pl-1 pr-0" data-toggle="collapse"><i class="d-block d-xl-none text-white toggle-icon font-medium-4" data-feather="x"></i><i class="d-none d-xl-block collapse-toggle-icon font-medium-4 text-white" data-feather="disc" data-ticon="disc"></i></a></li>
        </ul>
    </div>
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            <li class="{{ Route::is('mypanel.dashboard') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{ route('mypanel.dashboard') }}">
                    <i data-feather="home"></i><span class="menu-title text-truncate">Dashboard</span>
                </a>
            </li>
            <li class="{{ Route::is('mypanel.manufactures*') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{ route('mypanel.manufactures.index') }}">
                    <i data-feather="tool"></i><span class="menu-title text-truncate">Manufacture Orders</span>
                </a>
            </li>
            <li class="{{ Route::is('mypanel.invoices.index') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{ route('mypanel.invoices.index') }}">
                    <i data-feather="file-text"></i><span class="menu-title text-truncate">Invoices</span>
                </a>
            </li>
            <li class="{{ Route::is('mypanel.payments.index') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{ route('mypanel.payments.index') }}">
                    <i data-feather="dollar-sign"></i><span class="menu-title text-truncate">Payment History</span>
                </a>
            </li>
            <li class="{{ Route::is('mypanel.profile') ? 'active' : '' }} nav-item">
                <a class="d-flex align-items-center" href="{{ route('mypanel.profile') }}">
                    <i data-feather="user"></i><span class="menu-title text-truncate">My Profile</span>
                </a>
            </li>
        </ul>
    </div>
</div>
