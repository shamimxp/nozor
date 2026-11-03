@extends('layouts.admin')
@section('title', 'Draft Orders')

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Draft Orders</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active">Draft Orders</li>
    </ol>
</div>
@endsection

@section('content-header-right')
<a href="{{ route('admin.order-requests.index') }}" class="btn btn-outline-primary shadow-sm font-weight-bold">
    <i data-feather="list" class="mr-50"></i> View Order Requests
</a>
@endsection

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="row align-items-end mb-3">
            <div class="col-md-3">
                <label class="small font-weight-bold">Draft Number</label>
                <input type="text" id="filterDraftNumber" class="form-control form-control-sm" placeholder="Search by draft #...">
            </div>
            <div class="col-md-3">
                <label class="small font-weight-bold">Dealer Info</label>
                <input type="text" id="filterDealerInfo" class="form-control form-control-sm" placeholder="Search dealer name, shop, phone...">
            </div>
            <div class="col-md-2">
                <label class="small font-weight-bold">Start Date</label>
                <input type="date" id="filterStartDate" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="small font-weight-bold">End Date</label>
                <input type="date" id="filterEndDate" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <label class="small font-weight-bold">Status</label>
                <select id="filterStatus" class="form-control form-control-sm">
                    <option value="">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="converted">Converted</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-12 text-right mt-2">
                <button type="button" id="filterBtn" class="btn btn-sm btn-info shadow-sm mr-1"><i data-feather="filter"></i> Filter</button>
                <button type="button" id="resetBtn" class="btn btn-sm btn-secondary shadow-sm"><i data-feather="refresh-ccw"></i> Reset</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-striped w-100" id="draftOrderTable">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Draft Info</th>
                        <th width="20%">Dealer</th>
                        <th width="15%">Products</th>
                        <th width="15%">Total Amount</th>
                        <th width="10%">Status</th>
                        <th width="15%" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let table = $('#draftOrderTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.draft-orders.index') }}",
                data: function (d) {
                    d.draft_number = $('#filterDraftNumber').val();
                    d.dealer_info  = $('#filterDealerInfo').val();
                    d.start_date   = $('#filterStartDate').val();
                    d.end_date     = $('#filterEndDate').val();
                    d.status       = $('#filterStatus').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'id', orderable: false, searchable: false },
                { data: 'draft_info', name: 'draft_number' },
                { data: 'dealer_info', name: 'dealer.name' },
                { data: 'total_items', name: 'items_count', orderable: false, searchable: false },
                { data: 'totals', name: 'grand_total' },
                { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            ],
            drawCallback: function () {
                if (feather) {
                    feather.replace({ width: 14, height: 14 });
                }
            }
        });

        $('#filterBtn').on('click', function() {
            table.draw();
        });

        $('#resetBtn').on('click', function() {
            $('#filterDraftNumber').val('');
            $('#filterDealerInfo').val('');
            $('#filterStartDate').val('');
            $('#filterEndDate').val('');
            $('#filterStatus').val('');
            table.draw();
        });

        // Convert Draft Order to Dealer Order
        $(document).on('click', '.btnConvertOrder', function() {
            let id = $(this).data('id');
            let num = $(this).data('num');

            Swal.fire({
                title: 'Convert Draft Order?',
                text: 'Create an actual Dealer Order from draft ' + num + '?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28c76f',
                cancelButtonColor: '#6e7d88',
                confirmButtonText: 'Yes, Convert Order!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Converting...',
                        text: 'Please wait while the order is created.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    $.ajax({
                        url: "/admin/draft-orders/" + id + "/convert",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Converted!',
                                    text: response.message || 'Dealer order created successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    if (response.redirect_url) {
                                        window.location.href = response.redirect_url;
                                    } else {
                                        table.ajax.reload(null, false);
                                    }
                                });
                            } else {
                                Swal.fire('Error!', response.message || 'Could not convert order.', 'error');
                            }
                        },
                        error: function(xhr) {
                            let msg = 'Failed to convert order.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error!', msg, 'error');
                        }
                    });
                }
            });
        });

        // Delete Draft Order (Restores items to Order Request)
        $(document).on('click', '.btnDeleteDraft', function() {
            let id = $(this).data('id');
            let num = $(this).data('num');

            Swal.fire({
                title: 'Delete Draft Order?',
                text: 'Draft ' + num + ' will be deleted, and all products will be returned to the original Order Request.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ea5455',
                cancelButtonColor: '#6e7d88',
                confirmButtonText: 'Yes, Delete & Restore!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/admin/draft-orders/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                                table.ajax.reload(null, false);
                            } else {
                                toastr.error(response.message || 'Failed to delete draft order.');
                            }
                        },
                        error: function(xhr) {
                            let msg = 'Error deleting draft order.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            toastr.error(msg);
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
