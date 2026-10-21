<!DOCTYPE html>
<html>
<head>
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/invoice-print.css') }}">
</head>
<body onload="window.print()">

    <div class="invoice-page-wrap">

        {{-- CONTROLS (hidden when printing) --}}
        <div class="invoice-controls no-print">
            <a href="{{ route('quotations.invoice', $invoice->quotation_Id) }}" class="ctrl-btn">
                <i class="bi bi-arrow-left"></i> Back
            </a>

            <div class="invoice-controls-right">
                {{-- Edit: always available (dates + description + condition in one form) --}}
                <button type="button" class="ctrl-btn" data-bs-toggle="modal" data-bs-target="#editModal">
                    <i class="fa-solid fa-pen"></i> Edit
                </button>

                {{-- Print: greyed out until the form has been saved once (printed_date set) --}}
                @if($invoice->printed_date)
                    <button type="button" class="ctrl-btn ctrl-btn-primary" onclick="window.print()">
                        <i class="fa-solid fa-print"></i> Print
                    </button>
                @else
                    <button type="button" class="ctrl-btn" disabled title="Save the invoice details first">
                        <i class="fa-solid fa-print"></i> Print
                    </button>
                @endif
            </div>
        </div>

        {{-- INVOICE SLIP --}}
        <div class="invoice-slip border" id="invoiceSlip">
            <table class="print-wrapper-table">

                <!-- TOP HEADER BANNER GRAPHIC (REPEATS ON PRINT) -->
                <thead class="repeat-print-header">
                    <tr>
                        <td>
                            <div class="quote-print-header">
                                <img src="{{ asset('images/header.jpeg') }}" alt="Header" class="quote-header-img">
                            </div>
                        </td>
                    </tr>
                </thead>

                <!-- INNER BODY CONTENT -->
                <tbody>
                    <tr>
                        <td>
                            <div class="invoice-body-content">

                                <!-- LOGO (LEFT) & COMPANY ADDRESS DETAILS (RIGHT) -->
                                <div class="quote-header-content">
                                    <div class="quote-logo">
                                        <img src="{{ asset('images/logo.jpeg') }}" alt="Eco Hydrotech Solutions Logo">
                                    </div>
                                    <div class="text-end quote-company-info">
                                        <strong>ECO HYDROTECH SOLUTIONS SDN. BHD. (1688434-T)</strong><br>
                                        TH3, Level 2, TELIPOTS HUB<br>
                                        Institute of Oceanography and Environment<br>
                                        Universiti Malaysia Terengganu<br>
                                        21030, Kuala Nerus, Terengganu<br>
                                        Malaysia
                                    </div>
                                </div>

                                <!-- INVOICE TITLE + INVOICE NO / DATE -->
                                <div class="d-flex justify-content-between align-items-end quote-title-row">
                                    <div>
                                        <h2 class="quote-title mb-0">INVOICE</h2>
                                    </div>
                                    <div class="text-end quote-meta">
                                        <div>Invoice No: <strong>{{ $invoice->invoice_number }}</strong></div>
                                        <div>Date: <strong>{{ $invoice->printed_date ? \Carbon\Carbon::parse($invoice->printed_date)->format('d F Y') : 'DRAFT - NOT YET PRINTED' }}</strong></div>
                                    </div>
                                </div>
                                <hr class="quote-hr">

                                <!-- BILL TO & INVOICE DETAILS GRID -->
                                <div class="meta-grid">
                                    <div class="meta-box">
                                        <div class="info-title">BILL TO</div>
                                        <strong>{{ $invoice->quotation->project->client->company_name ?? '-' }}</strong><br>
                                        <div>{!! nl2br(e($invoice->quotation->project->client->client_address ?? '-')) !!}</div>
                                        <div class="mt-2">
                                            <strong>Attn: {{ $invoice->quotation->project->pic_name ?? '-' }}</strong><br>
                                            <span class="text-muted" style="font-size: 0.78rem;">Project &amp; Admin Executive</span>
                                        </div>
                                    </div>

                                    <div class="meta-box">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="info-title">Invoice Date</div>
                                                {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('d F Y') : '-' }}
                                            </div>
                                            <div class="col-6">
                                                <div class="info-title">Due Date</div>
                                                {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d F Y') : '-' }}
                                            </div>
                                            <div class="col-6 mt-2">
                                                <div class="info-title">Payment Terms</div>
                                                @if($invoice->invoice_date && $invoice->due_date)
                                                    Net {{ \Carbon\Carbon::parse($invoice->invoice_date)->diffInDays($invoice->due_date) }} Days
                                                @else
                                                    -
                                                @endif
                                            </div>
                                            <div class="col-6 mt-2">
                                                <div class="info-title">Your Ref.</div>
                                                {{ $invoice->po_number ?? ($invoice->quotation->po_number ?? '-') }}
                                            </div>
                                            <div class="col-12 mt-2">
                                                <div class="info-title">Our Ref.</div>
                                                {{ $invoice->quotation->quotation_no }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- PROJECT DETAILS -->
                                <div class="project-box">
                                    <span class="info-title">PROJECT</span><br>
                                    <strong>{{ $invoice->quotation->project->name }}</strong>
                                </div>

                                <!-- ITEMS TABLE -->
                                <table class="table table-bordered">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width: 5%;">No.</th>
                                            <th style="width: 65%;">Description</th>
                                            <th class="text-center" style="width: 10%;">Qty</th>
                                            <th class="text-end" style="width: 20%;">Amount (RM)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
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

                                <p class="mb-4" style="font-size: 0.9rem;"><strong>Amount in words:</strong> Ringgit Malaysia {{ \App\Helpers\NumberToWords::convert($invoice->paymentTerm->amount) }} Only.</p>

                                <!-- PAGE 2 CONTENT: forced page break when printing -->
                                <div class="page-break-before">

                                    <!-- PAYMENT DETAILS & NOTES -->
                                    <div class="row mb-4">
                                        <div class="col-6 border p-3">
                                            <div class="fw-bold text-primary mb-1">PAYMENT DETAILS</div>
                                            <strong>Account Name:</strong> ECO HYDROTECH SOLUTIONS SDN. BHD.<br>
                                            <strong>Bank:</strong> MAYBANK ISLAMIC<br>
                                            <strong>Account No.:</strong> 5630 6496 5609
                                        </div>
                                        <div class="col-6 border p-3">
                                            <div class="fw-bold text-primary mb-1">PAYMENT NOTE</div>
                                            {!! nl2br(e($invoice->paymentTerm->condition ?? '-')) !!}<br>
                                            <small>Kindly quote the invoice number in the payment reference.</small>
                                        </div>
                                    </div>

                                    <!-- SIGNATURE SECTION -->
                                    <div class="row mt-5 pt-3">
                                        <div class="col-6">
                                            @php
                                                // Chosen signer; a new draft falls back to the first person in the list
                                                $signer = $invoice->signatory ?? $signatories->sortBy('id')->first();
                                            @endphp
                                            <div class="fw-bold text-primary mb-4">Approved by:</div>
                                            <div class="w-75 d-flex justify-content-center align-items-end" style="height: 60px;">
                                                @if($signer && $signer->signature_path)
                                                    <img src="{{ asset($signer->signature_path) }}" alt="Signature" style="max-height: 50px;" onerror="this.style.display='none'">
                                                @endif
                                            </div>
                                            <div class="signature-line w-75"></div>
                                            <strong>{{ $signer->name ?? '-' }}</strong><br>
                                            <small class="text-muted">{{ $signer->position ?? '' }}</small>
                                        </div>
                                    </div>

                                </div>

                            </div> <!-- END .invoice-body-content -->
                        </td>
                    </tr>
                </tbody>

            </table>

            <!-- FOOTER: fixed to the bottom of every printed page -->
            <div class="quote-print-footer">
                <img src="{{ asset('images/footer.jpeg') }}" alt="Footer" class="quote-footer-img">
            </div>

        </div> <!-- end #invoiceSlip -->

    </div> <!-- end .invoice-page-wrap -->
</body>
</html>