@php
    $recipe = $recipe ?? null;
    $showProductSelect = $showProductSelect ?? false;
    $products = $products ?? collect();
    $raw_material = $raw_material ?? collect();
    $cost_settings = $cost_settings ?? \App\Models\CostSetting::where('status', 1)->orderBy('cost_title')->get();
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
                'price_per_unit' => optional($material)->price_per_unit ?? 0,
            ];
        }
    }
    $selectedMaterialIds = collect($existingItems)->pluck('raw_material_product_id')->map(fn ($id) => (int) $id)->all();
    $webSetting = \App\Models\WebSetting::first();
    $defaultDealerProfit = $webSetting ? $webSetting->dealer_profit_percent : 25;
    $defaultSpecialDealerProfit = $webSetting ? $webSetting->special_dealer_profit_percent : 15;
    $defaultCustomerProfit = 40;

    $dealerProfitType = old('dealer_profit_type', $recipe->dealer_profit_type ?? 'percentage');
    $specialDealerProfitType = old('special_dealer_profit_type', $recipe->special_dealer_profit_type ?? 'percentage');
    $customerProfitType = old('normal_customer_profit_type', $recipe->normal_customer_profit_type ?? 'percentage');

    $selectedCostSettingId = old('cost_setting_id', $recipe->cost_setting_id ?? '');
@endphp

{{-- Product Selection --}}
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

{{-- Select Cost Title (Cost Setting) --}}
<div class="col-md-{{ $showProductSelect ? '6' : '12' }} form-group">
    <label>Select Cost Title <small class="text-muted">(Cost Setting)</small></label>
    <select name="cost_setting_id" id="cost_setting_select" class="form-control select2">
        <option value="">-- Choose Cost Title --</option>
        @foreach($cost_settings as $cs)
            <option value="{{ $cs->id }}" {{ (string)$selectedCostSettingId === (string)$cs->id ? 'selected' : '' }}>
                {{ $cs->cost_title }}
            </option>
        @endforeach
    </select>
    <small class="text-muted font-small-2">Selecting a cost title will automatically load its cost heads in read-only mode</small>
</div>

{{-- Dynamic Cost Setting Heads Container (Auto Details - Read Only) --}}
<div class="col-md-12 mb-2" id="cost_setting_details_section" style="display: none;">
    <div class="card border mb-0 shadow-none bg-light">
        <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 font-weight-bold text-dark">
                <i data-feather="check-circle" class="text-success mr-50"></i> Cost Head Declaration Details (<span id="cs_display_title">-</span>)
                <span class="badge badge-light-secondary ml-50">Read-Only</span>
            </h5>
            <div>
                <span class="text-muted font-small-3 mr-50">Total Declared Charges:</span>
                <span class="font-weight-bolder text-primary">৳ <span id="cs_display_total">0.00</span></span>
            </div>
        </div>
        <div class="card-body p-2">
            <div class="row" id="cost_setting_heads_container">
                {{-- Loaded dynamically via AJAX --}}
            </div>
        </div>
    </div>
</div>



{{-- Manufacture Images --}}
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

{{-- Select Raw Materials (Positioned directly before Recipe Items) --}}
<div class="col-md-12 form-group mt-1">
    <label class="font-weight-bold">Select Raw Materials</label>
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

{{-- Recipe Items Table --}}
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

