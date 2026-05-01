<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Orders Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; }

        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #7367f0; padding-bottom: 10px; }
        .header h1 { font-size: 18px; color: #7367f0; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #666; }

        .meta { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 9px; color: #555; }
        .meta span { background: #f3f2ff; padding: 3px 8px; border-radius: 4px; border: 1px solid #e0deff; }

        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        thead tr { background: #7367f0; color: #fff; }
        thead th { padding: 7px 5px; text-align: left; font-size: 9px; font-weight: bold; }
        tbody tr:nth-child(even) { background: #f8f7ff; }
        tbody td { padding: 6px 5px; border-bottom: 1px solid #ebe9f1; font-size: 9px; vertical-align: middle; }

        .badge { display: inline-block; padding: 2px 7px; border-radius: 10px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-pending  { background: #fff3cd; color: #856404; }
        .badge-confirm  { background: #cff4fc; color: #0a6eaf; }
        .badge-received { background: #d1e7dd; color: #145a32; }

        .text-right  { text-align: right; }
        .text-danger { color: #dc3545; }
        .text-success{ color: #28c76f; }

        .totals-row { background: #ece9ff !important; font-weight: bold; }
        .totals-row td { border-top: 2px solid #7367f0; }

        .footer { margin-top: 20px; font-size: 8px; color: #aaa; text-align: center; border-top: 1px solid #eee; padding-top: 8px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Purchase Orders Report</h1>
    <p>Generated on {{ date('d M Y, h:i A') }}</p>
</div>

<div class="meta">
    <span>
        Purchase No:
        {{ $filters['purchase_number'] ?: 'All' }}
    </span>
    <span>
        Order No:
        {{ $filters['order_number'] ?: 'All' }}
    </span>
    <span>
        Vendor Phone:
        {{ $filters['vendor_phone'] ?: 'All' }}
    </span>
    <span>
        @if(!empty($filters['start_date']) && !empty($filters['end_date']))
            Period: {{ \Carbon\Carbon::parse($filters['start_date'])->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($filters['end_date'])->format('d M Y') }}
        @else
            Period: All Time
        @endif
    </span>
    <span>
        Status:
        {{ $filters['status'] ? strtoupper($filters['status']) : 'All' }}
    </span>
    <span>Total Records: {{ $purchases->count() }}</span>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Purchase No</th>
            <th>Order No</th>
            <th>Style No</th>
            <th>Received Date</th>
            <th>Vendor</th>
            <th>Phone</th>
            <th>Sub Total</th>
            <th>Carrying</th>
            <th>Grand Total</th>
            <th>Paid</th>
            <th>Due</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalGrand = 0;
            $totalPaid  = 0;
            $totalDue   = 0;
        @endphp

        @forelse($purchases as $i => $p)
            @php
                $totalGrand += $p->grand_total;
                $totalPaid  += $p->paid_amount;
                $totalDue   += $p->due_amount;

                $badgeClass = match($p->status) {
                    'pending'  => 'badge-pending',
                    'confirm'  => 'badge-confirm',
                    'received' => 'badge-received',
                    default    => 'badge-pending',
                };
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $p->purchase_number }}</strong></td>
                <td>{{ $p->customOrder->order_number ?? 'N/A' }}</td>
                <td>{{ $p->style_number }}</td>
                <td>{{ $p->received_date ? $p->received_date->format('d M Y') : '-' }}</td>
                <td>{{ $p->vendor->name ?? 'N/A' }}</td>
                <td>{{ $p->vendor->phone ?? '-' }}</td>
                <td class="text-right">৳{{ number_format($p->sub_total, 2) }}</td>
                <td class="text-right">৳{{ number_format($p->carrying_charge, 2) }}</td>
                <td class="text-right">৳{{ number_format($p->grand_total, 2) }}</td>
                <td class="text-right">৳{{ number_format($p->paid_amount, 2) }}</td>
                <td class="text-right {{ $p->due_amount > 0 ? 'text-danger' : 'text-success' }}">৳{{ number_format($p->due_amount, 2) }}</td>
                <td><span class="badge {{ $badgeClass }}">{{ strtoupper($p->status) }}</span></td>
            </tr>
        @empty
            <tr>
                <td colspan="13" style="text-align:center; color:#999; padding:20px;">No records found.</td>
            </tr>
        @endforelse

        @if($purchases->count() > 0)
        <tr class="totals-row">
            <td colspan="9" class="text-right"><strong>TOTALS</strong></td>
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
