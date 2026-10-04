<x-app-layout containerClass="w-full px-6 md:px-12 py-6">
    <div class="font-sans text-slate-800 antialiased"
         x-data="itemsPage()"
         x-cloak>

        <!-- Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 mb-6 border-b border-slate-200/60">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Items</h1>
                <p class="text-slate-500 mt-1 text-sm md:text-base">Line items priced by qty x rate x days, with mark-up</p>
            </div>
            <div>
                <x-modeling-nav />
            </div>
        </header>

        @if (session('success'))
            <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-6 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <!-- Module Filter + New Item -->
        <div class="flex items-center justify-between gap-3 mb-4">
            <form method="GET" action="{{ route('projects.modeling.items.index') }}">
                <select name="module_id" onchange="this.form.submit()" style="min-width: 280px;"
                    class="bg-white border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach ($modules as $module)
                        <option value="{{ $module->id }}" {{ (string) $selectedModuleId === (string) $module->id ? 'selected' : '' }}>
                            {{ $module->group }} — {{ $module->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            <button @click="showModal = true; editing = null"
                class="px-5 py-2.5 rounded-lg bg-teal-600 text-white font-semibold shadow-sm hover:bg-teal-700 transition-all">
                New item
            </button>
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
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="text-left px-5 py-3">Item</th>
                        <th class="text-left px-5 py-3">Work package</th>
                        <th class="text-right px-5 py-3">Cost to client</th>
                        <th class="text-right px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($items as $item)
                        <tr>
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $item->name }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $item->work_package }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-slate-700">RM {{ number_format($item->client_cost, 2) }}</td>
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
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Module</label>
                            <select name="module_id" x-model="form.module_id" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach ($modules as $module)
                                    <option value="{{ $module->id }}">{{ $module->group }} — {{ $module->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Work package</label>
                            <input type="text" name="work_package" x-model="form.work_package" required placeholder="e.g. Model Setup"
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Qty</label>
                            <input type="number" step="0.01" name="qty" x-model.number="form.qty" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Rate (MYR/day)</label>
                            <input type="number" step="0.01" name="rate" x-model.number="form.rate" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Days / units</label>
                            <input type="number" step="0.01" name="days" x-model.number="form.days" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Mark-up (%)</label>
                            <input type="number" step="0.01" name="markup" x-model.number="form.markup" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="bg-slate-50 rounded-lg p-3 mb-6 space-y-1">
                        <div class="flex justify-between text-sm text-slate-500">
                            <span>Internal cost</span>
                            <span x-text="'RM ' + (form.qty * form.rate * form.days).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                        <div class="flex justify-between text-sm font-semibold text-slate-700">
                            <span>Cost to client</span>
                            <div class="flex justify-between text-sm font-semibold text-slate-700">
                            <span x-text="'RM ' + ((form.qty * form.rate * form.days) / (1 - (form.markup / 100) || 1)).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                        </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 rounded-lg text-slate-600 hover:bg-slate-100 font-medium text-sm">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 text-sm">Save</button>
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
    };
}
</script>