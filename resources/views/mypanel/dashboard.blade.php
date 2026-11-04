@extends('layouts.mypanel')
@section('title', 'Worker Dashboard')

@section('content')
@if(session()->has('admin_id'))
    <div class="alert alert-warning">
        <div class="alert-body d-flex justify-content-between align-items-center">
            <span>You are currently logged in as Worker (Impersonation Mode)</span>
            <a href="{{ route('mypanel.back') }}" class="btn btn-sm btn-dark">Back to Admin</a>
        </div>
    </div>
@endif
<style>
    .stat-card-modern {
        border: 1px solid #efeae2;
        border-bottom: 3px solid #111c38;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(17, 28, 56, 0.04);
        background: #FDFAF5;
        padding: 1.5rem;
        transition: all 0.3s ease;
        height: 100%;
        opacity: 0;
        animation: fadeInUp 0.6s ease-out forwards;
    }
    .stat-card-modern:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(17, 28, 56, 0.08); }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .delay-1 { animation-delay: 0.1s; } .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; } .delay-4 { animation-delay: 0.4s; }
    .delay-5 { animation-delay: 0.5s; } .delay-6 { animation-delay: 0.6s; }
    .stat-card-modern .stat-value { font-size: 1.6rem; font-weight: 600; color: #334155; line-height: 1; }
    .stat-card-modern .stat-label { font-size: 0.9rem; color: #64748b; font-weight: 500; margin-bottom: 0.5rem; }
    .stat-icon-wrapper { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
    .bg-soft-primary { background-color: #f3f0ff; color: #7c3aed; }
    .bg-soft-warning { background-color: #fff7ed; color: #ea580c; }
    .bg-soft-success { background-color: #ecfdf5; color: #059669; }
    .bg-soft-info { background-color: #e0f2fe; color: #06b6d4; }
    .bg-soft-danger { background-color: #fef2f2; color: #ef4444; }
</style>

<div class="row match-height mb-2">
    <div class="col-12 mb-1">
        <h3 class="mb-0">Welcome, {{ $worker->name }}</h3>
        <p class="text-muted mb-0">{{ $worker->type_label }} · {{ $worker->phone }}</p>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-1">
        <div class="stat-card-modern delay-1">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="stat-label">Total Orders</div>
                <div class="stat-icon-wrapper bg-soft-primary"><i data-feather="box"></i></div>
            </div>
            <div class="stat-value">{{ $totalOrders }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-1">
        <div class="stat-card-modern delay-2">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="stat-label">Pending</div>
                <div class="stat-icon-wrapper bg-soft-warning"><i data-feather="clock"></i></div>
            </div>
            <div class="stat-value">{{ $pendingOrders }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-1">
        <div class="stat-card-modern delay-3">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="stat-label">Collected</div>
                <div class="stat-icon-wrapper bg-soft-success"><i data-feather="check-circle"></i></div>
            </div>
            <div class="stat-value">{{ $collectedOrders }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-1">
        <div class="stat-card-modern delay-4">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="stat-label">Total Billed</div>
                <div class="stat-icon-wrapper bg-soft-info"><i data-feather="file-text"></i></div>
            </div>
            <div class="stat-value">৳{{ number_format($totalBilled, 0) }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-1">
        <div class="stat-card-modern delay-5">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="stat-label">Total Paid</div>
                <div class="stat-icon-wrapper bg-soft-success"><i data-feather="credit-card"></i></div>
            </div>
            <div class="stat-value">৳{{ number_format($totalPaid, 0) }}</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-1">
        <div class="stat-card-modern delay-6">
            <div class="d-flex justify-content-between align-items-start mb-1">
                <div class="stat-label">Due Balance</div>
                <div class="stat-icon-wrapper bg-soft-danger"><i data-feather="alert-circle"></i></div>
            </div>
            <div class="stat-value">৳{{ number_format($totalDue, 0) }}</div>
        </div>
    </div>
</div>

<div class="row match-height">
    <div class="col-lg-8 mb-2">
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title mb-0">Yearly Overview ({{ date('Y') }})</h4>
            </div>
            <div class="card-body">
                <canvas id="workerChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-2">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Recent Orders</h4>
                <a href="{{ route('mypanel.manufactures.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($recentOrders as $order)
                        @php
                            $firstItem = $order->items->first();
                            $product = $firstItem ? $firstItem->product : $order->product;
                        @endphp
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('mypanel.manufactures.show', $order->id) }}" class="font-weight-bold">{{ $order->invoice_no }}</a>
                                <div class="small text-muted">{{ $product->name ?? 'N/A' }}</div>
                            </div>
                            @if($order->collected_by)
                                <span class="badge badge-light-success">Collected</span>
                            @else
                                <span class="badge badge-light-warning">Pending</span>
                            @endif
                        </li>
                    @empty
                        <li class="list-group-item text-muted">No manufacture orders assigned yet.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    new Chart(document.getElementById('workerChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: @json($months),
            datasets: [
                {
                    label: 'Orders',
                    data: @json($orderCounts),
                    borderColor: '#7367f0',
                    backgroundColor: 'rgba(115,103,240,0.1)',
                    tension: 0.35,
                    fill: true
                },
                {
                    label: 'Paid Amount',
                    data: @json($paidAmounts),
                    borderColor: '#28c76f',
                    backgroundColor: 'rgba(40,199,111,0.08)',
                    tension: 0.35,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endpush
