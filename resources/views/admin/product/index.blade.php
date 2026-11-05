@extends('layouts.admin')
@section('title', 'Product List')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Product List</h4>
                    <div class="d-flex" style="gap:6px;">
                        @include('admin.partials.show_deleted_button', ['route' => route('admin.product.trash'), 'permission' => 'product.deleted-list'])
                        @can('product.create')
                        <a href="{{ route('admin.product.create') }}" class="btn btn-primary">Add New Product</a>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body border-bottom p-1">
                <form id="filterForm">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <label>Product Name</label>
                                <input type="text" id="filter_name" class="form-control" placeholder="Search by name...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label>Category</label>
                                <select id="filter_category" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label>Sub Category</label>
                                <select id="filter_sub_category" class="form-control">
                                    <option value="">All Sub Categories</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label>Status</label>
                                <select id="filter_status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="filterBtn" class="btn btn-primary">Filter</button>
                            <button type="button" id="resetBtn" class="btn btn-secondary">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="productTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th class="text-center" style="width: 110px; min-width: 110px;">Manufacture Image</th>
                            <th>Category</th>
                            <th>Stock Quantity</th>
                            <th>Price</th>
                            <th>Featured</th>
                            <th>Model No</th>
                            <th>Manufacturer</th>
                            <th>Dealer Show</th>
                            <th>Customer Show</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="priceStockModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Product price || stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="priceStockForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="prod_id">
                    <input type="hidden" name="type" id="update_type">
                    <div class="form-group">
                        <label id="labelTitle">Total Quantity</label>
                        <input type="number" step="0.01" name="value" id="update_value" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="updateBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manufacture Blueprint Preview Modal -->
<div class="modal fade" id="manufactureImageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content shadow-lg border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-light py-1">
                <div>
                    <h5 class="modal-title font-weight-bolder text-primary mb-25" id="mfgModalTitleText">
                        Manufacture Blueprint
                    </h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center p-2" style="background: #f8fafc; max-height: 75vh; overflow-y: auto;">
                <div class="d-inline-block p-1 bg-white shadow-sm rounded" style="border: 1px solid #e2e8f0; max-width: 100%;">
                    <img id="mfgModalImage" src="" alt="Manufacture Blueprint" style="max-width: 100%; max-height: 60vh; object-fit: contain; border-radius: 4px; display: block; margin: 0 auto;">
                </div>
                <div id="mfgModalThumbs" class="mt-1 d-flex justify-content-center align-items-center flex-wrap" style="display: none; gap: 8px;"></div>
            </div>
            <div class="modal-footer py-1 d-flex justify-content-between align-items-center">
                <span class="small text-muted"><i data-feather="info" style="width: 13px; height: 13px;"></i> Click thumbnails to switch images</span>
                <div>
                    <a id="mfgModalDownloadBtn" href="#" download class="btn btn-sm btn-primary mr-50" target="_blank">
                        <i data-feather="download" style="width: 13px; height: 13px;" class="mr-25"></i> Open Full Image
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Manufacture Image Table Cell */
    #productTable td.mfg-image-cell {
        padding: 0 !important;
        margin: 0 !important;
        vertical-align: middle !important;
        text-align: center !important;
        height: 1px !important;
        width: 110px !important;
        min-width: 110px !important;
        position: relative !important;
    }

    .btn-preview-mfg-img {
        width: 100% !important;
        height: 100% !important;
        min-height: 75px !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        border-radius: 0 !important;
        display: block !important;
        position: relative !important;
        overflow: hidden !important;
        cursor: pointer !important;
        background: #f1f2f6 !important;
    }

    .btn-preview-mfg-img img {
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        transition: transform 0.25s ease, filter 0.2s ease;
    }

    .btn-preview-mfg-img:hover img {
        transform: scale(1.06);
        filter: brightness(0.92);
    }

    .btn-preview-mfg-img .mfg-zoom-badge {
        position: absolute;
        bottom: 4px;
        right: 4px;
        background: rgba(0, 0, 0, 0.65);
        color: #ffffff;
        border-radius: 3px;
        padding: 2px 5px;
        font-size: 9px;
        line-height: 1;
        display: flex;
        align-items: center;
        pointer-events: none;
        z-index: 2;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        transition: background-color 0.2s ease;
    }

    .btn-preview-mfg-img:hover .mfg-zoom-badge {
        background: #7367f0;
    }
</style>
@endsection

