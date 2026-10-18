@extends('layouts.admin')
@section('title', 'Create Manufacture Order')

@push('styles')
<style>
    .confirm-box {
        background-color: #f8f9fa;
        border: 1px solid #d8d6de;
        border-radius: 0.5rem;
        padding: 0.9rem 1.15rem;
        transition: all 0.25s ease-in-out;
        position: relative;
    }
    .confirm-box:hover {
        border-color: #b8b5c3;
    }
    .confirm-box.is-confirmed {
        background-color: #f0fdf4 !important;
        border-color: #28c76f !important;
        box-shadow: 0 2px 8px rgba(40, 199, 111, 0.12);
    }
    .confirm-box .custom-control {
        min-height: 1.5rem;
        padding-left: 1.85rem;
        margin-bottom: 0;
    }
    .confirm-box .custom-control-label {
        cursor: pointer;
        user-select: none;
        font-size: 0.95rem;
        line-height: 1.45;
        padding-top: 1px;
    }
    .confirm-box .custom-control-label::before,
    .confirm-box .custom-control-label::after {
        top: 0.12rem;
        left: -1.85rem;
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 4px;
    }
    .confirm-box .confirm-note {
        padding-left: 1.85rem;
    }
    .action-panel-card {
        border-radius: 0.65rem;
        border-top: 3px solid #7367f0 !important;
        box-shadow: 0 3px 12px rgba(115, 103, 240, 0.06);
    }
    .table-items-box {
        border-radius: 0.65rem;
        overflow: hidden;
        border: 1px solid #ebe9f1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }
    .table-items-box th {
        background-color: #f8f8fb !important;
        color: #5e5873;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .item-thumbnail-preview {
        width: 44px;
        height: 44px;
        border-radius: 6px;
        object-fit: cover;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        cursor: pointer;
        transition: transform 0.2s;
    }
    .item-thumbnail-preview:hover {
        transform: scale(1.08);
    }
    .btn-remove-item {
        color: #ea5455;
        background: rgba(234, 84, 85, 0.1);
        border: none;
        border-radius: 6px;
        padding: 5px 8px;
        transition: all 0.2s;
    }
    .btn-remove-item:hover {
        background: #ea5455;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-bottom py-1 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0 font-weight-bolder">Create Manufacture Order</h4>
                    <small class="text-muted">Invoice No: <span class="badge badge-light-primary font-weight-bold">{{ $invoice_no }}</span></small>
                </div>
                <a href="{{ route('admin.manufacture.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i data-feather="arrow-left" class="mr-25"></i> Back to List
                </a>
            </div>

            <div class="card-body pt-2">
                <form id="manufactureForm">
                    @csrf
                    <input type="hidden" name="invoice_no" value="{{ $invoice_no }}">

                    <!-- Add Product Bar (Inline Worker + Product + Qty + Actions) -->
                    <div class="card bg-white action-panel-card mb-2">
                        <div class="card-body p-2">
                            <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap">
                                <h6 class="font-weight-bold mb-0 text-primary">
                                    <i data-feather="plus-circle" class="mr-25"></i> Add Manufacture Products
                                </h6>
                                <div class="d-flex align-items-center flex-wrap">
                                    <span class="mr-2 font-weight-bold text-muted small">
                                        Worker Type: <span id="workerTypeBadge" class="badge badge-light-secondary font-weight-bold">Select Worker First</span>
                                    </span>
                                    <div class="small">
                                        <a href="javascript:void(0)" id="btnSelectAll" class="mr-1 text-primary font-weight-bold">
                                            <i data-feather="check-square" style="width: 14px; height: 14px;"></i> Select All
                                        </a>
                                        <a href="javascript:void(0)" id="btnClearAll" class="text-danger font-weight-bold">
                                            <i data-feather="x-square" style="width: 14px; height: 14px;"></i> Clear Selection
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-end">
                                <!-- 1. Select Assigned Worker (BEFORE Select Manufacture Products) -->
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-1 mb-lg-0">
                                    <label for="worker_id" class="font-weight-bold">
                                        Select Assigned Worker <span class="text-danger">*</span>
                                    </label>
                                    <select name="worker_id" id="worker_id" class="form-control select2" required>
                                        <option value="">-- Select Worker --</option>
                                        @foreach($workers as $worker)
                                            @php
                                                $partType = $worker->type == 1 ? 'finishing' : 'body';
                                                $typeLabel = $worker->type == 1 ? 'Finishing Part' : 'Body Part';
                                            @endphp
                                            <option value="{{ $worker->id }}" 
                                                data-type="{{ $partType }}" 
                                                data-type-label="{{ $typeLabel }}"
                                                data-phone="{{ $worker->phone }}">
                                                {{ $worker->name }} ({{ $typeLabel }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-text worker_id_error"></span>
                                </div>

                                <!-- 2. Select Manufacture Products -->
                                <div class="col-lg-4 col-md-4 col-sm-6 mb-1 mb-lg-0">
                                    <label for="select_product" class="font-weight-bold">
                                        Select Manufacture Products <small class="text-muted">(Multiple)</small>
                                    </label>
                                    <select id="select_product" class="form-control select2" multiple="multiple" data-placeholder="Choose products...">
                                        @php
                                            $readyProducts = $products->filter(function($p) {
                                                return $p->recipe && !empty($p->recipe->cost_setting_id);
                                            });
                                            $unassignedProducts = $products->filter(function($p) {
                                                return !$p->recipe || empty($p->recipe->cost_setting_id);
                                            });
                                        @endphp

                                        @if($readyProducts->isNotEmpty())
                                            <optgroup label="✅ Ready for Manufacture (Recipe Configured)">
                                                @foreach($readyProducts as $product)
                                                    @php
                                                        $recipe = $product->recipe;
                                                        $imgs = !empty($recipe->manufacture_images) ? $recipe->manufacture_images : [];
                                                        $mimg = !empty($imgs) && isset($imgs[0]) ? asset($imgs[0]) : ($product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png'));
                                                        $costSettingId = $recipe->cost_setting_id;
                                                    @endphp
                                                    <option value="{{ $product->id }}" 
                                                        data-name="{{ $product->name }}"
                                                        data-mimg="{{ $mimg }}"
                                                        data-cost-setting-id="{{ $costSettingId }}"
                                                        data-has-recipe="1">
                                                        {{ $product->name }} {{ $product->model_no ? '('.$product->model_no.')' : '' }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif

                                        @if($unassignedProducts->isNotEmpty())
                                            <optgroup label="⚠️ Pending Configuration (No Recipe Assigned)">
                                                @foreach($unassignedProducts as $product)
                                                    @php
                                                        $mimg = $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png');
                                                    @endphp
                                                    <option value="{{ $product->id }}" 
                                                        data-name="{{ $product->name }}"
                                                        data-mimg="{{ $mimg }}"
                                                        data-cost-setting-id=""
                                                        data-has-recipe="0"
                                                        disabled>
                                                        {{ $product->name }} {{ $product->model_no ? '('.$product->model_no.')' : '' }} [No Recipe Assigned]
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    </select>
                                </div>

                                <!-- 3. Quantity -->
                                <div class="col-lg-2 col-md-2 col-sm-4 mb-1 mb-lg-0">
                                    <label for="select_qty" class="font-weight-bold">Quantity (each)</label>
                                    <input type="number" id="select_qty" class="form-control" value="1" min="1">
                                </div>

                                <!-- 4. Action Buttons Inline -->
                                <div class="col-lg-3 col-md-2 col-sm-8">
                                    <div class="d-flex">
                                        <button type="button" id="btnAddProduct" class="btn btn-primary mr-50 flex-grow-1">
                                            <i data-feather="plus" class="mr-25"></i> Add to Order
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#browseProductsModal" title="Browse Products Modal">
                                            <i data-feather="grid"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items Table -->
                    <div class="table-responsive table-items-box mb-2">
                        <table class="table table-hover table-bordered mb-0" id="itemsTable">
                            <thead class="thead-light">
                                <tr class="text-center font-weight-bold text-uppercase small">
                                    <th style="width: 40px;">#</th>
                                    <th style="min-width: 200px;" class="text-left">Product & Blueprint</th>
                                    <th style="min-width: 130px;">Ref Invoice</th>
                                    <th style="min-width: 90px; width: 100px;">Quantity</th>
                                    <th style="min-width: 160px;">Cost Title</th>
                                    <th style="min-width: 140px;">Part Type</th>
                                    <th style="min-width: 110px; width: 120px;">Unit Price (৳)</th>
                                    <th style="min-width: 110px; width: 120px;">Total (৳)</th>
                                    <th style="min-width: 150px;">Note</th>
                                    <th style="width: 50px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <tr id="emptyRow">
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        <i data-feather="package" style="width: 38px; height: 38px;" class="text-secondary mb-50"></i>
                                        <h6 class="text-muted font-weight-bold mb-25">No products added yet</h6>
                                        <small>Select Assigned Worker first, then choose products and click <strong>Add to Order</strong>.</small>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light font-weight-bold">
                                <tr>
                                    <td colspan="3" class="text-right">Total Summary:</td>
                                    <td class="text-center text-primary font-weight-bold" id="tableTotalQty">0</td>
                                    <td colspan="3" class="text-right">Grand Total:</td>
                                    <td class="text-right text-success font-weight-bolder" id="tableGrandTotal">৳ 0.00</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <span class="text-danger error-text items_error mb-2 d-block font-weight-bold"></span>

                    <!-- Below That: Dealer, Confirmation & Financial Summary -->
                    <div class="row mt-2">
                        <!-- Left: Customer Details & Order Remarks -->
                        <div class="col-lg-7 col-md-12">
                            <div class="card border">
                                <div class="card-header bg-light py-75 border-bottom">
                                    <h5 class="card-title mb-0 font-weight-bold">
                                        <i data-feather="user-check" class="mr-50 text-primary"></i> Customer & Order Details
                                    </h5>
                                </div>
                                <div class="card-body pt-1">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group mb-1">
                                                <label for="dealer_id" class="font-weight-bold">Select Dealer / Customer <span class="text-danger">*</span></label>
                                                <select name="dealer_id" id="dealer_id" class="form-control select2">
                                                    <option value="">-- Select Dealer --</option>
                                                    @foreach($dealers as $dealer)
                                                        <option value="{{ $dealer->id }}" 
                                                            data-phone="{{ $dealer->phone }}" 
                                                            data-address="{{ $dealer->address }}">
                                                            {{ $dealer->name }} ({{ $dealer->phone }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <span class="text-danger error-text dealer_id_error"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-1">
                                                <label for="dealer_phone">Dealer Phone</label>
                                                <input type="text" id="dealer_phone" class="form-control bg-light" readonly placeholder="Auto-filled phone">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-1">
                                                <label for="dealer_address">Dealer Address</label>
                                                <input type="text" id="dealer_address" class="form-control bg-light" readonly placeholder="Auto-filled address">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-1">
                                        <label for="note" class="font-weight-bold">General Order Remarks / Note (Optional)</label>
                                        <textarea name="note" id="note" class="form-control" rows="2" placeholder="Write any specific notes for this entire order..."></textarea>
                                    </div>

                                    <div class="confirm-order-wrapper mt-1">
                                        <div class="confirm-box" id="confirmBox">
                                            <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                <div class="custom-control custom-checkbox custom-control-primary">
                                                    <input type="checkbox" class="custom-control-input" id="is_confirm" name="is_confirm" value="1">
                                                    <label class="custom-control-label font-weight-bolder text-dark" for="is_confirm">
                                                        Check to Confirm Order Immediately
                                                    </label>
                                                </div>
                                                <span class="badge badge-pill badge-light-secondary font-weight-bold" id="confirmStatusBadge">
                                                    <i data-feather="clock" style="width: 12px; height: 12px;" class="mr-25"></i> Draft / Unconfirmed
                                                </span>
                                            </div>
                                            <div class="confirm-note text-muted font-small-2 mt-50">
                                                <i data-feather="alert-circle" style="width: 13px; height: 13px;" class="mr-25 text-warning align-text-bottom"></i>
                                                Once confirmed, this order moves to confirmed status and cannot be edited or deleted.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Cost & Financial Summary -->
                        <div class="col-lg-5 col-md-12">
                            <div class="card border border-primary">
                                <div class="card-header bg-primary text-white py-75">
                                    <h5 class="card-title text-white mb-0 font-weight-bold">
                                        <i data-feather="dollar-sign" class="mr-50"></i> Order Financial Summary
                                    </h5>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted">Total Products / Items:</span>
                                        <strong id="summaryTotalItems" class="h6 mb-0">0 items</strong>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted">Total Manufacture Quantity:</span>
                                        <strong id="summaryTotalQty" class="h6 mb-0 text-primary">0 units</strong>
                                    </div>
                                    <hr class="my-50">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span>Body Parts Cost Total:</span>
                                        <span class="font-weight-bold text-dark" id="summaryBodyTotal">৳ 0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span>Finishing Parts Cost Total:</span>
                                        <span class="font-weight-bold text-dark" id="summaryFinishingTotal">৳ 0.00</span>
                                    </div>
                                    <hr class="my-1">
                                    <div class="d-flex justify-content-between align-items-center p-1 rounded bg-light-success">
                                        <span class="h5 mb-0 font-weight-bold text-success">Grand Total:</span>
                                        <span class="h4 mb-0 font-weight-bolder text-success" id="summaryGrandTotal">৳ 0.00</span>
                                    </div>

                                    <div class="mt-2">
                                        <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold shadow" id="saveBtn">
                                            <i data-feather="check-circle" class="mr-50"></i> Save Manufacture Order
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Image Preview Lightbox Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-1">
                <h5 class="modal-title font-weight-bold" id="imagePreviewTitle">Manufacture Blueprint</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-2">
                <img src="" id="modalPreviewImg" class="img-fluid rounded border shadow-sm" style="max-height: 400px; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<!-- Browse & Select Multiple Products Modal -->
<div class="modal fade" id="browseProductsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header py-1 bg-light border-bottom">
                <h5 class="modal-title font-weight-bold">
                    <i data-feather="package" class="mr-50 text-primary"></i> Browse & Select Multiple Products
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-2">
                <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap">
                    <div class="flex-grow-1 mr-1 mb-50">
                        <input type="text" id="modalProductSearch" class="form-control" placeholder="Search product by name or model...">
                    </div>
                    <div class="mb-50">
                        <button type="button" class="btn btn-sm btn-outline-primary mr-50" id="btnModalSelectAll">Select All</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnModalDeselectAll">Deselect All</button>
                    </div>
                </div>
                <div class="table-responsive border rounded" style="max-height: 380px; overflow-y: auto;">
                    <table class="table table-sm table-hover mb-0" id="modalProductsTable">
                        <thead class="thead-light sticky-top">
                            <tr>
                                <th style="width: 40px;" class="text-center">
                                    <div class="custom-control custom-checkbox custom-control-inline mr-0">
                                        <input type="checkbox" class="custom-control-input" id="checkAllModalProds">
                                        <label class="custom-control-label" for="checkAllModalProds"></label>
                                    </div>
                                </th>
                                <th style="width: 60px;">Image</th>
                                <th>Product Name</th>
                                <th>Model</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                @php
                                    $recipe = $product->recipe;
                                    $hasRecipe = $recipe && !empty($recipe->cost_setting_id);
                                    $imgs = $recipe && !empty($recipe->manufacture_images) ? $recipe->manufacture_images : [];
                                    $mimg = !empty($imgs) && isset($imgs[0]) ? asset($imgs[0]) : ($product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png'));
                                    $costSettingId = $recipe ? $recipe->cost_setting_id : null;
                                @endphp
                                <tr class="modal-product-row {{ !$hasRecipe ? 'bg-light text-muted' : '' }}" 
                                    data-name="{{ strtolower($product->name) }}" 
                                    data-model="{{ strtolower($product->model_no ?? '') }}"
                                    data-has-recipe="{{ $hasRecipe ? '1' : '0' }}">
                                    <td class="text-center align-middle">
                                        <div class="custom-control custom-checkbox custom-control-inline mr-0">
                                            <input type="checkbox" class="custom-control-input modal-prod-checkbox" 
                                                id="modal_chk_{{ $product->id }}" 
                                                value="{{ $product->id }}"
                                                data-name="{{ $product->name }}"
                                                data-mimg="{{ $mimg }}"
                                                data-has-recipe="{{ $hasRecipe ? '1' : '0' }}"
                                                data-cost-setting-id="{{ $costSettingId }}"
                                                {{ !$hasRecipe ? 'disabled' : '' }}>
                                            <label class="custom-control-label" for="modal_chk_{{ $product->id }}"></label>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <img src="{{ $mimg }}" class="rounded border" style="width: 38px; height: 38px; object-fit: cover; {{ !$hasRecipe ? 'opacity: 0.5;' : '' }}">
                                    </td>
                                    <td class="align-middle">
                                        <span class="font-weight-bold {{ $hasRecipe ? 'text-dark' : 'text-muted' }} d-block">{{ $product->name }}</span>
                                        @if(!$hasRecipe)
                                            <span class="badge badge-pill badge-light-danger font-small-1">
                                                <i data-feather="alert-triangle" style="width: 10px; height: 10px;"></i> No Recipe Assigned - Cannot Add
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-muted">
                                        {{ $product->model_no ?: '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer py-1 d-flex justify-content-between align-items-center">
                <span class="text-muted"><strong id="modalSelectedCount">0</strong> products selected</span>
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="btnModalAddSelected">
                        <i data-feather="plus" class="mr-25"></i> Add Selected to Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Master data passed from server
    const costSettingsMap = {};
    @foreach($costSettings as $cs)
        costSettingsMap[{{ $cs->id }}] = {
            id: {{ $cs->id }},
            title: "{{ addslashes($cs->cost_title) }}",
            body_price: {{ (float) $cs->body_charge }},
            finishing_price: {{ (float) $cs->finishing_charge }}
        };
    @endforeach

    // First active cost setting ID as default fallback
    const defaultCostSettingId = {{ $costSettings->first() ? $costSettings->first()->id : 'null' }};

    let rowCounter = 0;

    // Function to calculate order-level grand totals
    function calculateAllTotals() {
        let totalQty = 0;
        let bodyTotal = 0;
        let finishingTotal = 0;
        let grandTotal = 0;
        let totalItems = 0;

        $('#itemsTableBody tr.item-row').each(function () {
            totalItems++;
            let qty = parseFloat($(this).find('.item-qty').val()) || 0;
            let unitPrice = parseFloat($(this).find('.item-unit-price').val()) || 0;
            let partType = $(this).find('.item-part-type').val();

            totalQty += qty;
            let lineTotal = qty * unitPrice;
            grandTotal += lineTotal;

            if (partType === 'body') {
                bodyTotal += lineTotal;
            } else if (partType === 'finishing') {
                finishingTotal += lineTotal;
            } else if (partType === 'both') {
                let bodyPrice = parseFloat($(this).find('.item-body-price').val()) || 0;
                let finishingPrice = parseFloat($(this).find('.item-finishing-price').val()) || 0;
                bodyTotal += (bodyPrice * qty);
                finishingTotal += (finishingPrice * qty);
            }
        });

        // Toggle empty row placeholder
        if (totalItems === 0) {
            $('#emptyRow').show();
        } else {
            $('#emptyRow').hide();
        }

        // Update table footer
        $('#tableTotalQty').text(totalQty);
        $('#tableGrandTotal').text('৳ ' + grandTotal.toFixed(2));

        // Update summary card
        $('#summaryTotalItems').text(totalItems + (totalItems === 1 ? ' item' : ' items'));
        $('#summaryTotalQty').text(totalQty + ' units');
        $('#summaryBodyTotal').text('৳ ' + bodyTotal.toFixed(2));
        $('#summaryFinishingTotal').text('৳ ' + finishingTotal.toFixed(2));
        $('#summaryGrandTotal').text('৳ ' + grandTotal.toFixed(2));
    }

    // Function to add a product row (requires worker selected first)
    function addProductRow(productId, productName, mimg, costSettingId, qty) {
        let workerId = $('#worker_id').val();
        if (!workerId) {
            toastr.error('Please select an Assigned Worker first before adding products.');
            $('#worker_id').select2('open');
            return false;
        }

        let $selectedWorker = $('#worker_id').find('option:selected');
        let workerType = $selectedWorker.data('type') || 'body'; // 'body' or 'finishing'
        let workerTypeLabel = $selectedWorker.data('type-label') || (workerType === 'finishing' ? 'Finishing Part' : 'Body Part');

        // Check that product has an assigned recipe with a valid cost setting
        if (!costSettingId || !costSettingsMap[costSettingId]) {
            toastr.error('Validation Error: Product "' + productName + '" does not have an assigned Product Recipe / Cost Setting. Please configure its recipe first.');
            return false;
        }

        let effectiveCostSettingId = costSettingId;
        let cs = costSettingsMap[effectiveCostSettingId];
        let csTitle = cs.title;

        // Auto-select Unit Price based on worker's part type
        let unitPrice = (workerType === 'finishing') ? cs.finishing_price : cs.body_price;
        let bodyPrice = cs.body_price;
        let finishingPrice = cs.finishing_price;

        rowCounter++;
        let badgeClass = (workerType === 'finishing') ? 'badge-light-primary' : 'badge-light-info';
        let initialQty = qty || 1;
        let lineTotal = initialQty * unitPrice;

        let rowHtml = `
            <tr class="item-row" data-row-id="${rowCounter}">
                <td class="text-center font-weight-bold text-muted sl-num">${rowCounter}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${mimg}" class="rounded border mr-1 preview-thumbnail" 
                             style="width: 46px; height: 46px; object-fit: cover; cursor: pointer;" 
                             title="Click to view blueprint"
                             data-img="${mimg}" data-title="${productName}">
                        <div>
                            <strong class="d-block text-dark">${productName}</strong>
                            <small class="text-muted"><i data-feather="image" style="width: 12px; height: 12px;"></i> Blueprint attached</small>
                            <input type="hidden" name="items[${rowCounter}][product_id]" value="${productId}">
                            <input type="hidden" name="items[${rowCounter}][cost_title]" class="item-cost-title" value="${csTitle}">
                            <input type="hidden" name="items[${rowCounter}][cost_setting_id]" class="item-cost-setting" value="${effectiveCostSettingId}">
                            <input type="hidden" name="items[${rowCounter}][part_type]" class="item-part-type" value="${workerType}">
                            <input type="hidden" name="items[${rowCounter}][body_part_price]" class="item-body-price" value="${bodyPrice.toFixed(2)}">
                            <input type="hidden" name="items[${rowCounter}][finishing_part_price]" class="item-finishing-price" value="${finishingPrice.toFixed(2)}">
                        </div>
                    </div>
                </td>
                <td>
                    <input type="text" name="items[${rowCounter}][reff_invoice]" class="form-control form-control-sm" placeholder="Ref Invoice">
                </td>
                <td>
                    <input type="number" name="items[${rowCounter}][qty]" class="form-control form-control-sm text-center item-qty font-weight-bold" value="${initialQty}" min="1">
                </td>
                <td class="text-center align-middle">
                    <span class="badge badge-light-secondary font-weight-bold p-50 d-block text-dark item-cost-title-display">
                        ${csTitle}
                    </span>
                </td>
                <td class="text-center align-middle">
                    <span class="badge ${badgeClass} font-weight-bold p-50 d-block item-part-type-display">
                        ${workerTypeLabel}
                    </span>
                </td>
                <td>
                    <input type="number" step="0.01" name="items[${rowCounter}][unit_price]" class="form-control form-control-sm text-right item-unit-price font-weight-bold bg-light" value="${unitPrice.toFixed(2)}" readonly title="Auto-selected based on Worker Type & Cost Title">
                </td>
                <td class="text-right font-weight-bold text-success align-middle item-total-display">
                    ৳ ${lineTotal.toFixed(2)}
                </td>
                <td>
                    <input type="text" name="items[${rowCounter}][note]" class="form-control form-control-sm" placeholder="Item note">
                </td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-outline-danger btn-sm p-25 btn-remove-row" title="Remove Item">
                        <i data-feather="trash-2"></i>
                    </button>
                </td>
            </tr>
        `;

        $('#itemsTableBody').append(rowHtml);

        if (feather) {
            feather.replace();
        }

        calculateAllTotals();
        renumberRows();
        return true;
    }

    function renumberRows() {
        let count = 0;
        $('#itemsTableBody tr.item-row').each(function () {
            count++;
            $(this).find('.sl-num').text(count);
        });
    }

    $(function () {
        $('.select2').select2();

        // Dealer info auto-population
        $('#dealer_id').on('change', function () {
            let selected = $(this).find('option:selected');
            $('#dealer_phone').val(selected.data('phone') || '');
            $('#dealer_address').val(selected.data('address') || '');
        });

        // Worker selection changed -> update badge and recalculate all existing items!
        $('#worker_id').on('change', function () {
            let $selected = $(this).find('option:selected');
            let workerType = $selected.data('type');
            let workerTypeLabel = $selected.data('type-label');

            if (workerType) {
                let badgeClass = (workerType === 'finishing') ? 'badge-light-primary' : 'badge-light-info';
                $('#workerTypeBadge').removeClass('badge-light-secondary badge-light-primary badge-light-info')
                                     .addClass(badgeClass)
                                     .text(workerTypeLabel + ' Worker');
            } else {
                $('#workerTypeBadge').removeClass('badge-light-primary badge-light-info')
                                     .addClass('badge-light-secondary')
                                     .text('Select Worker First');
            }

            // Sync all existing table rows with the new worker's type and prices
            let rowCount = 0;
            $('#itemsTableBody tr.item-row').each(function () {
                rowCount++;
                let $row = $(this);
                let csId = $row.find('.item-cost-setting').val() || defaultCostSettingId;
                let cs = costSettingsMap[csId];
                let qty = parseFloat($row.find('.item-qty').val()) || 0;

                if (workerType && cs) {
                    let unitPrice = (workerType === 'finishing') ? cs.finishing_price : cs.body_price;
                    let badgeClass = (workerType === 'finishing') ? 'badge-light-primary' : 'badge-light-info';

                    $row.find('.item-part-type').val(workerType);
                    $row.find('.item-part-type-display').removeClass('badge-light-primary badge-light-info')
                                                        .addClass(badgeClass)
                                                        .text(workerTypeLabel);
                    $row.find('.item-unit-price').val(unitPrice.toFixed(2));
                    $row.find('.item-total-display').text('৳ ' + (qty * unitPrice).toFixed(2));
                }
            });

            if (rowCount > 0 && workerType) {
                calculateAllTotals();
                toastr.info('Updated all items to ' + workerTypeLabel + ' prices.');
            }
        });

        // Prevent selecting products without recipe in select2 dropdown
        $('#select_product').on('select2:select', function (e) {
            let elem = e.params.data.element;
            if (elem && $(elem).data('has-recipe') == '0') {
                let prodName = $(elem).data('name') || e.params.data.text;
                toastr.error('Validation Error: Product "' + prodName + '" has no Product Recipe assigned! It cannot be added to manufacture orders.');
                let currentVals = $('#select_product').val() || [];
                let filtered = currentVals.filter(v => v != e.params.data.id);
                $('#select_product').val(filtered).trigger('change');
            }
        });

        // Quick Select All / Clear Selection for Products dropdown
        $('#btnSelectAll').on('click', function () {
            let validVals = [];
            $('#select_product option:not(:disabled)').each(function () {
                if ($(this).data('has-recipe') == '1') {
                    validVals.push($(this).val());
                }
            });
            if (validVals.length === 0) {
                toastr.warning('No manufacture products with configured recipes found.');
                return;
            }
            $('#select_product').val(validVals).trigger('change');
            toastr.info(validVals.length + ' products with valid recipes selected.');
        });

        $('#btnClearAll').on('click', function () {
            $('#select_product').val(null).trigger('change');
        });

        // Add Product button clicked (requires worker first)
        $('#btnAddProduct').on('click', function () {
            let workerId = $('#worker_id').val();
            if (!workerId) {
                toastr.error('Please select an Assigned Worker first before adding products.');
                $('#worker_id').select2('open');
                return;
            }

            let $selectedProds = $('#select_product').find('option:selected');
            if ($selectedProds.length === 0) {
                toastr.warning('Please select at least one manufacture product.');
                return;
            }

            let qty = parseInt($('#select_qty').val()) || 1;
            let addedCount = 0;

            $selectedProds.each(function () {
                let productId = $(this).val();
                let productName = $(this).data('name');
                let mimg = $(this).data('mimg');
                let costSettingId = $(this).data('cost-setting-id');

                if (addProductRow(productId, productName, mimg, costSettingId, qty)) {
                    addedCount++;
                }
            });

            // Reset select bar
            $('#select_product').val(null).trigger('change');
            $('#select_qty').val(1);
            if (addedCount > 0) {
                toastr.success(addedCount + ' product' + (addedCount > 1 ? 's' : '') + ' added to order table.');
            }
        });

        // Modal Product Search
        $('#modalProductSearch').on('keyup', function () {
            let term = $(this).val().toLowerCase().trim();
            $('#modalProductsTable tbody tr.modal-product-row').each(function () {
                let name = $(this).data('name') || '';
                let model = $(this).data('model') || '';
                if (name.indexOf(term) !== -1 || model.indexOf(term) !== -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Check All / Deselect All in Modal (only products with recipes)
        $('#checkAllModalProds, #btnModalSelectAll').on('click', function () {
            $('#modalProductsTable tbody tr.modal-product-row:visible input.modal-prod-checkbox:not(:disabled)').prop('checked', true).trigger('change');
            $('#checkAllModalProds').prop('checked', true);
        });

        $('#btnModalDeselectAll').on('click', function () {
            $('#modalProductsTable tbody .modal-prod-checkbox').prop('checked', false).trigger('change');
            $('#checkAllModalProds').prop('checked', false);
        });

        // Clicking on a disabled modal row alerts the user
        $(document).on('click', '#modalProductsTable tbody tr.modal-product-row', function (e) {
            if ($(e.target).is('input[type="checkbox"]') || $(e.target).is('label')) return;
            let $chk = $(this).find('.modal-prod-checkbox');
            if ($chk.is(':disabled')) {
                let name = $chk.data('name');
                toastr.error('Validation Error: Product "' + name + '" does not have an assigned Product Recipe. Please configure its recipe first.');
                return;
            }
            $chk.prop('checked', !$chk.prop('checked')).trigger('change');
        });

        $(document).on('change', '.modal-prod-checkbox', function () {
            let count = $('#modalProductsTable tbody .modal-prod-checkbox:checked').length;
            $('#modalSelectedCount').text(count);
        });

        // Add Selected Products from Modal (requires worker first)
        $('#btnModalAddSelected').on('click', function () {
            let workerId = $('#worker_id').val();
            if (!workerId) {
                toastr.error('Please select an Assigned Worker first before adding products.');
                $('#browseProductsModal').modal('hide');
                $('#worker_id').select2('open');
                return;
            }

            let $checked = $('#modalProductsTable tbody .modal-prod-checkbox:checked');
            if ($checked.length === 0) {
                toastr.warning('Please check at least one product.');
                return;
            }

            let qty = parseInt($('#select_qty').val()) || 1;
            let addedCount = 0;

            $checked.each(function () {
                let productId = $(this).val();
                let productName = $(this).data('name');
                let mimg = $(this).data('mimg');
                let costSettingId = $(this).data('cost-setting-id');

                if (addProductRow(productId, productName, mimg, costSettingId, qty)) {
                    addedCount++;
                    $(this).prop('checked', false);
                }
            });

            $('#checkAllModalProds').prop('checked', false);
            $('#modalSelectedCount').text(0);
            $('#browseProductsModal').modal('hide');
            if (addedCount > 0) {
                toastr.success(addedCount + ' product' + (addedCount > 1 ? 's' : '') + ' added to order table.');
            }
        });

        // Quantity change event
        $(document).on('input', '.item-qty', function () {
            let $row = $(this).closest('tr');
            let qty = parseFloat($(this).val()) || 0;
            let unitPrice = parseFloat($row.find('.item-unit-price').val()) || 0;
            $row.find('.item-total-display').text('৳ ' + (qty * unitPrice).toFixed(2));
            calculateAllTotals();
        });

        // Remove row
        $(document).on('click', '.btn-remove-row', function () {
            $(this).closest('tr').remove();
            renumberRows();
            calculateAllTotals();
            toastr.info('Item removed from order.');
        });

        // Thumbnail zoom preview modal
        $(document).on('click', '.preview-thumbnail', function () {
            let img = $(this).data('img');
            let title = $(this).data('title');
            $('#modalPreviewImg').attr('src', img);
            $('#imagePreviewTitle').text(title + ' - Blueprint');
            $('#imagePreviewModal').modal('show');
        });

        // Form Submission via AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function updateConfirmBoxState() {
            if ($('#is_confirm').is(':checked')) {
                $('#confirmBox').addClass('is-confirmed');
                $('#confirmStatusBadge')
                    .removeClass('badge-light-secondary')
                    .addClass('badge-light-success')
                    .html('<i data-feather="check-circle" style="width: 12px; height: 12px;" class="mr-25"></i> Ready to Confirm');
            } else {
                $('#confirmBox').removeClass('is-confirmed');
                $('#confirmStatusBadge')
                    .removeClass('badge-light-success')
                    .addClass('badge-light-secondary')
                    .html('<i data-feather="clock" style="width: 12px; height: 12px;" class="mr-25"></i> Draft / Unconfirmed');
            }
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
        $('#is_confirm').on('change', updateConfirmBoxState);
        updateConfirmBoxState();

        $('#manufactureForm').on('submit', function (e) {
            e.preventDefault();
            $('.error-text').text('');

            if (!$('#worker_id').val()) {
                $('.worker_id_error').text('Please select an assigned worker.');
                toastr.error('Please select an assigned worker.');
                $('#worker_id').select2('open');
                return;
            }

            let itemCount = $('#itemsTableBody tr.item-row').length;
            if (itemCount === 0) {
                $('.items_error').text('Please add at least one product to the order.');
                toastr.error('Please add at least one product to the order.');
                return;
            }

            if (!$('#dealer_id').val()) {
                $('.dealer_id_error').text('Please select a dealer.');
                toastr.error('Please select a dealer.');
                return;
            }

            $('#saveBtn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving Order...').prop('disabled', true);

            $.ajax({
                url: "{{ route('admin.manufacture.store') }}",
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function (res) {
                    toastr.success(res.success || 'Manufacture Order created successfully!');
                    setTimeout(() => {
                        window.location.href = res.redirect || "{{ route('admin.manufacture.index') }}";
                    }, 1000);
                },
                error: function (xhr) {
                    $('#saveBtn').html('<i data-feather="check-circle" class="mr-50"></i> Save Manufacture Order').prop('disabled', false);
                    if (feather) {
                        feather.replace();
                    }

                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, val) {
                            let fieldName = key.replace(/\./g, '_');
                            $('span.' + fieldName + '_error').text(val[0]);
                        });
                        toastr.error('Validation error! Please check form fields.');
                    } else {
                        toastr.error(xhr.responseJSON.error || 'Something went wrong while saving.');
                    }
                }
            });
        });
    });
</script>
@endpush
