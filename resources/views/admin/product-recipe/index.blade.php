@extends('layouts.admin')
@section('title', 'Product Recipes')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Product Recipe List</h4>
                <a href="{{ route('admin.product-recipe.create') }}" class="btn btn-primary"><i data-feather="plus"></i> Add Recipe</a>
            </div>
            <div class="card-body pt-2">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped data-table w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
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
            if (confirm('Are you sure to delete?')) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ url('admin/product-recipe') }}/" + $(this).data('id'),
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        table.draw();
                        toastr.success(response.success);
                    }
                });
            }
        });
    });
</script>
@endpush
