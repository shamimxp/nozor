@extends('layouts.admin')
@section('title', 'Create Custom Order')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }
    .card-premium { border: none; border-radius: 12px; box-shadow: var(--card-shadow); overflow: hidden; margin-bottom: 2rem; }
    .card-premium .card-header { background: #fff; border-bottom: 1px solid rgba(0,0,0,0.05); padding: 1.25rem; }
    .sticky-summary { position: sticky; top: 100px; }
    .summary-item { display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px dashed #e5e7eb; }
    .grand-total-box { background: #f8fafc; border-radius: 8px; padding: 1rem; margin-top: 1rem; border: 1px solid #e2e8f0; }
    .btn-premium-save { background: var(--success-gradient); color: white; border: none; padding: 0.8rem 2rem; font-weight: 600; border-radius: 8px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
    .btn-premium-save:hover { filter: brightness(1.1); color: white; }
    .section-title { font-weight: 700; color: #1e293b; font-size: 1.1rem; display: flex; align-items: center; }
    .section-title i { margin-right: 10px; color: #6366f1; }
    .btn-indigo { background-color: #6366f1; color: white; }
</style>

<div class="content-header mb-2 mt-n1">
    <div class="row align-items-center">
        <div class="col">
            <h2 class="font-weight-bolder mb-0">Create Custom Order</h2>
            <p class="">Unique Ref: <span class="text-primary font-weight-bold">{{ $orderNumber }}</span></p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.custom-order.index') }}" class="btn btn-outline-secondary">
                <i data-feather="arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<form action="{{ route('admin.custom-order.store') }}" method="POST" enctype="multipart/form-data" id="customOrderForm">
    @csrf

    <div class="row">
        <div class="col-lg-8">
            {{-- 1. Basic Info --}}
            <div class="card card-premium">
                <div class="card-header"><span class="section-title"><i data-feather="user"></i> Basic Information</span></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="font-weight-bold small">ORDER NUMBER</label>
                            <input type="text" name="order_number" class="form-control bg-light" value="{{ $orderNumber }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold small">STYLE REF</label>
                            <input type="text" name="style_number" class="form-control bg-light" value="{{ $styleNumber }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold small">ORDER DATE <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 mt-1">
                            <label class="font-weight-bold small">CUSTOMER <span class="text-danger">*</span></label>
                            <select name="customer_id" class="form-control select2" required>
                                <option value="">-- Select Customer --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-1">
                            <label class="font-weight-bold small">ORDER STATUS</label>
                            <select name="status" id="orderStatus" class="form-control font-weight-bold text-primary select2">
                                <option value="pending">Pending</option>
                                <option value="order_confirm">Confirm</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </div>
                        <div class="col-md-4 mt-1">
                            <label class="font-weight-bold small">Delivery Date <span class="text-danger">*</span></label>
                            <input type="date" name="delivery_date" class="form-control" value="" required>
                        </div>
                        <div class="col-md-4 mt-1">
                            <label class="font-weight-bold small">ITEM TYPE <span class="text-danger">*</span></label>
                            <select name="type" id="filterType" class="form-control select2" required>
                                <option value="polo">Polo</option>
                                <option value="t-shirt">T-Shirt</option>
                            </select>
                        </div>
                        <div class="col-md-4 mt-1">
                            <label class="font-weight-bold small">SLEEVE <span class="text-danger">*</span></label>
                            <select name="sleeve" id="filterSleeve" class="form-control select2" required>
                                <option value="half">Half Sleeve</option>
                                <option value="full">Full Sleeve</option>
                            </select>
                        </div>
                        <div class="col-md-4 mt-1">
                            <label class="font-weight-bold small">PICKUP TYPE</label>
                            <select name="order_type" class="form-control select2">
                                <option value="take_away">Ready for Takeaway</option>
                                <option value="home_delivery">Home Delivery</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Fabric Specs --}}
            <div class="card card-premium">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="section-title"><i data-feather="shopping-cart"></i> Fabric Specification</span>
                </div>
                <div class="card-body">
                    <div class="bg-light p-1 rounded-lg border row mx-0 mb-2">
                        <div class="col-md-7 px-50">
                            <label class="small font-weight-bold ">Fabric Type</label>
                            <select id="fabricPriceSelect" class="form-control select2">
                                <option value="">Select fabric...</option>
                                @foreach($fabricPrices as $fp)
                                    <option value="{{ $fp->id }}"
                                            data-fabric="{{ $fp->fabric->name ?? '-' }}"
                                            data-type="{{ $fp->type }}"
                                            data-sleeve="{{ $fp->sleeve }}"
                                            data-price="{{ $fp->price }}">
                                        {{ $fp->fabric->name ?? '-' }} ({{ ucfirst($fp->type) }} - {{ ucfirst($fp->sleeve) }}) - ৳{{ $fp->price }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 px-50">
                            <label class="small font-weight-bold ">Qty</label>
                            <input type="number" id="fabricQtyInput" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-3 px-50 d-flex align-items-end">
                            <button type="button" id="addToCartBtn" class="btn btn-indigo btn-block">Add to Order</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="cartTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>Spec</th>
                                    <th class="text-right">Price</th>
                                    <th class="text-center" style="width:100px;">Qty</th>
                                    <th class="text-right">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 4. Images --}}
            <div class="card card-premium">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="section-title"><i data-feather="image"></i> Design Images</span>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="addImageBtn"><i data-feather="upload"></i> Add Images</button>
                </div>
                <div class="card-body">
                    <input type="file" id="imagePickerHidden" class="d-none" multiple accept="image/*">
                    <div id="imagePreviewContainer" class="row">
                        <div class="col-12 text-center py-2 ">No images attached.</div>
                    </div>
                </div>
            </div>

            {{-- 5. Notes Section --}}
            <div class="card card-premium">
                <div class="card-header"><span class="section-title"><i data-feather="file-text"></i> Special Instructions</span></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="font-weight-bold small  text-uppercase">Customer Note</label>
                            <textarea name="customer_note" class="form-control" rows="3" placeholder="Enter customer requirements..."></textarea>
                        </div>
                        <!-- <div class="col-md-6">
                            <label class="font-weight-bold small  text-uppercase">Vendor Instruction</label>
                            <textarea name="vendor_note" class="form-control" rows="3" placeholder="Internal notes for vendor..."></textarea>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-summary">
                <div class="card card-premium border-primary">
                    <div class="card-header bg-primary text-white"><span class="h5 mb-0 text-white">Summary</span></div>
                    <div class="card-body">
                        <div class="summary-item"><span>Total Quantity</span> <span id="summaryTotalQty">0 Pcs</span></div>
                        <div class="summary-item"><span>Sub Total</span> <span id="summarySubTotal">৳0.00</span></div>
                        <div class="summary-item align-items-center">
                            <span>Carrying Charge</span>
                            <input type="number" name="carrying_charge" id="carryingCharge" class="form-control form-control-sm text-right" style="width:100px" value="0.00">
                        </div>
                        <div class="grand-total-box d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 font-weight-bold">Grand Total</span>
                            <span class="h4 mb-0 text-primary font-weight-bolder" id="summaryGrandTotal">৳0.00</span>
                        </div>
                        <div class="mt-2">
                            <label class="small  font-weight-bold">PAID AMOUNT</label>
                            <input type="number" name="paid" id="paidAmount" class="form-control font-weight-bold text-success text-right" value="0">
                            <div class="summary-item border-0 pt-1">
                                <span class="text-danger font-weight-bold">Amount Due</span>
                                <span class="h5 mb-0 text-danger font-weight-bolder" id="summaryDue">৳0.00</span>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group mb-2">
                            <label class="font-weight-bold small ">VENDOR <span id="vendorRequired" class="text-danger d-none">*</span></label>
                            <select name="vendor_id" id="vendorSelect" class="form-control select2">
                                <option value="">-- Choose Vendor --</option>
                                @foreach($vendors as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-premium-save btn-block">SAVE ORDER</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="cartHiddenInputs"></div>
</form>
@endsection

@push('scripts')
<script>
$(function () {
    'use strict';
    var cart = [], selectedFiles = [];

    // Filter Logic
    function filterFabrics() {
        var type = $('#filterType').val(), sleeve = $('#filterSleeve').val();
        $('#fabricPriceSelect option').each(function() {
            var oType = $(this).data('type'), oSleeve = $(this).data('sleeve');
            if (!$(this).val()) return;
            if (oType == type && oSleeve == sleeve) $(this).show().prop('disabled', false);
            else $(this).hide().prop('disabled', false);
        });
        $('#fabricPriceSelect').val('').trigger('change');
    }
    $('#filterType, #filterSleeve').on('change', filterFabrics);
    filterFabrics();

    // Status logic
    $('#orderStatus').on('change', function() {
        if ($(this).val() == 'purchase_order') $('#vendorRequired').removeClass('d-none');
        else $('#vendorRequired').addClass('d-none');
    });

    $('#customOrderForm').on('submit', function(e) {
        if (cart.length == 0) { e.preventDefault(); toastr.error('Add at least one fabric spec.'); return; }
        if ($('#orderStatus').val() == 'purchase_order' && !$('#vendorSelect').val()) {
            e.preventDefault(); toastr.error('Vendor is required for Purchase Order status.');
            return;
        }
    });

    // Images
    $('#addImageBtn').on('click', () => $('#imagePickerHidden').trigger('click'));
    $('#imagePickerHidden').on('change', function() {
        Array.from(this.files).forEach(f => {
            let r = new FileReader();
            r.onload = e => { selectedFiles.push({ file: f, url: e.target.result }); renderImages(); };
            r.readAsDataURL(f);
        });
        $(this).val('');
    });
    function renderImages() {
        var c = $('#imagePreviewContainer').html('');
        syncFiles();
        if (!selectedFiles.length) c.append('<div class="col-12 text-center py-2 ">No images attached.</div>');
        else selectedFiles.forEach((it, i) => {
            c.append('<div class="col-md-3 mb-1"><div class="card border p-25 text-center"><img src="'+it.url+'" style="width:100%; height:120px; object-fit:cover;" class="rounded mb-50"><button type="button" class="btn btn-sm btn-outline-danger w-100 removeImg" data-idx="'+i+'">Remove</button></div></div>');
        });
    }
    function syncFiles() {
        $('#imageRealInput').remove();
        if (selectedFiles.length) {
            var dt = new DataTransfer(); selectedFiles.forEach(s => dt.items.add(s.file));
            var inp = $('<input type="file" name="images[]" id="imageRealInput" multiple class="d-none">');
            inp[0].files = dt.files; $('#customOrderForm').append(inp);
        }
    }
    $('body').on('click', '.removeImg', function() { selectedFiles.splice($(this).data('idx'), 1); renderImages(); });

    // Cart logic
    $('#addToCartBtn').on('click', function() {
        var sel = $('#fabricPriceSelect'), fpId = sel.val(); if (!fpId) return;
        var opt = sel.find('option:selected'), qty = parseInt($('#fabricQtyInput').val()) || 1;
        var exists = cart.find(c => c.fpId == fpId);
        if (exists) { exists.qty += qty; exists.total = exists.qty * exists.price; }
        else { cart.push({ fpId, fabric: opt.data('fabric'), type: opt.data('type'), sleeve: opt.data('sleeve'), price: parseFloat(opt.data('price')), qty, total: parseFloat(opt.data('price')) * qty }); }
        renderCart(); sel.val('').trigger('change'); $('#fabricQtyInput').val(1);
    });

    function renderCart() {
        var tb = $('#cartTable tbody').html(''), h = $('#cartHiddenInputs').html('');
        var tQ = 0, sT = 0;
        cart.forEach((it, idx) => {
            tQ += it.qty; sT += it.total;
            tb.append('<tr><td>'+it.fabric+'<br><small>'+it.type+' • '+it.sleeve+'</small></td><td class="text-right">৳'+it.price.toFixed(2)+'</td><td class="text-center"><input type="number" class="form-control form-control-sm text-center cartQty" data-idx="'+idx+'" value="'+it.qty+'" style="width:70px; margin:auto;"></td><td class="text-right">৳'+it.total.toFixed(2)+'</td><td><button type="button" class="btn btn-flat-danger p-25 removeCart" data-idx="'+idx+'"><i data-feather="trash-2"></i></button></td></tr>');
            h.append('<input type="hidden" name="cart_fabric_price_id[]" value="'+it.fpId+'"><input type="hidden" name="cart_quantity[]" value="'+it.qty+'">');
        });
        $('#summaryTotalQty').text(tQ + ' Pcs');
        $('#summarySubTotal').text('৳' + sT.toLocaleString());
        updateFinals(sT); feather.replace();
    }

    function updateFinals(sub) {
        var c = parseFloat($('#carryingCharge').val()) || 0, p = parseFloat($('#paidAmount').val()) || 0;
        var g = sub + c; $('#summaryGrandTotal').text('৳' + g.toLocaleString());
        $('#summaryDue').text('৳' + (g - p).toLocaleString());
    }
    $('body').on('change', '.cartQty', function() {
        var idx = $(this).data('idx'); cart[idx].qty = Math.max(1, parseInt($(this).val()) || 1);
        cart[idx].total = cart[idx].qty * cart[idx].price; renderCart();
    });
    $('body').on('click', '.removeCart', function() { cart.splice($(this).data('idx'), 1); renderCart(); });
    $('#carryingCharge, #paidAmount').on('input', () => { var sub = 0; cart.forEach(c => sub += c.total); updateFinals(sub); });

    $('.select2').select2({ width: '100%' });
});
</script>
@endpush
