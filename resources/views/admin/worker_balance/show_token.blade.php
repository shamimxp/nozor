@extends('layouts.admin')
@section('title', $page_title ?? 'Payment Token')

@push('styles')
<style>
    /* ── Cheque Styling ── */
    .cheque-wrapper {
        background: #f1f5f9;
        padding: 30px 15px;
    }
    .bank-cheque {
        max-width: 1376px;
        margin: 0 auto;
        background: #ffffff;
        background-image:
            radial-gradient(#e2e8f0 0.75px, transparent 0.75px),
            radial-gradient(#e2e8f0 0.75px, #ffffff 0.75px);
        background-size: 24px 24px;
        background-position: 0 0, 12px 12px;
        border: 2px solid #334155;
        border-radius: 12px;
        padding: 28px 32px;
        position: relative;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }
    .cheque-inner-border {
        border: 1px dashed #94a3b8;
        padding: 22px;
        border-radius: 8px;
        position: relative;
    }
    .cheque-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-25deg);
        font-size: 4rem;
        font-weight: 900;
        color: rgba(148, 163, 184, 0.08);
        text-transform: uppercase;
        pointer-events: none;
        white-space: nowrap;
        letter-spacing: 6px;
        z-index: 1;
    }
    .date-boxes {
        display: inline-flex;
        border: 1px solid #334155;
        border-radius: 4px;
        overflow: hidden;
    }
    .date-box {
        width: 22px;
        height: 26px;
        border-right: 1px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        color: #0f172a;
        background: #fff;
    }
    .date-box:last-child {
        border-right: none;
    }
    .underline-fill {
        border-bottom: 1.5px dotted #334155;
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        color: #0f172a;
        padding: 2px 8px;
        display: inline-block;
        word-break: break-word;
    }
    .amount-box {
        border: 2px solid #0f172a;
        background: #f8fafc;
        border-radius: 6px;
        padding: 8px 16px;
        font-size: 1.35rem;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: 1px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        white-space: nowrap;
    }
    .micr-line {
        font-family: 'Courier New', Courier, monospace;
        font-size: 13px;
        letter-spacing: 4px;
        color: #475569;
        text-align: center;
        margin-top: 25px;
        padding-top: 10px;
        border-top: 1px dashed #cbd5e1;
        overflow-x: auto;
        white-space: nowrap;
    }
    .sig-line {
        border-top: 1px solid #334155;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #475569;
        padding-top: 4px;
        margin-top: 45px;
    }

    @media (max-width: 767.98px) {
        .cheque-wrapper {
            padding: 15px 5px;
        }
        .bank-cheque {
            padding: 16px 12px;
            border-radius: 8px;
        }
        .cheque-inner-border {
            padding: 14px 10px;
        }
        .cheque-watermark {
            font-size: 2rem;
            letter-spacing: 3px;
        }
        .date-box {
            width: 18px;
            height: 22px;
            font-size: 11px;
        }
        .amount-box {
            font-size: 1.15rem;
            padding: 6px 12px;
        }
        .sig-line {
            margin-top: 30px;
            font-size: 10px;
        }
        .micr-line {
            font-size: 10px;
            letter-spacing: 2px;
        }
    }

    @media print {
        body * {
            visibility: hidden;
        }
        .cheque-printable-area, .cheque-printable-area * {
            visibility: visible;
        }
        .cheque-printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            background: none;
        }
        .no-print {
            display: none !important;
        }
        .bank-cheque {
            box-shadow: none !important;
            border: 2px solid #000 !important;
            max-width: 100% !important;
        }
    }
</style>
@endpush

@section('content')
<div class="row no-print mb-1">
    <div class="col-12 d-flex flex-wrap justify-content-between align-items-center">
        <a href="{{ route('admin.worker-payouts.index') }}" class="btn btn-outline-secondary btn-sm mb-50 mb-sm-0">
            <i data-feather="arrow-left" class="mr-25"></i> Back to Payment List
        </a>
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.worker-payouts.download', $payout->id) }}" class="btn btn-success btn-sm mr-50">
                <i data-feather="download" class="mr-25"></i> Download Payment Token
            </a>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                <i data-feather="printer" class="mr-25"></i> Print Payment Token
            </button>
        </div>
    </div>
</div>

