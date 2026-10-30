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
                    <div class="card-body d-flex flex-wrap">
                        <a href="{{ route('admin.dealer-order.export-pdf', $order->id) }}" class="btn btn-primary mr-1 mb-1">Download PDF</a>
                        <button class="btn btn-outline-secondary mr-1 mb-1" onclick="window.print()">
                            Print
                        </button>
                        @if($order->status != 'delivered' && $order->status != 'cancelled')
                            <a class="btn btn-outline-secondary mr-1 mb-1" href="{{ route('admin.dealer-order.edit', $order->id) }}"> Edit Order </a>
                        @endif
                        <a href="{{ route('admin.dealer-order.index') }}" class="btn btn-success mb-1">
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
                                <th class="py-1" style="width: 75px;">Product Image</th>
                                <th class="py-1" style="width: 75px;">Manufacture Image</th>
                                <th class="py-1">Product</th>
                                <th class="py-1">Rate</th>
                                <th class="py-1 text-center">Qty</th>
                                <th class="py-1 text-right">Total</th>
                                <th class="py-1">Note</th>
                                <th class="py-1 text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                if (!isset($assignedProductIds)) {
                                    $refNumber = trim($order->order_number ?? '');
                                    $refVariants = array_unique(array_filter([$refNumber, '#' . ltrim($refNumber, '#'), ltrim($refNumber, '#')]));
                                    $assignedProductIds = !empty($refVariants) ? \App\Models\ManufactureOrderItem::whereIn('reff_invoice', $refVariants)
                                        ->pluck('product_id')
                                        ->merge(
                                            \App\Models\Manufacture::whereIn('reff_invoice', $refVariants)
                                                ->whereNotNull('product_id')
                                                ->pluck('product_id')
                                        )
                                        ->filter()
                                        ->unique()
                                        ->values()
                                        ->toArray() : [];
                                }
                            @endphp
                            @foreach($order->items as $item)
                            <tr>
                                <td class="py-1">
                                    @if($item->product && $item->product->featured_image)
                                        @php $pImg = asset(config('imagepath.product') . $item->product->featured_image); @endphp
                                        <img src="{{ $pImg }}" alt="Product Image" width="40" height="40" 
                                            class="img-preview-trigger"
                                            data-preview-url="{{ $pImg }}"
                                            data-preview-title="{{ $item->product->name ?? 'Product' }} (Product Image)"
                                            title="Click to zoom image"
                                            style="border-radius: 4px; object-fit: cover; border: 1px solid #e5e7eb;" 
                                            onerror="this.src='{{ asset('images/no-image.png') }}'">
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="No Image" width="40" height="40" style="border-radius: 4px; object-fit: cover;">
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
                                        <img src="{{ $mfgImg }}" alt="Manufacture Image" width="40" height="40" 
                                            class="img-preview-trigger"
                                            data-preview-url="{{ $mfgImg }}"
                                            data-preview-title="{{ $item->product->name ?? 'Product' }} (Manufacture Image)"
                                            title="Click to zoom image"
                                            style="border-radius: 4px; object-fit: cover; border: 1px solid #e5e7eb;" 
                                            onerror="this.src='{{ asset('images/no-image.png') }}'">
                                    @else
                                        <span class="badge badge-light-secondary font-weight-normal" style="font-size:0.75rem;">N/A</span>
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
                                <td class="py-1">
                                    {{ $item->note }}
                                </td>
                                <td class="py-1 text-center">
                                    @php
                                        $isAssigned = in_array($item->product_id, $assignedProductIds ?? []);
                                    @endphp
                                    @if($item->product && $item->product->is_manufacturer == 1 && !$isAssigned)
                                        @php
                                            $imgs = [];
                                            if ($item->product->recipe && !empty($item->product->recipe->manufacture_images)) {
                                                foreach($item->product->recipe->manufacture_images as $mImg) {
                                                    $imgs[] = asset($mImg);
                                                }
                                            }
                                            if (empty($imgs)) {
                                                $imgs[] = asset('images/no-image.png');
                                            }
                                            $recipe = $item->product ? $item->product->recipe : null;
                                            $costSetting = $recipe && $recipe->costSetting ? $recipe->costSetting : null;
                                            
                                            $bodyPartPrice = 0;
                                            $finishingPartPrice = 0;
                                            $costSettingId = null;
                                            $costTitle = null;

                                            if ($costSetting) {
                                                $bodyPartPrice = (float) $costSetting->body_charge;
                                                $finishingPartPrice = (float) $costSetting->finishing_charge;
                                                $costSettingId = $costSetting->id;
                                                $costTitle = $costSetting->cost_title;
                                            } elseif ($recipe) {
                                                $bodyPartPrice = (float) ($recipe->body_charge ?? 0);
                                                $finishingPartPrice = (float) ($recipe->finishing_charge ?? 0);
                                            }
                                        @endphp
                                        <button class="btn btn-sm btn-danger btn-manufacture mb-25" 
                                            data-product_id="{{ $item->product_id }}"
                                            data-product_name="{{ $item->product->name }}"
                                            data-images="{{ json_encode($imgs) }}"
                                            data-qty="{{ $item->confirm_qty ?? $item->qty }}"
                                            data-body-price="{{ $bodyPartPrice }}"
                                            data-finishing-price="{{ $finishingPartPrice }}"
                                            data-cost-setting-id="{{ $costSettingId }}"
                                            data-cost-title="{{ $costTitle }}">
                                            Manufac. order
                                        </button>
                                    @endif
                                    <button class="btn btn-sm btn-secondary btn-edit-item mb-25" data-id="{{ $item->id }}" data-qty="{{ $item->confirm_qty ?? $item->qty }}" data-note="{{ $item->note }}">Edit</button>
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

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="editItemForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="item_id" id="edit_item_id">
                    <div class="form-group">
                        <label>Confirm Qty</label>
                        <input type="number" name="confirm_qty" id="edit_confirm_qty" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Note</label>
                        <textarea name="note" id="edit_note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manufacture Order Modal -->
