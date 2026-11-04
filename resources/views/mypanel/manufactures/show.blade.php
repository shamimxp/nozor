@extends('layouts.mypanel')
@section('title', 'Manufacture Order ' . $order->invoice_no)

@section('content')
<div class="row">
    <div class="col-12 mb-1 d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Order {{ $order->invoice_no }}</h4>
        <div>
            @if($order->status >= 1)
                <a href="{{ route('mypanel.manufactures.print', $order->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">Print</a>
            @endif
            <a href="{{ route('mypanel.manufactures.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
        </div>
    </div>
    <div class="col-md-6 mb-2">
        <div class="card">
            <div class="card-header border-bottom"><h4 class="card-title mb-0">Order Info</h4></div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr><th width="40%">Invoice</th><td>{{ $order->invoice_no }}</td></tr>
                    <tr><th>Date</th><td>{{ optional($order->created_at)->format('d M Y h:i A') }}</td></tr>
                    <tr><th>Dealer</th><td>{{ $order->dealer_name ?: optional($order->dealer)->name ?: '—' }}</td></tr>
                    <tr><th>Qty</th><td>{{ (int) $order->manufacture_qty }} pcs</td></tr>
                    <tr><th>Received</th><td>{{ (int) ($order->received_qty ?? 0) }} pcs</td></tr>
                    <tr><th>Grand Total</th><td><strong>৳ {{ number_format($order->grand_total, 2) }}</strong></td></tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($order->collected_by)
                                <span class="badge badge-light-success">Collected</span>
                            @else
                                <span class="badge badge-light-warning">Pending</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-2">
        <div class="card">
            <div class="card-header border-bottom"><h4 class="card-title mb-0">Items</h4></div>
            <div class="card-body table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Part</th>
                            <th>Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($order->items as $item)
                            <tr>
                                <td>{{ optional($item->product)->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($item->part_type ?: 'N/A') }}</td>
                                <td>{{ $item->qty ?? $order->manufacture_qty }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">{{ optional($order->product)->name ?? 'N/A' }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
