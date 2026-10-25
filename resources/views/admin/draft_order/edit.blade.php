@extends('layouts.admin')
@section('title', 'Edit Draft Order - ' . $draftOrder->draft_number)

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-8 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Edit Draft Order</h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.draft-orders.index') }}">Draft Orders</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.draft-orders.show', $draftOrder->id) }}">{{ $draftOrder->draft_number }}</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-4 col-12 text-md-right mb-2">
        <a href="{{ route('admin.draft-orders.show', $draftOrder->id) }}" class="btn btn-outline-secondary">
            <i data-feather="arrow-left" class="mr-50"></i> Back to Details
        </a>
    </div>
</div>

<div class="content-body">
    <form action="{{ route('admin.draft-orders.update', $draftOrder->id) }}" method="POST" id="editDraftForm">
        @csrf
        @method('PUT')

        <div class="card shadow-sm border-0 mb-2">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap pb-2 border-bottom">
                    <div>
                        <h4 class="font-weight-bolder text-primary mb-25">
                            <i data-feather="edit-3" class="mr-50"></i> {{ $draftOrder->draft_number }}
                        </h4>
                        <span class="text-muted">Dealer:</span>
                        <strong>{{ $draftOrder->dealer->shop_name ?? 'N/A' }}</strong> ({{ $draftOrder->dealer->name ?? 'N/A' }} - {{ $draftOrder->dealer->phone ?? 'N/A' }})
                    </div>
                    @if($draftOrder->orderRequest)
                        <div class="text-md-right mt-md-0 mt-1">
                            <span class="text-muted">Original Order Request:</span>
                            <span class="badge badge-light-info font-weight-bold">{{ $draftOrder->orderRequest->request_number ?? ('#' . $draftOrder->orderRequest->id) }}</span>
                        </div>
                    @endif
                </div>

                <div class="alert alert-light-info mt-2" role="alert">
                    <div class="alert-body d-flex align-items-center">
                        <i data-feather="info" class="mr-50 flex-shrink-0" style="width:20px;height:20px;"></i>
                        <span>
                            <strong>Note on Quantity Changes:</strong> Reducing an item's quantity will automatically return the excess items back to the original Order Request.
                        </span>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="table-responsive my-2">
                    <table class="table table-bordered mb-0" id="itemsTable">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="width: 60px;">Image</th>
                                <th>Product Name</th>
                                <th class="text-right" style="width: 130px;">Unit Price (৳)</th>
                                <th class="text-center" style="width: 140px;">Quantity</th>
                                <th class="text-right" style="width: 140px;">Total (৳)</th>
                                <th>Item Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($draftOrder->items as $index => $item)
                                <tr>
                                    <td>
                                        {{ $index + 1 }}
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td>
                                        @if($item->product && $item->product->featured_image)
                                            <img src="{{ asset(config('imagepath.product') . $item->product->featured_image) }}" width="40" height="40" style="border-radius:6px;object-fit:cover;" onerror="this.src='{{ asset('images/no-image.png') }}'">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" width="40" height="40" style="border-radius:6px;object-fit:cover;">
                                        @endif
                                    </td>
                                    <td class="font-weight-bold text-dark">
                                        {{ $item->product->name ?? 'N/A' }}
                                    </td>
                                    <td class="text-right font-weight-bold">
                                        ৳{{ number_format($item->unit_price, 2) }}
                                        <input type="hidden" class="unit-price-input" value="{{ $item->unit_price }}" data-index="{{ $index }}">
                                    </td>
                                    <td>
                                        <input type="number" name="items[{{ $index }}][qty]" class="form-control form-control-sm text-center qty-input font-weight-bold"
                                            value="{{ $item->qty }}" min="1" step="1" data-index="{{ $index }}" required>
                                    </td>
                                    <td class="text-right font-weight-bolder text-primary">
                                        ৳<span class="row-total" id="rowTotal{{ $index }}">{{ number_format($item->total, 2) }}</span>
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $index }}][note]" class="form-control form-control-sm"
                                            placeholder="Optional item note..." value="{{ $item->note }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Notes & Totals Row -->
                <div class="row mt-3">
                    <div class="col-md-7 col-12">
                        <div class="form-group">
                            <label class="font-weight-bold">Customer Note</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Customer notes...">{{ old('note', $draftOrder->note) }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Admin Internal Note</label>
                            <textarea name="admin_notes" class="form-control" rows="3" placeholder="Admin internal notes...">{{ old('admin_notes', $draftOrder->admin_notes) }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-5 col-12">
                        <div class="card bg-light border-0">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted font-weight-bold">Sub Total:</span>
                                    <span class="font-weight-bolder" id="summarySubTotal">৳{{ number_format($draftOrder->sub_total, 2) }}</span>
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-muted mb-25">Discount (৳)</label>
                                    <input type="number" name="discount" id="inputDiscount" class="form-control form-control-sm text-right"
                                        value="{{ old('discount', $draftOrder->discount ?? 0) }}" min="0" step="0.01">
                                </div>
                                <div class="form-group mb-1">
                                    <label class="small font-weight-bold text-muted mb-25">Carrying Charge (৳)</label>
                                    <input type="number" name="carrying_charge" id="inputCarrying" class="form-control form-control-sm text-right"
                                        value="{{ old('carrying_charge', $draftOrder->carrying_charge ?? 0) }}" min="0" step="0.01">
                                </div>
                                <hr class="my-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bolder text-dark" style="font-size:1.1rem;">Grand Total:</span>
                                    <span class="font-weight-bolder text-primary" id="summaryGrandTotal" style="font-size:1.25rem;">৳{{ number_format($draftOrder->grand_total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-right mt-2">
                            <button type="submit" class="btn btn-primary font-weight-bold px-3">
                                <i data-feather="save" class="mr-50"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function recalculate() {
        let subTotal = 0;
        $('.qty-input').each(function() {
            let index = $(this).data('index');
            let qty = parseInt($(this).val()) || 0;
            let unitPrice = parseFloat($('.unit-price-input[data-index="' + index + '"]').val()) || 0;
            let total = qty * unitPrice;
            $('#rowTotal' + index).text(total.toFixed(2));
            subTotal += total;
        });

        $('#summarySubTotal').text('৳' + subTotal.toFixed(2));

        let discount = parseFloat($('#inputDiscount').val()) || 0;
        let carrying = parseFloat($('#inputCarrying').val()) || 0;
        let grandTotal = Math.max(0, (subTotal - discount) + carrying);

        $('#summaryGrandTotal').text('৳' + grandTotal.toFixed(2));
    }

    $('.qty-input, #inputDiscount, #inputCarrying').on('input change', function() {
        recalculate();
    });

    recalculate();
});
</script>
@endpush
