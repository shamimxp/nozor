@extends('layouts.dealer')
@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Order Details</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dealer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('dealer.orders.index') }}">My Orders</a></li>
                        <li class="breadcrumb-item active">{{ $order->order_number }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="invoice-preview-wrapper">
        <div class="row invoice-preview">
            <!-- Top Actions Card -->
            <div class="col-12 invoice-actions mb-2">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex flex-wrap align-items-center justify-content-between p-1">
                        <div class="d-flex flex-wrap align-items-center">
                            <a href="{{ route('dealer.orders.pdf', $order->id) }}" class="btn btn-primary mr-1 mb-50 mb-sm-0">
                                <i data-feather="download" class="mr-25"></i> Download PDF
                            </a>
                            <button class="btn btn-outline-secondary mr-1 mb-50 mb-sm-0" onclick="window.print()">
                                <i data-feather="printer" class="mr-25"></i> Print Order
                            </button>
                        </div>
                        <div>
                            <a href="{{ route('dealer.orders.index') }}" class="btn btn-success mb-50 mb-sm-0">
                                <i data-feather="arrow-left" class="mr-25"></i> Back to Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Card -->
            <div class="col-12">
                <div class="card invoice-preview-card shadow-sm border-0">
                    <!-- Invoice Header -->
                    <div class="card-body invoice-padding pb-0">
                        <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                            <div>
                                <div class="logo-wrapper mb-1">
                                    <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="Logo" style="width: 180px; height: 55px; object-fit: contain;">
                                </div>
                                <p class="card-text mb-0">
                                    <strong>Status: </strong>
                                    @php
                                        $statusClass = 'badge-light-info';
                                        if ($order->status == 'pending') $statusClass = 'badge-light-warning';
                                        elseif ($order->status == 'delivered') $statusClass = 'badge-light-success';
                                        elseif ($order->status == 'cancelled') $statusClass = 'badge-light-danger';
                                    @endphp
                                    <span class="badge badge-pill {{ $statusClass }} font-weight-bold px-1 py-50">{{ ucfirst($order->status) }}</span>
                                </p>
                            </div>
                            <div class="mt-md-0 mt-2 text-md-right">
                                <h4 class="invoice-title font-weight-bolder text-primary">
                                    Order No <span class="invoice-number" style="font-size: 22px">#{{ $order->order_number }}</span>
                                </h4>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title text-md-right mb-25">Order Date:</p>
                                    <p class="invoice-date font-weight-bolder text-dark mb-0">
                                        {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d M, Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="invoice-spacing my-1" />

                    <!-- Dealer Details & Delivery Information -->
                    <div class="card-body invoice-padding pt-0">
                        <div class="row invoice-spacing">
                            <div class="col-xl-6 p-2">
                                <h6 class="mb-1 text-primary font-weight-bolder"><i data-feather="briefcase" class="mr-25"></i> Dealer Details:</h6>
                                <h6 class="mb-50 font-weight-bold text-dark">Shop Name: {{ $order->dealer->shop_name ?? 'N/A' }}</h6>
                                <p class="card-text mb-25"><strong>Name:</strong> {{ $order->dealer->name ?? 'N/A' }}</p>
                                <p class="card-text mb-25"><strong>Phone:</strong> {{ $order->dealer->phone ?? 'N/A' }}</p>
                                <p class="card-text mb-25"><strong>Email:</strong> {{ $order->dealer->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-xl-6 p-2 border-left">
                                <h6 class="mb-1 text-primary font-weight-bolder"><i data-feather="truck" class="mr-25"></i> Delivery Details:</h6>
                                <p class="card-text mb-50">
                                    <strong>Status: </strong>
                                    <span class="badge badge-pill {{ $statusClass }} font-weight-bold">{{ ucfirst($order->status) }}</span>
                                </p>
                                <p class="card-text mb-25"><strong>Delivery Address:</strong> {{ $order->dealer->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th class="py-1" style="width: 75px;">Product Image</th>
                                    <th class="py-1" style="width: 75px;">Manufacture Image</th>
                                    <th class="py-1">Product</th>
                                    <th class="py-1">Rate</th>
                                    <th class="py-1 text-center">Qty</th>
                                    <th class="py-1 text-right">Total</th>
                                    <th class="py-1">Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="py-1">
                                        @if($item->product && $item->product->featured_image)
                                            @php $pImg = asset(config('imagepath.product') . $item->product->featured_image); @endphp
                                            <img src="{{ $pImg }}" alt="Product Image" width="45" height="45" 
                                                class="img-preview-trigger shadow-sm"
                                                data-preview-url="{{ $pImg }}"
                                                data-preview-title="{{ $item->product->name ?? 'Product' }} (Product Image)"
                                                title="Click to zoom image"
                                                style="border-radius: 6px; object-fit: cover; border: 1px solid #e2e8f0;" 
                                                onerror="this.src='{{ asset('images/no-image.png') }}'">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" alt="No Image" width="45" height="45" style="border-radius: 6px; object-fit: cover;">
                                        @endif
                                    </td>
                                    <td class="py-1">
                                        @php
                                            $mfgImg = null;
                                            if ($item->product && $item->product->is_manufacturer == 1 && $item->product->recipe) {
                                                $mImages = $item->product->recipe->manufacture_images;
                                                if (is_string($mImages)) {
                                                    $mImages = json_decode($mImages, true);
                                                }
                                                if (is_array($mImages) && count($mImages) > 0 && !empty($mImages[0])) {
                                                    $mfgImg = asset($mImages[0]);
                                                }
                                            }
                                        @endphp
                                        @if($mfgImg)
                                            <img src="{{ $mfgImg }}" alt="Manufacture Image" width="45" height="45" 
                                                class="img-preview-trigger shadow-sm"
                                                data-preview-url="{{ $mfgImg }}"
                                                data-preview-title="{{ $item->product->name ?? 'Product' }} (Manufacture Image)"
                                                title="Click to zoom image"
                                                style="border-radius: 6px; object-fit: cover; border: 1px solid #e2e8f0;" 
                                                onerror="this.src='{{ asset('images/no-image.png') }}'">
                                        @else
                                            <span class="badge badge-light-secondary font-weight-normal" style="font-size:0.75rem;">N/A</span>
                                        @endif
                                    </td>
                                    <td class="py-1">
                                        <div class="d-flex align-items-center">
                                            <strong class="text-dark mb-0 font-weight-bold">{{ $item->product->name ?? '-' }}</strong>
                                            @if($item->product && $item->product->is_manufacturer == 1)
                                                <span class="badge badge-light-info ml-1" style="font-size: 0.7rem; padding: 0.2rem 0.4rem;">Manufacturer</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-1">
                                        <span class="font-weight-bold">৳ {{ number_format($item->price, 2) }}</span>
                                    </td>
                                    <td class="py-1 text-center">
                                        <span class="badge badge-pill badge-light-dark font-weight-bolder">{{ $item->qty }}</span>
                                    </td>
                                    <td class="py-1 text-right">
                                        <strong class="text-dark font-weight-bolder">৳ {{ number_format($item->total, 2) }}</strong>
                                    </td>
                                    <td class="py-1 text-muted">
                                        {{ $item->note ?: '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals & Notes Section -->
                    <div class="card-body invoice-padding pb-2 mt-1">
                        <div class="row invoice-sales-total-wrapper">
                            <div class="col-md-6 order-md-1 order-2 mt-md-0 mt-3">
                                <div class="p-1 rounded bg-light border">
                                    <h6 class="font-weight-bolder text-dark mb-50"><i data-feather="file-text" class="mr-25 text-primary"></i> Order Notes:</h6>
                                    <p class="card-text text-muted mb-0">{{ $order->note ?: 'No special notes provided.' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex justify-content-end order-md-2 order-1">
                                <div class="invoice-total-wrapper" style="width: 100%; max-width: 280px;">
                                    <div class="invoice-total-item d-flex justify-content-between py-25">
                                        <span class="invoice-total-title text-muted">Subtotal:</span>
                                        <span class="invoice-total-amount font-weight-bold">৳ {{ number_format($order->sub_total, 2) }}</span>
                                    </div>
                                    <div class="invoice-total-item d-flex justify-content-between py-25">
                                        <span class="invoice-total-title text-muted">Discount:</span>
                                        <span class="invoice-total-amount font-weight-bold">৳ {{ number_format($order->discount, 2) }}</span>
                                    </div>
                                    <div class="invoice-total-item d-flex justify-content-between py-25">
                                        <span class="invoice-total-title text-muted">Carrying:</span>
                                        <span class="invoice-total-amount font-weight-bold">৳ {{ number_format($order->carrying_charge, 2) }}</span>
                                    </div>
                                    <hr class="my-50" />
                                    <div class="invoice-total-item d-flex justify-content-between py-25">
                                        <span class="invoice-total-title font-weight-bolder h5 mb-0 text-dark">Grand Total:</span>
                                        <span class="invoice-total-amount font-weight-bolder h5 mb-0 text-dark">৳ {{ number_format($order->grand_total, 2) }}</span>
                                    </div>
                                    <div class="invoice-total-item d-flex justify-content-between py-25 mt-50">
                                        <span class="invoice-total-title text-success font-weight-bold">Paid Amount:</span>
                                        <span class="invoice-total-amount text-success font-weight-bold">৳ {{ number_format($order->paid, 2) }}</span>
                                    </div>
                                    <div class="invoice-total-item d-flex justify-content-between py-25">
                                        <span class="invoice-total-title text-danger font-weight-bold">Due Amount:</span>
                                        <span class="invoice-total-amount text-danger font-weight-bolder">৳ {{ number_format($order->due, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment History Breakdown (if settled) -->
                    @if(isset($order->payments) && $order->payments->count() > 0)
                    <div class="card-footer bg-light-secondary border-top p-2">
                        <h6 class="font-weight-bolder text-dark mb-1"><i data-feather="check-square" class="mr-25 text-success"></i> Order Payments & Wallet Settlement Breakdown</h6>
                        <div class="table-responsive bg-white rounded shadow-sm border">
                            <table class="table table-sm table-striped mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Payment Date</th>
                                        <th>Amount</th>
                                        <th>Txn / Ref</th>
                                        <th>Settlement Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->payments as $k => $pm)
                                    <tr>
                                        <td>{{ $k + 1 }}</td>
                                        <td>{{ $pm->payment_date ? \Carbon\Carbon::parse($pm->payment_date)->format('d M, Y') : '-' }}</td>
                                        <td><strong class="text-success">৳ {{ number_format($pm->amount, 2) }}</strong></td>
                                        <td>{{ $pm->dealerWalletTransaction->transaction_no ?? 'Wallet Settlement' }}</td>
                                        <td>{{ $pm->note ?? 'Wallet Auto-Settlement' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Hidden Print Template (Matches PDF Design) -->
    <div id="print-view">
        <div class="print-container">
            <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" class="watermark" alt="Watermark">

            @php
            if (!function_exists('getAmountInWordsPrintDealer')) {
                function getAmountInWordsPrintDealer($amount) {
                    if (class_exists('\NumberFormatter')) {
                        $f = new \NumberFormatter("en", \NumberFormatter::SPELLOUT);
                        $amt = explode('.', number_format($amount, 2, '.', ''));
                        $taka = (int)$amt[0];
                        $poysa = (int)$amt[1];

                        $str = $f->format($taka) . ' taka';
                        if ($poysa > 0) {
                            $str .= ' and ' . $f->format($poysa) . ' poysa';
                        }
                        return ucwords($str);
                    }
                    return number_format($amount, 2) . ' Taka';
                }
            }
            @endphp

            <table class="header-table">
                <tr>
                    <td class="header-logo">
                        <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="Logo">
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

            <div class="invoice-title">Dealer Invoice</div>

            <table class="info-table">
                <tr>
                    <td class="info-left">
                        Invoice No : {{ $order->order_number }}<br>
                        Dealer Name : {{ $order->dealer->name ?? '' }} ({{ $order->dealer->shop_name ?? '' }})<br>
                        Dealer Address : {{ $order->dealer->address ?? '' }}<br>
                        Dealer Phone No : {{ $order->dealer->phone ?? '' }}
                    </td>
                    <td class="info-right">
                        Date : {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d M Y') : date('d M Y') }}
                    </td>
                </tr>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">SL</th>
                        <th style="width: 45%; text-align: left;">Product Name</th>
                        <th style="width: 10%;">Req. Qty</th>
                        <th style="width: 10%;">Conf. Qty</th>
                        <th style="width: 15%;">Unit Price</th>
                        <th style="width: 15%;">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                    <tr>
                        <td class="text-center"><b>{{ $index + 1 }}</b></td>
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-center">{{ number_format($item->price, strpos($item->price, '.') ? 2 : 0) }}</td>
                        <td class="text-center">{{ number_format($item->total, strpos($item->total, '.') ? 2 : 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="bottom-table">
                <tr>
                    <td class="in-words-col">
                        Total In Words : <span>{{ getAmountInWordsPrintDealer($order->grand_total) }}</span>
                    </td>
                    <td class="totals-col">
                        <table class="totals-table">
                            <tr>
                                <td>Total Amount :</td>
                                <td>{{ number_format($order->grand_total, 2) }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td>Received Amount :</td>
                                <td>{{ $order->paid > 0 ? number_format($order->paid, 2) : '0.0' }}</td>
                            </tr>
                            <tr>
                                <td>Due Amount:</td>
                                <td>{{ $order->due > 0 ? number_format($order->due, 2) : '0.0' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <div class="description-box">
                Description: <span>{{ $order->note ?: 'N/A' }}</span>
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
        </div>
    </div>
</div>

<style>
    /* Hide print view on screens */
    @media screen {
        #print-view { display: none !important; }
    }

    @media print {
        /* Safely hide layout wrappers and UI elements */
        .header-navbar,
        .main-menu,
        .footer,
        .content-header,
        .invoice-preview-wrapper,
        .header-navbar-shadow,
        .sidenav-overlay,
        .drag-target,
        .scroll-to-top,
        .customizer {
            display: none !important;
        }

        /* Reset margins and paddings for a clean print */
        body, html, .app-content, .content-wrapper, .content-body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: white !important;
            height: auto !important;
            min-height: auto !important;
        }

        /* Show the print view inside the normal document flow */
        #print-view {
            display: block !important;
            position: relative !important;
            width: 100% !important;
            font-family: serif;
            color: #333;
            font-size: 13px;
        }
        #print-view .print-container {
            padding: 30px;
        }
        #print-view .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: -1;
            width: 400px;
        }
        #print-view .header-table {
            width: 100%;
            margin-bottom: 5px;
        }
        #print-view .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        #print-view .header-logo {
            width: 25%;
            text-align: left;
        }
        #print-view .header-logo img {
            width: 140px;
        }
        #print-view .header-content {
            width: 50%;
            text-align: center;
        }
        #print-view .header-empty {
            width: 25%;
        }
        #print-view .header-title {
            font-size: 20px;
            font-weight: bold;
            line-height: 1.1;
            margin-bottom: 5px;
        }
        #print-view .header-address {
            font-size: 13px;
            line-height: 1.3;
        }
        #print-view .divider {
            border-bottom: 2px solid #555;
            margin: 10px 0;
        }
        #print-view .invoice-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        #print-view .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        #print-view .info-table td {
            vertical-align: top;
            padding: 2px 0;
            border: none;
        }
        #print-view .info-left {
            width: 60%;
        }
        #print-view .info-right {
            width: 40%;
            text-align: right;
        }
        #print-view .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        #print-view .items-table th, #print-view .items-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 12px;
        }
        #print-view .items-table th {
            text-align: center;
            font-weight: bold;
        }
        #print-view .text-center { text-align: center !important; }
        #print-view .text-left { text-align: left !important; }
        #print-view .text-right { text-align: right !important; }
        #print-view .bottom-section {
            width: 100%;
            margin-top: 5px;
        }
        #print-view .bottom-table {
            width: 100%;
        }
        #print-view .bottom-table td {
            vertical-align: top;
            border: none;
        }
        #print-view .in-words-col {
            width: 60%;
            font-size: 12px;
            font-weight: bold;
        }
        #print-view .in-words-col span {
            font-weight: normal;
        }
        #print-view .totals-col {
            width: 40%;
        }
        #print-view .totals-table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }
        #print-view .totals-table td {
            padding: 2px 0;
            text-align: right;
            border: none;
        }
        #print-view .totals-table td:first-child {
            width: 60%;
            padding-right: 10px;
        }
        #print-view .totals-table td:last-child {
            width: 40%;
        }
        #print-view .totals-table .border-bottom td {
            border-bottom: 1px solid #000;
        }
        #print-view .description-box {
            margin-top: 25px;
            font-weight: bold;
            font-size: 12px;
        }
        #print-view .description-box span {
            font-weight: normal;
        }
        #print-view .signature-section {
            width: 100%;
            margin-top: 70px;
        }
        #print-view .signature-section table {
            width: 100%;
        }
        #print-view .signature-section td {
            width: 50%;
            border: none;
        }
        #print-view .sign-line {
            border-top: 1px solid #000;
            width: 150px;
            text-align: center;
            font-weight: bold;
            padding-top: 5px;
            font-size: 12px;
        }
        #print-view .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            font-weight: bold;
        }
    }
</style>

@include('admin.partials.image-preview-modal')
@endsection

@push('scripts')
<script>
    $(window).on('load', function() {
        if (feather) {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    });
</script>
@endpush
