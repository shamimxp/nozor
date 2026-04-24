@extends('layouts.admin')
@section('title', 'Purchase Details - ' . $purchase->purchase_number)
@section('content')
<div class="row">
    <div class="col-md-9">
        <div class="card invoice-preview-card">
            <div class="card-body">
                <div class="d-flex justify-content-between flex-md-row flex-column">
                    <div>
                        <h4 class="text-primary">Purchase Order: {{ $purchase->purchase_number }}</h4>
                        <p class="mb-0">Style No: <strong>{{ $purchase->style_number }}</strong></p>
                        <p class="mb-0">Date: <strong>{{ $purchase->created_at->format('d M, Y') }}</strong></p>
                        <p class="mb-0">Received Date: <strong>{{ $purchase->received_date ? $purchase->received_date->format('d M, Y') : '-' }}</strong></p>
                    </div>
                    <div class="mt-md-0 mt-2">
                        <span class="badge badge-light-{{ $purchase->status == 'received' ? 'success' : ($purchase->status == 'confirm' ? 'info' : 'warning') }} text-uppercase">
                            {{ $purchase->status }}
                        </span>
                    </div>
                </div>
            </div>

            <hr class="my-0">

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-1">Vendor:</h6>
                        <p class="mb-25"><strong>{{ $purchase->vendor->name }}</strong></p>
                        <p class="mb-25">{{ $purchase->vendor->company_name }}</p>
                        <p class="mb-25">{{ $purchase->vendor->phone }}</p>
                        <p class="mb-25">{{ $purchase->vendor->email }}</p>
                    </div>
                    <div class="col-md-6 text-md-right">
                        <h6 class="mb-1">Reference:</h6>
                        <p class="mb-25">Custom Order: <strong>#{{ $purchase->customOrder->order_number }}</strong></p>
                        <p class="mb-25">Created By: {{ $purchase->creator->name ?? 'System' }}</p>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item Details</th>
                            <th>Qty</th>
                            <th>Cost</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $item)
                        <tr>
                            <td>{{ $item->fabricPrice->fabric->name ?? '-' }} | {{ strtoupper($item->fabricPrice->type) }} | {{ strtoupper($item->fabricPrice->sleeve) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>৳{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="text-right">৳{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-7"></div>
                    <div class="col-md-5">
                        <div class="invoice-total-item d-flex justify-content-between mb-50">
                            <p class="mb-0">Subtotal:</p>
                            <p class="mb-0">৳{{ number_format($purchase->sub_total, 2) }}</p>
                        </div>
                        <div class="invoice-total-item d-flex justify-content-between mb-50">
                            <p class="mb-0">Carrying Charge:</p>
                            <p class="mb-0">৳{{ number_format($purchase->carrying_charge, 2) }}</p>
                        </div>
                        <hr class="my-50">
                        <div class="invoice-total-item d-flex justify-content-between mb-50">
                            <p class="mb-0 font-weight-bold">Grand Total:</p>
                            <p class="mb-0 font-weight-bold">৳{{ number_format($purchase->grand_total, 2) }}</p>
                        </div>
                        <div class="invoice-total-item d-flex justify-content-between mb-50">
                            <p class="mb-0 text-success">Paid Amount:</p>
                            <p class="mb-0 text-success">৳{{ number_format($purchase->paid_amount, 2) }}</p>
                        </div>
                        <div class="invoice-total-item d-flex justify-content-between mb-0">
                            <p class="mb-0 text-danger">Due Amount:</p>
                            <p class="mb-0 text-danger font-weight-bold">৳{{ number_format($purchase->due_amount, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-header border-bottom">
                <h4 class="card-title">Vendor Payment History</h4>
            </div>
            <div class="card-body p-0">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchase->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('d M, Y') }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>৳{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->note }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center p-2">No payments made yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                @if($purchase->due_amount > 0)
                <button class="btn btn-success btn-block mb-1" data-toggle="modal" data-target="#paymentModal">
                    Add Payment
                </button>
                @endif
                <button class="btn btn-outline-primary btn-block mb-1" onclick="window.print()">
                    Print Invoice
                </button>
                <a href="{{ route('admin.purchase.index') }}" class="btn btn-outline-secondary btn-block">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Payment for {{ $purchase->purchase_number }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="paymentForm">
                @csrf
                <input type="hidden" name="purchase_id" value="{{ $purchase->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Due Amount</label>
                        <input type="text" class="form-control" value="৳{{ number_format($purchase->due_amount, 2) }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="amount">Payment Amount</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control" max="{{ $purchase->due_amount }}" required>
                    </div>
                    <div class="form-group">
                        <label for="payment_date">Payment Date</label>
                        <input type="date" name="payment_date" id="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control">
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                            <option value="Mobile Banking">Mobile Banking</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="note">Note (Optional)</label>
                        <textarea name="note" id="note" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="savePaymentBtn">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#paymentForm').on('submit', function(e) {
        e.preventDefault();
        $('#savePaymentBtn').prop('disabled', true).text('Saving...');

        $.ajax({
            url: "{{ route('admin.purchase.add-payment') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                if (res.success) {
                    toastr.success(res.message);
                    location.reload();
                } else {
                    toastr.error(res.message);
                    $('#savePaymentBtn').prop('disabled', false).text('Save Payment');
                }
            },
            error: function(xhr) {
                toastr.error('Something went wrong!');
                $('#savePaymentBtn').prop('disabled', false).text('Save Payment');
            }
        });
    });
</script>
<style>
    @media print {
        .main-menu, .header-navbar, .footer, .btn, .col-md-3, .breadcrumb-wrapper {
            display: none !important;
        }
        .col-md-9 {
            width: 100% !important;
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
        .content-wrapper {
            margin: 0 !important;
            padding: 0 !important;
        }
        .card {
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>
@endpush