<div class="modal fade" id="manufactureModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="manufactureForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="manufac_modal_title">Body Part Order</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="text-center mb-1" id="manufac_images_container" style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                    </div>
                    <p class="mb-1 font-weight-bold">Product Name : <span id="manufac_product_name" class="text-primary"></span></p>
                    
                    <input type="hidden" name="dealer_id" value="{{ $order->dealer_id }}">
                    <input type="hidden" name="items[0][product_id]" id="manufac_product_id">
                    <input type="hidden" name="items[0][cost_setting_id]" id="manufac_cost_setting_id">
                    <input type="hidden" name="items[0][cost_title]" id="manufac_cost_title">
                    <input type="hidden" name="items[0][unit_price]" id="manufac_unit_price">
                    <input type="hidden" name="items[0][body_part_price]" id="manufac_body_price">
                    <input type="hidden" name="items[0][finishing_part_price]" id="manufac_finishing_price">
                    <input type="hidden" name="items[0][reff_invoice]" id="manufac_reff_invoice" value="{{ $order->order_number }}">
                    <input type="hidden" name="items[0][note]" id="manufac_item_note">
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Manufacture Part:</label>
                        <select name="items[0][part_type]" id="manufac_part" class="form-control">
                            <option value="body">body-part</option>
                            <option value="finishing">finishing-part</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Select Worker <span class="text-danger">*</span></label>
                        <select name="worker_id" class="form-control" required>
                            <option value="">Select Worker</option>
                            @if(isset($workers))
                            @foreach($workers as $worker)
                                <option value="{{ $worker->id }}">{{ $worker->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Manufacture Price (per pc):</label>
                        <input type="text" id="manufac_price" class="form-control font-weight-bold text-dark bg-light" readonly>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Manufacture Quantity <span class="text-danger">*</span>:</label>
                        <input type="number" name="items[0][qty]" id="manufac_qty" class="form-control font-weight-bold" required min="1">
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Total Price:</label>
                        <input type="text" id="manufac_total" class="form-control font-weight-bolder text-primary bg-light" readonly>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Dealer Name</label>
                        <input type="text" class="form-control" value="{{ $order->dealer->shop_name ?? ($order->dealer->name ?? '') }}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Dealer Confirm Invoice Number</label>
                        <input type="text" class="form-control" value="{{ $order->order_number }}" readonly>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Message / Note:</label>
                        <textarea name="note" id="manufac_note" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
                    </div>
                    <div class="form-group custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" name="is_confirm" id="manufac_is_confirm" value="1" checked>
                        <label class="custom-control-label font-weight-bold" for="manufac_is_confirm">Is Confirm</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

        // Edit Item Modal
        $('.btn-edit-item').on('click', function() {
            $('#edit_item_id').val($(this).data('id'));
            $('#edit_confirm_qty').val($(this).data('qty'));
            $('#edit_note').val($(this).data('note'));
            $('#editItemModal').modal('show');
        });

        $('#editItemForm').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.dealer-order.item.update') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        location.reload();
                    }
                },
                error: function(err) {
                    toastr.error('Error updating item.');
                }
            });
        });

        // Manufacture Modal
        let bodyPrice = 0;
        let finishingPrice = 0;

        function calculateManufacTotal() {
            let qty = parseFloat($('#manufac_qty').val()) || 0;
            let part = $('#manufac_part').val();
            let price = part === 'body' ? bodyPrice : finishingPrice;
            
            $('#manufac_price').val(price);
            $('#manufac_unit_price').val(price);
            $('#manufac_total').val((qty * price).toFixed(2));

            if (part === 'body') {
                $('#manufac_modal_title').text('Body Part Order');
            } else if (part === 'finishing') {
                $('#manufac_modal_title').text('Finishing Part Order');
            } else {
                $('#manufac_modal_title').text('Manufacture Order');
            }
        }

        $('.btn-manufacture').on('click', function() {
            let prodId = $(this).data('product_id');
            bodyPrice = parseFloat($(this).data('body-price')) || 0;
            finishingPrice = parseFloat($(this).data('finishing-price')) || 0;

            $('#manufac_product_id').val(prodId);
            $('#manufac_product_name').text($(this).data('product_name'));
            $('#manufac_cost_setting_id').val($(this).data('cost-setting-id'));
            $('#manufac_cost_title').val($(this).data('cost-title'));
            $('#manufac_body_price').val(bodyPrice);
            $('#manufac_finishing_price').val(finishingPrice);
            $('#manufac_qty').val($(this).data('qty'));

            let images = $(this).data('images');
            let imagesHtml = '';
            if (images && images.length > 0) {
                images.forEach(function(imgSrc) {
                    imagesHtml += '<img src="' + imgSrc + '" style="max-height: 150px; border-radius: 4px; object-fit: contain; border: 1px solid #ddd;">';
                });
            }
            $('#manufac_images_container').html(imagesHtml);

            // If prices are 0, fetch live from server as fallback
            if (bodyPrice === 0 && finishingPrice === 0) {
                $.get("{{ route('admin.manufacture.get-prices') }}", { product_id: prodId }, function(res) {
                    if (res) {
                        bodyPrice = parseFloat(res.body_part_price) || 0;
                        finishingPrice = parseFloat(res.finishing_part_price) || 0;
                        if (res.cost_setting_id) $('#manufac_cost_setting_id').val(res.cost_setting_id);
                        if (res.cost_title) $('#manufac_cost_title').val(res.cost_title);
                        $('#manufac_body_price').val(bodyPrice);
                        $('#manufac_finishing_price').val(finishingPrice);
                        calculateManufacTotal();
                    }
                });
            }

            calculateManufacTotal();
            $('#manufactureModal').modal('show');
        });

        $('#manufac_qty, #manufac_part').on('change keyup input', calculateManufacTotal);

        $('#manufactureForm').on('submit', function(e) {
            e.preventDefault();
            calculateManufacTotal();
            $('#manufac_item_note').val($('#manufac_note').val());

            const $btn = $(this).find('button[type="submit"]');
            const originalText = $btn.text();
            $btn.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('admin.manufacture.store') }}",
                type: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}'
                },
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.success);
                        $('#manufactureModal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else if (res.error) {
                        toastr.error(res.error);
                        $btn.prop('disabled', false).text(originalText);
                    }
                },
                error: function(err) {
                    let msg = 'Error creating manufacture order.';
                    if(err.responseJSON && err.responseJSON.errors) {
                        msg = Object.values(err.responseJSON.errors)[0][0];
                    } else if(err.responseJSON && err.responseJSON.message) {
                        msg = err.responseJSON.message;
                    }
                    toastr.error(msg);
                    $btn.prop('disabled', false).text(originalText);
                }
            });
        });
    </script>
@endpush
