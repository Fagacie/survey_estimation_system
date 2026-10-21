<!DOCTYPE html>
<html>
<head>
    <title>Quotation #{{ $quotation->quotation_no }}</title>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0cm;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #212529;
            margin-top: 2.35cm;
            margin-bottom: 1.6cm;
            margin-left: 1.4cm;
            margin-right: 1.4cm;
            font-size: 12px;
            line-height: 1.35;
        }

        /* Fixed Header and Footer */
        header {
            position: fixed;
            top: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2.3cm;
        }

        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 1.5cm;
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
        .mt-3 { margin-top: 15px; }
        .mt-4 { margin-top: 20px; }
        
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
            border-bottom: 2px solid #212529;
            margin: 6px 0 10px 0;
        }

        /* Boxes */
        .quote-box {
            background-color: #dfeefc;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 8px;
            font-size: 11.5px;
        }
        
        .quote-box-light {
            background-color: #eef6fb;
        }

        .quote-box-label {
            font-weight: 700;
            color: #1c53a0;
            font-size: 11.5px;
            text-transform: uppercase;
        }

        /* Tables */
        table { width: 100%; border-collapse: collapse; page-break-inside: auto; }
        
        tr { page-break-inside: avoid; page-break-after: auto; }
        
        .quote-table thead th {
            background-color: #1c6e7a;
            color: #ffffff;
            font-weight: 600;
            font-size: 11.5px;
            padding: 5px 8px;
            border: none;
            text-align: left;
        }
        
        .quote-table tbody td {
            background-color: #f5f5f5;
            padding: 5px 8px;
            font-size: 11.5px;
            border: none;
        }

        .quote-module-row td {
            background-color: #d6e9ec !important;
            color: #1c6e7a;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            padding: 6px 8px;
        }

        .quote-service-row td {
            font-weight: 600;
            padding-left: 20px;
            background-color: #ececec !important;
            padding-top: 4px;
            padding-bottom: 4px;
        }

        .quote-item-cell {
            padding-left: 40px !important;
        }

        .quote-totals-table {
            width: 270px;
            font-size: 12px;
            float: right;
            margin-bottom: 10px;
        }
        
        .quote-totals-table td {
            padding: 4px 8px;
        }
        
        .quote-totals-table tr:not(.quote-grand-total-row) td:last-child {
            background-color: #f0f0f0;
        }
        
        .quote-grand-total-row td {
            background-color: #1c6e7a;
            color: #ffffff;
            font-weight: 700;
        }

        /* Combined totals (Survey + Modelling + Grand Total) */
        .quote-summary-table {
            width: 100%;
            border-collapse: collapse;
            clear: both;
        }

        .quote-summary-table td {
            background-color: #ececec;
            color: #504f4f;
            font-weight: 500;
            font-size: 13px;
            padding: 4px 8px;
            border-bottom: 1px solid #ffffff;
        }

        .quote-summary-table td.qs-amount {
            text-align: right;
            white-space: nowrap;
            width: 1%;
        }

        .quote-summary-table tr.qs-grand td {
            font-size: 14px;
            padding-top: 7px;
            padding-bottom: 7px;
            border-bottom: none;
        }

        .quote-section-heading {
            color: #1c53a0;
            font-weight: 700;
            font-size: 12.5px;
            margin-bottom: 6px;
            margin-top: 0;
        }

        .signature-block {
            page-break-inside: avoid;
            margin-top: 15px;
        }

        .page-break {
            page-break-before: always;
        }
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
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
        
        <!-- LOGO & COMPANY INFO (Table layout for DomPDF) -->
        <table class="w-100 mb-2">
            <tr>
                <td style="width: 40%; vertical-align: top;">
                    <img src="{{ $getImg('images/logo.jpeg') }}" height="65">
                </td>
                <td style="width: 60%; vertical-align: top; font-size: 11px; line-height: 1.2; color: #212529;" class="text-end">
                    <strong>ECO HYDROTECH SOLUTIONS SDN. BHD. (1688434-T)</strong><br>
                    TH3, Level 2, TELIPOTS HUB<br>
                    Institute of Oceanography and Environment<br>
                    Universiti Malaysia Terengganu<br>
                    21030, Kuala Nerus, Terengganu<br>
                    Malaysia
                </td>
            </tr>
        </table>

        <!-- TITLE + QUOTATION NO / DATE -->
        <table class="w-100">
            <tr>
                <td style="vertical-align: bottom;">
                    <h2 class="quote-title">QUOTATION</h2>
                </td>
                <td class="text-end quote-meta" style="vertical-align: bottom;">
                    <div>Quotation No: <strong>{{ $quotation->quotation_no }}</strong></div>
                    <div>Date: <strong>{{ $quotation->created_at ? $quotation->created_at->format('d M Y') : '-' }}</strong></div>
                </td>
            </tr>
        </table>
        
        <hr class="quote-hr">

        <!-- CLIENT NAME / ADDRESS BOX -->
        <div class="quote-box">
            <div class="quote-box-label">{{ $quotation->project->client->company_name ?? '-' }}</div>
            <div class="mt-1" style="white-space: pre-line;">{{ $quotation->project->client->client_address ?? '-' }}</div>
        </div>

        <p class="mb-2">Dear Sir/Madam,</p>

        <!-- PROJECT BOX -->
        <div class="quote-box mb-2">
            <div class="quote-box-label">PROJECT</div>
            <div class="mt-1 fw-bold">{{ $quotation->project->name ?? '-' }}</div>
            <div class="mt-1"><strong>{{ $quotation->project->pic_name ?? '-' }}</strong></div>
            <div><strong>{{ $quotation->project->pic_no ?? '-' }}</strong></div>
        </div>

        {{-- ===== Work out what this quotation contains (survey / modelling) ===== --}}
        @php
            $mdlProject = $quotation->project;
            $mdlSummary = $mdlProject ? $mdlProject->modellingSummary : null;
            $mdlGroups = $mdlSummary
                ? $mdlProject->modellingItems()->with(['catalogModule', 'catalogItem'])->get()->groupBy('catalog_module_id')
                : collect();

            $hasModelling = $mdlSummary && $mdlGroups->isNotEmpty() && ($quotation->modelling_total ?? 0) > 0;
            $hasSurvey    = $quotation->items->isNotEmpty();

            // Hide the survey table only when there is modelling but no survey items.
            // If there is nothing at all, still show the survey table ("No items added").
            $showSurvey   = $hasSurvey || !$hasModelling;

            // Survey totals (survey_total is the survey subtotal before SST)
            $subtotal   = (float)($quotation->survey_total ?? 0);
            $sst        = round($subtotal * 0.08, 2);
            $finalTotal = $subtotal + $sst;   // survey total incl. SST
        @endphp

        @if($showSurvey)
        <!-- ITEMS TABLE -->
        <table class="quote-table mb-2">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-center" style="width: 60px;">Qty</th>
                    <th class="text-center" style="width: 70px;">Day/Sample</th>
                    <th class="text-end" style="width: 100px;">Unit Price (RM)</th>
                    <th class="text-end" style="width: 100px;">Total (RM)</th>
                </tr>
            </thead>
            <tbody>
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
                        <td colspan="5">{{ $loop->iteration }}. {{ $moduleName }}</td>
                    </tr>

                    @php
                        $groups = $moduleLines->groupBy(fn($l) =>
                            ($l->catalogItem->category->category_name ?? '') . '|' . ($l->catalogItem->service->service_name ?? '')
                        );
                    @endphp

                    @foreach($groups as $groupLines)
                        @php
                            $first = $groupLines->first();
                            $label = collect([
                                $first->catalogItem->category->category_name ?? '',
                                $first->catalogItem->service->service_name ?? '',
                            ])->filter()->unique()->implode(' - ');
                        @endphp

                        <tr class="quote-service-row">
                            <td colspan="5">{{ $toRoman($loop->iteration) }}) {{ $label ?: '-' }}</td>
                        </tr>

                        @foreach($groupLines as $line)
                            <tr>
                                <td class="quote-item-cell">
                                    &bull; {{ $line->catalogItem->item_name ?? $line->custom_item_name ?? 'Service Item' }}
                                </td>
                                <td class="text-center">{{ $line->unit_qty }}</td>
                                <td class="text-center">{{ $line->days }}</td>
                                <td class="text-end">{{ number_format((float)($line->daily_rate ?? 0), 2) }}</td>
                                <td class="text-end">{{ number_format((float)($line->line_total ?? 0), 2) }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">No items added to the quotation yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- TOTALS -->
        <div class="clearfix mb-4">
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
        @endif

        {{-- ===== MODELLING ===== --}}
        @if($hasModelling)
        <div class="mb-4">
            <h6 class="quote-section-heading">MODELLING</h6>
            <div class="mb-2" style="font-size: 12px;">
                Package: <strong>{{ $mdlSummary->package_name ?? 'Custom selection' }}</strong>
            </div>

            <table class="quote-table mb-2">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center" style="width: 60px;">Qty</th>
                        <th class="text-center" style="width: 70px;">Day/Sample</th>
                        <th class="text-end" style="width: 100px;">Unit Price (RM)</th>
                        <th class="text-end" style="width: 100px;">Total (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($mdlGroups as $items)
                        <tr class="quote-module-row">
                            <td colspan="5">{{ $items->first()->catalogModule->name ?? 'Module' }}</td>
                        </tr>
                        @foreach($items as $line)
                            @php $units = max(($line->unit_qty ?? 1) * ($line->days ?? 1), 1); @endphp
                            <tr>
                                <td class="quote-item-cell">
                                    &bull; {{ $line->catalogItem->name ?? 'Item' }}
                                </td>
                                <td class="text-center">{{ rtrim(rtrim(number_format((float)($line->unit_qty ?? 0), 2, '.', ''), '0'), '.') }}</td>
                                <td class="text-center">{{ rtrim(rtrim(number_format((float)($line->days ?? 0), 2, '.', ''), '0'), '.') }}</td>
                                <td class="text-end">{{ number_format((float)($line->line_total ?? 0) / $units, 2) }}</td>
                                <td class="text-end">{{ number_format((float)($line->line_total ?? 0), 2) }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

            <div class="clearfix">
                <table class="quote-totals-table">
                    <tr>
                        <td>Modelling Subtotal</td>
                        <td class="text-end">RM {{ number_format((float)($mdlSummary->client_subtotal ?? 0), 2) }}</td>
                    </tr>
                    @if($mdlSummary->contingency_amount > 0)
                    <tr>
                        <td>Contingency {{ $mdlSummary->contingency_percent }}%</td>
                        <td class="text-end">RM {{ number_format((float)($mdlSummary->contingency_amount ?? 0), 2) }}</td>
                    </tr>
                    @endif
                    @if($mdlSummary->tax_amount > 0)
                    <tr>
                        <td>SST {{ $mdlSummary->tax_percent }}%</td>
                        <td class="text-end">RM {{ number_format((float)($mdlSummary->tax_amount ?? 0), 2) }}</td>
                    </tr>
                    @endif
                    <tr class="quote-grand-total-row">
                        <td>Modelling Total</td>
                        <td class="text-end">RM {{ number_format((float)($mdlSummary->grand_total ?? 0), 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        @endif

        {{-- Summary box: only when BOTH survey and modelling exist --}}
        @if($hasSurvey && $hasModelling)
        <table class="quote-summary-table mb-4">
            <tr class="qs-survey">
                <td class="qs-label">Survey Total (incl. SST 8%)</td>
                <td class="qs-amount">RM {{ number_format($finalTotal, 2) }}</td>
            </tr>
            <tr class="qs-modelling">
                <td class="qs-label">Modelling Total</td>
                <td class="qs-amount">RM {{ number_format((float)$quotation->modelling_total, 2) }}</td>
            </tr>
            <tr class="qs-grand">
                <td class="qs-label">GRAND TOTAL</td>
                <td class="qs-amount">RM {{ number_format((float)$quotation->grand_total, 2) }}</td>
            </tr>
        </table>
        @endif

        <!-- ADDITIONAL NOTES -->
        @php $notes = trim($quotation->additional_notes ?? ''); @endphp
        @if($notes !== '' && $notes !== '-')
            <div class="quote-box quote-box-light mb-4" style="clear: both;">
                <div style="white-space: pre-wrap;">{{ $notes }}</div>
            </div>
        @endif

        <!-- PAYMENT TERMS AND SIGNATURE -->

        <table class="w-100 mt-2 mb-2" style="page-break-inside: avoid;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 20px;">
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
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <h6 class="quote-section-heading">Payment Information</h6>
                    <div>
                        <strong>Recipient:</strong> ECO HYDROTECH SOLUTIONS SDN. BHD.<br>
                        <strong>Bank:</strong> MAYBANK ISLAMIC<br>
                        <strong>Account Number:</strong> 5630 6496 5609<br>
                        <strong>Bank's Address:</strong> 1-j, Kuala Terengganu Branch, Kompleks Perdana, Jalan Air Jernih, 20300 Kuala Terengganu, Terengganu
                    </div>
                </td>
            </tr>
        </table>

        <!-- SIGNATURE BLOCK -->
        <div class="signature-block">
            <p class="mb-1">Yours sincerely,</p>
            @if(isset($signer) && $signer->signature_path)
                @if(file_exists(public_path($signer->signature_path)))
                    <img src="{{ $getImg($signer->signature_path) }}" style="max-height: 55px; margin-bottom: 5px;">
                @endif
            @endif
            <p class="mb-0 fw-bold">{{ $signer->name ?? '-' }}</p>
            <p class="mb-0">{{ $signer->position ?? '' }}</p>
            <p class="mb-0">Eco Hydrotech Solutions Sdn. Bhd.</p>
        </div>

    </main>
</body>
</html>