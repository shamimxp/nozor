<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dealer Order List</title>
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
        .badge-confirm    { background: #cff4fc; color: #0a6eaf; }
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
    <h1>Dealer Order List</h1>
    <p>Generated on {{ date('d M Y, h:i A') }}</p>
</div>

<div class="meta">
    <span>
        @if($filters['order_number'])
            Order No: {{ $filters['order_number'] }}
        @else
            Order No: All
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
            <th>Order No</th>
            <th>Date</th>
            <th>Dealer Name</th>
            <th>Dealer Phone</th>
            <th>Qty</th>
            <th>Grand Total</th>
            <th>Paid</th>
            <th>Due</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalGrand = 0; $totalPaid = 0; $totalDue = 0;
        @endphp

        @forelse($orders as $i => $order)
            @php
                $totalGrand += $order->grand_total;
                $totalPaid  += $order->paid;
                $totalDue   += $order->due;

                $badgeClass = match($order->status) {
                    'pending'       => 'badge-pending',
                    'order_confirm' => 'badge-confirm',
                    'delivered'     => 'badge-delivered',
                    'cancelled'     => 'badge-cancelled',
                    default         => 'badge-pending',
                };
                $statusLabel = match($order->status) {
                    'pending'       => 'Pending',
                    'order_confirm' => 'Confirmed',
                    'delivered'     => 'Delivered',
                    'cancelled'     => 'Cancelled',
                    default         => ucfirst($order->status),
                };
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $order->order_number }}</strong></td>
                <td>{{ $order->order_date->format('d M Y') }}</td>
                <td>{{ $order->dealer->shop_name ?? ($order->dealer->name ?? 'N/A') }}</td>
                <td>{{ $order->dealer->phone ?? 'N/A' }}</td>
                <td class="text-right">{{ $order->items->sum('qty') }}</td>
                <td class="text-right">৳{{ number_format($order->grand_total, 2) }}</td>
                <td class="text-right">৳{{ number_format($order->paid, 2) }}</td>
                <td class="text-right {{ $order->due > 0 ? 'text-danger' : 'text-success' }}">৳{{ number_format($order->due, 2) }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span></td>
            </tr>
        @empty
            <tr>
                <td colspan="10" style="text-align:center; color:#999; padding: 20px;">No records found.</td>
            </tr>
        @endforelse

        @if($orders->count() > 0)
        <tr class="totals-row">
            <td colspan="6" class="text-right"><strong>TOTALS</strong></td>
            <td class="text-right"><strong>৳{{ number_format($totalGrand, 2) }}</strong></td>
            <td class="text-right"><strong>৳{{ number_format($totalPaid, 2) }}</strong></td>
            <td class="text-right text-danger"><strong>৳{{ number_format($totalDue, 2) }}</strong></td>
            <td></td>
        </tr>
        @endif
    </tbody>
</table>

<div class="footer">
    This report was generated automatically. &copy; {{ date('Y') }} Nozor.
</div>

</body>
</html>
