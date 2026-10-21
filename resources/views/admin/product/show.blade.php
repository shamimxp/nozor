@extends('layouts.admin')
@section('title', 'Product Details: ' . $product->name)

@section('content')
@php
    $discountLabel = 'No Discount';
    if ($product->discount_type == 'percent') {
        $discountLabel = rtrim(rtrim(number_format((float) $product->discount_amount, 2), '0'), '.') . '%';
    } elseif ($product->discount_type == 'amount') {
        $discountLabel = '৳' . number_format((float) $product->discount_amount, 2) . ' Fixed';
    }

    $recipe = $product->recipe;
    $manufactureImages = ($product->is_manufacturer && $recipe && is_array($recipe->manufacture_images))
        ? $recipe->manufacture_images
        : [];

    $costSetting = ($recipe && $recipe->cost_setting_id) ? $recipe->costSetting : null;
    $hasCostSetting = $costSetting && $costSetting->items->isNotEmpty();

    // Calculate recipe item breakdown for table
    $recipeItemsBreakdown = [];
    $totalMaterialCost = 0;
    $totalArea = 0;

    if ($recipe && $recipe->items) {
        foreach ($recipe->items as $it) {
            $material = $it->rawMaterialProduct;
            $unitPrice = $material ? (float) $material->price_per_unit : 0;
            $qty = (float) $it->qty;
            $itemCost = round($qty * $unitPrice, 2);
            $totalMaterialCost += $itemCost;
            $totalArea += (float) $it->area;

            $recipeItemsBreakdown[] = [
                'material_name' => $material->name ?? 'N/A',
                'material_type' => optional(optional($material)->materialType)->name ?? '',
                'unit' => optional(optional($material)->unit)->name ?? '',
                'thickness' => $it->thickness,
                'width' => $it->width,
                'height' => $it->height,
                'area' => $it->area,
                'grade_value' => $it->grade_value,
                'qty' => $it->qty,
                'unit_price' => $unitPrice,
                'cost' => $itemCost,
            ];
        }
    }

    // Charges breakdown
    $chargesBreakdown = [];
    $totalCharges = 0;

    if ($hasCostSetting) {
        foreach ($costSetting->items as $csItem) {
            $chargeVal = (float) $csItem->cost_price;
            $totalCharges += $chargeVal;
            $chargesBreakdown[] = [
                'title' => $csItem->costHead->title ?? 'Cost Head #' . $csItem->cost_head_id,
                'amount' => $chargeVal,
            ];
        }
    } elseif ($recipe) {
        $legacyFields = [
            'Body Charge' => (float) $recipe->body_charge,
            'Finishing Charge' => (float) $recipe->finishing_charge,
            'Bearing' => (float) $recipe->bearing,
            'Stone' => (float) $recipe->stone,
            'Electric Bill' => (float) $recipe->electric_bill,
            'Gas Bill' => (float) $recipe->gas_bill,
            'Box Price' => (float) $recipe->box_price,
            'Carrying Charge' => (float) $recipe->carrying_charge,
            'Wire Price' => (float) $recipe->wire_price,
            'Pitol Price' => (float) ($recipe->pitol_price ?? 0),
        ];
        foreach ($legacyFields as $title => $amount) {
            $totalCharges += $amount;
            $chargesBreakdown[] = [
                'title' => $title,
                'amount' => $amount,
            ];
        }
    }
@endphp

