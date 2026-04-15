@extends('layouts.admin')
@section('title', 'Custom Orders')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Custom Order List</h4>
                <a href="{{ route('admin.custom-order.create') }}" class="btn btn-primary btn-sm">
                    <i data-feather="plus" class="mr-25"></i> Create New Order
                </a>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="customOrderTable" class="table table-bordered table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Style No</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Grand Total</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th>Assign Vendor</th>
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
    $.ajaxSetup({ headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} });

    var table = $('#customOrderTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.custom-order.index') }}",
        columns: [
            {data: 'DT_RowIndex',          orderable: false, searchable: false},
            {data: 'style_number',          name: 'style_number'},
            {data: 'order_date_formatted',  name: 'order_date'},
            {data: 'customer_name',         name: 'customer.name'},
            {data: 'grand_total_formatted', name: 'grand_total'},
            {data: 'paid_formatted',        name: 'paid'},
            {data: 'due_formatted',         name: 'due'},
            {data: 'status_badge',          name: 'status', orderable: false, searchable: false},
            {data: 'assign_vendor',         name: 'assign_vendor', orderable: false, searchable: false},
            {data: 'action',               name: 'action', orderable: false, searchable: false},
        ],
        order: [[0, 'desc']],
        drawCallback: function () {
            if (typeof feather !== 'undefined') feather.replace({width: 14, height: 14});
        }
    });

    // Assign vendor from list
    $('body').on('change', '.assignVendorSelect', function () {
        var orderId  = $(this).data('order-id');
        var vendorId = $(this).val();
        $.ajax({
            type: 'POST',
            url: "{{ route('admin.custom-order.assign-vendor') }}",
            data: { order_id: orderId, vendor_id: vendorId },
            success: function (data) { toastr.success(data.success); },
            error:   function ()     { toastr.error('Failed to assign vendor.'); }
        });
    });

    // Delete
    $('body').on('click', '.deleteOrder', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-outline-danger ml-1' },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'DELETE',
                    url: "{{ url('admin/custom-order') }}/" + id,
                    success: function (data) { table.draw(); toastr.success(data.success); },
                    error:   function ()     { toastr.error('Error deleting record.'); }
                });
            }
        });
    });
});
</script>
@endpush
