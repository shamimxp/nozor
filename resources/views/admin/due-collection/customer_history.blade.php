@extends('layouts.admin')

@section('title', 'Customer Payment History')

@push('styles')
<style>
    .customer-info-header {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Payment History</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.customer.index') }}">Customers</a></li>
                            <li class="breadcrumb-item active">Payment History</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="customer-info-header">
            <div class="row">
                <div class="col-md-6">
                    <h4>Customer: {{ $customer->name }}</h4>
                    <p class="mb-0">Phone: {{ $customer->phone }}</p>
                    <p class="mb-0">Email: {{ $customer->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <section id="basic-datatable">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">Payments from {{ $customer->name }}</h4>
                        </div>
                        <div class="card-datatable p-2">
                            <table class="table table-bordered" id="customer-payments-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Payment For</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                                        <td>
                                            <span class="badge badge-light-primary text-uppercase">{{ str_replace('_', ' ', $payment->payment_for) }}</span>
                                            @if($payment->payable)
                                                (#{{ $payment->payable->order_number ?? $payment->payable->id }})
                                            @endif
                                        </td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td>৳{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->note ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No payments found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
