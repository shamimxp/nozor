<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Manufacture Order - {{ $manufacture->invoice_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #000;
        }
        .print-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
        }
        .header {
            position: relative;
            text-align: center;
            margin-bottom: 10px;
            padding: 10px 0;
        }
        .logo-container {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
        }
        .logo {
            width: 140px;
            height: auto;
        }
        .company-info {
            width: 100%;
        }
        .company-name {
            font-size: 28px;
            font-weight: normal;
            margin: 0;
            margin-bottom: 5px;
        }
        .company-address, .company-phone {
            font-size: 14px;
            margin: 2px 0;
        }
        hr {
            border: 0;
            border-top: 1px solid #ccc;
            margin: 15px 0;
        }
        .details-section {
            text-align: center;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        .details-section strong {
            font-weight: bold;
        }
        .barcode-wrapper {
            margin: 15px 0;
            text-align: center;
        }
        .barcode {
            height: 40px;
        }
        .barcode-text {
            font-size: 12px;
            display: block;
            margin-top: 2px;
        }
        .qty-text {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .design-text {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .manufacture-image-container {
            text-align: center;
            margin: 20px auto;
            border: 1px solid #666;
            padding: 10px;
            width: 90%;
            max-width: 700px;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .manufacture-image-container img {
            max-width: 100%;
            max-height: 600px;
            object-fit: contain;
        }
        .footer-note {
            font-weight: bold;
            margin-top: 10px;
        }
        .developed-by {
            text-align: center;
            font-size: 12px;
            margin-top: 30px;
        }
        .developed-by span {
            color: blue;
            font-weight: bold;
        }
        
        @media print {
            body {
                padding: 0;
            }
            .print-container {
                max-width: 100%;
                border: none;
            }
            /* Hide print dialog if any UI elements are added later */
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
