<x-app-layout containerClass="w-full px-8 py-8">
    
    <!-- 1. PAGE HEADER -->
    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 mb-1 tracking-tight">Clients</h1>
            <div class="text-sm font-medium text-slate-500">Manage client details and contacts</div>
        </div>
        <div>
            <!-- Reusing Bootstrap modal trigger, styled with Tailwind -->
            <button data-bs-toggle="modal" data-bs-target="#createClientModal" class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 text-sm font-medium transition-colors border border-transparent shadow-sm">
                <i class="fa-solid fa-plus font-light"></i> Add Client
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-8 flex items-start gap-3 text-sm">
            <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- 2. CLIENTS TABLE WORKSPACE -->
    <div class="bg-white border border-slate-200 mb-12 shadow-sm">
        
        <!-- Table Controls -->
        <div class="px-5 py-3 border-b border-slate-200 flex flex-wrap justify-between items-center gap-4 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">Client Directory</h3>
        </div>

        <!-- Table Data (Dense) -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-500">
                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px]">Name / Company</th>
                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px]">Contact Info</th>
                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px]">Projects</th>
                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($clients as $client)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-5 py-3 align-middle">
                                <div class="font-semibold text-slate-800">{{ $client->name }}</div>
                                <div class="text-[11px] text-slate-500 flex items-center mt-0.5">
                                    <i class="fa-regular fa-building mr-1 opacity-70"></i> {{ $client->company ?? 'No Company' }}
                                </div>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <div class="text-slate-700 text-xs font-medium mb-1 flex items-center"><i class="fa-regular fa-envelope mr-1.5 opacity-70 text-slate-400 w-3"></i> {{ $client->email ?? '-' }}</div>
                                <div class="text-slate-700 text-xs font-medium flex items-center"><i class="fa-solid fa-phone mr-1.5 opacity-70 text-slate-400 w-3"></i> {{ $client->phone ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <span class="bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded border border-slate-200 text-xs font-medium">
                                    {{ $client->projects->count() }} Projects
                                </span>
                            </td>
                            <td class="px-5 py-3 align-middle text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- We do not have a dedicated edit view, assuming it uses a modal or isn't built yet based on original code, so keeping it disabled or unlinked as original just had # -->
                                    <button type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-all" title="Edit Client">
                                        <i class="fa-solid fa-pen font-light text-sm"></i>
                                    </button>
                                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="inline-block m-0 form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all" title="Delete Client" onclick="return confirm('Are you sure?')">
                                            <i class="fa-solid fa-trash-can font-light text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 bg-white">
                                <div class="text-slate-500 mb-2 font-medium">No clients found.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Client Modal (Bootstrap + Tailwind styling) -->
    <div class="modal fade" id="createClientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content !border-slate-200 !rounded-none !shadow-lg">
                <div class="modal-header border-b border-slate-200 !p-5">
                    <h5 class="modal-title font-bold text-slate-900 tracking-tight text-lg">Add New Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('clients.store') }}" method="POST">
                    @csrf
                    <div class="modal-body !p-5">
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Client Name *</label>
                            <input type="text" name="name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Company</label>
                            <input type="text" name="company" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Email</label>
                                <input type="email" name="email" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Phone</label>
                                <input type="text" name="phone" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Address</label>
                            <textarea name="address" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-t border-slate-200 !p-5 bg-slate-50">
                        <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 border border-transparent text-white text-sm font-medium hover:bg-teal-700 transition-colors">Save Client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
