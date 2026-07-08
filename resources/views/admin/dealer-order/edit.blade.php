@extends('layouts.admin')
@section('title', 'Edit Dealer Order')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
        --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }
    .card-premium { border: none; border-radius: 12px; box-shadow: var(--card-shadow); margin-bottom: 2rem; }
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
            <h2 class="font-weight-bolder mb-0">Edit Dealer Order</h2>
            <p class="">Unique Ref: <span class="text-primary font-weight-bold">{{ $order->order_number }}</span></p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.dealer-order.index') }}" class="btn btn-outline-secondary">
                <i data-feather="arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</div>

<form action="{{ route('admin.dealer-order.update', $order->id) }}" method="POST" enctype="multipart/form-data" id="dealerOrderForm">
    @csrf
    @method('PUT')

    <div class="row">
          <div class="col-lg-12">
             <div class="card card-premium">
                <div class="card-header"><span class="section-title"><i data-feather="user"></i> Basic Information</span></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="font-weight-bold small">ORDER NUMBER</label>
                            <input type="text" name="order_number" class="form-control bg-light" value="{{ $order->order_number }}" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold small">ORDER DATE <span class="text-danger">*</span></label>
                            <input type="date" name="order_date" class="form-control" value="{{ $order->order_date->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="font-weight-bold small">DEALER <span class="text-danger">*</span></label>
                            <select name="dealer_id" class="form-control select2" required>
                                <option value="">-- Select Dealer --</option>
                                @foreach($dealers as $c)
                                    <option value="{{ $c->id }}" {{ $order->dealer_id == $c->id ? 'selected' : '' }}>{{ $c->shop_name }} ({{ $c->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-1">
                            <label class="font-weight-bold small">ORDER STATUS</label>
                            <select name="status" id="orderStatus" class="form-control font-weight-bold text-primary select2">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirm" {{ $order->status == 'confirm' ? 'selected' : '' }}>Confirm</option>
                                <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card card-premium">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="section-title"><i data-feather="shopping-cart"></i> Product Items</span>
                </div>
                <div class="card-body">
                    <div class="bg-light p-1 rounded-lg border row mx-0">
                        <div class="col-md-12 px-50">
                            <label class="small font-weight-bold ">Select Product</label>
                            <select id="productSelect" class="form-control select2" data-placeholder="Select product...">
                                <option value="" disabled selected>Select product...</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-image="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : '' }}">
                                        {{ $product->name ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
              <div class="card card-premium">
                 <div class="card-body">
                      <div class="table-responsive">
                        <table class="table table-hover" id="cartTable">
                            <thead class="bg-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th class="text-right">Price</th>
                                    <th class="text-center" style="width:150px;">Qty</th>
                                    <th class="text-right">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        </div>
                  </div>
            </div>
        </div>
        <div class="col-lg-8">
            {{-- 4. Images --}}
            <div class="card card-premium d-none" id="designImagesCard">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="section-title"><i data-feather="image"></i> Design Images</span>
                </div>
                <div class="card-body">
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
                            <label class="font-weight-bold small  text-uppercase">Note</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Enter notes...">{{ $order->note }}</textarea>
                        </div>
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
                            <span>Discount</span>
                            <input type="number" name="discount" id="discount" class="form-control form-control-sm text-right" style="width:100px" value="{{ $order->discount }}" placeholder="0.00">
                        </div>
                        <div class="summary-item align-items-center">
                            <span>Carrying Charge</span>
                            <input type="number" name="carrying_charge" id="carryingCharge" class="form-control form-control-sm text-right" style="width:100px" value="{{ $order->carrying_charge }}" placeholder="0.00">
                        </div>
                        <div class="grand-total-box d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0 font-weight-bold">Grand Total</span>
                            <span class="h4 mb-0 text-primary font-weight-bolder" id="summaryGrandTotal">৳0.00</span>
                        </div>
                        <div class="mt-2">
                            <label class="small  font-weight-bold">PAID AMOUNT</label>
                            <input type="number" name="paid" id="paidAmount" class="form-control font-weight-bold text-success text-right" value="{{ $order->paid }}">
                            <div class="summary-item border-0 pt-1">
                                <span class="text-danger font-weight-bold">Amount Due</span>
                                <span class="h5 mb-0 text-danger font-weight-bolder" id="summaryDue">৳0.00</span>
                            </div>
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-premium-save btn-block">UPDATE ORDER</button>
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
    let cart = [];
    
    $(document).ready(function() {
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
            
            // fetch real-time price
            $.ajax({
                url: "{{ route('admin.product-price-calculator') }}",
                type: "GET",
                data: {
                    product_id: productId,
                    dealer_id: dealerId
                },
                success: function(res) {
                    if (res.success && res.data) {
                        let dealerPrice = res.data.dealer_price;
                        addToCart(productId, productName, dealerPrice, productImage);
                    } else {
                        toastr.error('Error fetching product price calculation');
                    }
                },
                error: function(err) {
                    toastr.error('Failed to fetch product price');
                }
            });
            
            // clear selection
            $(this).val('').trigger('change.select2');
        });
        
        $('#discount, #carryingCharge, #paidAmount').on('input', function() {
            updateSummary();
        });
    });
    
    function addToCart(id, name, price, image) {
        let existing = cart.find(i => i.id == id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({
                id: id,
                name: name,
                price: price,
                qty: 1,
                image: image
            });
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
        
        cart.forEach((item, index) => {
            let itemTotal = item.price * item.qty;
            
            let tr = `
                <tr>
                    <td>${item.name}</td>
                    <td class="text-right align-middle">৳${item.price}</td>
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
            `;
            tbody.append(tr);
            
            hiddenInputs.append(`
                <input type="hidden" name="items[${index}][product_id]" value="${item.id}">
                <input type="hidden" name="items[${index}][price]" value="${item.price}">
                <input type="hidden" name="items[${index}][qty]" value="${item.qty}">
            `);

            if (item.image) {
                previewContainer.append(`
                    <div class="col-auto mb-2 text-center img-prod-${item.id}">
                        <img src="${item.image}" class="img-thumbnail" style="height:100px;width:100px;object-fit:cover;">
                    </div>
                `);
            }
        });

        if (cart.length === 0 || !cart.some(item => item.image && item.image !== '')) {
            previewContainer.html('<div class="col-12 text-center py-2 ">No images attached.</div>');
            $('#designImagesCard').addClass('d-none');
        } else {
            $('#designImagesCard').removeClass('d-none');
        }
        
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        updateSummary();
    }
    
    $(document).on('input', '.item-qty', function() {
        let id = $(this).data('id');
        let qty = $(this).val();
        let item = cart.find(i => i.id == id);
        if (item) {
            item.qty = parseFloat(qty) || 0;
            
            let itemTotal = item.price * item.qty;
            $(this).closest('tr').find('.item-total').text('৳' + itemTotal.toFixed(2));
            
            let itemIndex = cart.indexOf(item);
            $('#cartHiddenInputs input[name="items['+itemIndex+'][qty]"]').val(item.qty);
            
            updateSummary();
        }
    });

    $(document).on('change', '.item-qty', function() {
        let id = $(this).data('id');
        let qty = parseFloat($(this).val());
        if (!qty || qty < 1) {
            qty = 1;
            $(this).val(qty);
        }
        let item = cart.find(i => i.id == id);
        if (item) {
            item.qty = qty;
            
            let itemTotal = item.price * item.qty;
            $(this).closest('tr').find('.item-total').text('৳' + itemTotal.toFixed(2));
            
            let itemIndex = cart.indexOf(item);
            $('#cartHiddenInputs input[name="items['+itemIndex+'][qty]"]').val(item.qty);
            
            updateSummary();
        }
    });
    
    $(document).on('click', '.remove-item', function() {
        let id = $(this).data('id');
        cart = cart.filter(i => i.id != id);
        renderCart();
    });
    
    function updateSummary() {
        let totalQty = 0;
        let subTotal = 0;
        
        cart.forEach(item => {
            totalQty += parseFloat(item.qty) || 0;
            subTotal += (parseFloat(item.price) || 0) * (parseFloat(item.qty) || 0);
        });
        
        let discount = parseFloat($('#discount').val()) || 0;
        let carryingCharge = parseFloat($('#carryingCharge').val()) || 0;
        let paidAmount = parseFloat($('#paidAmount').val()) || 0;
        
        let grandTotal = (subTotal - discount) + carryingCharge;
        let due = grandTotal - paidAmount;
        
        $('#summaryTotalQty').text(totalQty + ' Pcs');
        $('#summarySubTotal').text('৳' + subTotal.toFixed(2));
        $('#summaryGrandTotal').text('৳' + grandTotal.toFixed(2));
        $('#summaryDue').text('৳' + due.toFixed(2));
    }
</script>
@endpush