<div class="pd-page">
    {{-- Header Banner / Hero --}}
    <div class="pd-hero">
        <div>
            <div class="pd-kicker">Product Management &bull; Detailed View</div>
            <h1 class="pd-title">{{ $product->name }}</h1>
            <div class="pd-meta">
                @if($product->model_no)
                    <span class="pd-model"><i data-feather="bookmark" class="mr-25"></i> Model: {{ $product->model_no }}</span>
                @endif
                <span class="text-dark font-weight-bold">
                    <i data-feather="folder" class="mr-25"></i>
                    {{ $product->category->name ?? 'Uncategorized' }}
                    @if($product->subCategory)
                        <span class="pd-sep">/</span> {{ $product->subCategory->name }}
                    @endif
                </span>
                @if($product->unit)
                    <span class="badge badge-light-secondary font-small-2 ml-50">Unit: {{ $product->unit->name }}</span>
                @endif
            </div>

            {{-- Flags & Status Chips --}}
            <div class="pd-chips">
                <span class="pd-chip {{ $product->status ? 'is-on' : 'is-off' }}">
                    <i data-feather="{{ $product->status ? 'check-circle' : 'x-circle' }}" class="mr-25"></i>
                    Status: {{ $product->status ? 'Active' : 'Inactive' }}
                </span>
                <span class="pd-chip {{ $product->is_featured ? 'is-info' : '' }}">
                    <i data-feather="star" class="mr-25"></i> Featured: {{ $product->is_featured ? 'Yes' : 'No' }}
                </span>
                <span class="pd-chip {{ $product->is_manufacturer ? 'is-primary' : '' }}">
                    <i data-feather="tool" class="mr-25"></i> Manufacturer Product: {{ $product->is_manufacturer ? 'Yes' : 'No' }}
                </span>
                <span class="pd-chip {{ $product->dealer_show ? 'is-on' : 'is-off' }}">
                    <i data-feather="users" class="mr-25"></i> Dealer: {{ $product->dealer_show ? 'Active' : 'Disabled' }}
                </span>
                <span class="pd-chip {{ $product->customer_show ? 'is-on' : 'is-off' }}">
                    <i data-feather="user" class="mr-25"></i> Customer: {{ $product->customer_show ? 'Active' : 'Disabled' }}
                </span>
            </div>
        </div>

        <div class="pd-actions">
            <a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-primary btn-sm shadow-sm">
                <i data-feather="edit-2" class="mr-25"></i> Edit Product
            </a>
            <a href="{{ route('admin.product.duplicate', $product->id) }}" class="btn btn-warning btn-sm shadow-sm">
                <i data-feather="copy" class="mr-25"></i> Duplicate
            </a>
            <a href="{{ route('admin.product.index') }}" class="btn btn-outline-secondary btn-sm">
                <i data-feather="arrow-left" class="mr-25"></i> Back to List
            </a>
        </div>
    </div>

    {{-- Top Grid: Images + Specifications --}}
    <div class="row">
        {{-- Product Images Gallery --}}
        <div class="col-lg-5 col-12 mb-2">
            <div class="pd-card pd-gallery h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h5 class="mb-0 font-weight-bolder text-dark">
                        <i data-feather="image" class="mr-50 text-primary"></i> Product Images
                    </h5>
                    @if($product->gallery->count() > 0)
                        <span class="badge badge-light-primary">{{ $product->gallery->count() + ($product->featured_image ? 1 : 0) }} Images</span>
                    @endif
                </div>

                <div class="pd-stage">
                    <img id="pdMainImage" src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{ $product->name }}">
                </div>

                @if($product->featured_image || $product->gallery->count() > 0)
                    <div class="pd-thumbs mt-1">
                        @if($product->featured_image)
                            <button type="button" class="pd-thumb is-active" data-src="{{ asset(config('imagepath.product') . $product->featured_image) }}" title="Featured Image">
                                <img src="{{ asset(config('imagepath.product') . $product->featured_image) }}" alt="Featured">
                            </button>
                        @endif
                        @foreach($product->gallery as $img)
                            <button type="button" class="pd-thumb" data-src="{{ asset(config('imagepath.product') . $img->image) }}" title="Gallery Image">
                                <img src="{{ asset(config('imagepath.product') . $img->image) }}" alt="Gallery">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Product Specifications & Commercials --}}
        <div class="col-lg-7 col-12 mb-2">
            <div class="pd-card p-2 h-100">
                <div class="d-flex justify-content-between align-items-center mb-1 border-bottom pb-1">
                    <h5 class="mb-0 font-weight-bolder text-dark">
                        <i data-feather="info" class="mr-50 text-primary"></i> Product Specifications & Inventory
                    </h5>
                    <span class="badge badge-light-secondary font-small-2">ID: #{{ $product->id }}</span>
                </div>

                {{-- Pricing Overview Cards --}}
                <div class="pd-prices mb-2">
                    @if($pricing['type'] === 'manufacture')
                        @if($pricing['available'])
                            <div class="pd-price">
                                <span>Total Cost Price</span>
                                <strong>৳ {{ number_format($pricing['cost_price'], 2) }}</strong>
                                <small class="text-muted d-block mt-25">Material + Declared Charges</small>
                            </div>
                            <div class="pd-price is-dealer">
                                <span>
                                    Dealer Price
                                    <em>
                                        @if($pricing['regular_dealer_type'] === 'fixed')
                                            (+৳{{ number_format($pricing['regular_dealer_percent'], 2) }} Fixed)
                                        @else
                                            (+{{ rtrim(rtrim(number_format((float) $pricing['regular_dealer_percent'], 2), '0'), '.') }}%)
                                        @endif
                                    </em>
                                </span>
                                <strong>৳ {{ number_format($pricing['regular_dealer_price'], 2) }}</strong>
                                <small class="text-success d-block mt-25">Profit: ৳ {{ number_format($pricing['regular_dealer_profit_amount'], 2) }}</small>
                            </div>
                            <div class="pd-price is-special">
                                <span>
                                    Special Dealer Price
                                    <em>
                                        @if($pricing['special_dealer_type'] === 'fixed')
                                            (+৳{{ number_format($pricing['special_dealer_percent'], 2) }} Fixed)
                                        @else
                                            (+{{ rtrim(rtrim(number_format((float) $pricing['special_dealer_percent'], 2), '0'), '.') }}%)
                                        @endif
                                    </em>
                                </span>
                                <strong>৳ {{ number_format($pricing['special_dealer_price'], 2) }}</strong>
                                <small class="text-info d-block mt-25">Profit: ৳ {{ number_format($pricing['special_dealer_profit_amount'], 2) }}</small>
                            </div>
                            <div class="pd-price is-regular">
                                <span>
                                    Normal Customer Price
                                    <em>
                                        @if($pricing['customer_profit_type'] === 'fixed')
                                            (+৳{{ number_format($pricing['customer_profit_percent'], 2) }} Fixed)
                                        @else
                                            (+{{ rtrim(rtrim(number_format((float) $pricing['customer_profit_percent'], 2), '0'), '.') }}%)
                                        @endif
                                    </em>
                                </span>
                                <strong>৳ {{ number_format($pricing['regular_price'], 2) }}</strong>
                                <small class="text-primary d-block mt-25">Profit: ৳ {{ number_format($pricing['customer_profit_amount'], 2) }}</small>
                            </div>
                            @if($pricing['discount_price'] !== null)
                                <div class="pd-price is-discount pd-price-wide">
                                    <span>Discount Price (After {{ $discountLabel }})</span>
                                    <strong>৳ {{ number_format($pricing['discount_price'], 2) }}</strong>
                                </div>
                            @endif
                        @else
                            <div class="pd-empty">
                                <i data-feather="alert-triangle" class="text-warning mr-50"></i>
                                Recipe not configured yet. Please configure the recipe in edit mode to calculate dynamic prices.
                            </div>
                        @endif
                    @else
                        {{-- Regular Product Pricing --}}
                        <div class="pd-price">
                            <span>Cost Price</span>
                            <strong>৳ {{ number_format($pricing['cost_price'] ?? 0, 2) }}</strong>
                        </div>
                        <div class="pd-price is-regular">
                            <span>Selling / Wholesale Price</span>
                            <strong>৳ {{ number_format($pricing['wholesale_price'] ?? 0, 2) }}</strong>
                        </div>
                        <div class="pd-price is-discount {{ $pricing['discount_price'] !== null ? '' : 'pd-price-wide' }}">
                            <span>Discount Price</span>
                            <strong>{{ $pricing['discount_price'] !== null ? '৳ ' . number_format($pricing['discount_price'], 2) : 'No Discount' }}</strong>
                        </div>
                    @endif
                </div>

                {{-- Specs & Inventory Facts Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-sm mb-0">
                        <tbody>
                            <tr>
                                <th class="bg-light text-muted" style="width: 25%;">Product Name</th>
                                <td class="font-weight-bold">{{ $product->name }}</td>
                                <th class="bg-light text-muted" style="width: 25%;">Model Number</th>
                                <td>{{ $product->model_no ?: '—' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Category</th>
                                <td>{{ $product->category->name ?? 'N/A' }}</td>
                                <th class="bg-light text-muted">Sub-Category</th>
                                <td>{{ $product->subCategory->name ?? '—' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Unit</th>
                                <td>{{ $product->unit->name ?? '—' }}</td>
                                <th class="bg-light text-muted">Current Stock</th>
                                <td>
                                    <span class="badge {{ (float)$product->stock > 0 ? 'badge-light-success' : 'badge-light-danger' }} font-weight-bold">
                                        {{ $product->stock ?? 0 }} {{ $product->unit->name ?? 'Units' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Max Order Qty</th>
                                <td>{{ $product->max_order_qty > 0 ? $product->max_order_qty : 'No Limit' }}</td>
                                <th class="bg-light text-muted">Discount Setup</th>
                                <td>
                                    @if($product->discount_type)
                                        <span class="badge badge-light-warning">
                                            {{ ucfirst($product->discount_type) }} &bull; {{ $discountLabel }}
                                        </span>
                                    @else
                                        <span class="text-muted">No Discount</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light text-muted">Product Type</th>
                                <td>
                                    @if($product->is_manufacturer)
                                        <span class="badge badge-light-primary font-weight-bold">
                                            <i data-feather="cpu" class="mr-25"></i> Manufacture Recipe Product
                                        </span>
                                    @else
                                        <span class="badge badge-light-secondary">Regular Ready Product</span>
                                    @endif
                                </td>
                                <th class="bg-light text-muted">Visibility Status</th>
                                <td>
                                    <span class="badge {{ $product->dealer_show ? 'badge-light-success' : 'badge-light-secondary' }} mr-25">
                                        Dealer: {{ $product->dealer_show ? 'Yes' : 'No' }}
                                    </span>
                                    <span class="badge {{ $product->customer_show ? 'badge-light-success' : 'badge-light-secondary' }}">
                                        Customer: {{ $product->customer_show ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Attributes & Variations --}}
    <div class="row">
        {{-- Product Attributes --}}
        <div class="col-md-6 col-12 mb-2">
            <div class="pd-card pd-block h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h5 class="mb-0 font-weight-bolder text-dark">
                        <i data-feather="sliders" class="mr-50 text-primary"></i> Product Attributes
                    </h5>
                    <span class="badge badge-light-secondary">{{ $product->attributes->count() }} Configured</span>
                </div>
                @if($product->attributes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Attribute Name</th>
                                    <th>Assigned Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->attributes as $attr)
                                    <tr>
                                        <td class="font-weight-bold text-dark">{{ $attr->attribute->name ?? 'Attribute' }}</td>
                                        <td><span class="badge badge-light-primary font-weight-bold">{{ $attr->attribute_value }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="pd-muted"><i data-feather="info" class="mr-25"></i> No product attributes assigned.</p>
                @endif
            </div>
        </div>

        {{-- Product Variations --}}
        <div class="col-md-6 col-12 mb-2">
            <div class="pd-card pd-block h-100">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h5 class="mb-0 font-weight-bolder text-dark">
                        <i data-feather="layers" class="mr-50 text-primary"></i> Product Variations
                    </h5>
                    @if($product->variations && $product->variations->count() > 0)
                        @php $groupedVariations = $product->variations->groupBy('variation_id'); @endphp
                        <span class="badge badge-light-secondary">{{ $groupedVariations->count() }} Variation Group(s)</span>
                    @endif
                </div>
                @if($product->variations && $product->variations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Variation</th>
                                    <th>Available Options</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedVariations as $vars)
                                    <tr>
                                        <td class="font-weight-bold text-dark">{{ $vars->first()->variation->name ?? 'Variation' }}</td>
                                        <td>
                                            @foreach($vars as $v)
                                                <span class="badge badge-light-info mr-25 mb-25">{{ $v->variationValue->value ?? '' }}</span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="pd-muted"><i data-feather="info" class="mr-25"></i> No product variations assigned.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Product Recipe Section (When Product is a Manufacture Product) --}}
    @if($product->is_manufacturer)
        <div class="pd-card p-2 mb-2 border-primary">
            <div class="d-flex justify-content-between align-items-center mb-1 pb-1 border-bottom">
                <div>
                    <h4 class="mb-25 font-weight-bolder text-dark">
                        <i data-feather="cpu" class="mr-50 text-primary"></i> Product Recipe & Manufacturing Specifications
                    </h4>
                    <p class="text-muted font-small-3 mb-0">
                        Dynamic cost calculations, raw material formula, and cost head declarations for this manufacture item.
                    </p>
                </div>
                <span class="badge badge-primary px-1 py-50 font-weight-bold">Manufacture Recipe Active</span>
            </div>

            @if($recipe)
                {{-- 1. Cost Title & Cost Head Declaration Container --}}
                <div class="card border mb-2 shadow-none bg-light">
                    <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="card-title mb-0 font-weight-bold text-dark">
                                <i data-feather="check-circle" class="text-success mr-50"></i> Cost Head Declaration Details
                                @if($hasCostSetting)
                                    (<span class="text-primary font-weight-bolder">{{ $costSetting->cost_title }}</span>)
                                @else
                                    <span class="badge badge-light-secondary ml-50">Legacy Cost Fields</span>
                                @endif
                            </h5>
                            @if($hasCostSetting)
                                <small class="text-muted">Linked to Cost Setting #{{ $costSetting->id }} ({{ $costSetting->cost_title }})</small>
                            @endif
                        </div>
                        <div>
                            <span class="text-muted font-small-3 mr-50">Total Declared Charges:</span>
                            <span class="font-weight-bolder text-primary font-medium-2">৳ {{ number_format($totalCharges, 2) }}</span>
                        </div>
                    </div>
                    <div class="card-body p-2">
                        <div class="row">
                            @forelse($chargesBreakdown as $charge)
                                <div class="col-md-3 col-sm-6 col-12 mb-1">
                                    <div class="p-1 border rounded bg-white shadow-none">
                                        <span class="d-block text-muted font-small-3">{{ $charge['title'] }}</span>
                                        <strong class="font-medium-1 text-dark">৳ {{ number_format($charge['amount'], 2) }}</strong>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted mb-0">No cost heads declared.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- 2. Manufacture Images --}}
                <div class="card border mb-2 shadow-none">
                    <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 font-weight-bold text-dark">
                            <i data-feather="camera" class="mr-50 text-primary"></i> Manufacture Technical Images
                        </h5>
                        <span class="badge badge-light-primary">{{ count($manufactureImages) }} Images</span>
                    </div>
                    <div class="card-body p-2">
                        @if(count($manufactureImages) > 0)
                            <div class="pd-mfg mb-0">
                                @foreach($manufactureImages as $index => $img)
                                    <button type="button" class="manufacture-image-open shadow-sm" data-index="{{ $index }}" data-src="{{ asset($img) }}" title="Click to enlarge">
                                        <img src="{{ asset($img) }}" alt="Manufacture image {{ $index + 1 }}">
                                        <small><i data-feather="maximize-2" class="mr-25"></i> Image {{ $index + 1 }}</small>
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted mb-0"><i data-feather="info" class="mr-25"></i> No manufacture images uploaded for this recipe.</p>
                        @endif
                    </div>
                </div>

                {{-- 3. Raw Materials & Recipe Items Table --}}
                <div class="card border mb-2 shadow-none">
                    <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h5 class="card-title mb-0 font-weight-bold text-dark">
                                <i data-feather="list" class="mr-50 text-primary"></i> Recipe Items (Raw Material Formula)
                            </h5>
                            <small class="text-muted">{{ count($recipeItemsBreakdown) }} Material Item(s) in Recipe</small>
                        </div>
                        <div>
                            <span class="text-muted font-small-3 mr-50">Total Raw Material Cost:</span>
                            <span class="font-weight-bolder text-secondary font-medium-2">৳ {{ number_format($totalMaterialCost, 2) }}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover text-center align-middle mb-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th class="text-left">Raw Material</th>
                                        <th>Thickness (T)</th>
                                        <th>Width (X)</th>
                                        <th>Height (Y)</th>
                                        <th>Area</th>
                                        <th>Grade Value</th>
                                        <th>Qty (Carbide/Iron)</th>
                                        <th>Unit Price</th>
                                        <th class="text-right">Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recipeItemsBreakdown as $row)
                                        <tr>
                                            <td class="text-left font-weight-bold">
                                                {{ $row['material_name'] }}
                                                @if($row['material_type'])
                                                    <span class="badge badge-light-secondary font-small-1 ml-25">{{ $row['material_type'] }}</span>
                                                @endif
                                                @if($row['unit'])
                                                    <small class="text-muted">({{ $row['unit'] }})</small>
                                                @endif
                                            </td>
                                            <td>{{ $row['thickness'] }}</td>
                                            <td>{{ $row['width'] }}</td>
                                            <td>{{ $row['height'] }}</td>
                                            <td>{{ $row['area'] }}</td>
                                            <td>{{ $row['grade_value'] }}</td>
                                            <td class="font-weight-bold text-primary">{{ $row['qty'] }}</td>
                                            <td>৳ {{ number_format($row['unit_price'], 2) }}</td>
                                            <td class="text-right font-weight-bold text-dark">৳ {{ number_format($row['cost'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-muted py-2">No recipe items configured.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if(count($recipeItemsBreakdown) > 0)
                                    <tfoot class="bg-light font-weight-bold">
                                        <tr>
                                            <td colspan="4" class="text-right">Summary Totals:</td>
                                            <td>{{ number_format($totalArea, 2) }}</td>
                                            <td colspan="3" class="text-right">Total Material Cost:</td>
                                            <td class="text-right text-secondary font-medium-1">৳ {{ number_format($totalMaterialCost, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                {{-- 4. Product Pricing & Profit Calculation Section (Exact Mirror of Edit Page) --}}
                <div class="card border mb-0 shadow-none">
                    <div class="card-header bg-light p-2 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-0 font-weight-bolder text-dark">Product Pricing & Profit Calculation</h4>
                            <small class="text-muted">Real-time dynamic pricing based on raw materials, declared charges, and profit tiers</small>
                        </div>
                        <span class="badge badge-light-primary font-weight-bold">Dynamic Pricing Active</span>
                    </div>
                    <div class="card-body p-2">
                        {{-- Cost Summary Row --}}
                        <div class="row">
                            <div class="col-md-4 col-12 mb-1">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block font-weight-bold text-uppercase">Total Raw Material Cost</small>
                                    <h4 class="mb-0 text-secondary mt-1 font-weight-bolder">৳ {{ number_format($pricing['material_cost'] ?? $totalMaterialCost, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4 col-12 mb-1">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block font-weight-bold text-uppercase">Total Charges (Cost Setting / Heads)</small>
                                    <h4 class="mb-0 text-secondary mt-1 font-weight-bolder">৳ {{ number_format($pricing['charges_total'] ?? $totalCharges, 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4 col-12 mb-1">
                                <div class="p-2 border rounded bg-primary text-white shadow-sm">
                                    <small class="text-white-50 d-block font-weight-bold text-uppercase">Total Product Cost</small>
                                    <h4 class="mb-0 text-white mt-1 font-weight-bolder">৳ {{ number_format($pricing['cost_price'], 2) }}</h4>
                                </div>
                            </div>
                        </div>

                        {{-- Profit & Price Configuration Row --}}
                        <div class="row mt-2">
                            {{-- Dealer Price --}}
                            <div class="col-md-4 col-12 mb-2">
                                <div class="card border mb-0 h-100 shadow-none">
                                    <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 text-success font-weight-bolder">Dealer Pricing</h5>
                                        <span class="badge {{ $pricing['regular_dealer_type'] === 'fixed' ? 'badge-success' : 'badge-light-success' }} font-weight-bold">
                                            {{ $pricing['regular_dealer_type'] === 'fixed' ? 'Fixed (৳)' : 'Percentage (%)' }}
                                        </span>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="form-group mb-1">
                                            <span class="font-weight-bold text-muted font-small-3 d-block">Configured Profit:</span>
                                            <div class="mt-25">
                                                <h5 class="text-dark font-weight-bold mb-0">
                                                    @if($pricing['regular_dealer_type'] === 'fixed')
                                                        ৳ {{ number_format($pricing['regular_dealer_percent'], 2) }} (Fixed)
                                                    @else
                                                        {{ number_format($pricing['regular_dealer_percent'], 2) }}%
                                                    @endif
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <span class="text-muted font-small-3">Profit Amount:</span>
                                            <span class="font-weight-bold text-dark font-small-3">৳ {{ number_format($pricing['regular_dealer_profit_amount'], 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-1">
                                            <span class="font-weight-bold text-success">Dealer Price:</span>
                                            <h4 class="mb-0 text-success font-weight-bolder">৳ {{ number_format($pricing['regular_dealer_price'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Special Dealer Price --}}
                            <div class="col-md-4 col-12 mb-2">
                                <div class="card border mb-0 h-100 shadow-none">
                                    <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 text-info font-weight-bolder">Special Dealer Pricing</h5>
                                        <span class="badge {{ $pricing['special_dealer_type'] === 'fixed' ? 'badge-info' : 'badge-light-info' }} font-weight-bold">
                                            {{ $pricing['special_dealer_type'] === 'fixed' ? 'Fixed (৳)' : 'Percentage (%)' }}
                                        </span>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="form-group mb-1">
                                            <span class="font-weight-bold text-muted font-small-3 d-block">Configured Profit:</span>
                                            <div class="mt-25">
                                                <h5 class="text-dark font-weight-bold mb-0">
                                                    @if($pricing['special_dealer_type'] === 'fixed')
                                                        ৳ {{ number_format($pricing['special_dealer_percent'], 2) }} (Fixed)
                                                    @else
                                                        {{ number_format($pricing['special_dealer_percent'], 2) }}%
                                                    @endif
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <span class="text-muted font-small-3">Profit Amount:</span>
                                            <span class="font-weight-bold text-dark font-small-3">৳ {{ number_format($pricing['special_dealer_profit_amount'], 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-1">
                                            <span class="font-weight-bold text-info">Special Dealer Price:</span>
                                            <h4 class="mb-0 text-info font-weight-bolder">৳ {{ number_format($pricing['special_dealer_price'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Normal Customer Price --}}
                            <div class="col-md-4 col-12 mb-2">
                                <div class="card border mb-0 h-100 shadow-none">
                                    <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 text-primary font-weight-bolder">Normal Customer Pricing</h5>
                                        <span class="badge {{ $pricing['customer_profit_type'] === 'fixed' ? 'badge-primary' : 'badge-light-primary' }} font-weight-bold">
                                            {{ $pricing['customer_profit_type'] === 'fixed' ? 'Fixed (৳)' : 'Percentage (%)' }}
                                        </span>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="form-group mb-1">
                                            <span class="font-weight-bold text-muted font-small-3 d-block">Configured Profit:</span>
                                            <div class="mt-25">
                                                <h5 class="text-dark font-weight-bold mb-0">
                                                    @if($pricing['customer_profit_type'] === 'fixed')
                                                        ৳ {{ number_format($pricing['customer_profit_percent'], 2) }} (Fixed)
                                                    @else
                                                        {{ number_format($pricing['customer_profit_percent'], 2) }}%
                                                    @endif
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <span class="text-muted font-small-3">Profit Amount:</span>
                                            <span class="font-weight-bold text-dark font-small-3">৳ {{ number_format($pricing['customer_profit_amount'], 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-1">
                                            <span class="font-weight-bold text-primary">Customer Price:</span>
                                            <h4 class="mb-0 text-primary font-weight-bolder">৳ {{ number_format($pricing['regular_price'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($pricing['discount_price'] !== null)
                            <div class="alert alert-warning py-1 px-2 mb-0 mt-1 d-flex justify-content-between align-items-center">
                                <div>
                                    <i data-feather="percent" class="mr-50"></i>
                                    <strong>Active Discount:</strong> {{ $discountLabel }} off normal customer price.
                                </div>
                                <h4 class="mb-0 font-weight-bolder text-warning">Discount Price: ৳ {{ number_format($pricing['discount_price'], 2) }}</h4>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="alert alert-warning mb-0">
                    <i data-feather="alert-circle" class="mr-50"></i>
                    No recipe has been created for this manufacturer product yet. Please click <a href="{{ route('admin.product.edit', $product->id) }}" class="font-weight-bold text-primary">Edit Product</a> to configure its recipe.
                </div>
            @endif
        </div>
    @endif

    {{-- Short Description --}}
    <div class="pd-card pd-block mb-3">
        <h5 class="font-weight-bolder text-dark mb-1">
            <i data-feather="file-text" class="mr-50 text-primary"></i> Short Description
        </h5>
        <div class="pd-description p-1 bg-light rounded">
            {!! $product->short_description ?: '<p class="pd-muted mb-0">No description provided for this product.</p>' !!}
        </div>
    </div>
</div>

{{-- Lightbox Modal for Manufacture Images --}}
@if($product->is_manufacturer && $recipe && count($manufactureImages) > 0)
<div class="modal fade" id="manufactureImageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content shadow">
            <div class="modal-header py-1 bg-light">
                <h5 class="modal-title font-weight-bold text-dark" id="manufactureImageTitle">Manufacture Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center bg-white p-2">
                <img id="manufactureImagePreview" src="" alt="Manufacture image" class="img-fluid rounded border shadow-sm" style="max-height: 70vh; object-fit: contain;">
            </div>
            <div class="modal-footer justify-content-between py-1 bg-light">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="manufactureImagePrev">
                    <i data-feather="chevron-left" class="mr-25"></i> Previous
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="manufactureImageNext">
                    Next <i data-feather="chevron-right" class="ml-25"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
    .pd-page { color: #4b465c; }
    .pd-hero {
        display: flex;
        justify-content: space-between;
        gap: 1.5rem;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        padding: 1.5rem 1.65rem;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(115, 103, 240, 0.12), rgba(40, 199, 111, 0.06) 55%, #fff 100%);
        border: 1px solid #ebe9f1;
        box-shadow: 0 4px 20px rgba(47, 43, 61, 0.05);
    }
    .pd-kicker {
        font-size: 0.76rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #7367f0;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }
    .pd-title {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 800;
        color: #2f2b3d;
        line-height: 1.25;
    }
    .pd-meta {
        margin-top: 0.55rem;
        color: #6e6b7b;
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        align-items: center;
    }
    .pd-model {
        background: #fff;
        border: 1px solid #d9d6e6;
        border-radius: 999px;
        padding: 0.2rem 0.75rem;
        font-weight: 600;
        color: #5e5873;
        font-size: 0.85rem;
    }
    .pd-sep { margin: 0 0.25rem; color: #b9b9c3; }
    .pd-chips { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.9rem; }
    .pd-chip {
        background: #fff;
        border: 1px solid #ebe9f1;
        color: #6e6b7b;
        border-radius: 999px;
        padding: 0.25rem 0.8rem;
        font-size: 0.82rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }
    .pd-chip.is-on { background: #e5f8ed; color: #28c76f; border-color: transparent; }
    .pd-chip.is-off { background: #fceaea; color: #ea5455; border-color: transparent; }
    .pd-chip.is-info { background: #e7f9fc; color: #00a8c0; border-color: transparent; }
    .pd-chip.is-primary { background: #eceaff; color: #7367f0; border-color: transparent; }
    .pd-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
    .pd-card {
        background: #fff;
        border: 1px solid #ebe9f1;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(47, 43, 61, 0.04);
    }
    .pd-gallery { padding: 1.25rem; }
    .pd-stage {
        background: radial-gradient(circle at top, #fff, #f4f3f8 70%);
        border-radius: 14px;
        min-height: 340px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
        border: 1px solid #f0edf7;
    }
    .pd-stage img {
        max-width: 100%;
        max-height: 340px;
        object-fit: contain;
    }
    .pd-thumbs { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.85rem; }
    .pd-thumb {
        width: 68px;
        height: 68px;
        padding: 4px;
        border-radius: 10px;
        border: 2px solid #ebe9f1;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .pd-thumb:hover { border-color: #7367f0; }
    .pd-thumb.is-active { border-color: #7367f0; box-shadow: 0 0 0 2px rgba(115, 103, 240, 0.25); }
    .pd-thumb img { width: 100%; height: 100%; object-fit: cover; border-radius: 6px; }
    .pd-prices {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem;
    }
    .pd-price {
        background: #fff;
        border: 1px solid #ebe9f1;
        border-radius: 14px;
        padding: 0.95rem 1.1rem;
        border-left: 4px solid #b9b9c3;
    }
    .pd-price span { display: block; color: #6e6b7b; font-size: 0.84rem; font-weight: 600; }
    .pd-price em { font-style: normal; color: #7367f0; margin-left: 0.25rem; font-weight: 700; }
    .pd-price strong { display: block; margin-top: 0.25rem; font-size: 1.35rem; color: #2f2b3d; font-weight: 800; }
    .pd-price.is-dealer { border-left-color: #28c76f; background: #f3fbf6; }
    .pd-price.is-special { border-left-color: #00cfe8; background: #f4fcfe; }
    .pd-price.is-regular { border-left-color: #7367f0; background: #f7f6ff; }
    .pd-price.is-regular strong { color: #7367f0; }
    .pd-price.is-discount { border-left-color: #ff9f43; background: #fff8f1; }
    .pd-price.is-discount strong { color: #e07a1a; }
    .pd-price-wide { grid-column: 1 / -1; }
    .pd-empty {
        grid-column: 1 / -1;
        background: #fff;
        border: 1px dashed #d8d6de;
        border-radius: 14px;
        padding: 1.25rem;
        color: #6e6b7b;
    }
    .pd-block { padding: 1.25rem 1.35rem; }
    .pd-muted { color: #a8aaae; margin: 0; }
    .pd-mfg { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.85rem; }
    .pd-mfg button {
        border: 1px solid #ebe9f1;
        background: #fafafa;
        border-radius: 12px;
        padding: 0.5rem;
        text-align: left;
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .pd-mfg button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .pd-mfg img { width: 100%; height: 130px; object-fit: contain; border-radius: 8px; background: #fff; }
    .pd-mfg small { display: block; color: #6e6b7b; margin-top: 0.35rem; font-weight: 600; }
    .pd-description { color: #5e5873; line-height: 1.6; }
    @media (max-width: 991px) {
        .pd-hero { flex-direction: column; }
        .pd-prices, .pd-mfg { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 575px) {
        .pd-prices, .pd-mfg { grid-template-columns: 1fr; }
        .pd-title { font-size: 1.4rem; }
    }
</style>
@endpush

@push('scripts')
<script>
    $(function () {
        if (feather) {
            feather.replace();
        }

        $('.pd-thumb').on('click', function () {
            $('#pdMainImage').attr('src', $(this).data('src'));
            $('.pd-thumb').removeClass('is-active');
            $(this).addClass('is-active');
        });

        var images = [];
        $('.manufacture-image-open').each(function () {
            images.push($(this).data('src'));
        });
        var current = 0;

        function showImage(index) {
            if (!images.length) {
                return;
            }
            current = (index + images.length) % images.length;
            $('#manufactureImagePreview').attr('src', images[current]);
            $('#manufactureImageTitle').text('Manufacture Image ' + (current + 1) + ' of ' + images.length);
        }

        $('.manufacture-image-open').on('click', function () {
            showImage(parseInt($(this).data('index'), 10) || 0);
            $('#manufactureImageModal').modal('show');
        });

        $('#manufactureImagePrev').on('click', function () {
            showImage(current - 1);
        });
        $('#manufactureImageNext').on('click', function () {
            showImage(current + 1);
        });
    });
</script>
@endpush
