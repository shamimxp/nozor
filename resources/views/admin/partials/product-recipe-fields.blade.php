@php
    $recipe = $recipe ?? null;
    $showProductSelect = $showProductSelect ?? false;
    $products = $products ?? collect();
    $raw_material = $raw_material ?? collect();
    $existingItems = [];
    if ($recipe && $recipe->relationLoaded('items') === false) {
        $recipe->load('items.rawMaterialProduct.materialType');
    }
    if ($recipe) {
        foreach ($recipe->items as $item) {
            $material = $item->rawMaterialProduct;
            $existingItems[] = [
                'raw_material_product_id' => $item->raw_material_product_id,
                'name' => $material->name ?? '',
                'type' => optional(optional($material)->materialType)->name ?? '',
                'thickness' => $item->thickness,
                'width' => $item->width,
                'height' => $item->height,
                'area' => $item->area,
                'grade_value' => $item->grade_value,
                'qty' => $item->qty,
            ];
        }
    }
    $selectedMaterialIds = collect($existingItems)->pluck('raw_material_product_id')->map(fn ($id) => (int) $id)->all();
@endphp

@if($showProductSelect)
    <div class="col-md-6 form-group">
        <label>Select Product <span class="text-danger">*</span></label>
        <select name="product_id" class="form-control select2" required>
            <option value="">Select a Product</option>
            @foreach($products as $productOption)
                <option value="{{ $productOption->id }}" {{ $recipe && $recipe->product_id == $productOption->id ? 'selected' : '' }}>{{ $productOption->name }}</option>
            @endforeach
        </select>
    </div>
@endif

<div class="col-md-12 form-group">
    <label>Select Raw Materials</label>
    <select name="raw_materials[]" id="raw_materials_select" class="form-control" multiple>
        @foreach($raw_material as $material)
            <option value="{{ $material->id }}"
                data-type="{{ $material->materialType ? strtolower($material->materialType->name) : '' }}"
                data-grade="{{ $material->grade_value }}"
                data-price="{{ $material->price_per_unit }}"
                {{ in_array((int) $material->id, $selectedMaterialIds, true) ? 'selected' : '' }}>
                {{ $material->name }} ({{ $material->unit ? $material->unit->name : '' }})
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-3 form-group">
    <label>Body Charge</label>
    <input type="number" step="0.01" min="0" name="body_charge" class="form-control" value="{{ $recipe->body_charge ?? 0 }}">
</div>
<div class="col-md-3 form-group">
    <label>Finishing Charge</label>
    <input type="number" step="0.01" min="0" name="finishing_charge" class="form-control" value="{{ $recipe->finishing_charge ?? 0 }}">
</div>
<div class="col-md-3 form-group">
    <label>Bearing</label>
    <input type="number" step="0.01" min="0" name="bearing" class="form-control" value="{{ $recipe->bearing ?? 0 }}">
</div>
<div class="col-md-3 form-group">
    <label>Stone</label>
    <input type="number" step="0.01" min="0" name="stone" class="form-control" value="{{ $recipe->stone ?? 0 }}">
</div>
<div class="col-md-3 form-group">
    <label>Electric Bill</label>
    <input type="number" step="0.01" min="0" name="electric_bill" class="form-control" value="{{ $recipe->electric_bill ?? 0 }}">
</div>
<div class="col-md-3 form-group">
    <label>Gas Bill</label>
    <input type="number" step="0.01" min="0" name="gas_bill" class="form-control" value="{{ $recipe->gas_bill ?? 0 }}">
</div>
<div class="col-md-3 form-group">
    <label>Box Price</label>
    <input type="number" step="0.01" min="0" name="box_price" class="form-control" value="{{ $recipe->box_price ?? 0 }}">
</div>
<div class="col-md-3 form-group">
    <label>Carrying Charge</label>
    <input type="number" step="0.01" min="0" name="carrying_charge" class="form-control" value="{{ $recipe->carrying_charge ?? 0 }}">
</div>
<div class="col-md-3 form-group">
    <label>Wire Price</label>
    <input type="number" step="0.01" min="0" name="wire_price" class="form-control" value="{{ $recipe->wire_price ?? 0 }}">
</div>

<div class="col-md-12 form-group">
    <label>Manufacture Images (Multiple)</label>
    <input type="file" name="manufacture_images[]" id="manufacture_images" class="form-control" multiple accept="image/*">
    @if($recipe && is_array($recipe->manufacture_images) && count($recipe->manufacture_images) > 0)
        <div id="existing_images_container" class="mt-2 d-flex flex-wrap" style="gap: 10px; margin-bottom: 10px;">
            @foreach($recipe->manufacture_images as $img)
                <div class="position-relative existing-image-wrapper">
                    <input type="hidden" name="existing_images[]" value="{{ $img }}">
                    <img src="{{ asset($img) }}" alt="image" width="80" height="80" class="rounded border" style="object-fit: cover;">
                    <button type="button" class="btn btn-sm btn-danger position-absolute remove-existing-image" style="top: -5px; right: -5px; padding: 2px 5px; font-size: 10px;">&times;</button>
                </div>
            @endforeach
        </div>
    @endif
    <div id="image_preview_container" class="mt-2 d-flex flex-wrap" style="gap: 10px;"></div>
</div>

<div class="col-md-12 mt-1 mb-1">
    <div class="card border mb-0">
        <div class="card-header bg-light p-2 border-bottom">
            <h4 class="card-title mb-0">Recipe Items</h4>
        </div>
        <div class="card-body p-2">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-center align-middle mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th class="text-nowrap" style="min-width: 150px;">Material</th>
                            <th class="text-nowrap" style="min-width: 120px;">Thickness (T)</th>
                            <th class="text-nowrap" style="min-width: 100px;">Width (X)</th>
                            <th class="text-nowrap" style="min-width: 100px;">Height (Y)</th>
                            <th class="text-nowrap" style="min-width: 100px;">Area</th>
                            <th class="text-nowrap" style="min-width: 120px;">Grand Value</th>
                            <th class="text-nowrap" style="min-width: 150px;">Qty(Carbide/Iron)</th>
                        </tr>
                    </thead>
                    <tbody id="items_table_body"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    window.productRecipeExistingItems = @json($existingItems);
</script>
@once
    @push('scripts')
        <script src="{{ asset('js/product-recipe-form.js') }}?v=2"></script>
    @endpush
@endonce
