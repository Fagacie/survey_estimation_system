<x-app-layout containerClass="w-full px-6 md:px-12 py-6">
    <div class="font-sans text-slate-800 antialiased" x-data="{ showModal: false, editing: null }" x-cloak>

        <!-- Header -->
        <header class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 mb-6 border-b border-slate-200/60">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Packages</h1>
                <p class="text-slate-500 mt-1 text-sm md:text-base">Preset bundles of modules offered to clients</p>
            </div>
            <div>
                <x-modeling-nav />
            </div>
        </header>

        <div class="flex justify-end mb-6">
            <button @click="showModal = true; editing = null"
                class="px-5 py-2.5 rounded-lg bg-teal-600 text-white font-semibold shadow-sm hover:bg-teal-800 transition-all">
                New package
            </button>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-6 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 text-red-700 border border-red-200 p-3 mb-4 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Packages Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200/80 overflow-hidden">
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
                        <tr>
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
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3.5 py-2.5 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1.5">Modules in this package</label>
                    <div class="border border-slate-200 rounded-lg overflow-y-auto divide-y divide-slate-100 mb-6" style="max-height: 15rem;">
                        @foreach ($modules as $module)
                            <label class="flex items-center gap-3 p-3 hover:bg-slate-50 cursor-pointer select-none text-sm">
                                <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                                    :checked="editing && editing.modules.includes({{ $module->id }})"
                                    class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500">
                                <span class="text-slate-800 font-medium">{{ $module->name }}</span>
                                <span class="ml-auto text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">{{ $module->group }}</span>
                            </label>
                        @endforeach
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