@extends('layouts.admin')
@section('title', 'Edit Manufacture Order')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Edit Manufacture Order</h4>
                <a href="{{ route('admin.manufacture.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
            <div class="card-body mt-2">
                <form id="manufactureForm">
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="reff_invoice">Reference Invoice No</label>
                                <input type="text" name="reff_invoice" id="reff_invoice" class="form-control" value="{{ $manufacture->reff_invoice }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="product_id">Product <span class="text-danger">*</span></label>
                                <select name="product_id" id="product_id" class="form-control select2">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ $manufacture->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text product_id_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="dealer_id">Dealer <span class="text-danger">*</span></label>
                                <select name="dealer_id" id="dealer_id" class="form-control select2">
                                    <option value="">Select Dealer</option>
                                    @foreach($dealers as $dealer)
                                    <option value="{{ $dealer->id }}" data-phone="{{ $dealer->phone }}" data-address="{{ $dealer->address }}" {{ $manufacture->dealer_id == $dealer->id ? 'selected' : '' }}>{{ $dealer->name }} ({{ $dealer->phone }})</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text dealer_id_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="worker_id">Worker <span class="text-danger">*</span></label>
                                <select name="worker_id" id="worker_id" class="form-control select2">
                                    <option value="">Select Worker</option>
                                    @foreach($workers as $worker)
                                    <option value="{{ $worker->id }}" {{ $manufacture->worker_id == $worker->id ? 'selected' : '' }}>{{ $worker->name }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-text worker_id_error"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="dealer_phone">Dealer Phone</label>
                                <input type="text" id="dealer_phone" class="form-control" value="{{ $manufacture->dealer_phone }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-1">
                                <label for="dealer_address">Dealer Address</label>
                                <input type="text" id="dealer_address" class="form-control" value="{{ $manufacture->dealer_address }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label for="manufacture_qty">Manufacture Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="manufacture_qty" id="manufacture_qty" class="form-control" value="{{ $manufacture->manufacture_qty }}" min="1">
                                <span class="text-danger error-text manufacture_qty_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2" id="prices-section">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-1">Manufacturing Parts & Prices</h5>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check custom-control custom-checkbox mb-1">
                                <input type="checkbox" class="custom-control-input part-checkbox" id="part_body" name="parts[]" value="body" {{ $manufacture->body_total > 0 ? 'checked' : '' }}>
                                <label class="custom-control-label" for="part_body">Body Part (Price: <span id="display_body_price">0.00</span>)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check custom-control custom-checkbox mb-1">
                                <input type="checkbox" class="custom-control-input part-checkbox" id="part_finishing" name="parts[]" value="finishing" {{ $manufacture->finishing_total > 0 ? 'checked' : '' }}>
                                <label class="custom-control-label" for="part_finishing">Finishing Part (Price: <span id="display_finishing_price">0.00</span>)</label>
                            </div>
                        </div>
                        <span class="text-danger error-text parts_error col-12"></span>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label>Body Total</label>
                                <input type="text" id="body_total" class="form-control font-weight-bold" value="{{ $manufacture->body_total }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label>Finishing Total</label>
                                <input type="text" id="finishing_total" class="form-control font-weight-bold" value="{{ $manufacture->finishing_total }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-1">
                                <label>Grand Total</label>
                                <input type="text" id="grand_total" class="form-control font-weight-bold text-success" value="{{ $manufacture->grand_total }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="form-group mb-1">
                                <label for="note">Note (Optional)</label>
                                <textarea name="note" id="note" class="form-control" rows="2">{{ $manufacture->note }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_confirm" name="is_confirm" value="1">
                                <label class="custom-control-label" for="is_confirm">Confirm Order (Cannot be edited or deleted later)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary" id="saveBtn">Update Order</button>
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
    let bodyPartPrice = 0;
    let finishingPartPrice = 0;

    function calculateTotals() {
        let qty = parseFloat($('#manufacture_qty').val()) || 0;
        let bodyTotal = 0;
        let finishingTotal = 0;

        if ($('#part_body').is(':checked')) {
            bodyTotal = bodyPartPrice * qty;
        }
        if ($('#part_finishing').is(':checked')) {
            finishingTotal = finishingPartPrice * qty;
        }

        $('#body_total').val(bodyTotal.toFixed(2));
        $('#finishing_total').val(finishingTotal.toFixed(2));
        $('#grand_total').val((bodyTotal + finishingTotal).toFixed(2));
    }

    function loadPrices(productId) {
        if (productId) {
            $.get("{{ route('admin.manufacture.get-prices') }}", function(data) {
                bodyPartPrice = parseFloat(data.body_part_price);
                finishingPartPrice = parseFloat(data.finishing_part_price);

                $('#display_body_price').text(bodyPartPrice.toFixed(2));
                $('#display_finishing_price').text(finishingPartPrice.toFixed(2));

                if (bodyPartPrice <= 0) {
                    $('#part_body').prop('disabled', true).prop('checked', false);
                } else {
                    $('#part_body').prop('disabled', false);
                }

                if (finishingPartPrice <= 0) {
                    $('#part_finishing').prop('disabled', true).prop('checked', false);
                } else {
                    $('#part_finishing').prop('disabled', false);
                }

                $('#prices-section').show();
                calculateTotals();
            });
        }
    }

    $(function () {
        $('.select2').select2();

        $('#dealer_id').on('change', function() {
            let selected = $(this).find('option:selected');
            $('#dealer_phone').val(selected.data('phone') || '');
            $('#dealer_address').val(selected.data('address') || '');
        });

        $('#product_id').on('change', function() {
            let productId = $(this).val();
            loadPrices(productId);
        });

        $('#manufacture_qty').on('input', calculateTotals);
        $('.part-checkbox').on('change', calculateTotals);

        // Load prices on page load if product is selected
        if ($('#product_id').val()) {
            loadPrices($('#product_id').val());
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#manufactureForm').on('submit', function (e) {
            e.preventDefault();
            $('#saveBtn').text('Updating...').attr('disabled', true);
            $('.error-text').text('');

            if (!$('#part_body').is(':checked') && !$('#part_finishing').is(':checked')) {
                $('.parts_error').text('At least one part (Body or Finishing) must be selected.');
                $('#saveBtn').text('Update Order').attr('disabled', false);
                return;
            }

            $.ajax({
                url: "{{ route('admin.manufacture.update', $manufacture->id) }}",
                type: 'POST', // With _method=PUT in FormData
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function (data) {
                    toastr.success(data.success);
                    setTimeout(() => {
                        window.location.href = "{{ route('admin.manufacture.index') }}";
                    }, 1000);
                },
                error: function (data) {
                    $('#saveBtn').text('Update Order').attr('disabled', false);
                    if (data.status === 422) {
                        let errors = data.responseJSON.errors;
                        $.each(errors, function(prefix, val) {
                            $('span.' + prefix.replace('.', '_') + '_error').text(val[0]);
                        });
                        toastr.error('Validation error. Please check fields.');
                    } else {
                        toastr.error(data.responseJSON.error || 'Something went wrong.');
                    }
                }
            });
        });
    });
</script>
@endpush
