@extends('frontend.layouts.app')
@section('content')
    <div class="page-header breadcrumb-wrap">
        <div class="container">
            <div class="breadcrumb">
                <a href="{{Url('/')}}" rel="nofollow"><i class="fi-rs-home mr-5"></i>Home</a>
                <span></span> Shop <span></span> Wishlist
            </div>
        </div>
    </div>
    <div class="container mb-30 mt-10">
        <div class="row">
            <div class="col-xl-10 col-lg-12 m-auto">
                <div class="mb-1">
                    <h6 class="text-body">There are <span class="text-brand wishlist-page-count">{{ $wishlists->count() }}</span> products in your wishlist</h6>
                </div>
                <div class="table-responsive shopping-summery">
                    <table class="table table-wishlist">
                        <thead>
                        <tr class="main-heading">
                            <th scope="col" colspan="2" class="start pl-30">Product</th>
                            <th scope="col">Price</th>
                            <th scope="col">Action</th>
                            <th scope="col" class="end">Remove</th>
                        </tr>
                        </thead>
                        <tbody id="wishlist-table-body">
                        @forelse($wishlists as $wishlist)
                        @if($wishlist->product)
                        @php
                            $product = $wishlist->product;
                            $price = $product->selling_price ?? 0;
                            if ($product->discount_type == 'amount') {
                                $finalPrice = $price - ($product->discount_amount ?? 0);
                            } elseif ($product->discount_type == 'percent') {
                                $finalPrice = $price - ($price * ($product->discount_amount ?? 0) / 100);
                            } else {
                                $finalPrice = $price;
                            }
                        @endphp
                        <tr class="pt-30" id="wishlist-row-{{ $wishlist->id }}">
                            <td class="image product-thumbnail pt-40 pl-30">
                                <a href="{{ route('product.details', encrypt($product->id)) }}">
                                    <img style="max-width: 80px!important;" src="{{ $product->featured_image ? asset(config('imagepath.product') . $product->featured_image) : asset('images/no-image.png') }}" alt="{{ $product->name }}" />
                                </a>
                            </td>
                            <td class="product-des product-name" style="max-width: 150px;">
                                <h6 class="mb-5"><a class="product-name mb-10 text-heading" href="{{ route('product.details', encrypt($product->id)) }}" style="display: block; white-space: normal; word-wrap: break-word;">{{ $product->name }}</a></h6>
                            </td>
                            <td class="price" data-title="Price">
                                <h4 class="text-brand" style="white-space: nowrap;">৳{{ number_format($finalPrice, 2) }}</h4>
                                @if($finalPrice < $price)
                                <span class="old-price" style="text-decoration: line-through; color: #999;">৳{{ number_format($price, 2) }}</span>
                                @endif
                            </td>
                            <td class="text-center detail-info" data-title="Action">
                                <a href="javascript:void(0)" class="btn btn-sm add-to-cart-btn" data-id="{{ $product->id }}"><i class="fi-rs-shopping-cart mr-5"></i>Add to cart</a>
                            </td>
                            <td class="action text-center" data-title="Remove">
                                <a href="javascript:void(0)" class="text-body remove-wishlist-item" data-id="{{ $wishlist->id }}"><i class="fi-rs-trash text-danger"></i></a>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr id="empty-wishlist-row">
                            <td colspan="5" class="text-center py-4">
                                <p class="text-muted">Your wishlist is empty.</p>
                                <a href="{{ route('shop') }}" class="btn btn-sm">Continue Shopping</a>
                            </td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).on('click', '.remove-wishlist-item', function(e) {
        e.preventDefault();
        var btn = $(this);
        var wishlistId = btn.data('id');

        $.ajax({
            url: "{{ route('wishlist.remove') }}",
            type: "POST",
            data: {
                wishlist_id: wishlistId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.status === 'success') {
                    toastr.success(response.message);
                    $('#wishlist-row-' + wishlistId).fadeOut(300, function() {
                        $(this).remove();
                        $('.wishlist-page-count').text(response.wishlist_count);
                        $('.wishlist-count').text(response.wishlist_count);
                        if($('#wishlist-table-body tr').length === 0) {
                            $('#wishlist-table-body').html('<tr id="empty-wishlist-row"><td colspan="5" class="text-center py-4"><p class="text-muted">Your wishlist is empty.</p><a href="{{ route("shop") }}" class="btn btn-sm">Continue Shopping</a></td></tr>');
                        }
                    });
                } else {
                    toastr.error(response.message);
                }
            }
        });
    });
</script>
@endpush
