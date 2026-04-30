@extends('layouts.admin')
@section('title', 'Create Stock Adjustment')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <h4 class="card-title mb-25">Create Stock Adjustment</h4>
                        <small class="text-muted">Normal exchange can be received later to add stock back.</small>
                    </div>
                    <a href="{{ route('admin.stock-adjustment.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>
            </div>
            <div class="card-body pt-2">
                <form id="adjustmentForm">
                    @csrf
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label>Product</label>
                                <select name="product_id" id="productId" class="form-control select2" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                            {{ $product->name }} (Current Stock: {{ $product->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Current Stock</label>
                                <input type="text" id="currentStock" class="form-control bg-light" value="0.00" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Adjustment Type</label>
                                <select name="type" id="adjustmentType" class="form-control" required>
                                    <option value="normal">Normal (Exchange)</option>
                                    <option value="abnormal">Abnormal (Damage/Lost)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Quantity to Decrease</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" step="0.01" min="0.01" required>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label>Adjustment Date</label>
                                <input type="date" name="adjustment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info py-1" id="typeInfo">
                        Normal (Exchange) decreases stock now and creates a pending receive action. When received, the quantity is added back to this product.
                    </div>

                    <div class="form-group mb-0">
                        <label>Reason / Note</label>
                        <textarea name="reason" class="form-control" rows="4" placeholder="Write the reason for this stock adjustment"></textarea>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-primary" id="saveBtn">Save Adjustment</button>
                            <a href="{{ route('admin.stock-adjustment.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
        $('#productId').on('change', function() {
            var stock = parseFloat($(this).find(':selected').data('stock')) || 0;
            $('#currentStock').val(stock.toFixed(2));
            $('#quantity').attr('max', stock);
        });

        $('#adjustmentType').on('change', function() {
            if ($(this).val() === 'normal') {
                $('#typeInfo')
                    .removeClass('alert-danger')
                    .addClass('alert-info')
                    .text('Normal (Exchange) decreases stock now and creates a pending receive action. When received, the quantity is added back to this product.');
                return;
            }

            $('#typeInfo')
                .removeClass('alert-info')
                .addClass('alert-danger')
                .text('Abnormal (Damage/Lost) permanently decreases stock and cannot be received back.');
        });

        $('#adjustmentForm').on('submit', function(e) {
            e.preventDefault();

            var type = $('#adjustmentType').val();
            var product = $('#productId option:selected').text().trim();
            var quantity = parseFloat($('#quantity').val()) || 0;
            var currentStock = parseFloat($('#currentStock').val()) || 0;

            if (quantity > currentStock) {
                toastr.error('Adjustment quantity cannot exceed current stock.');
                return;
            }

            Swal.fire({
                title: 'Save stock adjustment?',
                text: (type === 'normal'
                    ? 'This will decrease stock now and allow receive later for exchange.'
                    : 'This will permanently decrease stock for damage or lost item.') + ' Product: ' + product,
                icon: type === 'normal' ? 'info' : 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, save',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-secondary ml-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                $('#saveBtn').text('Saving...').attr('disabled', true);
                $.ajax({
                    url: "{{ route('admin.stock-adjustment.store') }}",
                    type: "POST",
                    data: $('#adjustmentForm').serialize(),
                    success: function(response) {
                        toastr.success(response.success);
                        window.location.href = "{{ route('admin.stock-adjustment.index') }}";
                    },
                    error: function(xhr) {
                        $('#saveBtn').text('Save Adjustment').attr('disabled', false);
                        toastr.error(xhr.responseJSON.error || 'Something went wrong!');
                    }
                });
            });
        });
    });
</script>
@endpush
