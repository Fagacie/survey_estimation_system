<x-app-layout containerClass="w-full px-4 sm:px-6 xl:px-10 py-6">
    <link rel="stylesheet" href="{{ asset('css/modelling-management.css') }}?v={{ filemtime(public_path('css/modelling-management.css')) }}">


    <div class="ises-modelling-page modelling-catalog font-sans text-slate-800 antialiased" x-data="{ showModal: false, editing: null, search: '' }" x-cloak>

        <div class="mb-4">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Modelling Workspace', 'url' => route('projects.modeling.builder')],
                ['label' => 'Module Catalogue'],
            ]" />
        </div>

        <section class="modelling-page-heading">
            <div class="modelling-heading-copy">
                <div class="modelling-eyebrow"><span class="modelling-eyebrow-dot"></span> MODELLING WORKSPACE <span class="modelling-eyebrow-divider">/</span> CATALOGUE</div>
                <h1>Module catalogue</h1>
                <p>Organise reusable modelling modules by discipline and maintain the items used in cost estimates.</p>
            </div>
            <x-modeling-nav />
        </section>

        <section class="modelling-stat-grid" aria-label="Module catalogue summary">
            <article class="modelling-stat-card">
                <span class="modelling-stat-icon"><i class="fa-solid fa-layer-group"></i></span>
                <div><span class="modelling-stat-label">Total modules</span><strong>{{ $modules->count() }}</strong><small>Reusable catalogue entries</small></div>
            </article>
            <article class="modelling-stat-card">
                <span class="modelling-stat-icon modelling-stat-icon-blue"><i class="fa-solid fa-list-check"></i></span>
                <div><span class="modelling-stat-label">Linked items</span><strong>{{ $modules->sum('items_count') }}</strong><small>Across all modules</small></div>
            </article>
            <article class="modelling-stat-card">
                <span class="modelling-stat-icon modelling-stat-icon-violet"><i class="fa-solid fa-folder-tree"></i></span>
                <div><span class="modelling-stat-label">Module groups</span><strong>{{ $groups->count() }}</strong><small>Distinct disciplines</small></div>
            </article>
        </section>

        <section class="modelling-catalog-panel">
            <div class="modelling-panel-heading">
                <div><h2>All modules</h2><p>Search, review, and maintain your modelling catalogue.</p></div>
                <button type="button" @click="showModal = true; editing = null" class="modelling-primary-action">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i><span>New module</span>
                </button>
            </div>
            <div class="modelling-toolbar">
                <label class="modelling-search" aria-label="Search modules">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input type="search" x-model.debounce.150ms="search" placeholder="Search module name or group..." />
                    <kbd>Ctrl K</kbd>
                </label>
                <span class="modelling-result-count"><strong>{{ $modules->count() }}</strong> records</span>
            </div>

        <!-- Success Alert -->
        @if (session('success'))
            <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-6 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="bg-rose-50 text-rose-800 border border-rose-200 p-4 mb-6 rounded-lg text-sm">
                <div class="font-semibold mb-1">Please correct the following errors:</div>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Modules Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="text-left px-5 py-3">Module</th>
                        <th class="text-left px-5 py-3">Group</th>
                        <th class="text-left px-5 py-3">Items</th>
                        <th class="text-right px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($modules as $module)
                        <tr data-module-row data-search="{{ strtolower($module->name.' '.$module->group) }}" x-show="!search || $el.dataset.search.includes(search.toLowerCase())">
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $module->name }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs font-semibold text-sky-700 bg-sky-50 px-2.5 py-0.5 rounded-full border border-sky-100">
                                    {{ $module->group }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ $module->items_count }} item{{ $module->items_count === 1 ? '' : 's' }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="act-wrap">
                                    <button type="button" class="act-btn act-edit" title="Edit"
                                        @click="showModal = true; editing = { id: {{ $module->id }}, name: @js($module->name), group: @js($module->group) }">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <form action="{{ route('projects.modeling.modules.destroy', $module) }}" method="POST" style="margin:0;"
                                        onsubmit="return confirm('Delete this module?');">
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
                            <td colspan="4" class="px-5 py-8 text-center text-slate-400">No modules yet. Click "New module" to add one.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="modelling-empty-search" x-show="search && ![...$el.parentElement.querySelectorAll('[data-module-row]')].some(row => row.dataset.search.includes(search.toLowerCase()))" x-cloak>
            <i class="fa-solid fa-magnifying-glass"></i><strong>No matching modules</strong><span>Try a different name or group.</span>
        </div>
        </section>

        <!-- Modal -->
        <div x-show="showModal" x-cloak
             x-data="{ groupMode: 'existing' }"
             class="fixed inset-0 bg-slate-900/40 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg" @click.outside="showModal = false">
                <h2 class="text-lg font-bold text-slate-900 mb-4" x-text="editing ? 'Edit module' : 'New module'"></h2>

                <form :action="editing ? '{{ url('projects/modeling/modules') }}/' + editing.id : '{{ route('projects.modeling.modules.store') }}'"
                      method="POST">
                    @csrf
                    <template x-if="editing">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Name</label>
                    <input type="text" name="name" :value="editing ? editing.name : ''" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-sky-500">

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Module group</label>

                    <template x-if="groupMode === 'existing'">
                        <select name="group" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="" disabled selected>Select a group</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group }}" :selected="editing && editing.group === '{{ $group }}'">{{ $group }}</option>
                            @endforeach
                        </select>
                    </template>

                    <template x-if="groupMode === 'new'">
                        <input type="text" name="group" required
                               placeholder="e.g. Sediment Transport"
                               class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </template>

                    <button type="button"
                            @click="groupMode = groupMode === 'existing' ? 'new' : 'existing'"
                            class="text-xs font-semibold text-sky-700 hover:underline mb-6">
                        <span x-text="groupMode === 'existing' ? '+ Add new group' : '← Choose existing group instead'"></span>
                    </button>

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