@extends('layouts.admin')
@section('title', 'Order Request Detail')

@push('styles')
<style>
    /* ── Card & Layout ── */
    .req-card {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    }
    /* ── Info Label / Value ── */
    .info-label {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #9ca3af;
        margin-bottom: 2px;
    }
    .info-value {
        font-size: 0.9rem;
        font-weight: 700;
        color: #111827;
    }
    /* ── Table ── */
    .req-table thead th {
        background: #f3f4f6;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
        border-color: #e5e7eb;
        white-space: nowrap;
        vertical-align: middle;
    }
    .req-table td {
        font-size: 0.85rem;
        color: #374151;
        vertical-align: middle;
        border-color: #f3f4f6;
        padding: 0.65rem 0.75rem;
    }
    .req-table tbody tr:hover { background: #fafafa; }
    /* ── Summary Panel ── */
    .summary-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
    }
    .summary-panel .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .summary-panel .summary-row:last-child { border-bottom: none; }
    .summary-panel .s-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: #6b7280;
    }
    .summary-panel .s-value {
        font-size: 1rem;
        font-weight: 800;
        color: #111827;
    }
    .summary-panel .s-value.primary { color: #4f46e5; }
    /* ── Confirm Qty Input ── */
    .confirm-qty-input {
        border: 1.5px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        text-align: center;
        color: #111827;
        background: #f9fafb;
    }
    .confirm-qty-input:focus {
        border-color: #6366f1;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    /* ── Btn Back ── */
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #374151;
        background: #fff;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        padding: 0.45rem 1rem;
        transition: all 0.15s;
    }
    .btn-back:hover { background: #f3f4f6; color: #111827; border-color: #9ca3af; text-decoration: none; }
    /* ── Confirm Btn ── */
    .btn-confirm {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 700;
        padding: 0.6rem 1.8rem;
        box-shadow: 0 4px 12px rgba(79,70,229,0.35);
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-confirm:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        box-shadow: 0 6px 16px rgba(79,70,229,0.45);
        color: #fff;
    }
    /* ── Request number badge ── */
    .req-number-badge {
        display: inline-block;
        background: #ede9fe;
        color: #4f46e5;
        font-size: 0.8rem;
        font-weight: 700;
        border-radius: 6px;
        padding: 2px 10px;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
@inject('priceCalculator', 'App\Services\ProductPriceCalculator')

{{-- ── Page Header ── --}}
<div class="content-header row align-items-center mb-1">
    <div class="content-header-left col-md-8 col-12 mb-2">
        <div class="d-flex align-items-center" style="gap: 12px;">
            <h2 class="content-header-title mb-0 text-dark font-weight-bolder" style="font-size: 1.4rem;">Order Request Detail</h2>
            <span class="req-number-badge">{{ $orderRequest->request_number ?? '#'.$orderRequest->id }}</span>
        </div>
    </div>
    <div class="content-header-right col-md-4 col-12 text-md-right mb-2">
        <a href="{{ route('admin.order-requests.index') }}" class="btn-back">
            <i data-feather="arrow-left" style="width:15px;height:15px;"></i> Back to List
        </a>
    </div>
</div>

<div class="content-body">
<form action="{{ route('admin.order-requests.confirm', $orderRequest->id) }}" method="POST" id="confirmForm">
@csrf

{{-- ── Single Card: all content ── --}}
<div class="card req-card mb-2">
  <div class="card-body p-3">

    {{-- ── Dealer Info + Request Meta ── --}}
    <div class="d-flex justify-content-between align-items-start flex-wrap" style="gap: 20px;">

            {{-- LEFT: Dealer Info --}}
            <div class="d-flex align-items-start" style="gap: 14px;">
                @if(!empty($orderRequest->dealer->profile_image))
                    <img src="{{ asset($orderRequest->dealer->profile_image) }}"
                         alt="{{ $orderRequest->dealer->name }}"
                         style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid #e5e7eb;flex-shrink:0;"
                         onerror="this.style.display='none'; document.getElementById('dealerIconFallback').style.display='flex';">
                    <div id="dealerIconFallback" class="avatar bg-light-primary" style="display:none; border-radius:50%; flex-shrink:0; width:52px; height:52px; align-items:center; justify-content:center;">
                        <i data-feather="user" class="text-primary" style="width:22px;height:22px;"></i>
                    </div>
                @else
                    <div class="avatar bg-light-primary" style="border-radius:50%; flex-shrink:0; width:52px; height:52px; display:flex; align-items:center; justify-content:center;">
                        <i data-feather="user" class="text-primary" style="width:22px;height:22px;"></i>
                    </div>
                @endif
                <div>
                    <p class="mb-2 font-weight-bolder text-dark" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:0.7px; color:#6b7280 !important;">Dealer Information</p>
                    <div class="d-flex flex-wrap" style="gap: 24px;">
                        <div>
                            <div class="info-label">Name</div>
                            <div class="info-value">{{ $orderRequest->dealer->name ?? 'N/A' }}</div>
                        </div>
                        @if(!empty($orderRequest->dealer->shop_name))
                        <div>
                            <div class="info-label">Shop</div>
                            <div class="info-value">{{ $orderRequest->dealer->shop_name }}</div>
                        </div>
                        @endif
                        @if(!empty($orderRequest->dealer->address))
                        <div>
                            <div class="info-label">Address</div>
                            <div class="info-value">{{ $orderRequest->dealer->address }}</div>
                        </div>
                        @endif
                        <div>
                            <div class="info-label">Phone</div>
                            <div class="info-value">{{ $orderRequest->dealer->phone ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Request Meta --}}
            <div class="d-flex flex-wrap" style="gap: 24px; text-align:right;">
                <div>
                    <div class="info-label">Request Date</div>
                    <div class="info-value">{{ $orderRequest->created_at->format('d M Y') }}</div>
                </div>
                <div>
                    <div class="info-label">Total Amount</div>
                    <div class="info-value text-primary" id="display_total_amount" style="font-size:1.1rem;">0 <span style="font-size:0.75rem;font-weight:600;">TK</span></div>
                </div>
            </div>

    </div>{{-- END dealer info row --}}

    <hr style="border-color:#f3f4f6; margin: 1rem 0;">

    {{-- ── Items Table ── --}}
    <div class="table-responsive" style="margin: 0 -0.25rem;">

            <table class="table req-table mb-0">
                <thead>
                    <tr>
                        <th style="width:46px; padding-left:1rem;">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="selectAll">
                                <label class="custom-control-label" for="selectAll"></label>
                            </div>
                        </th>
                        <th>#</th>
                        <th>Product</th>
                        <th>Production</th>
                        <th>Product Name</th>
                        <th>Stock</th>
                        <th>Requested Qty</th>
                        <th>Confirm Qty</th>
                        <th>Due Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotal = 0; @endphp
                    @foreach($orderRequest->items as $index => $item)
                    @php
                        $pendingQty = $item->requested_qty - $item->confirmed_qty;
                        $unitPrice  = 0;
                        if ($item->product) {
                            if ($item->product->is_manufacturer == 1) {
                                try {
                                    $priceData = $priceCalculator->calculate($item->product_id, $orderRequest->dealer_id);
                                    $unitPrice = $priceData['dealer_price'];
                                } catch (\Exception $e) { $unitPrice = 0; }
                            } else {
                                $unitPrice = $item->product->selling_price ?? 0;
                            }
                        }
                        $itemTotal  = $unitPrice * $pendingQty;
                        $grandTotal += $itemTotal;

                        // Manufacture image
                        $mfgImage = null;
                        if ($item->product && $item->product->is_manufacturer) {
                            $recipe = \App\Models\ProductRecipe::where('product_id', $item->product_id)->first();
                            if ($recipe && is_array($recipe->manufacture_images) && count($recipe->manufacture_images) > 0) {
                                $mfgImage = $recipe->manufacture_images[0];
                            }
                        }
                    @endphp
                    <tr>
                        {{-- Checkbox --}}
                        <td style="padding-left:1rem;">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input item-checkbox" id="itemCheck{{$index}}" name="items[{{ $index }}][selected]" value="1" data-index="{{ $index }}">
                                <label class="custom-control-label" for="itemCheck{{$index}}"></label>
                            </div>
                            <input type="hidden" name="items[{{ $index }}][order_request_item_id]" value="{{ $item->id }}">
                        </td>
                        {{-- SL --}}
                        <td class="text-muted">{{ $index + 1 }}</td>
                        {{-- Product Image --}}
                        <td>
                            @if($item->product && $item->product->featured_image)
                                <img src="{{ asset(config('imagepath.product') . $item->product->featured_image) }}" width="42" height="42" style="border-radius:6px;object-fit:cover;border:1px solid #e5e7eb;" onerror="this.src='{{ asset('images/no-image.png') }}'">
                            @else
                                <img src="{{ asset('images/no-image.png') }}" width="42" height="42" style="border-radius:6px;object-fit:cover;">
                            @endif
                        </td>
                        {{-- Production Image --}}
                        <td>
                            @if($mfgImage)
                                <img src="{{ asset($mfgImage) }}" width="42" height="42" style="border-radius:6px;object-fit:cover;border:1px solid #e5e7eb;" onerror="this.src='{{ asset('images/no-image.png') }}'">
                            @else
                                <span class="badge badge-light-secondary" style="font-size:0.72rem;">N/A</span>
                            @endif
                        </td>
                        {{-- Name --}}
                        <td class="font-weight-bold text-dark">{{ optional($item->product)->name ?? 'N/A' }}</td>
                        {{-- Stock --}}
                        <td>
                            <span class="badge {{ (optional($item->product)->stock ?? 0) > 0 ? 'badge-light-success' : 'badge-light-danger' }}" style="font-size:0.78rem;">
                                {{ optional($item->product)->stock ?? 0 }}
                            </span>
                        </td>
                        {{-- Requested Qty --}}
                        <td class="font-weight-bold">{{ $item->requested_qty }}</td>
                        {{-- Confirm Qty --}}
                        <td style="width:120px;">
                            <input type="number" class="form-control confirm-qty-input"
                                name="items[{{ $index }}][confirm_qty]"
                                min="0" max="{{ $pendingQty }}" step="1" value="{{ $pendingQty }}"
                                data-unit-price="{{ $unitPrice }}" data-requested="{{ $item->requested_qty }}" data-index="{{ $index }}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, ''); if(this.value !== '' && parseInt(this.value) > parseInt(this.max)) this.value = this.max;"
                                @if($pendingQty == 0) readonly @endif
                                style="height:34px;">
                        </td>
                        {{-- Due Qty --}}
                        <td>
                            <span class="due-qty-text font-weight-bold" id="dueQty{{ $index }}" style="color:#f59e0b;">0</span>
                        </td>
                        {{-- Unit Price --}}
                        <td class="font-weight-bold text-dark">{{ number_format($unitPrice, 0) }}</td>
                        {{-- Row Total --}}
                        <td>
                            <span class="item-total-text font-weight-bolder text-primary" id="itemTotal{{ $index }}">{{ number_format($itemTotal, 0) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
    </div>{{-- END table-responsive --}}

    <hr style="border-color:#f3f4f6; margin: 1rem 0;">

    {{-- ── Notes + Summary ── --}}
    <div class="row">

            {{-- Notes --}}
            <div class="col-md-7 order-2 order-md-1">
                <div class="mb-3">
                    <p class="info-label mb-1">Customer Note</p>
                    <textarea name="order_note" class="form-control bg-light" rows="3" readonly style="resize:none; font-size:0.85rem; border-radius:8px; border-color:#e5e7eb;">{{ $orderRequest->note }}</textarea>
                </div>
                <div>
                    <p class="info-label mb-1">Admin Note</p>
                    <textarea name="admin_note" class="form-control" rows="3" placeholder="Add an internal note..." style="font-size:0.85rem; border-radius:8px; border-color:#d1d5db;"></textarea>
                </div>
            </div>
            {{-- Summary --}}
            <div class="col-md-5 order-1 order-md-2 mb-3 mb-md-0">
                <div class="summary-panel">
                    <div class="summary-row">
                        <span class="s-label">Total Amount</span>
                        <span class="s-value" id="summaryTotalAmount">{{ number_format($grandTotal, 0) }} <small class="text-muted" style="font-size:0.72rem;font-weight:600;">TK</small></span>
                    </div>
                    <div class="summary-row">
                        <span class="s-label">After Update Qty Amount</span>
                        <span class="s-value" id="summaryAfterUpdateAmount">{{ number_format($grandTotal, 0) }} <small class="text-muted" style="font-size:0.72rem;font-weight:600;">TK</small></span>
                    </div>
                    <div class="summary-row">
                        <span class="s-label">Selected Total Amount</span>
                        <span class="s-value primary" id="summarySelectedTotalAmount">0 <small class="text-muted" style="font-size:0.72rem;font-weight:600;">TK</small></span>
                    </div>
                </div>
                <div class="text-right mt-3">
                    <button type="submit" class="btn-confirm">
                        <i data-feather="check-circle" style="width:16px;height:16px;"></i>
                        Confirm Order
                    </button>
                </div>
            </div>
    </div>{{-- END notes+summary row --}}

  </div>{{-- END card-body --}}
</div>{{-- END single card --}}

</form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    const initialGrandTotal = {{ $grandTotal }};
    $('#display_total_amount').html(Math.round(initialGrandTotal).toLocaleString() + ' <span style="font-size:0.75rem;font-weight:600;">TK</span>');

    function calculateTotals() {
        let afterUpdateAmount   = 0;
        let selectedTotalAmount = 0;

        $('.confirm-qty-input').each(function () {
            const index      = $(this).data('index');
            const unitPrice  = parseFloat($(this).data('unit-price')) || 0;
            const confirmQty = parseInt($(this).val()) || 0;
            const pendingQty = parseInt($(this).attr('max')) || 0;

            const itemTotal = unitPrice * confirmQty;
            $('#itemTotal' + index).text(Math.round(itemTotal).toLocaleString());

            const dueQty = pendingQty - confirmQty;
            $('#dueQty' + index).text(dueQty);

            afterUpdateAmount += itemTotal;
            if ($('#itemCheck' + index).is(':checked')) {
                selectedTotalAmount += itemTotal;
            }
        });

        $('#summaryAfterUpdateAmount').html(Math.round(afterUpdateAmount).toLocaleString() + ' <small class="text-muted" style="font-size:0.72rem;font-weight:600;">TK</small>');
        $('#summarySelectedTotalAmount').html(Math.round(selectedTotalAmount).toLocaleString() + ' <small class="text-muted" style="font-size:0.72rem;font-weight:600;">TK</small>');
        $('#display_total_amount').html(Math.round(afterUpdateAmount).toLocaleString() + ' <span style="font-size:0.75rem;font-weight:600;">TK</span>');
    }

    calculateTotals();

    $('.confirm-qty-input').on('input change', function () { calculateTotals(); });

    $('.item-checkbox').on('change', function () {
        calculateTotals();
        $('#selectAll').prop('checked', $('.item-checkbox:checked').length === $('.item-checkbox').length);
    });

    $('#selectAll').on('change', function () {
        $('.item-checkbox').prop('checked', $(this).is(':checked'));
        calculateTotals();
    });

    $('#confirmForm').on('submit', function (e) {
        if ($('.item-checkbox:checked').length === 0) {
            e.preventDefault();
            toastr.error('Please select at least one item to confirm.');
            return;
        }
        $('.item-checkbox:not(:checked)').each(function () {
            const index = $(this).data('index');
            $('input[name="items[' + index + '][confirm_qty]"]').val(0);
        });
    });

    // Re-init feather icons for dynamically rendered elements
    if (typeof feather !== 'undefined') feather.replace();
});
</script>
@endpush
