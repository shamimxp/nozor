@extends('layouts.admin')
@section('title', 'Edit Dealer Order - ' . $order->order_number)

@section('content')
<style>
    :root {
        --primary-color: #6366f1;
        --primary-hover: #4f46e5;
        --success-color: #10b981;
        --dark-text: #1e293b;
        --muted-text: #64748b;
        --border-color: #e2e8f0;
        --bg-light: #f8fafc;
        --card-radius: 16px;
        --transition: all 0.3s ease;
    }

    .page-title { font-weight: 800; color: var(--dark-text); font-size: 1.5rem; letter-spacing: -0.5px; }
    .ref-badge { background: #e0e7ff; color: #4338ca; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 0.9rem; border: 1px solid #c7d2fe; }

    .card-modern { background: #ffffff; border: 1px solid rgba(226,232,240,0.8); border-radius: var(--card-radius); box-shadow: 0 4px 20px -4px rgba(0,0,0,0.03); margin-bottom: 1.5rem; overflow: hidden; }
    .card-modern .card-header { background: transparent; border-bottom: 1px solid var(--border-color); padding: 1.5rem 1.75rem; }
    .card-modern .card-body { padding: 1.75rem; }

    .section-title { font-weight: 700; color: var(--dark-text); font-size: 1.15rem; display: flex; align-items: center; margin: 0; }
    .section-title i { margin-right: 12px; color: var(--primary-color); background: #eef2ff; padding: 8px; border-radius: 8px; width: 34px; height: 34px; }

    .form-group label { font-weight: 600; color: var(--muted-text); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .form-control { border: 1px solid var(--border-color) !important; border-radius: 8px !important; padding: 0.6rem 1rem !important; height: 42px !important; font-size: 0.95rem; color: var(--dark-text) !important; background-color: var(--bg-light) !important; transition: var(--transition); box-shadow: none !important; }
    textarea.form-control { height: auto !important; }
    .form-control:focus { border-color: var(--primary-color) !important; background-color: #fff !important; }

    /* Select2 */
    .select2-hidden-accessible { border: 0 !important; clip: rect(0 0 0 0) !important; height: 1px !important; margin: -1px !important; overflow: hidden !important; padding: 0 !important; position: absolute !important; width: 1px !important; white-space: nowrap !important; }
    .select2-container { width: 100% !important; }
    .select2-container .select2-selection--single { height: 42px !important; border: 1px solid var(--border-color) !important; border-radius: 8px !important; background-color: var(--bg-light) !important; display: flex !important; align-items: center !important; padding: 0 12px !important; outline: none !important; box-shadow: none !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { padding-left: 0 !important; color: var(--dark-text) !important; font-weight: 500; line-height: normal !important; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 42px !important; top: 0 !important; right: 10px !important; }
    .select2-container--open .select2-selection--single { border-color: var(--primary-color) !important; background-color: #fff !important; }
    .select2-dropdown { border: 1px solid var(--border-color) !important; border-radius: 8px !important; box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important; overflow: hidden !important; margin-top: 4px !important; }
    .select2-container--open .select2-dropdown { border-color: var(--primary-color) !important; }
    .select2-search--dropdown .select2-search__field { height: 36px !important; border: 1px solid var(--border-color) !important; border-radius: 6px !important; padding: 0.4rem 0.75rem !important; font-size: 0.9rem !important; background-color: #fff !important; box-shadow: none !important; outline: none !important; margin: 6px !important; width: calc(100% - 12px) !important; color: var(--dark-text) !important; }
    .select2-search--dropdown .select2-search__field:focus { border-color: var(--primary-color) !important; }
    .select2-results__option { padding: 8px 14px !important; font-size: 0.9rem !important; color: var(--dark-text) !important; font-weight: 500 !important; }
    .select2-results__option--highlighted { background-color: var(--primary-color) !important; color: #fff !important; }
    .select2-results__option[aria-selected=true] { background-color: #eef2ff !important; color: var(--primary-color) !important; }

    /* Table */
    .table-modern { border-collapse: separate; border-spacing: 0 8px; margin-top: -8px; }
    .table-modern thead th { border: none; background: transparent; color: var(--muted-text); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; padding: 10px 15px; }
    .table-modern tbody tr { box-shadow: 0 2px 8px rgba(0,0,0,0.02); border-radius: 10px; background: #fff; border: 1px solid var(--border-color); transition: var(--transition); }
    .table-modern tbody tr:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.05); }
    .table-modern td { border-top: 1px solid var(--border-color) !important; border-bottom: 1px solid var(--border-color) !important; padding: 16px 15px !important; vertical-align: middle; font-weight: 500; color: var(--dark-text); }
    .table-modern td:first-child { border-left: 1px solid var(--border-color) !important; border-top-left-radius: 10px; border-bottom-left-radius: 10px; }
    .table-modern td:last-child { border-right: 1px solid var(--border-color) !important; border-top-right-radius: 10px; border-bottom-right-radius: 10px; }

    /* Summary Panel */
    .summary-panel { background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); border-radius: var(--card-radius); color: white; padding: 2rem; position: sticky; top: 90px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2); }
    .summary-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem; display: flex; align-items: center; color: #f8fafc; }
    .summary-title i { color: var(--primary-color); margin-right: 10px; }
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.1); color: #cbd5e1; font-weight: 500; }
    .summary-row .form-control { background: rgba(255,255,255,0.1) !important; border: 1px solid rgba(255,255,255,0.2) !important; color: white !important; width: 110px; }
    .grand-total-box { background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3); border-radius: 12px; padding: 1.5rem; margin: 1.5rem 0; display: flex; justify-content: space-between; align-items: center; }
    .grand-total-box .title { color: #e0e7ff; font-weight: 600; font-size: 1.1rem; }
    .grand-total-box .amount { color: #fff; font-weight: 800; font-size: 1.5rem; letter-spacing: -0.5px; }
    .btn-submit { background: var(--primary-color); color: white; border: none; padding: 1rem; font-weight: 700; border-radius: 12px; font-size: 1.1rem; transition: var(--transition); box-shadow: 0 4px 14px rgba(99,102,241,0.4); }
    .btn-submit:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 6px 20px rgba(99,102,241,0.6); color: white; }
    .input-group { display: flex; align-items: stretch; width: 100%; border-radius: 8px; overflow: hidden; }
    .input-group-prepend { display: flex; }
    .input-group-text { display: flex; align-items: center; padding: 0.375rem 0.75rem; font-size: 1rem; border-radius: 8px 0 0 8px !important; border-right: 0 !important; }
    .input-group .form-control { border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; }
</style>

<div class="content-header mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="page-title mb-1">Edit Dealer Order</h2>
            <div class="d-flex align-items-center">
                <span class="text-muted mr-2">Reference ID:</span>
                <span class="ref-badge">{{ $order->order_number }}</span>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.dealer-order.show', $order->id) }}" class="btn btn-outline-secondary mr-1" style="border-radius: 8px; font-weight: 600;">
                <i data-feather="eye" class="mr-1"></i> View Order
            </a>
            <a href="{{ route('admin.dealer-order.index') }}" class="btn btn-outline-secondary" style="border-radius: 8px; font-weight: 600;">
                <i data-feather="arrow-left" class="mr-1"></i> Back to List
            </a>
        </div>
    </div>
</div>

<form action="{{ route('admin.dealer-order.update', $order->id) }}" method="POST" enctype="multipart/form-data" id="dealerOrderForm">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-lg-8">
            <!-- Order Details -->
            <div class="card-modern">
                <div class="card-header">
                    <h4 class="section-title"><i data-feather="info"></i> Order Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Order Number</label>
                            <input type="text" name="order_number" class="form-control" value="{{ $order->order_number }}" readonly style="background: #f1f5f9 !important; font-weight: 700; color: #475569 !important; cursor: not-allowed;">
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Order Date <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control" value="{{ $order->order_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>Order Status</label>
                            <select name="status" id="orderStatus" class="form-control select2">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirm" {{ $order->status == 'confirm' ? 'selected' : '' }}>Confirm</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-12 form-group mt-2 mb-0">
                            <label>Select Dealer <span class="text-danger">*</span></label>
                            <select name="dealer_id" class="form-control select2" required>
                                <option value="">-- Choose a Dealer --</option>
                                @foreach($dealers as $c)
                                    <option value="{{ $c->id }}" {{ $order->dealer_id == $c->id ? 'selected' : '' }}>{{ $c->shop_name }} ({{ $c->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="card-modern">
                <div class="card-header d-flex justify-content-between align-items-center border-0 pb-0">
                    <h4 class="section-title"><i data-feather="shopping-cart"></i> Cart Items</h4>
                </div>
                <div class="card-body">
                    <div class="form-group mb-4 p-3 rounded" style="background: var(--bg-light); border: 1px dashed var(--border-color);">
                        <label class="text-primary"><i data-feather="search" width="14" class="mr-1"></i> Search & Add Product</label>
                        <select id="productSelect" class="form-control select2" data-placeholder="Type product name to add...">
                            <option value="" disabled selected>Type product name to add...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-image="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : '' }}">
                                    {{ $product->name ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="table-responsive">
                        <table class="table w-100 table-modern" id="cartTable">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th class="text-right">Unit Price</th>
                                    <th class="text-center" style="width: 140px;">Quantity</th>
                                    <th class="text-right">Line Total</th>
                                    <th class="text-center" style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Product Attachments -->
            <div class="card-modern d-none" id="designImagesCard">
                <div class="card-header">
                    <h4 class="section-title"><i data-feather="image"></i> Product Attachments</h4>
                </div>
                <div class="card-body">
                    <div id="imagePreviewContainer" class="row">
                        <div class="col-12 text-center text-muted py-3 img-empty-state">No images available.</div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card-modern mb-xl-0 mb-3">
                <div class="card-header border-bottom">
                    <h4 class="section-title"><i data-feather="file-text"></i> Additional Notes</h4>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label>Order Note</label>
                        <textarea name="note" class="form-control" rows="3" placeholder="Enter any special instructions or order notes here...">{{ $order->note }}</textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label>Admin Note</label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Enter admin specific notes here...">{{ $order->admin_notes }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Summary Panel -->
        <div class="col-lg-4">
            <div class="summary-panel">
                <h3 class="summary-title"><i data-feather="pie-chart"></i> Order Summary</h3>

                <div class="summary-row">
                    <span>Total Quantity</span>
                    <span id="summaryTotalQty" class="font-weight-bold text-white">0 Pcs</span>
                </div>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="summarySubTotal" class="font-weight-bold text-white">৳0.00</span>
                </div>
                <div class="summary-row border-0 pb-0">
                    <span>Discount</span>
                    <input type="number" name="discount" id="discount" class="form-control text-right" placeholder="0.00" value="{{ $order->discount }}">
                </div>
                <div class="summary-row mt-2">
                    <span>Carrying Charge</span>
                    <input type="number" name="carrying_charge" id="carryingCharge" class="form-control text-right" placeholder="0.00" value="{{ $order->carrying_charge }}">
                </div>

                <div class="grand-total-box">
                    <span class="title">Grand Total</span>
                    <span class="amount" id="summaryGrandTotal">৳0.00</span>
                </div>

                <div class="mt-4 p-3 rounded" style="background: rgba(0,0,0,0.2);">
                    <div class="form-group mb-2">
                        <label class="text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px; color: #94a3b8 !important;">Payment Received</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-transparent border-right-0" style="border-color: rgba(255,255,255,0.2); color: #10b981;">৳</span>
                            </div>
                            <input type="number" name="paid" id="paidAmount" class="form-control border-left-0 font-weight-bold text-right" value="{{ $order->paid }}" style="font-size: 1.1rem; background: rgba(255,255,255,0.1) !important; border-color: rgba(255,255,255,0.2) !important; color: #ffffff !important;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                        <span style="color: #f87171; font-size: 0.9rem; font-weight: 600; text-transform: uppercase;">Amount Due</span>
                        <span id="summaryDue" style="color: #f87171; font-size: 1.25rem; font-weight: 800;">৳0.00</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-submit btn-block mt-4 w-100">
                    <i data-feather="save" class="mr-2"></i> UPDATE ORDER
                </button>
            </div>
        </div>
    </div>

    <div id="cartHiddenInputs"></div>
</form>
@endsection
@push('scripts')
<script>
    let cart = [];

    $(document).ready(function() {
        // Pre-load existing cart items
        @foreach($order->items as $item)
        cart.push({
            id: {{ $item->product_id }},
            name: "{!! addslashes($item->product ? $item->product->name : '-') !!}",
            price: {{ $item->price }},
            qty: {{ $item->qty }},
            image: "{!! ($item->product && $item->product->featured_image) ? asset(config('imagepath.product') . $item->product->featured_image) : '' !!}"
        });
        @endforeach

        renderCart();

        // Initialize Select2
        $('select[name="status"]').select2({ minimumResultsForSearch: Infinity });
        $('select[name="dealer_id"], #productSelect').select2({
            placeholder: function() { return $(this).data('placeholder') || '-- Select --'; },
        });

        $('#productSelect').on('change', function() {
            let productId = $(this).val();
            let dealerId = $('select[name="dealer_id"]').val();

            if (!productId) return;

            if (!dealerId) {
                toastr.error('Please select a dealer first.');
                $(this).val('').trigger('change.select2');
                return;
            }

            let productName = $(this).find(':selected').text().trim();
            let productImage = $(this).find(':selected').data('image');

            $.ajax({
                url: "{{ route('admin.product-price-calculator') }}",
                type: "GET",
                data: { product_id: productId, dealer_id: dealerId },
                success: function(res) {
                    if (res.success && res.data) {
                        addToCart(productId, productName, res.data.dealer_price, productImage);
                    } else {
                        toastr.error('Error fetching product price calculation');
                    }
                },
                error: function() { toastr.error('Failed to fetch product price'); }
            });

            $(this).val('').trigger('change.select2');
        });

        $('#discount, #carryingCharge, #paidAmount').on('input', updateSummary);
    });

    function addToCart(id, name, price, image) {
        let existing = cart.find(i => i.id == id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id, name, price, qty: 1, image });
        }
        renderCart();
    }

    function renderCart() {
        let tbody = $('#cartTable tbody');
        tbody.empty();
        let hiddenInputs = $('#cartHiddenInputs');
        hiddenInputs.empty();
        let previewContainer = $('#imagePreviewContainer');
        previewContainer.empty();

        if (cart.length === 0) {
            tbody.html(`<tr><td colspan="5" class="text-center py-4 text-muted" style="border: 2px dashed var(--border-color) !important; background: transparent;"><i data-feather="package" width="32" height="32" class="mb-2 text-light"></i><br>No products added to the cart yet.</td></tr>`);
            previewContainer.html('<div class="col-12 text-center text-muted py-3 img-empty-state">No images available.</div>');
            $('#designImagesCard').addClass('d-none');
            updateSummary();
            feather && feather.replace();
            return;
        }

        let hasImage = false;
        cart.forEach((item, index) => {
            let itemTotal = item.price * item.qty;
            tbody.append(`
                <tr>
                    <td>${item.name}</td>
                    <td class="text-right align-middle">৳${parseFloat(item.price).toFixed(2)}</td>
                    <td class="text-center align-middle">
                        <input type="number" class="form-control text-center item-qty mx-auto" style="min-width: 100px;" data-id="${item.id}" value="${item.qty}" min="1">
                    </td>
                    <td class="text-right item-total align-middle">৳${itemTotal.toFixed(2)}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-item" data-id="${item.id}">
                            <i data-feather="trash-2"></i>
                        </button>
                    </td>
                </tr>
            `);
            hiddenInputs.append(`
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][price]" value="${item.price}">
                <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
            `);
            if (item.image) {
                hasImage = true;
                previewContainer.append(`
                    <div class="col-auto mb-2 text-center img-prod-${item.id}">
                        <img src="${item.image}" class="img-thumbnail" style="height:100px;width:100px;object-fit:cover;">
                    </div>
                `);
            }
        });

        if (hasImage) {
            $('#designImagesCard').removeClass('d-none');
        } else {
            previewContainer.html('<div class="col-12 text-center text-muted py-3 img-empty-state">No images available.</div>');
            $('#designImagesCard').addClass('d-none');
        }

        feather && feather.replace();
        updateSummary();
    }

    $(document).on('input', '.item-qty', function() {
        let id = $(this).data('id');
        let item = cart.find(i => i.id == id);
        if (item) {
            item.qty = parseFloat($(this).val()) || 0;
            $(this).closest('tr').find('.item-total').text('৳' + (item.price * item.qty).toFixed(2));
            let idx = cart.indexOf(item);
            $('#cartHiddenInputs input[name="items['+idx+'][qty]"]').val(item.qty);
            updateSummary();
        }
    });

    $(document).on('change', '.item-qty', function() {
        let qty = parseFloat($(this).val());
        if (!qty || qty < 1) { qty = 1; $(this).val(qty); }
        let id = $(this).data('id');
        let item = cart.find(i => i.id == id);
        if (item) {
            item.qty = qty;
            $(this).closest('tr').find('.item-total').text('৳' + (item.price * item.qty).toFixed(2));
            let idx = cart.indexOf(item);
            $('#cartHiddenInputs input[name="items['+idx+'][qty]"]').val(item.qty);
            updateSummary();
        }
    });

    $(document).on('click', '.remove-item', function() {
        cart = cart.filter(i => i.id != $(this).data('id'));
        renderCart();
    });

    function updateSummary() {
        let totalQty = 0, subTotal = 0;
        cart.forEach(item => {
            totalQty += parseFloat(item.qty) || 0;
            subTotal += (parseFloat(item.price) || 0) * (parseFloat(item.qty) || 0);
        });
        let discount = parseFloat($('#discount').val()) || 0;
        let carrying = parseFloat($('#carryingCharge').val()) || 0;
        let paid = parseFloat($('#paidAmount').val()) || 0;
        let grand = (subTotal - discount) + carrying;
        $('#summaryTotalQty').text(totalQty + ' Pcs');
        $('#summarySubTotal').text('৳' + subTotal.toFixed(2));
        $('#summaryGrandTotal').text('৳' + grand.toFixed(2));
        $('#summaryDue').text('৳' + (grand - paid).toFixed(2));
    }
</script>
@endpush
