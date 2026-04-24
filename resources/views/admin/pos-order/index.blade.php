@extends('layouts.admin')
@section('title', 'POS Order List')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">POS Order List</h4>
                    @if(Route::has('admin.pos'))
                    <a href="{{ route('admin.pos') }}" target="_blank" class="btn btn-primary">Go to POS</a>
                    @endif
                </div>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="posOrderTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order Info</th>
                            <th>Customer</th>
                            <th>Payment Summary</th>
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
@endsection

@push('scripts')
<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#posOrderTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.pos-order.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'order_info', name: 'order_number'},
                {data: 'customer_info', name: 'customer.name'},
                {data: 'payment_summary', name: 'total_amount'},
                {data: 'status', name: 'order_status'},
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

        // Cancel Order
        $('body').on('click', '.cancelOrder', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Cancel Order?',
                text: "Stock will be restored!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#82868b',
                confirmButtonText: 'Yes, cancel it!',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-outline-danger ml-1'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post("{{ route('admin.pos-order.cancel', ':id') }}".replace(':id', id), {
                        _token: '{{ csrf_token() }}'
                    }, function(res) {
                        if (res.success) {
                            toastr.success(res.message);
                            table.draw();
                        } else {
                            toastr.error(res.message);
                        }
                    });
                }
            });
        });

        // Delete Order
        $('body').on('click', '.deleteOrder', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this POS order? (Items will be removed)",
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
                        url: "{{ url('admin/pos-order') }}"+'/'+id,
                        success: function (data) {
                            table.draw();
                            toastr.success(data.message);
                        },
                        error: function (data) {
                            toastr.error('Something went wrong!');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
