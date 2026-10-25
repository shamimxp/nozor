<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Draft Order - {{ $draftOrder->draft_number }}</title>
    <link rel="stylesheet" href="{{ asset('admin/app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/app-assets/css/bootstrap.css') }}">
    <style>
        body {
            background-color: #fff;
            color: #222;
            font-size: 13px;
            padding: 20px;
        }
        .print-actions {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #ccc;
        }
        @media print {
            .print-actions {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
        .header-title {
            font-size: 20px;
            font-weight: 700;
        }
        .divider {
            border-bottom: 2px solid #333;
            margin: 12px 0;
        }
        .items-table th, .items-table td {
            border: 1px solid #333 !important;
            padding: 6px 10px;
        }
        .items-table th {
            background-color: #f2f2f2 !important;
            -webkit-print-color-adjust: exact;
        }
    </style>
</head>
<body>

    <div class="print-actions text-right">
        <button class="btn btn-primary btn-sm font-weight-bold" onclick="window.print()">
            Print Now
        </button>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.close()">
            Close
        </button>
    </div>

    <div class="row align-items-center mb-2">
        <div class="col-4">
            <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" style="max-width: 150px;" alt="Logo">
        </div>
        <div class="col-8 text-right">
            <div class="header-title">Wood Machinery and Hardware</div>
            <div class="small text-muted">
                Purbo Padardiya (Shahabuddin Road Shonglogno) Shatarkul Road, Badda, Dhaka-1212<br>
                Phone: 01674-088383 | Email: info@woodmachinery.com.bd
            </div>
        </div>
    </div>

    <div class="divider"></div>

    <div class="text-center my-2">
        <h4 class="font-weight-bolder text-uppercase mb-0">DRAFT ORDER / PROFORMA INVOICE</h4>
    </div>

    <div class="row mb-3">
        <div class="col-6">
            <h6 class="font-weight-bold text-uppercase mb-1" style="font-size: 11px;">Dealer Information:</h6>
            <div class="font-weight-bold" style="font-size: 15px;">{{ $draftOrder->dealer->shop_name ?? 'N/A' }}</div>
            <div><strong>Contact:</strong> {{ $draftOrder->dealer->name ?? 'N/A' }}</div>
            <div><strong>Phone:</strong> {{ $draftOrder->dealer->phone ?? 'N/A' }}</div>
            <div><strong>Address:</strong> {{ $draftOrder->dealer->address ?? 'N/A' }}</div>
        </div>
        <div class="col-6 text-right">
            <div><strong>Draft No:</strong> <span class="font-weight-bold">{{ $draftOrder->draft_number }}</span></div>
            @if($draftOrder->orderRequest)
                <div><strong>Order Request Ref:</strong> {{ $draftOrder->orderRequest->request_number ?? ('#' . $draftOrder->orderRequest->id) }}</div>
            @endif
            <div><strong>Date:</strong> {{ $draftOrder->created_at->format('d M, Y') }}</div>
            <div><strong>Status:</strong> <span class="text-uppercase">{{ $draftOrder->status }}</span></div>
        </div>
    </div>

    <table class="table items-table w-100 mb-3">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 50%;">Product Name</th>
                <th style="width: 15%; text-align: right;">Unit Price</th>
                <th style="width: 15%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($draftOrder->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->product->name ?? 'N/A' }}</td>
                <td class="text-right">৳{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-center">{{ $item->qty }}</td>
                <td class="text-right">৳{{ number_format($item->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="row">
        <div class="col-7">
            @if($draftOrder->note)
                <div class="mb-2">
                    <strong>Customer Note:</strong>
                    <div>{{ $draftOrder->note }}</div>
                </div>
            @endif
            @if($draftOrder->admin_notes)
                <div>
                    <strong>Admin Note:</strong>
                    <div>{{ $draftOrder->admin_notes }}</div>
                </div>
            @endif
        </div>
        <div class="col-5">
            <table class="table table-sm border-0">
                <tr>
                    <td class="text-right border-0 font-weight-bold">Sub Total:</td>
                    <td class="text-right border-0 font-weight-bold" style="width: 120px;">৳{{ number_format($draftOrder->sub_total, 2) }}</td>
                </tr>
                @if($draftOrder->discount > 0)
                <tr>
                    <td class="text-right border-0 text-danger font-weight-bold">Discount:</td>
                    <td class="text-right border-0 text-danger font-weight-bold">- ৳{{ number_format($draftOrder->discount, 2) }}</td>
                </tr>
                @endif
                @if($draftOrder->carrying_charge > 0)
                <tr>
                    <td class="text-right border-0 font-weight-bold">Carrying Charge:</td>
                    <td class="text-right border-0 font-weight-bold">+ ৳{{ number_format($draftOrder->carrying_charge, 2) }}</td>
                </tr>
                @endif
                <tr style="border-top: 2px solid #333;">
                    <td class="text-right font-weight-bolder" style="font-size: 15px;">Grand Total:</td>
                    <td class="text-right font-weight-bolder" style="font-size: 15px;">৳{{ number_format($draftOrder->grand_total, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row mt-5 pt-4">
        <div class="col-6">
            <div style="border-top: 1px solid #333; width: 160px; text-align: center; font-weight: bold; padding-top: 4px;">
                Prepared By
            </div>
        </div>
        <div class="col-6 text-right">
            <div style="border-top: 1px solid #333; width: 160px; text-align: center; font-weight: bold; padding-top: 4px; margin-left: auto;">
                Authorized Signature
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
