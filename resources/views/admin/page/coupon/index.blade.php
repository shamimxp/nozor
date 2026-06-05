@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Coupons</h4>
                <a href="{{ route('admin.coupon.create') }}" class="btn btn-primary btn-sm">Add Coupon</a>
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
                                        <form action="{{ route('admin.coupon.destroy', $coupon->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?');">Delete</button>
                                        </form>
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
    });
</script>
@endpush
