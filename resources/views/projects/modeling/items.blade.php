<x-app-layout containerClass="w-full px-4 sm:px-6 xl:px-10 py-6">
    <link rel="stylesheet" href="{{ asset('css/modelling-management.css') }}?v={{ filemtime(public_path('css/modelling-management.css')) }}">


    <div class="ises-modelling-page modelling-catalog font-sans text-slate-800 antialiased"
         x-data="itemsPage()"
         x-cloak>

        <section class="modelling-page-heading">
            <div class="modelling-heading-copy">
                <div class="modelling-eyebrow"><span class="modelling-eyebrow-dot"></span> MODELLING WORKSPACE <span class="modelling-eyebrow-divider">/</span> COST LIBRARY</div>
                <h1>Modelling items</h1>
                <p>Manage priced activities, work packages, quantities, and mark-ups used by the cost estimator.</p>
            </div>
            <x-modeling-nav />
        </section>
        <section class="modelling-stat-grid" aria-label="Item catalogue summary">
            <article class="modelling-stat-card"><span class="modelling-stat-icon"><i class="fa-solid fa-list-check"></i></span><div><span class="modelling-stat-label">Items in view</span><strong>{{ $items->count() }}</strong><small>For the selected module</small></div></article>
            <article class="modelling-stat-card"><span class="modelling-stat-icon modelling-stat-icon-blue"><i class="fa-solid fa-layer-group"></i></span><div><span class="modelling-stat-label">Available modules</span><strong>{{ $modules->count() }}</strong><small>Organised by discipline</small></div></article>
            <article class="modelling-stat-card"><span class="modelling-stat-icon modelling-stat-icon-violet"><i class="fa-solid fa-coins"></i></span><div><span class="modelling-stat-label">Client value in view</span><strong>RM {{ number_format($items->sum('client_cost'), 2) }}</strong><small>Combined item value</small></div></article>
        </section>

        @if (session('success'))
            <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-6 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <section class="modelling-catalog-panel">
            <div class="modelling-panel-heading">
                <div><h2>Item library</h2><p>Filter by module or search activities and work packages.</p></div>
                <button type="button" @click="showModal = true; editing = null" class="modelling-primary-action"><i class="fa-solid fa-plus"></i><span>New item</span></button>
            </div>
            <div class="modelling-toolbar modelling-items-toolbar">
            <form method="GET" action="{{ route('projects.modeling.items.index') }}">
                <select name="module_id" onchange="this.form.submit()" style="min-width: 280px;"
                    class="bg-white border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                    @foreach ($modules as $module)
                        <option value="{{ $module->id }}" {{ (string) $selectedModuleId === (string) $module->id ? 'selected' : '' }}>
                            {{ $module->group }} — {{ $module->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            <label class="modelling-search" aria-label="Search items"><i class="fa-solid fa-magnifying-glass"></i><input type="search" x-model.debounce.150ms="search" placeholder="Search item or work package..." /></label>
            </div>

        @if ($errors->any())
            <div class="bg-red-50 text-red-700 border border-red-200 p-3 mb-4 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Items Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="text-left px-5 py-3" style="width: 40%;">Item</th>
                        <th class="text-left px-5 py-3">Work package</th>
                        <th class="text-right px-5 py-3">Cost to client</th>
                        <th class="text-right px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($items as $item)
                        <tr data-item-row data-search="{{ strtolower($item->name.' '.$item->work_package) }}" x-show="!search || $el.dataset.search.includes(search.toLowerCase())">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $item->name }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $item->work_package }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-slate-700 whitespace-nowrap">RM {{ number_format($item->client_cost, 2) }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="act-wrap">
                                    <button type="button" class="act-btn act-edit" title="Edit"
                                        @click="showModal = true; editing = {
                                            id: {{ $item->id }},
                                            module_id: {{ $item->module_id }},
                                            work_package: @js($item->work_package),
                                            name: @js($item->name),
                                            qty: {{ $item->qty }},
                                            rate: {{ $item->rate }},
                                            days: {{ $item->days }},
                                            markup: {{ $item->markup }}
                                        }">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('projects.modeling.items.destroy', $item) }}" method="POST" style="margin:0;"
                                        onsubmit="return confirm('Delete this item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="act-btn act-del" title="Delete">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-400">No items for this module yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="modelling-empty-search" x-show="search && ![...$el.parentElement.querySelectorAll('[data-item-row]')].some(row => row.dataset.search.includes(search.toLowerCase()))" x-cloak><i class="fa-solid fa-magnifying-glass"></i><strong>No matching items</strong><span>Try another activity or work package.</span></div>
        </section>

        <!-- Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 bg-slate-900/40 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl p-6 w-full shadow-lg" style="max-width: 32rem;" @click.outside="showModal = false">
                <h2 class="text-lg font-bold text-slate-900 mb-4" x-text="editing ? 'Edit item' : 'New item'"></h2>

                <form :action="editing ? '{{ url('projects/modeling/items') }}/' + editing.id : '{{ route('projects.modeling.items.store') }}'"
                      method="POST" x-data="{
                          form: editing ?? { module_id: {{ $selectedModuleId ?? 'null' }}, work_package: '', name: '', qty: 1, rate: 0, days: 1, markup: 30 }
                      }" x-init="$watch('editing', v => form = v ?? { module_id: {{ $selectedModuleId ?? 'null' }}, work_package: '', name: '', qty: 1, rate: 0, days: 1, markup: 30 })">
                    @csrf
                    <template x-if="editing">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Item / activity</label>
                    <input type="text" name="name" x-model="form.name" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-sky-500">

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Module</label>
                            <select name="module_id" x-model="form.module_id" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                                @foreach ($modules as $module)
                                    <option value="{{ $module->id }}">{{ $module->group }} — {{ $module->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Work package</label>
                            <input type="text" name="work_package" x-model="form.work_package" required placeholder="e.g. Model Setup"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Qty</label>
                            <input type="number" step="0.01" name="qty" x-model.number="form.qty" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Rate (MYR/day)</label>
                            <input type="number" step="0.01" name="rate" x-model.number="form.rate" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Days / units</label>
                            <input type="number" step="0.01" name="days" x-model.number="form.days" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Mark-up (%)</label>
                            <input type="number" step="0.01" name="markup" x-model.number="form.markup" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-lg p-3 mb-6 space-y-1">
                        <div class="flex justify-between text-sm text-slate-500">
                            <span>Internal cost</span>
                            <span x-text="'RM ' + (form.qty * form.rate * form.days).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                        <div class="flex justify-between text-sm font-semibold text-slate-700">
                            <span>Cost to client</span>
                            <span x-text="'RM ' + ((form.qty * form.rate * form.days) / (1 - (form.markup / 100) || 1)).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium text-sm">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-sky-700 text-white font-semibold hover:bg-sky-800 text-sm shadow-sm">Save</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>

<script>
function itemsPage() {
    return {
        showModal: false,
        editing: null,
        search: '',
    };
}
</script>