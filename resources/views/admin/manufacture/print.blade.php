<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Manufacture Order - {{ $manufacture->invoice_no }}</title>
    <style>
        * {
            box-sizing: border-box;
        }
        @page {
            size: A4 portrait;
            margin: 10mm 8mm 12mm;
        }
        html, body {
            margin: 0;
            padding: 0;
            background: #fff;
        }
        body {
            font-family: Arial, sans-serif;
            color: #000;
        }
        .print-container {
            width: 194mm;
            height: 270mm;
            margin: 0 auto;
            background: #fff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .header {
            position: relative;
            text-align: center;
            flex: 0 0 auto;
            margin-bottom: 4px;
            padding: 2px 0 2px 120px;
        }
        .logo-container {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
        }
        .logo {
            width: 110px;
            height: auto;
        }
        .company-info {
            width: 100%;
        }
        .company-name {
            font-size: 22px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 2px;
        }
        .company-address, .company-phone {
            font-size: 12px;
            margin: 1px 0;
        }
        hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 6px 0;
            flex: 0 0 auto;
        }
        .details-section {
            text-align: center;
            font-size: 13px;
            line-height: 1.35;
            margin-bottom: 4px;
            flex: 0 0 auto;
        }
        .details-section strong {
            font-weight: bold;
        }
        .barcode-wrapper {
            margin: 4px 0;
            text-align: center;
        }
        .barcode {
            height: 28px;
        }
        .barcode-text {
            font-size: 11px;
            display: block;
            margin-top: 1px;
        }
        .qty-text {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .design-text {
            text-align: right;
            font-size: 13px;
            font-weight: bold;
            margin: 2px 0;
            flex: 0 0 auto;
        }
        .manufacture-image-container {
            position: relative;
            flex: 1 1 auto;
            min-height: 0;
            margin: 4px auto;
            border: 1px solid #666;
            width: 100%;
        }
        .manufacture-image-container img {
            position: absolute;
            top: 6px;
            right: 6px;
            bottom: 6px;
            left: 6px;
            width: calc(100% - 12px);
            height: calc(100% - 12px);
            object-fit: contain;
        }
        .footer-note {
            font-weight: bold;
            font-size: 13px;
            margin-top: 2px;
            flex: 0 0 auto;
        }
        .developed-by {
            text-align: center;
            font-size: 11px;
            margin-top: 6px;
            flex: 0 0 auto;
        }
        .developed-by span {
            color: blue;
            font-weight: bold;
        }

        @media screen {
            body {
                background: #e9e9e9;
                padding: 12px 0;
            }
            .print-container {
                box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
            }
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .print-container {
                width: auto;
                height: 270mm;
                max-height: 270mm;
                box-shadow: none;
                page-break-inside: avoid;
                break-inside: avoid;
                page-break-after: avoid;
                break-after: avoid;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="print-container">
        @php
            // Extract type
            $part_type = '';
            if ($manufacture->body_total > 0 && $manufacture->finishing_total > 0) {
                $part_type = 'body-part, finishing-part';
            } elseif ($manufacture->body_total > 0) {
                $part_type = 'body-part';
            } else {
                $part_type = 'finishing-part';
            }

            // Extract image
            $recipe = $manufacture->product ? $manufacture->product->recipe : null;
            $imgs = $recipe && !empty($recipe->manufacture_images) ? $recipe->manufacture_images : [];
            $manufacture_image = !empty($imgs) && isset($imgs[0]) ? asset($imgs[0]) : asset('images/no-image.png');
        @endphp

        <!-- Header -->
        <div class="header">
            <div class="logo-container">
                @if(isset($setting) && $setting->logo)
                    <img src="{{ asset('images/logo/'.$setting->logo) }}" class="logo" alt="Logo" onerror="this.src='{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}'">
                @else
                    <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" class="logo" alt="Logo">
                @endif
            </div>
            <div class="company-info">
                <h1 class="company-name">{{ $setting->name ?? 'Wood Machinery' }}</h1>
                <p class="company-address">{{ $setting->address ?? 'Purbo Padardiya (Shahabuddin Road Shonglogno) Shatarkul Road, Badda, Dhaka-1212' }}</p>
                <p class="company-phone">Phone: {{ $setting->phone ?? '01674-088383' }}</p>
            </div>
        </div>
        
        <hr>

        <!-- Details -->
        <div class="details-section">
            <div><strong>Name:</strong> {{ $manufacture->product->name ?? 'N/A' }}</div>
            <div><strong>Manufacturing Part:</strong> {{ $part_type }}</div>
            <div><strong>Assaign By:</strong> {{ $manufacture->worker->name ?? 'N/A' }} => {{ $manufacture->worker->phone ?? 'N/A' }}</div>
            
            <div class="barcode-wrapper">
                <img class="barcode" src="https://barcode.tec-it.com/barcode.ashx?data={{ $manufacture->id }}&code=Code128&dpi=96" alt="Barcode">
                <!-- <span class="barcode-text">{{ $manufacture->id }}</span> -->
            </div>

            <div class="qty-text">Manufacture Quantity: {{ $manufacture->manufacture_qty }}</div>
        </div>

        <div class="design-text">
            Design {{ $manufacture->product->id ?? '' }}
        </div>

        <!-- Image -->
        <div class="manufacture-image-container">
            <img src="{{ $manufacture_image }}" alt="Manufacture Image">
        </div>

        <hr>

        <!-- Footer -->
        <div class="footer-note">
            Note:
        </div>

        <div class="developed-by">
            Developed By : <span>Wood Machinery</span>
        </div>
    </div>
</body>
</html>
