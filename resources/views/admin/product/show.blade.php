@extends('layouts.admin')
@section('title', 'Product Details')
@section('content')
@php
    $discountLabel = 'No Discount';
    if ($product->discount_type == 'percent') {
        $discountLabel = rtrim(rtrim(number_format((float) $product->discount_amount, 2), '0'), '.') . '%';
    } elseif ($product->discount_type == 'amount') {
        $discountLabel = '৳' . number_format($product->discount_amount, 2) . ' fixed';
    }
    $manufactureImages = ($product->is_manufacturer && $product->recipe && is_array($product->recipe->manufacture_images))
        ? $product->recipe->manufacture_images
        : [];
    $percentText = function ($value) {
        return rtrim(rtrim(number_format((float) $value, 2), '0'), '.') . '%';
    };
@endphp
<div class="pd-page">
    <div class="pd-hero">
        <div>
            <div class="pd-kicker">Product details</div>
            <h1 class="pd-title">{{ $product->name }}</h1>
            <div class="pd-meta">
                @if($product->model_no)
                    <span class="pd-model">Model {{ $product->model_no }}</span>
                @endif
                <span>{{ $product->category->name ?? 'N/A' }}@if($product->subCategory) <span class="pd-sep">/</span> {{ $product->subCategory->name }}@endif</span>
            </div>
            <div class="pd-chips">
                <span class="pd-chip {{ $product->status ? 'is-on' : 'is-off' }}">{{ $product->status ? 'Active' : 'Inactive' }}</span>
                <span class="pd-chip {{ $product->is_featured ? 'is-info' : '' }}">Featured {{ $product->is_featured ? 'Yes' : 'No' }}</span>
                <span class="pd-chip {{ $product->is_manufacturer ? 'is-info' : '' }}">Manufacturer {{ $product->is_manufacturer ? 'Yes' : 'No' }}</span>
                <span class="pd-chip {{ $product->dealer_show ? 'is-on' : 'is-off' }}">Dealer {{ $product->dealer_show ? 'Active' : 'Disable' }}</span>
                <span class="pd-chip {{ $product->customer_show ? 'is-on' : 'is-off' }}">Customer {{ $product->customer_show ? 'Active' : 'Disable' }}</span>
            </div>
        </div>
        <div class="pd-actions">
            <a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-primary btn-sm">Edit</a>
            <a href="{{ route('admin.product.duplicate', $product->id) }}" class="btn btn-warning btn-sm">Duplicate</a>
            <a href="{{ route('admin.product.index') }}" class="btn btn-outline-secondary btn-sm">Back to List</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-2">
            <div class="pd-card pd-gallery">
                <div class="pd-stage">
                    <img id="pdMainImage" src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{ $product->name }}">
                </div>
                @if($product->featured_image || $product->gallery->count() > 0)
                    <div class="pd-thumbs">
                        @if($product->featured_image)
                            <button type="button" class="pd-thumb is-active" data-src="{{ asset(config('imagepath.product') . $product->featured_image) }}">
                                <img src="{{ asset(config('imagepath.product') . $product->featured_image) }}" alt="Featured">
                            </button>
                        @endif
                        @foreach($product->gallery as $img)
                            <button type="button" class="pd-thumb" data-src="{{ asset(config('imagepath.product') . $img->image) }}">
                                <img src="{{ asset(config('imagepath.product') . $img->image) }}" alt="Gallery">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-7 mb-2">
            <div class="pd-prices">
                @if($pricing['type'] === 'manufacture')
                    @if($pricing['available'])
                        <div class="pd-price">
                            <span>Cost Price</span>
                            <strong>৳{{ number_format($pricing['cost_price'], 2) }}</strong>
                        </div>
                        <div class="pd-price is-dealer">
                            <span>Regular Dealer Price <em>{{ $percentText($pricing['regular_dealer_percent']) }}</em></span>
                            <strong>৳{{ number_format($pricing['regular_dealer_price'], 2) }}</strong>
                        </div>
                        <div class="pd-price is-special">
                            <span>Special Dealer Price <em>{{ $percentText($pricing['special_dealer_percent']) }}</em></span>
                            <strong>৳{{ number_format($pricing['special_dealer_price'], 2) }}</strong>
                        </div>
                        <div class="pd-price is-regular">
                            <span>Regular Price</span>
                            <strong>৳{{ number_format($pricing['regular_price'], 2) }}</strong>
                        </div>
                        @if($pricing['discount_price'] !== null)
                            <div class="pd-price is-discount pd-price-wide">
                                <span>Discount Price</span>
                                <strong>৳{{ number_format($pricing['discount_price'], 2) }}</strong>
                            </div>
                        @endif
                    @else
                        <div class="pd-empty">No recipe yet, so manufacture prices are not available.</div>
                    @endif
                @else
                    <div class="pd-price">
                        <span>Cost Price</span>
                        <strong>৳{{ number_format($pricing['cost_price'] ?? 0, 2) }}</strong>
                    </div>
                    <div class="pd-price is-regular">
                        <span>Wholesale Price</span>
                        <strong>৳{{ number_format($pricing['wholesale_price'] ?? 0, 2) }}</strong>
                    </div>
                    <div class="pd-price is-discount">
                        <span>Discount Price</span>
                        <strong>{{ $pricing['discount_price'] !== null ? '৳' . number_format($pricing['discount_price'], 2) : 'No Discount' }}</strong>
                    </div>
                @endif
            </div>

            <div class="pd-facts">
                <div class="pd-fact">
                    <span>Unit</span>
                    <strong>{{ $product->unit->name ?? 'N/A' }}</strong>
                </div>
                <div class="pd-fact">
                    <span>Stock</span>
                    <strong class="{{ (float) $product->stock > 0 ? 'is-stock' : 'is-empty' }}">{{ $product->stock }}</strong>
                </div>
                <div class="pd-fact">
                    <span>Discount</span>
                    <strong>{{ $discountLabel }}</strong>
                </div>
                <div class="pd-fact">
                    <span>Category</span>
                    <strong>{{ $product->category->name ?? 'N/A' }}@if($product->subCategory) / {{ $product->subCategory->name }}@endif</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-2">
            <div class="pd-card pd-block">
                <h5>Attributes</h5>
                @if($product->attributes->count() > 0)
                    <div class="pd-tags">
                        @foreach($product->attributes as $attr)
                            <span><b>{{ $attr->attribute->name }}</b> {{ $attr->attribute_value }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="pd-muted">No attributes assigned.</p>
                @endif
            </div>
        </div>
        <div class="col-md-6 mb-2">
            <div class="pd-card pd-block">
                <h5>Variations</h5>
                @if($product->variations && $product->variations->count() > 0)
                    @php $groupedVariations = $product->variations->groupBy('variation_id'); @endphp
                    <div class="pd-tags">
                        @foreach($groupedVariations as $vars)
                            <span><b>{{ $vars->first()->variation->name }}</b> {{ $vars->pluck('variationValue.value')->implode(', ') }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="pd-muted">No variations assigned.</p>
                @endif
            </div>
        </div>
    </div>

    @if($product->is_manufacturer)
        <div class="pd-card pd-block mb-2">
            <h5>Product Recipe</h5>
            @if($product->recipe)
                <div class="pd-charges">
                    <div><span>Body Charge</span><strong>{{ number_format($product->recipe->body_charge, 2) }}</strong></div>
                    <div><span>Finishing Charge</span><strong>{{ number_format($product->recipe->finishing_charge, 2) }}</strong></div>
                    <div><span>Bearing</span><strong>{{ number_format($product->recipe->bearing, 2) }}</strong></div>
                    <div><span>Stone</span><strong>{{ number_format($product->recipe->stone, 2) }}</strong></div>
                    <div><span>Electric Bill</span><strong>{{ number_format($product->recipe->electric_bill, 2) }}</strong></div>
                    <div><span>Gas Bill</span><strong>{{ number_format($product->recipe->gas_bill, 2) }}</strong></div>
                    <div><span>Box Price</span><strong>{{ number_format($product->recipe->box_price, 2) }}</strong></div>
                    <div><span>Carrying Charge</span><strong>{{ number_format($product->recipe->carrying_charge, 2) }}</strong></div>
                    <div><span>Wire Price</span><strong>{{ number_format($product->recipe->wire_price, 2) }}</strong></div>
                </div>

                <div class="pd-subhead">
                    <h6>Manufacture Images</h6>
                    <span>{{ count($manufactureImages) }}</span>
                </div>
                @if(count($manufactureImages) > 0)
                    <div class="pd-mfg">
                        @foreach($manufactureImages as $index => $img)
                            <button type="button" class="manufacture-image-open" data-index="{{ $index }}" data-src="{{ asset($img) }}">
                                <img src="{{ asset($img) }}" alt="Manufacture image {{ $index + 1 }}">
                                <small>Image {{ $index + 1 }}</small>
                            </button>
                        @endforeach
                    </div>
                @else
                    <p class="pd-muted">No manufacture images uploaded.</p>
                @endif

                <div class="table-responsive pd-table-wrap">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Thickness (T)</th>
                                <th>Width (X)</th>
                                <th>Height (Y)</th>
                                <th>Area</th>
                                <th>Grand Value</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->recipe->items as $item)
                                <tr>
                                    <td>{{ $item->rawMaterialProduct->name ?? 'N/A' }}</td>
                                    <td>{{ $item->thickness }}</td>
                                    <td>{{ $item->width }}</td>
                                    <td>{{ $item->height }}</td>
                                    <td>{{ $item->area }}</td>
                                    <td>{{ $item->grade_value }}</td>
                                    <td>{{ $item->qty }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">No recipe items.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                <p class="pd-muted mb-0">No product recipe has been added for this product.</p>
            @endif
        </div>
    @endif

    <div class="pd-card pd-block">
        <h5>Short Description</h5>
        <div class="pd-description">
            {!! $product->short_description ?: '<p class="pd-muted mb-0">No description.</p>' !!}
        </div>
    </div>
</div>

@if($product->is_manufacturer && $product->recipe)
<div class="modal fade" id="manufactureImageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-1">
                <h5 class="modal-title" id="manufactureImageTitle">Manufacture Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center bg-light">
                <img id="manufactureImagePreview" src="" alt="Manufacture image" class="img-fluid rounded" style="max-height: 70vh; object-fit: contain;">
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" id="manufactureImagePrev">Previous</button>
                <button type="button" class="btn btn-outline-secondary" id="manufactureImageNext">Next</button>
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
        gap: 1rem;
        align-items: flex-start;
        margin-bottom: 1.25rem;
        padding: 1.25rem 1.35rem;
        border-radius: 16px;
        background:
            linear-gradient(135deg, rgba(115, 103, 240, 0.14), rgba(40, 199, 111, 0.08) 55%, #fff 100%);
        border: 1px solid #ebe9f1;
    }
    .pd-kicker {
        font-size: 0.75rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #7367f0;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }
    .pd-title {
        margin: 0;
        font-size: 1.65rem;
        font-weight: 700;
        color: #2f2b3d;
        line-height: 1.25;
    }
    .pd-meta {
        margin-top: 0.45rem;
        color: #6e6b7b;
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
        align-items: center;
    }
    .pd-model {
        background: #fff;
        border: 1px solid #d9d6e6;
        border-radius: 999px;
        padding: 0.15rem 0.65rem;
        font-weight: 600;
        color: #5e5873;
    }
    .pd-sep { margin: 0 0.2rem; color: #b9b9c3; }
    .pd-chips { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.8rem; }
    .pd-chip {
        background: #fff;
        border: 1px solid #ebe9f1;
        color: #6e6b7b;
        border-radius: 999px;
        padding: 0.2rem 0.7rem;
        font-size: 0.78rem;
        font-weight: 600;
    }
    .pd-chip.is-on { background: #e5f8ed; color: #28c76f; border-color: transparent; }
    .pd-chip.is-off { background: #fceaea; color: #ea5455; border-color: transparent; }
    .pd-chip.is-info { background: #e7f9fc; color: #00a8c0; border-color: transparent; }
    .pd-actions { display: flex; gap: 0.4rem; flex-wrap: wrap; }
    .pd-card {
        background: #fff;
        border: 1px solid #ebe9f1;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(47, 43, 61, 0.04);
    }
    .pd-gallery { padding: 0.85rem; }
    .pd-stage {
        background:
            radial-gradient(circle at top, #fff, #f4f3f8 70%);
        border-radius: 12px;
        min-height: 320px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .pd-stage img {
        max-width: 100%;
        max-height: 360px;
        object-fit: contain;
    }
    .pd-thumbs { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.75rem; }
    .pd-thumb {
        width: 68px;
        height: 68px;
        padding: 4px;
        border-radius: 10px;
        border: 1px solid #ebe9f1;
        background: #fff;
        cursor: pointer;
    }
    .pd-thumb.is-active { border-color: #7367f0; box-shadow: 0 0 0 2px rgba(115, 103, 240, 0.2); }
    .pd-thumb img { width: 100%; height: 100%; object-fit: cover; border-radius: 7px; }
    .pd-prices {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }
    .pd-price {
        background: #fff;
        border: 1px solid #ebe9f1;
        border-radius: 14px;
        padding: 0.9rem 1rem;
        border-left: 4px solid #b9b9c3;
    }
    .pd-price span { display: block; color: #6e6b7b; font-size: 0.82rem; font-weight: 600; }
    .pd-price em { font-style: normal; color: #7367f0; margin-left: 0.25rem; }
    .pd-price strong { display: block; margin-top: 0.2rem; font-size: 1.35rem; color: #2f2b3d; font-weight: 700; }
    .pd-price.is-dealer { border-left-color: #00cfe8; background: #f4fcfe; }
    .pd-price.is-special { border-left-color: #7367f0; background: #f7f6ff; }
    .pd-price.is-regular { border-left-color: #28c76f; background: #f3fbf6; }
    .pd-price.is-regular strong { color: #1f9d57; font-size: 1.55rem; }
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
    .pd-facts {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.65rem;
        margin-top: 0.75rem;
    }
    .pd-fact {
        background: #fff;
        border: 1px solid #ebe9f1;
        border-radius: 12px;
        padding: 0.7rem 0.75rem;
    }
    .pd-fact span { display: block; font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.04em; color: #a8aaae; font-weight: 700; }
    .pd-fact strong { display: block; margin-top: 0.2rem; color: #2f2b3d; font-size: 0.95rem; }
    .pd-fact .is-stock { color: #28c76f; }
    .pd-fact .is-empty { color: #ff9f43; }
    .pd-block { padding: 1rem 1.1rem 1.1rem; }
    .pd-block h5 { margin: 0 0 0.75rem; font-weight: 700; color: #2f2b3d; }
    .pd-muted { color: #a8aaae; margin: 0; }
    .pd-tags { display: flex; flex-wrap: wrap; gap: 0.45rem; }
    .pd-tags span {
        background: #f8f8f8;
        border-radius: 10px;
        padding: 0.4rem 0.65rem;
        color: #5e5873;
    }
    .pd-tags b { margin-right: 0.25rem; }
    .pd-charges {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.6rem;
        margin-bottom: 1rem;
    }
    .pd-charges div {
        background: #f8f7fc;
        border-radius: 12px;
        padding: 0.7rem 0.8rem;
    }
    .pd-charges span { display: block; color: #6e6b7b; font-size: 0.78rem; }
    .pd-charges strong { color: #2f2b3d; font-size: 1.05rem; }
    .pd-subhead { display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.7rem; }
    .pd-subhead h6 { margin: 0; font-weight: 700; color: #2f2b3d; }
    .pd-subhead span {
        background: #7367f0;
        color: #fff;
        border-radius: 999px;
        min-width: 1.6rem;
        text-align: center;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.1rem 0.45rem;
    }
    .pd-mfg { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.75rem; margin-bottom: 1rem; }
    .pd-mfg button {
        border: 1px solid #ebe9f1;
        background: #fafafa;
        border-radius: 12px;
        padding: 0.4rem;
        text-align: left;
        cursor: pointer;
    }
    .pd-mfg img { width: 100%; height: 140px; object-fit: contain; border-radius: 8px; background: #fff; }
    .pd-mfg small { display: block; color: #6e6b7b; margin-top: 0.25rem; padding-left: 0.15rem; }
    .pd-table-wrap { border: 1px solid #ebe9f1; border-radius: 12px; overflow: hidden; }
    .pd-table-wrap thead th { background: #f8f7fc; color: #5e5873; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.03em; border-top: 0; }
    .pd-table-wrap td, .pd-table-wrap th { vertical-align: middle; }
    .pd-description { color: #5e5873; }
    @media (max-width: 991px) {
        .pd-hero { flex-direction: column; }
        .pd-facts, .pd-charges, .pd-mfg { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 575px) {
        .pd-prices, .pd-facts, .pd-charges, .pd-mfg { grid-template-columns: 1fr; }
        .pd-title { font-size: 1.35rem; }
    }
</style>
@endpush

@push('scripts')
<script>
    $(function () {
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
