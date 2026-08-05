@extends('layouts.admin')
@section('title', 'Worker Profile')
@section('content')
<div class="row">
    <!-- Worker Details Profile Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                @if($worker->profile_image)
                    <img src="{{ asset('storage/'.$worker->profile_image) }}" class="rounded-circle mb-1" width="100" height="100" style="object-fit:cover;" alt="Worker Profile">
                @else
                    <div class="rounded-circle mb-1 bg-light d-flex align-items-center justify-content-center mx-auto" style="width:100px; height:100px;">
                        <i data-feather="user" style="width:40px; height:40px; color:#aaa;"></i>
                    </div>
                @endif
                <h4 class="mb-0">{{ $worker->name }}</h4>
                <p class="text-muted">{{ $worker->type == 1 ? 'Finishing Part Worker' : 'Body Part Worker' }}</p>

                <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-1">
                    <span class="font-weight-bold">Phone:</span>
                    <span>{{ $worker->phone ?? 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1 border-top pt-1">
                    <span class="font-weight-bold">Email:</span>
                    <span>{{ $worker->email ?? 'N/A' }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-1 border-top pt-1">
                    <span class="font-weight-bold">Status:</span>
                    <span>
                        @if($worker->status == 1)
                            <span class="badge badge-success">Active</span>
                        @else
                            <span class="badge badge-danger">Inactive</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-md-8">
        <div class="row">
            <!-- Manufacturing Stats -->
            <div class="col-sm-4 col-12">
                <div class="card text-center text-primary">
                    <div class="card-body py-2">
                        <div class="avatar bg-light-primary p-50 mb-1">
                            <div class="avatar-content">
                                <i data-feather="box" class="font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="font-weight-bolder">{{ $totalOrders }}</h2>
                        <p class="card-text">Total Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12">
                <div class="card text-center text-warning">
                    <div class="card-body py-2">
                        <div class="avatar bg-light-warning p-50 mb-1">
                            <div class="avatar-content">
                                <i data-feather="clock" class="font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="font-weight-bolder">{{ $pendingOrders }}</h2>
                        <p class="card-text">Pending Orders</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12">
                <div class="card text-center text-success">
                    <div class="card-body py-2">
                        <div class="avatar bg-light-success p-50 mb-1">
                            <div class="avatar-content">
                                <i data-feather="check-circle" class="font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="font-weight-bolder">{{ $completedOrders }}</h2>
                        <p class="card-text">Collected Orders</p>
                    </div>
                </div>
            </div>

            <!-- Financial Stats -->
            <div class="col-sm-4 col-12">
                <div class="card text-center">
                    <div class="card-body py-2">
                        <div class="avatar bg-light-info p-50 mb-1">
                            <div class="avatar-content">
                                <i data-feather="dollar-sign" class="font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="font-weight-bolder">{{ number_format($totalAmount, 2) }}</h2>
                        <p class="card-text">Total Billed</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12">
                <div class="card text-center text-success">
                    <div class="card-body py-2">
                        <div class="avatar bg-light-success p-50 mb-1">
                            <div class="avatar-content">
                                <i data-feather="credit-card" class="font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="font-weight-bolder">{{ number_format($totalPaid, 2) }}</h2>
                        <p class="card-text">Total Paid</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-12">
                <div class="card text-center text-danger">
                    <div class="card-body py-2">
                        <div class="avatar bg-light-danger p-50 mb-1">
                            <div class="avatar-content">
                                <i data-feather="alert-circle" class="font-medium-5"></i>
                            </div>
                        </div>
                        <h2 class="font-weight-bolder">{{ number_format($totalDue, 2) }}</h2>
                        <p class="card-text">Total Due</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="col-12 mt-2">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Payment History</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive p-1">
                    <table class="table table-striped yajra-datatable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Product</th>
                                <th>Status</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
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
    $(function () {
        var table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.worker.show', $worker->id) }}",
            columns: [
                {data: 'date', name: 'date'},
                {data: 'invoice_no', name: 'manufacture.invoice_no'},
                {data: 'product', name: 'manufacture.product.name'},
                {data: 'status', name: 'status'},
                {data: 'total_amount', name: 'total_amount', className: 'text-right font-weight-bold'},
            ],
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
