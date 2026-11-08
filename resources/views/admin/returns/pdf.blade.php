<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Return Slip - {{ $return->return_no }}</title>
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
        $channelLabel = match (strtolower((string) $return->channel)) {
            'dealer' => 'Dealer Order',
            'pos' => 'POS Sale',
            'web' => 'Web Order',
            default => ucfirst((string) $return->channel),
        };
        $refundLabel = strtolower((string) $return->refund_type) === 'wallet' ? 'Dealer Wallet' : 'Cash';
        $invoices = $return->items->pluck('order_number')->filter()->unique()->values();
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

    <div class="invoice-title">Return Slip</div>

    <table class="info-table">
        <tr>
            <td class="info-left">
                Return No : {{ $return->return_no }}<br>
                Customer Name : {{ $return->customer_name ?: 'General Party' }}<br>
                Customer Phone No : {{ $return->customer_phone ?: 'N/A' }}<br>
                Channel : {{ $channelLabel }}<br>
                Invoice No : {{ $invoices->isNotEmpty() ? $invoices->implode(', ') : 'N/A' }}
            </td>
            <td class="info-right">
                Date : {{ $return->return_date ? \Carbon\Carbon::parse($return->return_date)->format('d M Y') : date('d M Y') }}<br>
                Status : {{ ucfirst((string) $return->status) }}<br>
                Refund Method : {{ $refundLabel }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">SL</th>
                <th style="width: 35%; text-align: left;">Product Name</th>
                <th style="width: 18%;">Invoice</th>
                <th style="width: 10%;">Delivered</th>
                <th style="width: 10%;">Return Qty</th>
                <th style="width: 11%;">Unit Price</th>
                <th style="width: 11%;">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($return->items as $index => $item)
            <tr>
                <td class="text-center"><b>{{ $index + 1 }}</b></td>
                <td>
                    {{ $item->product_name }}
                    <div class="item-meta">
                        @if(!empty($item->product_code) && $item->product_code !== '-' && $item->product_code !== 'null')
                            Code: {{ $item->product_code }} |
                        @endif
                        Condition: {{ ucfirst($item->condition ?: 'good') }} | Restock: {{ $item->restock ? 'Yes' : 'No' }}
                        @if(!empty($item->item_note))
                            | Note: {{ $item->item_note }}
                        @endif
                    </div>
                </td>
                <td class="text-center">{{ $item->order_number }}</td>
                <td class="text-center">{{ rtrim(rtrim(number_format((float) $item->ordered_qty, 2, '.', ''), '0'), '.') }}</td>
                <td class="text-center">{{ rtrim(rtrim(number_format((float) $item->return_qty, 2, '.', ''), '0'), '.') }}</td>
                <td class="text-center">{{ number_format((float) $item->unit_price, 2) }}</td>
                <td class="text-center">{{ number_format((float) $item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="bottom-table">
        <tr>
            <td class="in-words-col">
                Refund In Words : <span>{{ $amountInWords }}</span>
            </td>
            <td class="totals-col">
                <table class="totals-table">
                    <tr>
                        <td>Subtotal :</td>
                        <td>{{ number_format((float) $return->subtotal, 2) }}</td>
                    </tr>
                    <tr class="border-bottom">
                        <td>Deduction :</td>
                        <td>{{ number_format((float) $return->deduction_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Net Refund :</td>
                        <td>{{ number_format((float) $return->refund_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="description-box">
        Description: <span>{{ $return->return_note ?: 'N/A' }}</span>
        @if(!empty($return->admin_review_note))
            <br>Review Note: <span>{{ $return->admin_review_note }}</span>
        @endif
        @if(strtolower((string) $return->status) === 'rejected' && !empty($return->reject_reason))
            <br>Reject Reason: <span>{{ $return->reject_reason }}</span>
        @endif
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
