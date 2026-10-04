<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Invoice</x-slot>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* Base Overrides */
        body {
            color: #334155;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .history-container {
            max-width: none !important;
            margin: 0 !important;
        }

        /* Page Heading */
        .page-header-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
        }

        .page-header-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .page-header-subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0;
        }

        /* Back Button */
        .btn-back {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .btn-back:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        /* Quotation Summary Card */
        .summary-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .summary-label {
            display: block;
            font-size: 0.725rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            margin-bottom: 4px;
        }

        .summary-number {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .summary-total {
            text-align: right;
        }

        .summary-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0d9488;
        }

        /* Table Design */
        .table-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        .custom-project-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .custom-project-table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.725rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 14px 20px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .custom-project-table tbody td {
            padding: 16px 20px;
            font-size: 0.875rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            background-color: #ffffff;
        }

        .custom-project-table tbody tr:last-child td {
            border-bottom: none;
        }

        .custom-project-table tbody tr:hover td {
            background-color: #f8fafc;
        }

        .term-name {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.925rem;
        }

        .term-amount {
            font-weight: 700;
            color: #0f172a;
        }

        /* Circular Action Buttons */
        .action-buttons-group {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .action-circle-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .action-circle-btn:hover {
            background-color: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .table-card { overflow-x: auto; }
            .custom-project-table { min-width: 700px; }
            .summary-card { flex-direction: column; align-items: flex-start; gap: 12px; }
            .summary-total { text-align: left; }
        }
    </style>

    <!-- Main Content Layout Wrapper -->
    <div class="history-container">

        <!-- Page Heading Section -->
        <div class="page-header-wrapper">
            <div>
                <h2 class="page-header-title">Invoice</h2>
                <p class="page-header-subtitle">Generate and edit invoices for each payment term of this quotation. Printing is done from the invoice page.</p>
            </div>
            <a href="{{ url('/history') }}" class="btn-back">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>

        <!-- Quotation Summary Card -->
        <div class="summary-card">
            <div>
                <span class="summary-label">Quotation</span>
                <h3 class="summary-number">{{ $quotation->quotation_no }}</h3>
            </div>
            <div class="summary-total">
                <span class="summary-label">Total Amount</span>
                <span class="summary-amount">RM{{ number_format($finalTotal, 2) }}</span>
            </div>
        </div>

        <!-- Payment Terms Table -->
        <div class="table-card">
            <table class="custom-project-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Payment</th>
                        <th class="text-center" style="width: 15%;">Percentage</th>
                        <th style="width: 35%;">Condition</th>
                        <th class="text-end" style="width: 15%;">Total</th>
                        <th class="text-end" style="width: 15%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quotation->paymentTerms as $term)
                    <tr>
                        <td class="term-name">{{ $term->name }}</td>
                        <td class="text-center fw-semibold">{{ number_format($term->percentage, 0) }}%</td>
                        <td>{{ $term->condition }}</td>
                        <td class="text-end term-amount">RM {{ number_format($term->amount ?? ($finalTotal * ($term->percentage / 100)), 2) }}</td>
                        <td>
                            <div class="action-buttons-group">
                                @if($term->invoiceCopy)
                                    {{-- Invoice already generated: open the slip (Edit and Print are on that page) --}}
                                    <a href="{{ route('invoices.show', $term->invoiceCopy->invoice_Id) }}" class="action-circle-btn" title="Open Invoice">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @else
                                    {{-- No invoice yet for this term --}}
                                    <a href="{{ route('invoices.create', ['quotation' => $quotation->quotation_Id, 'term' => $term->id]) }}" class="action-circle-btn" title="Generate Invoice">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            No payment terms configured.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @endpush
</x-app-layout>