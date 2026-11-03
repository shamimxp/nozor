@extends('layouts.admin')
@section('title', 'Analytical Business Dashboard')

@section('content')
    <!-- Content Header Row -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Business Intelligence & Analytics</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Executive Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
            <div class="form-group breadcrumb-right">
                <span class="badge badge-light-primary py-50 px-1 font-weight-bold">
                    <i data-feather="calendar" class="mr-50" style="width: 14px; height: 14px;"></i> {{ date('F d, Y') }}
                </span>
            </div>
        </div>
    </div>

    <section id="analytical-dashboard">
        <!-- FieldOra Executive Header Card -->
        <div class="fieldora-banner-card mb-2">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="d-flex align-items-center">
                    <div class="fieldora-banner-icon mr-1">
                        <i data-feather="activity"></i>
                    </div>
                    <div>
                        <h3 class="fieldora-banner-title">Executive Operations & Analytics Hub</h3>
                        <p class="fieldora-banner-subtitle">Real-time omnichannel sales, manufacturing throughput, cash flow, dues and inventory status</p>
                    </div>
                </div>
                <div class="fieldora-banner-actions mt-1 mt-md-0 d-flex align-items-center flex-wrap" style="gap: 8px;">
                    <a href="{{ route('admin.dealer-order.create') }}" class="btn btn-primary btn-sm">
                        <i data-feather="plus" class="mr-25"></i> Dealer Order
                    </a>
                    <a href="{{ route('admin.pos') }}" class="btn btn-outline-secondary btn-sm" target="_blank">
                        <i data-feather="shopping-cart" class="mr-25"></i> POS Terminal
                    </a>
                    <a href="{{ route('admin.collections.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="dollar-sign" class="mr-25"></i> Collections
                    </a>
                    <a href="{{ route('admin.worker-balance-sheet.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="users" class="mr-25"></i> Worker Ledger
                    </a>
                    <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i data-feather="rotate-ccw" class="mr-25"></i> Returns
                    </a>
                </div>
            </div>
        </div>

        <!-- 4 Primary Executive KPI Cards -->
        <div class="row match-height">
            <!-- Gross Omnichannel Revenue -->
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="card fieldora-stat-card mb-0 h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fieldora-stat-label">Gross Omnichannel Revenue</span>
                                <h3 class="fieldora-stat-value mt-50 mb-75" style="color: #111c38; font-weight: 700;">
                                    ৳ {{ number_format($kpis['totalGrossRevenue'], 2) }}
                                </h3>
                                <div class="d-flex flex-wrap align-items-center font-small-2" style="gap: 6px;">
                                    <span class="badge badge-light-success py-25 px-50 font-weight-bold">
                                        Today: ৳ {{ number_format($kpis['todayRevenue'], 0) }}
                                    </span>
                                    <span class="badge badge-light-primary py-25 px-50 font-weight-bold">
                                        This Month: ৳ {{ number_format($kpis['thisMonthRevenue'], 0) }}
                                    </span>
                                </div>
                            </div>
                            <div class="fieldora-stat-icon-wrapper" style="background: rgba(17, 28, 56, 0.08); color: #111c38; width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i data-feather="trending-up" style="width: 22px; height: 22px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Realized Cash Collected -->
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="card fieldora-stat-card mb-0 h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fieldora-stat-label">Realized Cash Inflow</span>
                                <h3 class="fieldora-stat-value mt-50 mb-75" style="color: #10b981; font-weight: 700;">
                                    ৳ {{ number_format($kpis['totalCashCollected'], 2) }}
                                </h3>
                                <div class="d-flex flex-wrap align-items-center font-small-2" style="gap: 6px;">
                                    <span class="badge badge-light-success py-25 px-50 font-weight-bold">
                                        <i data-feather="check-circle" style="width: 10px; height: 10px;" class="mr-25"></i> Collected
                                    </span>
                                    <span class="badge badge-light-info py-25 px-50" title="Dealer Pre-funded Wallets">
                                        Wallets: ৳ {{ number_format($kpis['totalDealerWallets'], 0) }}
                                    </span>
                                </div>
                            </div>
                            <div class="fieldora-stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.1); color: #10b981; width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i data-feather="dollar-sign" style="width: 22px; height: 22px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Outstanding Receivables (Dues) -->
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="card fieldora-stat-card mb-0 h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fieldora-stat-label">Outstanding Receivables</span>
                                <h3 class="fieldora-stat-value mt-50 mb-75" style="color: #ef4444; font-weight: 700;">
                                    ৳ {{ number_format($kpis['totalReceivableDue'], 2) }}
                                </h3>
                                <div class="d-flex flex-wrap align-items-center font-small-2" style="gap: 6px;">
                                    <span class="badge badge-light-danger py-25 px-50 font-weight-bold">
                                        Dealer: ৳ {{ number_format($kpis['channelDues']['dealer'], 0) }}
                                    </span>
                                    <span class="badge badge-light-warning py-25 px-50 font-weight-bold">
                                        POS/Cust: ৳ {{ number_format($kpis['channelDues']['pos'] + $kpis['channelDues']['custom'], 0) }}
                                    </span>
                                </div>
                            </div>
                            <div class="fieldora-stat-icon-wrapper" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i data-feather="clock" style="width: 22px; height: 22px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Omnichannel Orders -->
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="card fieldora-stat-card mb-0 h-100 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fieldora-stat-label">Omnichannel Orders</span>
                                <h3 class="fieldora-stat-value mt-50 mb-75" style="color: #2563eb; font-weight: 700;">
                                    {{ number_format($kpis['totalOrdersCount']) }} <small class="text-muted font-small-2">Orders</small>
                                </h3>
                                <div class="d-flex flex-wrap align-items-center font-small-2" style="gap: 6px;">
                                    <span class="badge badge-light-primary py-25 px-50 font-weight-bold">
                                        +{{ number_format($kpis['todayOrdersCount']) }} Today
                                    </span>
                                    <span class="text-muted font-small-1">
                                        Dealer: {{ $kpis['channelOrders']['dealer'] }} | POS: {{ $kpis['channelOrders']['pos'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="fieldora-stat-icon-wrapper" style="background: rgba(37, 99, 235, 0.1); color: #2563eb; width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i data-feather="shopping-bag" style="width: 22px; height: 22px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Analytics & Charts Row -->
        <div class="row match-height">
            <!-- 12-Month Multi-Channel Revenue Curve -->
            <div class="col-lg-8 col-12 mb-2">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap pb-1">
                        <div class="d-flex align-items-center">
                            <div class="fieldora-banner-icon mr-1" style="width: 36px; height: 36px; border-radius: 8px;">
                                <i data-feather="bar-chart-2" style="width: 18px; height: 18px;"></i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0" style="color: #111c38; font-weight: 700;">Omnichannel Revenue Performance ({{ date('Y') }})</h4>
                                <small class="text-muted">Monthly revenue streams across Dealer, POS, Custom, and Web channels</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-50 mt-sm-0" style="gap: 8px;">
                            <span class="badge badge-light-primary"><i data-feather="circle" style="width: 8px; height: 8px; fill: #111c38; stroke: #111c38;"></i> Dealer</span>
                            <span class="badge badge-light-success"><i data-feather="circle" style="width: 8px; height: 8px; fill: #10b981; stroke: #10b981;"></i> POS</span>
                            <span class="badge badge-light-warning"><i data-feather="circle" style="width: 8px; height: 8px; fill: #f59e0b; stroke: #f59e0b;"></i> Custom</span>
                            <span class="badge badge-light-info"><i data-feather="circle" style="width: 8px; height: 8px; fill: #0ea5e9; stroke: #0ea5e9;"></i> Web</span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div id="omnichannel-revenue-chart" style="min-height: 310px;"></div>
                    </div>
                </div>
            </div>

            <!-- Revenue Share Donut Chart -->
            <div class="col-lg-4 col-12 mb-2">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center pb-1">
                        <div class="d-flex align-items-center">
                            <div class="fieldora-banner-icon mr-1" style="width: 36px; height: 36px; border-radius: 8px;">
                                <i data-feather="pie-chart" style="width: 18px; height: 18px;"></i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0" style="color: #111c38; font-weight: 700;">Channel Revenue Share</h4>
                                <small class="text-muted">Proportional sales mix</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="channel-share-donut-chart" style="min-height: 220px;"></div>
                        <div class="mt-1 pt-75 border-top">
                            <div class="d-flex justify-content-between align-items-center mb-50">
                                <span class="d-flex align-items-center font-small-3">
                                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #111c38; margin-right: 8px;"></span>
                                    Dealer Orders
                                </span>
                                <span class="font-weight-bold" style="color: #111c38;">৳ {{ number_format($kpis['channelRevenues']['dealer'], 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-50">
                                <span class="d-flex align-items-center font-small-3">
                                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #10b981; margin-right: 8px;"></span>
                                    POS Sales
                                </span>
                                <span class="font-weight-bold" style="color: #10b981;">৳ {{ number_format($kpis['channelRevenues']['pos'], 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-50">
                                <span class="d-flex align-items-center font-small-3">
                                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #f59e0b; margin-right: 8px;"></span>
                                    Custom Orders
                                </span>
                                <span class="font-weight-bold" style="color: #f59e0b;">৳ {{ number_format($kpis['channelRevenues']['custom'], 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="d-flex align-items-center font-small-3">
                                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #0ea5e9; margin-right: 8px;"></span>
                                    Web Orders
                                </span>
                                <span class="font-weight-bold" style="color: #0ea5e9;">৳ {{ number_format($kpis['channelRevenues']['web'], 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Health & Dues Breakdown Row -->
        <div class="row match-height">
            <!-- Receivables Breakdown -->
            <div class="col-lg-6 col-12 mb-2">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center pb-1">
                        <div class="d-flex align-items-center">
                            <div class="fieldora-banner-icon mr-1" style="width: 36px; height: 36px; border-radius: 8px; background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                                <i data-feather="inbox" style="width: 18px; height: 18px;"></i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0" style="color: #111c38; font-weight: 700;">Receivables Portfolio (Pending Inflows)</h4>
                                <small class="text-muted">Total Outstanding: <strong class="text-danger">৳ {{ number_format($kpis['totalReceivableDue'], 2) }}</strong></small>
                            </div>
                        </div>
                        <a href="{{ route('admin.collections.index') }}" class="btn btn-outline-danger btn-sm py-25 px-50 font-small-2">
                            Record Collection
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Dealer Orders Dues -->
                            <div class="col-sm-6 col-12 mb-1">
                                <div class="p-1 rounded border" style="background: #ffffff; border-color: #efeae2 !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-50">
                                        <span class="fieldora-stat-label">Dealer Orders Due</span>
                                        <a href="{{ route('admin.dealer-order.due-list') }}" class="font-small-1 text-primary">View Due List</a>
                                    </div>
                                    <h4 class="mb-25 font-weight-bolder text-danger">৳ {{ number_format($kpis['channelDues']['dealer'], 2) }}</h4>
                                    <div class="font-small-2 text-muted">Active dealer network receivables</div>
                                </div>
                            </div>
                            <!-- POS Orders Dues -->
                            <div class="col-sm-6 col-12 mb-1">
                                <div class="p-1 rounded border" style="background: #ffffff; border-color: #efeae2 !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-50">
                                        <span class="fieldora-stat-label">POS Counter Dues</span>
                                        <a href="{{ route('admin.pos-order.due-list') }}" class="font-small-1 text-primary">View Due List</a>
                                    </div>
                                    <h4 class="mb-25 font-weight-bolder text-danger">৳ {{ number_format($kpis['channelDues']['pos'], 2) }}</h4>
                                    <div class="font-small-2 text-muted">Customer retail credit balance</div>
                                </div>
                            </div>
                            <!-- Custom Orders Dues -->
                            <div class="col-sm-6 col-12 mb-1 mb-sm-0">
                                <div class="p-1 rounded border" style="background: #ffffff; border-color: #efeae2 !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-50">
                                        <span class="fieldora-stat-label">Custom Orders Due</span>
                                        <a href="{{ route('admin.custom-order.due-list') }}" class="font-small-1 text-primary">View Due List</a>
                                    </div>
                                    <h4 class="mb-25 font-weight-bolder text-warning">৳ {{ number_format($kpis['channelDues']['custom'], 2) }}</h4>
                                    <div class="font-small-2 text-muted">Bespoke manufacturing orders</div>
                                </div>
                            </div>
                            <!-- Web Orders Pending -->
                            <div class="col-sm-6 col-12">
                                <div class="p-1 rounded border" style="background: #ffffff; border-color: #efeae2 !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-50">
                                        <span class="fieldora-stat-label">Web Orders Unsettled</span>
                                        <a href="{{ route('admin.web-order.index') }}" class="font-small-1 text-primary">View Orders</a>
                                    </div>
                                    <h4 class="mb-25 font-weight-bolder text-info">৳ {{ number_format($kpis['channelDues']['web'], 2) }}</h4>
                                    <div class="font-small-2 text-muted">Cash on delivery / pending delivery</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operational Payables & Outflows -->
            <div class="col-lg-6 col-12 mb-2">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center pb-1">
                        <div class="d-flex align-items-center">
                            <div class="fieldora-banner-icon mr-1" style="width: 36px; height: 36px; border-radius: 8px; background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                                <i data-feather="credit-card" style="width: 18px; height: 18px;"></i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0" style="color: #111c38; font-weight: 700;">Operational Payables & Outflows</h4>
                                <small class="text-muted">Total Payables: <strong class="text-primary">৳ {{ number_format($financialHealth['totalPayables'], 2) }}</strong></small>
                            </div>
                        </div>
                        <a href="{{ route('admin.worker-balance-sheet.index') }}" class="btn btn-outline-primary btn-sm py-25 px-50 font-small-2">
                            Worker Payouts
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Raw Material Dues -->
                            <div class="col-sm-6 col-12 mb-1">
                                <div class="p-1 rounded border" style="background: #ffffff; border-color: #efeae2 !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-50">
                                        <span class="fieldora-stat-label">Raw Material Due</span>
                                        <a href="{{ route('admin.inventory-purchase.index') }}" class="font-small-1 text-primary">Suppliers</a>
                                    </div>
                                    <h4 class="mb-25 font-weight-bolder text-danger">৳ {{ number_format($financialHealth['invPurchaseDue'], 2) }}</h4>
                                    <div class="font-small-2 text-muted">Factory raw inventory payables</div>
                                </div>
                            </div>
                            <!-- Worker Manufacturing Due -->
                            <div class="col-sm-6 col-12 mb-1">
                                <div class="p-1 rounded border" style="background: #ffffff; border-color: #efeae2 !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-50">
                                        <span class="fieldora-stat-label">Worker Wages Due</span>
                                        <a href="{{ route('admin.worker-balance-sheet.index') }}" class="font-small-1 text-primary">Ledger</a>
                                    </div>
                                    <h4 class="mb-25 font-weight-bolder text-danger">৳ {{ number_format($financialHealth['workerDue'], 2) }}</h4>
                                    <div class="font-small-2 text-muted">Pending piece-rate worker dues</div>
                                </div>
                            </div>
                            <!-- Total Cash Outflow Paid -->
                            <div class="col-sm-6 col-12 mb-1 mb-sm-0">
                                <div class="p-1 rounded border" style="background: #ffffff; border-color: #efeae2 !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-50">
                                        <span class="fieldora-stat-label">Lifetime Expense Outflow</span>
                                        <span class="badge badge-light-secondary font-small-1">Settled</span>
                                    </div>
                                    <h4 class="mb-25 font-weight-bolder text-dark">৳ {{ number_format($financialHealth['totalOutflow'], 2) }}</h4>
                                    <div class="font-small-2 text-muted">Purchases, wages, and operations</div>
                                </div>
                            </div>
                            <!-- Returns Refunded -->
                            <div class="col-sm-6 col-12">
                                <div class="p-1 rounded border" style="background: #ffffff; border-color: #efeae2 !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-50">
                                        <span class="fieldora-stat-label">Returns Refunded</span>
                                        <a href="{{ route('admin.returns.index') }}" class="font-small-1 text-primary">History</a>
                                    </div>
                                    <h4 class="mb-25 font-weight-bolder text-secondary">৳ {{ number_format($financialHealth['totalRefunds'], 2) }}</h4>
                                    <div class="font-small-2 text-muted">Wallet credits & adjustments</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Factory Floor Operations & Inventory Health Row -->
        <div class="row match-height">
            <!-- Factory Floor & Manufacturing Throughput -->
            <div class="col-lg-6 col-12 mb-2">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center pb-1">
                        <div class="d-flex align-items-center">
                            <div class="fieldora-banner-icon mr-1" style="width: 36px; height: 36px; border-radius: 8px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                                <i data-feather="cpu" style="width: 18px; height: 18px;"></i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0" style="color: #111c38; font-weight: 700;">Manufacturing & Quality Control</h4>
                                <small class="text-muted">Production velocity, finished stock and return claims</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.manufacture.index') }}" class="btn btn-outline-secondary btn-sm py-25 px-50 font-small-2">
                            Manage Jobs
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row text-center mb-1">
                            <div class="col-4 border-right">
                                <span class="fieldora-stat-label d-block mb-25">In Production</span>
                                <h3 class="font-weight-bolder text-warning mb-25">{{ $operations['inProgressJobs'] }}</h3>
                                <small class="text-muted font-small-1">Active Batch Jobs</small>
                            </div>
                            <div class="col-4 border-right">
                                <span class="fieldora-stat-label d-block mb-25">Completed</span>
                                <h3 class="font-weight-bolder text-success mb-25">{{ $operations['completedJobs'] }}</h3>
                                <small class="text-muted font-small-1">Finished Runs</small>
                            </div>
                            <div class="col-4">
                                <span class="fieldora-stat-label d-block mb-25">Units Produced</span>
                                <h3 class="font-weight-bolder text-primary mb-25">{{ number_format($operations['totalProducedQty']) }}</h3>
                                <small class="text-muted font-small-1">Total Pieces</small>
                            </div>
                        </div>
                        <div class="p-1 rounded mt-1" style="background: rgba(17, 28, 56, 0.03); border: 1px solid #efeae2;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="mr-75" style="width: 32px; height: 32px; border-radius: 6px; background: rgba(245, 158, 11, 0.15); color: #f59e0b; display: flex; align-items: center; justify-content: center;">
                                        <i data-feather="rotate-ccw" style="width: 16px; height: 16px;"></i>
                                    </div>
                                    <div>
                                        <strong style="color: #111c38;">Return & Warranty Audits</strong>
                                        <div class="font-small-2 text-muted">
                                            {{ $operations['totalReturnOrders'] }} Total Orders | <span class="text-warning font-weight-bold">{{ $operations['pendingReturnsCount'] }} Pending Review</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary btn-sm py-25 px-50">
                                    Audit Returns
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Critical Inventory & Stock-Out Alerts -->
            <div class="col-lg-6 col-12 mb-2">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center pb-1">
                        <div class="d-flex align-items-center">
                            <div class="fieldora-banner-icon mr-1" style="width: 36px; height: 36px; border-radius: 8px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                                <i data-feather="alert-triangle" style="width: 18px; height: 18px;"></i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0" style="color: #111c38; font-weight: 700;">Critical Inventory Watch</h4>
                                <small class="text-muted">
                                    <span class="text-danger font-weight-bold">{{ $operations['outOfStockCount'] }} Out of Stock</span> | 
                                    <span class="text-warning font-weight-bold">{{ $operations['lowStockCount'] }} Low Stock</span>
                                </small>
                            </div>
                        </div>
                        <a href="{{ route('admin.product.index') }}" class="btn btn-outline-secondary btn-sm py-25 px-50 font-small-2">
                            View Catalog
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background: rgba(17, 28, 56, 0.02); font-size: 0.8rem;">
                                <tr>
                                    <th style="padding-left: 20px;">Product Name</th>
                                    <th>Category</th>
                                    <th>Unit Price</th>
                                    <th class="text-center">Stock Level</th>
                                    <th class="text-right" style="padding-right: 20px;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($criticalInventory as $critItem)
                                    <tr>
                                        <td style="padding-left: 20px;">
                                            <span class="font-weight-bold" style="color: #111c38;">{{ Str::limit($critItem['name'], 30) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-secondary font-small-1">{{ $critItem['category'] }}</span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold">৳ {{ number_format($critItem['price'], 2) }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($critItem['stock'] <= 0)
                                                <span class="badge badge-light-danger py-25 px-50 font-small-1">Stock Out (0)</span>
                                            @else
                                                <span class="badge badge-light-warning py-25 px-50 font-small-1">{{ $critItem['stock'] }} left</span>
                                            @endif
                                        </td>
                                        <td class="text-right" style="padding-right: 20px;">
                                            <a href="{{ route('admin.product.edit', $critItem['id']) }}" class="btn btn-outline-primary btn-sm py-25 px-50 font-small-1">
                                                Restock
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            <i data-feather="check" class="text-success mr-25"></i> All products have optimal stock levels.
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Omnichannel Live Activity & Top Performing Products -->
        <div class="row match-height">
            <!-- Recent Omnichannel Orders Feed -->
            <div class="col-lg-7 col-12 mb-2">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center pb-1">
                        <div class="d-flex align-items-center">
                            <div class="fieldora-banner-icon mr-1" style="width: 36px; height: 36px; border-radius: 8px;">
                                <i data-feather="shopping-cart" style="width: 18px; height: 18px;"></i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0" style="color: #111c38; font-weight: 700;">Live Omnichannel Order Stream</h4>
                                <small class="text-muted">Unified live orders across Dealer, POS, Web and Custom channels</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.dealer-order.index') }}" class="btn btn-outline-secondary btn-sm py-25 px-50 font-small-2">
                            All Dealer Orders <i data-feather="chevron-right" class="ml-25"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead style="background: rgba(17, 28, 56, 0.02); font-size: 0.8rem;">
                                <tr>
                                    <th style="padding-left: 20px;">Channel</th>
                                    <th>Order #</th>
                                    <th>Customer / Party</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th class="text-right" style="padding-right: 20px;">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td style="padding-left: 20px;">
                                            <span class="badge badge-light-{{ $order['channel_badge'] }} font-small-1 font-weight-bold">
                                                {{ $order['channel'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bolder" style="color: #111c38;">{{ $order['order_no'] }}</span>
                                            <small class="d-block text-muted font-small-1">{{ \Carbon\Carbon::parse($order['created_at'])->format('d M, Y') }}</small>
                                        </td>
                                        <td>
                                            <span class="font-weight-bold" style="color: #1e293b;">{{ Str::limit($order['party_name'], 22) }}</span>
                                        </td>
                                        <td>
                                            <span class="font-weight-bolder text-success">৳ {{ number_format($order['amount'], 2) }}</span>
                                        </td>
                                        <td>
                                            @if($order['due'] <= 0)
                                                <span class="badge badge-light-success font-small-1">Paid</span>
                                            @else
                                                <span class="badge badge-light-danger font-small-1" title="Due: ৳ {{ number_format($order['due'], 2) }}">
                                                    Due ৳ {{ number_format($order['due'], 0) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-right" style="padding-right: 20px;">
                                            <a href="{{ $order['view_url'] }}" class="btn btn-outline-secondary btn-sm py-25 px-50 font-small-1">
                                                <i data-feather="eye" style="width: 12px; height: 12px;" class="mr-25"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">No orders found in database.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products -->
            <div class="col-lg-5 col-12 mb-2">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center pb-1">
                        <div class="d-flex align-items-center">
                            <div class="fieldora-banner-icon mr-1" style="width: 36px; height: 36px; border-radius: 8px; background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                                <i data-feather="award" style="width: 18px; height: 18px;"></i>
                            </div>
                            <div>
                                <h4 class="card-title mb-0" style="color: #111c38; font-weight: 700;">Top Performing Products</h4>
                                <small class="text-muted">High velocity catalog bestsellers</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.product.index') }}" class="btn btn-outline-secondary btn-sm py-25 px-50 font-small-2">
                            Catalog
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush mb-0">
                            @forelse($topProducts as $idx => $prod)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2 border-top-0">
                                    <div class="d-flex align-items-center" style="max-width: 70%;">
                                        <div class="mr-75 position-relative">
                                            <img src="{{ $prod['image'] }}" alt="{{ $prod['name'] }}" class="rounded" style="width: 44px; height: 44px; object-fit: cover; border: 1px solid #efeae2;">
                                            <span class="badge badge-pill badge-primary position-absolute" style="top: -6px; left: -6px; font-size: 0.65rem; padding: 2px 5px;">#{{ $idx + 1 }}</span>
                                        </div>
                                        <div class="text-truncate">
                                            <h6 class="mb-0 text-truncate font-weight-bold" style="color: #111c38;" title="{{ $prod['name'] }}">
                                                {{ $prod['name'] }}
                                            </h6>
                                            <small class="text-muted font-small-1">
                                                {{ $prod['category'] }} • ৳ {{ number_format($prod['price'], 2) }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge badge-light-success font-weight-bold py-25 px-50 font-small-1">
                                            {{ number_format($prod['sold_qty']) }} Sold
                                        </span>
                                        <small class="d-block text-muted font-small-1 mt-25">
                                            Stock: <strong>{{ $prod['stock'] }}</strong>
                                        </small>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-3">No sales record available yet.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    $(document).ready(function () {
        var navyColor    = '#111c38';
        var greenColor   = '#10b981';
        var amberColor   = '#f59e0b';
        var cyanColor    = '#0ea5e9';
        var gridBorder   = '#efeae2';

        // 1. 12-Month Omnichannel Revenue Spline/Area Chart
        var omnichannelOptions = {
            chart: {
                height: 310,
                type: 'area',
                toolbar: { show: false },
                dropShadow: {
                    enabled: true,
                    top: 2,
                    left: 0,
                    blur: 4,
                    opacity: 0.05
                }
            },
            colors: [navyColor, greenColor, amberColor, cyanColor],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: [3, 2.5, 2.5, 2]
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            series: [
                {
                    name: 'Dealer Orders',
                    data: {!! json_encode($monthlyTrends['dealerSeries']) !!}
                },
                {
                    name: 'POS Sales',
                    data: {!! json_encode($monthlyTrends['posSeries']) !!}
                },
                {
                    name: 'Custom Orders',
                    data: {!! json_encode($monthlyTrends['customSeries']) !!}
                },
                {
                    name: 'Web Orders',
                    data: {!! json_encode($monthlyTrends['webSeries']) !!}
                }
            ],
            xaxis: {
                categories: {!! json_encode($monthlyTrends['months']) !!},
                labels: {
                    style: { colors: '#64748b', fontSize: '12px', fontWeight: 500 }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    style: { colors: '#64748b', fontSize: '11px' },
                    formatter: function (val) {
                        if (val >= 1000) {
                            return '৳ ' + (val / 1000).toFixed(0) + 'k';
                        }
                        return '৳ ' + val;
                    }
                }
            },
            grid: {
                borderColor: gridBorder,
                strokeDashArray: 4,
                padding: { top: 0, right: 15, bottom: 0, left: 10 }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val) {
                        return '৳ ' + Number(val).toLocaleString('en-US', { minimumFractionDigits: 2 });
                    }
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                labels: { colors: '#64748b' }
            }
        };

        var omnichannelChartEl = document.querySelector("#omnichannel-revenue-chart");
        if (omnichannelChartEl) {
            new ApexCharts(omnichannelChartEl, omnichannelOptions).render();
        }

        // 2. Channel Revenue Share Donut Chart
        var dealerVal = {{ (float) $kpis['channelRevenues']['dealer'] }};
        var posVal    = {{ (float) $kpis['channelRevenues']['pos'] }};
        var customVal = {{ (float) $kpis['channelRevenues']['custom'] }};
        var webVal    = {{ (float) $kpis['channelRevenues']['web'] }};

        var donutOptions = {
            chart: {
                type: 'donut',
                height: 220,
                toolbar: { show: false }
            },
            labels: ['Dealer Orders', 'POS Sales', 'Custom Orders', 'Web Orders'],
            series: [dealerVal, posVal, customVal, webVal],
            colors: [navyColor, greenColor, amberColor, cyanColor],
            stroke: { width: 2, colors: ['#ffffff'] },
            dataLabels: { enabled: false },
            legend: { show: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '13px',
                                fontWeight: 600,
                                color: '#64748b',
                                offsetY: -5
                            },
                            value: {
                                show: true,
                                fontSize: '16px',
                                fontWeight: 700,
                                color: '#111c38',
                                offsetY: 5,
                                formatter: function (val) {
                                    return '৳ ' + (Number(val) / 1000).toFixed(1) + 'k';
                                }
                            },
                            total: {
                                show: true,
                                label: 'Gross Sales',
                                fontSize: '12px',
                                color: '#64748b',
                                formatter: function (w) {
                                    var sum = w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0);
                                    return '৳ ' + (sum / 1000).toFixed(0) + 'k';
                                }
                            }
                        }
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return '৳ ' + Number(val).toLocaleString('en-US', { minimumFractionDigits: 2 });
                    }
                }
            }
        };

        var donutChartEl = document.querySelector("#channel-share-donut-chart");
        if (donutChartEl) {
            new ApexCharts(donutChartEl, donutOptions).render();
        }

        // Re-initialize feather icons if present
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush
