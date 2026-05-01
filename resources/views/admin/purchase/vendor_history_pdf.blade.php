<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vendor History & Due Summary</title>
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
    <h1>Vendor History & Due Summary</h1>
    <p>Generated on {{ date('d M Y, h:i A') }}</p>
</div>

<div class="meta">
    <span>Total Vendors: {{ count($data) }}</span>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Vendor Name</th>
            <th>Company</th>
            <th>Phone</th>
            <th class="text-right">Total Purchased</th>
            <th class="text-right">Total Paid</th>
            <th class="text-right">Total Due</th>
        </tr>
    </thead>
    <tbody>
        @php
            $sumPurchased = 0;
            $sumPaid = 0;
            $sumDue = 0;
        @endphp

        @forelse($data as $i => $row)
            @php
                $sumPurchased += $row['total_purchased'];
                $sumPaid += $row['total_paid'];
                $sumDue += $row['total_due'];
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $row['name'] }}</strong></td>
                <td>{{ $row['company'] ?? 'N/A' }}</td>
                <td>{{ $row['phone'] ?? 'N/A' }}</td>
                <td class="text-right">৳{{ number_format($row['total_purchased'], 2) }}</td>
                <td class="text-right">৳{{ number_format($row['total_paid'], 2) }}</td>
                <td class="text-right {{ $row['total_due'] > 0 ? 'text-danger' : 'text-success' }}">৳{{ number_format($row['total_due'], 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" style="text-align:center; color:#999; padding:20px;">No vendors found.</td>
            </tr>
        @endforelse

        @if(count($data) > 0)
        <tr class="totals-row">
            <td colspan="4" class="text-right"><strong>TOTALS</strong></td>
            <td class="text-right"><strong>৳{{ number_format($sumPurchased, 2) }}</strong></td>
            <td class="text-right"><strong>৳{{ number_format($sumPaid, 2) }}</strong></td>
            <td class="text-right text-danger"><strong>৳{{ number_format($sumDue, 2) }}</strong></td>
        </tr>
        @endif
    </tbody>
</table>

<div class="footer">
    This report was generated automatically. &copy; {{ date('Y') }} Nozor.
</div>

</body>
</html>
