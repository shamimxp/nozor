@extends('layouts.admin')
@section('title', 'Cost Setting Details')
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-bottom p-1 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Cost Setting Details: <span class="text-primary">{{ $costSetting->cost_title }}</span></h4>
                <div>
                    <a href="{{ route('admin.cost-setting.edit', $costSetting->id) }}" class="btn btn-primary mr-50">
                        <i data-feather="edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.cost-setting.index') }}" class="btn btn-secondary">
                        <i data-feather="arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="border rounded p-2 bg-light">
                            <span class="text-muted font-small-3 d-block">Cost Title</span>
                            <h4 class="font-weight-bolder text-dark mb-0 mt-25">{{ $costSetting->cost_title }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-2 bg-light">
                            <span class="text-muted font-small-3 d-block">Status</span>
                            <h4 class="mb-0 mt-25">
                                @if($costSetting->status == 1)
                                    <span class="badge badge-light-success">Active</span>
                                @else
                                    <span class="badge badge-light-danger">Inactive</span>
                                @endif
                            </h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-2 bg-light">
                            <span class="text-muted font-small-3 d-block">Total Declared Cost</span>
                            <h4 class="font-weight-bolder text-primary mb-0 mt-25">
                                ৳ {{ number_format($costSetting->items->sum('cost_price'), 2) }}
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="card border shadow-none mb-0">
                    <div class="card-header bg-light p-1 border-bottom">
                        <h5 class="card-title mb-0 font-weight-bold">Cost Breakdown by Cost Head</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 60px;" class="text-center">#</th>
                                    <th>Cost Head</th>
                                    <th style="width: 150px;" class="text-center">Head Status</th>
                                    <th style="width: 200px;" class="text-right">Declared Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($costSetting->items as $index => $item)
                                    <tr>
                                        <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                                        <td class="font-weight-bold text-dark">{{ $item->costHead->title ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            @if(($item->costHead->status ?? 0) == 1)
                                                <span class="badge badge-light-success">Active</span>
                                            @else
                                                <span class="badge badge-light-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="text-right font-weight-bolder text-primary">
                                            ৳ {{ number_format($item->cost_price, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-2 text-muted">No cost heads assigned.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light font-weight-bold">
                                <tr>
                                    <td colspan="3" class="text-right font-weight-bolder">Grand Total Cost:</td>
                                    <td class="text-right text-primary font-weight-bolder" style="font-size: 1.15rem;">
                                        ৳ {{ number_format($costSetting->items->sum('cost_price'), 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
