<x-app-layout containerClass="w-full px-6 md:px-12 py-6">
    <div class="font-sans text-slate-800 antialiased" x-data="{ showModal: false, editing: null }" x-cloak>

        <!-- Header Section -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 mb-8 border-b border-slate-200/60">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Modules</h1>
                <p class="text-slate-500 mt-1 text-sm md:text-base">Grouped under a module category</p>
            </div>
            <div>
                <x-modeling-nav />
            </div>
        </header>

        <!-- New Module Button -->
        <div class="flex justify-end mb-6">
            <button @click="showModal = true; editing = null"
                class="px-5 py-2.5 rounded-lg bg-teal-600 text-white font-semibold shadow-sm hover:bg-teal-800 transition-all">
                New module
            </button>
        </div>

        <!-- Success Alert -->
        @if (session('success'))
            <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-6 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Modules Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
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
                        <tr>
                            <td class="px-5 py-3 font-medium text-slate-800">{{ $module->name }}</td>
                            <td class="px-5 py-3">
                                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
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
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Module group</label>

                    <template x-if="groupMode === 'existing'">
                        <select name="group" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="" disabled selected>Select a group</option>
                            @foreach ($groups as $group)
                                <option value="{{ $group }}" :selected="editing && editing.group === '{{ $group }}'">{{ $group }}</option>
                            @endforeach
                        </select>
                    </template>

                    <template x-if="groupMode === 'new'">
                        <input type="text" name="group" required
                               placeholder="e.g. Sediment Transport"
                               class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </template>

                    <button type="button"
                            @click="groupMode = groupMode === 'existing' ? 'new' : 'existing'"
                            class="text-xs font-semibold text-blue-600 hover:underline mb-6">
                        <span x-text="groupMode === 'existing' ? '+ Add new group' : '← Choose existing group instead'"></span>
                    </button>

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