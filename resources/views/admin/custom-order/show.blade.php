@extends('layouts.admin')
@section('title', 'Order Details - ' . $order->order_number)

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Custom Order Details</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.custom-order.index') }}"> Custom Orders</a></li>
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
                                <p class="card-text mb-25"><strong>Style:</strong> {{ $order->style_number }}</p>
                                <p class="card-text mb-25 text-uppercase"><strong>Category:</strong> {{ $order->type }} ({{ $order->sleeve }})</p>
                                <p class="card-text mb-0"><strong>Status:</strong> <span class="badge badge-light-primary">{{ strtoupper(str_replace('_', ' ', $order->status)) }}</span></p>
                            </div>
                            <div class="mt-md-0 mt-2 text-md-right">
                                <h4 class="invoice-title">
                                    Order No <span class="invoice-number" style="font-size: 20px">#{{ $order->order_number }}</span>
                                </h4>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title text-right">Order Date:</p>
                                    <p class="invoice-date"><strong>{{ $order->order_date ? Carbon\Carbon::parse($order->order_date)->format('d M Y') : 'N/A' }}</strong></p>
                                </div>
                                <div class="invoice-date-wrapper">
                                    <p class="invoice-date-title text-right">Delivery Date:</p>
                                    <p class="invoice-date"><strong> {{ $order->delivery_date ? Carbon\Carbon::parse($order->delivery_date)->format('d M Y') : 'N/A' }} </strong></p>
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
                                <h6 class="mb-25">Name: {{ $order->customer->name ?? 'Walk-in Customer' }}</h6>
                                <p class="card-text mb-25">Phone: {{ $order->customer->phone ?? 'N/A' }}</p>
                                <p class="card-text mb-25">Email: {{ $order->customer->email ?? '' }}</p>
                                <p class="card-text mb-0">DeliveryType: {{ $order->order_type == 'home_delivery' ? 'Home Delivery' : 'Take Away' }}</p>
                            </div>
                            <div class="col-xl-4 p-0 mt-xl-0 mt-2">
                                <h6 class="mb-2">Production Status:</h6>
                                <table>
                                    <tbody>
                                    <tr>
                                        <td class="pr-1">Assigned Vendor:</td>
                                        <td><span class="font-weight-bold">{{ $order->vendor->name ?? 'Not Assigned' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="pr-1">Total Items:</td>
                                        <td>{{ $order->total_quantity }} Pcs</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Address and Contact ends -->

                    <!-- Invoice Description starts -->
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th class="py-1">Fabric Specification</th>
                                <th class="py-1">Category</th>
                                <th class="py-1">Rate</th>
                                <th class="py-1 text-center">Qty</th>
                                <th class="py-1 text-right">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td class="py-1">
                                    <p class="card-text font-weight-bold mb-25">{{ $item->fabric_name }}</p>
                                </td>
                                <td class="py-1">
                                    <span class="badge badge-light-secondary text-uppercase">{{ $item->type }} • {{ $item->sleeve }}</span>
                                </td>
                                <td class="py-1">
                                    <span class="font-weight-bold">৳{{ number_format($item->unit_price, 2) }}</span>
                                </td>
                                <td class="py-1 text-center">
                                    <span class="font-weight-bold">{{ $item->quantity }}</span>
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
                                    <span class="font-weight-bold">Customer Notes:</span><br>
                                    <span>{{ $order->customer_note ?: 'No special notes provided.' }}</span>
                                </p>
                                @if($order->vendor_note)
                                <p class="card-text">
                                    <span class="font-weight-bold">Vendor Notes:</span><br>
                                    <span class="text-danger">{{ $order->vendor_note }}</span>
                                </p>
                                @endif
                            </div>
                            <div class="col-md-6 d-flex justify-content-end order-md-2 order-1">
                                <div class="invoice-total-wrapper" style="width: 100%; max-width: 200px;">
                                    <div class="invoice-total-item d-flex justify-content-between">
                                        <p class="invoice-total-title">Subtotal:</p>
                                        <p class="invoice-total-amount">৳{{ number_format($order->sub_total, 2) }}</p>
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

                    @if($order->images->count())
                    <div class="card-body invoice-padding pt-0 pb-2">
                        <h6 class="mb-2">Attachments:</h6>
                        <div class="row">
                            @foreach($order->images as $img)
                            <div class="col-md-3 mb-1">
                                <a href="{{ asset($img->image) }}" target="_blank">
                                    <img src="{{ asset($img->image) }}" class="img-fluid rounded border" style="height: 150px; width: 100%; object-fit: cover;">
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @else
                    <div class="card-body invoice-padding pt-0">
                        <div class="row">
                            <div class="col-12">
                                <span class="font-weight-bold text-muted">No design attachments provided.</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <!-- /Invoice -->

            <!-- Invoice Actions -->
            <div class="col-xl-3 col-md-4 col-12 invoice-actions mt-md-0 mt-2">
                <div class="card">
                    <div class="card-body">
{{--                        <a href="javascript:void(0)" class="btn btn-primary btn-block mb-75 disabled">--}}
{{--                            Send Order Details--}}
{{--                        </a>--}}
                        <a href="{{ route('admin.custom-order.export-pdf', $order->id) }}" class="btn btn-primary btn-block mb-75">Download PDF</a>
                        <button class="btn btn-outline-secondary btn-block mb-75" onclick="window.print()">
                            Print
                        </button>
                        @if($order->status != 'delivered')
                        <a class="btn btn-outline-secondary btn-block mb-75" href="{{ route('admin.custom-order.edit', $order->id) }}"> Edit Order </a>
                        @endif
                        <a href="{{ route('admin.custom-order.index') }}" class="btn btn-success btn-block">
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
                    <p class="mb-0" style="font-size: 11px; color: #b9b9c3; text-transform: uppercase; font-weight: bold;">Invoice To:</p>
                    <p class="mb-0" style="font-size: 14px;"><strong>{{ $order->customer->name ?? 'Walk-in Customer' }}</strong></p>
                    <p class="mb-0" style="font-size: 13px;">{{ $order->customer->addresses->first()->address ?? 'Address not provided' }}</p>
                    <p class="mb-0" style="font-size: 13px;">{{ $order->customer->phone ?? '' }}</p>
                </div>
                <div class="col-6 text-right">
                    <p class="mb-0" style="font-size: 11px; color: #b9b9c3; text-transform: uppercase; font-weight: bold;">Order Details:</p>
                    <p class="mb-0" style="font-size: 14px;">Style: <strong>{{ $order->style_number }}</strong></p>
                    <p class="mb-0" style="font-size: 13px;">Type: {{ strtoupper($order->type) }}</p>
                </div>
            </div>

            <!-- Table -->
            <table class="table mb-4" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f3f2f7;">
                        <th class="py-1 px-2 text-left" style="font-size: 11px; color: #5e5873; text-transform: uppercase;">Fabric Specification</th>
                        <th class="py-1 px-2 text-left" style="font-size: 11px; color: #5e5873; text-transform: uppercase;">Category</th>
                        <th class="py-1 px-2 text-left" style="font-size: 11px; color: #5e5873; text-transform: uppercase;">Rate</th>
                        <th class="py-1 px-2 text-center" style="font-size: 11px; color: #5e5873; text-transform: uppercase;">Qty</th>
                        <th class="py-1 px-2 text-right" style="font-size: 11px; color: #5e5873; text-transform: uppercase;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td class="py-2 px-2" style="border-bottom: 1px solid #ebe9f1; font-size: 14px;">{{ $item->fabric_name }}</td>
                        <td class="py-2 px-2" style="border-bottom: 1px solid #ebe9f1;">
                            <span style="background-color: #f3f2f7; color: #82868b; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase;">
                                {{ $item->type }} • {{ $item->sleeve }}
                            </span>
                        </td>
                        <td class="py-2 px-2" style="border-bottom: 1px solid #ebe9f1; font-size: 14px;">৳{{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-2 px-2 text-center" style="border-bottom: 1px solid #ebe9f1; font-size: 14px;">{{ $item->quantity }}</td>
                        <td class="py-2 px-2 text-right" style="border-bottom: 1px solid #ebe9f1; font-size: 14px;">৳{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary Section from Image -->
            <div class="row align-items-start">
                <div class="col-7">
                    <p class="mb-0" style="font-weight: bold; color: #5e5873; font-size: 14px;">Customer Notes:</p>
                    <p style="color: #b9b9c3; font-size: 13px;">{{ $order->customer_note ?: 'No special notes provided.' }}</p>
                </div>
                <div class="col-5">
                    <div class="d-flex justify-content-between mb-1">
                        <span style="color: #b9b9c3; font-size: 14px;">Subtotal:</span>
                        <span style="color: #b9b9c3; font-size: 14px;">৳{{ number_format($order->sub_total, 2) }}</span>
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
