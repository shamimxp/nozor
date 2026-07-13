@extends('layouts.dealer')
@section('title', 'Order Requests')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">Order Requests</h2>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-bottom p-1">
                        <div class="head-label">
                            <h6 class="mb-0">Order Request List</h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mt-2">
                            <table class="table table-bordered table-striped datatable">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Request Date</th>
                                        <th>Total Items</th>
                                        <th>Note</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/tables/datatable/responsive.bootstrap4.min.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/datatables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('app-assets/vendors/js/tables/datatable/dataTables.responsive.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            var table = $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('dealer.order-requests.index') }}',
                columns: [
                    { data: 'request_number', name: 'request_number', render: function(data, type, row) {
                        return data ? data : row.id;
                    }},
                    { data: 'request_date', name: 'request_date', searchable: false },
                    { data: 'total_items', name: 'total_items', searchable: false },
                    { data: 'note', name: 'note' },
                    { data: 'status', name: 'status' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                order: [[0, 'desc']],
                drawCallback: function() {
                    if (feather) feather.replace({ width: 14, height: 14 });
                }
            });
        });

        function deleteRequest(id) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Delete Request?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeDelete(id);
                    }
                });
            } else {
                if (confirm("Are you sure you want to delete this order request?")) {
                    executeDelete(id);
                }
            }
        }

        function executeDelete(id) {
            $.ajax({
                url: '{{ url("dealer/order-requests") }}/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.status === 'success') {
                        if (typeof toastr !== 'undefined') toastr.success(response.message);
                        else alert(response.message);
                        $('.datatable').DataTable().ajax.reload();
                    } else {
                        if (typeof toastr !== 'undefined') toastr.error(response.message);
                        else alert(response.message);
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') toastr.error('Error deleting request');
                    else alert('Error deleting request');
                }
            });
        }
    </script>
@endpush
