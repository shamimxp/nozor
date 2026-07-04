@extends('layouts.admin')
@section('title', 'Dealer Details')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h4 class="card-title mb-0">Dealer Details</h4>
                    <a href="{{ route('admin.dealer.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <div class="text-center">
                            @if($dealer->profile_image && file_exists(public_path($dealer->profile_image)))
                                <img src="{{ asset($dealer->profile_image) }}" alt="Profile Image" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 200px;">
                                    <span class="text-muted">No Profile Image</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-8 mb-2">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">Name</th>
                                    <td>{{ $dealer->name }}</td>
                                </tr>
                                <tr>
                                    <th>Shop Name</th>
                                    <td>{{ $dealer->shop_name }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $dealer->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $dealer->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>NID Number</th>
                                    <td>{{ $dealer->nid_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Bank Name</th>
                                    <td>{{ $dealer->bank_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Account No</th>
                                    <td>{{ $dealer->account_no ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $dealer->address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($dealer->status == 1)
                                            <span class="badge badge-light-success">Active</span>
                                        @else
                                            <span class="badge badge-light-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Special Dealer</th>
                                    <td>
                                        @if($dealer->is_special == 1)
                                            <span class="badge badge-light-info">Yes</span>
                                        @else
                                            <span class="badge badge-light-secondary">No</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6 mb-2">
                        <h5>NID Image</h5>
                        <div class="border p-1 rounded text-center">
                            @if($dealer->nid_image && file_exists(public_path($dealer->nid_image)))
                                <img src="{{ asset($dealer->nid_image) }}" alt="NID Image" class="img-fluid rounded" style="max-height: 300px;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 300px;">
                                    <span class="text-muted">No NID Image Provided</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <h5>Trade License</h5>
                        <div class="border p-1 rounded text-center">
                            @if($dealer->trade_license && file_exists(public_path($dealer->trade_license)))
                                <img src="{{ asset($dealer->trade_license) }}" alt="Trade License" class="img-fluid rounded" style="max-height: 300px;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded" style="height: 300px;">
                                    <span class="text-muted">No Trade License Provided</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function () {
        if (feather) {
            feather.replace({ width: 14, height: 14 });
        }
    });
</script>
@endpush
