@extends('layouts.dealer')
@section('title', 'POS System')

@section('content')
<div class="content-header row">
</div>
<div class="content-body">
    <div class="row">
        <!-- Main POS Area (Products) -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm" style="border-radius: 10px; min-height: 85vh;">
                <div class="card-body p-2">
                    <!-- Search & Filter -->
                    <form id="filter-form" class="row mb-2 px-1">
                        <div class="col-md-5 mb-1 mb-md-0 px-1">
                            <input type="text" name="search" id="search-input" class="form-control bg-light" placeholder="Search product..." style="border:1px solid #eee; border-radius: 6px;">
                        </div>
                        <div class="col-md-3 mb-1 mb-md-0 px-1">
                            <select name="category_id" id="category-select" class="form-control select2 bg-light" style="width:100%;">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 px-1">
                            <select name="subcategory_id" id="subcategory-select" class="form-control select2 bg-light" style="width:100%;">
                                <option value="">All Subcategories</option>
                            </select>
                        </div>
                    </form>
                    
                    <hr class="mt-1 mb-2">

                    <!-- Product Grid -->
                    <div id="product-scroll-area" style="overflow-y: auto; max-height: calc(85vh - 100px); overflow-x: hidden;">
                        <div class="row match-height" id="product-grid">
                            <!-- Products will be loaded here via AJAX -->
                        </div>
                        <div id="load-more-btn-container" class="text-center mt-2 mb-3" style="display: none;">
                            <button id="load-more-btn" class="btn btn-outline-dark" onclick="loadProducts(true)" style="border-radius: 8px; padding: 8px 24px; font-weight: bold;">
                                Load More
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cart Area -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm" style="border-radius: 10px; min-height: 85vh; display: flex; flex-direction: column;">
                <div class="card-header bg-light p-2 border-bottom d-flex justify-content-between align-items-center" style="border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0 text-dark font-weight-bolder">
                        <i data-feather="shopping-bag" class="text-primary" style="width: 18px; margin-right: 5px; margin-top: -3px;"></i>Current Order
                    </h5>
                    <button class="btn btn-sm btn-outline-danger" style="border-radius: 6px; padding: 5px 14px; font-size: 0.85rem;" onclick="clearCart()">
                        <i data-feather="trash-2" style="width: 14px; margin-right: 4px;"></i> Clear
                    </button>
                </div>
                
                <div class="card-body p-0 d-flex flex-column" style="flex: 1; overflow: hidden;">
                    <div class="table-responsive" style="flex: 1; overflow-y: auto; max-height: calc(85vh - 280px);">
                        <table class="table table-borderless table-striped text-center align-middle mb-0" id="cart-table">
                            <thead class="sticky-top shadow-sm" style="font-size: 0.8rem; background-color: #f4f6f9; color: #4b4b4b; text-transform: uppercase; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="text-left py-2 rounded-left">Item</th>
                                    <th class="py-2" style="width: 100px;">Qty</th>
                                    <th class="py-2">Price</th>
                                    <th class="py-2 rounded-right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="cart-items" style="background-color: #ffffff;">
                                <tr id="empty-cart-row">
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <i data-feather="shopping-cart" style="width: 40px; height: 40px; opacity: 0.2; margin-bottom: 10px;"></i><br>
                                        Cart is empty
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Order Summary Area -->
                    <div class="bg-light p-2 border-top" style="border-radius: 0 0 10px 10px;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="font-weight-bold" style="font-size: 0.9rem; color: #4a5568;">Sub Total</span>
                            <div class="text-primary font-weight-bolder text-right" style="font-size: 1rem;" id="cart-subtotal">
                                ৳ 0.00
                            </div>
                        </div>
                        <div class="mb-1">
                            <textarea class="form-control bg-light" id="cart-notes" rows="2" placeholder="Order Note..." style="border-radius: 8px; border: 1px solid #e0e0e0;"></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-1 mb-2">
                            <h4 class="font-weight-bolder mb-0 text-dark">Total</h4>
                            <h4 class="font-weight-bolder mb-0" style="font-size: 1.4rem; color: #0F172A;" id="cart-total">৳ 0.00</h4>
                        </div>
                        <button class="btn btn-block font-weight-bolder py-2 place-order-btn" onclick="placeOrder()">
                            <i data-feather="check-circle" style="margin-right: 5px; margin-top: -2px;"></i> Place Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.product-card:hover {
    border-color: #0F172A !important;
    box-shadow: 0 4px 15px 0 rgba(15, 23, 42, 0.15) !important;
    transform: translateY(-2px);
}
.product-card {
    transition: all 0.2s ease-in-out;
}
.product-card.active {
    border-color: #0F172A !important;
    background-color: #f0f2f5;
    border-width: 2px;
}
.select2-container--default .select2-selection--single {
    border: 1px solid #eee !important;
    border-radius: 6px !important;
    height: 38px !important;
    position: relative !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 38px !important;
    padding-right: 30px !important;
}
/* Hide the broken default arrow — theme CSS corrupts the <b> tag */
.select2-container--default .select2-selection--single .select2-selection__arrow {
    display: none !important;
}
/* Draw a clean arrow using ::after pseudo-element instead */
.select2-container--default .select2-selection--single::after {
    content: '' !important;
    position: absolute !important;
    right: 10px !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    width: 0 !important;
    height: 0 !important;
    border-left: 5px solid transparent !important;
    border-right: 5px solid transparent !important;
    border-top: 6px solid #888 !important;
    pointer-events: none !important;
    z-index: 1 !important;
}
.place-order-btn {
    border-radius: 8px;
    font-size: 1.15rem;
    background: linear-gradient(135deg, #1e293b 0%, #0F172A 100%);
    color: white !important;
    border: none;
    box-shadow: 0 4px 15px rgba(15, 23, 42, 0.35);
    transition: all 0.3s ease;
    letter-spacing: 0.5px;
}
.place-order-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.5);
    background: linear-gradient(135deg, #334155 0%, #1e293b 100%);
}
/* Scrollbar styling */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
::-webkit-scrollbar-track {
    background: #f1f1f1; 
}
::-webkit-scrollbar-thumb {
    background: #c1c1c1; 
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8; 
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    let cart = [];
    let currentOffset = 0;
    let currentLimit = 36;
    let isLoading = false;

    function loadProducts(append = false) {
        if (isLoading) return;
        isLoading = true;

        if (!append) {
            currentOffset = 0;
            currentLimit = 36;
            $('#product-grid').html('<div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 300px;"><div class="spinner-border" style="color: #0F172A; width: 2.5rem; height: 2.5rem;" role="status"><span class="sr-only">Loading...</span></div></div>');
            $('#load-more-btn-container').hide();
        } else {
            $('#load-more-btn').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...').prop('disabled', true);
        }

        const data = {
            search: $('#search-input').val(),
            category_id: $('#category-select').val(),
            subcategory_id: $('#subcategory-select').val(),
            offset: currentOffset,
            limit: currentLimit
        };

        $.ajax({
            url: '{{ route("dealer.pos.products") }}',
            type: 'GET',
            data: data,
            success: function(response) {
                if (!append) {
                    $('#product-grid').html(response.html);
                } else {
                    $('#product-grid').append(response.html);
                }
                
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }

                if (response.has_more) {
                    $('#load-more-btn-container').show();
                    $('#load-more-btn').html('Load More').prop('disabled', false);
                    currentOffset += currentLimit;
                    currentLimit = 12; // Subsequent loads
                } else {
                    $('#load-more-btn-container').hide();
                }
                
                isLoading = false;
            },
            error: function() {
                if (!append) {
                    $('#product-grid').html('<div class="col-12 text-center py-5 text-danger">Failed to load products</div>');
                } else {
                    $('#load-more-btn').html('Load More').prop('disabled', false);
                    alert('Failed to load more products.');
                }
                isLoading = false;
            }
        });
    }

    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2();

        // Initial load
        loadProducts();

        // Filter events
        $('#search-input').on('keyup', function() {
            clearTimeout($.data(this, 'timer'));
            var wait = setTimeout(function() {
                loadProducts();
            }, 500);
            $(this).data('timer', wait);
        });

        $('#category-select').on('change', function() {
            let catId = $(this).val();
            let subSelect = $('#subcategory-select');
            
            subSelect.html('<option value="">All Subcategories</option>');
            
            if(catId) {
                $.ajax({
                    url: '{{ url("dealer/pos/subcategories") }}/' + catId,
                    type: 'GET',
                    success: function(response) {
                        response.forEach(function(sub) {
                            subSelect.append(`<option value="${sub.id}">${sub.name}</option>`);
                        });
                        // Trigger select2 update and load products
                        subSelect.trigger('change.select2');
                        loadProducts();
                    }
                });
            } else {
                subSelect.trigger('change.select2');
                loadProducts();
            }
        });

        $('#subcategory-select').on('change', function() {
            loadProducts();
        });
    });

    // Cart Logic
    function addToCart(element) {
        let id = $(element).data('id');
        let title = $(element).data('title');
        let price = parseFloat($(element).data('price'));
        let image = $(element).data('image');

        let existing = cart.find(item => item.id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id, title, price, image, qty: 1 });
        }
        
        // Add active class animation
        $(element).addClass('active');
        setTimeout(() => $(element).removeClass('active'), 200);

        renderCart();
    }

    function updateQty(id, change) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty += change;
            if (item.qty <= 0) {
                removeFromCart(id);
            } else {
                renderCart();
            }
        }
    }

    function removeFromCart(id) {
        cart = cart.filter(item => item.id !== id);
        renderCart();
    }
    
    function clearCart() {
        cart = [];
        renderCart();
    }

    function renderCart() {
        const tbody = $('#cart-items');
        tbody.empty();
        
        let subtotal = 0;

        if (cart.length === 0) {
            tbody.html('<tr id="empty-cart-row"><td colspan="4" class="text-center py-5 text-muted"><i data-feather="shopping-cart" style="width: 40px; height: 40px; opacity: 0.2; margin-bottom: 10px;"></i><br>Cart is empty</td></tr>');
        } else {
            cart.forEach(item => {
                let total = item.qty * item.price;
                subtotal += total;

                tbody.append(`
                    <tr class="border-bottom">
                        <td class="text-left py-2">
                            <div class="font-weight-bolder text-dark text-truncate" style="font-size: 0.9rem; max-width: 150px;" title="${item.title}">
                                ${item.title}
                            </div>
                            <div class="font-weight-bold text-primary" style="font-size: 0.85rem;">৳${item.price.toFixed(2)}</div>
                        </td>
                        <td class="py-2">
                            <div class="input-group input-group-sm mx-auto" style="width: 85px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); border-radius: 4px;">
                                <div class="input-group-prepend">
                                    <button class="btn" type="button" style="padding: 0.2rem 0.5rem; border: 1px solid #0F172A; color: #0F172A; background: white;" onclick="updateQty(${item.id}, -1)">-</button>
                                </div>
                                <input type="number" class="form-control text-center px-0 font-weight-bolder bg-white text-dark" style="border-color: #0F172A;" value="${item.qty}" min="1" onchange="setQty(${item.id}, this.value)">
                                <div class="input-group-append">
                                    <button class="btn" type="button" style="padding: 0.2rem 0.5rem; border: 1px solid #0F172A; color: #0F172A; background: white;" onclick="updateQty(${item.id}, 1)">+</button>
                                </div>
                            </div>
                        </td>
                        <td class="text-primary font-weight-bolder py-2" style="font-size: 0.95rem;">৳${total.toFixed(2)}</td>
                        <td class="py-1">
                            <button class="btn btn-sm" style="width: 28px; height: 28px; padding: 0; background: #fff1f1; border: none; border-radius: 6px; color: #e53e3e;" onclick="removeFromCart(${item.id})">
                                <i data-feather="trash-2" style="width: 13px; height: 13px; color: #e53e3e;"></i>
                            </button>
                        </td>
                    </tr>
                `);
            });
        }
        
        if (feather) { feather.replace({ width: 14, height: 14 }); }

        $('#cart-subtotal').text('৳ ' + subtotal.toFixed(2));
        $('#cart-total').text('৳ ' + subtotal.toFixed(2));
    }
    
    function setQty(id, value) {
        let qty = parseInt(value);
        if (isNaN(qty) || qty < 1) {
            qty = 1;
        }
        let item = cart.find(i => i.id === id);
        if (item) {
            item.qty = qty;
            renderCart();
        }
    }

    function placeOrder() {
        if(cart.length === 0) {
            if (typeof toastr !== 'undefined') toastr.error('Cart is empty!');
            else alert('Cart is empty!');
            return;
        }
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Place Order?',
                text: "Are you sure you want to place this order request?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0F172A',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Place Order'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitOrder();
                }
            });
        } else {
            if(confirm("Are you sure you want to place this order request?")) {
                submitOrder();
            }
        }
    }

    function submitOrder() {
        // Format items for backend
        const items = cart.map(item => ({
            product_id: item.id,
            requested_qty: item.qty
        }));
        const note = $('#cart-notes').val();
        
        $.ajax({
            url: '{{ route("dealer.pos.order-request") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                items: items,
                note: note
            },
            success: function(response) {
                if (response.status === 'success') {
                    if (typeof toastr !== 'undefined') toastr.success(response.message);
                    else alert(response.message);
                    
                    cart = [];
                    $('#cart-notes').val('');
                    renderCart();
                } else {
                    if (typeof toastr !== 'undefined') toastr.error(response.message);
                    else alert(response.message);
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to submit order request.';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                if (typeof toastr !== 'undefined') toastr.error(errorMsg);
                else alert(errorMsg);
            }
        });
    }
</script>
@endpush
