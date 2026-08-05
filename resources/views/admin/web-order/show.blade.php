@extends('layouts.admin')
@section('title', 'Order Details - ' . $order->invoice_no)

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Web Order Details</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.web-order.index') }}"> Web Orders</a></li>
                        <li class="breadcrumb-item active">{{ $order->invoice_no }}</li>
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
                                        <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="Logo" style="width: 180px;" class="mr-25">
                                    </h3>
                                </div>
                            </div>
                            <div class="mt-md-0 mt-2 text-md-right">
                                <h4 class="invoice-title">
                                    Invoice No <span class="invoice-number" style="font-size: 20px">#{{ $order->invoice_no }}</span>
                                </h4>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title text-right">Order Date:</p>
                                    <p class="invoice-date"><strong>{{ $order->created_at->format('d M Y, h:i A') }}</strong></p>
                                </div>
                            </div>
                        </div>
                        <!-- Header ends -->
                    </div>

                    <hr class="invoice-spacing" />

                    <!-- Address and Contact starts -->
                    <div class="card-body invoice-padding pt-0">
                        <div class="row invoice-spacing">
                            <div class="col-xl-8 p-0">
                                <h6 class="mb-2">Customer Details:</h6>
                                <h6 class="mb-25">Name: {{ $order->address->name ?? 'N/A' }}</h6>
                                <p class="card-text mb-25">Phone: {{ $order->address->phone ?? 'N/A' }}</p>
                                <p class="card-text mb-25">Address: {{ $order->address->address ?? 'N/A' }}</p>
                                @if(!empty($order->address->note))
                                <p class="card-text mb-0 text-danger">Note: {{ $order->address->note }}</p>
                                @endif
                            </div>
                            <div class="col-xl-4 p-0 mt-xl-0 mt-2 text-xl-right">

                                <div class="col-xl-8 p-0">
                                    <h6 class="mb-2">Payment Details:</h6>
                                    <h6 class="mb-25">Payment Method: {{ strtoupper($order->payment_method) }}</h6>
                                    <p class="card-text mb-25">Payment Status:
                                        @if($order->status == 'delivered')
                                            <span class="font-weight-bold text-success">PAID</span>
                                        @else
                                            <span class="font-weight-bold text-danger">DUE</span>
                                        @endif
                                    </p>
                                    <p class="card-text mb-0"><strong>Status:</strong> <span class="badge badge-light-primary">{{ strtoupper(str_replace('_', ' ', $order->status)) }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Address and Contact ends -->

                    <!-- Invoice Description starts -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="py-1">Product</th>
                                <th class="py-1 text-center">Qty</th>
                                <th class="py-1 text-right">Price</th>
                                <th class="py-1 text-right">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td class="py-1">
                                    <p class="card-text font-weight-bold mb-25">
                                        {{ $item->product->name ?? 'Deleted Product' }}
                                        @if($item->size || $item->color)
                                            (<span class="text-bold" style="font-size: 0.85em;">
                                                @if($item->size)Size: {{ $item->size }}@endif
                                                @if($item->size && $item->color), @endif
                                                @if($item->color)Color: {{ $item->color }}@endif
                                            </span>)
                                        @endif
                                    </p>
                                </td>
                                <td class="py-1 text-center">
                                    <span class="font-weight-bold">{{ $item->quantity }}</span>
                                </td>
                                <td class="py-1 text-right">
                                    <span class="font-weight-bold">৳{{ number_format($item->price, 2) }}</span>
                                </td>
                                <td class="py-1 text-right">
                                    <span class="font-weight-bold">৳{{ number_format($item->price * $item->quantity, 2) }}</span>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="card-body invoice-padding pb-0 mt-2">
                        <div class="row invoice-sales-total-wrapper">
                            <div class="col-md-6 order-md-1 order-2 mt-md-0 mt-3">
                            </div>
                            <div class="col-md-6 d-flex justify-content-end order-md-2 order-1">
                                <div class="invoice-total-wrapper" style="width: 100%; max-width: 250px;">
                                    <div class="invoice-total-item d-flex justify-content-between">
                                        <p class="invoice-total-title">Subtotal:</p>
                                        <p class="invoice-total-amount">৳{{ number_format($order->subtotal, 2) }}</p>
                                    </div>
                                    <div class="invoice-total-item d-flex justify-content-between">
                                        <p class="invoice-total-title">Shipping:</p>
                                        <p class="invoice-total-amount">৳{{ number_format($order->shipping_charge, 2) }}</p>
                                    </div>
                                    <div class="invoice-total-item d-flex justify-content-between">
                                        <p class="invoice-total-title">Discount:</p>
                                        <p class="invoice-total-amount">-৳{{ number_format($order->discount, 2) }}</p>
                                    </div>
                                    <hr class="my-50" />
                                    <div class="invoice-total-item d-flex justify-content-between">
                                        <p class="invoice-total-title font-weight-bolder h5">Grand Total:</p>
                                        <p class="invoice-total-amount font-weight-bolder h5">৳{{ number_format($order->total, 2) }}</p>
                                    </div>
{{--                                    <hr class="my-50" />--}}
{{--                                    <div class="invoice-total-item d-flex justify-content-between mt-1">--}}
{{--                                        <p class="invoice-total-title {{ $order->status == 'delivered' ? 'text-success' : 'text-danger' }}">Payment Status:</p>--}}
{{--                                        <p class="invoice-total-amount font-weight-bolder {{ $order->status == 'delivered' ? 'text-success' : 'text-danger' }}">--}}
{{--                                            {{ $order->status == 'delivered' ? 'PAID' : 'DUE' }}--}}
{{--                                        </p>--}}
{{--                                    </div>--}}
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
                        <a href="{{ route('admin.web-order.export-pdf', $order->id) }}" class="btn btn-primary btn-block mb-75">Download PDF</a>
                        <button class="btn btn-outline-secondary btn-block mb-75" onclick="window.print()">
                            Print
                        </button>
                        @if($order->status === 'pending')
                        <a class="btn btn-outline-secondary btn-block mb-75" href="{{ route('admin.web-order.edit', $order->id) }}"> Edit Order </a>
                        @endif
                        <a href="{{ route('admin.web-order.index') }}" class="btn btn-success btn-block">
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
            <!-- /Invoice Actions -->
        </div>
    </section>

    <!-- Hidden Print Template (Matching User Image Design) -->
    <div id="print-view" class="d-none">
        <div class="print-container" style="font-family: 'Montserrat', Helvetica, Arial, serif; color: #5e5873; background: #fff; padding: 40px; width: 100%;">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" alt="Logo" style="width: 160px;" class="mb-2">
                </div>
                <div class="text-right">
                    <h1 class="font-weight-bold mb-0" style="color: #7367f0; font-size: 32px;">INVOICE</h1>
                    <p class="mb-0"><strong>#{{ $order->invoice_no }}</strong></p>
                    <p class="mb-0" style="font-size: 13px;">Date: {{ $order->created_at->format('d/m/Y') }}</p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <p class="mb-0" style="font-size: 11px; color: #b9b9c3; text-transform: uppercase; font-weight: bold;">Invoice To:</p>
                    <p class="mb-0" style="font-size: 14px;"><strong>{{ $order->address->name ?? 'N/A' }}</strong></p>
                    <p class="mb-0" style="font-size: 13px;">{{ $order->address->address ?? 'N/A' }}</p>
                    <p class="mb-0" style="font-size: 13px;">{{ $order->address->phone ?? 'N/A' }}</p>
                </div>
                <div class="col-6 text-right">
                    <p class="mb-0" style="font-size: 11px; color: #b9b9c3; text-transform: uppercase; font-weight: bold;">Payment Information:</p>
                    <p class="mb-0" style="font-size: 14px;">Method: <strong>{{ strtoupper($order->payment_method) }}</strong></p>
                    <p class="mb-0" style="font-size: 14px;">Status: <strong style="color: {{ $order->status == 'delivered' ? '#28c76f' : '#ea5455' }}">{{ $order->status == 'delivered' ? 'PAID' : 'DUE' }}</strong></p>
                </div>
            </div>

            <!-- Table -->
            <table class="table mb-4" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f3f2f7;">
                        <th class="py-1 px-2 text-left" style="font-size: 11px; color: #5e5873; text-transform: uppercase;">Product Name</th>
                        <th class="py-1 px-2 text-center" style="font-size: 11px; color: #5e5873; text-transform: uppercase;">Qty</th>
                        <th class="py-1 px-2 text-right" style="font-size: 11px; color: #5e5873; text-transform: uppercase;">Price</th>
                        <th class="py-1 px-2 text-right" style="font-size: 11px; color: #5e5873; text-transform: uppercase;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td class="py-2 px-2" style="border-bottom: 1px solid #ebe9f1; font-size: 14px;">
                            {{ $item->product->name ?? 'Deleted Product' }}
                            @if($item->size || $item->color)
                                <span style="color: #82868b; font-size: 0.85em;">
                                    (@if($item->size)Size: {{ $item->size }}@endif
                                    @if($item->size && $item->color), @endif
                                    @if($item->color)Color: {{ $item->color }}@endif
                                </span>
                            @endif
                        </td>
                        <td class="py-2 px-2 text-center" style="border-bottom: 1px solid #ebe9f1; font-size: 14px;">{{ $item->quantity }}</td>
                        <td class="py-2 px-2 text-right" style="border-bottom: 1px solid #ebe9f1; font-size: 14px;">৳{{ number_format($item->price, 2) }}</td>
                        <td class="py-2 px-2 text-right" style="border-bottom: 1px solid #ebe9f1; font-size: 14px;">৳{{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary Section -->
            <div class="row align-items-start">
                <div class="col-7">
                    <p class="mb-0" style="font-weight: bold; color: #5e5873; font-size: 14px;">Customer Notes:</p>
                    <p style="color: #b9b9c3; font-size: 13px;">{{ $order->address->note ?: 'No special notes provided.' }}</p>
                </div>
                <div class="col-5">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color: #b9b9c3; font-size: 14px;">Subtotal:</span>
                        <span style="color: #b9b9c3; font-size: 14px;">৳{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color: #b9b9c3; font-size: 14px;">Shipping Charge:</span>
                        <span style="color: #b9b9c3; font-size: 14px;">৳{{ number_format($order->shipping_charge, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color: #b9b9c3; font-size: 14px;">Discount:</span>
                        <span style="color: #b9b9c3; font-size: 14px;">-৳{{ number_format($order->discount, 2) }}</span>
                    </div>
                    <hr style="border: 0; border-top: 1px solid #ebe9f1; margin: 10px 0;">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-weight: bold; color: #5e5873; font-size: 18px;">Grand Total:</span>
                        <span style="font-weight: bold; color: #5e5873; font-size: 18px;">৳{{ number_format($order->total, 2) }}</span>
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
