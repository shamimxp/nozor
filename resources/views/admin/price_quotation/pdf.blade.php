<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4;
            margin: 12mm;
        }
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
        .items-table th,
        .items-table td {
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
        .description-box {
            margin-top: 25px;
            font-weight: bold;
            font-size: 12px;
        }
        .description-box span {
            font-weight: normal;
        }
        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    @php
        $logoFile = public_path('admin/app-assets/images/logo/edited_red_letters.svg');
        $logoSrc = is_file($logoFile) ? ('file://' . $logoFile) : null;
    @endphp

    @if($logoSrc)
        <img src="{{ $logoSrc }}" class="watermark" alt="Watermark">
    @endif

    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo">
                @endif
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

    <div class="invoice-title">{{ $title }}</div>

    <table class="info-table">
        <tr>
            <td class="info-left">
                Price Type : {{ $priceTypeLabel }}
            </td>
            <td class="info-right">
                Date : {{ $quotationDate->format('d M Y') }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 8%;">SL</th>
                <th style="width: 52%; text-align: left;">Product Name</th>
                <th style="width: 15%;">Quantity</th>
                <th style="width: 25%;">Unit Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $index => $item)
                <tr>
                    <td class="text-center"><b>{{ $index + 1 }}</b></td>
                    <td class="text-left">{{ $item['name'] }}</td>
                    <td class="text-center">{{ rtrim(rtrim(number_format($item['quantity'], 2, '.', ''), '0'), '.') }}</td>
                    <td class="text-center">
                        @if($item['final_price'] === null)
                            —
                        @else
                            {{ number_format($item['final_price'], 2) }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="description-box">
        Note : <span>{{ $note !== null && $note !== '' ? $note : '—' }}</span>
    </div>

    <div class="footer-text">
        Generated electronically · not stored in system
    </div>
</body>
</html>
