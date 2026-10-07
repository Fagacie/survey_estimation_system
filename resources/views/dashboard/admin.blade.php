<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Admin - Data Management</x-slot>

    <!-- Bootstrap & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/module-item.css') }}?v={{ filemtime(public_path('css/module-item.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/modelling-catalog.css') }}?v={{ filemtime(public_path('css/modelling-catalog.css')) }}">

    <!-- MAIN WRAPPER TO CENTER CONTENT -->
    <div class="admin-container catalog-page">

        <!-- PAGE TITLE & BUTTON -->
        <div class="page-header-wrapper">
            <div>
                <div class="catalog-kicker">Reference data</div>
                <h2 class="page-header">
                <i class="bi bi-clipboard-data"></i>
                    Modelling Catalog
                </h2>
                <p class="catalog-subtitle">Manage reusable survey services, categories, and internal rates.</p>
            </div>

            <!-- CREATE NEW ITEM BUTTON -->
            <a href="{{ route('newItem') }}">
                <button type="button" class="create-button" id="createButton">
                    <i class="fa-solid fa-plus"></i> CREATE NEW
                </button>
            </a>
        </div>
        
        <!-- SEARCH & FILTER -->
        <div class="bg-white border border-slate-200 rounded-lg p-4 mb-8 shadow-sm flex flex-col md:flex-row gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 w-full relative">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Search Items</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-slate-400 text-sm"></i>
                    </div>
                    <input type="text" id="searchInput" class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-md text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow shadow-sm" placeholder="Search by name, rate, or ID...">
                </div>
            </div>

            <!-- Filter by Module -->
            <div class="w-full md:w-64 relative">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Filter by Module</label>
                <div class="relative">
                    <select id="moduleFilter" class="w-full pl-3 pr-8 py-2 appearance-none bg-white border border-slate-200 rounded-md text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow shadow-sm cursor-pointer">
                        <option value="">All Modules</option>
                        @foreach($dataGroups->keys() as $moduleName)
                            <option value="{{ strtolower($moduleName) }}">{{ $moduleName }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-chevron-down text-slate-400 text-[10px]"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODULE ITEMS TABLE GROUPS -->
        @forelse ($dataGroups as $groupName => $group)
            @include('dashboard.module-item', [
                'title' => $groupName,
                'items' => $group['items'] ?? $group,
                'categoryId' => $group['id'] ?? null
            ])
        @empty
            <section class="data-group">
                <div class="table-card" style="padding: 20px; text-align: center;">
                    <p>No data groups found.</p>
                </div>
            </section>
        @endforelse          

    </div> <!-- END admin-container -->
    
    <script>
        window.adminModulesTree = @json($adminModulesTree);
    </script>
    @push('scripts')
    <!-- Bootstrap JS & Admin JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    @endpush
</x-app-layout>