<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $order->invoice_no }}</title>
    <style>
        body {
            font-family: serif;
            margin: 0;
            padding: 30px;
            color: #333;
            font-size: 13px;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: -1;
            width: 400px;
        }
        .header-table {
            width: 100%;
            margin-bottom: 5px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-logo {
            width: 25%;
            text-align: left;
        }
        .header-logo img {
            width: 140px;
        }
        .header-content {
            width: 50%;
            text-align: center;
        }
        .header-empty {
            width: 25%;
        }
        .header-title {
            font-size: 20px;
            font-weight: bold;
            line-height: 1.1;
            margin-bottom: 5px;
        }
        .header-address {
            font-size: 13px;
            line-height: 1.3;
        }
        .divider {
            border-bottom: 2px solid #555;
            margin: 10px 0;
        }
        .invoice-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .info-left {
            width: 60%;
        }
        .info-right {
            width: 40%;
            text-align: right;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 12px;
        }
        .items-table th {
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .bottom-table {
            width: 100%;
        }
        .bottom-table td {
            vertical-align: top;
        }
        .in-words-col {
            width: 60%;
            font-size: 12px;
            font-weight: bold;
        }
        .in-words-col span {
            font-weight: normal;
        }
        .totals-col {
            width: 40%;
        }
        .totals-table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 2px 0;
            text-align: right;
        }
        .totals-table td:first-child {
            width: 60%;
            padding-right: 10px;
        }
        .totals-table td:last-child {
            width: 40%;
        }
        .totals-table .border-bottom td {
            border-bottom: 1px solid #000;
        }
        .description-box {
            margin-top: 25px;
            font-weight: bold;
            font-size: 12px;
        }
        .description-box span {
            font-weight: normal;
        }
        .signature-section {
            width: 100%;
            margin-top: 70px;
        }
        .signature-section table {
            width: 100%;
        }
        .signature-section td {
            width: 50%;
        }
        .sign-line {
            border-top: 1px solid #000;
            width: 150px;
            text-align: center;
            font-weight: bold;
            padding-top: 5px;
            font-size: 12px;
        }
        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            font-weight: bold;
        }
        .item-meta {
            font-size: 10px;
            font-weight: normal;
        }
    </style>
</head>
<body>

    <img src="{{ public_path('admin/app-assets/images/logo/edited_red_letters.svg') }}" class="watermark" alt="Watermark">

    @php
        if (!function_exists('getAmountInWords')) {
            function getAmountInWords($amount) {
                $f = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
                $amt = explode('.', number_format($amount, 2, '.', ''));
                $taka = (int) $amt[0];
                $poysa = (int) $amt[1];
                $str = $f->format($taka) . ' taka';
                if ($poysa > 0) {
                    $str .= ' and ' . $f->format($poysa) . ' poysa';
                }
                return ucwords($str);
            }
        }

        $isPaid = $order->status === 'delivered';
        $received = $isPaid ? (float) $order->total : 0;
        $due = $isPaid ? 0 : (float) $order->total;
    @endphp

    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ public_path('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="Logo">
            </td>
            <td class="header-content">
                <div class="header-title">Wood Machinery and Hardware</div>
                <div class="header-address">
                    Purbo Padardiya (Shahabuddin Road Shonglogno) Shatarkul Road, Badda, Dhaka-1212<br>
                    Phone Number 01674-088383<br>
                    Email: info@woodmachinery.com.bd
                </div>
            </td>
            <td class="header-empty"></td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="invoice-title">Web Invoice</div>

    <table class="info-table">
        <tr>
            <td class="info-left">
                Invoice No : {{ $order->invoice_no }}<br>
                Customer Name : {{ $order->address->name ?? 'N/A' }}<br>
                Customer Address : {{ $order->address->address ?? 'N/A' }}<br>
                Customer Phone No : {{ $order->address->phone ?? 'N/A' }}
            </td>
            <td class="info-right">
                Order Date : {{ $order->created_at ? $order->created_at->format('d M Y') : date('d M Y') }}<br>
                Payment Method : {{ strtoupper($order->payment_method) }}<br>
                Payment Status : {{ $isPaid ? 'Paid' : 'Due' }}<br>
                Order Status : {{ ucfirst(str_replace('_', ' ', (string) $order->status)) }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">SL</th>
                <th style="width: 50%; text-align: left;">Product Name</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 17%;">Unit Price</th>
                <th style="width: 18%;">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
            <tr>
                <td class="text-center"><b>{{ $index + 1 }}</b></td>
                <td>
                    {{ $item->product->name ?? 'Deleted Product' }}
                    @if($item->size || $item->color)
                        <div class="item-meta">
                            @if($item->size)Size: {{ $item->size }}@endif
                            @if($item->size && $item->color) | @endif
                            @if($item->color)Color: {{ $item->color }}@endif
                        </div>
                    @endif
                </td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-center">{{ number_format((float) $item->price, 2) }}</td>
                <td class="text-center">{{ number_format((float) $item->price * (float) $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="bottom-table">
        <tr>
            <td class="in-words-col">
                Total In Words : <span>{{ getAmountInWords($order->total) }}</span>
            </td>
            <td class="totals-col">
                <table class="totals-table">
                    <tr>
                        <td>Subtotal :</td>
                        <td>{{ number_format((float) $order->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Discount :</td>
                        <td>{{ number_format((float) $order->discount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Shipping Charge :</td>
                        <td>{{ number_format((float) $order->shipping_charge, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Amount :</td>
                        <td>{{ number_format((float) $order->total, 2) }}</td>
                    </tr>
                    <tr class="border-bottom">
                        <td>Received Amount :</td>
                        <td>{{ $received > 0 ? number_format($received, 2) : '0.0' }}</td>
                    </tr>
                    <tr>
                        <td>Due Amount:</td>
                        <td>{{ $due > 0 ? number_format($due, 2) : '0.0' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="description-box">
        Description: <span>{{ filled($order->address->note ?? null) ? $order->address->note : 'N/A' }}</span>
    </div>

    <div class="signature-section">
        <table>
            <tr>
                <td class="text-left">
                    <div class="sign-line" style="float: left;">Receiver Signature</div>
                </td>
                <td class="text-right">
                    <div class="sign-line" style="float: right;">Manager Signature</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-text">
       Committed to Your Satisfaction
    </div>

</body>
</html>
