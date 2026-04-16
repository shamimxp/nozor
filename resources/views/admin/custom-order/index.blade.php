@extends('layouts.admin')
@section('title', 'Custom Order List')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Custom Order List</h4>
                    <a href="{{ route('admin.custom-order.create') }}" class="btn btn-primary">Add New Custom Order</a>
                </div>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="customOrderTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order Info</th>
                            <th>Customer</th>
                            <th>Items Summary</th>
                            <th>Totals</th>
                            <th>Assign Vendor</th>
                            <th>Status (Quick Update)</th>
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

        var table = $('#customOrderTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.custom-order.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'order_info', name: 'order_number'},
                {data: 'customer_name', name: 'customer.name'},
                {data: 'items_summary', name: 'type'},
                {data: 'totals', name: 'grand_total'},
                {data: 'assign_vendor', name: 'vendor_id', orderable: false, searchable: false},
                {data: 'status_badge', name: 'status', orderable: false, searchable: false},
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

        // Quick Vendor Update
        $('body').on('change', '.assignVendorSelect', function() {
            var id = $(this).data('order-id');
            var vendor_id = $(this).val();
            $.ajax({
                type: "POST",
                url: "{{ route('admin.custom-order.assign-vendor') }}",
                data: { 'order_id': id, 'vendor_id': vendor_id },
                success: function(data) {
                    if (data.success) {
                        toastr.success(data.message);
                        table.draw(false);
                    } else {
                        toastr.error(data.message);
                        table.draw(false);
                    }
                },
                error: function(data) {
                    toastr.error(data.responseJSON.message || 'Failed to assign vendor.');
                    table.draw(false);
                }
            });
        });

        // Quick Status Update
        $('body').on('change', '.updateStatusSelect', function() {
            var id = $(this).data('order-id');
            var status = $(this).val();
            $.ajax({
                type: "POST",
                url: "{{ route('admin.custom-order.update-status') }}",
                data: { 'order_id': id, 'status': status },
                success: function(data) {
                    if (data.success) {
                        toastr.success(data.message);
                        table.draw(false);
                    } else {
                        toastr.error(data.message);
                        table.draw(false);
                    }
                },
                error: function(data) {
                    toastr.error(data.responseJSON.message || 'Failed to update status.');
                    table.draw(false);
                }
            });
        });

        // Delete Order
        $('body').on('click', '.deleteOrder', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this order? (Items and images will be removed)",
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
                        url: "{{ url('admin/custom-order') }}"+'/'+id,
                        success: function (data) {
                            table.draw();
                            toastr.success(data.success);
                        },
                        error: function (data) {
                            toastr.error(data.responseJSON.error || 'Something went wrong!');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
