<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Web Orders List</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }

        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #4361ee; padding-bottom: 10px; }
        .header h1 { font-size: 18px; color: #4361ee; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #666; }

        .meta { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 9px; color: #555; }
        .meta span { background: #f1f3ff; padding: 3px 8px; border-radius: 4px; border: 1px solid #dde1f7; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #4361ee; color: #fff; }
        thead th { padding: 7px 6px; text-align: left; font-size: 9px; font-weight: bold; }
        tbody tr:nth-child(even) { background: #f6f7ff; }
        tbody td { padding: 6px 6px; border-bottom: 1px solid #e6e8f7; font-size: 9px; vertical-align: middle; }

        .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-pending    { background: #fff3cd; color: #856404; }
        .badge-processing { background: #cff4fc; color: #0a6eaf; }
        .badge-delivered  { background: #d1e7dd; color: #145a32; }
        .badge-cancelled  { background: #f8d7da; color: #842029; }

        .text-right { text-align: right; }
        .text-danger { color: #dc3545; }
        .text-success { color: #198754; }

        .totals-row { background: #eef0ff !important; font-weight: bold; }
        .totals-row td { border-top: 2px solid #4361ee; }

        .footer { margin-top: 20px; font-size: 8px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 8px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Web Orders Report</h1>
    <p>Generated on {{ date('d M Y, h:i A') }}</p>
</div>

<div class="meta">
    <span>
        @if($filters['invoice_no'])
            Invoice No: {{ $filters['invoice_no'] }}
        @else
            Invoice No: All
        @endif
    </span>
    <span>
        @if($filters['start_date'] && $filters['end_date'])
            Period: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($filters['end_date'])->format('d M Y') }}
        @else
            Period: All Time
        @endif
    </span>
    <span>
        Status:
        @if($filters['status'])
            {{ strtoupper(str_replace('_', ' ', $filters['status'])) }}
        @else
            All
        @endif
    </span>
    <span>Total Orders: {{ $orders->count() }}</span>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Invoice No</th>
            <th>Date</th>
            <th>Customer</th>
            <th class="text-right">Qty</th>
            <th class="text-right">Subtotal</th>
            <th class="text-right">Shipping</th>
            <th class="text-right">Discount</th>
            <th class="text-right">Grand Total</th>
            <th>Pay Method</th>
            <th>Pay Status</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalGrand = 0; $totalSub = 0; $totalShipping = 0; $totalDiscount = 0;
        @endphp

        @forelse($orders as $i => $order)
            @php
                $totalGrand += $order->total;
                $totalSub  += $order->subtotal;
                $totalShipping += $order->shipping_charge;
                $totalDiscount += $order->discount;

                $badgeClass = match($order->status) {
                    'pending'       => 'badge-pending',
                    'processing'    => 'badge-processing',
                    'delivered'     => 'badge-delivered',
                    'cancelled'     => 'badge-cancelled',
                    default         => 'badge-pending',
                };
                $statusLabel = match($order->status) {
                    'pending'       => 'Pending',
                    'processing'    => 'Processing',
                    'delivered'     => 'Delivered',
                    'cancelled'     => 'Cancelled',
                    default         => ucfirst($order->status),
                };
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $order->invoice_no }}</strong></td>
                <td>{{ $order->created_at->format('d M Y') }}</td>
                <td>
                    {{ $order->address->name ?? 'N/A' }}<br>
                    <small>{{ $order->address->phone ?? '' }}</small>
                </td>
                <td class="text-right">{{ $order->items->sum('quantity') }}</td>
                <td class="text-right">৳{{ number_format($order->subtotal, 2) }}</td>
                <td class="text-right">৳{{ number_format($order->shipping_charge, 2) }}</td>
                <td class="text-right">৳{{ number_format($order->discount, 2) }}</td>
                <td class="text-right">৳{{ number_format($order->total, 2) }}</td>
                <td>{{ strtoupper($order->payment_method) }}</td>
                <td class="{{ $order->status == 'delivered' ? 'text-success' : 'text-danger' }}">{{ $order->status == 'delivered' ? 'PAID' : 'DUE' }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span></td>
            </tr>
        @empty
            <tr>
                <td colspan="12" style="text-align:center; color:#999; padding: 20px;">No records found.</td>
            </tr>
        @endforelse

        @if($orders->count() > 0)
        <tr class="totals-row">
            <td colspan="5" class="text-right"><strong>TOTALS</strong></td>
            <td class="text-right"><strong>৳{{ number_format($totalSub, 2) }}</strong></td>
            <td class="text-right"><strong>৳{{ number_format($totalShipping, 2) }}</strong></td>
            <td class="text-right"><strong>৳{{ number_format($totalDiscount, 2) }}</strong></td>
            <td class="text-right"><strong>৳{{ number_format($totalGrand, 2) }}</strong></td>
            <td colspan="3"></td>
        </tr>
        @endif
    </tbody>
</table>

<div class="footer">
    This report was generated automatically. &copy; {{ date('Y') }} Nozor.
</div>

</body>
</html>
