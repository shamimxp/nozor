<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $order->invoice_no }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 40px;
            color: #5e5873;
            background-color: #fff;
            line-height: 1.5;
        }
        .header {
            width: 100%;
            margin-bottom: 30px;
        }
        .header table { width: 100%; }
        .logo { font-size: 24px; font-weight: bold; color: #7367f0; }
        .invoice-title {
            text-align: right;
            font-weight: bold;
            color: #7367f0;
            font-size: 28px;
            margin: 0;
        }
        .meta-data { text-align: right; }
        .meta-data p { margin: 2px 0; font-size: 13px; }

        .client-info {
            width: 100%;
            margin-bottom: 30px;
        }
        .client-info table { width: 100%; }
        .info-label {
            font-weight: bold;
            color: #b9b9c3;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-size: 11px;
        }
        .info-value { margin: 0; font-size: 14px; }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #f3f2f7;
            padding: 12px 15px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: #5e5873;
            letter-spacing: 0.5px;
        }
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #ebe9f1;
            font-size: 14px;
            vertical-align: middle;
        }
        .category-badge {
            background-color: #f3f2f7;
            color: #82868b;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }

        .summary-row {
            width: 100%;
            margin-top: 20px;
        }
        .summary-row table { width: 100%; }
        .notes-col { width: 60%; vertical-align: top; padding-top: 10px; }
        .totals-col { width: 40%; vertical-align: top; text-align: right; }

        .notes-title {
            font-weight: bold;
            color: #5e5873;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .notes-text {
            color: #b9b9c3;
            font-size: 13px;
        }

        .total-item {
            display: inline-block;
            width: 100%;
            margin-bottom: 8px;
            font-size: 14px;
        }
        .total-label { color: #5e5873; font-weight: bold; text-align: right; width: 60%; display: inline-block; }
        .total-amount { color: #5e5873; text-align: right; width: 35%; display: inline-block; }

        .grand-total-row {
            border-top: 1px solid #ebe9f1;
            margin-top: 10px;
            padding-top: 15px;
            margin-bottom: 15px;
        }
        .grand-total-label { color: #5e5873; font-weight: bold; font-size: 14px; text-align: right; width: 60%; display: inline-block; }
        .grand-total-amount { color: #5e5873; font-weight: bold; font-size: 14px; text-align: right; width: 35%; display: inline-block; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td style="width: 50%;">
                    <div class="logo" style="color: #7367f0; font-size: 32px; font-weight: bold; margin-bottom: 10px;">NOZOR</div>
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <div class="invoice-title">INVOICE</div>
                    <div class="meta-data">
                        <p><strong>#{{ $order->invoice_no }}</strong></p>
                        <p>Order Date: {{ $order->created_at->format('d M Y') }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="client-info">
        <table>
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <div class="info-label">Invoice To:</div>
                    <div class="info-value"><strong>Name: {{ $order->address->name ?? 'N/A' }}</strong></div>
                    <div class="info-value">Address: {{ $order->address->address ?? 'N/A' }}</div>
                    <div class="info-value">Phone: {{ $order->address->phone ?? 'N/A' }}</div>
                </td>
                <td style="width: 50%; vertical-align: top; text-align: right;">
                    <div class="info-label">Payment Information:</div>
                    <div class="info-value">Method: <strong>{{ strtoupper($order->payment_method) }}</strong></div>
                    <div class="info-value">Status: <strong style="color: {{ $order->status == 'delivered' ? '#28c76f' : '#ea5455' }}">{{ $order->status == 'delivered' ? 'PAID' : 'DUE' }}</strong></div>
                </td>
            </tr>
        </table>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 65%;">Product Name</th>
                <th style="width: 5%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Rate</th>
                <th style="width: 15%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    {{ $item->product->name ?? 'Deleted Product' }}
                    @if($item->size || $item->color)
                        <span style="color: #82868b; font-size: 0.9em;">
                            (@if($item->size)Size: {{ $item->size }}@endif
                            @if($item->size && $item->color), @endif
                            @if($item->color)Color: {{ $item->color }}@endif)
                        </span>
                    @endif
                </td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">TK {{ number_format($item->price, 2) }}</td>
                <td style="text-align: right;">TK {{ number_format($item->price * $item->quantity, 2) }} </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-row">
        <table>
            <tr>
                <td class="notes-col">
                    <div class="notes-title">Customer Notes:</div>
                    <div class="notes-text">{{ $order->address->note ?: 'No special notes provided.' }}</div>
                </td>
                <td class="totals-col">
                    <div class="total-item">
                        <span class="total-label">Subtotal:</span>
                        <span class="total-amount">TK {{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="total-item">
                        <span class="total-label">Shipping:</span>
                        <span class="total-amount">TK {{ number_format($order->shipping_charge, 2) }}</span>
                    </div>
                    <div class="total-item">
                        <span class="total-label">Discount:</span>
                        <span class="total-amount">-TK {{ number_format($order->discount, 2) }}</span>
                    </div>

                    <div class="grand-total-row">
                        <span class="grand-total-label">Grand Total:</span>
                        <span class="grand-total-amount">TK {{ number_format($order->total, 2) }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
