<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>POS Order Sales Report</title>
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
        <h2>POS Order Sales Report</h2>
        <p>Generated on: {{ date('d M, Y h:i A') }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th class="text-right">Total</th>
                <th class="text-right">Paid</th>
                <th class="text-right">Due</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
                $totalPaid = 0;
                $totalDue = 0;
            @endphp
            @foreach($orders as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ date('d M, Y', strtotime($order->order_date)) }}</td>
                <td>{{ $order->order_number }}</td>
                <td>
                    {{ $order->customer->name ?? 'Walk-in' }}<br>
                    <small>{{ $order->customer->phone ?? '' }}</small>
                </td>
                <td class="text-right">TK {{ number_format($order->total_amount, 2) }}</td>
                <td class="text-right">TK {{ number_format($order->paid_amount, 2) }}</td>
                <td class="text-right">TK {{ number_format($order->due_amount, 2) }}</td>
                <td>{{ ucfirst($order->order_status) }}</td>
            </tr>
            @php
                $totalAmount += $order->total_amount;
                $totalPaid += $order->paid_amount;
                $totalDue += $order->due_amount;
            @endphp
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td class="font-weight-bold">Total Sales:</td>
                <td class="text-right font-weight-bold">TK {{ number_format($totalAmount, 2) }}</td>
            </tr>
            <tr>
                <td>Total Paid:</td>
                <td class="text-right">TK {{ number_format($totalPaid, 2) }}</td>
            </tr>
            <tr>
                <td>Total Due:</td>
                <td class="text-right">TK {{ number_format($totalDue, 2) }}</td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        <p>This is a computer generated report.</p>
    </div>
</body>
</html>
