@extends('layouts.admin')
@section('title', 'Return #' . $return->return_no)

@push('styles')
<style>
    .return-detail-card {
        background-color: #FDFAF5;
        border: 1px solid #efeae2;
        border-radius: 10px;
    }
    .return-products-table thead th {
        background-color: #111C38 !important;
        color: #ffffff !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        border: none !important;
        padding: 10px !important;
        vertical-align: middle !important;
    }
    .return-products-table tbody td {
        font-size: 13px !important;
        vertical-align: middle !important;
        padding: 10px !important;
        border-bottom: 1px solid #f1f5f9;
    }
    @media print {
        body * {
            visibility: hidden;
        }
        #printableReturnArea, #printableReturnArea * {
            visibility: visible;
        }
        #printableReturnArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@section('breadcrumb')
<h2 class="content-header-title float-left mb-0">Return Details</h2>
<div class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.returns.index') }}">Return Orders</a></li>
        <li class="breadcrumb-item active">{{ $return->return_no }}</li>
    </ol>
</div>
@endsection

@section('content')
<div class="content-body" id="printableReturnArea">
    <!-- Top Action Bar -->
    <div class="card return-detail-card mb-2 shadow-none no-print">
        <div class="card-body p-2 d-flex flex-wrap align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <span class="fieldora-title-badge me-2" style="display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; background-color: #111C38; color: #ffffff; border-radius: 8px; margin-right: 12px;">
                    <i data-feather="corner-down-left" style="width: 22px; height: 22px; stroke: #ffffff;"></i>
                </span>
                <div>
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 font-weight-bolder text-dark me-2" style="margin-right: 8px;">{{ $return->return_no }}</h4>
                        {!! $return->status_badge !!}
                        <span class="ms-1" style="margin-left: 6px;">{!! $return->channel_badge !!}</span>
                        <span class="ms-1" style="margin-left: 6px;">{!! $return->refund_type_badge !!}</span>
                    </div>
                    <small class="text-muted">Return Date: {{ date('M d, Y', strtotime($return->return_date)) }} &bull; Created by {{ $return->creator ? $return->creator->name : 'Staff' }}</small>
                </div>
            </div>

            <div class="mt-1 mt-md-0 d-flex align-items-center" style="gap: 8px;">
                @if($return->status === 'pending')
                    <button type="button" class="btn btn-sm btn-success d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#approveReturnModal" data-toggle="modal" data-target="#approveReturnModal" style="height: 36px; border-radius: 6px; font-weight: 600; padding: 0 14px;">
                        <i data-feather="check-circle" style="width: 14px; height: 14px; margin-right: 4px;"></i> Approve Return
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#rejectReturnModal" data-toggle="modal" data-target="#rejectReturnModal" style="height: 36px; border-radius: 6px; font-weight: 600; padding: 0 14px;">
                        <i data-feather="x-circle" style="width: 14px; height: 14px; margin-right: 4px;"></i> Reject
                    </button>
                @endif

                <button type="button" onclick="window.print()" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center" style="height: 36px; border-radius: 6px; font-weight: 600; padding: 0 12px;">
                    <i data-feather="printer" style="width: 14px; height: 14px; margin-right: 4px;"></i> Print Slip
                </button>

                <a href="{{ route('admin.returns.index') }}" class="btn btn-sm btn-secondary d-inline-flex align-items-center" style="height: 36px; border-radius: 6px; font-weight: 600; padding: 0 14px; background-color: #64748b; border-color: #64748b;">
                    <i data-feather="arrow-left" style="width: 13px; height: 13px; margin-right: 4px;"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    @if($return->status === 'approved')
        <div class="alert alert-success p-2 mb-2 d-flex align-items-center justify-content-between" style="background-color: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px;">
            <div class="d-flex align-items-center">
                <i data-feather="check-circle" style="width: 22px; height: 22px; color: #059669; margin-right: 12px;"></i>
                <div>
                    <h6 class="mb-0 font-weight-bolder text-success">Return Approved &amp; Settled</h6>
                    <small class="text-muted">
                        Approved on {{ $return->approved_at ? $return->approved_at->format('M d, Y h:i A') : '-' }} by {{ $return->approver ? $return->approver->name : 'Admin' }}
                        @if($return->refund_type === 'wallet')
                            &bull; <strong>৳{{ number_format($return->refund_amount, 2) }} credited to dealer wallet</strong>
                        @else
                            &bull; Direct cash refund disbursed
                        @endif
                    </small>
                </div>
            </div>
            @if($return->walletTransaction)
                <div>
                    <a href="{{ route('admin.dealer-wallets.show', $return->party_id) }}" class="btn btn-sm btn-outline-primary font-weight-bold" style="border-radius: 6px; font-size: 12px;">
                        <i data-feather="credit-card" style="width: 13px; height: 13px; margin-right: 4px;"></i> View Wallet Ledger (#{{ $return->walletTransaction->transaction_no }})
                    </a>
                </div>
            @endif
        </div>
    @elseif($return->status === 'rejected')
        <div class="alert alert-danger p-2 mb-2 d-flex align-items-center" style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 8px;">
            <i data-feather="alert-triangle" style="width: 22px; height: 22px; color: #dc2626; margin-right: 12px;"></i>
            <div>
                <h6 class="mb-0 font-weight-bolder text-danger">Return Request Rejected</h6>
                <small class="text-muted">
                    Rejected on {{ $return->rejected_at ? $return->rejected_at->format('M d, Y h:i A') : '-' }} by {{ $return->rejector ? $return->rejector->name : 'Admin' }}
                    &bull; <strong>Reason:</strong> {{ $return->reject_reason ?: 'No reason specified' }}
                </small>
            </div>
        </div>
    @endif

    <!-- Information Cards -->
    <div class="row">
        <!-- Party Details -->
        <div class="col-md-6 mb-2">
            <div class="card return-detail-card shadow-none h-100">
                <div class="card-header border-bottom py-1 px-3">
                    <h5 class="card-title mb-0 font-weight-bolder" style="color: #111C38;">
                        <i data-feather="user" class="me-1"></i> Customer &amp; Order Details
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-1">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: #111C38; color: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; margin-right: 12px;">
                            {{ strtoupper(substr($return->customer_name ?: 'P', 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="mb-0 font-weight-bold text-dark">{{ $return->customer_name ?: 'General Party' }}</h5>
                            <small class="text-muted"><i data-feather="phone" style="width: 12px; height: 12px;"></i> {{ $return->customer_phone ?: 'No Phone' }}</small>
                        </div>
                    </div>

                    <div class="mt-2 pt-1 border-top">
                        <div class="d-flex justify-content-between mb-50">
                            <span class="text-muted font-small-3 font-weight-bold">Sales Channel:</span>
                            <span>{!! $return->channel_badge !!}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-50">
                            <span class="text-muted font-small-3 font-weight-bold">Referenced Invoices:</span>
                            <div>
                                @php
                                    $uniqueInvoices = $return->items->pluck('order_number')->unique();
                                @endphp
                                @foreach($uniqueInvoices as $inv)
                                    <span class="badge badge-light-primary font-small-1 font-weight-bold me-25">{{ $inv }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted font-small-3 font-weight-bold">Inventory Restocked:</span>
                            <span>
                                @if($return->stock_restocked)
                                    <span class="badge badge-light-success font-small-1"><i data-feather="check" style="width: 10px; height: 10px;"></i> Yes (Restocked)</span>
                                @else
                                    <span class="badge badge-light-secondary font-small-1">Pending Approval</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Summary -->
        <div class="col-md-6 mb-2">
            <div class="card return-detail-card shadow-none h-100">
                <div class="card-header border-bottom py-1 px-3">
                    <h5 class="card-title mb-0 font-weight-bolder" style="color: #111C38;">
                        <i data-feather="dollar-sign" class="me-1"></i> Refund &amp; Financial Summary
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted font-small-3 font-weight-bold">Total Products Returned:</span>
                        <span class="font-weight-bold text-dark font-small-3">{{ $return->total_items_count }} item(s) ({{ $return->total_qty }} units)</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted font-small-3 font-weight-bold">Products Subtotal:</span>
                        <span class="font-weight-bold text-dark font-small-3">৳{{ number_format($return->subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted font-small-3 font-weight-bold">Restocking Fee / Deduction:</span>
                        <span class="font-weight-bold text-danger font-small-3">-৳{{ number_format($return->deduction_amount, 2) }}</span>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="font-weight-bolder text-dark font-small-3 text-uppercase">Net Refund Amount:</span>
                        <h4 class="font-weight-bolder text-success mb-0">৳{{ number_format($return->refund_amount, 2) }}</h4>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted font-small-3 font-weight-bold">Refund Method:</span>
                        <span>{!! $return->refund_type_badge !!}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Return Notes Section (Highlighted) -->
    <div class="card return-detail-card mb-2 shadow-none" style="border-left: 4px solid #111C38;">
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-{{ $return->admin_review_note ? '6' : '12' }}">
                    <h6 class="font-weight-bolder text-dark mb-50">
                        <i data-feather="file-text" style="width: 15px; height: 15px; color: #111C38; margin-right: 4px;"></i>
                        Master Return Note (Added during creation):
                    </h6>
                    @if(!empty($return->return_note))
                        <div class="p-2 rounded font-small-3" style="background-color: #ffffff; border: 1px solid #e2e8f0; color: #1e293b; line-height: 1.5;">
                            {{ $return->return_note }}
                        </div>
                    @else
                        <span class="text-muted font-small-2 font-italic">No general return note recorded.</span>
                    @endif
                </div>

                @if(!empty($return->admin_review_note))
                    <div class="col-md-6 mt-1 mt-md-0">
                        <h6 class="font-weight-bolder text-dark mb-50">
                            <i data-feather="shield" style="width: 15px; height: 15px; color: #111C38; margin-right: 4px;"></i>
                            Admin Review / Authorization Note:
                        </h6>
                        <div class="p-2 rounded font-small-3" style="background-color: #ffffff; border: 1px solid #e2e8f0; color: #1e293b; line-height: 1.5;">
                            {{ $return->admin_review_note }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Returned Product Items Table -->
    <div class="card return-detail-card mb-2 shadow-none">
        <div class="card-header border-bottom py-1 px-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 font-weight-bolder" style="color: #111C38;">
                <i data-feather="box" class="me-1"></i> Returned Items &amp; Item Reasons
            </h5>
            <small class="text-muted">{{ count($return->items) }} item(s)</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table return-products-table w-100 mb-0">
                    <thead>
                        <tr>
                            <th style="width: 45px;">#</th>
                            <th>PRODUCT</th>
                            <th style="width: 130px;">INVOICE</th>
                            <th style="width: 90px;" class="text-center">DELIVERED</th>
                            <th style="width: 90px;" class="text-center">RET. QTY</th>
                            <th style="width: 110px;" class="text-end">UNIT PRICE</th>
                            <th style="width: 110px;" class="text-end">SUBTOTAL</th>
                            <th style="width: 110px;">CONDITION</th>
                            <th style="width: 90px;" class="text-center">RESTOCK</th>
                            <th>ITEM NOTE / REASON</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($return->items as $idx => $item)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @php
                                            $imgUrl = asset('images/no-image.png');
                                            if ($item->product && !empty($item->product->featured_image)) {
                                                $imgUrl = asset('images/product/' . ltrim($item->product->featured_image, '/'));
                                            }
                                        @endphp
                                        <img src="{{ $imgUrl }}" alt="{{ $item->product_name }}" class="rounded me-1 shadow-sm" style="width: 44px; height: 44px; object-fit: cover; border: 1px solid #e2e8f0; margin-right: 10px; flex-shrink: 0; background: #ffffff;" onerror="this.src='{{ asset('images/no-image.png') }}';">
                                        <div>
                                            <span class="font-weight-bolder text-dark font-small-3 d-block">{{ $item->product_name }}</span>
                                            @if(!empty($item->product_code) && $item->product_code !== 'null' && $item->product_code !== '-')
                                                <small class="text-muted font-small-1">Code: {{ $item->product_code }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-light-primary font-small-1 font-weight-bold">{{ $item->order_number }}</span>
                                </td>
                                <td class="text-center text-muted font-small-3">{{ $item->ordered_qty }}</td>
                                <td class="text-center font-weight-bolder text-dark font-small-3">{{ $item->return_qty }}</td>
                                <td class="text-end text-dark font-small-3">৳{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end font-weight-bold text-dark font-small-3">৳{{ number_format($item->subtotal, 2) }}</td>
                                <td>
                                    @if($item->condition === 'good')
                                        <span class="badge badge-light-success font-small-1">Good</span>
                                    @elseif($item->condition === 'damaged')
                                        <span class="badge badge-light-danger font-small-1">Damaged</span>
                                    @else
                                        <span class="badge badge-light-warning font-small-1">{{ ucfirst($item->condition) }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->restock)
                                        <span class="text-success"><i data-feather="check" style="width: 14px; height: 14px;"></i></span>
                                    @else
                                        <span class="text-muted">&times;</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($item->item_note))
                                        <span class="text-dark font-small-3 font-weight-bold">{{ $item->item_note }}</span>
                                    @else
                                        <span class="text-muted font-small-2 font-italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Approve Confirmation Modal -->
<div class="modal fade" id="approveReturnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.returns.approve', $return->id) }}" class="modal-content" style="border-radius: 10px; border: 1px solid #e2e8f0;">
            @csrf
            <div class="modal-header py-1" style="background-color: #111C38; color: #ffffff; border-top-left-radius: 9px; border-top-right-radius: 9px; display: flex; align-items: center; justify-content: space-between;">
                <h5 class="modal-title text-white font-weight-bold mb-0">
                    <i data-feather="check-circle" style="width: 16px; height: 16px; margin-right: 6px;"></i> Approve Return #{{ $return->return_no }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 26px; line-height: 1; color: #ffffff; opacity: 0.9; cursor: pointer; padding: 0;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="alert alert-primary p-2 mb-2" style="background-color: #f0f4fa; border: 1px solid #d4e2f4; color: #111C38; border-radius: 8px;">
                    <div class="font-weight-bold font-small-3">Total Net Refund: <span class="font-weight-bolder text-success">৳{{ number_format($return->refund_amount, 2) }}</span></div>
                    <div class="font-small-2 mt-25">
                        @if($return->refund_type === 'wallet')
                            <strong>Refund Method: Dealer Wallet Credit</strong> (Amount will be automatically added to the dealer wallet and logged in wallet transaction history).
                        @else
                            <strong>Refund Method: Cash Refund</strong>
                        @endif
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label font-weight-bold text-dark font-small-3 mb-25">Admin Approval Review Note (Optional):</label>
                    <textarea name="admin_review_note" class="form-control" rows="2" placeholder="e.g. Products inspected and accepted back into warehouse..."></textarea>
                </div>

                <p class="text-muted font-small-2 mb-0">
                    <i data-feather="info" style="width: 12px; height: 12px; margin-right: 3px;"></i>
                    Approving this return will mark items as returned, synchronize sellable stock, and credit wallet (if dealer wallet selected).
                </p>
            </div>
            <div class="modal-footer py-1 border-top bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 6px; font-weight: 600;">Cancel</button>
                <button type="submit" class="btn btn-sm btn-success" style="background-color: #28c76f; border-color: #28c76f; border-radius: 6px; font-weight: 600;">
                    <i data-feather="check" style="width: 14px; height: 14px; margin-right: 4px;"></i> Confirm Approval
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Confirmation Modal -->
<div class="modal fade" id="rejectReturnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('admin.returns.reject', $return->id) }}" class="modal-content" style="border-radius: 10px; border: 1px solid #e2e8f0;">
            @csrf
            <div class="modal-header py-1" style="background-color: #dc2626; color: #ffffff; border-top-left-radius: 9px; border-top-right-radius: 9px; display: flex; align-items: center; justify-content: space-between;">
                <h5 class="modal-title text-white font-weight-bold mb-0">
                    <i data-feather="x-circle" style="width: 16px; height: 16px; margin-right: 6px;"></i> Reject Return #{{ $return->return_no }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" style="background: transparent; border: none; font-size: 26px; line-height: 1; color: #ffffff; opacity: 0.9; cursor: pointer; padding: 0;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-2">
                    <label class="form-label font-weight-bold text-dark font-small-3 mb-25">Rejection Reason: <span class="text-danger">*</span></label>
                    <textarea name="reject_reason" class="form-control" rows="3" placeholder="Explain why this return request is being rejected (e.g. Return window expired, items missing original accessories)..." required></textarea>
                </div>
                <p class="text-muted font-small-2 mb-0">
                    <i data-feather="alert-circle" style="width: 12px; height: 12px; margin-right: 3px;"></i>
                    Rejecting this return will finalize the request with no refund issued and no inventory altered.
                </p>
            </div>
            <div class="modal-footer py-1 border-top bg-light">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" style="border-radius: 6px; font-weight: 600;">Cancel</button>
                <button type="submit" class="btn btn-sm btn-danger" style="background-color: #dc2626; border-color: #dc2626; border-radius: 6px; font-weight: 600;">
                    <i data-feather="x" style="width: 14px; height: 14px; margin-right: 4px;"></i> Confirm Rejection
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Ensure all modal close buttons work reliably in BS4 / BS5
    $(document).on('click', '.modal [data-dismiss="modal"], .modal [data-bs-dismiss="modal"], .modal .close', function(e) {
        e.preventDefault();
        $(this).closest('.modal').modal('hide');
    });
});
</script>
@endpush
