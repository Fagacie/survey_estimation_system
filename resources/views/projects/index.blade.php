<x-app-layout containerClass="w-full px-8 py-8">
    
    <!-- 1. PAGE HEADER -->
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-5 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight flex items-center gap-3">
                Project Control Room 
                <span class="text-[10px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full border border-slate-200 tracking-widest uppercase font-semibold">{{ now()->format('d M Y') }}</span>
            </h1>
            <div class="text-xs font-medium text-slate-500 mt-1">Manage survey planning, mapping data, and quotations.</div>
        </div>
        <div>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-xs font-semibold transition-colors shadow-sm">
                <i class="fa-solid fa-plus"></i> New Project
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-8 flex items-start gap-3 text-sm">
            <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <!-- 2. CORE METRICS (Ultra-clean and minimal) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col justify-center relative overflow-hidden group">
            <div class="text-[11px] font-bold tracking-wider text-gray-500 uppercase mb-1">Total Projects</div>
            <div class="text-3xl font-black text-gray-900 leading-none">{{ $metrics['total'] }}</div>
            <i class="fa-solid fa-layer-group absolute right-5 top-1/2 -translate-y-1/2 text-4xl text-slate-100 group-hover:scale-110 transition-transform"></i>
        </div>
        
        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col justify-center relative overflow-hidden group">
            <div class="text-[11px] font-bold tracking-wider text-gray-500 uppercase mb-1">Drafts</div>
            <div class="text-3xl font-black text-gray-900 leading-none">{{ $metrics['draft'] }}</div>
            <i class="fa-solid fa-pen-ruler absolute right-5 top-1/2 -translate-y-1/2 text-4xl text-slate-100 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col justify-center relative overflow-hidden group">
            <div class="text-[11px] font-bold tracking-wider text-gray-500 uppercase mb-1">Mapped Areas</div>
            <div class="text-3xl font-black text-gray-900 leading-none">{{ $metrics['mapped'] }}</div>
            <i class="fa-solid fa-map-location-dot absolute right-5 top-1/2 -translate-y-1/2 text-4xl text-slate-100 group-hover:scale-110 transition-transform"></i>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm flex flex-col justify-center relative overflow-hidden group">
            <div class="text-[11px] font-bold tracking-wider text-gray-500 uppercase mb-1">Quotations</div>
            <div class="text-3xl font-black text-gray-900 leading-none">{{ $metrics['quotations'] }}</div>
            <i class="fa-solid fa-file-invoice-dollar absolute right-5 top-1/2 -translate-y-1/2 text-4xl text-slate-100 group-hover:scale-110 transition-transform"></i>
        </div>
    </div>

    @if($metrics['total'] > 0)
        <!-- 3. ATTENTION REQUIRED (Operational Alerts - Scannable text list) -->
        @if($attention['missing_boundaries'] > 0 || $attention['missing_lines'] > 0 || $attention['missing_parameters'] > 0 || $attention['missing_cost'] > 0)
            <div class="bg-white border border-gray-200 rounded-lg mb-8 shadow-sm">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-sm"></i>
                    <h3 class="text-sm font-bold text-slate-800">Action Required</h3>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-slate-100 m-0">
                        @if($attention['missing_boundaries'] > 0)
                            <li class="px-5 py-2.5 flex justify-between items-center text-sm hover:bg-slate-50 transition-colors">
                                <span class="text-slate-600">Projects missing boundaries</span>
                                <span class="font-bold text-slate-900">{{ $attention['missing_boundaries'] }}</span>
                            </li>
                        @endif
                        @if($attention['missing_lines'] > 0)
                            <li class="px-5 py-2.5 flex justify-between items-center text-sm hover:bg-slate-50 transition-colors">
                                <span class="text-slate-600">Projects pending survey line generation</span>
                                <span class="font-bold text-slate-900">{{ $attention['missing_lines'] }}</span>
                            </li>
                        @endif
                        @if($attention['missing_parameters'] > 0)
                            <li class="px-5 py-2.5 flex justify-between items-center text-sm hover:bg-slate-50 transition-colors">
                                <span class="text-slate-600">Projects with missing survey parameters</span>
                                <span class="font-bold text-slate-900">{{ $attention['missing_parameters'] }}</span>
                            </li>
                        @endif
                        @if($attention['missing_cost'] > 0)
                            <li class="px-5 py-2.5 flex justify-between items-center text-sm hover:bg-slate-50 transition-colors">
                                <span class="text-slate-600">Projects awaiting final cost estimation</span>
                                <span class="font-bold text-slate-900">{{ $attention['missing_cost'] }}</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        @endif

        <!-- 4. PROJECTS TABLE WORKSPACE -->
        <div class="bg-white border border-gray-200 rounded-lg mb-12 shadow-sm">
            
            <!-- Table Controls -->
            <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap justify-between items-center gap-4">
                <div><h3 class="text-base font-bold text-slate-800 mb-1">Survey portfolio</h3><p class="text-xs text-slate-500 m-0">Latest projects and their current handoff point</p></div>
                
                <form method="GET" action="{{ route('projects.index') }}" class="flex flex-wrap gap-3 w-full md:w-auto">
                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-search text-slate-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" class="w-full md:w-64 pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-md text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow shadow-sm" placeholder="Search projects..." value="{{ request('search') }}">
                    </div>
                    
                    <!-- Status Filter -->
                    <div class="relative">
                        <select name="status" class="w-full md:w-40 pl-3 pr-8 py-2 appearance-none bg-white border border-slate-200 rounded-md text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow shadow-sm cursor-pointer" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-chevron-down text-slate-400 text-[10px]"></i>
                        </div>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="relative">
                        <select name="date_range" class="w-full md:w-40 pl-3 pr-8 py-2 appearance-none bg-white border border-slate-200 rounded-md text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow shadow-sm cursor-pointer" onchange="this.form.submit()">
                            <option value="">All Time</option>
                            <option value="last_30_days" {{ request('date_range') === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="this_month" {{ request('date_range') === 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="this_year" {{ request('date_range') === 'this_year' ? 'selected' : '' }}>This Year</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fa-regular fa-calendar text-slate-400 text-[11px]"></i>
                        </div>
                    </div>

                    @if(request('search') || request('status') || request('date_range'))
                        <a href="{{ route('projects.index') }}" class="px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 rounded-md text-sm font-medium transition-colors flex items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-xmark text-xs"></i> Clear
                        </a>
                    @endif
                    <button type="submit" class="hidden">Filter</button>
                </form>
            </div>

            <!-- Table Data (Dense) -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-500">
                            <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Code</th>
                            <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Project Details</th>
                            <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Client</th>
                            <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Status</th>
                            <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Updated</th>
                            <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($projects as $project)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td class="px-5 py-3 align-middle">
                                    <span class="font-mono text-slate-500 text-xs">
                                        {{ $project->number ?? 'PRJ-' . str_pad($project->project_Id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 align-middle">
                                    <div class="font-semibold text-slate-800">{{ $project->name }}</div>
                                    <div class="text-[11px] text-slate-500 flex items-center mt-0.5">
                                        <i class="fa-regular fa-clock mr-1 opacity-70"></i> {{ $project->period ?? 'Unspecified' }}
                                    </div>
                                </td>
                                <td class="px-5 py-3 align-middle">
                                    <span class="text-slate-700 font-medium text-xs">{{ $project->client?->company_name ?? '-' }}</span>
                                </td>
                                <td class="px-5 py-3 align-middle">
                                    @if($project->status === 'draft')
                                        <div class="flex items-center gap-1.5 text-slate-600 text-xs font-medium">
                                            <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div> Draft
                                        </div>
                                    @elseif($project->status === 'planned')
                                        <div class="flex items-center gap-1.5 text-blue-700 text-xs font-medium">
                                            <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div> In Progress
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1.5 text-emerald-700 text-xs font-medium">
                                            <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> Completed
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-3 align-middle text-slate-500 text-xs font-medium">
                                    {{ $project->updated_at->format('Y-m-d') }}
                                </td>
                                <td class="px-5 py-3 align-middle text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('projects.show', $project->project_Id) }}" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all" title="View">
                                            <i class="fa-solid fa-arrow-right font-light"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project->project_Id) }}" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-800 hover:bg-slate-100 transition-all" title="Edit">
                                            <i class="fa-solid fa-pen font-light text-sm"></i>
                                        </a>
                                        <form action="{{ route('projects.destroy', $project->project_Id) }}" method="POST" class="inline-block form-delete m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-red-600 hover:bg-red-50 transition-all btn-delete-action" title="Delete">
                                                <i class="fa-solid fa-trash-can font-light text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10 bg-white">
                                    <div class="text-slate-500 mb-2 font-medium">No projects found.</div>
                                    <a href="{{ route('projects.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">Clear Filters</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($projects->hasPages())
                <div class="px-5 py-3 border-t border-slate-200 bg-slate-50 flex justify-center">
                    {{ $projects->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

    @else
        <!-- Empty State -->
        <div class="text-center py-20 bg-white border border-slate-200 mt-8 shadow-sm">
            <i class="fa-solid fa-layer-group text-3xl text-slate-300 mb-4 font-light"></i>
            <h3 class="text-lg font-bold text-slate-800 mb-2">No projects yet</h3>
            <p class="text-slate-500 mb-6 mx-auto max-w-md text-sm">
                Create your first survey estimation project to begin tracking boundaries, lines, and costs.
            </p>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-2 bg-[#0052cc] hover:bg-blue-700 text-white px-6 py-2.5 text-sm font-medium transition-colors border border-transparent shadow-sm">
                <i class="fa-solid fa-plus font-light"></i> Create Project
            </a>
        </div>
    @endif

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete-action');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Delete this project?',
                        text: "All map data and cost estimations will be permanently removed.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#f8fafc',
                        customClass: {
                            cancelButton: 'text-slate-800 border-none shadow-sm',
                            confirmButton: 'text-white'
                        },
                        confirmButtonText: 'Yes, delete it'
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