@push('scripts')
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#productTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.product.index') }}",
                data: function (d) {
                    d.name = $('#filter_name').val();
                    d.category_id = $('#filter_category').val();
                    d.sub_category_id = $('#filter_sub_category').val();
                    d.status = $('#filter_status').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'product', name: 'product'},
                {data: 'manufacture_image', name: 'manufacture_image', orderable: false, searchable: false, className: 'mfg-image-cell p-0 text-center'},
                {data: 'category', name: 'category'},
                {data: 'stock', name: 'stock'},
                {data: 'price', name: 'price'},
                {data: 'featured', name: 'featured', orderable: false, searchable: false},
                {data: 'model_no', name: 'model_no'},
                {data: 'manufacturer', name: 'manufacturer', orderable: false, searchable: false},
                {data: 'dealer_show', name: 'dealer_show', orderable: false, searchable: false},
                {data: 'customer_show', name: 'customer_show', orderable: false, searchable: false},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({
                        width: 14,
                        height: 14
                    });
                }
            }
        });

        $('#filterBtn').click(function() {
            table.draw();
        });

        $('#resetBtn').click(function() {
            $('#filterForm')[0].reset();
            $('#filter_sub_category').html('<option value="">All Sub Categories</option>');
            table.draw();
        });

        // Dependent Dropdown
        $('#filter_category').change(function() {
            var category_id = $(this).val();
            if (category_id) {
                $.ajax({
                    url: "{{ url('/admin/get-subcategory') }}/" + category_id,
                    type: "GET",
                    success: function(data) {
                        $('#filter_sub_category').empty();
                        $('#filter_sub_category').append('<option value="">All Sub Categories</option>');
                        $.each(data, function(key, value) {
                            $('#filter_sub_category').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            } else {
                $('#filter_sub_category').html('<option value="">All Sub Categories</option>');
            }
        });

        $('body').on('click', '.deleteProduct', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Move to trash?',
                text: "You can restore this later from Show Deleted Data.",
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
                        type: "DELETE",
                        url: "{{ url('admin/product') }}"+'/'+id,
                        success: function (data) {
                            table.draw();
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message || data.success,
                                customClass: { confirmButton: 'btn btn-success' }
                            });
                        },
                        error: function (xhr) {
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

        $('body').on('change', '.changeStatus', function() {
            var id = $(this).data('id');
            var status = $(this).prop('checked') == true ? 1 : 0;
            $.ajax({
                type: "POST",
                url: "{{ route('admin.product.status') }}",
                data: { 'id': id, 'status': status },
                success: function(data) {
                    toastr.success(data.success);
                }
            });
        });

        $('body').on('click', '.toggleVisibility', function() {
            var button = $(this);
            var next = button.data('value') == 1 ? 0 : 1;
            button.prop('disabled', true);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.product.visibility') }}",
                data: {
                    id: button.data('id'),
                    field: button.data('field'),
                    value: next
                },
                success: function(data) {
                    button.data('value', next);
                    button.text(next === 1 ? 'Active' : 'Disable');
                    button.toggleClass('badge-success', next === 1);
                    button.toggleClass('badge-danger', next !== 1);
                    button.prop('disabled', false);
                    toastr.success(data.success);
                },
                error: function() {
                    button.prop('disabled', false);
                    toastr.error('Could not update this status.');
                }
            });
        });

        $('body').on('change', '.changeFeatured', function() {
            var id = $(this).data('id');
            var status = $(this).prop('checked') == true ? 1 : 0;
            $.ajax({
                type: "POST",
                url: "{{ route('admin.product.featured-status') }}",
                data: { 'id': id, 'status': status },
                success: function(data) {
                    toastr.success(data.success);
                }
            });
        });

        $('body').on('change', '.changeManufacturer', function() {
            var id = $(this).data('id');
            var status = $(this).prop('checked') == true ? 1 : 0;
            $.ajax({
                type: "POST",
                url: "{{ route('admin.product.manufacturer-status') }}",
                data: { 'id': id, 'status': status },
                success: function(data) {
                    toastr.success(data.success);
                }
            });
        });

        $('body').on('click', '.editPriceStock', function() {
            var id = $(this).data('id');
            var type = $(this).data('type');
            var value = $(this).data('value');

            $('#prod_id').val(id);
            $('#update_type').val(type);
            $('#update_value').val(value);

            if(type == 'stock'){
                $('#labelTitle').text('Total Quantity');
            } else {
                $('#labelTitle').text('Selling Price');
            }
            $('#priceStockModal').modal('show');
        });

        $('#priceStockForm').on('submit', function(e) {
            e.preventDefault();
            $('#updateBtn').text('Updating...').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: "{{ route('admin.product.update-price-stock') }}",
                data: $(this).serialize(),
                success: function(data) {
                    $('#priceStockModal').modal('hide');
                    $('#updateBtn').text('Submit').attr('disabled', false);
                    table.draw();
                    toastr.success(data.success);
                },
                error: function(data) {
                    $('#updateBtn').text('Submit').attr('disabled', false);
                    toastr.error('Something went wrong!');
                }
            });
        });

        // Manufacture Image Popup Preview
        $(document).on('click', '.btn-preview-mfg-img', function(e) {
            e.preventDefault();
            let mainImg = $(this).data('img');
            let title = $(this).data('title');
            let allImages = $(this).data('all-images');

            if (typeof allImages === 'string') {
                try {
                    allImages = JSON.parse(allImages);
                } catch(e) {
                    allImages = [mainImg];
                }
            }

            $('#mfgModalTitleText').text(title || 'Manufacture Blueprint');
            $('#mfgModalImage').attr('src', mainImg);
            $('#mfgModalDownloadBtn').attr('href', mainImg);

            let thumbsContainer = $('#mfgModalThumbs');
            thumbsContainer.empty();

            if (Array.isArray(allImages) && allImages.length > 1) {
                allImages.forEach(function(imgSrc) {
                    let activeClass = (imgSrc === mainImg) ? 'border-primary' : 'border-secondary';
                    let thumb = $('<img src="' + imgSrc + '" class="mfg-thumb-nav ' + activeClass + '" style="width: 52px; height: 52px; object-fit: contain; cursor: pointer; border: 2px solid; border-radius: 6px; background: #fff; padding: 2px;">');
                    thumbsContainer.append(thumb);
                });
                thumbsContainer.show();
            } else {
                thumbsContainer.hide();
            }

            if (typeof feather !== 'undefined') {
                feather.replace({ width: 14, height: 14 });
            }

            $('#manufactureImageModal').modal('show');
        });

        $(document).on('click', '.mfg-thumb-nav', function() {
            let src = $(this).attr('src');
            $('#mfgModalImage').attr('src', src);
            $('#mfgModalDownloadBtn').attr('href', src);
            $('.mfg-thumb-nav').removeClass('border-primary').addClass('border-secondary');
            $(this).removeClass('border-secondary').addClass('border-primary');
        });
    });
</script>
@endpush
