<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #212529;
            margin-top: 4.5cm;
            margin-bottom: 3.5cm;
            margin-left: 1.5cm;
            margin-right: 1.5cm;
            font-size: 13px;
            line-height: 1.4;
        }

        /* Fixed Header and Footer */
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 4cm;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
        }

        .header-img, .footer-img {
            width: 100%;
            display: block;
        }

        /* Utility Classes */
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mb-4 { margin-bottom: 20px; }
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
        .mt-4 { margin-top: 20px; }
        .text-muted { color: #6c757d; }
        .w-100 { width: 100%; }

        /* Typography */
        .quote-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .quote-meta {
            font-size: 12px;
            color: #333;
        }

        .quote-hr {
            border: 0;
            border-bottom: 2px solid #1c53a0;
            margin: 10px 0 15px 0;
        }

        .info-title {
            color: #1c53a0;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        /* Boxes */
        .meta-box {
            background-color: #ffffff;
            border: 1px solid #d8dde3;
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 13px;
            line-height: 1.45;
        }

        .project-box {
            background-color: #eef6fb;
            border: 1px solid #d8dde3;
            border-radius: 4px;
            padding: 10px 14px;
            margin-bottom: 20px;
        }

        /* Tables */
        table { width: 100%; border-collapse: collapse; page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        
        .table-bordered th, .table-bordered td {
            border: 1px solid #d8dde3;
        }

        .table-primary th {
            background-color: #1c53a0;
            color: #ffffff;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            padding: 10px;
            text-align: left;
        }
        
        .table-bordered td {
            padding: 10px;
            vertical-align: top;
            font-size: 13px;
        }

        .table-light td {
            background-color: #1c53a0;
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

    @php
        $getImg = function($path) {
            $fullPath = public_path($path);
            if(file_exists($fullPath)) {
                $ext = pathinfo($fullPath, PATHINFO_EXTENSION);
                return 'data:image/'.$ext.';base64,' . base64_encode(file_get_contents($fullPath));
            }
            return '';
        };
    @endphp

    <!-- FIXED HEADER -->
    <header>
        <img src="{{ $getImg('images/header.jpeg') }}" class="header-img">
    </header>

    <!-- FIXED FOOTER -->
    <footer>
        <img src="{{ $getImg('images/footer.jpeg') }}" class="footer-img">
    </footer>

    <!-- MAIN CONTENT -->
    <main>
        
        <!-- LOGO & COMPANY INFO -->
        <table class="w-100 mb-2">
            <tr>
                <td style="width: 40%; vertical-align: top;">
                    <img src="{{ $getImg('images/logo.jpeg') }}" style="max-height: 100px;">
                </td>
                <td style="width: 60%; vertical-align: top;" class="text-end quote-meta">
                    <strong>ECO HYDROTECH SOLUTIONS SDN. BHD. (1688434-T)</strong><br>
                    Institute of Oceanography and Environment<br>
                    Universiti Malaysia Terengganu<br>
                    21030, Kuala Nerus, Terengganu<br>
                    Malaysia
                </td>
            </tr>
        </table>

        <!-- INVOICE TITLE + INVOICE NO / DATE -->
        <table class="w-100">
            <tr>
                <td style="vertical-align: bottom;">
                    <h2 class="quote-title">INVOICE</h2>
                </td>
                <td class="text-end quote-meta" style="vertical-align: bottom;">
                    <div>Invoice No: <strong>{{ $invoice->invoice_number }}</strong></div>
                    <div>Date: <strong>{{ $invoice->printed_date ? \Carbon\Carbon::parse($invoice->printed_date)->format('d F Y') : 'DRAFT' }}</strong></div>
                </td>
            </tr>
        </table>
        
        <hr class="quote-hr">

        <!-- BILL TO & INVOICE DETAILS GRID -->
        <table class="w-100 mb-4" style="border-collapse: separate; border-spacing: 12px 0; margin-left: -12px;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding: 0;">
                    <div class="meta-box" style="height: 100%;">
                        <div class="info-title">BILL TO</div>
                        <strong>{{ $invoice->quotation->project->client->company_name ?? '-' }}</strong><br>
                        <div>{!! nl2br(e($invoice->quotation->project->client->client_address ?? '-')) !!}</div>
                        <div class="mt-2">
                            <strong>Attn: {{ $invoice->quotation->project->pic_name ?? '-' }}</strong><br>
                            <span class="text-muted" style="font-size: 11px;">Project &amp; Admin Executive</span>
                        </div>
                    </div>
                </td>
                <td style="width: 50%; vertical-align: top; padding: 0;">
                    <div class="meta-box" style="height: 100%;">
                        <table class="w-100">
                            <tr>
                                <td style="width: 50%; vertical-align: top; padding-bottom: 8px;">
                                    <div class="info-title">Invoice Date</div>
                                    {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d F Y') : '-' }}
                                </td>
                                <td style="width: 50%; vertical-align: top; padding-bottom: 8px;">
                                    <div class="info-title">Due Date</div>
                                    {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d F Y') : '-' }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 50%; vertical-align: top; padding-bottom: 8px;">
                                    <div class="info-title">Payment Terms</div>
                                    @if($invoice->invoice_date && $invoice->due_date)
                                        Net {{ \Carbon\Carbon::parse($invoice->invoice_date)->diffInDays($invoice->due_date) }} Days
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="width: 50%; vertical-align: top; padding-bottom: 8px;">
                                    <div class="info-title">Your Ref.</div>
                                    {{ $invoice->po_number ?? ($invoice->quotation->po_number ?? '-') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="vertical-align: top;">
                                    <div class="info-title">Our Ref.</div>
                                    {{ $invoice->quotation->quotation_no }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- PROJECT DETAILS -->
        <div class="project-box">
            <span class="info-title">PROJECT</span><br>
            <strong>{{ $invoice->quotation->project->name }}</strong>
        </div>

        <!-- ITEMS TABLE -->
        <table class="table-bordered mb-4">
            <thead class="table-primary">
                <tr>
                    <th style="width: 5%; text-align: center;">No.</th>
                    <th style="width: 60%;">Description</th>
                    <th class="text-center" style="width: 10%;">Qty</th>
                    <th class="text-end" style="width: 25%;">Amount (RM)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>
                        <strong>{{ $invoice->paymentTerm->name }} ({{ $invoice->paymentTerm->percentage }}%)</strong><br>
                        {!! nl2br(e($invoice->description ?? $invoice->quotation->project->name)) !!}<br>
                        <small class="text-muted">Pursuant to Quotation Ref. {{ $invoice->quotation->quotation_no }}</small>
                    </td>
                    <td class="text-center">1</td>
                    <td class="text-end fw-bold">{{ number_format($invoice->paymentTerm->amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end fw-bold">Subtotal</td>
                    <td class="text-end fw-bold">{{ number_format($invoice->paymentTerm->amount, 2) }}</td>
                </tr>
                <tr class="table-light">
                    <td colspan="3" class="text-end fw-bold">TOTAL DUE</td>
                    <td class="text-end fw-bold">{{ number_format($invoice->paymentTerm->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- PAGE 2: PAYMENT TERMS AND SIGNATURE -->
        <div class="page-break"></div>

        <table class="w-100 mt-4 mb-4" style="background-color: #ffffff; border: 1px solid #d8dde3; border-radius: 4px;">
            <tr>
                <td style="width: 100%; padding: 15px;">
                    <div class="info-title mb-2">Payment Information</div>
                    <table class="w-100">
                        <tr>
                            <td style="width: 30%; font-weight: bold; padding-bottom: 5px;">Recipient</td>
                            <td style="width: 70%; padding-bottom: 5px;">ECO HYDROTECH SOLUTIONS SDN. BHD.</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding-bottom: 5px;">Bank</td>
                            <td style="padding-bottom: 5px;">MAYBANK ISLAMIC</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; padding-bottom: 5px;">Account Number</td>
                            <td style="padding-bottom: 5px;">5630 6496 5609</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold; vertical-align: top;">Bank's Address</td>
                            <td>1-j, Kuala Terengganu Branch, Kompleks Perdana, Jalan Air Jernih, 20300 Kuala Terengganu, Terengganu</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <div style="margin-top: 30px;">
            <p class="mb-4">Yours sincerely,</p>
            
            @if(isset($invoice->signatory) && $invoice->signatory->signature_path)
                @if(file_exists(public_path($invoice->signatory->signature_path)))
                    <img src="{{ $getImg($invoice->signatory->signature_path) }}" style="max-height: 80px; margin-bottom: 10px;">
                @endif
            @endif
            
            <p class="mb-0 fw-bold">{{ $invoice->signatory->name ?? '-' }}</p>
            <p class="mb-0">{{ $invoice->signatory->position ?? '' }}</p>
            <p class="mb-0 fw-bold">Eco Hydrotech Solutions Sdn. Bhd.</p>
        </div>

    </main>
</body>
</html>
