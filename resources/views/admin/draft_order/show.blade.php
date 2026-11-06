@extends('layouts.admin')
@section('title', 'Draft Order Details - ' . $draftOrder->draft_number)

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Draft Order Details</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.draft-orders.index') }}">Draft Orders</a></li>
        <li class="breadcrumb-item active">{{ $draftOrder->draft_number }}</li>
    </ol>
</div>
@endsection

@section('content-header-right')
<a href="{{ route('admin.draft-orders.index') }}" class="btn btn-outline-secondary">
    <i data-feather="arrow-left" class="mr-50"></i> Back to List
</a>
@endsection

@section('content')
    <section class="invoice-preview-wrapper">
        <div class="row invoice-preview">
            <!-- Action Bar -->
            <div class="col-12 mb-2">
                <div class="card shadow-sm border-0">
                    <div class="card-body d-flex flex-wrap align-items-center" style="gap: 10px;">
                        <a href="{{ route('admin.draft-orders.export-pdf', $draftOrder->id) }}" class="btn btn-secondary shadow-sm">
                            <i data-feather="download" class="mr-50"></i> Download PDF
                        </a>
                        <a href="{{ route('admin.draft-orders.print', $draftOrder->id) }}" target="_blank" class="btn btn-outline-secondary shadow-sm">
                            <i data-feather="printer" class="mr-50"></i> Print Draft
                        </a>

                        @if($draftOrder->status === 'draft')
                            <a href="{{ route('admin.draft-orders.edit', $draftOrder->id) }}" class="btn btn-primary shadow-sm">
                                <i data-feather="edit" class="mr-50"></i> Edit Draft
                            </a>
                            <button type="button" class="btn btn-success shadow-sm" id="btnConvertOrder">
                                <i data-feather="check-circle" class="mr-50"></i> Create Order
                            </button>
                            <button type="button" class="btn btn-outline-danger shadow-sm ml-auto" id="btnDeleteDraft">
                                <i data-feather="trash" class="mr-50"></i> Delete Draft
                            </button>
                        @elseif($draftOrder->status === 'converted' && $draftOrder->dealer_order_id)
                            <a href="{{ route('admin.dealer-order.show', $draftOrder->dealer_order_id) }}" class="btn btn-outline-success shadow-sm">
                                <i data-feather="external-link" class="mr-50"></i> View Dealer Order ({{ $draftOrder->dealerOrder->order_number ?? '' }})
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Draft Order Card -->
            <div class="col-12">
                <div class="card shadow-sm border-0 invoice-preview-card">
                    <div class="card-body">
                        <!-- Header meta -->
                        <div class="d-flex justify-content-between flex-md-row flex-column pb-2 border-bottom">
                            <div>
                                <h3 class="text-primary font-weight-bolder mb-1">
                                    <i data-feather="file-text" class="mr-50"></i> DRAFT ORDER
                                </h3>
                                <p class="mb-50 text-dark"><strong>Draft Number:</strong> <span class="badge badge-light-primary" style="font-size:0.9rem;">{{ $draftOrder->draft_number }}</span></p>
                                @if($draftOrder->orderRequest)
                                    <p class="mb-0 text-muted">
                                        <strong>Source Order Request:</strong>
                                        <a href="{{ route('admin.order-requests.show', $draftOrder->orderRequest->id) }}" class="badge badge-light-info font-weight-bold ml-25">
                                            {{ $draftOrder->orderRequest->request_number ?? ('#' . $draftOrder->orderRequest->id) }}
                                        </a>
                                    </p>
                                @endif
                            </div>
                            <div class="mt-md-0 mt-2 text-md-right">
                                <p class="mb-50">
                                    <strong>Status:</strong>
                                    @if($draftOrder->status === 'converted')
                                        <span class="badge badge-success font-weight-bold">Converted to Order</span>
                                    @elseif($draftOrder->status === 'cancelled')
                                        <span class="badge badge-danger font-weight-bold">Cancelled</span>
                                    @else
                                        <span class="badge badge-warning font-weight-bold">Draft</span>
                                    @endif
                                </p>
                                <p class="mb-50"><strong>Date Created:</strong> {{ $draftOrder->created_at->format('d M, Y h:i A') }}</p>
                                @if($draftOrder->creator)
                                    <p class="mb-0 text-muted small"><strong>Created By:</strong> {{ $draftOrder->creator->name }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Dealer Info -->
                        <div class="row py-2 border-bottom">
                            <div class="col-md-6 col-12">
                                <h6 class="text-uppercase text-muted font-weight-bold mb-1" style="font-size:0.8rem; letter-spacing:0.5px;">Dealer Details</h6>
                                <p class="mb-25 font-weight-bolder text-dark" style="font-size:1.05rem;">{{ $draftOrder->dealer->shop_name ?? 'N/A' }}</p>
                                <p class="mb-25"><strong>Contact Person:</strong> {{ $draftOrder->dealer->name ?? 'N/A' }}</p>
                                <p class="mb-25"><strong>Phone:</strong> {{ $draftOrder->dealer->phone ?? 'N/A' }}</p>
                                <p class="mb-0"><strong>Email:</strong> {{ $draftOrder->dealer->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 col-12 mt-md-0 mt-1 border-left-md">
                                <h6 class="text-uppercase text-muted font-weight-bold mb-1" style="font-size:0.8rem; letter-spacing:0.5px;">Delivery & Address</h6>
                                <p class="mb-25"><strong>Address:</strong> {{ $draftOrder->dealer->address ?? 'N/A' }}</p>
                                @if($draftOrder->orderRequest && $draftOrder->orderRequest->created_at)
                                    <p class="mb-0"><strong>Original Request Date:</strong> {{ $draftOrder->orderRequest->created_at->format('d M, Y') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive my-2">
                            <table class="table table-hover mb-0" id="draftItemsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th style="width: 80px;">Product Image</th>
                                        <th style="width: 80px;">Manufacture Image</th>
                                        <th>Product Name</th>
                                        <th class="text-right">Unit Price</th>
                                        <th class="text-center">Quantity</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($draftOrder->items as $index => $item)
                                        @php
                                            $prodImg = ($item->product && $item->product->featured_image) ? asset(config('imagepath.product') . $item->product->featured_image) : null;
                                            $mfgImg = null;
                                            if ($item->product && $item->product->is_manufacturer == 1 && $item->product->recipe) {
                                                $mImages = $item->product->recipe->manufacture_images;
                                                if (is_string($mImages)) {
                                                    $mImages = json_decode($mImages, true);
                                                }
                                                if (is_array($mImages) && count($mImages) > 0 && !empty($mImages[0])) {
                                                    $mfgImg = asset($mImages[0]);
                                                }
                                            }
                                        @endphp
                                        <tr id="row-item-{{ $item->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($prodImg)
                                                    <img src="{{ $prodImg }}" width="42" height="42" 
                                                        class="img-preview-trigger" 
                                                        data-preview-url="{{ $prodImg }}"
                                                        data-preview-title="{{ $item->product->name ?? 'Product' }} (Product Image)"
                                                        title="Click to zoom image"
                                                        style="border-radius:6px;object-fit:cover;border:1px solid #e5e7eb;" 
                                                        onerror="this.src='{{ asset('images/no-image.png') }}'">
                                                @else
                                                    <img src="{{ asset('images/no-image.png') }}" width="42" height="42" style="border-radius:6px;object-fit:cover;">
                                                @endif
                                            </td>
                                            <td>
                                                @if($mfgImg)
                                                    <img src="{{ $mfgImg }}" width="42" height="42" 
                                                        class="img-preview-trigger" 
                                                        data-preview-url="{{ $mfgImg }}"
                                                        data-preview-title="{{ $item->product->name ?? 'Product' }} (Manufacture Image)"
                                                        title="Click to zoom image"
                                                        style="border-radius:6px;object-fit:cover;border:1px solid #e5e7eb;" 
                                                        onerror="this.src='{{ asset('images/no-image.png') }}'">
                                                @else
                                                    <span class="badge badge-light-secondary font-weight-normal" style="font-size:0.75rem;">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="font-weight-bold text-dark">{{ $item->product->name ?? 'N/A' }}</div>
                                                @if($item->note)
                                                    <small class="text-muted">{{ $item->note }}</small>
                                                @endif
                                            </td>
                                            <td class="text-right font-weight-bold">৳{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="text-center font-weight-bold">{{ $item->qty }}</td>
                                            <td class="text-right font-weight-bolder text-primary">৳{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-3 text-muted">No products found in this draft order.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Notes and Financial Summary -->
                        <div class="row mt-2 pt-2 border-top">
                            <div class="col-md-7 col-12">
                                @if($draftOrder->note)
                                    <div class="mb-2">
                                        <label class="font-weight-bold text-muted small text-uppercase">Customer Note</label>
                                        <div class="p-1 bg-light rounded text-dark">{{ $draftOrder->note }}</div>
                                    </div>
                                @endif
                                @if($draftOrder->admin_notes)
                                    <div>
                                        <label class="font-weight-bold text-muted small text-uppercase">Admin Note</label>
                                        <div class="p-1 bg-light rounded text-dark">{{ $draftOrder->admin_notes }}</div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-5 col-12">
                                <div class="card bg-light border-0">
                                    <div class="card-body p-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Sub Total:</span>
                                            <span class="font-weight-bold" id="displaySubTotal">৳{{ number_format($draftOrder->sub_total, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Discount:</span>
                                            <span class="font-weight-bold text-danger">- ৳{{ number_format($draftOrder->discount, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Promotional Discount:</span>
                                            <span class="font-weight-bold text-danger">- ৳{{ number_format($draftOrder->promotional_discount ?? 0, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Carrying Charge:</span>
                                            <span class="font-weight-bold">+ ৳{{ number_format($draftOrder->carrying_charge, 2) }}</span>
                                        </div>
                                        <hr class="my-1">
                                        <div class="d-flex justify-content-between">
                                            <span class="font-weight-bolder text-dark" style="font-size:1.1rem;">Grand Total:</span>
                                            <span class="font-weight-bolder text-primary" id="displayGrandTotal" style="font-size:1.2rem;">৳{{ number_format($draftOrder->grand_total, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

@include('admin.partials.image-preview-modal')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Delete single product from draft & restore to OrderRequest
    $('.btnDeleteItem').on('click', function() {
        let itemId = $(this).data('id');
        let prodName = $(this).data('product');
        let qty = $(this).data('qty');

        Swal.fire({
            title: 'Remove Product?',
            text: 'Remove ' + prodName + ' (' + qty + ' pcs) from this draft? The quantity will be automatically returned to the original Order Request.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea5455',
            cancelButtonColor: '#6e7d88',
            confirmButtonText: 'Yes, Remove & Restore!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/admin/draft-orders/{{ $draftOrder->id }}/items/" + itemId,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function() {
                                window.location.reload();
                            }, 800);
                        } else {
                            toastr.error(response.message || 'Failed to remove product.');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Error removing product.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        toastr.error(msg);
                    }
                });
            }
        });
    });

    // Soft-delete Entire Draft Order
    $('#btnDeleteDraft').on('click', function() {
        Swal.fire({
            title: 'Move to trash?',
            text: 'You can restore this later from Show Deleted Data.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-primary',
                cancelButton: 'btn btn-outline-danger ml-1'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.draft-orders.destroy', $draftOrder->id) }}",
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message || data.success,
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = "{{ route('admin.draft-orders.index') }}";
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Cannot delete',
                            text: xhr.responseJSON?.message || 'Something went wrong!',
                            customClass: { confirmButton: 'btn btn-danger' }
                        });
                    }
                });
            }
        });
    });

    // Convert Draft Order to Dealer Order
    $('#btnConvertOrder').on('click', function() {
        Swal.fire({
            title: 'Convert to Dealer Order?',
            text: 'Create a confirmed Dealer Order with current items and quantities from this draft?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28c76f',
            cancelButtonColor: '#6e7d88',
            confirmButtonText: 'Yes, Convert to Order!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Converting...',
                    text: 'Please wait while the order is created.',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });

                $.ajax({
                    url: "{{ route('admin.draft-orders.convert', $draftOrder->id) }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Converted!',
                                text: response.message || 'Dealer order created successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                if (response.redirect_url) {
                                    window.location.href = response.redirect_url;
                                } else {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire('Error!', response.message || 'Could not convert order.', 'error');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Failed to convert order.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire('Error!', msg, 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush
