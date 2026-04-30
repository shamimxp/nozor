@extends('layouts.admin')
@section('title', 'Edit Stock Purchase')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h4 class="card-title">Edit Stock Purchase - {{ $purchase->purchase_number }}</h4>
            </div>
            <div class="card-body pt-2">
                <form id="purchaseForm">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Purchase Number</label>
                                <input type="text" class="form-control" value="{{ $purchase->purchase_number }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Vendor</label>
                                <select name="vendor_id" class="form-control select2" required>
                                    <option value="">Select Vendor</option>
                                    @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}" {{ $purchase->vendor_id == $vendor->id ? 'selected' : '' }}>
                                            {{ $vendor->name }} ({{ $vendor->company_name }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Purchase Date</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ $purchase->purchase_date->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" {{ $purchase->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirm" {{ in_array($purchase->status, ['confirm', 'confirmed']) ? 'selected' : '' }}>Confirm</option>
                                    <option value="received" {{ $purchase->status === 'received' ? 'selected' : '' }}>Received</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-2">
                        <table class="table table-bordered" id="productTable">
                            <thead>
                                <tr>
                                    <th width="40%">Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="productBody">
                                @foreach($purchase->items as $index => $item)
                                <tr>
                                    <td>
                                        <select name="products[{{ $index }}][product_id]" class="form-control select2 product-select" required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->cost_price }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }} (Stock: {{ $product->stock }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="products[{{ $index }}][quantity]" class="form-control quantity" step="0.01" min="1" required value="{{ $item->quantity }}">
                                    </td>
                                    <td>
                                        <input type="number" name="products[{{ $index }}][price]" class="form-control price" step="0.01" min="0" required value="{{ $item->purchase_price }}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control total" readonly value="{{ number_format($item->quantity * $item->purchase_price, 2, '.', '') }}">
                                    </td>
                                    <td>
                                        @if($index === 0)
                                            <button type="button" class="btn btn-success btn-sm addRow"><i data-feather="plus"></i></button>
                                        @else
                                            <button type="button" class="btn btn-danger btn-sm removeRow"><i data-feather="minus"></i></button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Grand Total:</th>
                                    <th>
                                        <input type="number" name="total_amount" id="grandTotal" class="form-control" readonly value="{{ number_format($purchase->total_amount, 2, '.', '') }}">
                                    </th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Paid Amount:</th>
                                    <th>
                                        <input type="number" name="paid_amount" id="paidAmount" class="form-control" step="0.01" value="{{ number_format($purchase->paid_amount, 2, '.', '') }}" readonly>
                                    </th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">Due Amount:</th>
                                    <th>
                                        <input type="number" name="due_amount" id="dueAmount" class="form-control" readonly value="{{ number_format($purchase->due_amount, 2, '.', '') }}">
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row">
                        <div class="col-12 mt-2">
                            <div class="form-group">
                                <label>Note</label>
                                <textarea name="note" class="form-control" rows="3">{{ $purchase->note }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" id="saveBtn">Update Purchase</button>
                            <a href="{{ route('admin.inventory-purchase.index') }}" class="btn btn-secondary">Back</a>
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
    $(function() {
        var rowCount = {{ max(1, $purchase->items->count()) }};

        function refreshSelect2() {
            $('.select2').select2();
            if (typeof feather !== 'undefined') {
                feather.replace({ width: 14, height: 14 });
            }
        }

        $(document).on('click', '.addRow', function() {
            var productsHtml = $('#productBody select:first').html();
            var newRow = `<tr>
                <td>
                    <select name="products[${rowCount}][product_id]" class="form-control select2 product-select" required>
                        ${productsHtml}
                    </select>
                </td>
                <td><input type="number" name="products[${rowCount}][quantity]" class="form-control quantity" step="0.01" min="1" required></td>
                <td><input type="number" name="products[${rowCount}][price]" class="form-control price" step="0.01" min="0" required></td>
                <td><input type="text" class="form-control total" readonly value="0.00"></td>
                <td><button type="button" class="btn btn-danger btn-sm removeRow"><i data-feather="minus"></i></button></td>
            </tr>`;
            $('#productBody').append(newRow);
            rowCount++;
            refreshSelect2();
        });

        $(document).on('click', '.removeRow', function() {
            if ($('#productBody tr').length <= 1) {
                toastr.warning('At least one product is required.');
                return;
            }

            $(this).closest('tr').remove();
            calculateGrandTotal();
        });

        $(document).on('change', '.product-select', function() {
            var price = $(this).find(':selected').data('price');
            $(this).closest('tr').find('.price').val(price || 0);
            calculateRowTotal($(this).closest('tr'));
        });

        $(document).on('input', '.quantity, .price', function() {
            calculateRowTotal($(this).closest('tr'));
        });

        $(document).on('input', '#paidAmount', function() {
            calculateDue();
        });

        function calculateRowTotal(row) {
            var qty = parseFloat(row.find('.quantity').val()) || 0;
            var price = parseFloat(row.find('.price').val()) || 0;
            var total = qty * price;
            row.find('.total').val(total.toFixed(2));
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            var grandTotal = 0;
            $('#productBody .total').each(function() {
                grandTotal += parseFloat($(this).val()) || 0;
            });
            $('#grandTotal').val(grandTotal.toFixed(2));
            calculateDue();
        }

        function calculateDue() {
            var total = parseFloat($('#grandTotal').val()) || 0;
            var paid = parseFloat($('#paidAmount').val()) || 0;
            var due = Math.max(total - paid, 0);
            $('#dueAmount').val(due.toFixed(2));
            validatePaidAmount(false);
        }

        function validatePaidAmount(showMessage) {
            var total = parseFloat($('#grandTotal').val()) || 0;
            var paid = parseFloat($('#paidAmount').val()) || 0;

            if (paid > total) {
                $('#paidAmount').addClass('is-invalid');
                if (showMessage) {
                    toastr.error('Paid amount cannot be greater than Grand Total.');
                }
                return false;
            }

            $('#paidAmount').removeClass('is-invalid');
            return true;
        }

        function getAjaxErrorMessage(xhr) {
            if (xhr.responseJSON && xhr.responseJSON.error) {
                return xhr.responseJSON.error;
            }

            if (xhr.responseJSON && xhr.responseJSON.errors) {
                var firstMessage = 'Something went wrong!';
                $.each(xhr.responseJSON.errors, function(field, messages) {
                    firstMessage = messages[0];
                    return false;
                });
                return firstMessage;
            }

            return 'Something went wrong!';
        }

        refreshSelect2();
        calculateGrandTotal();

        $('#purchaseForm').on('submit', function(e) {
            e.preventDefault();
            if (!validatePaidAmount(true)) {
                return;
            }

            $('#saveBtn').text('Updating...').attr('disabled', true);
            $.ajax({
                url: "{{ route('admin.inventory-purchase.update', $purchase->id) }}",
                type: "POST",
                data: $(this).serialize(),
                success: function(response) {
                    toastr.success(response.success);
                    window.location.href = "{{ route('admin.inventory-purchase.index') }}";
                },
                error: function(xhr) {
                    $('#saveBtn').text('Update Purchase').attr('disabled', false);
                    toastr.error(getAjaxErrorMessage(xhr));
                }
            });
        });
    });
</script>
@endpush
