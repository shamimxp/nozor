@extends('layouts.admin')
@section('title', 'Coupons')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Coupons</h4>
                <div class="d-flex align-items-center gap-1">
                    @include('admin.partials.show_deleted_button', ['route' => route('admin.coupon.trash')])
                    <a href="{{ route('admin.coupon.create') }}" class="btn btn-primary btn-sm">Add Coupon</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="couponTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sl</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coupons as $key => $coupon)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $coupon->code }}</td>
                                    <td>{{ ucfirst($coupon->type) }}</td>
                                    <td>{{ $coupon->amount }}</td>
                                    <td>{{ $coupon->start_date }}</td>
                                    <td>{{ $coupon->end_date }}</td>
                                    <td>
                                        @if($coupon->status == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.coupon.edit', $coupon->id) }}" class="btn btn-info btn-sm">Edit</a>
                                        <button type="button" class="btn btn-danger btn-sm deleteCoupon" data-id="{{ $coupon->id }}">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
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
        $('#couponTable').DataTable();

        $('body').on('click', '.deleteCoupon', function () {
            var id = $(this).data('id');
            var $row = $(this).closest('tr');
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
                        url: "{{ url('admin/coupon') }}/" + id,
                        success: function (data) {
                            $row.fadeOut(300, function () { $(this).remove(); });
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: data.message || data.success,
                                customClass: { confirmButton: 'btn btn-success' }
                            });
                        },
                        error: function (xhr) {
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
