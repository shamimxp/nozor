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
                <div class="row mb-4">
                    <div class="col-md-3"><strong>Body Charge:</strong> {{ $recipe->body_charge }}</div>
                    <div class="col-md-3"><strong>Finishing Charge:</strong> {{ $recipe->finishing_charge }}</div>
                    <div class="col-md-3"><strong>Bearing:</strong> {{ $recipe->bearing }}</div>
                    <div class="col-md-3"><strong>Stone:</strong> {{ $recipe->stone }}</div>
                    <div class="col-md-3 mt-2"><strong>Electric Bill:</strong> {{ $recipe->electric_bill }}</div>
                    <div class="col-md-3 mt-2"><strong>Gas Bill:</strong> {{ $recipe->gas_bill }}</div>
                    <div class="col-md-3 mt-2"><strong>Box Price:</strong> {{ $recipe->box_price }}</div>
                    <div class="col-md-3 mt-2"><strong>Carrying Charge:</strong> {{ $recipe->carrying_charge }}</div>
                    <div class="col-md-3 mt-2"><strong>Wire Price:</strong> {{ $recipe->wire_price }}</div>
                </div>

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
