<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>POS Invoice #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
        }
        .header td {
            vertical-align: top;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #001f3f;
        }
        .company-info {
            text-align: right;
        }
        .invoice-info {
            margin-bottom: 30px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            width: 50%;
            vertical-align: top;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .items-table th {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            text-align: left;
            padding: 10px;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .totals-table {
            width: 100%;
            margin-top: 30px;
        }
        .totals-table td {
            padding: 5px;
        }
        .totals-label {
            text-align: right;
            font-weight: bold;
            width: 80%;
        }
        .totals-value {
            text-align: right;
            width: 20%;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 11px;
            color: #777;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .bg-success { background: #d4edda; color: #155724; }
        .bg-danger { background: #f8d7da; color: #721c24; }
        .bg-warning { background: #fff3cd; color: #856404; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table class="header">
            <tr>
                <td class="logo">NOZOR POS</td>
                <td class="company-info">
                    <strong>NOZOR E-commerce</strong><br>
                    Phone: +880123456789<br>
                    Email: support@nozor.com
                </td>
            </tr>
        </table>

        <div class="invoice-info">
            <table class="info-table">
                <tr>
                    <td>
                        <strong>Customer Details:</strong><br>
                        @if($order->customer)
                            Name: {{ $order->customer->name }}<br>
                            Phone: {{ $order->customer->phone }}<br>
                            Email: {{ $order->customer->email }}
                        @else
                            Walk-in Customer
                        @endif
                    </td>
                    <td style="text-align: right;">
                        <strong>Invoice Info:</strong><br>
                        Order ID: #{{ $order->order_number }}<br>
                        Date: {{ date('d M, Y', strtotime($order->order_date)) }}<br>
                        Method: {{ ucfirst($order->payment_method) }}<br>
                        Status: <span class="status-badge bg-{{ $order->order_status == 'completed' ? 'success' : ($order->order_status == 'cancelled' ? 'danger' : 'warning') }}">{{ $order->order_status }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Product Description</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Discount</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td>৳{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>৳{{ number_format($item->discount, 2) }}</td>
                    <td style="text-align: right;">৳{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td class="totals-label">Subtotal:</td>
                <td class="totals-value">৳{{ number_format($order->payable_amount + $order->discount_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="totals-label">Discount:</td>
                <td class="totals-value">-৳{{ number_format($order->discount_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="totals-label" style="font-size: 16px;">Total Payable:</td>
                <td class="totals-value" style="font-size: 16px;"><strong>৳{{ number_format($order->payable_amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td class="totals-label">Paid Amount:</td>
                <td class="totals-value">৳{{ number_format($order->paid_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="totals-label" style="color: #dc3545;">Due Amount:</td>
                <td class="totals-value" style="color: #dc3545;">৳{{ number_format($order->due_amount, 2) }}</td>
            </tr>
        </table>

        @if($order->note)
        <div style="margin-top: 30px;">
            <strong>Note:</strong><br>
            {{ $order->note }}
        </div>
        @endif

        <div class="footer">
            THANK YOU FOR YOUR BUSINESS!<br>
            Generated on: {{ date('d M, Y H:i A') }}
        </div>
    </div>
</body>
</html>
