@extends('layouts.admin')
@section('title', 'Manufacture Details')
@section('content')
@php
    $recipe = $manufacture->product ? $manufacture->product->recipe : null;
    $imgs = $recipe && !empty($recipe->manufacture_images) ? $recipe->manufacture_images : [];
    $manufacture_image = !empty($imgs) && isset($imgs[0]) ? asset($imgs[0]) : asset('images/no-image.png');
    $product_image = $manufacture->product && $manufacture->product->featured_image ? asset(config('imagepath.product') . $manufacture->product->featured_image) : asset('images/no-image.png');
@endphp

<div class="row mb-2">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="mb-0">
            Manufacture Order: <span class="text-primary">{{ $manufacture->invoice_no }}</span>
            @if($manufacture->reff_invoice)
                Reference Order: <span class="text-primary">{{ $manufacture->reff_invoice }}</span>
            @endif

        </h2>
        <div>
            <a href="{{ route('admin.manufacture.index') }}" class="btn btn-outline-secondary mr-1">
                <i data-feather="arrow-left"></i> Back to List
            </a>
            @if($manufacture->status >= 1)
                <a href="{{ route('admin.manufacture.print', $manufacture->id) }}" target="_blank" class="btn btn-primary">
                    <i data-feather="printer"></i> Print Order
                </a>
            @else
                <button class="btn btn-secondary" onclick="toastr.error('Please confirm this order before printing.')">
                    <i data-feather="printer"></i> Print Order
                </button>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Core Info & Images -->
    <div class="col-lg-7 col-md-12">
        <div class="card mb-2">
            <div class="card-header bg-light py-1">
                <h4 class="card-title mb-0"><i data-feather="info" class="mr-50"></i> General Information</h4>
            </div>
            <div class="card-body mt-2">
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <small class="text-muted text-uppercase d-block mb-25">Product</small>
                        <h5 class="mb-0">{{ $manufacture->product->name ?? 'N/A' }}</h5>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <small class="text-muted text-uppercase d-block mb-25">Quantity</small>
                        <h5 class="mb-0 font-weight-bolder text-primary" style="font-size: 1.2rem;">{{ $manufacture->manufacture_qty }} <small class="text-muted">Units</small></h5>
                    </div>

                    <div class="col-sm-6 mb-2">
                        <small class="text-muted text-uppercase d-block mb-25">Assigned Worker</small>
                        <h6 class="mb-0">{{ $manufacture->worker->name ?? 'N/A' }}</h6>
                        @if(isset($manufacture->worker->phone))
                            <small>{{ $manufacture->worker->phone }}</small>
                        @endif
                    </div>
                    <div class="col-sm-6 mb-2">
                        <small class="text-muted text-uppercase d-block mb-25">Dealer / Customer</small>
                        <h6 class="mb-0">{{ $manufacture->dealer_name ?? ($manufacture->dealer->name ?? 'N/A') }}</h6>
                        @if($manufacture->dealer_phone || isset($manufacture->dealer->phone))
                            <small>{{ $manufacture->dealer_phone ?? $manufacture->dealer->phone }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light py-1">
                        <h5 class="card-title mb-0">Product Image</h5>
                    </div>
                    <div class="card-body text-center mt-1 p-1">
                        <img src="{{ $product_image }}" class="img-fluid rounded" alt="Product Image" style="max-height: 250px; object-fit: contain;">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-light py-1">
                        <h5 class="card-title mb-0">Blueprint / Manufacture</h5>
                    </div>
                    <div class="card-body text-center mt-1 p-1">
                        <img src="{{ $manufacture_image }}" class="img-fluid rounded border" alt="Blueprint" style="max-height: 250px; object-fit: contain;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Status & Financials -->
    <div class="col-lg-5 col-md-12">
        <div class="card mb-2">
            <div class="card-header bg-light py-1">
                <h4 class="card-title mb-0"><i data-feather="activity" class="mr-50"></i> Status Timeline</h4>
            </div>
            <div class="card-body mt-2">
                <ul class="timeline">
                    <li class="timeline-item">
                        <span class="timeline-point timeline-point-indicator"></span>
                        <div class="timeline-event">
                            <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                <h6 class="mb-50">Order Created</h6>
                                <span class="timeline-event-time">{{ $manufacture->created_at->format('d M Y h:i A') }}</span>
                            </div>
                            <p>Created By: <strong>{{ $manufacture->creator->name ?? 'System' }}</strong></p>
                        </div>
                    </li>
                    <li class="timeline-item">
                        <span class="timeline-point timeline-point-{{ $manufacture->status >= 1 ? 'success' : 'secondary' }} timeline-point-indicator"></span>
                        <div class="timeline-event">
                            <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                <h6 class="mb-50">Confirmation</h6>
                                @if($manufacture->status >= 1)
                                    <span class="badge badge-light-success">Confirmed</span>
                                @else
                                    <span class="badge badge-light-warning">Pending</span>
                                @endif
                            </div>
                            <p class="mb-0">Order passed confirmation stage.</p>
                        </div>
                    </li>
                    <li class="timeline-item">
                        <span class="timeline-point timeline-point-{{ $manufacture->status >= 2 ? 'success' : 'secondary' }} timeline-point-indicator"></span>
                        <div class="timeline-event">
                            <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                <h6 class="mb-50">Completion</h6>
                                @if($manufacture->status >= 2)
                                    <span class="badge badge-light-success">Completed</span>
                                @else
                                    <span class="badge badge-light-secondary">Not Completed</span>
                                @endif
                            </div>
                            @if($manufacture->status >= 2)
                                <p class="mb-0">Completed By: <strong>{{ $manufacture->completedBy->name ?? 'N/A' }}</strong></p>
                            @endif
                        </div>
                    </li>
                    <li class="timeline-item">
                        <span class="timeline-point timeline-point-{{ $manufacture->collected_by ? 'primary' : 'secondary' }} timeline-point-indicator"></span>
                        <div class="timeline-event">
                            <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                <h6 class="mb-50">Collection</h6>
                            </div>
                            @if($manufacture->collected_by)
                                <p class="mb-0">Collected By: <strong class="text-primary">{{ $manufacture->collectedBy->name ?? 'N/A' }}</strong></p>
                            @else
                                <p class="mb-0 text-muted"><em>Pending collection assignment</em></p>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light py-1">
                <h4 class="card-title mb-0"><i data-feather="pie-chart" class="mr-50"></i> Financial Summary</h4>
            </div>
            <div class="card-body mt-2">
                <div class="d-flex justify-content-between mb-1">
                    <span>Body Part Cost ({{ number_format($manufacture->body_part_price, 2) }} x {{ $manufacture->body_total > 0 ? $manufacture->manufacture_qty : 0 }})</span>
                    <span class="font-weight-bold">৳ {{ number_format($manufacture->body_total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                    <span>Finishing Part Cost ({{ number_format($manufacture->finishing_part_price, 2) }} x {{ $manufacture->finishing_total > 0 ? $manufacture->manufacture_qty : 0 }})</span>
                    <span class="font-weight-bold">৳ {{ number_format($manufacture->finishing_total, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="font-weight-bold text-dark h5">Grand Total</span>
                    <span class="font-weight-bolder text-success h4">৳ {{ number_format($manufacture->grand_total, 2) }}</span>
                </div>
                @if($manufacture->note)
                    <div class="mt-2 p-1 bg-light-secondary rounded">
                        <strong>Note:</strong> {{ $manufacture->note }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
