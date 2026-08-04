@extends('layouts.admin')
@section('title', 'Manufacture Details')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom p-1">
                <h4 class="card-title">Manufacture Details</h4>
                <a href="{{ route('admin.manufacture.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
            <div class="card-body mt-2">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th>Invoice No</th>
                                <td>{{ $manufacture->invoice_no }}</td>
                            </tr>
                            <tr>
                                <th>Product</th>
                                <td>{{ $manufacture->product->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Dealer</th>
                                <td>{{ $manufacture->dealer->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Dealer Phone</th>
                                <td>{{ $manufacture->dealer_phone }}</td>
                            </tr>
                            <tr>
                                <th>Quantity</th>
                                <td>{{ $manufacture->manufacture_qty }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @if($manufacture->is_confirm)
                                        <span class="badge badge-success">Confirmed</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created By</th>
                                <td>{{ $manufacture->creator->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Date Created</th>
                                <td>{{ $manufacture->created_at->format('d M Y h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th colspan="2" class="text-center bg-light">Financial Summary</th>
                            </tr>
                            <tr>
                                <th>Body Part Price</th>
                                <td>{{ number_format($manufacture->body_part_price, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Finishing Part Price</th>
                                <td>{{ number_format($manufacture->finishing_part_price, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Total Body Cost</th>
                                <td>{{ number_format($manufacture->body_total, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Total Finishing Cost</th>
                                <td>{{ number_format($manufacture->finishing_total, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Grand Total</th>
                                <td class="font-weight-bold text-success">{{ number_format($manufacture->grand_total, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Note</th>
                                <td>{{ $manufacture->note ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
