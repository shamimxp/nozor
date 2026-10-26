<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Payment Token - {{ $payout->payment_number }}</title>
    <style>
        @font-face {
            font-family: 'NotoSansBengali';
            font-style: normal;
            font-weight: normal;
            src: url('{{ public_path("fonts/NotoSansBengali-Regular.ttf") }}') format("truetype");
        }
        @font-face {
            font-family: 'NotoSansBengali';
            font-style: normal;
            font-weight: bold;
            src: url('{{ public_path("fonts/NotoSansBengali-Bold.ttf") }}') format("truetype");
        }

        @page {
            size: 842pt 360pt;
            margin: 12pt;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'NotoSansBengali', 'DejaVu Sans', sans-serif;
            color: #0f172a;
            font-size: 11px;
            line-height: 1.3;
            background-color: #ffffff;
        }

        .cheque-outer {
            border: 2px solid #334155;
            border-radius: 10px;
            padding: 10px;
            background-color: #ffffff;
            position: relative;
            box-sizing: border-box;
        }

        .cheque-inner {
            border: 1px dashed #94a3b8;
            border-radius: 8px;
            padding: 14px 18px 12px 18px;
            background-color: #ffffff;
            position: relative;
            box-sizing: border-box;
        }

        .bg-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .content-layer {
            position: relative;
            z-index: 1;
        }

        /* ── Header ── */
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-img {
            height: 40px;
            vertical-align: middle;
            margin-right: 10px;
        }
        .company-name {
            font-size: 15px;
            font-weight: bold;
            color: #1e293b;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .company-sub {
            font-size: 8px;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .token-badge {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: bold;
            font-size: 9.5px;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            display: inline-block;
        }

        .date-table {
            border-collapse: collapse;
            display: inline-table;
            vertical-align: middle;
            margin-left: 4px;
        }
        .date-cell {
            border: 1px solid #334155;
            width: 15px;
            height: 17px;
            text-align: center;
            font-size: 9.5px;
            font-weight: bold;
            color: #0f172a;
            background-color: #ffffff;
        }
        .date-sep {
            width: 4px;
            background-color: #f1f5f9;
        }

        .divider {
            border-bottom: 1.5px solid #1e293b;
            margin: 8px 0 12px 0;
        }

        /* ── Payee & Amount Rows ── */
        .line-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .line-label {
            width: 145px;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }
        .line-fill {
            border-bottom: 1px dotted #64748b;
            font-size: 13px;
            font-weight: bold;
            color: #1e293b;
            padding-bottom: 2px;
            vertical-align: middle;
        }
        .amount-words {
            font-style: italic;
            font-size: 12px;
            color: #1e293b;
            vertical-align: middle;
        }

        .amount-box {
            border: 2px solid #0f172a;
            background-color: #f8fafc;
            border-radius: 6px;
            padding: 5px 12px;
            white-space: nowrap;
            display: inline-block;
            text-align: right;
        }

        /* ── Disbursed Strip ── */
        .strip-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            margin: 8px 0 10px 0;
        }
        .strip-table td {
            padding: 8px 12px;
            vertical-align: middle;
        }
        .strip-lbl {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #94a3b8;
            display: block;
            margin-bottom: 2px;
        }
        .strip-val {
            font-size: 12.5px;
            font-weight: bold;
            color: #0f172a;
            white-space: nowrap;
        }

        /* ── Signatures ── */
        .sig-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        .sig-cell {
            width: 22%;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            border-top: 1px solid #334155;
            padding-top: 4px;
            color: #334155;
            vertical-align: top;
        }
        .sig-gap {
            width: 4%;
        }

        /* ── MICR ── */
        .micr-text {
            text-align: center;
            letter-spacing: 5px;
            font-size: 10px;
            color: #64748b;
            margin-top: 12px;
            white-space: nowrap;
        }
    </style>
