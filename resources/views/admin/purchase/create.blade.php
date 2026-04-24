@extends('layouts.admin')
@section('title', 'Create Purchase Order')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">Create New Purchase Order ({{ $purchaseNumber }})</h4>
            </div>
            <div class="card-body pt-2">
                <form id="purchaseForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="custom_order_id">Select Custom Order (Link by Style No)</label>
                                <select name="custom_order_id" id="custom_order_id" class="form-control select2" required>
                                    <option value="">-- Select Custom Order --</option>
                                    @foreach($customOrders as $co)
                                        <option value="{{ $co->id }}" {{ (isset($customOrder) && $customOrder->id == $co->id) ? 'selected' : '' }}>
                                            {{ $co->style_number }} - {{ $co->order_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="vendor_id">Select Vendor</label>
                                <select name="vendor_id" id="vendor_id" class="form-control select2" required>
                                    <option value="">-- Select Vendor --</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }} ({{ $vendor->company_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="orderItemsSection" style="{{ isset($customOrder) ? '' : 'display:none;' }}">
                        <h5 class="mt-2 mb-1">Fabrics & Items (from Custom Order)</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="itemsTable">
                                <thead>
                                    <tr>
                                        <th>Fabric/Type/Sleeve</th>
                                        <th>Order Qty</th>
                                        <th style="width: 250px;">Unit Cost</th>
                                        <th style="width: 250px;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($customOrder))
                                        @foreach($customOrder->items as $idx => $item)
                                            <tr>
                                                <td>
                                                    {{ $item->fabricPrice->fabric->name ?? '-' }} | {{ strtoupper($item->type) }} | {{ strtoupper($item->sleeve) }}
                                                    <input type="hidden" name="items[{{$idx}}][fabric_price_id]" value="{{ $item->fabricPrice->id }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="items[{{$idx}}][quantity]" value="{{ $item->quantity }}" class="form-control item-qty" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="items[{{$idx}}][unit_cost]" class="form-control item-cost" required>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control item-subtotal" readonly value="0.00">
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-2">
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="carrying_charge">Carrying Charge</label>
                                    <input type="number" step="0.01" id="carrying_charge" name="carrying_charge" class="form-control" value="0">
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="grand_total_display">Grand Total</label>
                                    <input type="text" id="grand_total_display" class="form-control" readonly value="0.00">
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="paid_amount">Paid Amount (Initial)</label>
                                    <input type="number" step="0.01" id="paid_amount" name="paid_amount" class="form-control" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="status">Purchase Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="pending">Pending</option>
                                        <option value="confirm">Confirm</option>
                                        <option value="received">Received</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="received_date">Received Date (Must)</label>
                                    <input type="date" id="received_date" name="received_date" class="form-control" required value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-md-4 col-12">
                                <div class="form-group">
                                    <label for="payment_method">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-control">
                                        <option value="Cash">Cash</option>
                                        <option value="Bank">Bank</option>
                                        <option value="Mobile Banking">Mobile Banking</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Save Purchase Order</button>
                        </div>
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
        $('.select2').select2();

        $('#custom_order_id').on('change', function() {
            const coId = $(this).val();
            if (coId) {
                window.location.href = "{{ route('admin.purchase.create') }}?custom_order_id=" + coId;
            }
        });

        function calculateTotals() {
            let subTotal = 0;
            $('.item-qty').each(function(index) {
                const qty = parseFloat($(this).val()) || 0;
                const cost = parseFloat($('.item-cost').eq(index).val()) || 0;
                const lineTotal = qty * cost;
                $('.item-subtotal').eq(index).val(lineTotal.toFixed(2));
                subTotal += lineTotal;
            });

            const carrying = parseFloat($('#carrying_charge').val()) || 0;
            const grandTotal = subTotal + carrying;
            $('#grand_total_display').val(grandTotal.toFixed(2));
        }

        $(document).on('input', '.item-cost, #carrying_charge', function() {
            calculateTotals();
        });

        $('#purchaseForm').on('submit', function(e) {
            e.preventDefault();
            $('#submitBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');

            $.ajax({
                url: "{{ route('admin.purchase.store') }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    if (res.success) {
                        toastr.success(res.message);
                        window.location.href = "{{ route('admin.purchase.index') }}";
                    } else {
                        toastr.error(res.message);
                        $('#submitBtn').prop('disabled', false).text('Save Purchase Order');
                    }
                },
                error: function(xhr) {
                    const errors = xhr.responseJSON.errors;
                    if (errors) {
                        for (let key in errors) {
                            toastr.error(errors[key][0]);
                        }
                    } else {
                        toastr.error('Something went wrong!');
                    }
                    $('#submitBtn').prop('disabled', false).text('Save Purchase Order');
                }
            });
        });
        
        @if(isset($customOrder))
            calculateTotals();
        @endif
    });
</script>
@endpush
