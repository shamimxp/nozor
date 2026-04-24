@extends('layouts.admin')
@section('title', 'POS Order Details - #' . $order->order_number)
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card invoice-preview-card">
            <div class="card-body invoice-padding pb-0">
                <!-- Header -->
                <div class="d-flex justify-content-between flex-md-row flex-column invoice-spacing mt-0">
                    <div>
                        <div class="logo-wrapper">
                            <h3 class="text-primary invoice-logo">NOZOR</h3>
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
            <div class="card-body invoice-padding pt-0">
                <div class="row invoice-spacing">
                    <div class="col-xl-8 p-0">
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
                                    <td class="pr-1">Method:</td>
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
    <div class="col-md-3 d-print-none">
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
    @media print {
        .invoice-preview-card {
            box-shadow: none !important;
            border: none !important;
        }
        .invoice-padding {
            padding: 0 !important;
        }
        .main-menu, .header-navbar, .footer, .btn, .d-print-none, .breadcrumb-wrapper {
            display: none !important;
        }
        .content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
        .card {
            border: none !important;
        }
    }
</style>
@endpush
