@extends('layouts.admin')
@section('title', 'Manufacture Order: ' . $manufacture->invoice_no)

@push('styles')
<style>
    .kpi-card {
        border-radius: 0.65rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.06);
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }
    .kpi-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
    }
    .hero-header-card {
        background: #ffffff;
        border-radius: 0.75rem;
        border-left: 5px solid #7367f0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .party-card {
        border-radius: 0.65rem;
        border: 1px solid #ebe9f1;
        transition: all 0.2s ease;
    }
    .party-card:hover {
        border-color: #7367f0;
        box-shadow: 0 4px 12px rgba(115, 103, 240, 0.08);
    }
    .avatar-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
    }
    .table-items-card {
        border-radius: 0.65rem;
        overflow: hidden;
        border: 1px solid #ebe9f1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .item-thumbnail {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .item-thumbnail:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 10px rgba(0,0,0,0.18);
    }
    .status-pill {
        padding: 0.4rem 0.85rem;
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 2rem;
        display: inline-flex;
        align-items: center;
    }
    .status-pulse {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 6px;
        display: inline-block;
    }
    .copy-btn {
        cursor: pointer;
        transition: color 0.15s ease;
    }
    .copy-btn:hover {
        color: #7367f0 !important;
    }
    .financial-card {
        border-radius: 0.65rem;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(115, 103, 240, 0.12);
    }
</style>
@endpush

@section('content')
<!-- Hero Order Header -->
<div class="card hero-header-card mb-2">
    <div class="card-body p-2 d-flex justify-content-between align-items-center flex-wrap">
        <div class="d-flex align-items-center mb-1 mb-md-0">
            <div class="mr-1">
                <span class="avatar-icon-box bg-light-primary text-primary">
                    <i data-feather="box"></i>
                </span>
            </div>
            <div>
                <div class="d-flex align-items-center flex-wrap">
                    <h3 class="mb-0 font-weight-bolder mr-1 text-dark">
                        {{ $manufacture->invoice_no }}
                    </h3>
                    <i data-feather="copy" class="text-muted copy-btn mr-1" id="copyInvoiceBtn" title="Copy Invoice Number" style="width: 16px; height: 16px;"></i>

                    @if($manufacture->status == 0)
                        <span class="status-pill bg-light-warning text-warning">
                            <span class="status-pulse bg-warning"></span> Pending Confirmation
                        </span>
                    @elseif($manufacture->status == 1)
                        <span class="status-pill bg-light-primary text-primary">
                            <span class="status-pulse bg-primary"></span> Confirmed / In Production
                        </span>
                    @elseif($manufacture->status == 2)
                        <span class="status-pill bg-light-success text-success">
                            <span class="status-pulse bg-success"></span> Completed
                        </span>
                    @endif

                    @if($manufacture->collected_by)
                        <span class="status-pill bg-light-info text-info ml-50">
                            <i data-feather="check-circle" style="width: 13px; height: 13px;" class="mr-25"></i> Collected
                        </span>
                    @endif
                </div>
                <div class="text-muted small mt-25">
                    <i data-feather="calendar" style="width: 13px; height: 13px;" class="mr-25"></i>
                    Created on <strong>{{ $manufacture->created_at->format('d M Y, h:i A') }}</strong> by 
                    <span class="font-weight-bold text-dark">{{ $manufacture->creator->name ?? 'Administrator' }}</span>
                </div>
            </div>
        </div>

        <!-- Action Toolbar -->
        <div class="d-flex align-items-center flex-wrap">
            <a href="{{ route('admin.manufacture.index') }}" class="btn btn-outline-secondary mr-50">
                <i data-feather="arrow-left" class="mr-25"></i> Back to Orders
            </a>

            @if(!$manufacture->is_confirm)
                <a href="{{ route('admin.manufacture.edit', $manufacture->id) }}" class="btn btn-outline-primary mr-50">
                    <i data-feather="edit-2" class="mr-25"></i> Edit Order
                </a>
            @endif

            @if($manufacture->status >= 1)
                <a href="{{ route('admin.manufacture.print', $manufacture->id) }}" target="_blank" class="btn btn-primary shadow">
                    <i data-feather="printer" class="mr-25"></i> Print Order / Blueprint
                </a>
            @else
                <button class="btn btn-secondary" onclick="toastr.warning('Please confirm this order before printing.')">
                    <i data-feather="printer" class="mr-25"></i> Print Order
                </button>
            @endif
        </div>
    </div>
