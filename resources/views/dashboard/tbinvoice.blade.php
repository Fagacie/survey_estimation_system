<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Invoice Milestones</x-slot>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Custom Modern Stylesheet --}}
    <link rel="stylesheet" href="{{ asset('css/tbinvoice.css') }}?v={{ filemtime(public_path('css/tbinvoice.css')) }}">

    <div class="tbinvoice-page-wrap">

        {{-- Breadcrumb Navigation --}}
        <nav class="tb-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Dashboard</a>
            <span class="tb-breadcrumb-sep">/</span>
            <a href="{{ route('quotations.history') }}">Quotation History</a>
            <span class="tb-breadcrumb-sep">/</span>
            <a href="{{ route('quotations.show', $quotation->quotation_Id) }}">{{ $quotation->quotation_no }}</a>
            <span class="tb-breadcrumb-sep">/</span>
            <span class="tb-breadcrumb-current">Invoice Milestones</span>
        </nav>

        {{-- Page Header Card --}}
        <div class="tb-header-card">
            <div>
                <span class="tb-header-badge">
                    <i class="bi bi-receipt-cutoff"></i> Quotation Billing Schedule
                </span>
                <h1 class="tb-header-title">Invoice Milestones</h1>
                <p class="tb-header-subtitle">
                    Manage billing milestones, review progress, and generate official invoice slips for quotation <strong>{{ $quotation->quotation_no }}</strong>.
                </p>
            </div>
            <div class="tb-header-actions">
                <a href="{{ route('quotations.show', $quotation->quotation_Id) }}" class="btn-tb-secondary">
                    <i class="bi bi-file-earmark-text"></i> View Quotation
                </a>
                <a href="{{ route('quotations.history') }}" class="btn-tb-secondary">
                    <i class="bi bi-arrow-left"></i> Quotation History
                </a>
            </div>
        </div>

        {{-- KPI Summary Cards Grid --}}
        @php
            $invoicedTerms = $quotation->paymentTerms->filter(fn($t) => $t->invoiceCopy)->count();
            $totalTerms = $quotation->paymentTerms->count();
            $invoicedPct = $totalTerms > 0 ? round(($invoicedTerms / $totalTerms) * 100) : 0;
        @endphp
        <div class="tb-metrics-grid">
            {{-- Quotation Reference --}}
            <div class="tb-metric-card">
                <div class="tb-metric-top">
                    <div>
                        <div class="tb-metric-label">Quotation Reference</div>
                        <div class="tb-metric-value" style="font-size: 1.25rem;">{{ $quotation->quotation_no }}</div>
                    </div>
                    <div class="tb-metric-icon blue">
                        <i class="bi bi-file-earmark-check"></i>
                    </div>
                </div>
                <div class="tb-metric-subtext">
                    <i class="bi bi-folder2-open text-sky-600"></i>
                    <span>{{ Str::limit($quotation->project->name ?? 'Standard Project', 26) }}</span>
                </div>
            </div>

            {{-- Total Contract Value --}}
            <div class="tb-metric-card">
                <div class="tb-metric-top">
                    <div>
                        <div class="tb-metric-label">Total Contract Value</div>
                        <div class="tb-metric-value">RM {{ number_format($finalTotal, 2) }}</div>
                    </div>
                    <div class="tb-metric-icon teal">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
                <div class="tb-metric-subtext">
                    <i class="bi bi-info-circle text-teal-600"></i>
                    <span>Includes SST: RM {{ number_format($sstAmount, 2) }}</span>
                </div>
            </div>

            {{-- Payment Terms Count --}}
            <div class="tb-metric-card">
                <div class="tb-metric-top">
                    <div>
                        <div class="tb-metric-label">Configured Terms</div>
                        <div class="tb-metric-value">{{ $totalTerms }} Milestones</div>
                    </div>
                    <div class="tb-metric-icon indigo">
                        <i class="bi bi-diagram-3"></i>
                    </div>
                </div>
                <div class="tb-metric-subtext">
                    <i class="bi bi-pie-chart text-indigo-600"></i>
                    <span>100% allocation scheduled</span>
                </div>
            </div>

            {{-- Invoicing Progress --}}
            <div class="tb-metric-card">
                <div class="tb-metric-top">
                    <div>
                        <div class="tb-metric-label">Invoicing Progress</div>
                        <div class="tb-metric-value">{{ $invoicedTerms }} of {{ $totalTerms }} Slips</div>
                    </div>
                    <div class="tb-metric-icon amber">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
                <div>
                    <div class="tb-progress-track">
                        <div class="tb-progress-bar" style="width: {{ $invoicedPct }}%;"></div>
                    </div>
                    <div class="tb-metric-subtext" style="margin-top: 6px; font-size: 0.75rem;">
                        <span>{{ $invoicedPct }}% of milestones issued</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment Terms Table Card --}}
        <div class="tb-table-card">
            <div class="tb-table-header">
                <div>
                    <h2 class="tb-table-title">
                        <i class="bi bi-calendar-check" style="color: var(--tb-primary);"></i>
                        Payment Terms &amp; Invoicing Schedule
                        <span class="tb-count-badge">{{ $totalTerms }} Terms</span>
                    </h2>
                    <p class="tb-table-help">
                        Issue slips for pending payment milestones or view/print generated invoices.
                    </p>
                </div>
            </div>

            <div class="tb-table-responsive">
                <table class="tb-table">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Milestone Name</th>
                            <th class="text-center" style="width: 12%;">Allocation</th>
                            <th style="width: 35%;">Condition / Trigger</th>
                            <th class="text-end" style="width: 13%;">Total Amount</th>
                            <th class="text-end" style="width: 15%;">Status &amp; Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotation->paymentTerms as $index => $term)
                        <tr>
                            <td>
                                <div class="tb-term-cell">
                                    <span class="tb-seq-chip">{{ sprintf('%02d', $index + 1) }}</span>
                                    <div>
                                        <div class="tb-term-title">{{ $term->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="tb-percent-pill">{{ number_format($term->percentage, 0) }}%</span>
                            </td>
                            <td>
                                <div class="tb-condition-text">
                                    {{ $term->condition ?: 'Standard milestone payment trigger.' }}
                                </div>
                            </td>
                            <td class="tb-amount-cell">
                                RM {{ number_format($term->amount ?? ($finalTotal * ($term->percentage / 100)), 2) }}
                            </td>
                            <td class="tb-action-cell">
                                @if($term->invoiceCopy)
                                    <span class="tb-status-badge invoiced">
                                        <i class="bi bi-check-circle-fill"></i> Invoiced
                                    </span>
                                    <a href="{{ route('invoices.show', $term->invoiceCopy->invoice_Id) }}" class="btn-tb-row view" title="Open Invoice Slip">
                                        <i class="bi bi-pencil-square"></i> Open Slip
                                    </a>
                                @else
                                    <span class="tb-status-badge pending">
                                        <i class="bi bi-clock-history"></i> Pending
                                    </span>
                                    <a href="{{ route('invoices.create', ['quotation' => $quotation->quotation_Id, 'term' => $term->id]) }}" class="btn-tb-row create" title="Generate Invoice Slip">
                                        <i class="bi bi-receipt"></i> Generate
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="tb-empty-state">
                                    <div class="tb-empty-icon">
                                        <i class="bi bi-receipt"></i>
                                    </div>
                                    <div class="tb-empty-title">No Payment Terms Configured</div>
                                    <p class="tb-empty-desc">
                                        No billing terms have been established for this quotation yet. Check the quotation configuration.
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @endpush
</x-app-layout>