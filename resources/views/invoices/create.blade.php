<x-app-layout containerClass="w-full px-8 py-8">
    <x-slot name="header">{{ isset($invoice) ? 'Edit Invoice' : 'Create Invoice' }}</x-slot>

    <div class="flex justify-between items-center mb-6 mt-2">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
            {{ isset($invoice) ? 'Edit Invoice' : 'New Invoice' }}
        </h1>
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 px-4 py-2 text-sm font-medium border border-slate-200 transition-colors shadow-sm no-underline">
            <i class="fa-solid fa-arrow-left"></i> Back to Invoices
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-6 flex items-start gap-3 text-sm">
            <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 text-red-800 border border-red-200 p-4 mb-6 text-sm">
            <div class="font-medium mb-1"><i class="fa-solid fa-circle-xmark mr-1"></i> Please fix the following errors:</div>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div x-data="invoiceForm()">
        {{-- Show quote total if a project is selected and has a cost estimation --}}
        <template x-if="projectId && quoteTotal > 0">
            <div class="bg-sky-50 text-sky-800 border border-sky-200 p-4 mb-6 flex items-center gap-3 text-sm">
                <i class="fa-solid fa-circle-info text-sky-500"></i>
                <span>Quotation Total: <strong x-text="'RM ' + formatMoney(quoteTotal)"></strong> — Enter the milestone amount below.</span>
            </div>
        </template>

    <form
        action="{{ isset($invoice) ? route('invoices.update', $invoice->id) : route('invoices.store') }}"
        method="POST"
    >
        @csrf
        @if(isset($invoice))
            @method('PUT')
        @endif

        @if(isset($prefill['cost_estimation_id']))
            <input type="hidden" name="cost_estimation_id" value="{{ $prefill['cost_estimation_id'] ?? '' }}">
        @endif

        <div class="flex flex-col xl:flex-row gap-8">

            {{-- LEFT: Main Form --}}
            <div class="flex-grow space-y-6">

                {{-- SECTION A: Invoice Header --}}
                <div class="bg-white border border-slate-200 p-6 shadow-sm">
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-4">Invoice Details</div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Invoice Number <span class="text-red-400">*</span></label>
                            <input type="text" name="invoice_number" value="{{ old('invoice_number', $invoice->invoice_number ?? $invoiceNumber ?? '') }}" required
                                class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                        </div>
                        @if(isset($invoice))
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Status</label>
                            <select name="status" class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400">
                                @foreach(['Draft', 'Issued', 'Paid', 'Overdue', 'Cancelled'] as $s)
                                    <option value="{{ $s }}" {{ ($invoice->status ?? 'Draft') === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Issue Date <span class="text-red-400">*</span></label>
                            <input type="date" name="issue_date" value="{{ old('issue_date', isset($invoice) ? $invoice->issue_date->format('Y-m-d') : date('Y-m-d')) }}" required
                                class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Due Date</label>
                            <input type="date" name="due_date" value="{{ old('due_date', isset($invoice) && $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '') }}"
                                class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Payment Terms</label>
                            <input type="text" name="payment_terms" value="{{ old('payment_terms', $invoice->payment_terms ?? '') }}" placeholder="e.g. Net 30 Days"
                                class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Currency</label>
                            <select name="currency" class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400">
                                <option value="MYR" {{ old('currency', $invoice->currency ?? 'MYR') === 'MYR' ? 'selected' : '' }}>MYR (Ringgit Malaysia)</option>
                                <option value="USD" {{ old('currency', $invoice->currency ?? '') === 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                                <option value="SGD" {{ old('currency', $invoice->currency ?? '') === 'SGD' ? 'selected' : '' }}>SGD (Singapore Dollar)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- SECTION B: References --}}
                <div class="bg-white border border-slate-200 p-6 shadow-sm">
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-4">References</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Client <span class="text-red-400">*</span></label>
                            <select name="client_id" required class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400">
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $invoice->client_id ?? $prefill['client_id'] ?? '') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }} {{ $client->company ? '(' . $client->company . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Project <span class="text-slate-400">(optional)</span></label>
                            <select name="project_id" x-model="projectId" class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400">
                                <option value="">No Project (Standalone)</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ old('project_id', $invoice->project_id ?? $prefill['project_id'] ?? '') == $proj->id ? 'selected' : '' }}>
                                        {{ $proj->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Client Ref / PO Number</label>
                            <input type="text" name="client_ref" value="{{ old('client_ref', $invoice->client_ref ?? '') }}" placeholder="e.g. PO-013/2026"
                                class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Our Ref / Quotation No.</label>
                            <input type="text" name="our_ref" value="{{ old('our_ref', $invoice->our_ref ?? $prefill['our_ref'] ?? '') }}" placeholder="e.g. EHS/FIN/SW-QUO/2026/001"
                                class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                        </div>
                    </div>
                </div>

                {{-- SECTION D: Line Items --}}
                <div class="bg-white border border-slate-200 p-6 shadow-sm mb-6">
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-4">Line Items</div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="text-left pb-2 text-[10px] uppercase font-bold text-slate-500 tracking-wider w-8">#</th>
                                    <th class="text-left pb-2 text-[10px] uppercase font-bold text-slate-500 tracking-wider">Title</th>
                                    <th class="text-left pb-2 text-[10px] uppercase font-bold text-slate-500 tracking-wider">Description</th>
                                    <th class="text-right pb-2 text-[10px] uppercase font-bold text-slate-500 tracking-wider w-24">Qty</th>
                                    <th class="text-right pb-2 text-[10px] uppercase font-bold text-slate-500 tracking-wider w-36">Unit Price (RM)</th>
                                    <th class="text-right pb-2 text-[10px] uppercase font-bold text-slate-500 tracking-wider w-36">Total (RM)</th>
                                    <th class="w-10"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(item, index) in items" :key="index">
                                    <tr class="border-b border-slate-100">
                                        <td class="py-3 text-slate-400 font-medium" x-text="index + 1"></td>
                                        <td class="py-3 pr-2">
                                            <input type="text" :name="`items[${index}][title]`" x-model="item.title" required placeholder="e.g. First Payment (40%)"
                                                class="w-full border border-slate-200 bg-slate-50 px-2 py-2 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                                        </td>
                                        <td class="py-3 pr-2">
                                            <input type="text" :name="`items[${index}][description]`" x-model="item.description" placeholder="Optional description..."
                                                class="w-full border border-slate-200 bg-slate-50 px-2 py-2 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                                        </td>
                                        <td class="py-3 pr-2">
                                            <input type="number" :name="`items[${index}][quantity]`" x-model.number="item.quantity" step="0.01" min="0.01" required
                                                @input="calculateTotals()"
                                                class="w-full border border-slate-200 bg-slate-50 px-2 py-2 text-sm text-right focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                                        </td>
                                        <td class="py-3 pr-2">
                                            <input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" step="0.01" min="0" required
                                                @input="calculateTotals()"
                                                class="w-full border border-slate-200 bg-slate-50 px-2 py-2 text-sm text-right focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                                        </td>
                                        <td class="py-3 text-right font-semibold text-slate-700" x-text="formatMoney(item.quantity * item.unit_price)"></td>
                                        <td class="py-3 text-center">
                                            <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                                class="text-slate-300 hover:text-red-500 transition-colors">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" @click="addItem()"
                        class="mt-4 inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-800 font-medium transition-colors">
                        <i class="fa-solid fa-plus text-xs"></i> Add Item
                    </button>
                </div>

                {{-- SECTION E: Payment Details --}}
                <div class="bg-white border border-slate-200 p-6 shadow-sm">
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-4">Payment Details</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Bank Name</label>
                            <input type="text" name="payment_bank_name" value="{{ old('payment_bank_name', $invoice->payment_bank_name ?? $defaults['payment_bank_name'] ?? '') }}"
                                class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Account Name</label>
                            <input type="text" name="payment_account_name" value="{{ old('payment_account_name', $invoice->payment_account_name ?? $defaults['payment_account_name'] ?? '') }}"
                                class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Account Number</label>
                            <input type="text" name="payment_account_number" value="{{ old('payment_account_number', $invoice->payment_account_number ?? $defaults['payment_account_number'] ?? '') }}"
                                class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                    </div>
                </div>

                {{-- SECTION F: Notes --}}
                <div class="bg-white border border-slate-200 p-6 shadow-sm">
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-4">Notes</div>
                    <textarea name="notes" rows="3" placeholder="e.g. This invoice represents the First Payment of 40%..."
                        class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">{{ old('notes', $invoice->notes ?? '') }}</textarea>
                </div>

                {{-- SECTION G: Signatures --}}
                <div class="bg-white border border-slate-200 p-6 shadow-sm">
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-4">Signatures</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-xs font-semibold text-slate-500 mb-3 uppercase tracking-wide">Prepared By</div>
                            <div class="space-y-3">
                                <input type="text" name="prepared_by_name" value="{{ old('prepared_by_name', $invoice->prepared_by_name ?? auth()->user()->name) }}" placeholder="Name"
                                    class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                                <input type="text" name="prepared_by_title" value="{{ old('prepared_by_title', $invoice->prepared_by_title ?? $defaults['default_prepared_by_title'] ?? '') }}" placeholder="Title / Role"
                                    class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                            </div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-500 mb-3 uppercase tracking-wide">Approved By</div>
                            <div class="space-y-3">
                                <input type="text" name="approved_by_name" value="{{ old('approved_by_name', $invoice->approved_by_name ?? $defaults['default_approved_by_name'] ?? '') }}" placeholder="Name"
                                    class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                                <input type="text" name="approved_by_title" value="{{ old('approved_by_title', $invoice->approved_by_title ?? $defaults['default_approved_by_title'] ?? '') }}" placeholder="Title / Role"
                                    class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Totals Sidebar --}}
            <div class="xl:w-80 flex-shrink-0">
                <div class="bg-white border border-slate-200 p-6 shadow-sm sticky top-24">
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-5">Summary</div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="font-medium text-slate-700" x-text="'RM ' + formatMoney(subtotal)"></span>
                        </div>

                        <div class="flex items-center gap-3 text-sm">
                            <span class="text-slate-500 flex-shrink-0">Discount</span>
                            <input type="number" name="discount_amount" x-model.number="discountAmount" step="0.01" min="0" @input="calculateTotals()" placeholder="0.00"
                                class="flex-grow border border-slate-200 bg-slate-50 px-2 py-1.5 text-sm text-right focus:outline-none focus:border-slate-400 focus:bg-white transition-colors w-24">
                        </div>

                        <div class="flex items-center gap-3 text-sm">
                            <span class="text-slate-500 flex-shrink-0">Tax (%)</span>
                            <input type="number" name="tax_rate" x-model.number="taxRate" step="0.01" min="0" max="100" @input="calculateTotals()" placeholder="0"
                                class="flex-grow border border-slate-200 bg-slate-50 px-2 py-1.5 text-sm text-right focus:outline-none focus:border-slate-400 focus:bg-white transition-colors w-24">
                        </div>

                        <div class="flex justify-between items-center text-sm" x-show="taxAmount > 0">
                            <span class="text-slate-500">Tax Amount</span>
                            <span class="text-slate-600" x-text="'RM ' + formatMoney(taxAmount)"></span>
                        </div>

                        <div class="border-t border-slate-200 pt-4 flex justify-between items-center">
                            <span class="font-bold text-slate-900">Grand Total</span>
                            <span class="text-xl font-bold text-slate-900" x-text="'RM ' + formatMoney(grandTotal)"></span>
                        </div>

                        <template x-if="projectId && quoteTotal > 0">
                            <div class="bg-blue-50/50 border border-blue-100 p-3 mt-4 rounded text-sm text-blue-800 flex items-start gap-2">
                                <i class="fa-solid fa-chart-pie mt-0.5 text-blue-500"></i>
                                <div>
                                    <div class="font-medium">Project Progress</div>
                                    <div class="text-blue-600 text-xs mt-0.5">
                                        This invoice represents <span class="font-bold" x-text="((grandTotal / quoteTotal) * 100).toFixed(2) + '%'"></span> of the approved project estimate (RM <span x-text="formatMoney(quoteTotal)"></span>).
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button type="submit"
                        class="w-full mt-6 bg-slate-900 hover:bg-slate-800 text-white py-3 text-sm font-medium transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ isset($invoice) ? 'Update Invoice' : 'Save Invoice' }}
                    </button>

                    @if(isset($invoice))
                        <a href="{{ route('invoices.preview', $invoice->id) }}"
                            class="w-full mt-3 bg-white hover:bg-slate-50 text-slate-700 py-3 text-sm font-medium border border-slate-200 transition-colors flex items-center justify-center gap-2 no-underline">
                            <i class="fa-solid fa-file-pdf"></i> Preview PDF
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </form>
    </div>

    <script>
        function invoiceForm() {
            @php
                $existingItems = isset($invoice) && $invoice->items->count() > 0 
                    ? $invoice->items->map(fn($i) => [
                        'title' => $i->title,
                        'description' => $i->description ?? '',
                        'quantity' => $i->quantity,
                        'unit_price' => $i->unit_price,
                    ]) 
                    : [['title' => '', 'description' => '', 'quantity' => 1, 'unit_price' => 0]];
            @endphp
            
            // Initialize items from existing invoice or start with one empty row
            let existingItems = @json($existingItems);
            
            @php
                $projectCosts = $projects->mapWithKeys(function ($p) {
                    return [$p->id => [
                        'quote_total' => $p->costEstimation?->total_cost ?? 0,
                        'our_ref' => $p->costEstimation?->quotation_number ?? '',
                    ]];
                });
            @endphp
            let projectCosts = @json($projectCosts);


            return {
                items: existingItems,
                subtotal: 0,
                discountAmount: {{ (float) old('discount_amount', $invoice->discount_amount ?? 0) }},
                taxRate: {{ (float) old('tax_rate', $invoice->tax_rate ?? 0) }},
                taxAmount: 0,
                grandTotal: 0,
                
                // Project and Generator variables
                projectId: '{{ (string) old('project_id', $invoice->project_id ?? $prefill['project_id'] ?? '') }}',
                quoteTotal: 0,
                ourRef: '',
                invoicePercentage: 100,
                customPercentage: null,
                projectCosts: projectCosts,

                init() {
                    console.log("AlpineJS InvoiceForm Initialized! Project ID:", this.projectId);
                    this.updateProjectDetails();
                    this.calculateTotals();
                    
                    // Watch for project changes
                    this.$watch('projectId', value => {
                        this.updateProjectDetails();
                    });
                },

                updateProjectDetails() {
                    if (this.projectId && this.projectCosts[this.projectId]) {
                        this.quoteTotal = this.projectCosts[this.projectId].quote_total;
                        this.ourRef = this.projectCosts[this.projectId].our_ref;
                    } else {
                        this.quoteTotal = 0;
                        this.ourRef = '';
                    }
                },

                addItem() {
                    this.items.push({ title: '', description: '', quantity: 1, unit_price: 0 });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                    this.calculateTotals();
                },
                
                calculateTotals() {
                    this.subtotal = this.items.reduce((sum, item) => {
                        return sum + ((item.quantity || 0) * (item.unit_price || 0));
                    }, 0);

                    this.taxAmount = this.taxRate > 0 ? this.subtotal * (this.taxRate / 100) : 0;
                    this.grandTotal = this.subtotal - (this.discountAmount || 0) + this.taxAmount;
                },

                formatMoney(value) {
                    return (Number(value) || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                }
            };
        }
    </script>
</x-app-layout>