</div>

<!-- Key Metrics Row -->
<div class="row mb-2">
    <!-- Metric 1: Total Items -->
    <div class="col-xl-3 col-sm-6 mb-1 mb-xl-0">
        <div class="card kpi-card h-100 bg-white">
            <div class="card-body p-1 d-flex align-items-center">
                <div class="kpi-icon bg-light-primary text-primary mr-1">
                    <i data-feather="layers"></i>
                </div>
                <div>
                    <h4 class="font-weight-bolder mb-0 text-dark">{{ $manufacture->items->count() }}</h4>
                    <span class="text-muted small">Distinct Order Items</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Metric 2: Total Units -->
    <div class="col-xl-3 col-sm-6 mb-1 mb-xl-0">
        <div class="card kpi-card h-100 bg-white">
            <div class="card-body p-1 d-flex align-items-center">
                <div class="kpi-icon bg-light-info text-info mr-1">
                    <i data-feather="package"></i>
                </div>
                <div>
                    <h4 class="font-weight-bolder mb-0 text-info">{{ $manufacture->manufacture_qty }}</h4>
                    <span class="text-muted small">Total Manufacture Units</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Metric 3: Body Parts Cost -->
    <div class="col-xl-3 col-sm-6 mb-1 mb-sm-0">
        <div class="card kpi-card h-100 bg-white">
            <div class="card-body p-1 d-flex align-items-center">
                <div class="kpi-icon bg-light-warning text-warning mr-1">
                    <i data-feather="cpu"></i>
                </div>
                <div>
                    <h4 class="font-weight-bolder mb-0 text-dark">৳ {{ number_format($manufacture->body_total, 2) }}</h4>
                    <span class="text-muted small">Body Parts Cost</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Metric 4: Grand Total -->
    <div class="col-xl-3 col-sm-6">
        <div class="card kpi-card h-100 bg-white">
            <div class="card-body p-1 d-flex align-items-center">
                <div class="kpi-icon bg-light-success text-success mr-1">
                    <i data-feather="credit-card"></i>
                </div>
                <div>
                    <h4 class="font-weight-bolder mb-0 text-success">৳ {{ number_format($manufacture->grand_total, 2) }}</h4>
                    <span class="text-muted small">Grand Total Payable</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Order Items Table, Stakeholder Profiles & Notes -->
    <div class="col-lg-8 col-md-12">
        <!-- Customer & Assigned Worker Cards -->
        <div class="row mb-2">
            <!-- Dealer Profile -->
            <div class="col-md-6 mb-1 mb-md-0">
                <div class="card party-card h-100 bg-white">
                    <div class="card-header bg-light py-1 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 font-weight-bold text-dark">
                            <i data-feather="user-check" class="mr-50 text-primary"></i> Dealer / Customer
                        </h6>
                        <span class="badge badge-light-secondary font-weight-bold">Client</span>
                    </div>
                    <div class="card-body pt-1">
                        <div class="d-flex align-items-center mb-1">
                            <span class="avatar-icon-box bg-light-primary text-primary mr-1">
                                {{ strtoupper(substr($manufacture->dealer_name ?? ($manufacture->dealer->name ?? 'D'), 0, 1)) }}
                            </span>
                            <div>
                                <h5 class="font-weight-bolder mb-0 text-dark">
                                    {{ $manufacture->dealer_name ?? ($manufacture->dealer->name ?? 'N/A') }}
                                </h5>
                                @if($manufacture->dealer_phone ?? ($manufacture->dealer->phone ?? null))
                                    <a href="tel:{{ $manufacture->dealer_phone ?? $manufacture->dealer->phone }}" class="text-primary font-weight-bold small">
                                        <i data-feather="phone-call" style="width: 12px; height: 12px;" class="mr-25"></i>
                                        {{ $manufacture->dealer_phone ?? $manufacture->dealer->phone }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        <p class="mb-0 text-muted small">
                            <i data-feather="map-pin" style="width: 13px; height: 13px;" class="mr-25 text-secondary"></i> 
                            {{ $manufacture->dealer_address ?? ($manufacture->dealer->address ?? 'No physical address recorded') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Worker Profile -->
            <div class="col-md-6">
                <div class="card party-card h-100 bg-white">
                    <div class="card-header bg-light py-1 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 font-weight-bold text-dark">
                            <i data-feather="tool" class="mr-50 text-primary"></i> Assigned Worker
                        </h6>
                        @php
                            $w = $manufacture->worker;
                            $wType = $w ? ($w->type == 1 ? 'Finishing Part' : 'Body Part') : 'Worker';
                        @endphp
                        <span class="badge {{ $w && $w->type == 1 ? 'badge-light-primary' : 'badge-light-info' }} font-weight-bold">
                            {{ $wType }}
                        </span>
                    </div>
                    <div class="card-body pt-1">
                        <div class="d-flex align-items-center mb-1">
                            <span class="avatar-icon-box bg-light-info text-info mr-1">
                                {{ strtoupper(substr($w->name ?? 'W', 0, 1)) }}
                            </span>
                            <div>
                                <h5 class="font-weight-bolder mb-0 text-dark">
                                    {{ $w->name ?? 'Unassigned' }}
                                </h5>
                                @if($w && $w->phone)
                                    <a href="tel:{{ $w->phone }}" class="text-primary font-weight-bold small">
                                        <i data-feather="phone-call" style="width: 12px; height: 12px;" class="mr-25"></i>
                                        {{ $w->phone }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        <p class="mb-0 text-muted small">
                            <i data-feather="clock" style="width: 13px; height: 13px;" class="mr-25 text-secondary"></i> 
                            Order assignment date: <strong>{{ $manufacture->created_at->format('d M Y') }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items Table Card -->
        <div class="card table-items-card mb-2">
            <div class="card-header bg-light py-1 border-bottom d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="card-title mb-0 font-weight-bold text-dark">
                    <i data-feather="package" class="mr-50 text-primary"></i> Order Products & Specifications ({{ $manufacture->items->count() }})
                </h5>
                <span class="badge badge-pill badge-primary font-weight-bolder">Total Units: {{ $manufacture->manufacture_qty }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="thead-light">
                        <tr class="text-center font-weight-bold text-uppercase small">
                            <th style="width: 45px;">#</th>
                            <th class="text-left" style="min-width: 220px;">Product & Blueprint</th>
                            <th style="min-width: 110px;">Ref Invoice</th>
                            <th style="min-width: 130px;">Cost Title</th>
                            <th style="min-width: 110px;">Part Type</th>
                            <th style="width: 80px;">Quantity</th>
                            <th style="width: 110px;" class="text-right">Unit Price</th>
                            <th style="width: 120px;" class="text-right">Total Price</th>
                            <th style="min-width: 130px;">Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($manufacture->items as $idx => $item)
                            @php
                                $prod = $item->product;
                                $recipe = $prod ? $prod->recipe : null;
                                $imgs = $recipe && !empty($recipe->manufacture_images) ? $recipe->manufacture_images : [];
                                $mimg = !empty($imgs) && isset($imgs[0]) ? asset($imgs[0]) : ($prod && $prod->featured_image ? asset(config('imagepath.product') . $prod->featured_image) : asset('images/no-image.png'));
                            @endphp
                            <tr>
                                <td class="text-center font-weight-bold text-muted">{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $mimg }}" class="item-thumbnail mr-1 preview-thumbnail" 
                                             title="Click to zoom blueprint"
                                             data-img="{{ $mimg }}" 
                                             data-title="{{ $prod->name ?? 'Product Blueprint' }}">
                                        <div>
                                            <strong class="text-dark d-block">{{ $prod->name ?? 'Product #'.$item->product_id }}</strong>
                                            @if($prod && $prod->model_no)
                                                <small class="badge badge-light-secondary font-weight-normal mt-25">Model: {{ $prod->model_no }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($item->reff_invoice)
                                        <span class="badge badge-light-secondary font-weight-bold">{{ $item->reff_invoice }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-primary font-weight-bold">
                                        <i data-feather="tag" style="width: 11px; height: 11px;" class="mr-25"></i>
                                        {{ $item->cost_title ?: ($item->costSetting->cost_title ?? 'Default') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php $pt = strtolower($item->part_type); @endphp
                                    @if($pt === 'body')
                                        <span class="badge badge-light-info">Body Part</span>
                                    @elseif($pt === 'finishing')
                                        <span class="badge badge-light-primary">Finishing Part</span>
                                    @elseif($pt === 'both')
                                        <span class="badge badge-light-success">Both</span>
                                    @else
                                        <span class="badge badge-light-secondary">{{ ucfirst($item->part_type) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-pill badge-light-dark font-weight-bolder" style="font-size: 13px;">{{ $item->qty }}</span>
                                </td>
                                <td class="text-right font-weight-bold text-dark">
                                    ৳ {{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="text-right font-weight-bolder text-success">
                                    ৳ {{ number_format($item->total_price, 2) }}
                                </td>
                                <td class="small text-muted">
                                    {{ $item->note ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">No items recorded for this order.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light font-weight-bold">
                        <tr>
                            <td colspan="5" class="text-right text-uppercase small font-weight-bolder">Summary Totals:</td>
                            <td class="text-center text-primary font-weight-bolder" style="font-size: 14px;">{{ $manufacture->manufacture_qty }}</td>
                            <td class="text-right text-muted small">Grand Total:</td>
                            <td class="text-right text-success font-weight-bolder" style="font-size: 14px;">৳ {{ number_format($manufacture->grand_total, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($manufacture->note)
            <div class="card border mb-2">
                <div class="card-header bg-light py-1 border-bottom">
                    <h6 class="card-title mb-0 font-weight-bold text-dark">
                        <i data-feather="file-text" class="mr-50 text-primary"></i> General Order Remarks / Notes
                    </h6>
                </div>
                <div class="card-body pt-1">
                    <p class="mb-0 text-dark font-italic">{{ $manufacture->note }}</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Production Timeline & Financial Breakdown -->
    <div class="col-lg-4 col-md-12">
        <!-- Production Timeline -->
        <div class="card border mb-2">
            <div class="card-header bg-light py-1 border-bottom">
                <h6 class="card-title mb-0 font-weight-bold text-dark">
                    <i data-feather="activity" class="mr-50 text-primary"></i> Lifecycle Timeline
                </h6>
            </div>
            <div class="card-body pt-2">
                <ul class="timeline">
                    <!-- Step 1: Created -->
                    <li class="timeline-item">
                        <span class="timeline-point timeline-point-indicator timeline-point-primary"></span>
                        <div class="timeline-event">
                            <div class="d-flex justify-content-between mb-25">
                                <h6 class="font-weight-bold mb-0 text-dark">Order Created</h6>
                                <small class="text-muted">{{ $manufacture->created_at->format('d M, h:i A') }}</small>
                            </div>
                            <small class="text-muted">By: <strong>{{ $manufacture->creator->name ?? 'Admin' }}</strong></small>
                        </div>
                    </li>

                    <!-- Step 2: Confirmation -->
                    <li class="timeline-item">
                        <span class="timeline-point timeline-point-indicator {{ $manufacture->status >= 1 ? 'timeline-point-success' : 'timeline-point-secondary' }}"></span>
                        <div class="timeline-event">
                            <div class="d-flex justify-content-between mb-25">
                                <h6 class="font-weight-bold mb-0 text-dark">Order Confirmation</h6>
                                @if($manufacture->status >= 1)
                                    <span class="badge badge-light-success font-weight-bold">Confirmed</span>
                                @else
                                    <span class="badge badge-light-warning">Pending</span>
                                @endif
                            </div>
                            <small class="text-muted">
                                {{ $manufacture->status >= 1 ? 'Approved and blueprint dispatched to worker.' : 'Waiting for administrative approval.' }}
                            </small>
                        </div>
                    </li>

                    <!-- Step 3: Production Complete -->
                    <li class="timeline-item">
                        <span class="timeline-point timeline-point-indicator {{ $manufacture->status >= 2 ? 'timeline-point-success' : 'timeline-point-secondary' }}"></span>
                        <div class="timeline-event">
                            <div class="d-flex justify-content-between mb-25">
                                <h6 class="font-weight-bold mb-0 text-dark">Production Completed</h6>
                                @if($manufacture->status >= 2)
                                    <span class="badge badge-light-success">Completed</span>
                                @else
                                    <span class="badge badge-light-secondary">In Production</span>
                                @endif
                            </div>
                            @if($manufacture->status >= 2)
                                <small class="text-muted">Completed by: <strong>{{ $manufacture->completedBy->name ?? 'Admin' }}</strong></small>
                            @else
                                <small class="text-muted">Manufacturing currently underway with worker.</small>
                            @endif
                        </div>
                    </li>

                    <!-- Step 4: Product Collection / Received -->
                    <li class="timeline-item">
                        <span class="timeline-point timeline-point-indicator {{ $manufacture->collected_by ? 'timeline-point-success' : 'timeline-point-secondary' }}"></span>
                        <div class="timeline-event">
                            <div class="d-flex justify-content-between mb-25">
                                <h6 class="font-weight-bold mb-0 text-dark">Collected & Received</h6>
                                @if($manufacture->collected_by)
                                    <span class="badge badge-success">Received</span>
                                @else
                                    <span class="badge badge-light-secondary">Pending Collection</span>
                                @endif
                            </div>
                            @if($manufacture->collected_by)
                                <small class="text-muted">Collected by: <strong>{{ $manufacture->collectedBy->name ?? 'Admin' }}</strong></small>
                            @else
                                <small class="text-muted">To be received and logged upon delivery.</small>
                            @endif
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Financial Summary Card -->
        <div class="card financial-card border-0 mb-2">
            <div class="card-header bg-primary text-white py-1">
                <h5 class="card-title text-white mb-0 font-weight-bold">
                    <i data-feather="dollar-sign" class="mr-50"></i> Financial Breakdown
                </h5>
            </div>
            <div class="card-body pt-2 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted">Total Product Items:</span>
                    <strong class="text-dark">{{ $manufacture->items->count() }} items</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted">Total Quantity:</span>
                    <strong class="text-primary font-weight-bolder">{{ $manufacture->manufacture_qty }} units</strong>
                </div>
                <hr class="my-50">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted">Body Parts Cost:</span>
                    <strong class="text-dark">৳ {{ number_format($manufacture->body_total, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted">Finishing Parts Cost:</span>
                    <strong class="text-dark">৳ {{ number_format($manufacture->finishing_total, 2) }}</strong>
                </div>

                @php
                    $grandTotal = $manufacture->grand_total > 0 ? $manufacture->grand_total : 1;
                    $bodyPercent = round(($manufacture->body_total / $grandTotal) * 100);
                    $finishingPercent = 100 - $bodyPercent;
                @endphp
                <div class="progress mt-1 mb-1" style="height: 8px;">
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $bodyPercent }}%" title="Body: {{ $bodyPercent }}%"></div>
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $finishingPercent }}%" title="Finishing: {{ $finishingPercent }}%"></div>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-1">
                    <span><i class="fa fa-circle text-info mr-25"></i> Body ({{ $bodyPercent }}%)</span>
                    <span><i class="fa fa-circle text-primary mr-25"></i> Finishing ({{ $finishingPercent }}%)</span>
                </div>

                <div class="p-1 rounded bg-light-success d-flex justify-content-between align-items-center mt-1">
                    <span class="h5 mb-0 font-weight-bold text-success">Grand Total:</span>
                    <span class="h4 mb-0 font-weight-bolder text-success">৳ {{ number_format($manufacture->grand_total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Blueprint Lightbox Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-1 bg-light">
                <h5 class="modal-title font-weight-bold" id="imagePreviewTitle">Manufacture Blueprint</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-2">
                <img src="" id="modalPreviewImg" class="img-fluid rounded border shadow-sm" style="max-height: 480px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Image preview modal
        $('.preview-thumbnail').on('click', function () {
            let img = $(this).data('img');
            let title = $(this).data('title');
            $('#modalPreviewImg').attr('src', img);
            $('#imagePreviewTitle').text(title + ' - Blueprint');
            $('#imagePreviewModal').modal('show');
        });

        // Copy invoice number to clipboard
        $('#copyInvoiceBtn').on('click', function() {
            let invoiceNo = "{{ $manufacture->invoice_no }}";
            navigator.clipboard.writeText(invoiceNo).then(function() {
                toastr.success('Invoice number ' + invoiceNo + ' copied to clipboard!');
            }, function() {
                toastr.info('Invoice number: ' + invoiceNo);
            });
        });
    });
</script>
@endpush
