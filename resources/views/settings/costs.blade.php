<x-app-layout containerClass="w-full px-8 py-8">
    
    <!-- 1. PAGE HEADER -->
    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 mb-1 tracking-tight">Master Cost Rates</h1>
            <div class="text-sm font-medium text-slate-500">Manage default cost items for different survey types.</div>
        </div>
        <div>
            <button data-bs-toggle="modal" data-bs-target="#addRateModal" class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 text-sm font-medium transition-colors border border-transparent shadow-sm">
                <i class="fa-solid fa-plus font-light"></i> Add New Rate
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-8 flex items-start gap-3 text-sm">
            <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- 2. COST RATES TABLE WORKSPACE -->
    <div class="bg-white border border-slate-200 mb-12 shadow-sm">
        
        <!-- Table Controls -->
        <div class="px-5 py-3 border-b border-slate-200 flex flex-wrap justify-between items-center gap-4 bg-slate-50">
            <h3 class="text-sm font-bold text-slate-800">SBES Master Rates</h3>
        </div>

        <!-- Table Data (Dense) -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-500">
                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] w-[15%]">Category</th>
                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] w-[35%]">Item Name</th>
                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] w-[15%]">Unit Type</th>
                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] w-[15%]">Base Multiplier</th>
                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] w-[15%]">Default Rate (RM)</th>
                        <th class="px-5 py-2.5 font-bold uppercase tracking-wider text-[10px] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rates as $rate)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-5 py-3 align-middle">
                                @php
                                    $badgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
                                    if($rate->category == 'Equipment') $badgeClass = 'bg-sky-50 text-sky-700 border-sky-100';
                                    elseif($rate->category == 'Personnel') $badgeClass = 'bg-amber-50 text-amber-700 border-amber-100';
                                    elseif($rate->category == 'Logistics') $badgeClass = 'bg-purple-50 text-purple-700 border-purple-100';
                                    elseif($rate->category == 'Analysis') $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                @endphp
                                <span class="px-2 py-0.5 rounded border text-[11px] font-semibold uppercase tracking-wide {{ $badgeClass }}">
                                    {{ $rate->category }}
                                </span>
                            </td>
                            <td class="px-5 py-3 align-middle font-semibold text-slate-800">
                                {{ $rate->name }}
                            </td>
                            <td class="px-5 py-3 align-middle">
                                <span class="bg-slate-50 border border-slate-200 text-slate-600 px-2.5 py-0.5 rounded text-xs font-medium">
                                    {{ $rate->unit_type }}
                                </span>
                            </td>
                            <td class="px-5 py-3 align-middle">
                                @if($rate->unit_type == 'Per Day')
                                    <div class="text-slate-500 text-xs font-semibold flex items-center gap-1.5">
                                        <i class="fa-solid fa-xmark opacity-70"></i> {{ $rate->base_multiplier }}
                                    </div>
                                @else
                                    <span class="text-slate-400 font-medium">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 align-middle font-bold text-slate-900">
                                {{ number_format($rate->default_rate, 2) }}
                            </td>
                            <td class="px-5 py-3 align-middle text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-all" title="Edit Rate" data-bs-toggle="modal" data-bs-target="#editRateModal{{ $rate->id }}">
                                        <i class="fa-solid fa-pen font-light text-sm"></i>
                                    </button>
                                    <form action="{{ route('settings.costs.destroy', $rate->id) }}" method="POST" class="inline-block m-0 form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all btn-delete-action" title="Delete Rate">
                                            <i class="fa-solid fa-trash-can font-light text-sm"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Edit Rate Modal -->
                                <div class="modal fade text-left" id="editRateModal{{ $rate->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content !border-slate-200 !rounded-none !shadow-lg whitespace-normal">
                                            <form action="{{ route('settings.costs.update', $rate->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header border-b border-slate-200 !p-5">
                                                    <h5 class="modal-title font-bold text-slate-900 tracking-tight text-lg">Edit Cost Rate</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body !p-5">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div class="md:col-span-2 mb-2">
                                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Category</label>
                                                            <select name="category" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" required>
                                                                <option value="Equipment" {{ $rate->category == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                                                                <option value="Personnel" {{ $rate->category == 'Personnel' ? 'selected' : '' }}>Personnel</option>
                                                                <option value="Logistics" {{ $rate->category == 'Logistics' ? 'selected' : '' }}>Logistics</option>
                                                                <option value="Analysis" {{ $rate->category == 'Analysis' ? 'selected' : '' }}>Analysis & Reporting</option>
                                                                <option value="Miscellaneous" {{ $rate->category == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                                                            </select>
                                                        </div>
                                                        <div class="md:col-span-2 mb-2">
                                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Item Name</label>
                                                            <input type="text" name="name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" required value="{{ $rate->name }}">
                                                        </div>
                                                        <div class="mb-2">
                                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Unit Type</label>
                                                            <select name="unit_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" required onchange="toggleMultiplierEdit(this, {{ $rate->id }})">
                                                                <option value="Per Day" {{ $rate->unit_type == 'Per Day' ? 'selected' : '' }}>Per Day</option>
                                                                <option value="Lump Sum" {{ $rate->unit_type == 'Lump Sum' ? 'selected' : '' }}>Lump Sum</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-2 multiplier-group-edit-{{ $rate->id }}" style="display: {{ $rate->unit_type == 'Per Day' ? 'block' : 'none' }};">
                                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Base Multiplier</label>
                                                            <select name="base_multiplier" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                                                                <option value="Total Duration" {{ $rate->base_multiplier == 'Total Duration' ? 'selected' : '' }}>Total Duration</option>
                                                                <option value="Execution Days" {{ $rate->base_multiplier == 'Execution Days' ? 'selected' : '' }}>Execution Days</option>
                                                                <option value="MOB/DEMOB Days" {{ $rate->base_multiplier == 'MOB/DEMOB Days' ? 'selected' : '' }}>MOB/DEMOB Days</option>
                                                                <option value="Weather Days" {{ $rate->base_multiplier == 'Weather Days' ? 'selected' : '' }}>Weather Days</option>
                                                            </select>
                                                        </div>
                                                        <div class="md:col-span-2 mb-2">
                                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Default Rate (RM)</label>
                                                            <input type="number" step="0.01" name="default_rate" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" required value="{{ $rate->default_rate }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-t border-slate-200 !p-5 bg-slate-50">
                                                    <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="px-4 py-2 bg-teal-600 border border-transparent text-white text-sm font-medium hover:bg-teal-700 transition-colors">Update Rate</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 bg-white">
                                <i class="fa-solid fa-list-ul text-3xl text-slate-200 mb-3 block"></i>
                                <div class="text-slate-500 font-medium">No rates configured for SBES.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Rate Modal -->
    <div class="modal fade" id="addRateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content !border-slate-200 !rounded-none !shadow-lg">
                <form action="{{ route('settings.costs.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-b border-slate-200 !p-5">
                        <h5 class="modal-title font-bold text-slate-900 tracking-tight text-lg">Add Cost Rate</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body !p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2 mb-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Category</label>
                                <select name="category" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" required>
                                    <option value="Equipment">Equipment</option>
                                    <option value="Personnel">Personnel</option>
                                    <option value="Logistics">Logistics</option>
                                    <option value="Analysis">Analysis & Reporting</option>
                                    <option value="Miscellaneous">Miscellaneous</option>
                                </select>
                            </div>
                            <div class="md:col-span-2 mb-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Item Name</label>
                                <input type="text" name="name" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" required placeholder="e.g. Survey Boat">
                            </div>
                            <div class="mb-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Unit Type</label>
                                <select name="unit_type" id="add_unit_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" required onchange="toggleMultiplierAdd(this)">
                                    <option value="Per Day">Per Day</option>
                                    <option value="Lump Sum">Lump Sum</option>
                                </select>
                            </div>
                            <div class="mb-2" id="add_multiplier_group">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Base Multiplier</label>
                                <select name="base_multiplier" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500">
                                    <option value="Total Duration">Total Duration</option>
                                    <option value="Execution Days">Execution Days</option>
                                    <option value="MOB/DEMOB Days">MOB/DEMOB Days</option>
                                    <option value="Weather Days">Weather Days</option>
                                </select>
                            </div>
                            <div class="md:col-span-2 mb-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Default Rate (RM)</label>
                                <input type="number" step="0.01" name="default_rate" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 text-sm focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500" required placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-t border-slate-200 !p-5 bg-slate-50">
                        <button type="button" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-teal-600 border border-transparent text-white text-sm font-medium hover:bg-teal-700 transition-colors">Save Rate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function toggleMultiplierEdit(selectElement, id) {
            const group = document.querySelector('.multiplier-group-edit-' + id);
            if(group) {
                group.style.display = selectElement.value === 'Per Day' ? 'block' : 'none';
            }
        }

        function toggleMultiplierAdd(selectElement) {
            const group = document.getElementById('add_multiplier_group');
            if(group) {
                group.style.display = selectElement.value === 'Per Day' ? 'block' : 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete-action');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#f8fafc',
                        customClass: {
                            cancelButton: 'text-slate-800 border-none shadow-sm',
                            confirmButton: 'text-white'
                        },
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
