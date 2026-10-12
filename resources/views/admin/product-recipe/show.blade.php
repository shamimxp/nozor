@extends('layouts.admin')
@section('title', 'Product Recipe Details')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Product Recipe Details: <span class="text-primary">{{ $recipe->product->name ?? 'N/A' }}</span></h4>
                </div>
                <div>
                    <a href="{{ route('admin.product-recipe.edit', $recipe->id) }}" class="btn btn-primary mr-50">
                        <i data-feather="edit"></i> Edit Recipe
                    </a>
                    <a href="{{ route('admin.product-recipe.index') }}" class="btn btn-secondary">
                        <i data-feather="arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body pt-2">

                {{-- Product & Cost Title Info Header Cards --}}
                <div class="row mb-2">
                    <div class="col-md-6 mb-1">
                        <div class="border rounded p-1 bg-light h-100">
                            <span class="text-muted font-small-2 font-weight-bold text-uppercase d-block">Selected Product</span>
                            <h4 class="font-weight-bolder text-dark mb-0 mt-25">
                                {{ $recipe->product->name ?? 'N/A' }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="border rounded p-1 bg-light h-100 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted font-small-2 font-weight-bold text-uppercase d-block">Cost Title (Cost Setting)</span>
                                <h4 class="font-weight-bolder text-primary mb-0 mt-25">
                                    {{ $recipe->costSetting->cost_title ?? 'Legacy / Unset' }}
                                </h4>
                            </div>
                            @if($recipe->costSetting)
                                <a href="{{ route('admin.cost-setting.show', $recipe->cost_setting_id) }}" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="external-link" class="mr-25"></i> View Setting
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Cost Head Declaration Details (Read-Only Auto Details) --}}
                @if($recipe->costSetting && $recipe->costSetting->items->isNotEmpty())
                    <div class="card border mb-2 shadow-none bg-light">
                        <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 font-weight-bold text-dark">
                                <i data-feather="check-circle" class="text-success mr-50"></i> Cost Head Declaration Details (<span>{{ $recipe->costSetting->cost_title }}</span>)
                                <span class="badge badge-light-secondary ml-50">Read-Only</span>
                            </h5>
                            <div>
                                <span class="text-muted font-small-3 mr-50">Total Declared Charges:</span>
                                <span class="font-weight-bolder text-primary">৳ {{ number_format($pricingSummary['charges_total'], 2) }}</span>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="row">
                                @foreach($recipe->costSetting->items as $headItem)
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-1">
                                        <div class="border rounded p-1 bg-white">
                                            <small class="text-muted font-weight-bold d-block text-truncate" title="{{ $headItem->costHead->title ?? 'N/A' }}">
                                                {{ $headItem->costHead->title ?? 'N/A' }}
                                            </small>
                                            <div class="input-group input-group-sm mt-50">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-light font-weight-bold">৳</span>
                                                </div>
                                                <input type="text" class="form-control bg-light text-right font-weight-bold text-dark" value="{{ number_format($headItem->cost_price, 2) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @elseif($pricingSummary['charges_total'] > 0)
                    {{-- Legacy Charges fallback --}}
                    <div class="card border mb-2 shadow-none bg-light">
                        <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 font-weight-bold text-dark">
                                <i data-feather="file-text" class="mr-50"></i> Fixed Charges Breakdown (Legacy)
                            </h5>
                            <div>
                                <span class="text-muted font-small-3 mr-50">Total Charges:</span>
                                <span class="font-weight-bolder text-primary">৳ {{ number_format($pricingSummary['charges_total'], 2) }}</span>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <div class="row">
                                @php
                                    $legacyFields = [
                                        'Body Charge' => $recipe->body_charge,
                                        'Finishing Charge' => $recipe->finishing_charge,
                                        'Bearing' => $recipe->bearing,
                                        'Stone' => $recipe->stone,
                                        'Electric Bill' => $recipe->electric_bill,
                                        'Gas Bill' => $recipe->gas_bill,
                                        'Box Price' => $recipe->box_price,
                                        'Carrying Charge' => $recipe->carrying_charge,
                                        'Wire Price' => $recipe->wire_price,
                                        'Pitol Price' => $recipe->pitol_price,
                                    ];
                                @endphp
                                @foreach($legacyFields as $fLabel => $fVal)
                                    <div class="col-lg-3 col-md-4 col-sm-6 mb-1">
                                        <div class="border rounded p-1 bg-white">
                                            <small class="text-muted font-weight-bold d-block">{{ $fLabel }}</small>
                                            <div class="input-group input-group-sm mt-50">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-light">৳</span>
                                                </div>
                                                <input type="text" class="form-control bg-light text-right font-weight-bold" value="{{ number_format((float)$fVal, 2) }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Manufacture Images --}}
                @if(is_array($recipe->manufacture_images) && count($recipe->manufacture_images) > 0)
                    <div class="card border mb-2 shadow-none">
                        <div class="card-header bg-light py-1 px-2 border-bottom">
                            <h5 class="card-title mb-0 font-weight-bold text-dark">
                                <i data-feather="image" class="mr-50"></i> Manufacture Images
                                <span class="badge badge-light-primary ml-50">{{ count($recipe->manufacture_images) }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-2 d-flex flex-wrap" style="gap: 15px;">
                            @foreach($recipe->manufacture_images as $img)
                                <a href="{{ asset($img) }}" target="_blank" title="Click to view full image">
                                    <img src="{{ asset($img) }}" alt="manufacture image" width="90" height="90" class="rounded border shadow-sm" style="object-fit: cover;">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Selected Raw Materials Badges --}}
                @php
                    $uniqueMaterials = $recipe->items->map(fn($it) => $it->rawMaterialProduct)->filter()->unique('id');
                @endphp
                @if($uniqueMaterials->isNotEmpty())
                    <div class="card border mb-2 shadow-none">
                        <div class="card-header bg-light py-1 px-2 border-bottom">
                            <h5 class="card-title mb-0 font-weight-bold text-dark">
                                <i data-feather="layers" class="mr-50"></i> Selected Raw Materials
                                <span class="badge badge-light-primary ml-50">{{ $uniqueMaterials->count() }}</span>
                            </h5>
                        </div>
                        <div class="card-body p-2 d-flex flex-wrap" style="gap: 10px;">
                            @foreach($uniqueMaterials as $mat)
                                <div class="badge badge-light-primary p-1 d-flex align-items-center" style="font-size: 0.85rem;">
                                    <i data-feather="box" class="mr-50"></i>
                                    <strong>{{ $mat->name }}</strong>
                                    @if($mat->materialType)
                                        <span class="badge badge-white text-primary ml-50">{{ $mat->materialType->name }}</span>
                                    @endif
                                    @if($mat->unit)
                                        <span class="text-muted ml-50 font-small-2">({{ $mat->unit->name }})</span>
                                    @endif
                                    <span class="badge badge-light-secondary ml-50">৳ {{ number_format($mat->price_per_unit, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Recipe Items Table --}}
                <div class="card border mb-2 shadow-none">
                    <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 font-weight-bold text-dark">
                            <i data-feather="list" class="mr-50"></i> Recipe Items
                            <span class="badge badge-light-primary ml-50">{{ $recipe->items->count() }} items</span>
                        </h5>
                        <div>
                            <span class="text-muted font-small-3 mr-50">Total Raw Material Cost:</span>
                            <span class="font-weight-bolder text-secondary">৳ {{ number_format($pricingSummary['material_cost'], 2) }}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover text-center align-middle mb-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th class="text-left">Material</th>
                                        <th>Thickness (T)</th>
                                        <th>Width (X)</th>
                                        <th>Height (Y)</th>
                                        <th>Area</th>
                                        <th>Grand Value</th>
                                        <th>Qty(Carbide/Iron)</th>
                                        <th>Unit Price</th>
                                        <th>Item Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pricingSummary['items_data'] as $idx => $row)
                                        <tr>
                                            <td class="font-weight-bold text-muted">{{ $idx + 1 }}</td>
                                            <td class="text-left font-weight-bold text-dark">
                                                {{ $row['item']->rawMaterialProduct->name ?? 'N/A' }}
                                                @if(optional($row['item']->rawMaterialProduct->materialType)->name)
                                                    <span class="badge badge-light-secondary font-small-1 ml-50">{{ $row['item']->rawMaterialProduct->materialType->name }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $row['item']->thickness }}</td>
                                            <td>{{ $row['item']->width }}</td>
                                            <td>{{ $row['item']->height }}</td>
                                            <td>{{ $row['item']->area }}</td>
                                            <td>{{ $row['item']->grade_value }}</td>
                                            <td class="font-weight-bold">{{ $row['qty'] }}</td>
                                            <td>৳ {{ number_format($row['unit_price'], 2) }}</td>
                                            <td class="font-weight-bolder text-primary">৳ {{ number_format($row['cost'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="py-2 text-muted">No recipe items defined.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-light font-weight-bold">
                                    <tr>
                                        <td colspan="9" class="text-right">Total Raw Material Cost:</td>
                                        <td class="text-primary font-weight-bolder text-center" style="font-size: 1.05rem;">
                                            ৳ {{ number_format($pricingSummary['material_cost'], 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Product Pricing & Profit Calculation Section (Identical to Edit Page) --}}
                <div class="card border mb-2 shadow-none">
                    <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 font-weight-bold text-dark">
                            <i data-feather="dollar-sign" class="mr-50 text-primary"></i> Product Pricing & Profit Calculation
                        </h4>
                        <span class="badge badge-light-primary">Calculated Breakdown</span>
                    </div>
                    <div class="card-body p-2">
                        <!-- Cost Summary Row -->
                        <div class="row">
                            <div class="col-md-4 col-12 mb-1">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block font-weight-bold text-uppercase">Total Raw Material Cost</small>
                                    <h4 class="mb-0 text-secondary mt-1">৳ {{ number_format($pricingSummary['material_cost'], 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4 col-12 mb-1">
                                <div class="p-2 border rounded bg-light">
                                    <small class="text-muted d-block font-weight-bold text-uppercase">Total Charges (Cost Setting / Heads)</small>
                                    <h4 class="mb-0 text-secondary mt-1">৳ {{ number_format($pricingSummary['charges_total'], 2) }}</h4>
                                </div>
                            </div>
                            <div class="col-md-4 col-12 mb-1">
                                <div class="p-2 border rounded bg-primary text-white">
                                    <small class="text-white-50 d-block font-weight-bold text-uppercase">Total Product Cost</small>
                                    <h4 class="mb-0 text-white mt-1">৳ {{ number_format($pricingSummary['product_cost'], 2) }}</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Profit & Price Configuration Row (3 Cards side by side) -->
                        <div class="row mt-2">
                            <!-- Dealer Price -->
                            <div class="col-md-4 col-12 mb-2">
                                <div class="card border mb-0 h-100 shadow-none">
                                    <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 text-success font-weight-bolder">Dealer Pricing</h5>
                                        <span class="badge badge-light-success">
                                            {{ $pricingSummary['dealer_profit_type'] === 'fixed' ? 'Fixed (৳)' : 'Percentage (%)' }}
                                        </span>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="form-group mb-1">
                                            <label class="font-weight-bold text-muted font-small-3">Dealer Profit ({{ $pricingSummary['dealer_profit_type'] === 'fixed' ? '৳' : '%' }})</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control bg-light font-weight-bold" value="{{ $pricingSummary['dealer_profit_val'] }}" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $pricingSummary['dealer_profit_type'] === 'fixed' ? '৳' : '%' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <span class="text-muted font-small-3">Profit Amount:</span>
                                            <span class="font-weight-bold text-dark font-small-3">৳ {{ number_format($pricingSummary['dealer_profit_amount'], 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-1">
                                            <span class="font-weight-bold text-success">Dealer Price:</span>
                                            <h4 class="mb-0 text-success font-weight-bolder">৳ {{ number_format($pricingSummary['dealer_price'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Special Dealer Price -->
                            <div class="col-md-4 col-12 mb-2">
                                <div class="card border mb-0 h-100 shadow-none">
                                    <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 text-info font-weight-bolder">Special Dealer Pricing</h5>
                                        <span class="badge badge-light-info">
                                            {{ $pricingSummary['special_profit_type'] === 'fixed' ? 'Fixed (৳)' : 'Percentage (%)' }}
                                        </span>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="form-group mb-1">
                                            <label class="font-weight-bold text-muted font-small-3">Special Dealer Profit ({{ $pricingSummary['special_profit_type'] === 'fixed' ? '৳' : '%' }})</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control bg-light font-weight-bold" value="{{ $pricingSummary['special_profit_val'] }}" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $pricingSummary['special_profit_type'] === 'fixed' ? '৳' : '%' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <span class="text-muted font-small-3">Profit Amount:</span>
                                            <span class="font-weight-bold text-dark font-small-3">৳ {{ number_format($pricingSummary['special_profit_amount'], 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-1">
                                            <span class="font-weight-bold text-info">Special Dealer Price:</span>
                                            <h4 class="mb-0 text-info font-weight-bolder">৳ {{ number_format($pricingSummary['special_dealer_price'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Normal Customer Price -->
                            <div class="col-md-4 col-12 mb-2">
                                <div class="card border mb-0 h-100 shadow-none">
                                    <div class="card-header bg-light py-1 px-2 border-bottom d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0 text-primary font-weight-bolder">Normal Customer Pricing</h5>
                                        <span class="badge badge-light-primary">
                                            {{ $pricingSummary['customer_profit_type'] === 'fixed' ? 'Fixed (৳)' : 'Percentage (%)' }}
                                        </span>
                                    </div>
                                    <div class="card-body p-2">
                                        <div class="form-group mb-1">
                                            <label class="font-weight-bold text-muted font-small-3">Customer Profit ({{ $pricingSummary['customer_profit_type'] === 'fixed' ? '৳' : '%' }})</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control bg-light font-weight-bold" value="{{ $pricingSummary['customer_profit_val'] }}" readonly>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">{{ $pricingSummary['customer_profit_type'] === 'fixed' ? '৳' : '%' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <span class="text-muted font-small-3">Profit Amount:</span>
                                            <span class="font-weight-bold text-dark font-small-3">৳ {{ number_format($pricingSummary['customer_profit_amount'], 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center pt-1">
                                            <span class="font-weight-bold text-primary">Customer Price:</span>
                                            <h4 class="mb-0 text-primary font-weight-bolder">৳ {{ number_format($pricingSummary['customer_price'], 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info py-1 px-2 mb-0 mt-1" role="alert">
                            <small>Prices and profits are calculated based on raw material costs and cost setting declaration charges.</small>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons Footer --}}
                <div class="d-flex justify-content-end mt-2" style="gap: 10px;">
                    <a href="{{ route('admin.product-recipe.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="mr-25"></i> Back to List
                    </a>
                    <a href="{{ route('admin.product-recipe.edit', $recipe->id) }}" class="btn btn-primary">
                        <i data-feather="edit" class="mr-25"></i> Edit Recipe
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if (typeof feather !== 'undefined') {
            feather.replace({
                width: 14,
                height: 14
            });
        }
    });
</script>
@endpush
