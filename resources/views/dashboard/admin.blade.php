<x-app-layout>
    <x-slot name="header">Admin - Data Management</x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
        <link rel="stylesheet" href="{{ asset('css/module-item.css') }}?v={{ filemtime(public_path('css/module-item.css')) }}">
        <link rel="stylesheet" href="{{ asset('css/modelling-catalog.css') }}?v={{ filemtime(public_path('css/modelling-catalog.css')) }}">
    @endpush

    <!-- MAIN WRAPPER TO CENTER CONTENT -->
    <div class="admin-container catalog-page">

        <!-- PAGE TITLE & BUTTON -->
        <div class="page-header-wrapper">
            <div>
                <div class="catalog-kicker">Reference data</div>
                <h2 class="page-header">
                    <i class="fa-solid fa-boxes-stacked" aria-hidden="true"></i>
                    Item Catalog
                </h2>
                <p class="catalog-subtitle">Manage the survey reference data used throughout project planning and cost estimation.</p>
            </div>

            <!-- CREATE NEW ITEM BUTTON -->
            <a href="{{ route('newItem') }}" class="create-button" id="createButton">
                <i class="fa-solid fa-plus" aria-hidden="true"></i> Create new item
            </a>
        </div>
        
        <section class="catalog-summary-grid" aria-label="Item catalogue summary">
            <article class="catalog-summary-card">
                <span class="catalog-summary-icon"><i class="fa-solid fa-cubes-stacked" aria-hidden="true"></i></span>
                <div><span>Modules</span><strong>{{ $dataGroups->count() }}</strong><small>Reference-data groups</small></div>
            </article>
            <article class="catalog-summary-card">
                <span class="catalog-summary-icon catalog-summary-icon-blue"><i class="fa-solid fa-list-check" aria-hidden="true"></i></span>
                <div><span>Catalogue items</span><strong>{{ $dataGroups->sum(fn ($group) => count($group['items'] ?? [])) }}</strong><small>Available survey items</small></div>
            </article>
            <article class="catalog-summary-card">
                <span class="catalog-summary-icon catalog-summary-icon-violet"><i class="fa-solid fa-ruler-combined" aria-hidden="true"></i></span>
                <div><span>Units</span><strong>{{ $units->count() }}</strong><small>Measurement units defined</small></div>
            </article>
        </section>

        <!-- SEARCH & FILTER -->
        <div class="catalog-filter-panel bg-white border border-slate-200 rounded-lg p-4 mb-8 shadow-sm flex flex-col md:flex-row gap-4 items-end">
            <!-- Search -->
            <div class="flex-1 w-full relative">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Search Items</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-search text-slate-400 text-sm"></i>
                    </div>
                    <input type="search" id="searchInput" autocomplete="off" class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-md text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-shadow shadow-sm" placeholder="Search item, category, service, rate, or remark...">
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

        <div class="catalog-results-heading"><div><h2>Reference data</h2><p>Browse items grouped by survey discipline.</p></div><span id="catalogResultCount">{{ $dataGroups->sum(fn ($group) => count($group['items'] ?? [])) }} items</span></div>
        <div id="catalogNoResults" class="catalog-no-results" hidden><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i><strong>No matching items</strong><span>Try a different search term or module filter.</span></div>

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