<div class="cheque-wrapper cheque-printable-area">
    <!-- Authentic Bank Cheque / Payment Voucher -->
    <div class="bank-cheque">
        <div class="cheque-watermark">WOOD MACHINERY</div>

        <div class="cheque-inner-border">
            <!-- Cheque Header -->
            <div class="row align-items-center mb-1">
                <div class="col-12 col-md-7 mb-1 mb-md-0">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('admin/app-assets/images/logo/edited_red_letters.svg') }}" style="max-height: 44px; margin-right: 12px;" alt="Logo" onerror="this.style.display='none'">
                        <div>
                            <h5 class="font-weight-bolder text-dark mb-0" style="letter-spacing: 0.5px;">WOOD MACHINERY &amp; HARDWARE</h5>
                            <span class="small text-muted font-weight-bold text-uppercase" style="font-size: 10px; letter-spacing: 1px;">Industrial Worker Payout Token / Voucher</span>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-5 text-md-right text-left">
                    <div class="mb-50">
                        <span class="badge badge-light-primary font-weight-bolder" style="font-size: 12px; padding: 5px 10px;">
                            TOKEN NO: {{ $payout->payment_number }}
                        </span>
                    </div>
                    <div class="d-flex align-items-center justify-content-md-end justify-content-start">
                        <span class="small font-weight-bold mr-50 text-muted">DATE:</span>
                        @php
                            $d = $payout->payment_date ? $payout->payment_date->format('d') : date('d');
                            $m = $payout->payment_date ? $payout->payment_date->format('m') : date('m');
                            $y = $payout->payment_date ? $payout->payment_date->format('Y') : date('Y');
                        @endphp
                        <div class="date-boxes align-middle">
                            <span class="date-box">{{ $d[0] }}</span>
                            <span class="date-box">{{ $d[1] }}</span>
                            <span class="date-box" style="background:#f1f5f9; width:4px;"></span>
                            <span class="date-box">{{ $m[0] }}</span>
                            <span class="date-box">{{ $m[1] }}</span>
                            <span class="date-box" style="background:#f1f5f9; width:4px;"></span>
                            <span class="date-box">{{ $y[0] }}</span>
                            <span class="date-box">{{ $y[1] }}</span>
                            <span class="date-box">{{ $y[2] }}</span>
                            <span class="date-box">{{ $y[3] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <hr style="border-top: 1.5px solid #0f172a; margin: 12px 0 16px 0;">

            <!-- Payee Row -->
            <div class="mb-2 d-flex flex-column flex-sm-row align-items-start align-items-sm-baseline">
                <span class="font-weight-bold text-uppercase mb-25 mb-sm-0" style="font-size: 11px; letter-spacing: 1px; min-width: 150px; flex-shrink: 0; color: #475569;">
                    Pay To The Order Of:
                </span>
                <span class="underline-fill flex-grow-1 w-100" style="font-size: 1.15rem; color: #1e293b;">
                    {{ $payout->worker->name ?? 'N/A' }}
                    @if($payout->worker && $payout->worker->phone)
                        <span style="font-size: 0.85rem; font-weight: 600; color: #64748b;">(Phone: {{ $payout->worker->phone }})</span>
                    @endif
                </span>
            </div>

            <!-- Amount in Words & Figure Box Row -->
            <div class="row align-items-center mb-2">
                <div class="col-12 col-md-8 mb-1 mb-md-0">
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-baseline">
                        <span class="font-weight-bold text-uppercase mb-25 mb-sm-0" style="font-size: 11px; letter-spacing: 1px; min-width: 150px; flex-shrink: 0; color: #475569;">
                            The Sum Of (Taka):
                        </span>
                        <span class="underline-fill flex-grow-1 w-100 font-italic" style="font-size: 0.95rem; color: #0f172a;">
                            {{ $amountInWords }}
                        </span>
                    </div>
                </div>
                <div class="col-12 col-md-4 text-md-right text-left">
                    <div class="amount-box d-inline-block">
                        <span style="font-size: 1.1rem; color: #64748b; margin-right: 4px;">৳</span>
                        {{ number_format($payout->amount, 2) }} /-
                    </div>
                </div>
            </div>

            <!-- Payment Method & Balance Details Strip -->
            <div class="p-1 mb-2 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <div class="row align-items-center">
                    <div class="col-12 col-md-4 mb-1 mb-md-0">
                        <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10px;">Disbursed Via</small>
                        <div class="d-flex align-items-center mt-25">
                            @php
                                $method = $payout->payment_method;
                                $logo = asset('images/payments/' . strtolower($method) . '.svg');
                            @endphp
                            <img src="{{ $logo }}" alt="{{ $method }}" style="width: 22px; height: 22px; margin-right: 8px; object-fit: contain;">
                            <strong class="text-dark">{{ $method }}</strong>
                            @if($payout->account_details)
                                <span class="small text-muted ml-50">({{ $payout->account_details }})</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-8">
                        <div class="d-flex flex-wrap justify-content-md-end justify-content-between align-items-center">
                            <div class="mr-md-2 mb-50 mb-md-0">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10px;">Previous Due</small>
                                <span class="font-weight-bold text-dark">৳ {{ number_format($payout->total_balance, 2) }}</span>
                            </div>
                            <div class="mr-md-2 mb-50 mb-md-0">
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10px;">Amount Paid</small>
                                <span class="font-weight-bolder text-success">৳ {{ number_format($payout->amount, 2) }}</span>
                            </div>
                            <div>
                                <small class="text-muted text-uppercase font-weight-bold d-block" style="font-size: 10px;">Remaining Due</small>
                                <span class="font-weight-bold text-danger">৳ {{ number_format($payout->remaining_balance, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @if($payout->note)
                    <div class="mt-50 pt-50 border-top small text-muted">
                        <strong>Note:</strong> {{ $payout->note }}
                    </div>
                @endif
            </div>

            <!-- Signature Lines -->
            <div class="row pt-1 pt-md-2">
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <div class="sig-line">
                        Prepared By<br>
                        <small class="text-muted font-weight-normal">{{ $payout->creator->name ?? 'Admin' }}</small>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <div class="sig-line">
                        Checked By<br>
                        <small class="text-muted font-weight-normal">Accounts Dept</small>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <div class="sig-line">
                        Authorized Signatory<br>
                        <small class="text-muted font-weight-normal">Management</small>
                    </div>
                </div>
                <div class="col-6 col-md-3 mb-2 mb-md-0">
                    <div class="sig-line">
                        Payee Signature<br>
                        <small class="text-muted font-weight-normal">{{ $payout->worker->name ?? 'Worker' }}</small>
                    </div>
                </div>
            </div>

            <!-- Security MICR Code Band -->
            <div class="micr-line">
                ⑈ {{ str_pad($payout->id, 6, '0', STR_PAD_LEFT) }} ⑈ 012270034 ⑈ {{ str_pad($payout->worker_id, 8, '0', STR_PAD_LEFT) }} ⑈ 10
            </div>
        </div>
    </div>

    <!-- Settlement Breakdown Table -->
    @if($payout->items->count() > 0)
    <div class="card mt-2 shadow-sm border-0 no-print" style="max-width: 1000px; margin: 20px auto 0 auto; border-radius: 10px;">
        <div class="card-header bg-light py-1 border-bottom">
            <h6 class="mb-0 font-weight-bolder text-dark">
                <i data-feather="list" class="mr-25"></i> Settled Manufacture Invoices
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0">
                    <thead class="bg-light font-weight-bold">
                        <tr>
                            <th>SL</th>
                            <th>Invoice Date</th>
                            <th>Manufacture Order No</th>
                            <th>Total Invoice Amount</th>
                            <th>Amount Paid by this Token</th>
                            <th>Invoice Status Now</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payout->items as $idx => $item)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $item->invoice && $item->invoice->date ? date('d M Y', strtotime($item->invoice->date)) : 'N/A' }}</td>
                            <td>
                                @if($item->invoice && $item->invoice->manufacture)
                                    <span class="badge badge-light-primary font-weight-bold">
                                        {{ $item->invoice->manufacture->invoice_no }}
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>৳ {{ number_format($item->invoice ? $item->invoice->total_amount : 0, 2) }}</td>
                            <td class="font-weight-bolder text-success">৳ {{ number_format($item->amount, 2) }}</td>
                            <td>
                                @php
                                    $st = $item->invoice ? $item->invoice->status : 'pending';
                                @endphp
                                @if($st === 'paid')
                                    <span class="badge badge-light-success font-weight-bold">Paid</span>
                                @elseif($st === 'partially_paid')
                                    <span class="badge badge-light-info font-weight-bold">Partially Paid (Due: ৳{{ number_format($item->invoice->due_amount, 2) }})</span>
                                @else
                                    <span class="badge badge-light-warning font-weight-bold">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
