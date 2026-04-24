@extends('layouts.admin')
@section('title', 'POS Order Analysis')
@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="font-weight-bolder mb-0">৳{{ number_format($total_sales, 2) }}</h2>
                        <p class="card-text">Total Sales (This Month)</p>
                    </div>
                    <div class="avatar bg-light-primary p-50">
                        <div class="avatar-content">
                            <i data-feather="dollar-sign" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="font-weight-bolder mb-0">৳{{ number_format($total_paid, 2) }}</h2>
                        <p class="card-text">Total Paid (This Month)</p>
                    </div>
                    <div class="avatar bg-light-success p-50">
                        <div class="avatar-content">
                            <i data-feather="check-circle" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="font-weight-bolder mb-0 text-danger">৳{{ number_format($total_due, 2) }}</h2>
                        <p class="card-text">Total Due (This Month)</p>
                    </div>
                    <div class="avatar bg-light-danger p-50">
                        <div class="avatar-content">
                            <i data-feather="alert-circle" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="font-weight-bolder mb-0">{{ $total_orders }}</h2>
                        <p class="card-text">Total Orders (This Month)</p>
                    </div>
                    <div class="avatar bg-light-info p-50">
                        <div class="avatar-content">
                            <i data-feather="shopping-cart" class="font-medium-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">Recent POS Orders (This Month)</h4>
            </div>
            <div class="card-body table-responsive pt-1">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_orders as $order)
                        <tr>
                            <td><a href="{{ route('admin.pos-order.show', $order->id) }}">#{{ $order->order_number }}</a></td>
                            <td>{{ date('d M, Y', strtotime($order->order_date)) }}</td>
                            <td>
                                @if($order->customer)
                                    {{ $order->customer->name }}
                                @else
                                    <span class="text-muted">Walk-in Customer</span>
                                @endif
                            </td>
                            <td>৳{{ number_format($order->total_amount, 2) }}</td>
                            <td>৳{{ number_format($order->paid_amount, 2) }}</td>
                            <td class="{{ $order->due_amount > 0 ? 'text-danger' : 'text-success' }}">৳{{ number_format($order->due_amount, 2) }}</td>
                            <td>
                                @php
                                    $status_class = [
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        'cancelled' => 'danger'
                                    ][$order->order_status] ?? 'info';
                                @endphp
                                <span class="badge badge-light-{{ $status_class }} text-uppercase">{{ $order->order_status }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center p-2">No orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
