<x-app-layout containerClass="w-full px-6 md:px-12 py-6">
    <div class="font-sans text-slate-800 antialiased" x-data="quotationBuilder()" x-cloak>

        <!-- ============ PREVIEW SHEET (report style, nothing is saved) ============ -->
        <div x-show="showPreview" x-cloak
             @keydown.escape.window="showPreview = false"
             @click.self="showPreview = false"
             style="position:fixed; top:0; right:0; bottom:0; left:0; z-index:9999; background:rgba(15,23,42,0.55); overflow-y:auto; padding:24px;">

            <style>
                .mp-sheet { max-width: 900px; margin: 0 auto; background: #fff; border-radius: 8px; padding: 40px 48px; box-shadow: 0 20px 40px rgba(0,0,0,0.25); font-family: Arial, Helvetica, sans-serif; color: #1f2937; }
                .mp-title { font-size: 1.9rem; font-weight: 800; color: #17496b; margin: 0; }
                .mp-generated { font-size: 0.8rem; color: #6b7280; margin: 4px 0 16px; }
                .mp-draft { display: inline-block; font-size: 0.7rem; font-weight: 700; color: #b45309; background: #fef3c7; border: 1px solid #fcd34d; border-radius: 999px; padding: 2px 10px; margin-left: 8px; }
                .mp-h2 { font-size: 1.15rem; font-weight: 700; color: #17496b; border-bottom: 1px solid #d5dde6; padding-bottom: 6px; margin: 28px 0 14px; }
                .mp-h3 { font-size: 1rem; font-weight: 700; color: #111827; margin: 18px 0 8px; }
                .mp-table { width: 100%; border-collapse: collapse; font-size: 0.85rem; }
                .mp-table td, .mp-table th { border: 1px solid #cfd8e3; padding: 8px 10px; text-align: left; }
                .mp-table thead th { background: #eef2f7; font-weight: 700; }
                .mp-table .lbl { background: #eef2f7; font-weight: 700; width: 18%; }
                .mp-table .mp-num { text-align: right; white-space: nowrap; }
                .mp-table .mp-total-row td { background: #f8fafc; font-weight: 700; }
                .mp-boxes { display: grid; grid-template-columns: repeat(4, 1fr); gap: 6px; margin-bottom: 8px; }
                .mp-box { border: 1px solid #cfd8e3; text-align: center; padding: 12px 8px; }
                .mp-box b { display: block; font-size: 1.35rem; color: #17496b; }
                .mp-box span { font-size: 0.8rem; color: #4b5563; }
                .mp-btn { padding: 8px 18px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; font-weight: 600; font-size: 0.85rem; color: #1f2937; cursor: pointer; }
                .mp-btn:hover { background: #f1f5f9; }
            </style>

            <div class="mp-sheet">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
                    <div>
                        <h1 class="mp-title">MODELLING PREVIEW</h1>
                        <div class="mp-generated">
                            Generated <span x-text="generatedAt"></span>
                            <span class="mp-draft">DRAFT - NOT SAVED YET</span>
                        </div>
                    </div>
                    <button type="button" class="mp-btn" @click="showPreview = false">Close</button>
                </div>

                <!-- Project info -->
                <table class="mp-table">
                    <tr>
                        <td class="lbl">Project</td>
                        <td x-text="projectInfo.name || '-'"></td>
                        <td class="lbl">Project No.</td>
                        <td x-text="projectInfo.number || '-'"></td>
                    </tr>
                    <tr>
                        <td class="lbl">Client</td>
                        <td x-text="projectInfo.client || '-'"></td>
                        <td class="lbl">Period</td>
                        <td x-text="projectInfo.period || '-'"></td>
                    </tr>
                </table>

                <h2 class="mp-h2">Modelling Summary</h2>
                <table class="mp-table">
                    <tr>
                        <td class="lbl">Package</td>
                        <td colspan="3" x-text="presetLabel"></td>
                    </tr>
                    <tr>
                        <td class="lbl">Subtotal (internal)</td>
                        <td x-text="money(totals.internal)"></td>
                        <td class="lbl">Subtotal (client)</td>
                        <td x-text="money(totals.client)"></td>
                    </tr>
                    <tr>
                        <td class="lbl" x-text="'Contingency (' + terms.contingency + '%)'"></td>
                        <td x-text="money(totals.contingency)"></td>
                        <td class="lbl" x-text="'SST / Tax (' + terms.tax + '%)'"></td>
                        <td x-text="money(totals.tax)"></td>
                    </tr>
                    <tr>
                        <td class="lbl">Grand Total</td>
                        <td colspan="3" style="font-weight:700;" x-text="money(totals.grandTotal)"></td>
                    </tr>
                </table>

                <!-- Modules and items -->
                <h2 class="mp-h2" x-text="'Modules & Items - ' + presetLabel"></h2>

                <template x-for="mod in activeModules" :key="mod.id">
                    <div>
                        <div class="mp-h3">
                            <span x-text="mod.name"></span>
                            <span style="font-weight:400; color:#6b7280; font-size:0.8rem;" x-text="' - ' + mod.group"></span>
                        </div>
                        <table class="mp-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th style="width:22%;">Work Package</th>
                                    <th class="mp-num" style="width:20%;">Cost to Client</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(sub, i) in mod.items" :key="i">
                                    <tr>
                                        <td x-text="sub.name"></td>
                                        <td x-text="sub.work_package"></td>
                                        <td class="mp-num" x-text="money(sub.client)"></td>
                                    </tr>
                                </template>
                                <tr x-show="mod.items.length === 0">
                                    <td colspan="3" style="color:#9ca3af;">No items in this module yet.</td>
                                </tr>
                                <tr class="mp-total-row">
                                    <td colspan="2">Module total</td>
                                    <td class="mp-num" x-text="money(mod.client_total)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <p x-show="activeModules.length === 0" style="color:#9ca3af; font-size:0.85rem;">No modules selected.</p>

                <p style="margin-top:28px; font-size:0.75rem; color:#6b7280;">
                    Draft preview for review only. Nothing has been saved to the database yet.
                </p>

            </div>
        </div>
        <!-- ============ END PREVIEW SHEET ============ -->

        @if($project)
            <a href="{{ route('projects.show', $project->project_Id) }}" class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-800 transition-colors mb-3">
                <i class="fa-solid fa-arrow-left text-xs"></i> Back to Project
            </a>
        @endif

        <!-- Header Section -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 mb-4 border-b border-slate-200/60">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Modeling Cost Estimator</h1>
                <p class="text-slate-500 mt-1 text-sm md:text-base">Select modules, adjust costs, and generate a quotation</p>
            </div>
            <div>
                <x-modeling-nav />
            </div>
        </header>

        <!-- Success Alert Banner -->
        <div x-show="savedNotification" x-transition class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-6 rounded-lg flex items-center justify-between text-sm shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span class="font-medium">Quotation saved successfully!</span>
            </div>
            <button @click="savedNotification = false" class="text-emerald-600 hover:text-emerald-800 font-bold ml-4">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Project Meta Row (read-only, comes from the selected project) -->
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-200/80 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Project Name</label>
                    <input type="text" value="{{ $project->name ?? '' }}" readonly placeholder="No project selected"
                        class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 cursor-not-allowed focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Client Name</label>
                    <input type="text" value="{{ $project->client->company_name ?? '' }}" readonly placeholder="No project selected"
                        class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 cursor-not-allowed focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Project Code</label>
                    <input type="text" value="{{ $project->number ?? '' }}" readonly placeholder="No project selected"
                        class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 cursor-not-allowed focus:outline-none">
                </div>
            </div>
        </div>

        <!-- Package Presets Pills (from Packages page) -->
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <template x-for="preset in presets" :key="preset.id">
                <button type="button"
                    @click="applyPreset(preset.id)"
                    :class="activePreset === preset.id
                        ? 'bg-blue-600 text-white shadow-sm hover:bg-blue-700'
                        : 'bg-white text-slate-700 border border-slate-200/80 hover:bg-slate-100 hover:text-slate-900'"
                    class="px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-150">
                    <span x-text="preset.name"></span>
                </button>
            </template>
            <p x-show="presets.length === 0" class="text-sm text-slate-400">
                No packages available yet. Create a package to quickly load a predefined modelling setup.
            </p>
        </div>

        <!-- 3-Column Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Column 1: Module Library + Commercial Terms -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Module Library (from Modules page) -->
                <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-200/80">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Module Library</h3>

                    <div class="space-y-6">
                        <template x-for="group in groups" :key="group.name">
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <div class="text-xs font-bold uppercase tracking-wider text-slate-400" x-text="group.name"></div>
                                    <span x-show="group.required" class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">Required</span>
                                </div>
                                <div class="space-y-1">
                                    <template x-for="mod in group.modules" :key="mod.id">
                                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer select-none text-sm font-medium text-slate-800 transition-colors">
                                            <input type="checkbox"
                                                :checked="isSelected(mod.id)"
                                                :disabled="mod.required"
                                                @change="toggle(mod.id)"
                                                class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 transition">
                                            <span x-text="mod.name"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div x-show="catalog.length === 0" class="py-8 text-center text-slate-400 text-sm">
                            No modules yet. Add some on the Modules page first.
                        </div>
                    </div>
                </div>

                <!-- Commercial Terms -->
                <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-200/80">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Commercial Terms</h3>

                    <div class="space-y-6">
                        <!-- Contingency -->
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-sm font-medium text-slate-700">Contingency</span>
                                <span class="text-sm font-bold" style="color:#d97706;" x-text="terms.contingency + '%'"></span>
                            </div>
                            <input type="range" min="0" max="30" step="1" x-model.number="terms.contingency"
                                style="width:100%; accent-color:#0f766e;">
                        </div>

                        <!-- SST / Tax -->
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-sm font-medium text-slate-700">SST / Tax</span>
                                <span class="text-sm font-bold" style="color:#d97706;" x-text="terms.tax + '%'"></span>
                            </div>
                            <input type="range" min="0" max="20" step="1" x-model.number="terms.tax"
                                style="width:100%; accent-color:#0f766e;">
                            <p class="text-xs text-slate-400 mt-1.5">Optional. Leave at 0% for no tax.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Column 2: Cost Ladder (from Items page) -->
            <div class="lg:col-span-5 bg-white rounded-xl p-5 shadow-sm border border-slate-200/80">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Cost Ladder</h3>

                <div class="space-y-6 divide-y divide-slate-100">
                    <template x-for="mod in activeModules" :key="mod.id">
                        <div class="pt-4 first:pt-0">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-bold text-slate-900 text-sm md:text-base" x-text="mod.name"></h4>
                                <span class="font-bold text-slate-900 text-sm md:text-base" x-text="money(mod.client_total)"></span>
                            </div>
                            <ul class="space-y-2 pl-1">
                                <template x-for="(sub, i) in mod.items" :key="i">
                                    <li class="flex items-start justify-between text-xs md:text-sm text-slate-500 gap-4">
                                        <span class="leading-snug" x-text="sub.name"></span>
                                        <span class="font-semibold text-slate-600 whitespace-nowrap" x-text="money(sub.client)"></span>
                                    </li>
                                </template>
                            </ul>
                            <p x-show="mod.items.length === 0" class="text-xs text-slate-400 pl-1">No items in this module yet.</p>
                        </div>
                    </template>

                    <div x-show="activeModules.length === 0" class="py-8 text-center text-slate-400 text-sm">
                        No active modules selected. Select modules from the library to view cost breakdown.
                    </div>
                </div>
            </div>

            <!-- Column 3: Summary -->
            <div class="lg:col-span-3 bg-white rounded-xl p-5 shadow-sm border border-slate-200/80 sticky top-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-5">Summary</h3>

                <div class="space-y-3 mb-6 text-sm">
                    <div class="flex justify-between items-baseline gap-3 text-slate-500">
                        <span class="leading-snug">Subtotal (internal)</span>
                        <span class="font-medium text-slate-700 whitespace-nowrap" x-text="money(totals.internal)"></span>
                    </div>
                    <div class="flex justify-between items-baseline gap-3 text-slate-500">
                        <span class="leading-snug">Subtotal (client)</span>
                        <span class="font-medium text-slate-700 whitespace-nowrap" x-text="money(totals.client)"></span>
                    </div>
                    <div class="flex justify-between items-baseline gap-3 text-slate-500">
                        <span class="leading-snug" x-text="'Contingency (' + terms.contingency + '%)'"></span>
                        <span class="font-medium text-slate-700 whitespace-nowrap" x-text="money(totals.contingency)"></span>
                    </div>
                    <div class="flex justify-between items-baseline gap-3 text-slate-500">
                        <span class="leading-snug" x-text="'SST / Tax (' + terms.tax + '%)'"></span>
                        <span class="font-medium text-slate-700 whitespace-nowrap" x-text="money(totals.tax)"></span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-200/80 mb-6">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Total</span>
                    <span class="block text-2xl font-black text-slate-900" x-text="money(totals.grandTotal)"></span>
                </div>

                <div class="space-y-2.5">
                    <button type="button" @click="openPreview()" class="w-full bg-slate-50 hover:bg-slate-100 text-slate-700 font-semibold py-2.5 rounded-lg border border-slate-200 text-sm transition-all duration-150">
                        Preview
                    </button>

                    <button type="button" @click="saveQuote()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-lg text-sm shadow-sm transition-all duration-150 tracking-wide uppercase">
                        Save Quote
                    </button>
                </div>
            </div>

        </div>
    </div>

    @php
        $projectInfo = [
            'name'   => $project->name ?? null,
            'number' => $project->number ?? null,
            'client' => $project->client->company_name ?? null,
            'period' => $project->period ?? null,
        ];
    @endphp

    <script>
    function quotationBuilder() {
        return {
            savedNotification: false,
            showPreview: false,
            generatedAt: '',
            activePreset: null,

            // Project this quote belongs to (id is needed later when saving)
            projectId: @json($project->project_Id ?? null),
            projectInfo: @json($projectInfo),

            // Real data from the database (Modules, Items, Packages pages)
            catalog: @json($catalog),
            presets: @json($presets),

            // Adjustable terms (percent). Tax defaults to 0 because it is optional.
            terms: {
                contingency: 10,
                tax: 0
            },

            // Optional modules the user ticked (required modules are always on)
            selected: [],

            isSelected(id) {
                const mod = this.catalog.find(m => m.id === id);
                return !!mod && (mod.required || this.selected.includes(id));
            },

            toggle(id) {
                if (this.selected.includes(id)) {
                    this.selected = this.selected.filter(x => x !== id);
                } else {
                    this.selected.push(id);
                }
                this.activePreset = 'custom';
            },

            applyPreset(presetId) {
                const found = this.presets.find(p => p.id === presetId);
                if (found) {
                    this.activePreset = presetId;
                    this.selected = [...found.modules];
                }
            },

            get groups() {
                const map = {};
                this.catalog.forEach(m => {
                    (map[m.group] = map[m.group] || []).push(m);
                });
                return Object.entries(map).map(([name, modules]) => ({
                    name,
                    modules,
                    required: modules.every(m => m.required)
                }));
            },

            get activeModules() {
                return this.catalog.filter(m => this.isSelected(m.id));
            },

            get itemCount() {
                return this.activeModules.reduce((n, m) => n + m.items.length, 0);
            },

            get presetLabel() {
                const p = this.presets.find(x => x.id === this.activePreset);
                return p ? p.name : 'Custom selection';
            },

            get totals() {
                let internal = 0;
                let client = 0;
                this.activeModules.forEach(m => {
                    internal += m.internal_total;
                    client += m.client_total;
                });

                const contingency = client * (this.terms.contingency / 100);
                const tax = (client + contingency) * (this.terms.tax / 100);
                const grandTotal = client + contingency + tax;

                return { internal, client, contingency, tax, grandTotal };
            },

            money(n) {
                return 'RM ' + Number(n).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            },

            openPreview() {
                const d = new Date();
                const pad = n => String(n).padStart(2, '0');
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                this.generatedAt = pad(d.getDate()) + ' ' + months[d.getMonth()] + ' ' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes());
                this.showPreview = true;
            },

            async saveQuote() {
                if (!this.projectId) {
                    alert('No project selected. Open this Builder from a project page first.');
                    return;
                }

                const token = document.querySelector('meta[name="csrf-token"]').content;

                try {
                    const res = await fetch('{{ route('projects.modeling.save') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({
                            project_id: this.projectId,
                            module_ids: this.activeModules.map(m => m.id),
                            package_id: this.presets.find(p => p.id === this.activePreset)?.id ?? null,
                            contingency_percent: this.terms.contingency,
                            tax_percent: this.terms.tax,
                        }),
                    });

                    const data = await res.json();

                    if (data.success) {
                        this.savedNotification = true;
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    } else {
                        alert('Could not save. Please try again.');
                    }
                } catch (e) {
                    alert('Could not save. Please check your connection and try again.');
                }
            }
        };
    }
    </script>
</x-app-layout>