@extends('layouts.admin')
@section('title', 'Add Product Recipe')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Add Product Recipe</h4>
                <a href="{{ route('admin.product-recipe.index') }}" class="btn btn-secondary"><i data-feather="arrow-left"></i> Back to List</a>
            </div>
            <div class="card-body pt-2">
                <form action="{{ route('admin.product-recipe.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Select Product <span class="text-danger">*</span></label>
                            <select name="product_id" class="form-control select2" required>
                                <option value="">Select a Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Select Raw Materials</label>
                            <select name="raw_materials[]" class="form-control select2" multiple>
                                @foreach($raw_material as $material)
                                    <option value="{{ $material->id }}">{{ $material->name }} ({{ $material->unit ? $material->unit->name : '' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Body Charge</label>
                            <input type="number" step="0.01" name="body_charge" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Finishing Charge</label>
                            <input type="number" step="0.01" name="finishing_charge" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Bearing</label>
                            <input type="number" step="0.01" name="bearing" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Stone</label>
                            <input type="number" step="0.01" name="stone" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Electric Bill</label>
                            <input type="number" step="0.01" name="electric_bill" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Gas Bill</label>
                            <input type="number" step="0.01" name="gas_bill" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Box Price</label>
                            <input type="number" step="0.01" name="box_price" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Carrying Charge</label>
                            <input type="number" step="0.01" name="carrying_charge" class="form-control" value="0">
                        </div>
                        <div class="col-md-3 form-group">
                            <label>Wire Price</label>
                            <input type="number" step="0.01" name="wire_price" class="form-control" value="0">
                        </div>
                        <div class="col-md-12 mt-3 mb-3">
                            <div class="card border">
                                <div class="card-header bg-light p-2 border-bottom">
                                    <h4 class="card-title mb-0">Items Details</h4>
                                </div>
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover text-center align-middle mb-0">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th class="text-nowrap" style="min-width: 120px;">Thickness (T)</th>
                                                    <th class="text-nowrap" style="min-width: 100px;">Width (X)</th>
                                                    <th class="text-nowrap" style="min-width: 100px;">Height (Y)</th>
                                                    <th class="text-nowrap" style="min-width: 100px;">Area</th>
                                                    <th class="text-nowrap" style="min-width: 120px;">Grand Value</th>
                                                    <th class="text-nowrap" style="min-width: 150px;">Qty(Carbide/Iron)</th>
                                                    <th class="text-nowrap" style="min-width: 180px;">Price/gram(Carbide/Iron)</th>
                                                    <th class="text-nowrap" style="min-width: 180px;">Total Price(Carbide/Iron)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <input type="number" step="0.01" name="thickness[]" class="form-control form-control-sm text-center" value="2" readonly>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" name="width[]" placeholder="0" class="form-control form-control-sm text-center" value="">
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" name="height[]" placeholder="0" class="form-control form-control-sm text-center" value="">
                                                    </td>
                                                    <td>
                                                        {{-- if raw material carbaid apply area = (thickness * width * height),--}}
                                                        {{-- if raw material iron apply area = (width * width * height),--}}
                                                        <input type="number" step="0.01" name="area[]" class="form-control form-control-sm text-center area" value="0" readonly>
                                                    </td>
                                                    <td>
                                                        {{-- it comes from Raw Material grand_value depend on raw_materials selected /carbide or iron--}}
                                                        <input type="number" step="0.01" name="grade_value[]" class="form-control form-control-sm text-center" value="0" readonly>
                                                    </td>
                                                    <td>
                                                      {{--  area * grade_value  --}}
                                                        <input type="number" step="0.01" name="qty[]" class="form-control form-control-sm text-center" value="0" readonly>
                                                    </td>
                                                    <td>
                                                      {{--  it comes from Raw Material price_per_gm depend on raw_materials selected /carbide or iron--}}
                                                        <input type="number" step="0.01" name="price_per_gm[]" class="form-control form-control-sm text-center" value="0" readonly>
                                                    </td>
                                                    <td>
                                                        {{-- total_price = qty * price_per_gm--}}
                                                        <input type="number" step="0.01" name="total_price[]" class="form-control form-control-sm text-center" value="0" readonly>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Save Recipe</button>
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
        $('.select2').select2({
            placeholder: "Select options"
        });
    });
</script>
@endpush
