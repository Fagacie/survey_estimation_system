<x-app-layout containerClass="w-full px-8 py-8">
    <x-slot name="header">
        Cost Estimation: {{ $project->name }}
    </x-slot>

    <div class="flex justify-between items-center mb-6 mt-2">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Cost Estimation</h1>
        <a href="{{ route('projects.show', $project->id) }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 px-4 py-2 text-sm font-medium border border-slate-200 transition-colors shadow-sm no-underline">
            <i class="fa-solid fa-arrow-left"></i> Back to Project
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-8 flex items-start gap-3 text-sm">
            <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- ── ENGINEERING SUMMARY ────────────────────────────────── --}}
    <div class="bg-white border border-slate-200 p-6 mb-8 shadow-sm">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-6">
            <div class="text-center">
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Distance</div>
                <div class="text-lg font-bold text-sky-600">{{ number_format($duration['distance_nm'], 2) }} NM</div>
            </div>
            <div class="text-center border-l border-slate-100">
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Speed</div>
                <div class="text-lg font-bold text-blue-500">{{ $duration['speed_knots'] }} kn</div>
            </div>
            <div class="text-center border-l border-slate-100">
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Survey Hours</div>
                <div class="text-lg font-bold text-amber-500">{{ number_format($duration['survey_hours'], 1) }} hrs</div>
            </div>
            <div class="text-center border-l border-slate-100">
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Execution</div>
                <div class="text-lg font-bold text-emerald-500">{{ number_format($duration['execution_days'], 1) }} days</div>
            </div>
            <div class="text-center border-l border-slate-100">
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Weather</div>
                <div class="text-lg font-bold text-slate-400">{{ $duration['weather_days'] }} days</div>
            </div>
            <div class="text-center border-l border-slate-100">
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">MOB/DEMOB</div>
                <div class="text-lg font-bold text-slate-400">{{ $duration['mod_demod_days'] }} days</div>
            </div>
            <div class="text-center border-l border-slate-100">
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Patch Test</div>
                <div class="text-lg font-bold text-slate-400">{{ $duration['patch_test_days'] }} days</div>
            </div>
            <div class="text-center border-l border-slate-100">
                <div class="text-[10px] uppercase font-bold text-slate-500 tracking-wider mb-1">Total Duration</div>
                <div class="text-lg font-bold text-rose-500">{{ number_format($duration['total_days'], 1) }} days</div>
            </div>
        </div>
        
        <div class="mt-5 text-center text-xs font-medium text-slate-500 flex justify-center items-center gap-1.5">
            <i class="fa-solid fa-circle-info text-slate-400"></i> Global Allowances are configured in the <a href="{{ route('projects.show', $project->id) }}" class="text-teal-600 hover:text-teal-700 underline underline-offset-2">Project Overview</a> and automatically applied.
        </div>
    </div>

    <form action="{{ route('projects.cost.store', $project->id) }}" method="POST" id="cost-form">
        @csrf
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- ── LINE ITEMS (GROUPED BY CATEGORY) ──────────────── --}}
            <div class="flex-grow">
                @php
                    $flatIndex = 0;
                    $categoryColors = [
                        'Equipment' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'border' => 'border-sky-200', 'icon' => 'fa-wrench'],
                        'Personnel' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'fa-users'],
                        'Logistics' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'icon' => 'fa-truck'],
                        'Analysis'  => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'fa-chart-line'],
                        'Miscellaneous' => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-300', 'icon' => 'fa-box'],
                        'General'   => ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'border' => 'border-slate-300', 'icon' => 'fa-box'],
                    ];
                @endphp

                @php
                    if($groupedItems->isEmpty()) {
                        $groupedItems = collect(['Miscellaneous' => collect()]);
                    }
                @endphp

                @foreach($groupedItems as $category => $items)
                    @php $c = $categoryColors[$category] ?? $categoryColors['General']; @endphp
                    <div class="cost-section bg-white border border-slate-200 mb-8 shadow-sm">
                        <div class="px-5 py-3 border-b border-b-2 {{ $c['border'] }} {{ $c['bg'] }} flex justify-between items-center">
                            <h6 class="text-xs font-bold uppercase tracking-wider {{ $c['text'] }} m-0">
                                <i class="fa-solid {{ $c['icon'] }} mr-2 opacity-75"></i>{{ $category }}
                            </h6>
                            <span class="category-subtotal-value text-sm font-bold text-slate-800" data-category="{{ $category }}">RM 0.00</span>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[900px] text-left border-collapse text-sm cost-table">
                                <thead>
                                    <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-500">
                                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] w-[35%]">Description</th>
                                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] w-[12%] text-right">Days</th>
                                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] w-[12%] text-right">Qty/Pax</th>
                                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] w-[16%] text-right">Unit Rate (RM)</th>
                                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] w-[15%] text-right">Total (RM)</th>
                                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] w-[10%] text-center"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($items as $item)
                                        <tr class="cost-row hover:bg-slate-50 transition-colors group" data-category="{{ $category }}">
                                            <input type="hidden" name="items[{{ $flatIndex }}][cost_rate_id]" value="{{ $item->cost_rate_id ?? '' }}">
                                            <input type="hidden" name="items[{{ $flatIndex }}][category]" value="{{ $category }}">
                                            <td class="px-3 py-2">
                                                <input type="text" name="items[{{ $flatIndex }}][description]" class="w-full px-2 py-1.5 bg-transparent border border-transparent hover:border-slate-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:bg-white text-sm text-slate-800 transition-all font-medium" value="{{ $item->description }}" required>
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" step="0.01" name="items[{{ $flatIndex }}][days]" class="w-full px-2 py-1.5 bg-transparent border border-transparent hover:border-slate-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:bg-white text-sm text-slate-800 transition-all text-right item-days font-medium @if(($item->unit_type ?? $item->costRate?->unit_type ?? '') === 'Lump Sum') !bg-slate-100 !text-slate-400 !border-slate-200 @endif" value="{{ $item->days }}" required @if(($item->unit_type ?? $item->costRate?->unit_type ?? '') === 'Lump Sum') readonly tabindex="-1" title="Not applicable for Lump Sum" @endif>
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" step="1" name="items[{{ $flatIndex }}][units]" class="w-full px-2 py-1.5 bg-transparent border border-transparent hover:border-slate-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:bg-white text-sm text-slate-800 transition-all text-right item-units font-medium @if(($item->unit_type ?? $item->costRate?->unit_type ?? '') === 'Lump Sum') !bg-slate-100 !text-slate-400 !border-slate-200 @endif" value="{{ $item->units ?? 1 }}" required @if(($item->unit_type ?? $item->costRate?->unit_type ?? '') === 'Lump Sum') readonly tabindex="-1" title="Not applicable for Lump Sum" @endif>
                                            </td>
                                            <td class="px-3 py-2">
                                                <input type="number" step="0.01" name="items[{{ $flatIndex }}][unit_rate]" class="w-full px-2 py-1.5 bg-transparent border border-transparent hover:border-slate-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:bg-white text-sm text-slate-800 transition-all text-right item-rate font-medium" value="{{ $item->unit_rate }}" required>
                                            </td>
                                            <td class="px-4 py-2 text-right item-total font-bold text-slate-900 align-middle">
                                                {{ number_format($item->total_price, 2) }}
                                            </td>
                                            <td class="px-3 py-2 text-center align-middle">
                                                <button type="button" class="btn-remove-row text-slate-300 hover:text-red-500 transition-colors px-2 py-1 focus:outline-none">
                                                    <i class="fa-solid fa-trash-can text-sm font-light"></i>
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
                <div class="text-center mt-6">
                    <button type="button" id="btn-add-row" class="inline-flex items-center gap-2 bg-white text-teal-600 border border-teal-600 hover:bg-teal-50 px-5 py-2.5 text-sm font-bold transition-colors">
                        <i class="fa-solid fa-plus font-light"></i> Add Custom Line Item
                    </button>
                </div>
            </div>

            {{-- ── SUMMARY PANEL ─────────────────────────────────── --}}
            <div class="w-full lg:w-[320px] flex-shrink-0">
                <div class="bg-white border border-slate-200 shadow-sm sticky top-6">
                    <div class="bg-slate-900 px-5 py-4 border-b border-slate-800">
                        <h5 class="text-white font-bold tracking-tight m-0 flex items-center gap-2"><i class="fa-solid fa-calculator text-slate-400"></i> Summary</h5>
                    </div>
                    
                    <div class="p-5">
                        <div class="mb-5">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Total Duration</label>
                            <h4 class="text-xl font-bold text-slate-900 m-0">{{ number_format($duration['total_days'], 1) }} <span class="text-sm font-medium text-slate-400 ml-1">days</span></h4>
                        </div>

                        <div class="h-px bg-slate-200 my-4 w-full"></div>

                        <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-3">By Category</label>
                        <div id="category-breakdown" class="flex flex-col gap-2.5">
                            {{-- Populated by JS --}}
                        </div>

                        <div class="h-px bg-slate-200 my-4 w-full"></div>

                        <div class="flex justify-between items-center mb-5">
                            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Grand Total</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-slate-400 text-sm font-medium">RM</span>
                                <span class="text-2xl font-black text-slate-900 tracking-tight" id="grand-total">0.00</span>
                            </div>
                        </div>

                        @if($estimation && $estimation->status === 'Manual')
                            <div class="bg-amber-50 text-amber-800 border border-amber-200 px-3 py-2 mb-4 text-xs font-medium flex items-start gap-2">
                                <i class="fa-solid fa-pen mt-0.5"></i> Manually edited. Rates may differ from master.
                            </div>
                        @endif

                        <button type="submit" class="w-full flex justify-center items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-4 py-3 text-sm font-bold transition-colors">
                            <i class="fa-solid fa-floppy-disk font-light"></i> Save Estimation
                        </button>
                    </div>

                    <div class="bg-slate-50 border-t border-slate-200 p-4 flex flex-col gap-2">
                        <button type="button" id="btn-recalculate" class="w-full flex justify-center items-center gap-2 bg-white border border-amber-500 text-amber-600 hover:bg-amber-50 px-4 py-2 text-sm font-bold transition-colors">
                            <i class="fa-solid fa-arrows-rotate font-light"></i> Recalculate
                        </button>
                        <a href="{{ route('projects.report.pdf', $project->id) }}" target="_blank" class="w-full flex justify-center items-center gap-2 bg-white border border-sky-500 text-sky-600 hover:bg-sky-50 px-4 py-2 text-sm font-bold transition-colors no-underline">
                            <i class="fa-solid fa-file-pdf font-light"></i> Download Report
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </form>

    {{-- Hidden template for new rows --}}
    <template id="row-template">
        <tr class="cost-row hover:bg-slate-50 transition-colors group" data-category="Miscellaneous">
            <input type="hidden" name="items[__INDEX__][cost_rate_id]" value="">
            <input type="hidden" name="items[__INDEX__][category]" value="Miscellaneous">
            <td class="px-3 py-2">
                <input type="text" name="items[__INDEX__][description]" class="w-full px-2 py-1.5 bg-transparent border border-transparent hover:border-slate-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:bg-white text-sm text-slate-800 transition-all font-medium" placeholder="Custom item..." required>
            </td>
            <td class="px-3 py-2">
                <input type="number" step="0.01" name="items[__INDEX__][days]" class="w-full px-2 py-1.5 bg-transparent border border-transparent hover:border-slate-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:bg-white text-sm text-slate-800 transition-all text-right item-days font-medium" value="1" required>
            </td>
            <td class="px-3 py-2">
                <input type="number" step="1" name="items[__INDEX__][units]" class="w-full px-2 py-1.5 bg-transparent border border-transparent hover:border-slate-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:bg-white text-sm text-slate-800 transition-all text-right item-units font-medium" value="1" required>
            </td>
            <td class="px-3 py-2">
                <input type="number" step="0.01" name="items[__INDEX__][unit_rate]" class="w-full px-2 py-1.5 bg-transparent border border-transparent hover:border-slate-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 focus:bg-white text-sm text-slate-800 transition-all text-right item-rate font-medium" value="0.00" required>
            </td>
            <td class="px-4 py-2 text-right item-total font-bold text-slate-900 align-middle">
                0.00
            </td>
            <td class="px-3 py-2 text-center align-middle">
                <button type="button" class="btn-remove-row text-slate-300 hover:text-red-500 transition-colors px-2 py-1 focus:outline-none">
                    <i class="fa-solid fa-trash-can text-sm font-light"></i>
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
                    breakdownEl.innerHTML += `<div class="flex justify-between items-center text-sm"><span class="text-slate-600 font-medium">${cat}</span><span class="font-bold text-slate-900">RM ${formatCurrency(total)}</span></div>`;
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
                    cancelButtonColor: '#f8fafc',
                    customClass: {
                        cancelButton: 'text-slate-800 border-none shadow-sm',
                        confirmButton: 'text-white'
                    },
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
