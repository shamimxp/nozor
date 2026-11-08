<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @php
        $labelMm = (198 - (1.6 * ($row + 1))) / max(1, $row);
        $barcodeMm = max(16, $labelMm - 5);
    @endphp
    <style>
        @page { margin: 6mm; }
        * { margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; color: #000; }
        table.line {
            width: 198mm;
            border-collapse: separate;
            border-spacing: 1.6mm 1.6mm;
            margin: 0;
            page-break-inside: avoid;
        }
        td.label {
            width: {{ number_format($labelMm, 2, '.', '') }}mm;
            border: 0.25mm solid #adadad;
            text-align: center;
            vertical-align: top;
            padding: 1.6mm 1.2mm 1.2mm;
        }
        .name {
            font-size: 8pt;
            line-height: 10pt;
            font-weight: normal;
        }
        .barcode {
            display: block;
            width: {{ number_format($barcodeMm, 2, '.', '') }}mm;
            height: 9mm;
            margin: 1mm auto 0;
        }
        .code {
            font-size: 6pt;
            line-height: 7pt;
            font-weight: bold;
            margin-top: 0.3mm;
        }
    </style>
</head>
<body>
    @for ($line = 0; $line < $column; $line++)
        <table class="line">
            <tr>
                @for ($cell = 0; $cell < $row; $cell++)
                    <td class="label">
                        <div class="name">{{ $product->name }}</div>
                        <img class="barcode" src="{{ $barcode }}" alt="{{ $code }}">
                        <div class="code">{{ $code }}</div>
                    </td>
                @endfor
            </tr>
        </table>
    @endfor
</body>
</html>
