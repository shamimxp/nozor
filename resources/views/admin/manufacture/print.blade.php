<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Manufacture Order - {{ $manufacture->invoice_no }}</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        @page {
            size: A4 portrait;
            margin: 5mm 6mm;
        }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff;
        }
        body {
            font-family: Arial, sans-serif;
            color: #000;
        }
        .print-container {
            width: 100%;
            max-width: 194mm;
            height: 260mm;
            max-height: 260mm;
            margin: 0 auto;
            background: #fff;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-sizing: border-box;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .print-container + .print-container {
            page-break-before: always;
            break-before: page;
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
                margin: 0 auto 20px;
            }
            .print-container:last-child {
                margin-bottom: 0;
            }
        }

        @media print {
            html, body {
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                overflow: hidden !important;
            }
            .print-container {
                width: 100% !important;
                max-width: 100% !important;
                height: 260mm !important;
                max-height: 260mm !important;
                margin: 0 auto !important;
                padding: 0 !important;
                box-shadow: none !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
                overflow: hidden !important;
            }
            .print-container + .print-container {
                page-break-before: always !important;
                break-before: page !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body onload="window.print()">
    @php
        $itemsToPrint = $manufacture->items->isNotEmpty() ? $manufacture->items : collect([null]);
    @endphp

    @foreach($itemsToPrint as $item)
        @php
            $product = $item ? $item->product : $manufacture->product;
            $recipe = $product ? $product->recipe : null;
            $imgs = $recipe && !empty($recipe->manufacture_images) ? $recipe->manufacture_images : [];
            $manufacture_image = !empty($imgs) && isset($imgs[0]) ? asset($imgs[0]) : ($product && $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png'));

            $partType = $item ? $item->part_type : '';
            if ($partType === 'body') {
                $partTypeLabel = 'Body Part';
            } elseif ($partType === 'finishing') {
                $partTypeLabel = 'Finishing Part';
            } elseif ($partType === 'both') {
                $partTypeLabel = 'Body Part & Finishing Part';
            } else {
                $partTypeLabel = ($manufacture->body_total > 0 && $manufacture->finishing_total > 0) ? 'Body Part & Finishing Part' : ($manufacture->body_total > 0 ? 'Body Part' : 'Finishing Part');
            }

            $itemQty = $item ? $item->qty : $manufacture->manufacture_qty;
            $refInvoice = $item ? $item->reff_invoice : $manufacture->reff_invoice;
            $costTitle = $item ? ($item->cost_title ?: ($item->costSetting->cost_title ?? '')) : '';
        @endphp

        <div class="print-container">
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
                <div><strong>Order No:</strong> {{ $manufacture->invoice_no }} @if($refInvoice) (Ref: {{ $refInvoice }}) @endif</div>
                <div><strong>Product:</strong> {{ $product->name ?? 'N/A' }}</div>
                <div><strong>Cost Title:</strong> {{ $costTitle ?: 'Standard' }} | <strong>Type:</strong> {{ $partTypeLabel }}</div>
                <div><strong>Assigned Worker:</strong> {{ $manufacture->worker->name ?? 'N/A' }} => {{ $manufacture->worker->phone ?? 'N/A' }}</div>
                <div><strong>Customer:</strong> {{ $manufacture->dealer_name ?? ($manufacture->dealer->name ?? 'N/A') }}</div>
                
                <div class="barcode-wrapper">
                    <img class="barcode" src="https://barcode.tec-it.com/barcode.ashx?data={{ $manufacture->id }}&code=Code128&dpi=96" alt="Barcode">
                </div>

                <div class="qty-text">Manufacture Quantity: {{ $itemQty }} Units</div>
            </div>

            <div class="design-text">
                Design Code: {{ $product->id ?? '' }}
            </div>

            <!-- Image -->
            <div class="manufacture-image-container">
                <img src="{{ $manufacture_image }}" alt="Manufacture Blueprint">
            </div>

            <hr>

            <!-- Footer -->
            <div class="footer-note">
                Note: {{ $item && $item->note ? $item->note : ($manufacture->note ?: 'N/A') }}
            </div>

            <div class="developed-by">
                Printed: {{ date('d M Y, h:i A') }} | Developed By : <span>Wood Machinery</span>
            </div>
        </div>
    @endforeach
</body>
</html>
