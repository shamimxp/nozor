@extends('layouts.admin')
@section('title', 'Wishlist Management')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Customer Wishlists</h4>
                </div>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="wishlistTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer Info</th>
                            <th>Product Name</th>
                            <th>Added Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
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

        var table = $('#wishlistTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.wishlist.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'user_info', name: 'user.name'},
                {data: 'product_info', name: 'product.name'},
                {data: 'date', name: 'created_at'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        $('body').on('click', '.delete-wishlist', function () {
            var id = $(this).data('id');
            if(confirm("Are you sure you want to delete this wishlist record?")) {
                $.ajax({
                    type: "DELETE",
                    url: "{{ route('admin.wishlist.index') }}/" + id,
                    success: function (data) {
                        if(data.success) {
                            toastr.success(data.message);
                            table.draw();
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function (data) {
                        toastr.error('Error deleting record.');
                    }
                });
            }
        });
    });
</script>
@endpush
