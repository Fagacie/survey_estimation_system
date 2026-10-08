<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Quotation #{{ $quotation->quotation_no }}</x-slot>

    <!-- Bootstrap 5 CSS -->
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

        /* Outer Table Wrapper Layout */
        table.print-wrapper-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.print-wrapper-table td {
            padding: 0;
            border: none;
        }

        /* Header & Footer Image Containers */
        .quote-print-header,
        .quote-print-footer {
            width: 100%;
            display: block;
            line-height: 0;
            position: relative;
        }

        .quote-header-img,
        .quote-footer-img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Logo on Left, Company Info on Right (Page 1 only) */
        .quote-header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: -50px;
            position: relative;
            z-index: 10;
            padding: 0;
            margin-bottom: 10px;
        }

        .quote-logo img {
            max-height: 100px;
            width: auto;
            display: block;
        }

        .quote-company-info {
            font-size: 0.80rem;
            line-height: 1.15;
            color: #212529;
        }

        /* Main Content Padding */
        .quote-body {
            padding: 10px 40px 100px 40px;
        }

        .quote-title-row { margin-top: 10px; }
        .quote-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
            letter-spacing: 0.5px;
        }
        .quote-meta {
            font-size: 0.85rem;
            line-height: 1.2;
            color: #333;
        }
        .quote-hr { border-top: 2px solid #212529; margin: 10px 0 20px 0; opacity: 1; }

        .quote-box {
            background-color: #dfeefc !important;
            border-radius: 4px;
            padding: 0.75rem 1rem;
            line-height: 1.2;
            font-size: 0.85rem;
        }

        .quote-box div {
            line-height: 1.2;
            margin-top: 2px !important;
        }

        .quote-box-light { background-color: #eef6fb !important; }
        .quote-box-label { font-weight: 700; color: #1c53a0; font-size: 0.85rem; text-transform: uppercase; }

        .quote-table { width: 100%; border-collapse: collapse; }
        .quote-table thead th {
            background-color: #1c6e7a !important;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.4rem 0.75rem;
            border: none;
        }
        .quote-table tbody td {
            background-color: #f5f5f5 !important;
            padding: 0.35rem 0.75rem;
            font-size: 0.85rem;
            line-height: 1.25;
            border-bottom: none !important;
        }

        /* Grouped rows */
        .quote-table tbody td.quote-module-cell {
            background-color: #d6e9ec !important;
            color: #1c6e7a;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding-top: 0.45rem;
            padding-bottom: 0.45rem;
        }

        .quote-table tbody td.quote-service-cell {
            font-weight: 600;
            padding-left: 1.5rem;
            background-color: #ececec !important;
            padding-top: 0.4rem;
            padding-bottom: 0.4rem;
        }

        .quote-table tbody td.quote-item-cell {
            padding-left: 2.75rem;
        }

        .quote-module-row,
        .quote-service-row {
            break-after: avoid;
            page-break-after: avoid;
        }

        .quote-totals-table { width: 280px; font-size: 0.9rem; }
        .quote-totals-table td { padding: 0.3rem 0.75rem; }
        .quote-totals-table tr:not(.quote-grand-total-row) td:last-child { background-color: #f0f0f0 !important; }
        .quote-grand-total-row td { background-color: #1c6e7a !important; color: #ffffff !important; font-weight: 700; }

                /* ===== Combined totals bars (Survey + Modelling + Grand Total) ===== */
        /* CHANGE THE FONT SIZES HERE */
        :root {
            --total-font-survey: 0.9rem;
            --total-font-modelling: 0.9rem;
            --total-font-grand: 1rem;
        }

        table.quote-summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.quote-summary-table td {
            background-color: #ececec !important;
            color: #504f4f !important;
            font-weight: 500;
            padding: 0.2rem 0.45rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
        }

        table.quote-summary-table td.qs-label {
            text-align: left;
        }

        table.quote-summary-table td.qs-amount {
            text-align: right;
            white-space: nowrap;
            width: 1%;
        }

        table.quote-summary-table tr.qs-survey td    { font-size: var(--total-font-survey); }
        table.quote-summary-table tr.qs-modelling td { font-size: var(--total-font-modelling); }
        table.quote-summary-table tr.qs-grand td {
            font-size: var(--total-font-grand);
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: none;
        }

        .quote-section-heading { color: #1c53a0; font-weight: 700; font-size: 0.9rem; margin-bottom: 0.5rem; }
        .quote-two-col { font-size: 0.85rem; line-height: 1.3; }
        .quote-two-col strong { font-weight: 700; }
        .quote-signature p { font-size: 0.9rem; line-height: 1.25; }

        /* =========================================================
           PRINT SPECIFIC OVERRIDES
           ========================================================= */
        @page {
            size: A4 portrait;
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

            /* Banner image repeats on every page */
            thead.repeat-print-header {
                display: table-header-group !important;
            }

            /* Footer fixed to the bottom of every printed page */
            .quote-print-footer {
                position: fixed !important;
                bottom: 0 !important;
                left: 0 !important;
                width: 100% !important;
                z-index: 1000;
            }

            /* Content clears the fixed footer */
            .quote-body {
                padding-top: 10px !important;
                padding-bottom: 35mm !important;
                padding-left: 15mm !important;
                padding-right: 15mm !important;
            }

            /* Payment Terms, Payment Info and Signature go to page 2 */
            .page-break-before {
                page-break-before: always !important;
                break-before: page !important;
                padding-top: 25mm !important;
            }

            /* Avoid breaking items mid-element */
            .quote-title-row,
            .quote-box,
            table.quote-summary-table,
            .quote-box-light,
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
            <a href="{{ route('quotations.download', $quotation->quotation_Id) }}" target="_blank" class="px-5 py-2.5 rounded-lg text-sm font-bold bg-teal-600 text-white hover:bg-teal-500 shadow-sm transition-colors inline-block">
                <i class="fa-solid fa-file-pdf mr-2"></i> DOWNLOAD PDF
            </a>
        </div>

        <!-- A4 PAGE CONTAINER -->
        <div class="page-container">

            <table class="print-wrapper-table">

                <!-- HEADER: banner only (repeats on every printed page) -->
                <thead class="repeat-print-header">
                    <tr>
                        <td>
                            <div class="quote-print-header">
                                <img src="{{ asset('images/header.jpeg') }}" alt="Header" class="quote-header-img">
                            </div>
                        </td>
                    </tr>
                </thead>

                <!-- PAGE 1 CONTENT -->
                <tbody>
                    <tr>
                        <td>
                            <div class="quote-body">

                                <!-- LOGO & COMPANY INFO (PAGE 1 ONLY) -->
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
                                    <span class="quote-box-label"><span class="fw-bold">{{ $quotation->project->client->company_name ?? '-' }}</span></span>
                                    <div class="mt-1" style="white-space: pre-line;">{{ $quotation->project->client->client_address ?? '-' }}</div>
                                </div>

                                <p class="mb-3">Dear Sir/Madam,</p>

                                <!-- PROJECT BOX (PIC and No. of PIC inside the box) -->
                                <div class="quote-box mb-2">
                                    <span class="quote-box-label">PROJECT</span>
                                    <div class="mt-1 fw-bold">{{ $quotation->project->name ?? '-' }}</div>
                                    <div class="mt-1"><strong>{{ $quotation->project->pic_name ?? '-' }}</strong></div>
                                    <div><strong>{{ $quotation->project->pic_no ?? '-' }}</strong></div>
                                </div>

                                <!-- ITEMS TABLE -->
                                <table class="table quote-table mb-2">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th class="text-center" style="width: 80px;">Quantity</th>
                                            <th class="text-center" style="width: 80px;">Day/Sample</th>
                                            <th class="text-end" style="width: 120px;">Unit Price (RM)</th>
                                            <th class="text-end" style="width: 130px;">Total (RM)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Group: Module > Category + Service > Items --}}
                                        @php
                                            $toRoman = function ($n) {
                                                $out = '';
                                                foreach ([[10,'x'],[9,'ix'],[5,'v'],[4,'iv'],[1,'i']] as [$val, $sym]) {
                                                    while ($n >= $val) { $out .= $sym; $n -= $val; }
                                                }
                                                return $out;
                                            };
                                            $groupedByModule = $quotation->items->groupBy(fn($l) => $l->module->module_name ?? 'MODULE');
                                        @endphp

                                        @forelse($groupedByModule as $moduleName => $moduleLines)
                                            <tr class="quote-module-row">
                                                <td colspan="5" class="quote-module-cell">{{ $loop->iteration }}. {{ $moduleName }}</td>
                                            </tr>

                                        @php
                                            $groups = $moduleLines->groupBy(fn($l) => $l->catalogItem->category->category_name ?? $l->category->category_name ?? '');
                                        @endphp

                                        @foreach($groups as $categoryName => $groupLines)
                                            @php $label = $categoryName; 
                                        @endphp

                                                <tr class="quote-service-row">
                                                    <td colspan="5" class="quote-service-cell">{{ $toRoman($loop->iteration) }}) {{ mb_strtoupper($label ?: '-') }}</td>
                                                </tr>

                                                @foreach($groupLines as $line)
                                                        @php
                                                            // Unit price the client sees = rate after markup
                                                            $unitPrice = round($line->daily_rate * (1 + ($line->mark_up ?? 0) / 100), 2);
                                                        @endphp
                                                    <tr>
                                                        <td class="quote-item-cell">
                                                            <div class="d-flex">
                                                                <span class="me-2">&bull;</span>
                                                                <span>{{ $line->catalogItem->item_name ?? $line->custom_item_name ?? 'Service Item' }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">{{ $line->unit_qty }}</td>
                                                        <td class="text-center">{{ $line->days }}</td>
                                                        <td class="text-end">{{ number_format($unitPrice, 2) }}</td>
                                                        <td class="text-end">{{ number_format($line->line_total, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">No items added to the quotation yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <!-- TOTALS CALCULATION -->
                                @php
                                    $subtotal   = $quotation->survey_total;
                                    $sst        = round($subtotal * 0.08, 2);
                                    $finalTotal = $subtotal + $sst;   // survey total incl. SST
                                @endphp

                                <div class="d-flex justify-content-end mb-4">
                                    <table class="quote-totals-table">
                                        <tr>
                                            <td>Subtotal</td>
                                            <td class="text-end">RM {{ number_format($subtotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>SST 8%</td>
                                            <td class="text-end">RM {{ number_format($sst, 2) }}</td>
                                        </tr>
                                        <tr class="quote-grand-total-row">
                                            <td>Grand Total</td>
                                            <td class="text-end">RM {{ number_format($finalTotal, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>

                                {{-- ===== MODELLING (separate table, read-only, from saved Modelling Builder data) ===== --}}
                                @php
                                    $mdlProject = $quotation->project;
                                    $mdlSummary = $mdlProject ? $mdlProject->modellingSummary : null;
                                    $mdlGroups = $mdlSummary
                                        ? $mdlProject->modellingItems()->with(['catalogModule', 'catalogItem'])->get()->groupBy('catalog_module_id')
                                        : collect();
                                @endphp

                                @if($mdlSummary && $mdlGroups->isNotEmpty() && ($quotation->modelling_total ?? 0) > 0)
                                <div class="quote-modelling-block mb-4">
                                    <h6 class="quote-section-heading">MODELLING</h6>
                                    <div class="mb-2" style="font-size: 0.85rem;">
                                        Package: <strong>{{ $mdlSummary->package_name ?? 'Custom selection' }}</strong>
                                    </div>

                                    <table class="table quote-table mb-2">
                                        <thead>
                                            <tr>
                                                <th>Description</th>
                                                <th class="text-center" style="width: 80px;">Quantity</th>
                                                <th class="text-center" style="width: 80px;">Day/Sample</th>
                                                <th class="text-end" style="width: 120px;">Unit Price (RM)</th>
                                                <th class="text-end" style="width: 130px;">Total (RM)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($mdlGroups as $items)
                                                <tr class="quote-module-row">
                                                    <td colspan="5" class="quote-module-cell">{{ $items->first()->catalogModule->name ?? 'Module' }}</td>
                                                </tr>
                                                @foreach($items as $line)
                                                    @php $units = max(($line->unit_qty ?? 1) * ($line->days ?? 1), 1); @endphp
                                                    <tr>
                                                        <td class="quote-item-cell">
                                                            <div class="d-flex"><span class="me-2">&bull;</span><span>{{ $line->catalogItem->name ?? 'Item' }}</span></div>
                                                        </td>
                                                        <td class="text-center">{{ rtrim(rtrim(number_format($line->unit_qty, 2, '.', ''), '0'), '.') }}</td>
                                                        <td class="text-center">{{ rtrim(rtrim(number_format($line->days, 2, '.', ''), '0'), '.') }}</td>
                                                        <td class="text-end">{{ number_format($line->line_total / $units, 2) }}</td>
                                                        <td class="text-end">{{ number_format($line->line_total, 2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <div class="d-flex justify-content-end">
                                        <table class="quote-totals-table">
                                            <tr>
                                                <td>Modelling Subtotal</td>
                                                <td class="text-end">RM {{ number_format($mdlSummary->client_subtotal, 2) }}</td>
                                            </tr>
                                            @if($mdlSummary->contingency_amount > 0)
                                            <tr>
                                                <td>Contingency {{ $mdlSummary->contingency_percent }}%</td>
                                                <td class="text-end">RM {{ number_format($mdlSummary->contingency_amount, 2) }}</td>
                                            </tr>
                                            @endif
                                            @if($mdlSummary->tax_amount > 0)
                                            <tr>
                                                <td>SST {{ $mdlSummary->tax_percent }}%</td>
                                                <td class="text-end">RM {{ number_format($mdlSummary->tax_amount, 2) }}</td>
                                            </tr>
                                            @endif
                                            <tr class="quote-grand-total-row">
                                                <td>Modelling Total</td>
                                                <td class="text-end">RM {{ number_format($mdlSummary->grand_total, 2) }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                @if(($quotation->modelling_total ?? 0) > 0)
                                <table class="quote-summary-table mb-4">
                                    <tr class="qs-survey">
                                        <td class="qs-label">Survey Total (incl. SST 8%)</td>
                                        <td class="qs-amount">RM {{ number_format($finalTotal, 2) }}</td>
                                    </tr>
                                    <tr class="qs-modelling">
                                        <td class="qs-label">Modelling Total</td>
                                        <td class="qs-amount">RM {{ number_format($quotation->modelling_total, 2) }}</td>
                                    </tr>
                                    <tr class="qs-grand">
                                        <td class="qs-label">GRAND TOTAL</td>
                                        <td class="qs-amount">RM {{ number_format($quotation->grand_total, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                <!-- ADDITIONAL NOTES (hidden when empty) -->
                                @php $notes = trim($quotation->additional_notes ?? ''); @endphp
                                @if($notes !== '' && $notes !== '-')
                                    <div class="quote-box quote-box-light mb-4">
                                        <div class="mt-1" style="white-space: pre-wrap;">{{ $notes }}</div>
                                    </div>
                                @endif

                                <!-- PAGE 2 CONTENT: forced page break -->
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
                                    @if($signer && $signer->signature_path)
                                        <img src="{{ asset($signer->signature_path) }}" alt="Signature" style="max-height: 80px;" onerror="this.style.display='none'">
                                    @endif
                                    <p class="mb-0 fw-bold">{{ $signer->name ?? '-' }}</p>
                                    <p class="mb-0">{{ $signer->position ?? '' }}</p>
                                    <p class="mb-0">Eco Hydrotech Solutions Sdn. Bhd.</p>
                                </div>

                                </div>

                            </div>
                        </td>
                    </tr>
                </tbody>

            </table>

            <!-- FOOTER: fixed to the bottom of every printed page -->
            <div class="quote-print-footer">
                <img src="{{ asset('images/footer.jpeg') }}" alt="Footer" class="quote-footer-img">
            </div>

        </div>

    </div> <!-- end .max-w-7xl -->

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @endpush
</x-app-layout>