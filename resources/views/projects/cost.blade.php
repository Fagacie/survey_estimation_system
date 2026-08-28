<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-weight-bold text-dark mb-0">Cost Estimation: {{ $project->name }}</h2>
            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to Map
            </a>
        </div>
    </x-slot>

    <style>
        .eng-card { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: 16px; color: #fff; padding: 24px; margin-bottom: 24px; }
        .eng-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; }
        .eng-item { text-align: center; }
        .eng-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 700; margin-bottom: 4px; }
        .eng-value { font-size: 1.3rem; font-weight: 800; }
        .eng-value.cyan { color: #22d3ee; }
        .eng-value.amber { color: #fbbf24; }
        .eng-value.green { color: #34d399; }
        .eng-value.rose { color: #fb7185; }
        .eng-value.blue { color: #60a5fa; }
        .eng-divider { width: 1px; background: rgba(255,255,255,0.15); margin: 0 8px; }

        .cost-section { margin-bottom: 24px; }
        .cost-section-header { background: #f8fafc; padding: 12px 16px; border-radius: 12px 12px 0 0; border: 1px solid #e2e8f0; border-bottom: 2px solid; display: flex; justify-content: space-between; align-items: center; }
        .cost-section-header h6 { margin: 0; font-weight: 700; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .cost-section-header .subtotal { font-weight: 800; font-size: 0.9rem; }

        .cat-equipment { border-bottom-color: #0284c7; }
        .cat-equipment h6 { color: #0284c7; }
        .cat-personnel { border-bottom-color: #d97706; }
        .cat-personnel h6 { color: #d97706; }
        .cat-logistics { border-bottom-color: #9333ea; }
        .cat-logistics h6 { color: #9333ea; }
        .cat-analysis { border-bottom-color: #16a34a; }
        .cat-analysis h6 { color: #16a34a; }
        .cat-miscellaneous, .cat-general { border-bottom-color: #475569; }
        .cat-miscellaneous h6, .cat-general h6 { color: #475569; }

        .cost-table { width: 100%; border-collapse: collapse; }
        .cost-table th { background: #f8fafc; color: #64748b; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 14px; font-weight: 700; border-bottom: 1px solid #e2e8f0; }
        .cost-table td { padding: 8px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .cost-table .form-control-sm { border: 1px solid transparent; background: transparent; transition: all 0.2s; }
        .cost-table .form-control-sm:hover { background: #f8fafc; border-color: #e2e8f0; }
        .cost-table .form-control-sm:focus { background: #fff; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

        .summary-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; position: sticky; top: 20px; }
        .summary-card .card-header { background: linear-gradient(135deg, #0f172a, #1e293b); color: #fff; border-radius: 16px 16px 0 0; padding: 16px 20px; border: none; }
        .summary-total { font-size: 1.6rem; font-weight: 800; color: #0f172a; }
        .category-subtotal { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; }
        .category-subtotal:last-child { border-bottom: none; }

        .btn-action { border-radius: 10px; font-weight: 600; font-size: 0.85rem; padding: 10px 16px; width: 100%; margin-bottom: 8px; transition: all 0.2s; }
        .btn-action:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .btn-save { background: #0f172a; color: #fff; border: none; }
        .btn-save:hover { background: #1e293b; color: #fff; }
        .btn-recalc { background: #fff; color: #d97706; border: 2px solid #fbbf24; }
        .btn-recalc:hover { background: #fef3c7; color: #d97706; }
        .btn-report { background: #fff; color: #0284c7; border: 2px solid #0284c7; }
        .btn-report:hover { background: #e0f2fe; color: #0284c7; }
        .btn-quotation { background: #fff; color: #16a34a; border: 2px solid #16a34a; }
        .btn-quotation:hover { background: #dcfce7; color: #16a34a; }
    </style>

    <div class="container-fluid py-4" style="max-width: 1400px;">

        @if(session('success'))
            <div class="alert alert-success shadow-sm rounded-3 border-0 mb-4 fw-bold">
                <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        {{-- ── ENGINEERING SUMMARY ────────────────────────────────── --}}
        <div class="eng-card">
            <div class="eng-grid">
                <div class="eng-item">
                    <div class="eng-label">Distance</div>
                    <div class="eng-value cyan">{{ number_format($duration['distance_nm'], 2) }} NM</div>
                </div>
                <div class="eng-item">
                    <div class="eng-label">Speed</div>
                    <div class="eng-value blue">{{ $duration['speed_knots'] }} kn</div>
                </div>
                <div class="eng-item">
                    <div class="eng-label">Survey Hours</div>
                    <div class="eng-value amber">{{ number_format($duration['survey_hours'], 1) }} hrs</div>
                </div>
                <div class="eng-item">
                    <div class="eng-label">Execution</div>
                    <div class="eng-value green">{{ number_format($duration['execution_days'], 1) }} days</div>
                </div>
                <div class="eng-item">
                    <div class="eng-label">Weather</div>
                    <div class="eng-value" style="color:#94a3b8">{{ $duration['weather_days'] }} days</div>
                </div>
                <div class="eng-item">
                    <div class="eng-label">MOB/DEMOB</div>
                    <div class="eng-value" style="color:#94a3b8">{{ $duration['mod_demod_days'] }} days</div>
                </div>
                <div class="eng-item">
                    <div class="eng-label">Patch Test</div>
                    <div class="eng-value" style="color:#94a3b8">{{ $duration['patch_test_days'] }} days</div>
                </div>
                <div class="eng-item">
                    <div class="eng-label">Total Duration</div>
                    <div class="eng-value rose">{{ number_format($duration['total_days'], 1) }} days</div>
                </div>
            </div>
        </div>

        <form action="{{ route('projects.cost.store', $project->id) }}" method="POST" id="cost-form">
            @csrf
            <div class="row">

                {{-- ── LINE ITEMS (GROUPED BY CATEGORY) ──────────────── --}}
                <div class="col-lg-9 mb-4">

                    @php
                        $flatIndex = 0;
                        $categoryClasses = [
                            'Equipment' => 'cat-equipment',
                            'Personnel' => 'cat-personnel',
                            'Logistics' => 'cat-logistics',
                            'Analysis'  => 'cat-analysis',
                            'Miscellaneous' => 'cat-miscellaneous',
                            'General'   => 'cat-general',
                        ];
                    @endphp

                    @foreach($groupedItems as $category => $items)
                        <div class="cost-section">
                            <div class="cost-section-header {{ $categoryClasses[$category] ?? 'cat-general' }}">
                                <h6><i class="fa-solid fa-{{ match($category) {
                                    'Equipment' => 'wrench',
                                    'Personnel' => 'users',
                                    'Logistics' => 'truck',
                                    'Analysis' => 'chart-line',
                                    default => 'box'
                                } }} me-2"></i>{{ $category }}</h6>
                                <span class="subtotal category-subtotal-value" data-category="{{ $category }}">RM 0.00</span>
                            </div>
                            <div style="border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 12px 12px; overflow: hidden;">
                                <table class="cost-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 35%">Description</th>
                                            <th style="width: 12%" class="text-end">Days (Duration)</th>
                                            <th style="width: 12%" class="text-end">Qty/Pax</th>
                                            <th style="width: 16%" class="text-end">Unit Rate (RM)</th>
                                            <th style="width: 15%" class="text-end">Total (RM)</th>
                                            <th style="width: 10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr class="cost-row" data-category="{{ $category }}">
                                                <input type="hidden" name="items[{{ $flatIndex }}][cost_rate_id]" value="{{ $item->cost_rate_id ?? '' }}">
                                                <input type="hidden" name="items[{{ $flatIndex }}][category]" value="{{ $category }}">
                                                <td>
                                                    <input type="text" name="items[{{ $flatIndex }}][description]" class="form-control form-control-sm" value="{{ $item->description }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="items[{{ $flatIndex }}][days]" class="form-control form-control-sm item-days text-end @if(($item->unit_type ?? $item->costRate?->unit_type ?? '') === 'Lump Sum') bg-light text-muted @endif" value="{{ $item->days }}" required @if(($item->unit_type ?? $item->costRate?->unit_type ?? '') === 'Lump Sum') readonly tabindex="-1" title="Not applicable for Lump Sum" @endif>
                                                </td>
                                                <td>
                                                    <input type="number" step="1" name="items[{{ $flatIndex }}][units]" class="form-control form-control-sm item-units text-end @if(($item->unit_type ?? $item->costRate?->unit_type ?? '') === 'Lump Sum') bg-light text-muted @endif" value="{{ $item->units ?? 1 }}" required @if(($item->unit_type ?? $item->costRate?->unit_type ?? '') === 'Lump Sum') readonly tabindex="-1" title="Not applicable for Lump Sum" @endif>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="items[{{ $flatIndex }}][unit_rate]" class="form-control form-control-sm item-rate text-end" value="{{ $item->unit_rate }}" required>
                                                </td>
                                                <td class="text-end item-total fw-bold" style="font-size: 0.9rem;">
                                                    {{ number_format($item->total_price, 2) }}
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm text-danger btn-remove-row" style="background: none; border: none; opacity: 0.5;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.5">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            @php $flatIndex++; @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    {{-- Add custom item --}}
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold" id="btn-add-row">
                            <i class="fa-solid fa-plus me-1"></i> Add Custom Line Item
                        </button>
                    </div>
                </div>

                {{-- ── SUMMARY PANEL ─────────────────────────────────── --}}
                <div class="col-lg-3">
                    <div class="summary-card">
                        <div class="card-header">
                            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-calculator me-2"></i>Summary</h5>
                        </div>
                        <div class="card-body p-3">

                            <div class="mb-3">
                                <label class="text-muted small text-uppercase fw-bold mb-1">Total Duration</label>
                                <h4 class="fw-bold mb-0">{{ number_format($duration['total_days'], 1) }} <small class="text-muted">days</small></h4>
                            </div>

                            <hr>

                            <label class="text-muted small text-uppercase fw-bold mb-2">By Category</label>
                            <div id="category-breakdown">
                                {{-- Populated by JS --}}
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem;">Grand Total</span>
                                <div>
                                    <span class="text-muted me-1" style="font-size: 0.8rem;">RM</span>
                                    <span class="summary-total" id="grand-total">0.00</span>
                                </div>
                            </div>

                            @if($estimation && $estimation->status === 'Manual')
                                <div class="alert alert-warning py-2 px-3 mb-3" style="font-size: 0.75rem; border-radius: 8px;">
                                    <i class="fa-solid fa-pen me-1"></i> Manually edited. Rates may differ from master.
                                </div>
                            @endif

                            <button type="submit" class="btn btn-action btn-save">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Save Estimation
                            </button>
                        </div>

                        <div class="card-footer bg-transparent border-top p-3">
                            <button type="button" class="btn btn-action btn-recalc" id="btn-recalculate">
                                <i class="fa-solid fa-arrows-rotate me-2"></i> Recalculate from Master
                            </button>
                            <a href="{{ route('projects.report.pdf', $project->id) }}" class="btn btn-action btn-report" target="_blank">
                                <i class="fa-solid fa-file-pdf me-2"></i> Download Report
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- Hidden template for new rows --}}
    <template id="row-template">
        <tr class="cost-row" data-category="Miscellaneous">
            <input type="hidden" name="items[__INDEX__][cost_rate_id]" value="">
            <input type="hidden" name="items[__INDEX__][category]" value="Miscellaneous">
            <td>
                <input type="text" name="items[__INDEX__][description]" class="form-control form-control-sm" placeholder="Custom item..." required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[__INDEX__][days]" class="form-control form-control-sm item-days text-end" value="1" required>
            </td>
            <td>
                <input type="number" step="1" name="items[__INDEX__][units]" class="form-control form-control-sm item-units text-end" value="1" required>
            </td>
            <td>
                <input type="number" step="0.01" name="items[__INDEX__][unit_rate]" class="form-control form-control-sm item-rate text-end" value="0.00" required>
            </td>
            <td class="text-end item-total fw-bold" style="font-size: 0.9rem;">0.00</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm text-danger btn-remove-row" style="background: none; border: none;">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </td>
        </tr>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let itemIndex = {{ $flatIndex ?? count($lineItems ?? []) }};
            const tbody = document.querySelector('#cost-form');
            const btnAdd = document.getElementById('btn-add-row');
            const grandTotalEl = document.getElementById('grand-total');
            const template = document.getElementById('row-template').innerHTML;

            function formatCurrency(value) {
                return parseFloat(value).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function calculateTotals() {
                let grandTotal = 0;
                let categoryTotals = {};

                document.querySelectorAll('.cost-row').forEach(row => {
                    const days = parseFloat(row.querySelector('.item-days')?.value) || 0;
                    const units = parseInt(row.querySelector('.item-units')?.value) || 1;
                    const rate = parseFloat(row.querySelector('.item-rate')?.value) || 0;
                    
                    const total = days * units * rate;
                    const totalEl = row.querySelector('.item-total');
                    if (totalEl) totalEl.innerText = formatCurrency(total);
                    grandTotal += total;

                    const cat = row.dataset.category || 'General';
                    categoryTotals[cat] = (categoryTotals[cat] || 0) + total;
                });

                grandTotalEl.innerText = formatCurrency(grandTotal);

                // Update category subtotals in table headers
                document.querySelectorAll('.category-subtotal-value').forEach(el => {
                    const cat = el.dataset.category;
                    el.innerText = 'RM ' + formatCurrency(categoryTotals[cat] || 0);
                });

                // Update sidebar breakdown
                const breakdownEl = document.getElementById('category-breakdown');
                breakdownEl.innerHTML = '';
                for (const [cat, total] of Object.entries(categoryTotals)) {
                    breakdownEl.innerHTML += `<div class="category-subtotal"><span>${cat}</span><span class="fw-bold">RM ${formatCurrency(total)}</span></div>`;
                }
            }

            // Event delegation for inputs
            document.getElementById('cost-form').addEventListener('input', function(e) {
                if (e.target.classList.contains('item-days') || e.target.classList.contains('item-units') || e.target.classList.contains('item-rate')) {
                    calculateTotals();
                }
            });

            // Event delegation for remove buttons
            document.getElementById('cost-form').addEventListener('click', function(e) {
                let btn = e.target.closest('.btn-remove-row');
                if (btn) {
                    btn.closest('tr').remove();
                    calculateTotals();
                }
            });

            // Add custom item — append to Miscellaneous section or create one
            btnAdd.addEventListener('click', function() {
                let html = template.replace(/__INDEX__/g, itemIndex++);

                // Find the last cost-table tbody, or append to the form
                let lastTbody = document.querySelector('.cost-section:last-of-type .cost-table tbody');
                if (lastTbody) {
                    lastTbody.insertAdjacentHTML('beforeend', html);
                }
                calculateTotals();
            });

            // Recalculate button
            document.getElementById('btn-recalculate').addEventListener('click', function() {
                Swal.fire({
                    title: 'Recalculate from Master Rates?',
                    text: 'This will discard all manual edits and regenerate items from the current master rate catalog.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d97706',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, recalculate'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // POST to recalculate endpoint
                        let form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route("projects.cost.recalculate", $project->id) }}';
                        let csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        form.appendChild(csrf);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });

            // Initial calculation
            calculateTotals();
        });
    </script>

</x-app-layout>
