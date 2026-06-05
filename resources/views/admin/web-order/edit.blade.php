@extends('layouts.admin')
@section('title', 'Edit Web Order')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Edit Order: {{ $order->invoice_no }}</h4>
                    <a href="{{ route('admin.web-order.index') }}" class="btn btn-outline-secondary btn-sm"><i data-feather="arrow-left"></i> Back</a>
                </div>
            </div>
            <div class="card-body mt-2">
                <form action="{{ route('admin.web-order.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-2">Customer Details</h5>
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $order->address->name ?? '' }}" required>
                            </div>
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ $order->address->phone ?? '' }}" required>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="3" required>{{ $order->address->address ?? '' }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Note</label>
                                <textarea name="note" class="form-control" rows="2">{{ $order->address->note ?? '' }}</textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="mb-2">Order Information</h5>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Delivery Area (Auto-calculate Shipping)</label>
                                <select id="delivery_area_selector" class="form-control mb-1">
                                    <option value="">-- Custom/Unchanged --</option>
                                    <option value="{{ $settings->inside_dhaka ?? 0 }}">Inside Dhaka (৳{{ $settings->inside_dhaka ?? 0 }})</option>
                                    <option value="{{ $settings->subcity ?? 0 }}">Subcity (৳{{ $settings->subcity ?? 0 }})</option>
                                    <option value="{{ $settings->outside_dhaka ?? 0 }}">Outside Dhaka (৳{{ $settings->outside_dhaka ?? 0 }})</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Shipping Charge (৳)</label>
                                <input type="number" step="0.01" name="shipping_charge" id="shipping_charge_input" class="form-control" value="{{ $order->shipping_charge }}">
                            </div>
                            <div class="form-group">
                                <label>Discount (৳)</label>
                                <input type="number" step="0.01" name="discount" class="form-control" value="{{ $order->discount }}">
                            </div>
                            <hr>
                            <p class="mb-0"><strong>Subtotal:</strong> <span id="display-subtotal">৳{{ number_format($order->subtotal, 2) }}</span></p>
                            <p class="mb-0"><strong>Total:</strong> <span id="display-total">৳{{ number_format($order->total, 2) }}</span></p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5 class="mb-2">Order Items</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Product Name</th>
                                            <th width="150">Size</th>
                                            <th width="150">Color</th>
                                            <th width="150">Quantity</th>
                                            <th width="180">Unit Price (৳)</th>
                                            <th width="180">Total (৳)</th>
                                            <th width="80" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-table-body">
                                        @foreach($order->items as $item)
                                        @php
                                            $product = $item->product;
                                            $groupedVariations = $product && $product->variations ? $product->variations->groupBy('variation_id') : [];
                                            $sizes = [];
                                            $colors = [];
                                            foreach($groupedVariations as $varId => $vars) {
                                                $name = strtolower($vars->first()->variation->name ?? '');
                                                if ($name == 'size') {
                                                    foreach($vars as $var) {
                                                        if ($var->variationValue) $sizes[] = $var->variationValue->value;
                                                    }
                                                }
                                                if ($name == 'color') {
                                                    foreach($vars as $var) {
                                                        if ($var->variationValue) $colors[] = $var->variationValue->value;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <tr class="item-row">
                                            <td>
                                                {{ $item->product->name ?? 'Deleted Product' }}
                                                <input type="hidden" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                                            </td>
                                            <td class="text-center">
                                                @if(count($sizes) > 0 || $item->size)
                                                    <select name="items[{{ $item->id }}][size]" class="form-control form-control-sm text-center">
                                                        @if(!in_array($item->size, $sizes) && $item->size)
                                                            <option value="{{ $item->size }}" selected>{{ $item->size }}</option>
                                                        @endif
                                                        @foreach($sizes as $s)
                                                            <option value="{{ $s }}" {{ $item->size == $s ? 'selected' : '' }}>{{ $s }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <span class="text-muted font-weight-bold">-</span>
                                                    <input type="hidden" name="items[{{ $item->id }}][size]" value="">
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if(count($colors) > 0 || $item->color)
                                                    <select name="items[{{ $item->id }}][color]" class="form-control form-control-sm text-center">
                                                        @if(!in_array($item->color, $colors) && $item->color)
                                                            <option value="{{ $item->color }}" selected>{{ $item->color }}</option>
                                                        @endif
                                                        @foreach($colors as $c)
                                                            <option value="{{ $c }}" {{ $item->color == $c ? 'selected' : '' }}>{{ $c }}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <span class="text-muted font-weight-bold">-</span>
                                                    <input type="hidden" name="items[{{ $item->id }}][color]" value="">
                                                @endif
                                            </td>
                                            <td>
                                                <input type="number" name="items[{{ $item->id }}][quantity]" class="form-control form-control-sm item-qty" value="{{ $item->quantity }}" min="1" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" name="items[{{ $item->id }}][price]" class="form-control form-control-sm item-price" value="{{ $item->price }}" min="0" required readonly>
                                            </td>
                                            <td class="item-total-text font-weight-bold">৳{{ number_format($item->price * $item->quantity, 2) }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn"><i data-feather="trash-2"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="text-right mt-3">
                        <h4 class="text-primary font-weight-bolder">Dynamic Grand Total: <span id="dynamic-grand-total">৳{{ number_format($order->total, 2) }}</span></h4>
                        <button type="submit" class="btn btn-primary mt-1"><i data-feather="save"></i> Update Order</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if (feather) { feather.replace({ width: 14, height: 14 }); }

        function calculateTotals() {
            let subtotal = 0;
            $('.item-row').each(function() {
                let qty = parseFloat($(this).find('.item-qty').val()) || 0;
                let price = parseFloat($(this).find('.item-price').val()) || 0;
                let total = qty * price;
                subtotal += total;
                $(this).find('.item-total-text').text('৳' + total.toFixed(2));
            });

            let shipping = parseFloat($('input[name="shipping_charge"]').val()) || 0;
            let discount = parseFloat($('input[name="discount"]').val()) || 0;
            let grandTotal = subtotal + shipping - discount;

            $('#dynamic-grand-total').text('৳' + grandTotal.toFixed(2));
            $('#display-subtotal').text('৳' + subtotal.toFixed(2));
            $('#display-total').text('৳' + grandTotal.toFixed(2));
        }

        $(document).on('input', '.item-qty, .item-price, input[name="shipping_charge"], input[name="discount"]', function() {
            calculateTotals();
        });

        $('#delivery_area_selector').on('change', function() {
            if($(this).val() !== "") {
                $('input[name="shipping_charge"]').val($(this).val());
                calculateTotals();
            }
        });

        $(document).on('click', '.remove-item-btn', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('tr').remove();
                calculateTotals();
            } else {
                toastr.warning('An order must have at least one item.');
            }
        });
    });
</script>
@endpush
