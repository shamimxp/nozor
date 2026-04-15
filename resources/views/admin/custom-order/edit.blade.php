@extends('layouts.admin')
@section('title', 'Edit Custom Order')
@section('content')
<form action="{{ route('admin.custom-order.update', $order->id) }}" method="POST" enctype="multipart/form-data" id="customOrderForm">
    @csrf
    @method('PUT')

    {{-- ───── BASIC INFO ───── --}}
    <div class="card">
        <div class="card-header border-bottom p-1">
            <h4 class="card-title mb-0"><i data-feather="edit" class="mr-50"></i> Edit Custom Order — {{ $order->style_number }}</h4>
        </div>
        <div class="card-body pt-2">
            <div class="row">
                {{-- Style Number --}}
                <div class="col-md-3">
                    <div class="form-group mb-1">
                        <label>Style Number</label>
                        <input type="text" class="form-control bg-light" value="{{ $order->style_number }}" readonly>
                    </div>
                </div>
                {{-- Order Date --}}
                <div class="col-md-3">
                    <div class="form-group mb-1">
                        <label>Order Date <span class="text-danger">*</span></label>
                        <input type="date" name="order_date" class="form-control" value="{{ old('order_date', $order->order_date->format('Y-m-d')) }}" required>
                    </div>
                </div>
                {{-- Customer --}}
                <div class="col-md-3">
                    <div class="form-group mb-1">
                        <label>Customer <span class="text-danger">*</span></label>
                        <select name="customer_id" class="form-control select2" required>
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id', $order->customer_id) == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Order Type --}}
                <div class="col-md-3">
                    <div class="form-group mb-1">
                        <label>Order Type <span class="text-danger">*</span></label>
                        <select name="order_type" class="form-control" required>
                            <option value="take_away" {{ old('order_type', $order->order_type) == 'take_away' ? 'selected' : '' }}>Take Away</option>
                            <option value="home_delivery" {{ old('order_type', $order->order_type) == 'home_delivery' ? 'selected' : '' }}>Home Delivery</option>
                        </select>
                    </div>
                </div>
                {{-- Type --}}
                <div class="col-md-3">
                    <div class="form-group mb-1">
                        <label>Type <span class="text-danger">*</span></label>
                        <select name="type" id="orderType" class="form-control" required>
                            <option value="">-- Select --</option>
                            <option value="polo" {{ old('type', $order->type) == 'polo' ? 'selected' : '' }}>Polo</option>
                            <option value="t-shirt" {{ old('type', $order->type) == 't-shirt' ? 'selected' : '' }}>T-Shirt</option>
                        </select>
                    </div>
                </div>
                {{-- Sleeve --}}
                <div class="col-md-3">
                    <div class="form-group mb-1">
                        <label>Sleeve <span class="text-danger">*</span></label>
                        <select name="sleeve" id="orderSleeve" class="form-control" required>
                            <option value="">-- Select --</option>
                            <option value="half" {{ old('sleeve', $order->sleeve) == 'half' ? 'selected' : '' }}>Half Sleeve</option>
                            <option value="full" {{ old('sleeve', $order->sleeve) == 'full' ? 'selected' : '' }}>Full Sleeve</option>
                        </select>
                    </div>
                </div>
                {{-- Delivery Date --}}
                <div class="col-md-3">
                    <div class="form-group mb-1">
                        <label>Delivery Date</label>
                        <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date', $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
                {{-- Collected Date --}}
                <div class="col-md-3">
                    <div class="form-group mb-1">
                        <label>Collected Date</label>
                        <input type="date" name="collected_date" class="form-control" value="{{ old('collected_date', $order->collected_date ? $order->collected_date->format('Y-m-d') : '') }}">
                    </div>
                </div>
                {{-- Vendor --}}
                <div class="col-md-3">
                    <div class="form-group mb-1">
                        <label>Assign Vendor <small class="text-muted">(Optional)</small></label>
                        <select name="vendor_id" class="form-control select2">
                            <option value="">-- Select Vendor --</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" {{ old('vendor_id', $order->vendor_id) == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Status --}}
                <div class="col-md-3">
                    <div class="form-group mb-1">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ old('status', $order->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>
                {{-- Customer Note --}}
                <div class="col-md-3">
                    <div class="form-group mb-1">
                        <label>Customer Note</label>
                        <textarea name="customer_note" class="form-control" rows="2">{{ old('customer_note', $order->customer_note) }}</textarea>
                    </div>
                </div>
                {{-- Vendor Note --}}
                <div class="col-md-3">
                    <div class="form-group mb-1">
                        <label>Vendor Note</label>
                        <textarea name="vendor_note" class="form-control" rows="2">{{ old('vendor_note', $order->vendor_note) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ───── IMAGES ───── --}}
    <div class="card">
        <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i data-feather="image" class="mr-50"></i> Design Images
                <span class="badge badge-pill badge-light-success ml-50" id="newImageCountBadge" style="display:none;">0 New</span>
            </h4>
            <button type="button" class="btn btn-outline-primary btn-sm" id="addImageBtn">
                <i data-feather="plus" class="mr-25"></i> Add New Images
            </button>
        </div>
        <div class="card-body pt-2">
            {{-- Existing Images --}}
            @if($order->images->count())
            <label class="mb-1 font-weight-bold">Existing Images</label>
            <div class="row mb-2" id="existingImagesRow">
                @foreach($order->images as $img)
                <div class="col-md-2 col-sm-3 col-4 mb-1 text-center" id="existImg{{ $img->id }}">
                    <div class="border rounded p-50 position-relative" style="background:#f8f8f8;">
                        <img src="{{ asset($img->image) }}" class="rounded" style="width:100%;height:110px;object-fit:cover;">
                        <button type="button" class="btn btn-sm btn-outline-danger mt-50 removeExistingImgBtn" data-id="{{ $img->id }}" style="width:100%;">
                            <i data-feather="trash-2" style="width:12px;height:12px;"></i> Remove
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Hidden file picker --}}
            <input type="file" id="imagePickerHidden" class="d-none" multiple accept="image/*">

            {{-- New images preview --}}
            <label class="mb-1 font-weight-bold" id="newImagesLabel" style="display:none;">New Images</label>
            <div id="newImagePreviewContainer" class="row"></div>

            {{-- Hidden container for remove_images checkboxes --}}
            <div id="removeImagesContainer"></div>
        </div>
    </div>

    {{-- ───── FABRIC CART ───── --}}
    <div class="card">
        <div class="card-header border-bottom p-1">
            <h4 class="card-title mb-0"><i data-feather="shopping-cart" class="mr-50"></i> Fabric Selection (Cart)</h4>
        </div>
        <div class="card-body pt-2">
            <div class="row mb-2">
                <div class="col-md-6">
                    <label>Select Fabric Price</label>
                    <select id="fabricPriceSelect" class="form-control select2">
                        <option value="">-- Select Fabric --</option>
                        @foreach($fabricPrices as $fp)
                            <option value="{{ $fp->id }}"
                                    data-fabric="{{ $fp->fabric->name ?? '-' }}"
                                    data-type="{{ $fp->type }}"
                                    data-sleeve="{{ $fp->sleeve }}"
                                    data-price="{{ $fp->price }}">
                                {{ $fp->fabric->name ?? '-' }} – {{ ucfirst($fp->type) }} – {{ ucfirst($fp->sleeve) }} (৳{{ number_format($fp->price, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Quantity</label>
                    <input type="number" id="fabricQtyInput" class="form-control" min="1" value="1">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" id="addToCartBtn" class="btn btn-success btn-block">
                        <i data-feather="plus-circle" class="mr-25"></i> Add to Cart
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="cartTable">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Fabric</th>
                            <th>Type</th>
                            <th>Sleeve</th>
                            <th>Unit Price</th>
                            <th style="width:100px;">Qty</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ───── SUMMARY ───── --}}
    <div class="card">
        <div class="card-header border-bottom p-1">
            <h4 class="card-title mb-0"><i data-feather="dollar-sign" class="mr-50"></i> Order Summary</h4>
        </div>
        <div class="card-body pt-2">
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-bordered mb-0">
                        <tr>
                            <th>Total Quantity</th>
                            <td class="text-right" id="summaryTotalQty">0</td>
                        </tr>
                        <tr>
                            <th>Sub Total</th>
                            <td class="text-right" id="summarySubTotal">৳0.00</td>
                        </tr>
                        <tr>
                            <th>Carrying Charge</th>
                            <td>
                                <input type="number" name="carrying_charge" id="carryingCharge" class="form-control form-control-sm text-right" value="{{ old('carrying_charge', $order->carrying_charge) }}" min="0" step="0.01">
                            </td>
                        </tr>
                        <tr class="table-primary">
                            <th class="font-weight-bolder">Grand Total</th>
                            <td class="text-right font-weight-bolder" id="summaryGrandTotal">৳0.00</td>
                        </tr>
                        <tr>
                            <th>Paid Amount</th>
                            <td>
                                <input type="number" name="paid" id="paidAmount" class="form-control form-control-sm text-right" value="{{ old('paid', $order->paid) }}" min="0" step="0.01">
                            </td>
                        </tr>
                        <tr>
                            <th>Due Amount</th>
                            <td class="text-right text-danger font-weight-bolder" id="summaryDue">৳0.00</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="mt-2 text-right">
                <a href="{{ route('admin.custom-order.index') }}" class="btn btn-outline-secondary mr-1">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i data-feather="save" class="mr-25"></i> Update Order
                </button>
            </div>
        </div>
    </div>

    <div id="cartHiddenInputs"></div>
</form>
@endsection

@push('scripts')
<script>
$(function () {
    // ── Pre-load existing items into cart ──────────────
    var cart = [];
    @foreach($order->items as $item)
    cart.push({
        fpId: {{ $item->fabric_price_id }},
        fabric: "{{ $item->fabric_name }}",
        type: "{{ $item->type }}",
        sleeve: "{{ $item->sleeve }}",
        unitPrice: {{ $item->unit_price }},
        quantity: {{ $item->quantity }},
        total: {{ $item->total }}
    });
    @endforeach

    // ── Multi Image Manager ─────────────────────────────
    var selectedFiles = []; // array of { file, dataUrl }

    // Remove existing image
    $('body').on('click', '.removeExistingImgBtn', function () {
        var imgId = $(this).data('id');
        $('#existImg' + imgId).fadeOut(300, function() { $(this).remove(); });
        $('#removeImagesContainer').append('<input type="hidden" name="remove_images[]" value="' + imgId + '">');
    });

    // Add new images
    $('#addImageBtn').on('click', function () {
        $('#imagePickerHidden').trigger('click');
    });

    $('#imagePickerHidden').on('change', function () {
        var files = this.files;
        if (!files.length) return;

        var filesArray = Array.from(files);
        var loadedCount = 0;

        filesArray.forEach(function(file) {
            var reader = new FileReader();
            reader.onload = function (e) {
                selectedFiles.push({ file: file, dataUrl: e.target.result });
                loadedCount++;
                if (loadedCount === filesArray.length) {
                    renderNewImagePreviews();
                }
            };
            reader.readAsDataURL(file);
        });
        $(this).val('');
    });

    function renderNewImagePreviews() {
        var container = $('#newImagePreviewContainer');
        container.html('');
        
        syncNewFileInput();

        if (selectedFiles.length === 0) {
            $('#newImagesLabel').hide();
            $('#newImageCountBadge').hide();
            return;
        }

        $('#newImagesLabel').show();
        $('#newImageCountBadge').text(selectedFiles.length + ' New').show();

        selectedFiles.forEach(function (item, idx) {
            container.append(
                '<div class="col-md-2 col-sm-4 col-6 mb-1">' +
                    '<div class="card border mb-0" style="background:#f0f9f0;">' +
                        '<div class="card-body p-50 text-center">' +
                            '<img src="' + item.dataUrl + '" class="rounded mb-50" style="width:100%; height:120px; object-fit:cover;">' +
                            '<button type="button" class="btn btn-flat-danger btn-sm p-25 removeNewImageBtn" data-idx="' + idx + '" style="width:100%;">' +
                                '<i data-feather="trash-2"></i> Remove' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
        });

        if (typeof feather !== 'undefined') feather.replace({width: 14, height: 14});
    }

    function syncNewFileInput() {
        $('#imageRealInput').remove();
        if (selectedFiles.length === 0) return;

        var dt = new DataTransfer();
        selectedFiles.forEach(function (item) {
            dt.items.add(item.file);
        });

        var realInput = $('<input type="file" name="images[]" id="imageRealInput" multiple class="d-none">');
        realInput[0].files = dt.files;
        $('#customOrderForm').append(realInput);
    }

    $('body').on('click', '.removeNewImageBtn', function () {
        var idx = $(this).data('idx');
        selectedFiles.splice(idx, 1);
        renderNewImagePreviews();
    });

    // ── Add to Cart ───────────────────────────────────
    $('#addToCartBtn').on('click', function () {
        var sel = $('#fabricPriceSelect');
        var fpId = sel.val();
        if (!fpId) { toastr.warning('Please select a fabric.'); return; }
        var qty = parseInt($('#fabricQtyInput').val()) || 1;
        if (qty < 1) { toastr.warning('Quantity must be at least 1.'); return; }

        var opt    = sel.find('option:selected');
        var fabric = opt.data('fabric');
        var type   = opt.data('type');
        var sleeve = opt.data('sleeve');
        var price  = parseFloat(opt.data('price'));

        var exists = cart.find(c => c.fpId == fpId);
        if (exists) {
            exists.quantity += qty;
            exists.total = exists.quantity * exists.unitPrice;
        } else {
            cart.push({
                fpId: fpId, fabric: fabric, type: type, sleeve: sleeve,
                unitPrice: price, quantity: qty, total: price * qty
            });
        }

        renderCart();
        sel.val('').trigger('change');
        $('#fabricQtyInput').val(1);
    });

    // ── Render Cart ───────────────────────────────────
    function renderCart() {
        var tbody = $('#cartTable tbody');
        var hidden = $('#cartHiddenInputs');
        tbody.html('');
        hidden.html('');

        var totalQty = 0, subTotal = 0;

        cart.forEach(function (item, idx) {
            totalQty += item.quantity;
            subTotal += item.total;

            tbody.append(
                '<tr>' +
                '<td>' + (idx + 1) + '</td>' +
                '<td>' + item.fabric + '</td>' +
                '<td>' + ucfirst(item.type) + '</td>' +
                '<td>' + ucfirst(item.sleeve) + '</td>' +
                '<td class="text-right">৳' + item.unitPrice.toFixed(2) + '</td>' +
                '<td><input type="number" class="form-control form-control-sm text-center cartQtyChange" data-idx="' + idx + '" value="' + item.quantity + '" min="1"></td>' +
                '<td class="text-right">৳' + item.total.toFixed(2) + '</td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger removeCartItem" data-idx="' + idx + '"><i data-feather="x"></i></button></td>' +
                '</tr>'
            );

            hidden.append('<input type="hidden" name="cart_fabric_price_id[]" value="' + item.fpId + '">');
            hidden.append('<input type="hidden" name="cart_quantity[]" value="' + item.quantity + '">');
        });

        if (cart.length === 0) {
            tbody.append('<tr><td colspan="8" class="text-center text-muted">No fabrics added yet.</td></tr>');
        }

        updateSummary(totalQty, subTotal);
        if (typeof feather !== 'undefined') feather.replace({width: 14, height: 14});
    }

    $('body').on('change', '.cartQtyChange', function () {
        var idx = $(this).data('idx');
        var newQty = parseInt($(this).val()) || 1;
        if (newQty < 1) newQty = 1;
        cart[idx].quantity = newQty;
        cart[idx].total = cart[idx].unitPrice * newQty;
        renderCart();
    });

    $('body').on('click', '.removeCartItem', function () {
        var idx = $(this).data('idx');
        cart.splice(idx, 1);
        renderCart();
    });

    function updateSummary(totalQty, subTotal) {
        var carrying = parseFloat($('#carryingCharge').val()) || 0;
        var grand    = subTotal + carrying;
        var paid     = parseFloat($('#paidAmount').val()) || 0;
        var due      = grand - paid;

        $('#summaryTotalQty').text(totalQty);
        $('#summarySubTotal').text('৳' + subTotal.toFixed(2));
        $('#summaryGrandTotal').text('৳' + grand.toFixed(2));
        $('#summaryDue').text('৳' + due.toFixed(2));
    }

    $('#carryingCharge, #paidAmount').on('input', function () {
        var totalQty = 0, subTotal = 0;
        cart.forEach(function (item) { totalQty += item.quantity; subTotal += item.total; });
        updateSummary(totalQty, subTotal);
    });

    $('#customOrderForm').on('submit', function (e) {
        if (cart.length === 0) {
            e.preventDefault();
            toastr.error('Please add at least one fabric to the cart.');
            return false;
        }
    });

    function ucfirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : ''; }

    // Initialize cart render with existing items
    renderCart();
    if (typeof feather !== 'undefined') feather.replace({width: 14, height: 14});
});
</script>
@endpush
