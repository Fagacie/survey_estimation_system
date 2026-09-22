<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Quotation #{{ $quotation->quotation_no }}</x-slot>

    <!-- Bootstrap 5 CSS (Scoped for print layout if necessary) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* =========================================================
           GLOBAL & PREVIEW STYLES
           ========================================================= */
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #212529;
            margin: 0;
            padding: 0;
        }

        /* Screen Preview Container */
        .page-container {
            background: #ffffff;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            position: relative;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            padding: 0 !important;
        }

        /* Header & Footer Image Containers */
        .quote-print-header,
        .quote-print-footer {
            width: 100%;
            display: block;
            line-height: 0;
        }

        .quote-header-img,
        .quote-footer-img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Top Header Row Layout (Logo on Left, Company Info on Right) */
        .quote-header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: -20px; /* Slight negative margin to overlay header graphic smoothly */
            position: relative;
            z-index: 10;
            padding: 0 40px;
        }

        .quote-logo img {
            max-height: 100px;
            width: auto;
            display: block;
        }

        .quote-company-info {
            font-size: 1rem;
            line-height: 1.4;
            color: #212529;
        }

        /* Main Content Padding */
        .quote-body {
            padding: 10px 40px 20px 40px;
        }

        .quote-title-row { margin-top: 10px; }
        .quote-title { font-size: 1.6rem; font-weight: 700; color: #333; letter-spacing: 0.5px; }
        .quote-meta { font-size: 0.85rem; color: #333; }
        .quote-hr { border-top: 2px solid #212529; margin: 10px 0 20px 0; opacity: 1; }

        .quote-box {
            background-color: #dfeefc !important;
            border-radius: 4px;
            padding: 0.75rem 1rem;
        }

        .quote-box-light { background-color: #eef6fb !important; }
        .quote-box-label { font-weight: 700; color: #1c53a0; font-size: 0.85rem; text-transform: uppercase; }

        .quote-details-strip {
            display: flex;
            gap: 24px;
            font-size: 0.82rem;
            color: #495057;
            flex-wrap: wrap;
        }

        .quote-table { width: 100%; border-collapse: collapse; }
        .quote-table thead th {
            background-color: #1c6e7a !important;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.6rem 0.75rem;
            border: none;
        }
        .quote-table tbody td {
            background-color: #f5f5f5 !important;
            padding: 0.6rem 0.75rem;
            font-size: 0.85rem;
            border-bottom: 4px solid #ffffff !important;
        }

        .quote-totals-table { width: 280px; font-size: 0.9rem; }
        .quote-totals-table td { padding: 0.4rem 0.75rem; }
        .quote-totals-table tr:not(.quote-grand-total-row) td:last-child { background-color: #f0f0f0 !important; }
        .quote-grand-total-row td { background-color: #1c6e7a !important; color: #ffffff !important; font-weight: 700; }

        .quote-section-heading { color: #1c53a0; font-weight: 700; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .quote-two-col { font-size: 0.85rem; }
        .quote-signature p { font-size: 0.9rem; }

        /* Outer Table Wrapper Layout */
        table.print-wrapper-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.print-wrapper-table td {
            padding: 0;
            border: none;
        }
            background: #f8f9fa;
            border-left: 4px solid #004b87;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 0 4px 4px 0;
        }
        .quote-details-col {
            flex: 1;
        }
        .quote-details-title {
            font-size: 8pt;
            text-transform: uppercase;
            font-weight: 700;
            color: #004b87;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }
        .quote-details-text {
            font-size: 10pt;
            line-height: 1.5;
            color: #333;
        }

        /* TABLE */
        .quote-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 9.5pt;
        }
        .quote-table th {
            background-color: #004b87;
            color: white;
            text-transform: uppercase;
            font-size: 8.5pt;
            font-weight: 600;
            padding: 12px 10px;
            text-align: left;
            border: 1px solid #003666;
        }
        .quote-table th.text-right { text-align: right; }
        .quote-table th.text-center { text-align: center; }

        .quote-table td {
            padding: 12px 10px;
            border: 1px solid #e0e0e0;
            vertical-align: top;
            color: #222;
        }
        .quote-table td.text-right { text-align: right; }
        .quote-table td.text-center { text-align: center; }
        
        .quote-table tr:nth-child(even) {
            background-color: #fafbfc;
        }

        /* TOTALS SECTION */
        .quote-totals-container {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 30px;
        }
        .quote-totals-table {
            width: 320px;
            border-collapse: collapse;
        }
        .quote-totals-table td {
            padding: 8px 12px;
            font-size: 10pt;
            border-bottom: 1px solid #eee;
        }
        .quote-totals-label {
            font-weight: 600;
            color: #555;
            text-align: right;
        }
        .quote-totals-value {
            text-align: right;
            font-family: 'Consolas', 'Courier New', monospace;
            font-weight: 500;
        }
        .quote-totals-table tr:last-child td {
            border-bottom: none;
            background: #004b87;
            color: white;
            font-size: 12pt;
            font-weight: 700;
        }
        .quote-totals-table tr:last-child td.quote-totals-label {
            color: white;
        }

        /* TWO COLUMN LAYOUT (Terms & Bank) */
        .quote-two-col {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        .quote-col-half {
            flex: 1;
        }

        /* BOXES */
        .quote-box {
            border: 1px solid #004b87;
            border-radius: 4px;
            overflow: hidden;
            height: 100%;
        }
        .quote-box-header {
            background: #004b87;
            color: white;
            padding: 8px 12px;
            font-size: 9pt;
            font-weight: 600;
            text-transform: uppercase;
        }
        .quote-box-content {
            padding: 12px;
            font-size: 9pt;
            line-height: 1.6;
        }
        
        .quote-box-light {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 15px;
            height: 100%;
        }
        .quote-box-light-title {
            font-size: 9pt;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 5px;
        }

        /* SIGNATURE */
        .quote-signature {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .quote-sig-line {
            width: 250px;
            border-bottom: 1px solid #000;
            margin-bottom: 8px;
        }
        .quote-sig-text {
            font-size: 9pt;
            color: #555;
            line-height: 1.4;
        }

        /* FOOTER */
        .quote-print-footer {
            width: 100%;
            margin: 0;
        }
        .quote-footer-img {
            width: 100%;
            height: auto;
            display: block;
            margin: 0;
        }

        @media print {
            html, body {
                background: #ffffff;
                margin: 0;
                padding: 0;
                width: 100%;
            }

            .quote-page {
                min-height: 251mm;
                box-sizing: border-box;
            }

            .page-container {
                border: none;
                width: 100%;
                max-width: 100%;
                box-shadow: none;
                padding: 0;
                margin: 0;
            }

            .no-print {
                display: none !important;
            }

            /* 2. EXPAND TABLE TO FULL PAGE HEIGHT: Forces footer to bottom of Page 2 */
            table.print-wrapper-table {
                height: 100vh !important;
            }

            /* 3. REPEATING HEADER: Flushes banner, logo, and company info flush to top edge */
            thead.repeat-print-header {
                display: table-header-group !important;
            }

            /* 4. REPEATING FOOTER: Anchors strictly to the bottom edge of every page */
            tfoot.repeat-print-footer {
                display: table-footer-group !important;
            }

            tfoot.repeat-print-footer td {
                vertical-align: bottom !important; /* Pushes footer image to bottom edge */
            }

            /* Adjust padding inside print view so elements flow cleanly */
            .quote-body {
                padding-top: 10px !important;
                padding-bottom: 20px !important;
                padding-left: 15mm !important;
                padding-right: 15mm !important;
            }

            /* 5. FORCE PAGE BREAK: Moves Payment Terms, Payment Info, and Signature to Page 2 */
            .page-break-before {
                page-break-before: always !important;
                break-before: page !important;
                padding-top: 15mm !important; /* Spacing from top header on page 2 */
            }

            /* Avoid breaking items mid-element */
            .quote-title-row,
            .quote-box,
            .quote-box-light,
            .quote-details-strip,
            .quote-totals-table,
            .quote-two-col,
            .quote-signature {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .quote-table tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            a {
                text-decoration: none !important;
                color: inherit !important;
            }
        }
    </style>

    <div class="max-w-7xl mx-auto">
        <!-- ACTION BAR (HIDDEN IN PRINT VIEW) -->
        <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 no-print sticky top-4 z-50">
            <a href="{{ url('/history') }}" class="px-4 py-2 rounded-lg text-sm font-bold border border-slate-300 hover:bg-slate-50 transition-colors text-slate-700">
                <i class="fa-solid fa-arrow-left mr-2"></i> BACK TO HISTORY
            </a>
            <button onclick="window.print()" class="px-5 py-2.5 rounded-lg text-sm font-bold bg-teal-600 text-white hover:bg-teal-500 shadow-sm transition-colors">
                <i class="fa-solid fa-print mr-2"></i> PRINT / SAVE PDF
            </button>
        </div>

<!-- A4 PAGE CONTAINER -->
<div class="page-container">

    <table class="print-wrapper-table">

        <!-- 
            ===================================================================
            HEADER SECTION (FLUSH TO TOP EDGE)
            Includes: Banner Image + Company Logo + Address Details inside <thead>
            ===================================================================
        -->
        <thead class="repeat-print-header">
            <tr>
                <td>
                    <!-- Top Graphic Banner -->
                    <div class="quote-print-header">
                        <img src="{{ asset('images/header.jpeg') }}" alt="Header" class="quote-header-img">
                    </div>

                    <!-- Company Logo & Address moved inside header block to stay attached at top -->
                    <div class="quote-header-content">
                        <div class="quote-logo">
                            <img src="{{ asset('images/logo.jpeg') }}" alt="Eco Hydrotech Solutions Logo">
                        </div>
                        <div class="text-end quote-company-info">
                            <strong>ECO HYDROTECH SOLUTIONS SDN. BHD. (1688434-T)</strong><br>
                            Institute of Oceanography and Environment<br>
                            Universiti Malaysia Terengganu<br>
                            21030, Kuala Nerus, Terengganu<br>
                            Malaysia
                        </div>
                    </div>
                </td>
            </tr>
        </thead>

        <!-- 
            ===================================================================
            PAGE 1 CONTENT: Quotation Metadata, Client Info, Items & Totals
            ===================================================================
        -->
        <tbody>
            <tr>
                <td>
                    <div class="quote-body">

                        <!-- TITLE + QUOTATION NO / DATE -->
                        <div class="d-flex justify-content-between align-items-end quote-title-row">
                            <h2 class="quote-title mb-0">QUOTATION</h2>
                            <div class="text-end quote-meta">
                                <div>Quotation No: <strong>{{ $quotation->quotation_no }}</strong></div>
                                <div>Date: <strong>{{ $quotation->created_at ? $quotation->created_at->format('d M Y') : '-' }}</strong></div>
                            </div>
                        </div>
                        <hr class="quote-hr">

                        <!-- CLIENT NAME / ADDRESS BOX -->
                        <div class="quote-box mb-3">
                            <span class="quote-box-label">CLIENT NAME: <span class="fw-bold">{{ $quotation->project->client->company_name ?? '-' }}</span></span>
                            <div class="mt-1" style="white-space: pre-line;">{{ $quotation->project->client->client_address ?? '-' }}</div>
                        </div>

                        <p class="mb-3">Dear Sir/Madam,</p>

                        <!-- PROJECT BOX -->
                        <div class="quote-box mb-2">
                            <span class="quote-box-label">PROJECT</span>
                            <div class="mt-1 fw-bold">{{ $quotation->project->name ?? '-' }}</div>
                        </div>

                        <!-- SECONDARY DETAILS STRIP -->
                        <div class="quote-details-strip mb-4">
                            <span>Period: <strong>{{ $quotation->project->period ?? '-' }}</strong></span>
                            <span>PIC: <strong>{{ $quotation->project->pic_name ?? '-' }}</strong></span>
                            <span>No. of PIC: <strong>{{ $quotation->project->pic_no ?? '-' }}</strong></span>
                        </div>

                        <!-- ITEMS TABLE -->
                        <table class="table quote-table mb-2">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th class="text-center" style="width: 80px;">Quantity</th>
                                    <th class="text-center" style="width: 80px;">Days</th>
                                    <th class="text-end" style="width: 120px;">Unit Price</th>
                                    <th class="text-end" style="width: 130px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quotation->items as $line)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-secondary border me-1">
                                                {{ strtoupper($line->module->module_name ?? 'MODULE') }}
                                            </span>
                                            <strong>{{ $line->catalogItem->item_name ?? 'Service Item' }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $line->catalogItem->category->category_name ?? '' }}
                                                @if($line->catalogItem->category ?? false) &middot; @endif
                                                {{ $line->catalogItem->service->service_name ?? '' }}
                                            </small>
                                        </td>
                                        <td class="text-center">{{ $line->unit_qty }}</td>
                                        <td class="text-center">{{ $line->days }}</td>
                                        <td class="text-end">MYR {{ number_format($line->daily_rate, 2) }}</td>
                                        <td class="text-end">MYR {{ number_format($line->line_total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No items added to the quotation yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- TOTALS CALCULATION -->
                        @php
                            $subtotal = $quotation->grand_total;
                            $sst = $subtotal * 0.08;
                            $finalTotal = $subtotal + $sst;
                        @endphp

                        <div class="d-flex justify-content-end mb-4">
                            <table class="quote-totals-table">
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">MYR {{ number_format($subtotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>SST 8%</td>
                                    <td class="text-end">MYR {{ number_format($sst, 2) }}</td>
                                </tr>
                                <tr class="quote-grand-total-row">
                                    <td>Grand Total</td>
                                    <td class="text-end">MYR {{ number_format($finalTotal, 2) }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- ADDITIONAL NOTES -->
                        <div class="quote-box quote-box-light mb-4">
                            <span class="quote-box-label">Additional Notes</span>
                            <div class="mt-1" style="white-space: pre-wrap;">{{ $quotation->additional_notes ?? '-' }}</div>
                        </div>

                        <!-- 
                            ===================================================================
                            PAGE 2 CONTENT: Forced to break onto page 2 using .page-break-before
                            Contains: Payment Terms, Payment Info, and Technical Director Signature
                            ===================================================================
                        -->
                        <div class="page-break-before">
                            
                            <!-- PAYMENT TERMS & PAYMENT INFORMATION -->
                            <div class="row quote-two-col mb-4">
                                <div class="col-6">
                                    <h6 class="quote-section-heading">Payment Terms</h6>
                                    <div>
                                        @forelse($quotation->paymentTerms as $term)
                                            <div class="mb-1">
                                                {{ $term->name }} : <strong>{{ number_format($term->percentage, 0) }}%</strong>
                                                - {{ $term->condition ?? '-' }}
                                            </div>
                                        @empty
                                            <div>-</div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h6 class="quote-section-heading">Payment Information</h6>
                                    <div>
                                        <strong>Recipient:</strong> ECO HYDROTECH SOLUTIONS SDN. BHD.<br>
                                        <strong>Bank:</strong> MAYBANK ISLAMIC<br>
                                        <strong>Account Number:</strong> 5630 6496 5609<br>
                                        <strong>Bank's Address:</strong> 1-j, Kuala Terengganu Branch, Kompleks Perdana, Jalan Air Jernih, 20300 Kuala Terengganu, Terengganu
                                    </div>
                                </div>
                            </div>

                            <!-- SIGNATURE BLOCK -->
                            <div class="quote-signature mb-4">
                                <p class="mb-4">Yours sincerely,</p>
                                <img src="{{ asset('images/DrMadihasign.jpeg') }}" alt="Dr Madiha sign" style="max-height: 80px;">
                                <p class="mb-0 fw-bold">Ts. Dr Madiha Mokhtar</p>
                                <p class="mb-0">Technical Director</p>
                                <p class="mb-0">Eco Hydrotech Solutions Sdn. Bhd.</p>
                            </div>



                        </div>

                    </div>
                </td>
            </tr>
        </tbody>

        <!-- 
            ===================================================================
            FOOTER SECTION (ANCHORED TO PHYSICAL BOTTOM OF PAGE)
            Renders inside <tfoot> and aligned vertically to bottom
            ===================================================================
        -->
        <tfoot class="repeat-print-footer">
            <tr>
                <td>
                    <div class="quote-print-footer">
                        <img src="{{ asset('images/footer.jpeg') }}" alt="Footer" class="quote-footer-img">
                    </div>
                </td>
            </tr>
        </tfoot>

    </table>

    </div>
    
    </div> <!-- end .max-w-7xl -->
    @push('scripts')
    <!-- Bootstrap Bundle JS (scoped for this view if needed by components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @endpush
</x-app-layout>