<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Return Order List</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }

        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #4361ee; padding-bottom: 10px; }
        .header h1 { font-size: 18px; color: #4361ee; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #666; }

        .meta { margin-bottom: 12px; font-size: 9px; color: #555; }
        .meta span { background: #f1f3ff; padding: 3px 8px; border-radius: 4px; border: 1px solid #dde1f7; margin-right: 6px; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #4361ee; color: #fff; }
        thead th { padding: 7px 6px; text-align: left; font-size: 9px; font-weight: bold; }
        tbody tr:nth-child(even) { background: #f6f7ff; }
        tbody td { padding: 6px 6px; border-bottom: 1px solid #e6e8f7; font-size: 9px; vertical-align: middle; }

        .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-pending   { background: #fff3cd; color: #856404; }
        .badge-approved  { background: #d1e7dd; color: #145a32; }
        .badge-rejected  { background: #f8d7da; color: #842029; }
        .badge-cancelled { background: #eceff1; color: #455a64; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .totals-row { background: #eef0ff !important; font-weight: bold; }
        .totals-row td { border-top: 2px solid #4361ee; }

        .footer { margin-top: 20px; font-size: 8px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 8px; }
    </style>
</head>
<body>

@php
    $channelText = match ($filters['channel'] ?? 'all') {
        'dealer' => 'Dealer Order',
        'pos' => 'POS Sale',
        'web' => 'Web Order',
        default => 'All',
    };
    $statusText = ($filters['status'] ?? 'all') === 'all' ? 'All' : ucfirst($filters['status']);
    $refundText = match ($filters['refund_type'] ?? 'all') {
        'wallet' => 'Dealer Wallet',
        'cash' => 'Cash',
        default => 'All',
    };
    if (!empty($filters['from_date']) || !empty($filters['to_date'])) {
        $from = !empty($filters['from_date']) ? \Carbon\Carbon::parse($filters['from_date'])->format('d M Y') : 'Start';
        $to = !empty($filters['to_date']) ? \Carbon\Carbon::parse($filters['to_date'])->format('d M Y') : 'Today';
        $periodText = $from . ' - ' . $to;
    } else {
        $periodText = 'All Time';
    }
    $totalQty = 0;
    $totalRefund = 0;
@endphp

<div class="header">
    <h1>Return Order List</h1>
    <p>Generated on {{ date('d M Y, h:i A') }}</p>
</div>

<div class="meta">
    <span>Channel: {{ $channelText }}</span>
    <span>Status: {{ $statusText }}</span>
    <span>Refund: {{ $refundText }}</span>
    <span>Period: {{ $periodText }}</span>
    <span>Total Returns: {{ $returns->count() }}</span>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Return No</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>Channel</th>
            <th>Invoices</th>
            <th class="text-center">Qty</th>
            <th class="text-right">Refund</th>
            <th>Type</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($returns as $i => $row)
            @php
                $totalQty += (float) $row->total_qty;
                $totalRefund += (float) $row->refund_amount;
                $invoices = $row->items->pluck('order_number')->filter()->unique()->implode(', ');
                $channel = match (strtolower((string) $row->channel)) {
                    'dealer' => 'Dealer',
                    'pos' => 'POS',
                    'web' => 'Web',
                    default => ucfirst((string) $row->channel),
                };
                $badgeClass = match (strtolower((string) $row->status)) {
                    'approved' => 'badge-approved',
                    'rejected' => 'badge-rejected',
                    'cancelled' => 'badge-cancelled',
                    default => 'badge-pending',
                };
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $row->return_no }}</strong></td>
                <td>{{ $row->return_date ? \Carbon\Carbon::parse($row->return_date)->format('d M Y') : '-' }}</td>
                <td>{{ $row->customer_name ?: 'General Party' }}</td>
                <td>{{ $row->customer_phone ?: 'N/A' }}</td>
                <td>{{ $channel }}</td>
                <td>{{ $invoices !== '' ? $invoices : '-' }}</td>
                <td class="text-center">{{ rtrim(rtrim(number_format((float) $row->total_qty, 2, '.', ''), '0'), '.') }}</td>
                <td class="text-right">৳{{ number_format((float) $row->refund_amount, 2) }}</td>
                <td>{{ strtolower((string) $row->refund_type) === 'wallet' ? 'Wallet' : 'Cash' }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ ucfirst((string) $row->status) }}</span></td>
            </tr>
        @empty
            <tr>
                <td colspan="11" style="text-align:center; color:#999; padding: 20px;">No records found.</td>
            </tr>
        @endforelse

        @if($returns->count() > 0)
        <tr class="totals-row">
            <td colspan="7" class="text-right"><strong>TOTALS</strong></td>
            <td class="text-center"><strong>{{ rtrim(rtrim(number_format($totalQty, 2, '.', ''), '0'), '.') }}</strong></td>
            <td class="text-right"><strong>৳{{ number_format($totalRefund, 2) }}</strong></td>
            <td colspan="2"></td>
        </tr>
        @endif
    </tbody>
</table>

<div class="footer">
    This report was generated automatically. &copy; {{ date('Y') }} Wood-Machinery.
</div>

</body>
</html>
