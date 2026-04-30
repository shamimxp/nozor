@extends('layouts.admin')
@section('title', 'Stock Purchase Details')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-25">Purchase #{{ $purchase->purchase_number }}</h4>
                    <div class="text-muted">
                        {{ $purchase->purchase_date->format('d M, Y') }}
                    </div>
                </div>
                <span class="badge badge-light-{{ ['pending' => 'warning', 'confirm' => 'info', 'confirmed' => 'info', 'received' => 'success'][$purchase->status] ?? 'secondary' }} text-uppercase">
                    {{ $purchase->status }}
                </span>
            </div>
            <div class="card-body pt-2">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>Vendor:</strong><br>
                        {{ $purchase->vendor->name ?? '-' }}<br>
                        <small class="text-muted">{{ $purchase->vendor->phone ?? '-' }}</small>
                    </div>
                    <div class="col-md-4">
                        <strong>Created By:</strong><br>
                        {{ $purchase->creator->name ?? 'System' }}
                    </div>
                    <div class="col-md-4">
                        <strong>Note:</strong><br>
                        {{ $purchase->note ?: '-' }}
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Purchase Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchase->items as $item)
                                <tr>
                                    <td>{{ $item->product->name ?? '-' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>৳{{ number_format($item->purchase_price, 2) }}</td>
                                    <td>৳{{ number_format($item->quantity * $item->purchase_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total Amount</th>
                                <th>৳{{ number_format($purchase->total_amount, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Paid Amount</th>
                                <th>৳{{ number_format($purchase->paid_amount, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-right">Due Amount</th>
                                <th>৳{{ number_format($purchase->due_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <h5 class="mt-2">Payment History</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchase->payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date ? $payment->payment_date->format('d M, Y') : '-' }}</td>
                                    <td>৳{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->payment_method }}</td>
                                    <td>{{ $payment->note ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No payment history found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-2">
                    <a href="{{ route('admin.inventory-purchase.index') }}" class="btn btn-secondary">Back</a>
                    @if($purchase->status !== 'received')
                        <a href="{{ route('admin.inventory-purchase.edit', $purchase->id) }}" class="btn btn-primary">Edit</a>
                    @endif
                    @if(in_array($purchase->status, ['pending', 'confirm']))
                        <a href="{{ route('admin.inventory-purchase.receive', $purchase->id) }}" class="btn btn-success">Receive</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
