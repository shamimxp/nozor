@extends('layouts.admin')
@section('page-title', 'Print Barcode')
@section('title', 'Print Barcode')

@section('content')
<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-2">Print Item</h4>
        <p class="text-muted mb-2">{{ $product->name }}</p>

        <div class="barcode-controls">
            <div class="row">
                <div class="col-md-6 mb-1">
                    <label for="codeRow">Row</label>
                    <input type="number" min="1" id="codeRow" class="form-control" placeholder="Row">
                    <small class="text-muted">How many labels sit on one line.</small>
                </div>
                <div class="col-md-6 mb-1">
                    <label for="codeColumn">Column</label>
                    <input type="number" min="1" id="codeColumn" class="form-control" placeholder="Column">
                    <small class="text-muted">How many lines to print.</small>
                </div>
            </div>
            <div class="barcode-actions">
                <button type="button" class="btn btn-primary barcode-action-btn" id="viewBarcodes">View</button>
                <button type="button" class="btn btn-primary barcode-action-btn" id="printBarcodes">Print</button>
                <button type="button" class="btn btn-primary barcode-action-btn" id="pdfBarcodes">PDF</button>
            </div>
        </div>

        <div class="barcode-preview">
            <div id="printableArea"></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .barcode-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
    }
    .barcode-action-btn {
        width: 120px;
        height: 38px;
        margin: 0;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    .barcode-preview {
        margin-top: 18px;
        padding: 16px;
        background: #eef1f4;
        border-radius: 6px;
    }
    .barcode-preview:empty,
    .barcode-preview:not(:has(.barcode-label)) {
        display: none;
    }
    .barcode-sheet {
        display: grid;
        grid-template-columns: repeat(var(--cols, 4), minmax(0, 1fr));
        gap: 6px;
        margin-bottom: 6px;
    }
    .barcode-label {
        width: auto;
        box-sizing: border-box;
        padding: 8px 8px 6px;
        border: 1px solid #adadad;
        text-align: center;
        line-height: 16px;
        background: #fff;
        break-inside: avoid;
        page-break-inside: avoid;
    }
    .barcode-label .name {
        margin: 0;
        font-size: 13px;
        font-weight: 400;
        line-height: 16px;
        color: #000;
    }
    .barcode-label .barcode-svg,
    .barcode-label .barcode-img {
        display: block;
        width: 150px;
        height: 40px;
        margin: 6px auto 0;
    }
    .barcode-label .code {
        display: block;
        margin-top: 1px;
        font-size: 8px;
        font-weight: 700;
        line-height: 10px;
        color: #000;
    }
    @page { size: A4 portrait; margin: 6mm; }
    @media print {
        .header-navbar,
        .main-menu,
        .footer,
        .content-header,
        .header-navbar-shadow,
        .sidenav-overlay,
        .barcode-controls,
        .phpdebugbar,
        .card-title,
        .text-muted {
            display: none !important;
        }
        html, body,
        .app-content,
        .content-wrapper,
        .content-body,
        .card,
        .card-body,
        .barcode-preview {
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            box-shadow: none !important;
            width: auto !important;
            min-height: 0 !important;
        }
        .vertical-layout .app-content {
            margin-left: 0 !important;
        }
        .barcode-label {
            width: auto;
            padding: 1.6mm 1.2mm 1.2mm;
            margin: 0;
        }
        .barcode-label .name {
            font-size: 8pt;
            line-height: 10pt;
        }
        .barcode-label .barcode-img,
        .barcode-label .barcode-svg {
            width: 88%;
            height: 9mm;
            margin-top: 1mm;
        }
        .barcode-label .code {
            font-size: 6pt;
            line-height: 7pt;
        }
        .barcode-sheet {
            gap: 1.6mm;
            margin-bottom: 1.6mm;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
    const barcodeName = @json($product->name);
    const barcodeCode = @json($code);
    const barcodePdfUrl = @json(route('admin.product.barcode.pdf', $product->id));

    function readGrid() {
        const row = parseInt(document.getElementById('codeRow').value, 10) || 0;
        const column = parseInt(document.getElementById('codeColumn').value, 10) || 0;
        if (row < 1 || column < 1) {
            document.getElementById('printableArea').innerHTML = '';
            if (window.toastr) {
                toastr.error('Enter a row and a column of at least 1.');
            }
            return null;
        }
        return { row: row, column: column };
    }

    function renderBarcodes() {
        const grid = readGrid();
        const area = document.getElementById('printableArea');
        if (!grid) {
            return false;
        }
        const row = grid.row;
        const column = grid.column;

        const canvas = document.createElement('canvas');
        JsBarcode(canvas, barcodeCode, {
            format: 'CODE128',
            width: 2,
            height: 40,
            displayValue: false,
            margin: 8,
            background: '#ffffff'
        });
        const barcodeSrc = canvas.toDataURL('image/png');

        let html = '';
        for (let line = 0; line < column; line++) {
            html += '<div class="barcode-sheet" style="--cols:' + row + '">';
            for (let cell = 0; cell < row; cell++) {
                html += '<div class="barcode-label">'
                    + '<p class="name"></p>'
                    + '<img class="barcode-img" alt="">'
                    + '<small class="code"></small>'
                    + '</div>';
            }
            html += '</div>';
        }

        area.innerHTML = html;
        area.querySelectorAll('.name').forEach(function (el) { el.textContent = barcodeName; });
        area.querySelectorAll('.code').forEach(function (el) { el.textContent = barcodeCode; });
        area.querySelectorAll('.barcode-img').forEach(function (el) { el.src = barcodeSrc; });
        return true;
    }

    document.getElementById('viewBarcodes').addEventListener('click', renderBarcodes);
    document.getElementById('printBarcodes').addEventListener('click', function () {
        if (renderBarcodes()) {
            window.print();
        }
    });
    document.getElementById('pdfBarcodes').addEventListener('click', function () {
        const grid = readGrid();
        if (!grid) {
            return;
        }
        window.location = barcodePdfUrl + '?row=' + grid.row + '&column=' + grid.column;
    });
</script>
@endpush
