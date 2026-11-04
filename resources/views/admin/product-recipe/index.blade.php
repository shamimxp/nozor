@extends('layouts.admin')
@section('title', 'Product Recipes')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Product Recipe List</h4>
                <div>
                    @include('admin.partials.show_deleted_button', ['route' => route('admin.product-recipe.trash')])
                    <a href="{{ route('admin.product-recipe.create') }}" class="btn btn-primary"><i data-feather="plus"></i> Add Recipe</a>
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped data-table w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Cost Title</th>
                                <th>Images</th>
                                <th>Material Items</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.product-recipe.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'product_name', name: 'product.name'},
                {data: 'cost_title', name: 'costSetting.cost_title'},
                {data: 'images', name: 'images', orderable: false, searchable: false},
                {data: 'material_items', name: 'material_items', orderable: false, searchable: false},
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

        $('body').on('click', '.deleteBtn', function() {
            var id = $(this).data('id');
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
                        url: "{{ url('admin/product-recipe') }}/" + id,
                        data: { "_token": "{{ csrf_token() }}" },
                        success: function(data) {
                            table.draw();
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message || data.success,
                                customClass: { confirmButton: 'btn btn-success' }
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
    });
</script>
@endpush
