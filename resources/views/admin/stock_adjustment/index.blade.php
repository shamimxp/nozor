@extends('layouts.admin')
@section('title', 'Stock Adjustment List')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Stock Adjustment List</h4>
                    <a href="{{ route('admin.stock-adjustment.create') }}" class="btn btn-primary">Create Adjustment</a>
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="border rounded p-1 mb-2">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" id="filterDateFrom" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" id="filterDateTo" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4 text-md-right">
                            <button type="button" id="filterBtn" class="btn btn-primary mr-1">Filter</button>
                            <button type="button" id="resetFilterBtn" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="adjustmentTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Type</th>
                                <th>Receive Status</th>
                                <th>Date</th>
                                <th>Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        var adjustmentTable = $('#adjustmentTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.stock-adjustment.index') }}",
                data: function(d) {
                    d.date_from = $('#filterDateFrom').val();
                    d.date_to = $('#filterDateTo').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'product_name', name: 'product_name'},
                {data: 'quantity', name: 'quantity'},
                {data: 'type_badge', name: 'type_badge'},
                {data: 'receive_status', name: 'receive_status', orderable: false, searchable: false},
                {data: 'date', name: 'date'},
                {data: 'reason', name: 'reason'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        $('#filterBtn').on('click', function() {
            adjustmentTable.ajax.reload();
        });

        $('#resetFilterBtn').on('click', function() {
            $('#filterDateFrom, #filterDateTo').val('');
            adjustmentTable.ajax.reload();
        });

        $('#filterDateFrom, #filterDateTo').on('change', function() {
            adjustmentTable.ajax.reload();
        });

        $(document).on('click', '.receiveAdjustmentBtn', function() {
            var button = $(this);
            var product = button.data('product');
            var quantity = button.data('quantity');

            Swal.fire({
                title: 'Receive exchange stock?',
                text: 'This will add ' + quantity + ' quantity back to ' + product + '.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, receive',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-outline-secondary ml-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (!result.isConfirmed) {
                    return;
                }

                button.attr('disabled', true);

                $.ajax({
                    url: "{{ url('/admin/stock-adjustment') }}/" + button.data('id') + "/receive",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        toastr.success(response.success);
                        adjustmentTable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        button.attr('disabled', false);
                        toastr.error(xhr.responseJSON.error || 'Something went wrong!');
                    }
                });
            });
        });
    });
</script>
@endpush
