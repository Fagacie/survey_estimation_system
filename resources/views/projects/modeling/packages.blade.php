<x-app-layout containerClass="w-full px-4 sm:px-6 xl:px-10 py-6">
    <link rel="stylesheet" href="{{ asset('css/modelling-management.css') }}?v={{ filemtime(public_path('css/modelling-management.css')) }}">


    <div class="ises-modelling-page modelling-catalog font-sans text-slate-800 antialiased" x-data="{ showModal: false, editing: null, search: '' }" x-cloak>

        <section class="modelling-page-heading">
            <div class="modelling-heading-copy">
                <div class="modelling-eyebrow"><span class="modelling-eyebrow-dot"></span> MODELLING WORKSPACE <span class="modelling-eyebrow-divider">/</span> PRESETS</div>
                <h1>Modelling packages</h1>
                <p>Build reusable bundles of modules to speed up project estimates and keep standard scopes consistent.</p>
            </div>
            <x-modeling-nav />
        </section>
        <section class="modelling-stat-grid" aria-label="Package catalogue summary">
            <article class="modelling-stat-card"><span class="modelling-stat-icon"><i class="fa-solid fa-boxes-stacked"></i></span><div><span class="modelling-stat-label">Saved packages</span><strong>{{ $packages->count() }}</strong><small>Reusable scope presets</small></div></article>
            <article class="modelling-stat-card"><span class="modelling-stat-icon modelling-stat-icon-blue"><i class="fa-solid fa-layer-group"></i></span><div><span class="modelling-stat-label">Available modules</span><strong>{{ $modules->count() }}</strong><small>In the catalogue</small></div></article>
            <article class="modelling-stat-card"><span class="modelling-stat-icon modelling-stat-icon-violet"><i class="fa-solid fa-diagram-project"></i></span><div><span class="modelling-stat-label">Module assignments</span><strong>{{ $packages->sum(fn ($package) => $package->modules->count()) }}</strong><small>Across all saved packages</small></div></article>
        </section>
        <section class="modelling-catalog-panel">
            <div class="modelling-panel-heading">
                <div><h2>Package library</h2><p>Review included modules and maintain reusable modelling scopes.</p></div>
                <button type="button" @click="showModal = true; editing = null" class="modelling-primary-action"><i class="fa-solid fa-plus"></i><span>New package</span></button>
            </div>
            <div class="modelling-toolbar">
                <label class="modelling-search" aria-label="Search packages"><i class="fa-solid fa-magnifying-glass"></i><input type="search" x-model.debounce.150ms="search" placeholder="Search package or included module..." /></label>
                <span class="modelling-result-count"><strong>{{ $packages->count() }}</strong> records</span>
            </div>

        @if (session('success'))
            <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-6 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="bg-red-50 text-red-700 border border-red-200 p-3 mb-4 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Packages Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="text-left px-5 py-3">Package</th>
                        <th class="text-left px-5 py-3">Modules</th>
                        <th class="text-right px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($packages as $package)
                        <tr data-package-row data-search="{{ strtolower($package->name.' '.$package->modules->pluck('name')->implode(' ')) }}" x-show="!search || $el.dataset.search.includes(search.toLowerCase())">
                            <td class="px-5 py-3 font-medium text-slate-800 align-top whitespace-nowrap">{{ $package->name }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $package->modules->pluck('name')->implode(', ') }}</td>
                            <td class="px-5 py-3 text-right align-top whitespace-nowrap">
                                <div class="act-wrap">
                                    <button type="button" class="act-btn act-edit" title="Edit"
                                        @click="showModal = true; editing = { id: {{ $package->id }}, name: @js($package->name), modules: @js($package->modules->pluck('id')) }">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('projects.modeling.packages.destroy', $package) }}" method="POST" style="margin:0;"
                                        onsubmit="return confirm('Delete this package?');">
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
                            <td colspan="3" class="px-5 py-8 text-center text-slate-400">No packages yet. Click "New package" to add one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
            <div class="modelling-empty-search" x-show="search && ![...$el.parentElement.querySelectorAll('[data-package-row]')].some(row => row.dataset.search.includes(search.toLowerCase()))" x-cloak><i class="fa-solid fa-magnifying-glass"></i><strong>No matching packages</strong><span>Try another package or module name.</span></div>
        </section>

        <!-- Modal -->
        <div x-show="showModal" x-cloak class="fixed inset-0 bg-slate-900/40 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl p-6 w-full shadow-lg" style="max-width: 28rem;" @click.outside="showModal = false">
                <h2 class="text-lg font-bold text-slate-900 mb-4" x-text="editing ? 'Edit package' : 'New package'"></h2>

                <form :action="editing ? '{{ url('projects/modeling/packages') }}/' + editing.id : '{{ route('projects.modeling.packages.store') }}'"
                      method="POST">
                    @csrf
                    <template x-if="editing">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Name</label>
                    <input type="text" name="name" :value="editing ? editing.name : ''" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-sky-500">

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Modules in this package</label>
                    <div class="border border-slate-200 rounded-lg overflow-y-auto divide-y divide-slate-100 mb-6" style="max-height: 15rem;">
                        @foreach ($modules as $module)
                            <label class="flex items-center gap-3 p-3 hover:bg-slate-50 cursor-pointer select-none text-sm">
                                <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                                    :checked="editing && editing.modules.includes({{ $module->id }})"
                                    class="w-4 h-4 text-sky-700 rounded border-slate-300 focus:ring-sky-500">
                                <span class="text-slate-800 font-medium">{{ $module->name }}</span>
                                <span class="ml-auto text-xs font-semibold text-sky-700 bg-sky-50 px-2.5 py-0.5 rounded-full border border-sky-100">{{ $module->group }}</span>
                            </label>
                        @endforeach
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