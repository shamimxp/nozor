@extends('layouts.admin')
@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Dealer Order Details</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dealer-order.index') }}"> Dealer Orders</a></li>
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
            <!-- Invoice Actions -->
            <div class="col-xl-12 col-md-12 col-12 invoice-actions mt-md-0 mt-2">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('admin.dealer-order.export-pdf', $order->id) }}" class="btn btn-primary btn-block mb-75">Download PDF</a>
                        <button class="btn btn-outline-secondary btn-block mb-75" onclick="window.print()">
                            Print
                        </button>
                        @if($order->status != 'delivered' && $order->status != 'cancelled')
                            <a class="btn btn-outline-secondary btn-block mb-75" href="{{ route('admin.dealer-order.edit', $order->id) }}"> Edit Order </a>
                        @endif
                        <a href="{{ route('admin.dealer-order.index') }}" class="btn btn-success btn-block">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
            <!-- Invoice -->
            <div class="col-xl-12 col-md-12 col-12">
                <div class="card invoice-preview-card">
                    <div class="card-body invoice-padding pb-0">
                        <!-- Header starts -->
                        <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                            <div>
                                <div class="logo-wrapper">
                                    <h3 class="text-primary invoice-logo">
                                        <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="Logo" style="width: 180px;" class="mr-25">
                                    </h3>
                                </div>

                            </div>
                            <div class="mt-md-0 mt-2 text-md-right">
                                <h4 class="invoice-title">
                                    Order No <span class="invoice-number" style="font-size: 20px">#{{ $order->order_number }}</span>
                                </h4>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title text-right">Order Date:</p>
                                    <p class="invoice-date"><strong>{{ $order->order_date ? Carbon\Carbon::parse($order->order_date)->format('d M Y') : 'N/A' }}</strong></p>
                                </div>
                            </div>
                        </div>
                        <!-- Header ends -->
                    </div>

                    <hr class="invoice-spacing" />

                    <!-- Address and Contact starts -->
                    <div class="card-body invoice-padding pt-0">
                        <div class="row invoice-spacing">
                            <div class="col-xl-6 p-2">
                                <h6 class="mb-2">Dealer Details:</h6>
                                <h6 class="mb-25">Shop Name: {{ $order->dealer->shop_name ?? 'N/A' }}</h6>
                                <p class="card-text mb-25">Name: {{ $order->dealer->name ?? 'N/A' }}</p>
                                <p class="card-text mb-25">Phone: {{ $order->dealer->phone ?? 'N/A' }}</p>
                                <p class="card-text mb-25">Email: {{ $order->dealer->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-xl-6 p-2 border-left">
                                <h6 class="mb-2">Delivery Details:</h6>
                                <p class="card-text mb-25">
                                    <strong>Status: </strong> <span class="badge badge-light-primary">{{ ucfirst($order->status) }}</span></strong>
                                </p>
                                <p class="card-text mb-25"><strong>Address:</strong>  {{ $order->dealer->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Address and Contact ends -->

                    <!-- Invoice Description starts -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="py-1" style="width: 60px;">Image</th>
                                <th class="py-1">Product</th>
                                <th class="py-1">Rate</th>
                                <th class="py-1 text-center">Qty</th>
                                <th class="py-1 text-right">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td class="py-1">
                                    @if($item->product && $item->product->featured_image)
                                        <img src="{{ asset(config('imagepath.product') . $item->product->featured_image) }}" alt="Image" width="40" height="40" style="border-radius: 4px; object-fit: cover; border: 1px solid #e5e7eb;" onerror="this.src='{{ asset('images/no-image.png') }}'">
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="No Image" width="40" height="40" style="border-radius: 4px; object-fit: cover;">
                                    @endif
                                </td>
                                <td class="py-1">
                                    <div class="d-flex align-items-center">
                                        <p class="card-text font-weight-bold mb-0">{{ $item->product->name ?? '-' }}</p>
                                        @if($item->product && $item->product->is_manufacturer == 1)
                                            <span class="badge badge-light-info ml-1" style="font-size: 0.7rem; padding: 0.2rem 0.4rem;">Manufacturer</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-1">
                                    <span class="font-weight-bold">৳{{ number_format($item->price, 2) }}</span>
                                </td>
                                <td class="py-1 text-center">
                                    <span class="font-weight-bold">{{ $item->qty }}</span>
                                </td>
                                <td class="py-1 text-right">
                                    <span class="font-weight-bold">৳{{ number_format($item->total, 2) }}</span>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body invoice-padding pb-0 mt-2">
                        <div class="row invoice-sales-total-wrapper">
                            <div class="col-md-6 order-md-1 order-2 mt-md-0 mt-3">
                                <p class="card-text mb-1">
                                    <span class="font-weight-bold">Notes:</span><br>
                                    <span>{{ $order->note ?: 'No special notes provided.' }}</span>
                                </p>
                                <p class="card-text mt-1">
                                    <span class="font-weight-bold text-primary">Admin Notes:</span><br>
                                    <span>{{ $order->admin_notes ?: 'No admin notes provided.' }}</span>
                                </p>
                            </div>
                            <div class="col-md-6 d-flex justify-content-end order-md-2 order-1">
                                <div class="invoice-total-wrapper" style="width: 100%; max-width: 200px;">
                                    <div class="invoice-total-item d-flex justify-content-between">
                                        <p class="invoice-total-title">Subtotal:</p>
                                        <p class="invoice-total-amount">৳{{ number_format($order->sub_total, 2) }}</p>
                                    </div>
                                    <div class="invoice-total-item d-flex justify-content-between">
                                        <p class="invoice-total-title">Discount:</p>
                                        <p class="invoice-total-amount">৳{{ number_format($order->discount, 2) }}</p>
                                    </div>
                                    <div class="invoice-total-item d-flex justify-content-between">
                                        <p class="invoice-total-title">Carrying:</p>
                                        <p class="invoice-total-amount">৳{{ number_format($order->carrying_charge, 2) }}</p>
                                    </div>
                                    <hr class="my-50" />
                                    <div class="invoice-total-item d-flex justify-content-between">
                                        <p class="invoice-total-title font-weight-bolder h5">Grand Total:</p>
                                        <p class="invoice-total-amount font-weight-bolder h5">৳{{ number_format($order->grand_total, 2) }}</p>
                                    </div>
                                    <div class="invoice-total-item d-flex justify-content-between mt-1">
                                        <p class="invoice-total-title text-success">Paid Amount:</p>
                                        <p class="invoice-total-amount text-success">৳{{ number_format($order->paid, 2) }}</p>
                                    </div>
                                    <div class="invoice-total-item d-flex justify-content-between">
                                        <p class="invoice-total-title text-danger font-weight-bold">Due Amount:</p>
                                        <p class="invoice-total-amount text-danger font-weight-bold">৳{{ number_format($order->due, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Invoice Description ends -->

                    <hr class="invoice-spacing" />
                </div>
            </div>
            <!-- /Invoice -->


            <!-- /Invoice Actions -->
        </div>
    </section>

    <!-- Hidden Print Template -->
    <div id="print-view">
        <div class="print-container">
            <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" class="watermark" alt="Watermark">

            @php
            if (!function_exists('getAmountInWordsPrint')) {
                function getAmountInWordsPrint($amount) {
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
                        Total In Words : <span>{{ getAmountInWordsPrint($order->grand_total) }}</span>
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
        })
    </script>
@endpush
