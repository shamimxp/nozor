@extends('layouts.admin')
@section('content')
    <section id="dashboard-ecommerce">
        <!-- FieldOra Warm Header Banner Card -->
        <div class="fieldora-banner-card">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center">
                    <div class="fieldora-banner-icon mr-1">
                        <i data-feather="grid"></i>
                    </div>
                    <div>
                        <h3 class="fieldora-banner-title">Dashboard Overview</h3>
                        <p class="fieldora-banner-subtitle">Real-time business performance, sales, inventory, and pending dues</p>
                    </div>
                </div>
                <div class="fieldora-banner-actions mt-1 mt-md-0 d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <a href="{{ route('admin.pos') }}" class="btn btn-outline-secondary" target="_blank">
                        <i data-feather="shopping-cart" class="mr-50"></i> POS Terminal
                    </a>
                    <a href="{{ route('admin.collections.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="dollar-sign" class="mr-50"></i> Collections
                    </a>
                    <a href="{{ route('admin.dealer-order.create') }}" class="btn btn-primary">
                        <i data-feather="plus" class="mr-50"></i> Create Order
                    </a>
                </div>
            </div>
        </div>

        <!-- 4 Key Performance Metric Cards -->
        <div class="row match-height">
            <!-- Today's Sales Card -->
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="card fieldora-stat-card mb-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fieldora-stat-label">Today's Revenue</div>
                            <div class="fieldora-stat-value">৳ {{ number_format($todayTotalRevenue, 2) }}</div>
                            <small class="text-muted font-small-2 mt-50 d-block">
                                <span class="badge badge-light-success py-25 px-50 font-small-1">Today</span> Active sales
                            </small>
                        </div>
                        <div class="fieldora-stat-icon-wrapper" style="background: rgba(17, 28, 56, 0.08); color: #111c38;">
                            <i data-feather="sun" style="width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Orders Card -->
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="card fieldora-stat-card mb-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fieldora-stat-label">Total Orders</div>
                            <div class="fieldora-stat-value">{{ number_format($salesCount) }}</div>
                            <small class="text-muted font-small-2 mt-50 d-block">
                                <span class="badge badge-light-primary py-25 px-50 font-small-1">Orders</span> POS, Web & Dealer
                            </small>
                        </div>
                        <div class="fieldora-stat-icon-wrapper" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                            <i data-feather="shopping-bag" style="width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customers Card -->
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="card fieldora-stat-card mb-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fieldora-stat-label">Total Customers</div>
                            <div class="fieldora-stat-value">{{ number_format($customerCount) }}</div>
                            <small class="text-muted font-small-2 mt-50 d-block">
                                <span class="badge badge-light-info py-25 px-50 font-small-1">Parties</span> Registered buyers
                            </small>
                        </div>
                        <div class="fieldora-stat-icon-wrapper" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                            <i data-feather="users" style="width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Lifetime Revenue Card -->
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="card fieldora-stat-card mb-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fieldora-stat-label">Lifetime Revenue</div>
                            <div class="fieldora-stat-value">৳ {{ number_format($totalRevenue, 2) }}</div>
                            <small class="text-muted font-small-2 mt-50 d-block">
                                <span class="badge badge-light-success py-25 px-50 font-small-1">Total</span> {{ number_format($productCount) }} Products
                            </small>
                        </div>
                        <div class="fieldora-stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i data-feather="dollar-sign" style="width: 20px; height: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row match-height">
            <div class="col-lg-4 col-12">
                <div class="row match-height">
                    <!-- Bar Chart - Orders -->
                    <div class="col-lg-6 col-md-3 col-6">
                        <div class="card mb-2">
                            <div class="card-body pb-50">
                                <div class="fieldora-stat-label">Orders</div>
                                <h3 class="font-weight-bolder mb-1" style="color: #111c38;">{{ number_format($salesCount) }}</h3>
                                <div id="statistics-order-chart"></div>
                            </div>
                        </div>
                    </div>
                    <!--/ Bar Chart - Orders -->

                    <!-- Line Chart - Profit -->
                    <div class="col-lg-6 col-md-3 col-6">
                        <div class="card card-tiny-line-stats mb-2">
                            <div class="card-body pb-50">
                                <div class="fieldora-stat-label">Revenue</div>
                                <h3 class="font-weight-bolder mb-1" style="color: #111c38;">৳ {{ number_format($totalRevenue / 1000, 1) }}k</h3>
                                <div id="statistics-profit-chart"></div>
                            </div>
                        </div>
                    </div>
                    <!--/ Line Chart - Profit -->

                    <!-- Earnings Card -->
                    <div class="col-lg-12 col-md-6 col-12">
                        <div class="card earnings-card mb-2">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <h4 class="card-title mb-50">Earnings by Source</h4>
                                        <div class="font-small-2 text-muted mb-50">All Time</div>
                                        <h5 class="mb-1" style="color: #111c38; font-weight: 700;">৳ {{ number_format($totalRevenue, 2) }}</h5>
                                        <p class="card-text text-muted font-small-2">
                                            POS, Web, and Custom Orders
                                        </p>
                                    </div>
                                    <div class="col-6">
                                        <div id="earnings-chart"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--/ Earnings Card -->
                </div>
            </div>

            <!-- Revenue Report Card -->
            <div class="col-lg-8 col-12">
                <div class="card card-revenue-budget mb-2">
                    <div class="row mx-0">
                        <div class="col-md-12 col-12 revenue-report-wrapper">
                            <div class="d-sm-flex justify-content-between align-items-center mb-2 pt-1 px-1">
                                <div class="d-flex align-items-center">
                                    <div class="fieldora-banner-icon mr-1" style="width: 34px; height: 34px; border-radius: 8px;">
                                        <i data-feather="bar-chart-2" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <h4 class="card-title mb-0">Revenue Report ({{ date('Y') }})</h4>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="badge badge-light-primary">Monthly Revenue Trend</span>
                                </div>
                            </div>
                            <div id="revenue-report-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Revenue Report Card -->
        </div>

        <div class="row match-height">
            <!-- Dues Section -->
            <div class="col-xl-12 col-md-12 col-12">
                <div class="card mb-2">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="fieldora-banner-icon mr-1" style="width: 34px; height: 34px; border-radius: 8px;">
                                <i data-feather="clock" style="width: 16px; height: 16px;"></i>
                            </div>
                            <h4 class="card-title mb-0">Pending Dues Overview</h4>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-light-danger py-50 px-75 font-small-2">
                                Total Outstanding: <strong>৳ {{ number_format($totalDue, 2) }}</strong>
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-4 col-sm-6 col-12 mb-2 mb-xl-0">
                                <div class="media align-items-center p-1 rounded" style="background: #ffffff; border: 1px solid #efeae2;">
                                    <div class="avatar mr-1" style="background: rgba(17, 28, 56, 0.1); color: #111c38; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                        <i data-feather="shopping-bag" style="width: 18px; height: 18px;"></i>
                                    </div>
                                    <div class="media-body my-auto">
                                        <div class="fieldora-stat-label mb-25">POS Order Dues</div>
                                        <h4 class="font-weight-bolder mb-0 text-danger">৳ {{ number_format($posOrderDue, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 col-12 mb-2 mb-xl-0">
                                <div class="media align-items-center p-1 rounded" style="background: #ffffff; border: 1px solid #efeae2;">
                                    <div class="avatar mr-1" style="background: rgba(37, 99, 235, 0.1); color: #2563eb; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                        <i data-feather="globe" style="width: 18px; height: 18px;"></i>
                                    </div>
                                    <div class="media-body my-auto">
                                        <div class="fieldora-stat-label mb-25">Web Order Dues</div>
                                        <h4 class="font-weight-bolder mb-0 text-warning">৳ {{ number_format($webOrderDue, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-sm-6 col-12 mb-2 mb-sm-0">
                                <div class="media align-items-center p-1 rounded" style="background: #ffffff; border: 1px solid #efeae2;">
                                    <div class="avatar mr-1" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                        <i data-feather="settings" style="width: 18px; height: 18px;"></i>
                                    </div>
                                    <div class="media-body my-auto">
                                        <div class="fieldora-stat-label mb-25">Custom Order Dues</div>
                                        <h4 class="font-weight-bolder mb-0 text-danger">৳ {{ number_format($customOrderDue, 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Dues Section -->
        </div>

        <div class="row match-height">
            <!-- Recent Web Orders Card -->
            <div class="col-lg-12 col-12">
                <div class="card card-company-table mb-2">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="fieldora-banner-icon mr-1" style="width: 34px; height: 34px; border-radius: 8px;">
                                <i data-feather="shopping-cart" style="width: 16px; height: 16px;"></i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0">Recent Web Orders</h4>
                                <small class="text-muted">Latest customer purchases through website</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.web-order.index') }}" class="btn btn-outline-secondary btn-sm">
                            View All Orders <i data-feather="chevron-right" class="ml-25"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                <tr>
                                    <th style="padding-left: 20px;">Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Amount</th>
                                    <th class="text-right" style="padding-right: 20px;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($recentWebOrders as $order)
                                <tr>
                                    <td style="padding-left: 20px;">
                                        <span class="font-weight-bolder" style="color: #111c38;">#{{ $order->invoice_no }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar mr-1" style="width: 32px; height: 32px; border-radius: 6px; background: rgba(17, 28, 56, 0.08); color: #111c38; display: flex; align-items: center; justify-content: center;">
                                                <i data-feather="user" style="width: 14px; height: 14px;"></i>
                                            </div>
                                            <span class="font-weight-bold">{{ $order->address->name ?? 'Unknown Customer' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="text-muted">{{ $order->created_at->format('d M, Y') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'completed' ? 'success' : 'primary') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bolder text-success">৳ {{ number_format($order->total, 2) }}</span>
                                    </td>
                                    <td class="text-right" style="padding-right: 20px;">
                                        <a href="{{ route('admin.web-order.show', $order->id) }}" class="btn btn-outline-secondary btn-sm py-25 px-50">
                                            <i data-feather="eye" style="width: 12px; height: 12px;" class="mr-25"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No recent web orders found.</td>
                                </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Recent Web Orders Card -->
        </div>

            <!-- Developer Meetup Card -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-developer-meetup">
                    <div class="meetup-img-wrapper rounded-top text-center">
                        <img src="{{asset('admin')}}/app-assets/images/illustration/email.svg" alt="Meeting Pic" height="170" />
                    </div>
                    <div class="card-body">
                        <div class="meetup-header d-flex align-items-center">
                            <div class="meetup-day">
                                <h6 class="mb-0">THU</h6>
                                <h3 class="mb-0">24</h3>
                            </div>
                            <div class="my-auto">
                                <h4 class="card-title mb-25">Developer Meetup</h4>
                                <p class="card-text mb-0">Meet world popular developers</p>
                            </div>
                        </div>
                        <div class="media">
                            <div class="avatar bg-light-primary rounded mr-1">
                                <div class="avatar-content">
                                    <i data-feather="calendar" class="avatar-icon font-medium-3"></i>
                                </div>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0">Sat, May 25, 2020</h6>
                                <small>10:AM to 6:PM</small>
                            </div>
                        </div>
                        <div class="media mt-2">
                            <div class="avatar bg-light-primary rounded mr-1">
                                <div class="avatar-content">
                                    <i data-feather="map-pin" class="avatar-icon font-medium-3"></i>
                                </div>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0">Central Park</h6>
                                <small>Manhattan, New york City</small>
                            </div>
                        </div>
                        <div class="avatar-group">
                            <div data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Billy Hopkins" class="avatar pull-up">
                                <img src="{{asset('admin')}}/app-assets/images/portrait/small/avatar-s-9.jpg" alt="Avatar" width="33" height="33" />
                            </div>
                            <div data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Amy Carson" class="avatar pull-up">
                                <img src="{{asset('admin')}}/app-assets/images/portrait/small/avatar-s-6.jpg" alt="Avatar" width="33" height="33" />
                            </div>
                            <div data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Brandon Miles" class="avatar pull-up">
                                <img src="{{asset('admin')}}/app-assets/images/portrait/small/avatar-s-8.jpg" alt="Avatar" width="33" height="33" />
                            </div>
                            <div data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Daisy Weber" class="avatar pull-up">
                                <img src="{{asset('admin')}}/app-assets/images/portrait/small/avatar-s-20.jpg" alt="Avatar" width="33" height="33" />
                            </div>
                            <div data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom" data-original-title="Jenny Looper" class="avatar pull-up">
                                <img src="{{asset('admin')}}/app-assets/images/portrait/small/avatar-s-20.jpg" alt="Avatar" width="33" height="33" />
                            </div>
                            <h6 class="align-self-center cursor-pointer ml-50 mb-0">+42</h6>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Developer Meetup Card -->

            <!-- Browser States Card -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-browser-states">
                    <div class="card-header">
                        <div>
                            <h4 class="card-title">Browser States</h4>
                            <p class="card-text font-small-2">Counter August 2020</p>
                        </div>
                        <div class="dropdown chart-dropdown">
                            <i data-feather="more-vertical" class="font-medium-3 cursor-pointer" data-toggle="dropdown"></i>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="browser-states">
                            <div class="media">
                                <img src="{{asset('admin')}}/app-assets/images/icons/google-chrome.png" class="rounded mr-1" height="30" alt="Google Chrome" />
                                <h6 class="align-self-center mb-0">Google Chrome</h6>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="font-weight-bold text-body-heading mr-1">54.4%</div>
                                <div id="browser-state-chart-primary"></div>
                            </div>
                        </div>
                        <div class="browser-states">
                            <div class="media">
                                <img src="{{asset('admin')}}/app-assets/images/icons/mozila-firefox.png" class="rounded mr-1" height="30" alt="Mozila Firefox" />
                                <h6 class="align-self-center mb-0">Mozila Firefox</h6>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="font-weight-bold text-body-heading mr-1">6.1%</div>
                                <div id="browser-state-chart-warning"></div>
                            </div>
                        </div>
                        <div class="browser-states">
                            <div class="media">
                                <img src="{{asset('admin')}}/app-assets/images/icons/apple-safari.png" class="rounded mr-1" height="30" alt="Apple Safari" />
                                <h6 class="align-self-center mb-0">Apple Safari</h6>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="font-weight-bold text-body-heading mr-1">14.6%</div>
                                <div id="browser-state-chart-secondary"></div>
                            </div>
                        </div>
                        <div class="browser-states">
                            <div class="media">
                                <img src="{{asset('admin/app-assets/images/icons/internet-explorer.png')}}" class="rounded mr-1" height="30" alt="Internet Explorer" />
                                <h6 class="align-self-center mb-0">Internet Explorer</h6>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="font-weight-bold text-body-heading mr-1">4.2%</div>
                                <div id="browser-state-chart-info"></div>
                            </div>
                        </div>
                        <div class="browser-states">
                            <div class="media">
                                <img src="{{asset('admin/app-assets/images/icons/opera.png')}}" class="rounded mr-1" height="30" alt="Opera Mini" />
                                <h6 class="align-self-center mb-0">Opera Mini</h6>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="font-weight-bold text-body-heading mr-1">8.4%</div>
                                <div id="browser-state-chart-danger"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Browser States Card -->

            <!-- Goal Overview Card -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Goal Overview</h4>
                        <i data-feather="help-circle" class="font-medium-3 text-muted cursor-pointer"></i>
                    </div>
                    <div class="card-body p-0">
                        <div id="goal-overview-radial-bar-chart" class="my-2"></div>
                        <div class="row border-top text-center mx-0">
                            <div class="col-6 border-right py-1">
                                <p class="card-text text-muted mb-0">Completed</p>
                                <h3 class="font-weight-bolder mb-0">786,617</h3>
                            </div>
                            <div class="col-6 py-1">
                                <p class="card-text text-muted mb-0">In Progress</p>
                                <h3 class="font-weight-bolder mb-0">13,561</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Goal Overview Card -->

            <!-- Transaction Card -->
            <div class="col-lg-4 col-md-6 col-12">
                <div class="card card-transaction">
                    <div class="card-header">
                        <h4 class="card-title">Transactions</h4>
                        <div class="dropdown chart-dropdown">
                            <i data-feather="more-vertical" class="font-medium-3 cursor-pointer" data-toggle="dropdown"></i>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="transaction-item">
                            <div class="media">
                                <div class="avatar bg-light-primary rounded">
                                    <div class="avatar-content">
                                        <i data-feather="pocket" class="avatar-icon font-medium-3"></i>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h6 class="transaction-title">Wallet</h6>
                                    <small>Starbucks</small>
                                </div>
                            </div>
                            <div class="font-weight-bolder text-danger">- $74</div>
                        </div>
                        <div class="transaction-item">
                            <div class="media">
                                <div class="avatar bg-light-success rounded">
                                    <div class="avatar-content">
                                        <i data-feather="check" class="avatar-icon font-medium-3"></i>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h6 class="transaction-title">Bank Transfer</h6>
                                    <small>Add Money</small>
                                </div>
                            </div>
                            <div class="font-weight-bolder text-success">+ $480</div>
                        </div>
                        <div class="transaction-item">
                            <div class="media">
                                <div class="avatar bg-light-danger rounded">
                                    <div class="avatar-content">
                                        <i data-feather="dollar-sign" class="avatar-icon font-medium-3"></i>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h6 class="transaction-title">Paypal</h6>
                                    <small>Add Money</small>
                                </div>
                            </div>
                            <div class="font-weight-bolder text-success">+ $590</div>
                        </div>
                        <div class="transaction-item">
                            <div class="media">
                                <div class="avatar bg-light-warning rounded">
                                    <div class="avatar-content">
                                        <i data-feather="credit-card" class="avatar-icon font-medium-3"></i>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h6 class="transaction-title">Mastercard</h6>
                                    <small>Ordered Food</small>
                                </div>
                            </div>
                            <div class="font-weight-bolder text-danger">- $23</div>
                        </div>
                        <div class="transaction-item">
                            <div class="media">
                                <div class="avatar bg-light-info rounded">
                                    <div class="avatar-content">
                                        <i data-feather="trending-up" class="avatar-icon font-medium-3"></i>
                                    </div>
                                </div>
                                <div class="media-body">
                                    <h6 class="transaction-title">Transfer</h6>
                                    <small>Refund</small>
                                </div>
                            </div>
                            <div class="font-weight-bolder text-success">+ $98</div>
                        </div>
                    </div>
                </div>
            </div>
            <!--/ Transaction Card -->
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    $(window).on("load", function () {
        var primaryColor = '#111c38';
        var infoColor = '#0ea5e9';
        var successColor = '#10b981';
        var warningColor = '#f59e0b';
        var gridBorderColor = '#e2e8f0';
        
        // Orders Chart
        var orderChartOptions = {
            chart: {
                height: 70,
                type: 'bar',
                stacked: true,
                toolbar: { show: false }
            },
            grid: {
                show: false,
                padding: { left: 0, right: 0, top: -15, bottom: -15 }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '20%',
                    borderRadius: 5,
                    colors: {
                        backgroundBarColors: ['#f3f3f3', '#f3f3f3', '#f3f3f3', '#f3f3f3', '#f3f3f3'],
                        backgroundBarRadius: 5
                    }
                }
            },
            legend: { show: false },
            dataLabels: { enabled: false },
            colors: [warningColor],
            series: [{
                name: 'Orders',
                data: {!! json_encode(array_slice($monthlyOrders, -5)) !!} // last 5 months
            }],
            xaxis: {
                labels: { show: false },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { show: false },
            tooltip: { x: { show: false } }
        };
        new ApexCharts(document.querySelector("#statistics-order-chart"), orderChartOptions).render();

        // Revenue Chart (Line)
        var profitChartOptions = {
            chart: {
                height: 70,
                type: 'line',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            grid: {
                borderColor: gridBorderColor,
                strokeDashArray: 5,
                xaxis: { lines: { show: true } },
                yaxis: { lines: { show: false } },
                padding: { top: -30, bottom: -10 }
            },
            stroke: { width: 3 },
            colors: [infoColor],
            series: [{
                name: 'Revenue',
                data: {!! json_encode(array_slice($monthlyRevenue, -6)) !!} // last 6 months
            }],
            markers: {
                size: 2,
                colors: infoColor,
                strokeColors: infoColor,
                strokeWidth: 2,
                strokeOpacity: 1,
                fillOpacity: 1,
                discrete: [{
                    seriesIndex: 0,
                    dataPointIndex: 5,
                    fillColor: '#ffffff',
                    strokeColor: infoColor,
                    size: 5
                }],
                shape: "circle",
                radius: 2,
                hover: { size: 3 }
            },
            xaxis: {
                labels: { show: true, style: { fontSize: '0px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: { show: false },
            tooltip: { x: { show: false } }
        };
        new ApexCharts(document.querySelector("#statistics-profit-chart"), profitChartOptions).render();

        // Earnings Chart (Donut)
        var earningsChartOptions = {
            chart: {
                type: 'donut',
                height: 120,
                toolbar: { show: false }
            },
            dataLabels: { enabled: false },
            series: [{!! $earningsSource['POS'] !!}, {!! $earningsSource['Web'] !!}, {!! $earningsSource['Custom'] !!}],
            labels: ['POS', 'Web', 'Custom'],
            stroke: { width: 0 },
            colors: [successColor, primaryColor, warningColor],
            grid: {
                padding: { right: -20, bottom: -8, left: -20 }
            },
            plotOptions: {
                pie: {
                    donut: {
                        labels: {
                            show: true,
                            name: { offsetY: 15 },
                            value: {
                                offsetY: -15,
                                formatter: function (val) {
                                    return parseInt(val) + "k";
                                }
                            },
                            total: {
                                show: true,
                                offsetY: 15,
                                label: 'Total',
                                formatter: function (w) {
                                    return "{{ number_format($totalRevenue / 1000, 1) }}k";
                                }
                            }
                        }
                    }
                }
            },
            legend: { show: false }
        };
        new ApexCharts(document.querySelector("#earnings-chart"), earningsChartOptions).render();

        // Revenue Report (Main Bar Chart)
        var revenueReportChartOptions = {
            chart: {
                height: 230,
                stacked: true,
                type: 'bar',
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    columnWidth: '17%',
                    borderRadius: 5
                }
            },
            colors: [primaryColor],
            series: [
                {
                    name: 'Revenue',
                    data: {!! json_encode($monthlyRevenue) !!}
                }
            ],
            dataLabels: { enabled: false },
            legend: { show: false },
            grid: {
                padding: { top: -20, bottom: -10 },
                yaxis: { lines: { show: false } }
            },
            xaxis: {
                categories: {!! json_encode($months) !!},
                labels: {
                    style: { colors: '#b9b9c3', fontSize: '0.86rem' }
                },
                axisTicks: { show: false },
                axisBorder: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#b9b9c3', fontSize: '0.86rem' },
                    formatter: function (value) {
                        return (value / 1000).toFixed(0) + 'k';
                    }
                }
            }
        };
        new ApexCharts(document.querySelector("#revenue-report-chart"), revenueReportChartOptions).render();
    });
</script>
@endpush
