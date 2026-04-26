<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>POS Order Profit/Loss Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            color: #7367f0;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-success { color: green; }
        .text-danger { color: red; }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #999;
            text-align: center;
        }
        .summary {
            float: right;
            width: 250px;
        }
        .summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary td {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        .font-weight-bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>POS Order Profit/Loss Report</h2>
        <p>Generated on: {{ date('d M, Y h:i A') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Order ID</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Cost (COGS)</th>
                <th class="text-right">Profit/Loss</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalRevenue = 0;
                $totalCost = 0;
            @endphp
            @foreach($orders as $index => $order)
            @php
                $cost = 0;
                foreach($order->items as $item) {
                    $cost += ($item->product->cost_price ?? 0) * $item->quantity;
                }
                $profit = $order->total_amount - $cost;
                $totalRevenue += $order->total_amount;
                $totalCost += $cost;
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ date('d M, Y', strtotime($order->order_date)) }}</td>
                <td>{{ $order->order_number }}</td>
                <td class="text-right">TK {{ number_format($order->total_amount, 2) }}</td>
                <td class="text-right">TK {{ number_format($cost, 2) }}</td>
                <td class="text-right {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">TK {{ number_format($profit, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td class="font-weight-bold">Total Revenue:</td>
                <td class="text-right font-weight-bold">TK {{ number_format($totalRevenue, 2) }}</td>
            </tr>
            <tr>
                <td>Total Cost:</td>
                <td class="text-right">TK {{ number_format($totalCost, 2) }}</td>
            </tr>
            <tr>
                <td class="font-weight-bold">Total Profit:</td>
                <td class="text-right font-weight-bold {{ ($totalRevenue - $totalCost) >= 0 ? 'text-success' : 'text-danger' }}">TK {{ number_format($totalRevenue - $totalCost, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        <p>This is a computer generated report.</p>
    </div>
</body>
</html>
