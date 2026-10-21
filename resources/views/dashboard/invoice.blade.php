<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Invoice {{ $invoice->invoice_number }}</x-slot>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/invoice.css') }}?v={{ filemtime(public_path('css/invoice.css')) }}">
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
                    <a href="{{ route('invoices.download', $invoice->invoice_Id) }}" target="_blank" class="ctrl-btn ctrl-btn-primary">
                        <i class="fa-solid fa-file-pdf"></i> Download PDF
                    </a>
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

    {{-- EDIT INVOICE MODAL (dates + description + condition in one form) --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="invoiceForm">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="invoiceFormErrors" class="alert alert-danger d-none" style="white-space: pre-line;"></div>

                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Invoice Date</label>
                                <input type="date" class="form-control" name="invoice_date" required
                                       value="{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Due Date</label>
                                <input type="date" class="form-control" name="due_date" required
                                       value="{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : '' }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" name="description" rows="3">{{ $invoice->description }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Payment Note (Condition)</label>
                            <textarea class="form-control" name="condition" rows="3">{{ $invoice->paymentTerm->condition }}</textarea>
                        </div>

                        <hr>

                        {{-- APPROVED BY: pick a saved person, or add a new one --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Approved by</label>
                            <select class="form-select" name="signatory_id" id="signatorySelect" required>
                                @foreach($signatories as $s)
                                    <option value="{{ $s->id }}" {{ optional($signer)->id == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }}
                                    </option>
                                @endforeach
                                <option value="new">+ Add new person...</option>
                            </select>
                        </div>

                        <div id="newSignatoryFields" class="border rounded p-3 d-none">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Name</label>
                                <input type="text" class="form-control" name="new_name" maxlength="255">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Position</label>
                                <input type="text" class="form-control" name="new_position" maxlength="255">
                            </div>
                            <div>
                                <label class="form-label fw-semibold">Signature image</label>
                                <input type="file" class="form-control" name="new_signature" accept="image/png,image/jpeg">
                                <div class="form-text">PNG with a transparent background looks best. Max 2 MB.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "{{ csrf_token() }}";

        // Approved by: show the "new person" fields only when "+ Add new person" is chosen
        const signatorySelect = document.getElementById('signatorySelect');
        const newSignatoryFields = document.getElementById('newSignatoryFields');

        function toggleNewSignatory() {
            if (!signatorySelect || !newSignatoryFields) return;
            newSignatoryFields.classList.toggle('d-none', signatorySelect.value !== 'new');
        }

        signatorySelect?.addEventListener('change', toggleNewSignatory);
        toggleNewSignatory();

        document.getElementById('invoiceForm')?.addEventListener('submit', async function (e) {
            e.preventDefault();

            const btn = this.querySelector('button[type="submit"]');
            const errBox = document.getElementById('invoiceFormErrors');
            errBox.classList.add('d-none');
            btn.disabled = true;

            try {
                const res = await fetch('{{ route("invoices.updateDetails", $invoice->invoice_Id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: new FormData(this)
                });

                if (res.ok) {
                    location.reload();
                    return;
                }

                if (res.status === 422) {
                    const data = await res.json();
                    errBox.textContent = Object.values(data.errors).flat().join('\n');
                    errBox.classList.remove('d-none');
                } else {
                    alert('Failed to save.');
                }
            } catch (err) {
                alert('Failed to save. Please check your connection and try again.');
            }

            btn.disabled = false;
        });
    });
    </script>
    @endpush
</x-app-layout>