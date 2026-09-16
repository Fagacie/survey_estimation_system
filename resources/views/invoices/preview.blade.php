<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: #f1f5f9;
            color: #000;
            font-size: 10pt;
            line-height: 1.4;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            background: #fff;
            padding: 50px 45px 80px 45px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            position: relative;
        }

        /* ── HEADER GRAPHIC ──────────────────────────── */
        .top-left-graphic {
            position: absolute;
            top: 0;
            left: 0;
            width: 250px;
            height: 120px;
            z-index: 1;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-left: 20px;
        }

        .header-logo img {
            max-height: 90px;
            max-width: 220px;
        }
        
        .header-company {
            text-align: right;
            font-size: 10pt;
            color: #000;
            line-height: 1.3;
        }
        
        .header-company .company-name {
            font-size: 12pt;
            font-weight: 700;
            margin-bottom: 4px;
        }

        /* ── TITLE BAR ───────────────────────── */
        .title-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 2px solid #1a5c8b;
            padding-bottom: 5px;
            margin-bottom: 20px;
        }
        
        .invoice-title {
            font-size: 24pt;
            font-weight: 700;
            color: #1a5c8b;
            letter-spacing: 1px;
        }
        
        .invoice-meta-right {
            text-align: right;
        }
        
        .original-text {
            color: #4a8c2a;
            font-weight: 700;
            font-size: 10pt;
            margin-bottom: 4px;
        }
        
        .invoice-no-text {
            color: #1a5c8b;
            font-weight: 700;
            font-size: 10pt;
        }

        /* ── BILL TO GRID ────────────────────── */
        .meta-box {
            border: 1px solid #a0a0a0;
            display: flex;
            margin-bottom: 20px;
        }
        
        .meta-box-left {
            flex: 1.2;
            padding: 10px 15px;
            border-right: 1px solid #a0a0a0;
        }
        
        .meta-box-right {
            flex: 1;
            padding: 10px 15px;
        }
        
        .meta-label-blue {
            color: #1a5c8b;
            font-weight: 700;
            font-size: 9pt;
            margin-bottom: 5px;
        }
        
        .meta-value-text {
            margin-bottom: 12px;
            font-size: 10pt;
        }
        
        .meta-row {
            margin-bottom: 5px;
        }
        
        .meta-row-label {
            color: #1a5c8b;
            font-weight: 700;
            font-size: 9pt;
        }
        
        .meta-row-val {
            font-size: 9pt;
            margin-bottom: 5px;
        }

        /* ── PROJECT BOX ─────────────────────── */
        .project-box {
            border: 1px solid #a0a0a0;
            margin-bottom: 20px;
            background: #eef4f9;
        }
        
        .project-header {
            color: #1a5c8b;
            font-weight: 700;
            font-size: 9pt;
            padding: 5px 15px;
        }
        
        .project-name {
            font-weight: 700;
            font-size: 11pt;
            padding: 10px 15px;
            background: #fff;
            border-top: 1px solid #a0a0a0;
        }

        /* ── ITEMS TABLE ─────────────────────── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #a0a0a0;
        }
        
        .items-table thead th {
            background: #1a5c8b;
            color: #fff;
            font-size: 9pt;
            font-weight: 700;
            padding: 10px;
            border: 1px solid #a0a0a0;
        }
        
        .items-table tbody td {
            padding: 12px 10px;
            border: 1px solid #a0a0a0;
            vertical-align: top;
            font-size: 10pt;
        }
        
        .col-no { width: 50px; text-align: center; }
        .col-desc { text-align: left; }
        .col-qty { width: 80px; text-align: center; }
        .col-amt { width: 150px; text-align: right; font-weight: 700; }
        
        .item-title {
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .item-desc {
            font-size: 9pt;
            color: #333;
            margin-bottom: 12px;
        }
        
        .table-subtotal td {
            padding: 8px 10px;
        }
        
        .table-total {
            background: #dae8f5;
        }
        
        .table-total td {
            color: #1a5c8b;
            font-weight: 700;
            padding: 10px;
        }

        /* ── AMOUNT IN WORDS ─────────────────── */
        .amount-words {
            font-size: 10pt;
            margin-bottom: 30px;
        }
        .amount-words strong {
            color: #1a5c8b;
        }

        /* ── PAGE 2 (PAYMENT & SIG) ──────────── */
        .payment-section {
            display: flex;
            margin-bottom: 40px;
        }
        
        .payment-details {
            flex: 1.2;
            border: 1px solid #a0a0a0;
            padding: 15px;
        }
        
        .payment-notes {
            flex: 1;
            border: 1px solid #a0a0a0;
            border-left: none;
            padding: 15px;
            font-size: 9pt;
        }
        
        .payment-details div {
            margin-bottom: 3px;
            font-size: 9.5pt;
        }
        
        .signatures {
            display: flex;
            gap: 40px;
        }
        
        .sig-block {
            flex: 1;
        }
        
        .sig-title-blue {
            color: #1a5c8b;
            font-weight: 700;
            font-size: 10pt;
            margin-bottom: 10px;
        }
        
        .sig-image-container {
            height: 70px;
            display: flex;
            align-items: flex-end;
            margin-bottom: 5px;
        }
        
        .sig-image-container img {
            max-height: 60px;
        }
        
        .sig-line {
            border-bottom: 2px dotted #000;
            width: 90%;
            margin-bottom: 5px;
        }
        
        .sig-name {
            font-weight: 700;
            font-size: 10pt;
        }
        
        .sig-role {
            font-size: 9.5pt;
        }

        /* ── FOOTER GRAPHIC ──────────────────── */
        .footer-graphic {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 0 45px 20px 45px;
        }
        
        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
        }
        
        .footer-tagline {
            font-size: 13pt;
            font-weight: 700;
            color: #333;
        }
        
        .footer-tagline span.blue { color: #1a5c8b; }
        .footer-tagline span.green { color: #4a8c2a; }
        
        .footer-contact {
            font-size: 11pt;
            text-align: right;
        }
        
        .footer-contact div {
            margin-bottom: 5px;
        }
        
        .footer-contact i {
            width: 20px;
            text-align: center;
            color: #333;
        }
        
        .footer-bottom-border {
            height: 15px;
            width: 100%;
            background: linear-gradient(to right, #1a5c8b 50%, #4a8c2a 50%);
            position: absolute;
            bottom: 0;
            left: 0;
        }

        /* ── PRINT CONTROLS ──────────────────── */
        .print-controls {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 100;
            display: flex;
            gap: 8px;
        }
        .print-btn {
            background: #0f172a;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            font-family: 'Roboto', sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }
        .print-btn:hover { background: #1e293b; }
        .print-btn.secondary {
            background: #fff;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .print-btn.secondary:hover { background: #f8fafc; }

        /* ── PRINT STYLES ────────────────────── */
        @media print {
            body { background: #fff; }
            .page {
                margin: 0;
                box-shadow: none;
                page-break-after: always;
            }
            .page:last-child {
                page-break-after: auto;
            }
            .print-controls { display: none !important; }
            @page {
                size: A4;
                margin: 0; /* Let .page handle margins to allow full-bleed graphics */
            }
        }
    </style>
</head>
<body>

    {{-- PRINT CONTROLS --}}
    <div class="print-controls">
        <a href="{{ route('invoices.show', $invoice->id) }}" class="print-btn secondary">
            ← Back to Edit
        </a>
        <button class="print-btn" onclick="window.print()">
            🖨 Print / Save as PDF
        </button>
    </div>

    {{-- PAGE 1 --}}
    <div class="page">
        {{-- TOP LEFT SHAPE --}}
        <svg class="top-left-graphic" viewBox="0 0 250 120" preserveAspectRatio="none">
            <!-- Blue shape -->
            <polygon points="0,0 250,0 220,15 20,15 0,100" fill="#1a5c8b" />
            <!-- Green shape -->
            <polygon points="0,100 20,15 220,15 200,25 20,25 0,120" fill="#4a8c2a" />
        </svg>

        {{-- HEADER --}}
        <div class="header-content">
            <div class="header-logo">
                @if($company['company_logo'] ?? false)
                    <img src="{{ asset('storage/' . $company['company_logo']) }}" alt="Company Logo">
                @else
                    <img src="{{ asset('images/logo.png') }}" alt="Eco Hydrotech Logo" style="max-height: 90px; max-width: 220px; margin-top: 10px;">
                @endif
            </div>
            <div class="header-company">
                <div class="company-name">{{ $company['company_name'] ?? 'ECO HYDROTECH SOLUTIONS SDN. BHD.' }} @if($company['company_reg_no'] ?? false) ({{ $company['company_reg_no'] }}) @endif</div>
                @if($company['company_address'] ?? false)
                    <div>{!! nl2br(e($company['company_address'])) !!}</div>
                @else
                    <div>Institute of Oceanography and Environment<br>Universiti Malaysia Terengganu<br>21030, Kuala Nerus, Terengganu<br>Malaysia</div>
                @endif
            </div>
        </div>

        {{-- TITLE BAR --}}
        <div class="title-row">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-meta-right">
                <div class="original-text">ORIGINAL</div>
                <div class="invoice-no-text">Invoice No. {{ $invoice->invoice_number }}</div>
            </div>
        </div>

        {{-- INVOICE META --}}
        <div class="meta-box">
            <div class="meta-box-left">
                <div class="meta-label-blue">BILL TO</div>
                <div style="font-weight: 700; font-size: 10pt; margin-bottom: 10px;">{{ $invoice->client->name }}</div>
                
                @if($invoice->client->address ?? false)
                    <div class="meta-value-text">{!! nl2br(e($invoice->client->address)) !!}</div>
                @endif
                
                <div style="margin-top: 15px;">
                    <span style="font-weight: 700;">Attn: </span>
                    <span style="font-weight: 700;">{{ $invoice->user->name ?? auth()->user()->name ?? '—' }}</span>
                </div>
                @if($invoice->client->contact_email || $invoice->client->contact_phone)
                    <div>{{ $invoice->client->contact_email }} {{ $invoice->client->contact_phone ? '/ ' . $invoice->client->contact_phone : '' }}</div>
                @endif
            </div>
            <div class="meta-box-right">
                <div class="meta-row">
                    <div class="meta-row-label">Invoice Date</div>
                    <div class="meta-row-val">{{ $invoice->issue_date->format('j F Y') }}</div>
                </div>
                
                @if($invoice->due_date)
                    <div class="meta-row">
                        <div class="meta-row-label">Due Date</div>
                        <div class="meta-row-val">{{ $invoice->due_date->format('j F Y') }}</div>
                    </div>
                @endif
                
                @if($invoice->payment_terms)
                    <div class="meta-row">
                        <div class="meta-row-label">Payment Terms</div>
                        <div class="meta-row-val">{{ $invoice->payment_terms }}</div>
                    </div>
                @endif
                
                @if($invoice->client_ref)
                    <div class="meta-row">
                        <div class="meta-row-label">Your Ref.</div>
                        <div class="meta-row-val">{{ $invoice->client_ref }}</div>
                    </div>
                @endif
                
                @if($invoice->our_ref)
                    <div class="meta-row">
                        <div class="meta-row-label">Our Ref.</div>
                        <div class="meta-row-val">{{ $invoice->our_ref }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- PROJECT NAME --}}
        @if($invoice->project)
            <div class="project-box">
                <div class="project-header">PROJECT</div>
                <div class="project-name">{{ $invoice->project->name }}</div>
            </div>
        @endif

        {{-- LINE ITEMS TABLE --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-no">No.</th>
                    <th class="col-desc">Description</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-amt">Amount ({{ $invoice->currency }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                    <tr>
                        <td class="col-no">{{ $index + 1 }}</td>
                        <td class="col-desc">
                            <div class="item-title">{{ $item->title }}</div>
                            @if($item->description)
                                <div class="item-desc">{!! nl2br(e($item->description)) !!}</div>
                            @endif
                        </td>
                        <td class="col-qty">{{ $item->quantity + 0 }}</td>
                        <td class="col-amt">{{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
                
                {{-- Spacer row if needed --}}
                <tr>
                    <td colspan="4" style="border-bottom: none; border-top: none; padding: 20px;"></td>
                </tr>

                {{-- Subtotal & Totals --}}
                <tr class="table-subtotal">
                    <td colspan="2" style="border-right: none; border-bottom: none;"></td>
                    <td style="text-align: right; border-left: none;">Subtotal</td>
                    <td class="col-amt">{{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                
                @if($invoice->discount_amount)
                    <tr class="table-subtotal">
                        <td colspan="2" style="border-right: none; border-bottom: none; border-top: none;"></td>
                        <td style="text-align: right; border-left: none; border-top: none;">Discount</td>
                        <td class="col-amt" style="border-top: none;">-{{ number_format($invoice->discount_amount, 2) }}</td>
                    </tr>
                @endif
                
                @if($invoice->tax_rate)
                    <tr class="table-subtotal">
                        <td colspan="2" style="border-right: none; border-bottom: none; border-top: none;"></td>
                        <td style="text-align: right; border-left: none; border-top: none;">Tax ({{ number_format($invoice->tax_rate, 1) }}%)</td>
                        <td class="col-amt" style="border-top: none;">{{ number_format($invoice->tax_amount, 2) }}</td>
                    </tr>
                @endif
                
                <tr class="table-total">
                    <td colspan="2" style="border-right: none;"></td>
                    <td style="text-align: right; border-left: none; font-weight: 700; color: #1a5c8b;">TOTAL DUE</td>
                    <td class="col-amt" style="color: #1a5c8b;">{{ number_format($invoice->grand_total, 2) }}</td>
                </tr>
                @php
                    $quoteTotal = $invoice->project?->costEstimation?->total_cost ?? 0;
                    $percentage = $quoteTotal > 0 ? number_format(($invoice->grand_total / $quoteTotal) * 100, 2) : 0;
                @endphp
                @if($quoteTotal > 0)
                <tr>
                    <td colspan="4" style="text-align: right; padding-top: 15px; font-size: 8.5pt; color: #475569; border: none;">
                        <strong>Project Progress:</strong> This invoice represents <strong>{{ $percentage }}%</strong> of the approved project estimate with the amount (RM {{ number_format($quoteTotal, 2) }}).
                    </td>
                </tr>
                @endif
            </tbody>
        </table>

        {{-- AMOUNT IN WORDS --}}
        @if($invoice->amount_in_words)
            <div class="amount-words">
                <strong>Amount in words:</strong> {{ $invoice->amount_in_words }} Only.
            </div>
        @endif

        {{-- FOOTER --}}
        <div class="footer-graphic">
            <div class="footer-content">
                <div class="footer-tagline">
                    "Integrated <span class="blue">Water</span> and <span class="green">Environmental</span> Solutions<br>
                    through <span class="green">Nature-Based Innovation</span>"
                </div>
                <div class="footer-contact">
                    <div><i class="fa-solid fa-envelope"></i> {{ $company['company_email'] ?? 'info@ecohydrotechsolutions.com' }}</div>
                    <div><i class="fa-solid fa-phone"></i> {{ $company['company_phone'] ?? '+60 19-464 0632' }}</div>
                </div>
            </div>
            <div class="footer-bottom-border"></div>
        </div>
    </div>
    
    {{-- PAGE 2 (if signatures / notes exist) --}}
    <div class="page">
        {{-- TOP LEFT SHAPE (Repeated) --}}
        <svg class="top-left-graphic" viewBox="0 0 250 120" preserveAspectRatio="none">
            <polygon points="0,0 250,0 220,15 20,15 0,100" fill="#1a5c8b" />
            <polygon points="0,100 20,15 220,15 200,25 20,25 0,120" fill="#4a8c2a" />
        </svg>
        
        <div style="height: 120px;"></div> {{-- Spacer for header graphic --}}

        {{-- PAYMENT DETAILS & NOTES --}}
        <div class="payment-section">
            <div class="payment-details">
                <div class="meta-label-blue">PAYMENT DETAILS</div>
                <div><strong>Account Name:</strong> {{ $invoice->payment_account_name ?? 'ECO HYDROTECH SOLUTIONS SDN. BHD.' }}</div>
                <div><strong>Bank:</strong> {{ $invoice->payment_bank_name ?? 'MAYBANK ISLAMIC' }}</div>
                <div><strong>Account No.:</strong> {{ $invoice->payment_account_number ?? '5630 6496 5609' }}</div>
            </div>
            
            <div class="payment-notes">
                <div class="meta-label-blue">PAYMENT NOTE</div>
                @if($invoice->notes)
                    {!! nl2br(e($invoice->notes)) !!}
                @else
                    • Kindly quote the invoice number in the payment reference.
                @endif
            </div>
        </div>

        {{-- SIGNATURES --}}
        <div class="signatures">
            <div class="sig-block">
                <div class="sig-title-blue">Prepared by:</div>
                <div class="sig-image-container">
                    @if($company['prepared_by_signature'] ?? false)
                        <img src="{{ asset('storage/' . $company['prepared_by_signature']) }}" alt="Signature">
                    @endif
                </div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $invoice->prepared_by_name ?? '—' }}</div>
                <div class="sig-role">{{ $invoice->prepared_by_title ?? '' }}</div>
            </div>
            <div class="sig-block">
                <div class="sig-title-blue">Approved by:</div>
                <div class="sig-image-container">
                    @if($company['approved_by_signature'] ?? false)
                        <img src="{{ asset('storage/' . $company['approved_by_signature']) }}" alt="Signature">
                    @endif
                </div>
                <div class="sig-line"></div>
                <div class="sig-name">{{ $invoice->approved_by_name ?? '—' }}</div>
                <div class="sig-role">{{ $invoice->approved_by_title ?? '' }}</div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="footer-graphic">
            <div class="footer-content">
                <div class="footer-tagline">
                    "Integrated <span class="blue">Water</span> and <span class="green">Environmental</span> Solutions<br>
                    through <span class="green">Nature-Based Innovation</span>"
                </div>
                <div class="footer-contact">
                    <div><i class="fa-solid fa-envelope"></i> {{ $company['company_email'] ?? 'info@ecohydrotechsolutions.com' }}</div>
                    <div><i class="fa-solid fa-phone"></i> {{ $company['company_phone'] ?? '+60 19-464 0632' }}</div>
                </div>
            </div>
            <div class="footer-bottom-border"></div>
        </div>
    </div>
</body>
</html>
