<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Item Catalog</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v={{ filemtime(public_path('css/admin.css')) }}">
    @endpush

    <div class="admin-catalog-page catalog-page">
        <!-- Breadcrumb Navigation -->
        <nav class="adm-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
            <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
            <span class="breadcrumb-current">Item Catalog</span>
        </nav>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="border-radius: var(--adm-radius-sm); border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534;">
                <i class="fa-solid fa-circle-check me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="border-radius: var(--adm-radius-sm); border: 1px solid #fecaca; background: #fef2f2; color: #991b1b;">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Page Header -->
        <header class="adm-header">
            <div class="adm-header-title-wrap">
                <h1>Item Catalog</h1>
                <p>Manage survey reference data, operational disciplines, and standard unit rates across project modules.</p>
            </div>
            <div class="adm-header-actions">
                <span class="adm-date-pill">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->format('d M Y') }}
                </span>
                <a href="{{ route('admin.equipment.index') }}" class="adm-btn adm-btn--secondary">
                    <i class="fa-solid fa-drone"></i>
                    <span>Drone Equipment</span>
                </a>
                <a href="{{ route('newItem') }}" class="adm-btn adm-btn--primary" id="createButton">
                    <i class="fa-solid fa-plus"></i>
                    <span>Create New Item</span>
                </a>
            </div>
        </header>

        @php
            $totalModules = $dataGroups->count();
            $totalItems = $dataGroups->sum(fn ($group) => count($group['items'] ?? []));
            $totalUnits = $units->count();
        @endphp

        <!-- KPI Metrics Grid -->
        <section class="adm-kpi-grid" aria-label="Catalog summary metrics">
            <!-- 1. Modules -->
            <article class="adm-kpi-tile">
                <div class="adm-kpi-icon">
                    <i class="fa-solid fa-cubes-stacked"></i>
                </div>
                <div class="adm-kpi-content">
                    <span class="adm-kpi-label">Modules & Disciplines</span>
                    <span class="adm-kpi-value">{{ $totalModules }}</span>
                    <span class="adm-kpi-subtext">Active survey disciplines</span>
                </div>
            </article>

            <!-- 2. Catalog Items -->
            <article class="adm-kpi-tile">
                <div class="adm-kpi-icon adm-kpi-icon--emerald">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div class="adm-kpi-content">
                    <span class="adm-kpi-label">Catalog Items</span>
                    <span class="adm-kpi-value">{{ $totalItems }}</span>
                    <span class="adm-kpi-subtext">Reference quotation items</span>
                </div>
            </article>

            <!-- 3. Measurement Units -->
            <article class="adm-kpi-tile">
                <div class="adm-kpi-icon adm-kpi-icon--teal">
                    <i class="fa-solid fa-ruler-combined"></i>
                </div>
                <div class="adm-kpi-content">
                    <span class="adm-kpi-label">Measurement Units</span>
                    <span class="adm-kpi-value">{{ $totalUnits }}</span>
                    <span class="adm-kpi-subtext">Standardized billing units</span>
                </div>
            </article>

            <!-- 4. Real-time Status -->
            <article class="adm-kpi-tile">
                <div class="adm-kpi-icon adm-kpi-icon--amber">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div class="adm-kpi-content">
                    <span class="adm-kpi-label">Quotation Sync</span>
                    <span class="adm-kpi-value">Live</span>
                    <span class="adm-kpi-subtext">Instant cost estimation feed</span>
                </div>
            </article>
        </section>

        <!-- Search & Filter Toolbar -->
        <div class="adm-filter-toolbar">
            <div class="adm-search-box">
                <i class="fa-solid fa-magnifying-glass adm-search-icon"></i>
                <input type="search"
                       id="searchInput"
                       class="adm-search-input"
                       placeholder="Search item, category, service, internal rate, or remark..."
                       autocomplete="off">
            </div>

            <div class="adm-select-box">
                <select id="moduleFilter" class="adm-select-input" aria-label="Filter items by module">
                    <option value="">All Modules</option>
                    @foreach($dataGroups->keys() as $moduleName)
                        <option value="{{ strtolower($moduleName) }}">{{ $moduleName }}</option>
                    @endforeach
                </select>
            </div>

            <div class="adm-filter-stats">
                <span id="catalogResultCount" class="adm-result-badge" aria-live="polite">
                    {{ $totalItems }} {{ $totalItems === 1 ? 'item' : 'items' }}
                </span>
                <button type="button" id="clearCatalogFilters" class="adm-clear-btn" title="Reset search filters">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset</span>
                </button>
            </div>
        </div>

        <!-- No Matching Items State -->
        <div id="catalogNoResults" class="adm-module-card" style="display: none;">
            <div class="adm-empty-state">
                <div class="adm-empty-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                <h3 class="adm-empty-title">No matching catalog items</h3>
                <p class="adm-empty-text">No items matched your search query or selected module filter. Try adjusting your keywords.</p>
                <button type="button" onclick="document.getElementById('clearCatalogFilters')?.click()" class="adm-btn adm-btn--primary" style="margin-top: 0.5rem;">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset Filters</span>
                </button>
            </div>
        </div>

        <!-- Module Groups -->
        @forelse ($dataGroups as $groupName => $group)
            @include('dashboard.module-item', [
                'title' => $groupName,
                'items' => $group['items'] ?? $group,
                'categoryId' => $group['id'] ?? null
            ])
        @empty
            <div class="adm-module-card">
                <div class="adm-empty-state">
                    <div class="adm-empty-icon"><i class="fa-regular fa-folder-open"></i></div>
                    <h3 class="adm-empty-title">No item groups found</h3>
                    <p class="adm-empty-text">No modules or survey catalog items have been defined yet.</p>
                    <a href="{{ route('newItem') }}" class="adm-btn adm-btn--primary" style="margin-top: 0.5rem;">
                        <i class="fa-solid fa-plus"></i>
                        <span>Create First Item</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <script>
        window.adminModulesTree = @json($adminModulesTree);
    </script>
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const clearBtn = document.getElementById('clearCatalogFilters');
            const searchInput = document.getElementById('searchInput');
            const moduleFilter = document.getElementById('moduleFilter');

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    if (searchInput) searchInput.value = '';
                    if (moduleFilter) moduleFilter.value = '';
                    searchInput?.dispatchEvent(new Event('input'));
                });
            }

            // SweetAlert2 Item Deletion
            document.querySelectorAll('.btn-delete-item').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const itemName = this.getAttribute('data-name') || 'this item';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Delete Item?',
                            text: `Are you sure you want to delete "${itemName}"? This item will no longer appear in new quotation estimates.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Yes, delete item',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else if (confirm(`Are you sure you want to delete "${itemName}"?`)) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>