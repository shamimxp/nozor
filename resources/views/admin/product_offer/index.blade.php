@extends('layouts.admin')
@section('title', 'Product Offers')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Promotional Product Offers</h4>
                <div class="d-flex align-items-center" style="gap:.5rem;">
                    @include('admin.partials.show_deleted_button', [
                        'route' => route('admin.product-offer.trash'),
                        'permission' => 'product_offer.deleted-list',
                    ])
                    @can('product_offer.create')
                        <a href="{{ route('admin.product-offer.create') }}" class="btn btn-primary btn-sm">
                            <i data-feather="plus" style="width:14px;height:14px;"></i>
                            Add Offer
                        </a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="productOfferTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Product</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Qty Range</th>
                                <th>Offer Type</th>
                                <th>Discount</th>
                                <th>Created By</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($offers as $key => $offer)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $offer->product->name ?? '—' }}</td>
                                    <td>{{ optional($offer->from_date)->format('d M Y') }}</td>
                                    <td>{{ optional($offer->to_date)->format('d M Y') }}</td>
                                    <td>{{ (int) $offer->from_qty }} – {{ (int) $offer->to_qty }}</td>
                                    <td>{{ $offer->offer_type === 'percent' ? 'Percentage' : 'Fixed' }}</td>
                                    <td>
                                        @if($offer->offer_type === 'percent')
                                            {{ rtrim(rtrim(number_format((float) $offer->discount_value, 2, '.', ''), '0'), '.') }}%
                                        @else
                                            {{ number_format((float) $offer->discount_value, 2) }}
                                        @endif
                                    </td>
                                    <td>{{ $offer->createdBy->name ?? '—' }}</td>
                                    <td>{{ optional($offer->created_at)->format('d M Y h:i A') }}</td>
                                    <td>
                                        <div class="d-flex align-items-center flex-wrap" style="gap:.5rem;">
                                            @can('product_offer.edit')
                                                <div class="custom-control custom-switch custom-switch-primary mb-0">
                                                    <input type="checkbox"
                                                           class="custom-control-input changeStatus"
                                                           data-id="{{ $offer->id }}"
                                                           id="status_{{ $offer->id }}"
                                                           {{ $offer->status == 1 ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="status_{{ $offer->id }}">
                                                        <span class="switch-icon-left"><i data-feather="check"></i></span>
                                                        <span class="switch-icon-right"><i data-feather="x"></i></span>
                                                    </label>
                                                </div>
                                                <a href="{{ route('admin.product-offer.edit', $offer->id) }}" class="btn btn-info btn-sm">Edit</a>
                                            @endcan
                                            @can('product_offer.delete')
                                                <button type="button" class="btn btn-danger btn-sm deleteOffer" data-id="{{ $offer->id }}">Delete</button>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('#productOfferTable').DataTable();

        if (window.feather) {
            feather.replace({ width: 14, height: 14 });
        }

        $('body').on('change', '.changeStatus', function () {
            var $el = $(this);
            var id = $el.data('id');
            var status = $el.prop('checked') ? 1 : 0;
            $.ajax({
                type: 'POST',
                url: "{{ route('admin.product-offer.status') }}",
                data: { id: id, status: status },
                success: function (data) {
                    toastr.success(data.success || 'Status changed successfully.');
                },
                error: function (xhr) {
                    $el.prop('checked', !status);
                    toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to change status.');
                }
            });
        });

        $('body').on('click', '.deleteOffer', function () {
            var id = $(this).data('id');
            var $row = $(this).closest('tr');
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
            }).then(function (result) {
                if (!result.isConfirmed) return;
                $.ajax({
                    type: 'DELETE',
                    url: "{{ url('admin/product-offer') }}/" + id,
                    success: function (data) {
                        $row.fadeOut(300, function () { $(this).remove(); });
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message || 'Record moved to trash successfully.',
                            customClass: { confirmButton: 'btn btn-success' }
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Cannot delete',
                            text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong!',
                            customClass: { confirmButton: 'btn btn-danger' }
                        });
                    }
                });
            });
        });
    });
</script>
@endpush
