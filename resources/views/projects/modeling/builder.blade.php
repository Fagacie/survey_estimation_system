<x-app-layout containerClass="w-full px-4 sm:px-6 xl:px-8 py-6">
    <x-slot name="header">Modelling Cost Estimator</x-slot>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Stylesheet --}}
    <link rel="stylesheet" href="{{ asset('css/modelling-management.css') }}?v={{ filemtime(public_path('css/modelling-management.css')) }}">

    <div class="mb-builder-shell ises-modelling-page" x-data="quotationBuilder()" x-cloak>

        <!-- ============ PREVIEW SHEET MODAL ============ -->
        <div x-show="showPreview" x-cloak
             @keydown.escape.window="showPreview = false"
             @click.self="showPreview = false"
             class="mb-modal-overlay">

            <div class="mb-modal-sheet">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid #e2e8f0;">
                    <div>
                        <h2 style="font-family:'Space Grotesk',sans-serif; font-size:1.6rem; font-weight:700; color:#0f172a; margin:0 0 4px 0;">
                            Modelling Cost Preview
                        </h2>
                        <div style="font-size:0.8125rem; color:#64748b;">
                            Generated <span x-text="generatedAt"></span> &bull;
                            <span style="display:inline-block; font-size:0.7rem; font-weight:700; color:#b45309; background:#fef3c7; border:1px solid rgba(217,119,6,0.3); border-radius:999px; padding:2px 10px; margin-left:4px;">
                                DRAFT - NOT SAVED YET
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn-mb-preview" style="width:auto; padding:6px 16px; margin:0;" @click="showPreview = false">
                        <i class="fa-solid fa-xmark"></i> Close
                    </button>
                </div>

                <!-- Project info -->
                <table class="w-full text-sm mb-5" style="border-collapse:separate; border-spacing:0; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                    <tr>
                        <td style="background:#f8fafc; font-weight:700; color:#475569; padding:8px 14px; width:20%; border-bottom:1px solid #e2e8f0;">Project</td>
                        <td style="padding:8px 14px; color:#0f172a; border-bottom:1px solid #e2e8f0;" x-text="projectInfo.name || '-'"></td>
                        <td style="background:#f8fafc; font-weight:700; color:#475569; padding:8px 14px; width:20%; border-bottom:1px solid #e2e8f0;">Project Ref.</td>
                        <td style="padding:8px 14px; color:#0f172a; border-bottom:1px solid #e2e8f0;" x-text="projectInfo.number || '-'"></td>
                    </tr>
                    <tr>
                        <td style="background:#f8fafc; font-weight:700; color:#475569; padding:8px 14px;">Client</td>
                        <td style="padding:8px 14px; color:#0f172a;" x-text="projectInfo.client || '-'"></td>
                        <td style="background:#f8fafc; font-weight:700; color:#475569; padding:8px 14px;">Period</td>
                        <td style="padding:8px 14px; color:#0f172a;" x-text="projectInfo.period || '-'"></td>
                    </tr>
                </table>

                <h3 style="font-family:'Space Grotesk',sans-serif; font-size:1.1rem; font-weight:700; color:#0f172a; margin:20px 0 10px;">
                    Modelling Summary
                </h3>
                <table class="w-full text-sm mb-6" style="border-collapse:separate; border-spacing:0; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">
                    <tr>
                        <td style="background:#f8fafc; font-weight:700; color:#475569; padding:8px 14px; width:30%; border-bottom:1px solid #e2e8f0;">Selected Package</td>
                        <td colspan="3" style="padding:8px 14px; font-weight:600; color:#0284c7; border-bottom:1px solid #e2e8f0;" x-text="presetLabel"></td>
                    </tr>
                    <tr>
                        <td style="background:#f8fafc; font-weight:700; color:#475569; padding:8px 14px; border-bottom:1px solid #e2e8f0;">Subtotal (Internal)</td>
                        <td style="padding:8px 14px; border-bottom:1px solid #e2e8f0;" x-text="money(totals.internal)"></td>
                        <td style="background:#f8fafc; font-weight:700; color:#475569; padding:8px 14px; border-bottom:1px solid #e2e8f0;">Subtotal (Client)</td>
                        <td style="padding:8px 14px; border-bottom:1px solid #e2e8f0;" x-text="money(totals.client)"></td>
                    </tr>
                    <tr>
                        <td style="background:#f8fafc; font-weight:700; color:#475569; padding:8px 14px; border-bottom:1px solid #e2e8f0;" x-text="'Contingency (' + terms.contingency + '%)'"></td>
                        <td style="padding:8px 14px; border-bottom:1px solid #e2e8f0;" x-text="money(totals.contingency)"></td>
                        <td style="background:#f8fafc; font-weight:700; color:#475569; padding:8px 14px; border-bottom:1px solid #e2e8f0;" x-text="'SST / Tax (' + terms.tax + '%)'"></td>
                        <td style="padding:8px 14px; border-bottom:1px solid #e2e8f0;" x-text="money(totals.tax)"></td>
                    </tr>
                    <tr style="background:#f8fafc;">
                        <td style="font-weight:700; color:#0f172a; padding:10px 14px; font-size:1rem;">Grand Total</td>
                        <td colspan="3" style="font-family:'Space Grotesk',sans-serif; font-size:1.15rem; font-weight:800; color:#0284c7; padding:10px 14px;" x-text="money(totals.grandTotal)"></td>
                    </tr>
                </table>

                <!-- Modules and items -->
                <h3 style="font-family:'Space Grotesk',sans-serif; font-size:1.1rem; font-weight:700; color:#0f172a; margin:20px 0 10px;" x-text="'Modules & Items — ' + presetLabel"></h3>

                <template x-for="mod in activeModules" :key="mod.id">
                    <div class="mb-4">
                        <div style="font-weight:700; color:#0f172a; font-size:0.9rem; margin-bottom:6px;">
                            <span x-text="mod.name"></span>
                            <span style="font-weight:500; color:#64748b; font-size:0.8rem;" x-text="' — ' + mod.group"></span>
                        </div>
                        <table class="w-full text-sm mb-3" style="border-collapse:separate; border-spacing:0; border:1px solid #e2e8f0; border-radius:6px; overflow:hidden;">
                            <thead>
                                <tr style="background:#f8fafc;">
                                    <th style="padding:6px 12px; text-align:left; font-size:0.75rem; text-transform:uppercase; color:#64748b; border-bottom:1px solid #e2e8f0;">Item</th>
                                    <th style="padding:6px 12px; text-align:left; font-size:0.75rem; text-transform:uppercase; color:#64748b; border-bottom:1px solid #e2e8f0; width:25%;">Work Package</th>
                                    <th style="padding:6px 12px; text-align:right; font-size:0.75rem; text-transform:uppercase; color:#64748b; border-bottom:1px solid #e2e8f0; width:22%;">Cost to Client</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(sub, i) in mod.items" :key="i">
                                    <tr>
                                        <td style="padding:6px 12px; border-bottom:1px solid #f1f5f9;" x-text="sub.name"></td>
                                        <td style="padding:6px 12px; color:#64748b; border-bottom:1px solid #f1f5f9;" x-text="sub.work_package"></td>
                                        <td style="padding:6px 12px; text-align:right; font-weight:600; border-bottom:1px solid #f1f5f9;" x-text="money(sub.client)"></td>
                                    </tr>
                                </template>
                                <tr x-show="mod.items.length === 0">
                                    <td colspan="3" style="padding:8px 12px; color:#94a3b8; text-align:center;">No items in this module yet.</td>
                                </tr>
                                <tr style="background:#f8fafc; font-weight:700;">
                                    <td colspan="2" style="padding:6px 12px; color:#475569;">Module Total</td>
                                    <td style="padding:6px 12px; text-align:right; color:#0f172a;" x-text="money(mod.client_total)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </template>

                <p x-show="activeModules.length === 0" style="color:#94a3b8; font-size:0.875rem;">No modules selected.</p>

                <div style="margin-top:24px; padding-top:14px; border-top:1px solid #e2e8f0; font-size:0.75rem; color:#64748b; display:flex; justify-content:space-between; align-items:center;">
                    <span>Draft estimation preview for review only. Nothing is permanently committed until saved.</span>
                    <button type="button" class="btn-mb-preview" style="width:auto; padding:6px 16px; margin:0;" @click="showPreview = false">Close</button>
                </div>
            </div>
        </div>
        <!-- ============ END PREVIEW SHEET ============ -->

        {{-- Breadcrumb Navigation --}}
        <nav class="mb-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Dashboard</a>
            <span class="mb-breadcrumb-sep">/</span>
            <a href="{{ route('projects.index') }}">Projects</a>
            <span class="mb-breadcrumb-sep">/</span>
            @if($project)
                <a href="{{ route('projects.show', $project->project_Id) }}">{{ Str::limit($project->name, 24) }}</a>
                <span class="mb-breadcrumb-sep">/</span>
            @endif
            <a href="{{ route('projects.modeling.modules.index') }}">Modelling</a>
            <span class="mb-breadcrumb-sep">/</span>
            <span class="mb-breadcrumb-current">Cost Estimator</span>
        </nav>

        {{-- Page Header --}}
        <header class="mb-header-card">
            <div>
                <h1 class="mb-header-title">Modelling Cost Estimator</h1>
                <p class="mb-header-subtitle">Select simulation modules, adjust commercial terms, and generate accurate modelling estimates.</p>
            </div>
            <div>
                <x-modeling-nav />
            </div>
        </header>

        {{-- Success Alert Banner --}}
        <div x-show="savedNotification" x-transition class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-5 rounded-xl flex items-center justify-between text-sm shadow-sm">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                <span class="font-medium">Modelling quotation snapshot successfully saved to project!</span>
            </div>
            <button @click="savedNotification = false" class="text-emerald-600 hover:text-emerald-800 font-bold ml-4">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        {{-- Project Meta Row --}}
        <div class="mb-meta-card">
            <div class="mb-meta-item">
                <label>Project Name</label>
                <div class="mb-meta-val">
                    <i class="bi bi-folder2 text-sky-600"></i>
                    <span>{{ $project->name ?? 'No Project Loaded' }}</span>
                </div>
            </div>
            <div class="mb-meta-item">
                <label>Client</label>
                <div class="mb-meta-val">
                    <i class="bi bi-building text-sky-600"></i>
                    <span>{{ $project->client->company_name ?? 'Standard Client' }}</span>
                </div>
            </div>
            <div class="mb-meta-item">
                <label>Project Reference</label>
                <div class="mb-meta-val">
                    <i class="bi bi-hash text-sky-600"></i>
                    <span>{{ $project->number ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        {{-- Package Presets Pills --}}
        <div class="mb-preset-wrap">
            <span style="font-size:0.8125rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#64748b; margin-right:4px;">
                Preset Packages:
            </span>
            <template x-for="preset in presets" :key="preset.id">
                <button type="button"
                    @click="applyPreset(preset.id)"
                    :class="activePreset === preset.id ? 'active' : ''"
                    class="mb-preset-pill">
                    <i class="fa-solid" :class="activePreset === preset.id ? 'fa-check' : 'fa-box-archive'"></i>
                    <span x-text="preset.name"></span>
                </button>
            </template>
            <p x-show="presets.length === 0" class="text-sm text-slate-400">
                No preset packages configured. Create a package in Packages to quickly load scopes.
            </p>
        </div>

        <!-- 3-Column Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- Column 1: Module Library + Commercial Terms -->
            <div class="lg:col-span-4 space-y-5">

                <!-- Module Library (from Modules page) -->
                <div class="mb-card">
                    <h3 class="mb-card-title">
                        <span>Module Library</span>
                        <span class="text-xs font-semibold text-slate-400" x-text="catalog.length + ' modules'"></span>
                    </h3>

                    <div class="space-y-5">
                        <template x-for="group in groups" :key="group.name">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500" x-text="group.name"></div>
                                    <span x-show="group.required" class="text-xs font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-100">Required</span>
                                </div>
                                <div class="space-y-1">
                                    <template x-for="mod in group.modules" :key="mod.id">
                                        <label class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 cursor-pointer select-none text-sm font-medium text-slate-800 transition-colors">
                                            <input type="checkbox"
                                                :checked="isSelected(mod.id)"
                                                :disabled="mod.required"
                                                @change="toggle(mod.id)"
                                                class="w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500 transition">
                                            <span x-text="mod.name"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div x-show="catalog.length === 0" class="py-6 text-center text-slate-400 text-sm">
                            No modules found. Add some on the Modules catalogue first.
                        </div>
                    </div>
                </div>

                <!-- Commercial Terms -->
                <div class="mb-card">
                    <h3 class="mb-card-title">
                        <span>Commercial Terms</span>
                    </h3>

                    <div class="space-y-4">
                        <!-- Contingency -->
                        <div class="mb-slider-item">
                            <div class="mb-slider-top">
                                <span class="mb-slider-label">Contingency Mark-up</span>
                                <span class="mb-slider-badge" x-text="terms.contingency + '%'"></span>
                            </div>
                            <input type="range" min="0" max="30" step="1" x-model.number="terms.contingency" class="mb-range-slider">
                        </div>

                        <!-- SST / Tax -->
                        <div class="mb-slider-item">
                            <div class="mb-slider-top">
                                <span class="mb-slider-label">SST / Service Tax</span>
                                <span class="mb-slider-badge" x-text="terms.tax + '%'"></span>
                            </div>
                            <input type="range" min="0" max="20" step="1" x-model.number="terms.tax" class="mb-range-slider">
                            <p class="text-xs text-slate-400 mt-2">Optional service tax. Set to 0% for tax-exempt contracts.</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Column 2: Cost Ladder (from Items page) -->
            <div class="lg:col-span-5 mb-card">
                <h3 class="mb-card-title">
                    <span>Cost Ladder</span>
                    <span class="text-xs font-semibold text-slate-400" x-text="activeModules.length + ' modules active'"></span>
                </h3>

                <div class="space-y-4">
                    <template x-for="mod in activeModules" :key="mod.id">
                        <div class="mb-ladder-group">
                            <div class="mb-ladder-header">
                                <h4 class="mb-ladder-title" x-text="mod.name"></h4>
                                <span class="mb-ladder-total" x-text="money(mod.client_total)"></span>
                            </div>
                            <div class="space-y-1">
                                <template x-for="(sub, i) in mod.items" :key="i">
                                    <div class="mb-ladder-item">
                                        <span x-text="sub.name"></span>
                                        <span class="font-medium text-slate-700 whitespace-nowrap" x-text="money(sub.client)"></span>
                                    </div>
                                </template>
                            </div>
                            <p x-show="mod.items.length === 0" class="text-xs text-slate-400 mt-1">No items configured in this module.</p>
                        </div>
                    </template>

                    <div x-show="activeModules.length === 0" class="py-12 text-center text-slate-400 text-sm">
                        <i class="bi bi-diagram-3 fs-3 d-block mb-2 text-slate-300"></i>
                        No active modules selected. Choose modules from the library to generate cost ladder.
                    </div>
                </div>
            </div>

            <!-- Column 3: Summary (Sticky) -->
            <div class="lg:col-span-3">
                <div class="mb-summary-card">
                    <h3 class="mb-card-title">
                        <span>Commercial Summary</span>
                    </h3>

                    <div class="space-y-2 mb-4">
                        <div class="mb-summary-row">
                            <span>Subtotal (Internal)</span>
                            <span class="mb-summary-val" x-text="money(totals.internal)"></span>
                        </div>
                        <div class="mb-summary-row">
                            <span>Subtotal (Client)</span>
                            <span class="mb-summary-val" x-text="money(totals.client)"></span>
                        </div>
                        <div class="mb-summary-row">
                            <span x-text="'Contingency (' + terms.contingency + '%)'"></span>
                            <span class="mb-summary-val" x-text="money(totals.contingency)"></span>
                        </div>
                        <div class="mb-summary-row">
                            <span x-text="'SST / Tax (' + terms.tax + '%)'"></span>
                            <span class="mb-summary-val" x-text="money(totals.tax)"></span>
                        </div>
                    </div>

                    <div class="mb-grand-total-wrap">
                        <span class="mb-grand-total-label">Grand Total</span>
                        <span class="mb-grand-total-val" x-text="money(totals.grandTotal)"></span>
                    </div>

                    <div>
                        <button type="button" @click="openPreview()" class="btn-mb-preview">
                            <i class="fa-regular fa-eye"></i> Preview Sheet
                        </button>

                        <button type="button" @click="saveQuote()" class="btn-mb-save">
                            <i class="fa-solid fa-floppy-disk"></i> Save Quote
                        </button>
                    </div>
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