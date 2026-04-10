@extends('layouts.admin')
@section('title', 'Product Details')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title">Product Details: {{ $product->name }}</h4>
                    <a href="{{ route('admin.product.index') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="row">
                    <div class="col-md-5">
                        <div class="text-center mb-2">
                            <img src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" class="img-fluid rounded border" style="max-height: 400px;" alt="">
                        </div>
                        @if($product->gallery->count() > 0)
                        <div class="d-flex flex-wrap justify-content-center">
                            @foreach($product->gallery as $img)
                            <img src="{{ asset(config('imagepath.product') . $img->image) }}" width="80" class="img-thumbnail m-1" alt="">
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div class="col-md-7">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Name</th>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <th>Category</th>
                                <td>{{ $product->category->name ?? 'N/A' }} > {{ $product->subCategory->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Unit</th>
                                <td>{{ $product->unit->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Selling Price</th>
                                <td><span class="badge badge-light-success font-medium-3">৳{{ $product->selling_price }}</span></td>
                            </tr>
                            <tr>
                                <th>Cost Price</th>
                                <td>৳{{ $product->cost_price }}</td>
                            </tr>
                            <tr>
                                <th>Stock</th>
                                <td>{{ $product->stock }}</td>
                            </tr>
                            <tr>
                                <th>Discount</th>
                                <td>
                                    @if($product->discount_type == 'percent')
                                        {{ $product->discount_amount }}%
                                    @elseif($product->discount_type == 'amount')
                                        {{ $product->discount_amount }} (Fixed)
                                    @else
                                        No Discount
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge badge-{{ $product->status ? 'success' : 'danger' }}">
                                        {{ $product->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Featured</th>
                                <td>
                                    <span class="badge badge-{{ $product->is_featured ? 'info' : 'secondary' }}">
                                        {{ $product->is_featured ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                        
                        <div class="mt-2">
                            <h5>Attributes</h5>
                            @if($product->attributes->count() > 0)
                            <ul>
                                @foreach($product->attributes as $attr)
                                <li><strong>{{ $attr->attribute->name }}:</strong> {{ $attr->attribute_value }}</li>
                                @endforeach
                            </ul>
                            @else
                            <p class="text-muted">No attributes assigned.</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <h5>Short Description</h5>
                        <div class="border p-2 rounded bg-light">
                            {!! $product->short_description !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