</head>
<body>

    <!-- Authentic Bank Cheque Voucher -->
    <div class="cheque-outer">
        <div class="cheque-inner">

            <!-- Background Pattern & Diagonal Watermark -->
            <div class="bg-layer">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="dotPattern" width="20" height="20" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="0.75" fill="#cbd5e1" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#dotPattern)" />
                    <text x="180" y="220"
                          font-size="64"
                          font-family="'NotoSansBengali', sans-serif"
                          font-weight="bold"
                          fill="#64748b"
                          fill-opacity="0.08"
                          transform="rotate(-15 180 220)"
                          letter-spacing="10">WOOD MACHINERY</text>
                </svg>
            </div>

            <div class="content-layer">
                <!-- Cheque Header -->
                <table class="header-table">
                    <tr>
                        <td style="width: 60%; vertical-align: middle;">
                            <table style="border-collapse: collapse;">
                                <tr>
                                    @php
                                        $logoPath = public_path('admin/app-assets/images/logo/edited_red_letters.svg');
                                    @endphp
                                    @if(file_exists($logoPath))
                                        <td style="vertical-align: middle; padding-right: 10px;">
                                            <img src="{{ $logoPath }}" class="logo-img" alt="Logo">
                                        </td>
                                    @endif
                                    <td style="vertical-align: middle;">
                                        <div class="company-name">WOOD MACHINERY &amp; HARDWARE</div>
                                        <div class="company-sub">INDUSTRIAL WORKER PAYOUT TOKEN / VOUCHER</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 40%; text-align: right; vertical-align: middle;">
                            <div style="margin-bottom: 4px;">
                                <span class="token-badge">TOKEN NO: {{ $payout->payment_number }}</span>
                            </div>
                            <div>
                                <span style="font-size: 9px; font-weight: bold; color: #94a3b8; vertical-align: middle;">DATE:</span>
                                @php
                                    $d = $payout->payment_date ? $payout->payment_date->format('d') : date('d');
                                    $m = $payout->payment_date ? $payout->payment_date->format('m') : date('m');
                                    $y = $payout->payment_date ? $payout->payment_date->format('Y') : date('Y');
                                @endphp
                                <table class="date-table">
                                    <tr>
                                        <td class="date-cell">{{ $d[0] }}</td>
                                        <td class="date-cell">{{ $d[1] }}</td>
                                        <td class="date-sep"></td>
                                        <td class="date-cell">{{ $m[0] }}</td>
                                        <td class="date-cell">{{ $m[1] }}</td>
                                        <td class="date-sep"></td>
                                        <td class="date-cell">{{ $y[0] }}</td>
                                        <td class="date-cell">{{ $y[1] }}</td>
                                        <td class="date-cell">{{ $y[2] }}</td>
                                        <td class="date-cell">{{ $y[3] }}</td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>

                <div class="divider"></div>

                <!-- Payee Row -->
                <table class="line-table">
                    <tr>
                        <td class="line-label">PAY TO THE ORDER OF:</td>
                        <td class="line-fill">
                            <span style="font-size: 13.5px; font-weight: bold; color: #1e293b;">{{ $payout->worker->name ?? 'N/A' }}</span>
                            @if($payout->worker && $payout->worker->phone)
                                <span style="font-size: 10px; color: #64748b; font-weight: normal; margin-left: 4px;">(Phone: {{ $payout->worker->phone }})</span>
                            @endif
                        </td>
                    </tr>
                </table>

                <!-- Amount in Words & Figure Box Row -->
                <table class="line-table">
                    <tr>
                        <td class="line-label">THE SUM OF (TAKA):</td>
                        <td class="line-fill amount-words">
                            {{ $amountInWords }}
                        </td>
                        <td style="width: 175px; text-align: right; vertical-align: middle; padding-left: 8px;">
                            <div class="amount-box">
                                <span style="color: #64748b; font-size: 12px; font-weight: bold; margin-right: 3px;">৳</span><span style="font-size: 14px; font-weight: bold; color: #0f172a;">{{ number_format($payout->amount, 2) }} /-</span>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Payment Method & Dues Strip -->
                <table class="strip-table">
                    <tr>
                        <td style="width: 32%; vertical-align: middle;">
                            <span class="strip-lbl">DISBURSED VIA</span>
                            <table style="border-collapse: collapse; margin-top: 2px;">
                                <tr>
                                    @php
                                        $methodName = $payout->payment_method;
                                        $methodIcon = public_path('images/payments/' . strtolower($methodName) . '.svg');
                                    @endphp
                                    @if(file_exists($methodIcon))
                                        <td style="vertical-align: middle; padding-right: 6px;">
                                            <img src="{{ $methodIcon }}" style="width: 20px; height: 20px; display: block;" alt="{{ $methodName }}">
                                        </td>
                                    @endif
                                    <td style="vertical-align: middle;">
                                        <strong style="font-size: 12.5px; color: #0f172a;">{{ $methodName }}</strong>
                                        @if($payout->account_details)
                                            <span style="font-size: 8.5px; color: #64748b; margin-left: 4px;">({{ $payout->account_details }})</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width: 20%;"></td>
                        <td style="width: 16%; text-align: right; vertical-align: middle;">
                            <span class="strip-lbl">PREVIOUS DUE</span>
                            <span class="strip-val">৳ {{ number_format($payout->total_balance, 2) }}</span>
                        </td>
                        <td style="width: 16%; text-align: right; vertical-align: middle;">
                            <span class="strip-lbl" style="color: #10b981;">AMOUNT PAID</span>
                            <span class="strip-val" style="color: #10b981;">৳ {{ number_format($payout->amount, 2) }}</span>
                        </td>
                        <td style="width: 16%; text-align: right; vertical-align: middle;">
                            <span class="strip-lbl" style="color: #ef4444;">REMAINING DUE</span>
                            <span class="strip-val" style="color: #ef4444;">৳ {{ number_format($payout->remaining_balance, 2) }}</span>
                        </td>
                    </tr>
                    @if($payout->note)
                    <tr>
                        <td colspan="5" style="border-top: 1px solid #e2e8f0; padding-top: 4px; font-size: 9px; color: #64748b;">
                            <strong>Note:</strong> {{ $payout->note }}
                        </td>
                    </tr>
                    @endif
                </table>

                <!-- Signature Lines -->
                <table class="sig-table">
                    <tr>
                        <td class="sig-cell">
                            PREPARED BY<br>
                            <span style="font-size: 8px; font-weight: normal; color: #94a3b8;">{{ $payout->creator->name ?? 'Admin' }}</span>
                        </td>
                        <td class="sig-gap"></td>
                        <td class="sig-cell">
                            CHECKED BY<br>
                            <span style="font-size: 8px; font-weight: normal; color: #94a3b8;">ACCOUNTS DEPT</span>
                        </td>
                        <td class="sig-gap"></td>
                        <td class="sig-cell">
                            AUTHORIZED SIGNATORY<br>
                            <span style="font-size: 8px; font-weight: normal; color: #94a3b8;">MANAGEMENT</span>
                        </td>
                        <td class="sig-gap"></td>
                        <td class="sig-cell">
                            PAYEE SIGNATURE<br>
                            <span style="font-size: 8px; font-weight: normal; color: #94a3b8;">{{ $payout->worker->name ?? 'Worker' }}</span>
                        </td>
                    </tr>
                </table>

                <!-- Security MICR Code Band -->
                <div class="micr-text">
                    ⑈ {{ str_pad($payout->id, 6, '0', STR_PAD_LEFT) }} ⑈ 012270034 ⑈ {{ str_pad($payout->worker_id, 8, '0', STR_PAD_LEFT) }} ⑈ 10
                </div>
            </div>

        </div>
    </div>

</body>
</html>
