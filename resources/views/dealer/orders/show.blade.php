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
                        <li class="breadcrumb-item"><a href="{{ route('dealer.orders.index') }}"> My Orders</a></li>
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
            <!-- Invoice -->
            <div class="col-xl-9 col-md-8 col-12">
                <div class="card invoice-preview-card">
                    <div class="card-body invoice-padding pb-0">
                        <!-- Header starts -->
                        <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                            <div>
                                <div class="logo-wrapper">
                                    <h3 class="text-primary invoice-logo">
                                        <img src="{{ asset('images/nozor_logo.png') }}" alt="Logo" style="width: 180px;" class="mr-25">
                                    </h3>
                                </div>
                                <p class="card-text mb-0"><strong>Status:</strong> <span class="badge badge-light-primary">{{ ucfirst($order->status) }}</span></p>
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
                                <h6 class="mb-2">Your Details:</h6>
                                <h6 class="mb-25">Shop Name: {{ $order->dealer->shop_name ?? 'N/A' }}</h6>
                                <p class="card-text mb-25">Name: {{ $order->dealer->name ?? 'N/A' }}</p>
                                <p class="card-text mb-25">Phone: {{ $order->dealer->phone ?? 'N/A' }}</p>
                                <p class="card-text mb-25">Email: {{ $order->dealer->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-xl-6 p-2 border-left">
                                <h6 class="mb-2">Delivery Details:</h6>
                                <p class="card-text mb-25">
                                    <strong>Status:</strong>
                                </p>
                                <p class="card-text mb-25"><strong>Details:</strong> </p>
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

            <!-- Invoice Actions -->
            <div class="col-xl-3 col-md-4 col-12 invoice-actions mt-md-0 mt-2">
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-primary btn-block mb-75" onclick="window.print()">
                            Print Order
                        </button>
                        <a href="{{ route('dealer.orders.index') }}" class="btn btn-outline-secondary btn-block">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Invoice Actions -->
        </div>
    </section>

    <!-- Hidden Print Template -->
    <div id="print-view" class="d-none">
        <div class="print-container" style="font-family: 'Montserrat', Helvetica, Arial, serif; color: #5e5873; background: #fff; padding: 40px; width: 100%;">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <img src="{{ asset('images/nozor_logo.png') }}" alt="Logo" style="width: 160px;" class="mb-2">
                </div>
                <div class="text-right">
                    <h1 class="font-weight-bold mb-0" style="color: #7367f0; font-size: 32px;">INVOICE</h1>
                    <p class="mb-0"><strong>#{{ $order->order_number }}</strong></p>
                    <p class="mb-0" style="font-size: 13px;">Order Date: {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') : '' }}</p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <p class="mb-0" style="font-size: 11px; color: #b9b9c3; text-transform: uppercase; font-weight: bold;">Invoice To (Dealer):</p>
                    <p class="mb-0" style="font-size: 14px;"><strong>{{ $order->dealer->shop_name ?? 'N/A' }}</strong></p>
                    <p class="mb-0" style="font-size: 13px;">{{ $order->dealer->name ?? '' }}</p>
                    <p class="mb-0" style="font-size: 13px;">{{ $order->dealer->phone ?? '' }}</p>
                </div>
                <div class="col-6 text-right">
                    <p class="mb-0" style="font-size: 11px; color: #b9b9c3; text-transform: uppercase; font-weight: bold;">Order Details:</p>
                    <p class="mb-0" style="font-size: 14px;">Status: <strong>{{ strtoupper(str_replace('_', ' ', $order->status)) }}</strong></p>
                </div>
            </div>

            <!-- Table -->
            <table class="table mb-4" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f3f2f7;">
                        <th class="py-1 px-2 text-left" style="font-size: 11px; color: #5e5873; text-transform: uppercase; width: 45%;">Product</th>
                        <th class="py-1 px-2 text-left" style="font-size: 11px; color: #5e5873; text-transform: uppercase; width: 15%;">Rate</th>
                        <th class="py-1 px-2 text-center" style="font-size: 11px; color: #5e5873; text-transform: uppercase; width: 15%;">Qty</th>
                        <th class="py-1 px-2 text-right" style="font-size: 11px; color: #5e5873; text-transform: uppercase; width: 25%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td class="py-2 px-2" style="border-bottom: 1px solid #ebe9f1; font-size: 14px;">
                            {{ $item->product->name ?? '-' }}
                            @if($item->product && $item->product->is_manufacturer == 1)
                                <span style="font-size: 10px; background: #e0f2fe; color: #0284c7; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">Mfg</span>
                            @endif
                        </td>
                        <td class="py-2 px-2" style="border-bottom: 1px solid #ebe9f1; font-size: 14px;">৳{{ number_format($item->price, 2) }}</td>
                        <td class="py-2 px-2 text-center" style="border-bottom: 1px solid #ebe9f1; font-size: 14px;">{{ $item->qty }}</td>
                        <td class="py-2 px-2 text-right" style="border-bottom: 1px solid #ebe9f1; font-size: 14px;">৳{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row align-items-start">
                <div class="col-7">
                    <p class="mb-0" style="font-weight: bold; color: #5e5873; font-size: 14px;">Notes:</p>
                    <p style="color: #b9b9c3; font-size: 13px; margin-bottom: 8px;">{{ $order->note ?: 'No special notes provided.' }}</p>
                </div>
                <div class="col-5">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color: #b9b9c3; font-size: 14px;">Subtotal:</span>
                        <span style="color: #b9b9c3; font-size: 14px;">৳{{ number_format($order->sub_total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color: #b9b9c3; font-size: 14px;">Discount:</span>
                        <span style="color: #b9b9c3; font-size: 14px;">৳{{ number_format($order->discount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color: #b9b9c3; font-size: 14px;">Carrying:</span>
                        <span style="color: #b9b9c3; font-size: 14px;">৳{{ number_format($order->carrying_charge, 2) }}</span>
                    </div>
                    <hr style="border: 0; border-top: 1px solid #ebe9f1; margin: 10px 0;">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-weight: bold; color: #5e5873; font-size: 18px;">Grand Total:</span>
                        <span style="font-weight: bold; color: #5e5873; font-size: 18px;">৳{{ number_format($order->grand_total, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1" style="color: #28c76f; font-size: 14px;">
                        <span>Paid Amount:</span>
                        <span>৳{{ number_format($order->paid, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between" style="color: #ea5455; font-size: 14px;">
                        <span>Due Amount:</span>
                        <span>৳{{ number_format($order->due, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
            background: #fff !important;
        }
        #print-view, #print-view * {
            visibility: visible;
        }
        #print-view {
            position: absolute;
            left: 0;
            top: 0;
            width: 100% !important;
            display: block !important;
        }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .d-flex { display: flex !important; }
        .justify-content-between { justify-content: space-between !important; }
        .align-items-start { align-items: flex-start !important; }
        .mb-0 { margin-bottom: 0 !important; }
        .mb-1 { margin-bottom: 0.25rem !important; }
        .mb-2 { margin-bottom: 0.5rem !important; }
        .mb-4 { margin-bottom: 1.5rem !important; }
        .row { display: flex !important; flex-wrap: wrap !important; }
        .col-6 { flex: 0 0 50% !important; max-width: 50% !important; }
        .col-7 { flex: 0 0 58.33% !important; max-width: 58.33% !important; }
        .col-5 { flex: 0 0 41.66% !important; max-width: 41.66% !important; }
        .font-weight-bold { font-weight: bold !important; }
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
