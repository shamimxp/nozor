@extends('layouts.admin')
@section('title', 'Edit Purchase - ' . $purchase->purchase_number)
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">Edit Purchase: {{ $purchase->purchase_number }} (Style: {{ $purchase->style_number }})</h4>
            </div>
            <div class="card-body pt-2">
                <form action="{{ route('admin.purchase.update', $purchase->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label>Custom Order</label>
                                <input type="text" class="form-control" value="{{ $purchase->customOrder->style_number }} - {{ $purchase->customOrder->order_number }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="vendor_id">Vendor</label>
                                <select name="vendor_id" id="vendor_id" class="form-control select2" required>
                                    <option value="">-- Select Vendor --</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ $purchase->vendor_id == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }} ({{ $vendor->company_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-2 mb-1">Fabrics & Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="itemsTable">
                            <thead>
                                <tr>
                                    <th>Fabric/Type/Sleeve</th>
                                    <th>Qty</th>
                                    <th style="width: 150px;">Unit Cost</th>
                                    <th style="width: 150px;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($purchase->items as $idx => $item)
                                    <tr>
                                        <td>
                                            {{ $item->fabricPrice->fabric->name ?? '-' }} | {{ strtoupper($item->fabricPrice->type) }} | {{ strtoupper($item->fabricPrice->sleeve) }}
                                            <input type="hidden" name="items[{{$idx}}][fabric_price_id]" value="{{ $item->fabric_price_id }}">
                                        </td>
                                        <td>
                                            <input type="number" name="items[{{$idx}}][quantity]" value="{{ $item->quantity }}" class="form-control item-qty" readonly>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" name="items[{{$idx}}][unit_cost]" value="{{ $item->unit_cost }}" class="form-control item-cost" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control item-subtotal" readonly value="{{ number_format($item->subtotal, 2) }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label for="carrying_charge">Carrying Charge</label>
                                <input type="number" step="0.01" id="carrying_charge" name="carrying_charge" class="form-control" value="{{ $purchase->carrying_charge }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label for="grand_total_display">Grand Total</label>
                                <input type="text" id="grand_total_display" class="form-control" readonly value="{{ number_format($purchase->grand_total, 2) }}">
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label>Paid Amount (so far)</label>
                                <input type="text" class="form-control" value="৳{{ number_format($purchase->paid_amount, 2) }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label for="status">Purchase Status</label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="pending" {{ $purchase->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirm" {{ $purchase->status == 'confirm' ? 'selected' : '' }}>Confirm</option>
                                    <option value="received" {{ $purchase->status == 'received' ? 'selected' : '' }}>Received</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label for="received_date">Received Date</label>
                                <input type="date" id="received_date" name="received_date" class="form-control" required value="{{ $purchase->received_date ? $purchase->received_date->format('Y-m-d') : '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <button type="submit" class="btn btn-primary">Update Purchase Order</button>
                        <a href="{{ route('admin.purchase.index') }}" class="btn btn-outline-secondary ml-1">Cancel</a>
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
    });
</script>
@endpush
