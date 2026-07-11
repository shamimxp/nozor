@forelse($products as $product)
    <div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-2 px-1">
        <div class="card border text-center shadow-none product-card {{ $product->is_out_of_stock ? 'stock__out' : '' }}" 
             style="border-radius: 12px; transition: 0.3s; cursor:pointer; position: relative;"
             data-id="{{ $product->id }}" 
             data-title="{{ $product->name }}" 
             data-price="{{ $product->pos_price }}" 
             data-stock="{{ $product->is_manufacturer ? 999999 : $product->stock }}"
             data-image="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : '' }}"
             onclick="{{ $product->is_out_of_stock ? '' : 'addToCart(this)' }}">
             
            @if($product->is_out_of_stock)
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.6); z-index: 8; border-radius: 12px; pointer-events: none;"></div>
                <div style="position: absolute; top: 50%; left: 0; width: 100%; transform: translateY(-50%); background-color: #f5365c; color: #ffffff !important; padding: 10px 0; font-size: 14px; font-weight: 800; text-transform: uppercase; text-align: center; z-index: 10; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); letter-spacing: 1.5px; pointer-events: none;">Stock Out</div>
            @endif

            <div class="card-body p-1">
                <div class="mb-50" style="height: 100px; background:#f8f9fa; border-radius: 8px; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                    @if($product->featured_image)
                        <img src="{{ asset(config('imagepath.product') . $product->featured_image) }}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                    @else
                        <i data-feather="image" style="width: 24px; height: 24px; opacity:0.3" class="mb-25"></i>
                        <p class="mb-0 font-weight-bolder" style="font-size: 0.8rem; opacity:0.5">NO IMAGE</p>
                    @endif
                </div>
                <h6 class="font-weight-bolder text-dark mb-25" style="font-size: 0.75rem; height: 32px; overflow: hidden; line-height: 1.1;">{{ $product->name }}</h6>
                <div class="d-flex justify-content-center align-items-center mt-50">
                    @if($product->pos_original_price && $product->pos_original_price > $product->pos_price)
                        <del class="text-muted mr-25" style="font-size: 0.65rem;">৳{{ $product->pos_original_price }}</del>
                    @endif
                    <span class="font-weight-bolder" style="font-size: 0.8rem; color: #0F172A;">৳{{ $product->pos_price }}</span>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-12 text-center py-5">
        <h5 class="text-muted">No products found</h5>
    </div>
@endforelse
