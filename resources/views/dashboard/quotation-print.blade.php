<!DOCTYPE html>
<html>
<head>
    <title>Quotation #{{ $quotation->quotation_no }}</title>
    <meta charset="utf-8">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('css/quotation-print.css') }}">
</head>
<body onload="window.print()">
    <div class="max-w-7xl mx-auto">
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
                                        TH3, Level 2, TELIPOTS HUB<br>
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
                                                    <td colspan="5" class="quote-service-cell">{{ $toRoman($loop->iteration) }}) {{ $label ?: '-' }}</td>
                                                </tr>

                                                @foreach($groupLines as $line)
                                                    <tr>
                                                        <td class="quote-item-cell">
                                                            <div class="d-flex"><span class="me-2">&bull;</span><span>{{ $line->catalogItem->item_name ?? $line->custom_item_name ?? 'Service Item' }}</span></div>
                                                        </td>
                                                        <td class="text-center">{{ $line->unit_qty }}</td>
                                                        <td class="text-center">{{ $line->days }}</td>
                                                        <td class="text-end">{{ number_format($line->daily_rate, 2) }}</td>
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
                                    $subtotal = $quotation->grand_total;
                                    $sst = $subtotal * 0.08;
                                    $finalTotal = $subtotal + $sst;
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

                                @if($mdlSummary && $mdlGroups->isNotEmpty())
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
</body>
</html>