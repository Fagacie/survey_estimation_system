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
            margin-top: 1.2cm;
            margin-bottom: 2.0cm;
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
            height: 1.2cm;
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
            margin: 10px 0 15px 0;
        }

        /* Boxes */
        .quote-box {
            background-color: #dfeefc;
            border-radius: 4px;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-size: 12px;
        }
        
        .quote-box-light {
            background-color: #eef6fb;
        }

        .quote-box-label {
            font-weight: 700;
            color: #1c53a0;
            font-size: 12px;
            text-transform: uppercase;
        }

        /* Tables */
        table { width: 100%; border-collapse: collapse; page-break-inside: auto; }
        
        tr { page-break-inside: avoid; page-break-after: auto; }
        
        .quote-table thead th {
            background-color: #1c6e7a;
            color: #ffffff;
            font-weight: 600;
            font-size: 12px;
            padding: 6px 10px;
            border: none;
            text-align: left;
        }
        
        .quote-table tbody td {
            background-color: #f5f5f5;
            padding: 6px 10px;
            font-size: 12px;
            border: none;
        }

        .quote-module-row td {
            background-color: #d6e9ec !important;
            color: #1c6e7a;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            padding: 8px 10px;
        }

        .quote-service-row td {
            font-weight: 600;
            padding-left: 20px;
            background-color: #ececec !important;
        }

        .quote-item-cell {
            padding-left: 40px !important;
        }

        .quote-totals-table {
            width: 280px;
            font-size: 13px;
            float: right;
            margin-bottom: 20px;
        }
        
        .quote-totals-table td {
            padding: 5px 10px;
        }
        
        .quote-totals-table tr:not(.quote-grand-total-row) td:last-child {
            background-color: #f0f0f0;
        }
        
        .quote-grand-total-row td {
            background-color: #1c6e7a;
            color: #ffffff;
            font-weight: 700;
        }

        .quote-section-heading {
            color: #1c53a0;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 10px;
            margin-top: 0;
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
                    <img src="{{ $getImg('images/logo.jpeg') }}" height="80">
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

        <p class="mb-3">Dear Sir/Madam,</p>

        <!-- PROJECT BOX -->
        <div class="quote-box mb-4">
            <div class="quote-box-label">PROJECT</div>
            <div class="mt-1 fw-bold">{{ $quotation->project->name ?? '-' }}</div>
            <div class="mt-1"><strong>{{ $quotation->project->pic_name ?? '-' }}</strong></div>
            <div><strong>{{ $quotation->project->pic_no ?? '-' }}</strong></div>
        </div>

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

        <!-- TOTALS CALCULATION -->
        @php
            $subtotal = (float)($quotation->grand_total ?? 0);
            $sst = $subtotal * 0.08;
            $finalTotal = $subtotal + $sst;
        @endphp

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

        {{-- ===== MODELLING ===== --}}
        @php
            $mdlProject = $quotation->project;
            $mdlSummary = $mdlProject ? $mdlProject->modellingSummary : null;
            $mdlGroups = $mdlSummary
                ? $mdlProject->modellingItems()->with(['catalogModule', 'catalogItem'])->get()->groupBy('catalog_module_id')
                : collect();
        @endphp

        @if($mdlSummary && $mdlGroups->isNotEmpty())
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

        <!-- ADDITIONAL NOTES -->
        @php $notes = trim($quotation->additional_notes ?? ''); @endphp
        @if($notes !== '' && $notes !== '-')
            <div class="quote-box quote-box-light mb-4" style="clear: both;">
                <div style="white-space: pre-wrap;">{{ $notes }}</div>
            </div>
        @endif

        <!-- PAYMENT TERMS AND SIGNATURE -->

        <table class="w-100 mt-4 mb-4">
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
        <div style="margin-top: 40px;">
            <p class="mb-4">Yours sincerely,</p>
            @if(isset($signer) && $signer->signature_path)
                @if(file_exists(public_path($signer->signature_path)))
                    <img src="{{ $getImg($signer->signature_path) }}" style="max-height: 80px; margin-bottom: 10px;">
                @endif
            @endif
            <p class="mb-0 fw-bold">{{ $signer->name ?? '-' }}</p>
            <p class="mb-0">{{ $signer->position ?? '' }}</p>
            <p class="mb-0">Eco Hydrotech Solutions Sdn. Bhd.</p>
        </div>

    </main>
</body>
</html>
