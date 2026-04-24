@extends('layouts.admin')
@section('title', 'Payment Detail - ' . $vendor->name)
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">Payment History: {{ $vendor->name }} ({{ $vendor->company_name }})</h4>
                <a href="{{ route('admin.purchase.vendor-history') }}" class="btn btn-outline-primary">Back to Summary</a>
            </div>
            <div class="card-body table-responsive pt-2">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Purchase Reference</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                            <td>
                                @if($payment->purchase)
                                    <a href="{{ route('admin.purchase.show', $payment->purchase_id) }}">#{{ $payment->purchase->purchase_number }}</a>
                                @else
                                    General Payment
                                @endif
                            </td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>৳{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->note }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center p-2">No payment history found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
