@extends('layouts.admin')
@section('title', 'Add Product Offer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Add Product Offer</h4>
                <a href="{{ route('admin.product-offer.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.product-offer.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-1">
                            <label>Product <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-control select2" required data-placeholder="Select product">
                                <option value="">Select product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ (string) old('product_id') === (string) $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3 mb-1">
                            <label>From Date <span class="text-danger">*</span></label>
                            <input type="date" name="from_date" class="form-control" required value="{{ old('from_date') }}">
                        </div>
                        <div class="col-md-3 mb-1">
                            <label>To Date <span class="text-danger">*</span></label>
                            <input type="date" name="to_date" class="form-control" required value="{{ old('to_date') }}">
                        </div>

                        <div class="col-md-3 mb-1">
                            <label>From Qty <span class="text-danger">*</span></label>
                            <input type="number" name="from_qty" class="form-control" step="1" min="1" required value="{{ old('from_qty', 1) }}">
                        </div>
                        <div class="col-md-3 mb-1">
                            <label>To Qty <span class="text-danger">*</span></label>
                            <input type="number" name="to_qty" class="form-control" step="1" min="1" required value="{{ old('to_qty') }}">
                        </div>

                        <div class="col-md-3 mb-1">
                            <label>Offer Type <span class="text-danger">*</span></label>
                            <select name="offer_type" class="form-control" required>
                                <option value="fixed" {{ old('offer_type') === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                <option value="percent" {{ old('offer_type') === 'percent' ? 'selected' : '' }}>Percentage</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-1">
                            <label>Discount Value <span class="text-danger">*</span></label>
                            <input type="number" name="discount_value" class="form-control" step="0.01" min="0.01" required value="{{ old('discount_value') }}">
                        </div>

                        <div class="col-md-3 mb-1">
                            <label>Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-12 mt-1">
                            <button type="submit" class="btn btn-primary">Save Offer</button>
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
    $(function () {
        if ($.fn.select2) {
            $('.select2').select2({ width: '100%' });
        }
    });
</script>
@endpush
