@extends('layouts.dealer')
@section('title', 'Request Detail')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0 text-primary font-weight-bolder" style="letter-spacing: 0.5px; border-left: 4px solid #7367f0; padding-left: 10px;">Request Detail</h2>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="invoice-preview-wrapper">
        <div class="row invoice-preview">
            <!-- Invoice -->
            <div class="col-xl-12 col-md-12 col-12">
                <div class="card invoice-preview-card bg-white shadow-sm" style="border-radius: 12px; border: 1px solid #f0f0f0;">
                    
                    <div class="card-body pb-0">
                        <div class="row">
                            <!-- Dealer Info -->
                            <div class="col-sm-6 col-12 mb-2">
                                <div class="d-flex align-items-start">
                                    <div class="avatar bg-light-info mr-2" style="border-radius: 50%;">
                                        <div class="avatar-content" style="width: 48px; height: 48px;">
                                            <i data-feather="user" class="text-info" style="width: 20px; height: 20px;"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bolder mb-50 text-uppercase text-dark" style="font-size: 0.85rem; letter-spacing: 0.5px;">Dealer</h6>
                                        <h5 class="font-weight-bolder mb-25 text-dark"><strong>Name:</strong> {{ $orderRequest->dealer->name ?? 'N/A' }}</h5>
                                        <p class="font-weight-bolder mb-25 text-dark"><strong>Shop:</strong> {{ $orderRequest->dealer->shop_name ?? 'N/A' }}</p>
                                        <p class="font-weight-bolder mb-25 text-dark"><strong>Address:</strong>  {{ $orderRequest->dealer->address ?? 'N/A' }}</p>
                                        <p class="font-weight-bolder mb-25 text-dark"><strong>Phone:</strong>  {{ $orderRequest->dealer->phone ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Request Info -->
                            <div class="col-sm-6 col-12 mb-2">
                                <div class="d-flex align-items-start">
                                    <div class="avatar bg-light-info mr-2" style="border-radius: 50%;">
                                        <div class="avatar-content" style="width: 48px; height: 48px;">
                                            <i data-feather="truck" class="text-info" style="width: 20px; height: 20px;"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bolder mb-50 text-uppercase text-secondary" style="font-size: 0.85rem; letter-spacing: 0.5px;">Request Info</h6>
                                        <p class="card-text mb-25 font-weight-bold text-muted">Status: 
                                            @php
                                                $badge = $orderRequest->status == 'pending' ? 'text-warning' : ($orderRequest->status == 'completed' ? 'text-success' : 'text-info');
                                            @endphp
                                            <span class="{{ $badge }} font-weight-bold">{{ ucfirst(str_replace('_', ' ', $orderRequest->status)) }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="invoice-spacing mt-0" />

                    <!-- Summary Table -->
                    <div class="card-body pb-0 pt-0">
                        <div class="table-responsive">
                            <table class="table table-bordered text-nowrap">
                                <tbody>
                                    <tr>
                                    <tr>
                                        <td class="font-weight-bolder text-uppercase text-dark" style="width: 30%; font-size: 0.85rem; letter-spacing: 0.5px; border-top: none;">Request Status</td>
                                        <td style="border-top: none;">
                                            @php
                                                $bgBadge = $orderRequest->status == 'pending' ? 'badge-light-warning' : ($orderRequest->status == 'completed' ? 'badge-light-success' : 'badge-light-info');
                                            @endphp
                                            <span class="badge badge-pill {{ $bgBadge }} font-weight-bold" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">{{ ucfirst(str_replace('_', ' ', $orderRequest->status)) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bolder text-uppercase text-dark" style="font-size: 0.85rem; letter-spacing: 0.5px;">Request Date</td>
                                        <td class="font-weight-bold text-dark">{{ $orderRequest->created_at->format('d M Y, h:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bolder text-uppercase text-dark" style="font-size: 0.85rem; letter-spacing: 0.5px;">Total Amount</td>
                                        <td class="font-weight-bold text-dark">{{ number_format($totalAmount, 2) }} Tk</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-body mt-2">
                        <!-- Items Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead style="background-color: #f3f4f6;">
                                    <tr>
                                        <th class="py-2 text-dark font-weight-bolder text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">Sl</th>
                                        <th class="py-2 text-dark font-weight-bolder text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">Product Image</th>
                                        <th class="py-2 text-dark font-weight-bolder text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">Product</th>
                                        <th class="py-2 text-dark font-weight-bolder text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">Unit Price</th>
                                        <th class="py-2 text-dark font-weight-bolder text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">Quantity</th>
                                        <th class="py-2 text-dark font-weight-bolder text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">Total</th>
                                        <th class="py-2 text-dark font-weight-bolder text-uppercase" style="font-size: 0.9rem; letter-spacing: 0.5px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orderRequest->items as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($item->product && $item->product->featured_image)
                                                    <img src="{{ asset(config('imagepath.product') . $item->product->featured_image) }}" alt="Item" height="40" width="40" style="object-fit: contain;">
                                                @else
                                                    <i data-feather="image" style="width:30px; height:30px; opacity: 0.3;"></i>
                                                @endif
                                            </td>
                                            <td class="font-weight-bold text-dark">
                                                <div class="d-flex align-items-center">
                                                    <span>{{ $item->product->name ?? 'N/A' }}</span>
                                                    @if($item->product && $item->product->is_manufacturer == 1)
                                                        <span class="badge badge-light-info ml-1" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">Manufacturer</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ number_format($item->unit_price ?? 0, 2) }}</td>
                                            <td>{{ $item->requested_qty }}</td>
                                            <td>{{ number_format($item->total_price ?? 0, 2) }}</td>
                                            <td>
                                                @if($item->requested_qty == $item->confirmed_qty)
                                                    <span class="text-success">Confirmed</span>
                                                @elseif($item->confirmed_qty > 0)
                                                    <span class="text-info">Partial</span>
                                                @else
                                                    <span class="text-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row invoice-sales-total-wrapper mt-3">
                            <div class="col-md-6 order-md-1 order-2 mt-md-0 mt-3">
                                @if($orderRequest->note)
                                    <p class="card-text mb-0"><span class="font-weight-bolder text-dark">Note:</span> <span class="text-muted">{{ $orderRequest->note }}</span></p>
                                @endif
                            </div>
                            <div class="col-md-6 d-flex justify-content-end order-md-2 order-1">
                                <div class="invoice-total-wrapper">
                                    <div class="invoice-total-item d-flex align-items-center">
                                        <p class="invoice-total-title mb-0 mr-1 text-dark">Total Amount:</p>
                                        <h3 class="invoice-total-amount mb-0 text-dark font-weight-bolder">{{ number_format($totalAmount, 0) }}TK</h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
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
