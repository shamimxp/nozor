@extends('layouts.admin')
@section('title', 'Product List')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Product List</h4>
                    <a href="{{ route('admin.product.create') }}" class="btn btn-primary">Add New Product</a>
                </div>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="productTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Stock Quantity</th>
                            <th>Price</th>
                            <th>Featured</th>
                            <th>Status</th>
                            <th>Action</th>
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
                <h5 class="modal-title" id="modalTitle">Product price & stock</h5>
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
                    <button type="submit" class="btn btn-success" id="updateBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
            ajax: "{{ route('admin.product.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'product', name: 'product'},
                {data: 'category', name: 'category'},
                {data: 'stock', name: 'stock'},
                {data: 'price', name: 'price'},
                {data: 'featured', name: 'featured', orderable: false, searchable: false},
                {data: 'status', name: 'status', orderable: false, searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
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

        $('body').on('click', '.deleteProduct', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
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
                            toastr.success(data.success);
                        },
                        error: function (data) {
                            console.log('Error:', data);
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
    });
</script>
@endpush