{{-- Product Pricing & Profit Calculation Section --}}
<div class="col-md-12 mt-2 mb-2">
    <div class="card border">
        <div class="card-header bg-light p-2 border-bottom d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Product Pricing & Profit Calculation</h4>
            <span class="badge badge-light-primary">Real-time Calculation</span>
        </div>
        <div class="card-body p-2">
            <!-- Cost Summary Row -->
            <div class="row">
                <div class="col-md-4 col-12 mb-1">
                    <div class="p-2 border rounded bg-light">
                        <small class="text-muted d-block font-weight-bold text-uppercase">Total Raw Material Cost</small>
                        <h4 class="mb-0 text-secondary mt-1">৳ <span id="calc_material_cost">0.00</span></h4>
                    </div>
                </div>
                <div class="col-md-4 col-12 mb-1">
                    <div class="p-2 border rounded bg-light">
                        <small class="text-muted d-block font-weight-bold text-uppercase">Total Charges (Cost Setting / Heads)</small>
                        <h4 class="mb-0 text-secondary mt-1">৳ <span id="calc_charges_total">0.00</span></h4>
                    </div>
                </div>
                <div class="col-md-4 col-12 mb-1">
                    <div class="p-2 border rounded bg-primary text-white">
                        <small class="text-white-50 d-block font-weight-bold text-uppercase">Total Product Cost</small>
                        <h4 class="mb-0 text-white mt-1">৳ <span id="calc_product_cost">0.00</span></h4>
                    </div>
                </div>
            </div>

            <!-- Profit & Price Configuration Row -->
            <div class="row mt-2">
                <!-- Dealer Price -->
                <!-- Dealer Price -->
                <div class="col-md-4 col-12 mb-2">
                    <div class="card border mb-0 h-100">
                        <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-success font-weight-bolder">Dealer Pricing</h5>
                            <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-success btn-sm profit-type-btn {{ $dealerProfitType === 'percentage' ? 'active' : '' }} py-25 px-50">
                                    <input type="radio" name="dealer_profit_type" value="percentage" {{ $dealerProfitType === 'percentage' ? 'checked' : '' }}> %
                                </label>
                                <label class="btn btn-outline-success btn-sm profit-type-btn {{ $dealerProfitType === 'fixed' ? 'active' : '' }} py-25 px-50">
                                    <input type="radio" name="dealer_profit_type" value="fixed" {{ $dealerProfitType === 'fixed' ? 'checked' : '' }}> Fixed (৳)
                                </label>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="form-group mb-1">
                                <label class="font-weight-bold" id="dealer_profit_label">Dealer Profit ({{ $dealerProfitType === 'fixed' ? '৳' : '%' }}) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="dealer_profit_percent" id="dealer_profit_percent" class="form-control" placeholder="0.00" value="{{ old('dealer_profit_percent', ($recipe && $recipe->dealer_profit_percent !== null) ? $recipe->dealer_profit_percent : '') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text profit-unit-symbol" id="dealer_profit_unit">{{ $dealerProfitType === 'fixed' ? '৳' : '%' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <span class="text-muted font-small-3">Profit Amount:</span>
                                <span class="font-weight-bold text-dark font-small-3">৳ <span id="calc_dealer_profit_amount">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-1">
                                <span class="font-weight-bold text-success">Dealer Price:</span>
                                <h4 class="mb-0 text-success font-weight-bolder">৳ <span id="calc_dealer_price">0.00</span></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Special Dealer Price -->
                <div class="col-md-4 col-12 mb-2">
                    <div class="card border mb-0 h-100">
                        <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-info font-weight-bolder">Special Dealer Pricing</h5>
                            <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-info btn-sm profit-type-btn {{ $specialDealerProfitType === 'percentage' ? 'active' : '' }} py-25 px-50">
                                    <input type="radio" name="special_dealer_profit_type" value="percentage" {{ $specialDealerProfitType === 'percentage' ? 'checked' : '' }}> %
                                </label>
                                <label class="btn btn-outline-info btn-sm profit-type-btn {{ $specialDealerProfitType === 'fixed' ? 'active' : '' }} py-25 px-50">
                                    <input type="radio" name="special_dealer_profit_type" value="fixed" {{ $specialDealerProfitType === 'fixed' ? 'checked' : '' }}> Fixed (৳)
                                </label>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="form-group mb-1">
                                <label class="font-weight-bold" id="special_dealer_profit_label">Special Dealer Profit ({{ $specialDealerProfitType === 'fixed' ? '৳' : '%' }}) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="special_dealer_profit_percent" id="special_dealer_profit_percent" class="form-control" placeholder="0.00" value="{{ old('special_dealer_profit_percent', ($recipe && $recipe->special_dealer_profit_percent !== null) ? $recipe->special_dealer_profit_percent : '') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text profit-unit-symbol" id="special_dealer_profit_unit">{{ $specialDealerProfitType === 'fixed' ? '৳' : '%' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <span class="text-muted font-small-3">Profit Amount:</span>
                                <span class="font-weight-bold text-dark font-small-3">৳ <span id="calc_special_dealer_profit_amount">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-1">
                                <span class="font-weight-bold text-info">Special Dealer Price:</span>
                                <h4 class="mb-0 text-info font-weight-bolder">৳ <span id="calc_special_dealer_price">0.00</span></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Normal Customer Price -->
                <div class="col-md-4 col-12 mb-2">
                    <div class="card border mb-0 h-100">
                        <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-primary font-weight-bolder">Normal Customer Pricing</h5>
                            <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
                                <label class="btn btn-outline-primary btn-sm profit-type-btn {{ $customerProfitType === 'percentage' ? 'active' : '' }} py-25 px-50">
                                    <input type="radio" name="normal_customer_profit_type" value="percentage" {{ $customerProfitType === 'percentage' ? 'checked' : '' }}> %
                                </label>
                                <label class="btn btn-outline-primary btn-sm profit-type-btn {{ $customerProfitType === 'fixed' ? 'active' : '' }} py-25 px-50">
                                    <input type="radio" name="normal_customer_profit_type" value="fixed" {{ $customerProfitType === 'fixed' ? 'checked' : '' }}> Fixed (৳)
                                </label>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="form-group mb-1">
                                <label class="font-weight-bold" id="normal_customer_profit_label">Customer Profit ({{ $customerProfitType === 'fixed' ? '৳' : '%' }}) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="normal_customer_profit_percent" id="normal_customer_profit_percent" class="form-control" placeholder="0.00" value="{{ old('normal_customer_profit_percent', ($recipe && $recipe->normal_customer_profit_percent !== null) ? $recipe->normal_customer_profit_percent : '') }}">
                                    <div class="input-group-append">
                                        <span class="input-group-text profit-unit-symbol" id="normal_customer_profit_unit">{{ $customerProfitType === 'fixed' ? '৳' : '%' }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                <span class="text-muted font-small-3">Profit Amount:</span>
                                <span class="font-weight-bold text-dark font-small-3">৳ <span id="calc_customer_profit_amount">0.00</span></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center pt-1">
                                <span class="font-weight-bold text-primary">Customer Price:</span>
                                <h4 class="mb-0 text-primary font-weight-bolder">৳ <span id="calc_customer_price">0.00</span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info py-1 px-2 mb-0 mt-1" role="alert">
                <small>Prices and profits are calculated automatically in real time. Choose % (percentage) or Fixed amount (৳) for each pricing tier.</small>
            </div>
        </div>
    </div>
</div>

<script>
    window.productRecipeExistingItems = @json($existingItems);
</script>
@once
    @push('scripts')
        <script src="{{ asset('js/product-recipe-form.js') }}?v=4"></script>
    @endpush
@endonce
