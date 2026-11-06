<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: A4;
            margin: 11mm 12mm;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            background: #fff;
            font-size: 12px;
        }
        .brand-header {
            border: 2px solid #1f3d1a;
            background: #c5cfa0;
            text-align: center;
            padding: 10px 12px;
            margin-bottom: 12px;
        }
        .brand-header .name {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: .3px;
            line-height: 1.15;
            margin: 0;
        }
        .brand-header .meta {
            margin-top: 4px;
            font-size: 10.5px;
            color: #333;
        }
        .brand-header .date {
            margin-top: 3px;
            font-size: 11px;
            font-weight: 700;
        }
        .quote-label {
            text-align: center;
            font-size: 12px;
            font-weight: 700;
            margin: 0 0 10px;
            color: #222;
        }
        .cat-block {
            width: 78%;
            margin: 0 0 14px;
            page-break-inside: avoid;
        }
        .cat-title {
            border: 1.5px solid #111;
            border-bottom: 0;
            background: #aeb890;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 800;
            color: #1f3d1a;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            border: 1.5px solid #111;
            background: #c5cfa0;
        }
        .items td {
            border: 1px solid #111;
            vertical-align: middle;
            background: #c5cfa0;
        }
        .items .img-cell {
            width: 78px;
            padding: 5px;
            text-align: center;
            background: #d7ddb8;
        }
        .items .img-cell img {
            width: 64px;
            height: 64px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #9aa67a;
        }
        .items .img-fallback {
            width: 64px;
            height: 64px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e4e8cf;
            border: 1px solid #9aa67a;
            font-size: 9px;
            color: #555;
            font-weight: 700;
        }
        .items .name-cell {
            padding: 6px 10px;
            font-size: 12.5px;
            font-weight: 600;
        }
        .items .name-cell .model {
            display: block;
            margin-top: 2px;
            font-size: 10px;
            font-weight: 400;
            color: #444;
        }
        .items .name-cell .disc {
            display: block;
            margin-top: 2px;
            font-size: 10px;
            color: #7a1f1f;
            font-weight: 700;
        }
        .items .price-cell {
            width: 90px;
            padding: 6px 8px;
            text-align: right;
            font-weight: 800;
            font-size: 13px;
            white-space: nowrap;
        }
        .items .price-cell .old {
            display: block;
            text-decoration: line-through;
            color: #555;
            font-weight: 400;
            font-size: 10px;
        }
        .footer {
            margin-top: 12px;
            font-size: 10px;
            color: #444;
            width: 78%;
        }
    </style>
</head>
<body>
    <div class="brand-header">
        <div class="name">{{ $company['name'] }}</div>
        <div class="meta">
            @if(!empty($company['address'])){{ trim(preg_replace('/\s+/', ' ', $company['address'])) }}@endif
            @if(!empty($company['phone'])) · {{ $company['phone'] }}@endif
            @if(!empty($company['email'])) · {{ $company['email'] }}@endif
        </div>
        <div class="date">{{ $priceTypeLabel }} · {{ $generatedAt->format('d M Y, h:i A') }}</div>
    </div>

    <div class="quote-label">{{ $title }}</div>

    @foreach($grouped as $categoryName => $items)
        <div class="cat-block">
            <div class="cat-title">{{ $categoryName }}</div>
            <table class="items">
                <tbody>
                    @foreach($items as $item)
                        @php
                            $imagePath = (!empty($item['image_path']) && is_file($item['image_path']))
                                ? $item['image_path']
                                : null;
                        @endphp
                        <tr>
                            <td class="img-cell">
                                @if($imagePath)
                                    <img src="file://{{ $imagePath }}" alt="">
                                @else
                                    <div class="img-fallback">No Image</div>
                                @endif
                            </td>
                            <td class="name-cell">
                                {{ $item['name'] }}
                                @if(!empty($item['model_no']))
                                    <span class="model">{{ $item['model_no'] }}</span>
                                @endif
                                @if($item['has_discount'] && $item['discount_label'])
                                    <span class="disc">{{ $item['discount_label'] }}</span>
                                @endif
                            </td>
                            <td class="price-cell">
                                @if($item['final_price'] === null)
                                    —
                                @else
                                    @if($item['has_discount'] && $item['base_price'] !== null)
                                        <span class="old">{{ number_format($item['base_price'], 0) }}</span>
                                    @endif
                                    {{ number_format($item['final_price'], 0) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    @if($note)
        <div class="footer"><strong>Note:</strong> {{ $note }}</div>
    @endif
    <div class="footer">
        Generated electronically · not stored in system · {{ $generatedAt->format('d M Y, h:i A') }}
    </div>
</body>
</html>
