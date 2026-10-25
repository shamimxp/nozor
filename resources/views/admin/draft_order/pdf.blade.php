<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Draft Order - {{ $draftOrder->draft_number }}</title>
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
            opacity: 0.08;
            z-index: -1;
            width: 450px;
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
            font-size: 17px;
            font-weight: bold;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .draft-badge {
            display: inline-block;
            background: #fef3c7;
            color: #b45309;
            border: 1px solid #f59e0b;
            padding: 2px 10px;
            font-size: 12px;
            border-radius: 4px;
            margin-left: 6px;
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
            line-height: 1.5;
        }
        .info-right {
            width: 40%;
            text-align: right;
            line-height: 1.5;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            font-size: 12px;
        }
        .items-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .bottom-section {
            width: 100%;
            margin-top: 5px;
        }
        .bottom-table {
            width: 100%;
        }
        .bottom-table td {
            vertical-align: top;
        }
        .in-words-col {
            width: 55%;
            font-size: 12px;
            font-weight: bold;
        }
        .in-words-col span {
            font-weight: normal;
        }
        .totals-col {
            width: 45%;
        }
        .totals-table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 3px 0;
            text-align: right;
        }
        .totals-table td:first-child {
            width: 60%;
            padding-right: 10px;
        }
        .totals-table td:last-child {
            width: 40%;
        }
        .totals-table .border-top td {
            border-top: 1px solid #000;
            font-weight: bold;
        }
        .description-box {
            margin-top: 20px;
            font-weight: bold;
            font-size: 12px;
            line-height: 1.4;
        }
        .description-box span {
            font-weight: normal;
        }
        .signature-section {
            width: 100%;
            margin-top: 60px;
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
            margin-top: 25px;
            font-size: 11px;
            font-weight: bold;
            color: #666;
        }
    </style>
</head>
<body>

    <img src="{{ public_path('admin/app-assets/images/logo/edited_red_letters.svg') }}" class="watermark" alt="Watermark">

    @php
    function getDraftAmountInWords($amount) {
        if (!class_exists('\NumberFormatter')) {
            return number_format($amount, 2) . ' Taka only';
        }
        $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
        $amt = explode('.', number_format($amount, 2, '.', ''));
        $taka = (int)$amt[0];
        $poysa = (int)($amt[1] ?? 0);
        
        $str = $f->format($taka) . ' taka';
        if ($poysa > 0) {
            $str .= ' and ' . $f->format($poysa) . ' poysa';
        }
        return ucwords($str);
    }
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

    <div class="invoice-title">
        DRAFT ORDER / PROFORMA INVOICE
    </div>

    <table class="info-table">
        <tr>
            <td class="info-left">
                <strong>Draft No :</strong> {{ $draftOrder->draft_number }}<br>
                @if($draftOrder->orderRequest)
                    <strong>Order Request Ref :</strong> {{ $draftOrder->orderRequest->request_number ?? ('#' . $draftOrder->orderRequest->id) }}<br>
                @endif
                <strong>Dealer Name :</strong> {{ $draftOrder->dealer->name ?? 'N/A' }} ({{ $draftOrder->dealer->shop_name ?? 'N/A' }})<br>
                <strong>Dealer Address :</strong> {{ $draftOrder->dealer->address ?? 'N/A' }}<br>
                <strong>Dealer Phone :</strong> {{ $draftOrder->dealer->phone ?? 'N/A' }}
            </td>
            <td class="info-right">
                <strong>Date :</strong> {{ $draftOrder->created_at->format('d M Y') }}<br>
                <strong>Status :</strong> {{ strtoupper($draftOrder->status) }}<br>
                @if($draftOrder->creator)
                    <strong>Prepared By :</strong> {{ $draftOrder->creator->name }}<br>
                @endif
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">SL</th>
                <th style="width: 50%; text-align: left;">Product Name</th>
                <th style="width: 15%;">Unit Price</th>
                <th style="width: 15%;">Quantity</th>
                <th style="width: 15%;">Total (BDT)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($draftOrder->items as $index => $item)
            <tr>
                <td class="text-center"><b>{{ $index + 1 }}</b></td>
                <td>{{ $item->product->name ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="text-right">{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="bottom-table">
        <tr>
            <td class="in-words-col">
                Total In Words : <span>{{ getDraftAmountInWords($draftOrder->grand_total) }}</span>
            </td>
            <td class="totals-col">
                <table class="totals-table">
                    <tr>
                        <td>Sub Total :</td>
                        <td>{{ number_format($draftOrder->sub_total, 2) }}</td>
                    </tr>
                    @if($draftOrder->discount > 0)
                    <tr>
                        <td>Discount :</td>
                        <td>- {{ number_format($draftOrder->discount, 2) }}</td>
                    </tr>
                    @endif
                    @if($draftOrder->carrying_charge > 0)
                    <tr>
                        <td>Carrying Charge :</td>
                        <td>+ {{ number_format($draftOrder->carrying_charge, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="border-top">
                        <td>Grand Total :</td>
                        <td>{{ number_format($draftOrder->grand_total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if($draftOrder->note || $draftOrder->admin_notes)
    <div class="description-box">
        @if($draftOrder->note)
            <div><strong>Note:</strong> <span>{{ $draftOrder->note }}</span></div>
        @endif
        @if($draftOrder->admin_notes)
            <div><strong>Admin Note:</strong> <span>{{ $draftOrder->admin_notes }}</span></div>
        @endif
    </div>
    @endif

    <div class="signature-section">
        <table>
            <tr>
                <td class="text-left">
                    <div class="sign-line" style="float: left;">Prepared By</div>
                </td>
                <td class="text-right">
                    <div class="sign-line" style="float: right;">Authorized Signature</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-text">
        * This is a computer-generated Draft Order / Proforma Invoice.
    </div>

</body>
</html>
