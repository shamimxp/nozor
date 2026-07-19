@extends('layouts.admin')
@section('title', 'POS Order Details - #' . $order->order_number)
@section('content')
<div class="row">
    <div class="col-10">
        <div class="card invoice-preview-card">
            <div class="card-body invoice-padding pb-4">
                <!-- Header -->
                <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                    <div>
                        <div class="logo-wrapper mb-4">
                            <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="Logo" style="width: 140px;">
                        </div>
                        <p class="card-text mb-25">Order Number: <strong>#{{ $order->order_number }}</strong></p>
                        <p class="card-text mb-25">Order Date: <strong>{{ date('d M, Y', strtotime($order->order_date)) }}</strong></p>
                    </div>
                    <div class="mt-md-0 mt-2">
                        <h4 class="invoice-title">
                            Status: <span class="badge badge-light-{{ $order->order_status == 'completed' ? 'success' : ($order->order_status == 'cancelled' ? 'danger' : 'warning') }} text-uppercase">{{ $order->order_status }}</span>
                        </h4>
                        <div class="invoice-date-wrapper">
                            <p class="invoice-date-title text-uppercase">Payment: <strong>{{ $order->payment_status }}</strong></p>
                        </div>
                    </div>
                </div>
                <!-- /Header -->
            </div>

            <hr class="invoice-spacing" />

            <!-- Address and Contact -->
            <div class="card-body invoice-padding pt-2">
                <div class="row invoice-spacing">
                    <div class="col-xl-8 p-2">
                        <h6 class="mb-2">Customer:</h6>
                        @if($order->customer)
                            <h6 class="mb-25">{{ $order->customer->name }}</h6>
                            <p class="card-text mb-25">{{ $order->customer->phone }}</p>
                            <p class="card-text mb-25">{{ $order->customer->email }}</p>
                        @else
                            <p class="card-text mb-25 text-muted">Walk-in Customer</p>
                        @endif
                    </div>
                    <div class="col-xl-4 p-0 mt-xl-0 mt-2">
                        <h6 class="mb-2">Payment Details:</h6>
                        <table>
                            <tbody>
                                <tr>
                                    <td class="pr-1">Payment Method:</td>
                                    <td><span class="font-weight-bold">{{ ucfirst($order->payment_method) }}</span></td>
                                </tr>
                                <tr>
                                    <td class="pr-1">Assisted by:</td>
                                    <td>{{ $order->creator->name ?? 'System' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- /Address and Contact -->

            <!-- Invoice Description -->
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="py-1">Product Description</th>
                            <th class="py-1">Unit Price</th>
                            <th class="py-1">Quantity</th>
                            <th class="py-1">Discount</th>
                            <th class="py-1">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td class="py-1">
                                <p class="card-text font-weight-bold mb-25">{{ $item->product_name }}</p>
                            </td>
                            <td class="py-1">
                                <span class="font-weight-bold">৳{{ number_format($item->unit_price, 2) }}</span>
                            </td>
                            <td class="py-1">
                                <span class="font-weight-bold">{{ $item->quantity }}</span>
                            </td>
                            <td class="py-1">
                                <span class="font-weight-bold">৳{{ number_format($item->discount, 2) }}</span>
                            </td>
                            <td class="py-1">
                                <span class="font-weight-bold">৳{{ number_format($item->subtotal, 2) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-body invoice-padding pb-0">
                <div class="row invoice-sales-total-wrapper">
                    <div class="col-md-6 order-md-1 order-2 mt-md-0 mt-3">
                        <p class="card-text mb-0">
                            <span class="font-weight-bold">Note:</span> {{ $order->note ?? 'N/A' }}
                        </p>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end order-md-2 order-1">
                        <div class="invoice-total-wrapper" style="min-width: 200px">
                            <div class="invoice-total-item d-flex justify-content-between">
                                <p class="invoice-total-title">Subtotal:</p>
                                <p class="invoice-total-amount">৳{{ number_format($order->payable_amount + $order->discount_amount, 2) }}</p>
                            </div>
                            <div class="invoice-total-item d-flex justify-content-between">
                                <p class="invoice-total-title">Discount:</p>
                                <p class="invoice-total-amount">-৳{{ number_format($order->discount_amount, 2) }}</p>
                            </div>
                            <hr class="my-50" />
                            <div class="invoice-total-item d-flex justify-content-between">
                                <p class="invoice-total-title font-weight-bold">Total Payable:</p>
                                <p class="invoice-total-amount font-weight-bold">৳{{ number_format($order->payable_amount, 2) }}</p>
                            </div>
                            <div class="invoice-total-item d-flex justify-content-between">
                                <p class="invoice-total-title">Paid Amount:</p>
                                <p class="invoice-total-amount">৳{{ number_format($order->paid_amount, 2) }}</p>
                            </div>
                            <div class="invoice-total-item d-flex justify-content-between">
                                <p class="invoice-total-title text-danger">Due Amount:</p>
                                <p class="invoice-total-amount text-danger">৳{{ number_format($order->due_amount, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Invoice Description -->

            <hr class="invoice-spacing" />

            <!-- Footer -->
            <div class="card-body invoice-padding pt-0">
                <div class="row">
                    <div class="col-12">
                        <span class="font-weight-bold">THANK YOU FOR YOUR BUSINESS!</span>
                    </div>
                </div>
            </div>
            <!-- /Footer -->
        </div>
    </div>
    <!-- Actions Sidebar -->
    <div class="col-md-2 d-print-none">
        <div class="card">
            <div class="card-body">
                <button class="btn btn-primary btn-block mb-75" onclick="window.print()">
                    <i data-feather="printer" class="mr-25"></i> Print
                </button>
                <a href="{{ route('admin.pos-order.export-pdf', $order->id) }}" class="btn btn-outline-secondary btn-block mb-75">
                    <i data-feather="download" class="mr-25"></i> Download PDF
                </a>
                @if($order->order_status != 'cancelled')
                <button class="btn btn-danger btn-block mb-75" id="cancelOrderBtn" data-id="{{ $order->id }}">
                    <i data-feather="x-circle" class="mr-25"></i> Cancel Order
                </button>
                @endif
                <a href="{{ route('admin.pos-order.index') }}" class="btn btn-outline-primary btn-block">
                    <i data-feather="corner-up-left" class="mr-25"></i> Back to List
                </a>
            </div>
        </div>
    </div>
</div>

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

            <div class="invoice-title">POS Invoice</div>

            <table class="info-table">
                <tr>
                    <td class="info-left">
                        Invoice No : {{ $order->order_number }}<br>
                        @if($order->customer)
                        Customer Name : {{ $order->customer->name }}<br>
                        Customer Phone No : {{ $order->customer->phone }}
                        @else
                        Customer Name : Walk-in Customer
                        @endif
                    </td>
                    <td class="info-right">
                        Order Date : {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d M Y') : date('d M Y') }}<br>
                        Payment Method: {{ ucfirst($order->payment_method) }}<br>
                        Payment Status: {{ ucfirst($order->payment_status) }}
                    </td>
                </tr>
            </table>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">SL</th>
                        <th style="width: 45%; text-align: left;">Product Name</th>
                        <th style="width: 10%;">Qty</th>
                        <th style="width: 15%;">Unit Price</th>
                        <th style="width: 10%;">Discount</th>
                        <th style="width: 15%;">Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                    <tr>
                        <td class="text-center"><b>{{ $index + 1 }}</b></td>
                        <td>{{ $item->product_name ?? '-' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">{{ number_format($item->unit_price, strpos($item->unit_price, '.') ? 2 : 0) }}</td>
                        <td class="text-center">{{ number_format($item->discount, strpos($item->discount, '.') ? 2 : 0) }}</td>
                        <td class="text-center">{{ number_format($item->subtotal, strpos($item->subtotal, '.') ? 2 : 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <table class="bottom-table">
                <tr>
                    <td class="in-words-col">
                        Total In Words : <span>{{ getAmountInWordsPrint($order->payable_amount) }}</span>
                    </td>
                    <td class="totals-col">
                        <table class="totals-table">
                            <tr>
                                <td>Subtotal :</td>
                                <td>{{ number_format($order->payable_amount + $order->discount_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Discount :</td>
                                <td>{{ number_format($order->discount_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Total Amount :</td>
                                <td>{{ number_format($order->payable_amount, 2) }}</td>
                            </tr>
                            <tr class="border-bottom">
                                <td>Received Amount :</td>
                                <td>{{ $order->paid_amount > 0 ? number_format($order->paid_amount, 2) : '0.0' }}</td>
                            </tr>
                            <tr>
                                <td>Due Amount:</td>
                                <td>{{ $order->due_amount > 0 ? number_format($order->due_amount, 2) : '0.0' }}</td>
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
@endsection

@push('scripts')
<script>
    $(function() {
        $('#cancelOrderBtn').on('click', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "This will cancel the order and RESTORE product stock!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route('admin.pos-order.cancel', ':id') }}".replace(':id', id), {
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            location.reload();
                        } else {
                            toastr.error(res.message);
                        }
                    });
                }
            });
        });
    });
</script>
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
        .invoice-preview-card,
        .d-print-none,
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
@endpush
