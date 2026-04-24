@extends('layouts.admin')
@section('title', 'Purchase List')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Purchase List</h4>
                    <a href="{{ route('admin.purchase.create') }}" class="btn btn-primary">Create Purchase</a>
                </div>
            </div>
            <div class="card-body table-responsive pt-2">
                <table id="purchaseTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Purchase Info</th>
                            <th>Vendor</th>
                            <th>Financial Summary</th>
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

        var table = $('#purchaseTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.purchase.index') }}",
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'purchase_info', name: 'purchase_number'},
                {data: 'vendor_info', name: 'vendor.name'},
                {data: 'financials', name: 'grand_total'},
                {data: 'status', name: 'status'},
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

        $('body').on('click', '.deletePurchase', function () {
            var id = $(this).data("id");
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "{{ url('admin/purchase') }}"+'/'+id,
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
