@extends('layouts.dealer')

@section('title', 'Dealer Dashboard')

@section('content')
    @if(session()->has('admin_id'))
        <div class="alert alert-warning">
            <div class="alert-body d-flex justify-content-between align-items-center">
                <span>You are currently logged in as Dealer (Impersonation Mode)</span>
                <a href="{{ route('dealer.back') }}" class="btn btn-sm btn-dark">Back to Admin</a>
            </div>
        </div>
    @endif

    <style>
        .stat-card-modern {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-bottom-width: 3px;
            border-radius: 6px;
            box-shadow: 0 4px 15px -4px rgba(0, 0, 0, 0.03);
            background: #fff;
            padding: 1.5rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }
        .stat-card-modern:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.1);
            border-bottom-width: 5px;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }

        .stat-card-modern .stat-value {
            font-size: 1.8rem;
            font-weight: 500;
            color: #334155;
            line-height: 1;
        }
        .stat-card-modern .stat-label {
            font-size: 0.9rem;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        .stat-card-modern .stat-sub-label {
            font-size: 0.8rem;
            color: #94a3b8;
        }
        .stat-card-modern .stat-icon-wrapper {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .bg-soft-primary { background-color: #f3f0ff; color: #7c3aed; }
        .bg-soft-warning { background-color: #fff7ed; color: #ea580c; }
        .bg-soft-danger { background-color: #fef2f2; color: #ef4444; }
        .bg-soft-info { background-color: #e0f2fe; color: #06b6d4; }
        
        .border-bottom-primary { border-bottom-color: #e9d5ff !important; }
        .border-bottom-warning { border-bottom-color: #fed7aa !important; }
        .border-bottom-danger { border-bottom-color: #fecaca !important; }
        .border-bottom-info { border-bottom-color: #a5f3fc !important; }

        .stat-card-modern.border-bottom-primary:hover { border-bottom-color: #c084fc !important; }
        .stat-card-modern.border-bottom-warning:hover { border-bottom-color: #fb923c !important; }
        .stat-card-modern.border-bottom-danger:hover { border-bottom-color: #f87171 !important; }
        .stat-card-modern.border-bottom-info:hover { border-bottom-color: #22d3ee !important; }
        
        .text-dark { color: #334155 !important; }
        
        .earning-tabs .nav-link {
            border: 1px solid transparent;
            border-radius: 8px;
            padding: 1rem 0.5rem;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }
        .earning-tabs .nav-link.active {
            border-color: #7c3aed;
            box-shadow: 0 4px 12px 0 rgba(124, 58, 237, 0.2);
            background-color: #fff;
        }
        .earning-tabs .tab-icon-wrapper {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            color: #64748b;
        }
        .earning-tabs .nav-link.active .tab-icon-wrapper {
            background-color: #f3f0ff;
            color: #7c3aed;
        }
        .apexcharts-toolbar {
            display: none !important;
        }
    </style>

    <div class="row">
        <!-- Confirmed Orders -->
        <div class="col-xl-3 col-md-6 col-12 mb-3">
            <div class="stat-card-modern delay-1 border-bottom-primary">
                <div class="d-flex align-items-center mb-1">
                    <div class="stat-icon-wrapper bg-soft-primary mr-1">
                        <i data-feather="check-circle" width="20" height="20"></i>
                    </div>
                    <div class="stat-value mb-0">{{ $totalConfirmedOrders }}</div>
                </div>
                <div class="stat-label">Confirmed orders</div>
                <div class="stat-sub-label"><strong class="text-dark">+ Overall</strong> since joined</div>
            </div>
        </div>

        <!-- Requested Orders -->
        <div class="col-xl-3 col-md-6 col-12 mb-3">
            <div class="stat-card-modern delay-2 border-bottom-warning">
                <div class="d-flex align-items-center mb-1">
                    <div class="stat-icon-wrapper bg-soft-warning mr-1">
                        <i data-feather="file-text" width="20" height="20"></i>
                    </div>
                    <div class="stat-value mb-0">{{ $totalRequestedOrders }}</div>
                </div>
                <div class="stat-label">Requested orders</div>
                <div class="stat-sub-label"><strong class="text-dark">+ Overall</strong> since joined</div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="col-xl-3 col-md-6 col-12 mb-3">
            <div class="stat-card-modern delay-3 border-bottom-danger">
                <div class="d-flex align-items-center mb-1">
                    <div class="stat-icon-wrapper bg-soft-danger mr-1">
                        <i data-feather="box" width="20" height="20"></i>
                    </div>
                    <div class="stat-value mb-0">{{ $totalProducts }}</div>
                </div>
                <div class="stat-label">Total products</div>
                <div class="stat-sub-label"><strong class="text-dark">+ Overall</strong> system total</div>
            </div>
        </div>

        <!-- Order Delivered -->
        <div class="col-xl-3 col-md-6 col-12 mb-3">
            <div class="stat-card-modern delay-4 border-bottom-info">
                <div class="d-flex align-items-center mb-1">
                    <div class="stat-icon-wrapper bg-soft-info mr-1">
                        <i data-feather="truck" width="20" height="20"></i>
                    </div>
                    <div class="stat-value mb-0">{{ $totalOrderDelivered }}</div>
                </div>
                <div class="stat-label">Orders delivered</div>
                <div class="stat-sub-label"><strong class="text-dark">+ Overall</strong> since joined</div>
            </div>
        </div>
    </div>

    <!-- Graph Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card mt-2" style="border: 1px solid rgba(226, 232, 240, 0.8); border-radius: 6px; box-shadow: 0 4px 15px -4px rgba(0, 0, 0, 0.03); opacity: 0; animation: fadeInUp 0.6s ease-out forwards; animation-delay: 0.5s;">
                <div class="card-header d-flex justify-content-between align-items-center pb-0 border-bottom-0">
                    <div>
                        <h4 class="card-title mb-50" style="color: #334155; font-weight: 600;">Monthly Reports</h4>
                        <small class="text-muted" style="font-size: 0.9rem;">Monthly Order Overview</small>
                    </div>
                    <i data-feather="more-vertical" class="text-muted cursor-pointer"></i>
                </div>
                <div class="card-body mt-2 pt-0">
                    <ul class="nav nav-tabs nav-justified earning-tabs d-flex justify-content-start" id="earningTabs" role="tablist" style="border-bottom: none; max-width: 600px;">
                        <li class="nav-item">
                            <a class="nav-link active" id="sales-tab" data-toggle="tab" href="#sales" role="tab" aria-selected="true" onclick="updateChart('sales')">
                                <div class="tab-icon-wrapper mx-auto mb-50">
                                    <i data-feather="bar-chart-2"></i>
                                </div>
                                <span class="font-weight-bold" style="color: #475569; font-size: 0.85rem;">Confirm Amt</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="request-tab" data-toggle="tab" href="#request" role="tab" aria-selected="false" onclick="updateChart('request')">
                                <div class="tab-icon-wrapper mx-auto mb-50">
                                    <i data-feather="file-text"></i>
                                </div>
                                <span class="font-weight-bold" style="color: #475569; font-size: 0.85rem;">Req Qty</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="orders-tab" data-toggle="tab" href="#orders" role="tab" aria-selected="false" onclick="updateChart('orders')">
                                <div class="tab-icon-wrapper mx-auto mb-50">
                                    <i data-feather="shopping-cart"></i>
                                </div>
                                <span class="font-weight-bold" style="color: #475569; font-size: 0.85rem;">Order Qty</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="delivery-tab" data-toggle="tab" href="#delivery" role="tab" aria-selected="false" onclick="updateChart('delivery')">
                                <div class="tab-icon-wrapper mx-auto mb-50">
                                    <i data-feather="truck"></i>
                                </div>
                                <span class="font-weight-bold" style="color: #475569; font-size: 0.85rem;">Delivery Qty</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content mt-3">
                        <div id="monthlyChart" style="min-height: 300px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    var months = @json($months);
    var chartData = {
        sales: {
            name: 'Confirmed Amount (৳)',
            data: @json($confirmedOrderAmount)
        },
        request: {
            name: 'Requested Qty',
            data: @json($requestedOrderQty)
        },
        orders: {
            name: 'Ordered Qty',
            data: @json($orderQuantity)
        },
        delivery: {
            name: 'Delivered Qty',
            data: @json($deliveredQuantity)
        }
    };

    var options = {
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '20%',
                borderRadius: 4
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        series: [{
            name: chartData.sales.name,
            data: chartData.sales.data
        }],
        xaxis: {
            categories: months,
            axisBorder: { show: false },
            axisTicks: { show: false },
            labels: {
                style: {
                    colors: '#94a3b8',
                    fontSize: '13px'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#94a3b8',
                    fontSize: '13px'
                },
                formatter: function (value) {
                    if(value >= 1000) {
                        return (value / 1000).toFixed(1) + 'k';
                    }
                    return value;
                }
            }
        },
        fill: {
            opacity: 1
        },
        colors: ['#7c3aed'],
        tooltip: {
            y: {
                formatter: function (val) {
                    return val;
                }
            }
        },
        grid: {
            borderColor: '#f1f5f9',
            strokeDashArray: 4,
            yaxis: {
                lines: { show: true }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#monthlyChart"), options);
    chart.render();

    function updateChart(type) {
        chart.updateSeries([{
            name: chartData[type].name,
            data: chartData[type].data
        }]);
    }
</script>
@endpush
