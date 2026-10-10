@extends('layouts.admin')
@section('title', 'Product Recipe Details')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Product Recipe Details: {{ $recipe->product->name ?? 'N/A' }}</h4>
                <a href="{{ route('admin.product-recipe.index') }}" class="btn btn-secondary"><i data-feather="arrow-left"></i> Back to List</a>
            </div>
            <div class="card-body mt-3">
                <div class="row mb-3">
                    @if($recipe->costSetting)
                        <div class="col-md-12 mb-2">
                            <div class="alert alert-light border p-2">
                                <span class="text-muted font-small-3 d-block">Declared Cost Title:</span>
                                <h4 class="text-primary font-weight-bold mb-1">{{ $recipe->costSetting->cost_title }}</h4>
                                <div class="row">
                                    @foreach($recipe->costSetting->items as $headItem)
                                        <div class="col-md-3 col-sm-6 mb-1">
                                            <div class="border rounded p-1 bg-white">
                                                <small class="text-muted d-block">{{ $headItem->costHead->title ?? 'N/A' }}:</small>
                                                <span class="font-weight-bold text-dark">৳ {{ number_format($headItem->cost_price, 2) }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-md-3"><strong>Body Charge:</strong> {{ $recipe->body_charge }}</div>
                        <div class="col-md-3"><strong>Finishing Charge:</strong> {{ $recipe->finishing_charge }}</div>
                        <div class="col-md-3"><strong>Bearing:</strong> {{ $recipe->bearing }}</div>
                        <div class="col-md-3"><strong>Stone:</strong> {{ $recipe->stone }}</div>
                        <div class="col-md-3 mt-2"><strong>Electric Bill:</strong> {{ $recipe->electric_bill }}</div>
                        <div class="col-md-3 mt-2"><strong>Gas Bill:</strong> {{ $recipe->gas_bill }}</div>
                        <div class="col-md-3 mt-2"><strong>Box Price:</strong> {{ $recipe->box_price }}</div>
                        <div class="col-md-3 mt-2"><strong>Carrying Charge:</strong> {{ $recipe->carrying_charge }}</div>
                        <div class="col-md-3 mt-2"><strong>Wire Price:</strong> {{ $recipe->wire_price }}</div>
                        <div class="col-md-3 mt-2"><strong>Pitol Price:</strong> {{ $recipe->pitol_price }}</div>
                    @endif

                    <div class="col-md-4 mt-2">
                        <div class="border rounded p-1 bg-light">
                            <span class="text-success font-weight-bold d-block">Dealer Pricing</span>
                            <small class="text-muted d-block">Profit: {{ $recipe->dealer_profit_percent }} {{ ($recipe->dealer_profit_type ?? 'percentage') === 'fixed' ? '৳ (Fixed)' : '%' }}</small>
                            <h4 class="text-success font-weight-bolder mb-0 mt-25">
                                ৳ {{ number_format($pricing['regular_dealer_price'] ?? 0, 2) }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="border rounded p-1 bg-light">
                            <span class="text-info font-weight-bold d-block">Special Dealer Pricing</span>
                            <small class="text-muted d-block">Profit: {{ $recipe->special_dealer_profit_percent }} {{ ($recipe->special_dealer_profit_type ?? 'percentage') === 'fixed' ? '৳ (Fixed)' : '%' }}</small>
                            <h4 class="text-info font-weight-bolder mb-0 mt-25">
                                ৳ {{ number_format($pricing['special_dealer_price'] ?? 0, 2) }}
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-4 mt-2">
                        <div class="border rounded p-1 bg-light">
                            <span class="text-primary font-weight-bold d-block">Normal Customer Pricing</span>
                            <small class="text-muted d-block">Profit: {{ $recipe->normal_customer_profit_percent }} {{ ($recipe->normal_customer_profit_type ?? 'percentage') === 'fixed' ? '৳ (Fixed)' : '%' }}</small>
                            <h4 class="text-primary font-weight-bolder mb-0 mt-25">
                                ৳ {{ number_format($pricing['regular_price'] ?? 0, 2) }}
                            </h4>
                        </div>
                    </div>
                </div>

                @if(is_array($recipe->manufacture_images) && count($recipe->manufacture_images) > 0)
                <div class="card border shadow-none mb-4">
                    <div class="card-header bg-light p-2 border-bottom">
                        <h4 class="card-title mb-0">Manufacture Images</h4>
                    </div>
                    <div class="card-body p-2 d-flex flex-wrap" style="gap: 10px;">
                        @foreach($recipe->manufacture_images as $img)
                            <a href="{{ asset($img) }}" target="_blank">
                                <img src="{{ asset($img) }}" alt="image" width="100" height="100" class="rounded border" style="object-fit: cover;">
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="card border shadow-none">
                    <div class="card-header bg-light p-2 border-bottom">
                        <h4 class="card-title mb-0">Recipe Items</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover text-center align-middle mb-0">
                                <thead class="table-secondary">
                                    <tr>
                                        <th>Material</th>
                                        <th>Thickness (T)</th>
                                        <th>Width (X)</th>
                                        <th>Height (Y)</th>
                                        <th>Area</th>
                                        <th>Grand Value</th>
                                        <th>Qty(Carbide/Iron)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recipe->items as $item)
                                        <tr>
                                            <td>{{ $item->rawMaterialProduct->name ?? 'N/A' }}</td>
                                            <td>{{ $item->thickness }}</td>
                                            <td>{{ $item->width }}</td>
                                            <td>{{ $item->height }}</td>
                                            <td>{{ $item->area }}</td>
                                            <td>{{ $item->grade_value }}</td>
                                            <td>{{ $item->qty }}</td>
                                        </tr>
                                    @endforeach
                                    @if($recipe->items->isEmpty())
                                        <tr>
                                            <td colspan="7">No items found for this recipe.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
