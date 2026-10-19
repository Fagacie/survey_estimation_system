<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Quotation History</x-slot>

    <link rel="stylesheet" href="{{ asset('css/quotation-history.css') }}?v={{ filemtime(public_path('css/quotation-history.css')) }}">

    <div class="quotation-history-page">
        <!-- Breadcrumb Navigation -->
        <nav class="qh-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
            <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
            <span class="breadcrumb-current">Quotation History</span>
        </nav>

        <!-- Flash Success Notification -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert" style="border-radius: var(--qh-radius-sm); border: 1px solid #bbf7d0; background: #f0fdf4; color: #166534;">
                <i class="fa-solid fa-circle-check me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Page Header -->
        <header class="qh-header">
            <div class="qh-header-title-wrap">
                <h1>Quotation History</h1>
                <p>Review commercial estimations, verify project scopes, and generate client invoices.</p>
            </div>
            <div class="qh-header-actions">
                <span class="qh-date-pill">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->format('d M Y') }}
                </span>
                <a href="{{ route('quotation.index') }}" class="qh-btn qh-btn--primary">
                    <i class="fa-solid fa-plus"></i>
                    <span>New Quotation</span>
                </a>
            </div>
        </header>

        @php
            $totalCount = $quotations->count();
            $totalValue = (float) $quotations->sum('grand_total');
            $avgValue = $totalCount > 0 ? ($totalValue / $totalCount) : 0.0;
            $recentCount = $quotations->filter(function($q) {
                return $q->created_at && \Carbon\Carbon::parse($q->created_at)->gte(now()->subDays(30));
            })->count();
        @endphp

        <!-- KPI Summary Metric Tiles -->
        <section class="qh-kpi-grid" aria-label="Commercial quotation metrics">
            <!-- 1. Total Quotations -->
            <article class="qh-kpi-tile">
                <div class="qh-kpi-icon">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                <div class="qh-kpi-content">
                    <span class="qh-kpi-label">Total Quotations</span>
                    <span class="qh-kpi-value">{{ $totalCount }}</span>
                    <span class="qh-kpi-subtext">Saved estimation records</span>
                </div>
            </article>

            <!-- 2. Combined Value -->
            <article class="qh-kpi-tile">
                <div class="qh-kpi-icon qh-kpi-icon--emerald">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <div class="qh-kpi-content">
                    <span class="qh-kpi-label">Combined Value</span>
                    <span class="qh-kpi-value">RM {{ number_format($totalValue, 2) }}</span>
                    <span class="qh-kpi-subtext">Total active estimates</span>
                </div>
            </article>

            <!-- 3. Average Quotation Value -->
            <article class="qh-kpi-tile">
                <div class="qh-kpi-icon qh-kpi-icon--teal">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <div class="qh-kpi-content">
                    <span class="qh-kpi-label">Average Value</span>
                    <span class="qh-kpi-value">RM {{ number_format($avgValue, 2) }}</span>
                    <span class="qh-kpi-subtext">Per quotation estimate</span>
                </div>
            </article>

            <!-- 4. Recent Quotations -->
            <article class="qh-kpi-tile">
                <div class="qh-kpi-icon qh-kpi-icon--amber">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div class="qh-kpi-content">
                    <span class="qh-kpi-label">Recent (30 Days)</span>
                    <span class="qh-kpi-value">{{ $recentCount }}</span>
                    <span class="qh-kpi-subtext">Estimates drafted this month</span>
                </div>
            </article>
        </section>

        <!-- Quotation Register Card -->
        <div class="qh-register-card">
            <!-- Filter Toolbar -->
            <div class="qh-filter-toolbar">
                <!-- Search Input -->
                <div class="qh-search-box">
                    <i class="fa-solid fa-magnifying-glass qh-search-icon"></i>
                    <input type="text"
                           id="searchHistoryInput"
                           class="qh-search-input"
                           placeholder="Search quotation code, project title, or client..."
                           autocomplete="off">
                </div>

                <!-- Project Type Dropdown -->
                <div class="qh-select-box">
                    <select id="typeFilter" class="qh-select-input" aria-label="Filter by project type">
                        <option value="">All Project Types</option>
                        <option value="CP">CP - Coastal Project</option>
                        <option value="RP">RP - River Project</option>
                        <option value="MP">MP - Maritime Project</option>
                        <option value="GP">GP - Geotech Project</option>
                        <option value="JP">JP - Jetty Project</option>
                    </select>
                </div>

                <!-- Location Dropdown -->
                <div class="qh-select-box">
                    <select id="locationFilter" class="qh-select-input" aria-label="Filter by location">
                        <option value="">All Locations</option>
                        @foreach(\App\Models\Project::LOCATIONS as $loc)
                            <option value="{{ $loc }}">{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Month Dropdown -->
                <div class="qh-select-box">
                    <select id="monthFilter" class="qh-select-input" aria-label="Filter by month">
                        <option value="">All Months</option>
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                </div>

                <!-- Clear Filters Button -->
                <button type="button" id="clearHistoryFilters" class="qh-clear-btn" title="Reset all search filters">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset</span>
                </button>
            </div>

            <!-- Register Table Header Bar -->
            <div class="qh-register-header">
                <div class="qh-register-title-wrap">
                    <h2>Quotation Register</h2>
                    <span id="historyResultsCount" class="qh-result-count" aria-live="polite">
                        {{ $totalCount }} {{ $totalCount === 1 ? 'record' : 'records' }}
                    </span>
                </div>
            </div>

            <!-- Five-Column Data Table -->
            <div class="qh-table-wrap">
                <table class="qh-table" id="quotationHistoryTable">
                    <caption class="sr-only">Saved survey estimation quotations register</caption>
                    <thead>
                        <tr>
                            <th scope="col" style="width: 170px;">Quotation Code</th>
                            <th scope="col">Project Details</th>
                            <th scope="col" style="width: 230px;">Client Entity</th>
                            <th scope="col" class="text-end" style="width: 180px;">Total Value</th>
                            <th scope="col" class="text-end" style="width: 170px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="quotationTableBody">
                        @forelse($quotations as $item)
                            @php
                                $createdDate = $item->created_at ? \Carbon\Carbon::parse($item->created_at) : null;
                                $formattedDate = $createdDate ? $createdDate->format('Y-m-d') : '';
                                $displayDate = $createdDate ? $createdDate->format('d M Y') : 'N/A';
                                $relativeDate = $createdDate ? $createdDate->diffForHumans() : 'Recently';
                                $monthVal = $createdDate ? $createdDate->format('m') : '';
                                $codeVal = $item->project?->number ?? $item->quotation_no ?? ('QTN-' . str_pad($item->quotation_Id, 4, '0', STR_PAD_LEFT));
                                $projectName = $item->project?->name ?? $item->project?->project_name ?? 'Untitled Project';
                                $locationVal = $item->project?->location ?? '';
                                $projectType = $item->project?->project_type ?? '';
                                $clientName = $item->project?->client?->company_name ?? $item->project?->client?->name ?? 'Unassigned Client';
                                $clientInitials = collect(preg_split('/\s+/', $clientName))->filter()->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('') ?: 'CL';
                                $searchIndex = strtolower($codeVal . ' ' . $projectName . ' ' . $clientName . ' ' . $locationVal . ' ' . $projectType);
                            @endphp
                            <tr class="quotation-row"
                                data-code="{{ strtolower($codeVal) }}"
                                data-date="{{ $formattedDate }}"
                                data-month="{{ $monthVal }}"
                                data-type="{{ strtolower($projectType) }}"
                                data-location="{{ strtolower($locationVal) }}"
                                data-search="{{ $searchIndex }}">

                                <!-- Column 1: Quotation / Project Code -->
                                <td>
                                    <span class="qh-code-pill">
                                        <i class="fa-solid fa-hashtag text-slate-400"></i>
                                        {{ $codeVal }}
                                    </span>
                                </td>

                                <!-- Column 2: Project Details & Embedded Metadata (Created Date & Location) -->
                                <td>
                                    <div class="qh-project-name">
                                        {{ $projectName }}
                                    </div>
                                    <div class="qh-project-meta">
                                        <span class="qh-meta-item" title="Date created">
                                            <i class="fa-regular fa-calendar"></i>
                                            {{ $displayDate }}
                                        </span>
                                        <span class="qh-meta-divider">•</span>
                                        <span class="qh-meta-item" title="Time elapsed">
                                            <i class="fa-regular fa-clock"></i>
                                            {{ $relativeDate }}
                                        </span>
                                        @if($locationVal)
                                            <span class="qh-meta-divider">•</span>
                                            <span class="qh-meta-item" title="Project site location">
                                                <i class="fa-solid fa-location-dot text-rose-500"></i>
                                                {{ $locationVal }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Column 3: Client Entity -->
                                <td>
                                    <div class="qh-client-cell">
                                        <div class="qh-client-avatar">
                                            {{ strtoupper($clientInitials) }}
                                        </div>
                                        <span class="qh-client-name" title="{{ $clientName }}">
                                            {{ $clientName }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Column 4: Total Value -->
                                <td class="text-end">
                                    <span class="qh-amount-cell">
                                        RM {{ number_format((float) ($item->grand_total ?? 0), 2) }}
                                    </span>
                                </td>

                                <!-- Column 5: Actions -->
                                <td class="qh-actions-cell">
                                    <div class="qh-action-group">
                                        <a href="{{ route('quotations.show', $item->quotation_Id) }}"
                                           class="qh-action-btn"
                                           title="View quotation details"
                                           aria-label="View quotation {{ $codeVal }}">
                                            <span>Details</span>
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>

                                        <a href="{{ route('quotations.invoice', $item->quotation_Id) }}"
                                           class="qh-action-btn qh-action-btn--invoice"
                                           title="Generate official client invoice"
                                           aria-label="Generate invoice for quotation {{ $codeVal }}">
                                            <i class="fa-solid fa-file-invoice-dollar"></i>
                                            <span>Invoice</span>
                                        </a>

                                        <form action="{{ route('quotations.destroy', $item->quotation_Id) }}"
                                              method="POST"
                                              class="inline-block form-delete-quotation m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    class="qh-action-btn qh-action-btn--delete btn-delete-quotation"
                                                    title="Delete quotation record"
                                                    aria-label="Delete quotation {{ $codeVal }}"
                                                    data-code="{{ $codeVal }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyInitialRow">
                                <td colspan="5" class="p-0">
                                    <div class="qh-empty-state">
                                        <div class="qh-empty-icon"><i class="fa-regular fa-folder-open"></i></div>
                                        <h3 class="qh-empty-title">No quotations yet</h3>
                                        <p class="qh-empty-text">No commercial estimates have been drafted yet. Start a new quotation to calculate project survey rates and equipment costs.</p>
                                        <a href="{{ route('quotation.index') }}" class="qh-btn qh-btn--primary" style="margin-top: 0.5rem;">
                                            <i class="fa-solid fa-plus"></i>
                                            <span>Draft New Quotation</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                        <!-- Dynamic No-Matches Row (shown via client-side filter when 0 records match) -->
                        <tr id="emptyFilterRow" style="display: none;">
                            <td colspan="5" class="p-0">
                                <div class="qh-empty-state">
                                    <div class="qh-empty-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                                    <h3 class="qh-empty-title">No matching quotations found</h3>
                                    <p class="qh-empty-text">No quotation records matched your search keywords or active filters. Try broadening your criteria.</p>
                                    <button type="button" id="resetFiltersEmptyBtn" class="qh-btn qh-btn--primary" style="margin-top: 0.5rem;">
                                        <i class="fa-solid fa-rotate-left"></i>
                                        <span>Clear All Filters</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Legacy Preview Modal Container (Safely Retained) -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold">Quotation Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-white p-4">
                    @include('dashboard.preview')
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="window.print()">
                        <i class="fa-solid fa-print me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Attachments -->
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchHistoryInput');
            const typeFilter = document.getElementById('typeFilter');
            const locationFilter = document.getElementById('locationFilter');
            const monthFilter = document.getElementById('monthFilter');
            const clearFiltersButton = document.getElementById('clearHistoryFilters');
            const resetFiltersEmptyBtn = document.getElementById('resetFiltersEmptyBtn');
            const resultsCount = document.getElementById('historyResultsCount');
            const rows = Array.from(document.querySelectorAll('.quotation-row'));
            const emptyFilterRow = document.getElementById('emptyFilterRow');

            function filterTable() {
                const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const selectedType = typeFilter ? typeFilter.value.toLowerCase().trim() : '';
                const selectedLocation = locationFilter ? locationFilter.value.toLowerCase().trim() : '';
                const selectedMonth = monthFilter ? monthFilter.value.trim() : '';
                let visibleCount = 0;

                rows.forEach(row => {
                    const rowSearch = (row.getAttribute('data-search') || '') + ' ' + row.innerText.toLowerCase();
                    const rowType = row.getAttribute('data-type') || '';
                    const rowLocation = row.getAttribute('data-location') || '';
                    const rowMonth = row.getAttribute('data-month') || '';

                    const matchesSearch = query === '' || rowSearch.includes(query);
                    const matchesType = selectedType === '' || rowType === selectedType;
                    const matchesLocation = selectedLocation === '' || rowLocation === selectedLocation;
                    const matchesMonth = selectedMonth === '' || rowMonth === selectedMonth;
                    const isVisible = matchesSearch && matchesType && matchesLocation && matchesMonth;

                    row.style.display = isVisible ? '' : 'none';
                    if (isVisible) visibleCount++;
                });

                if (resultsCount) {
                    resultsCount.textContent = visibleCount + (visibleCount === 1 ? ' record' : ' records');
                }

                if (emptyFilterRow) {
                    if (rows.length > 0 && visibleCount === 0) {
                        emptyFilterRow.style.display = '';
                    } else {
                        emptyFilterRow.style.display = 'none';
                    }
                }
            }

            function clearHistoryFilters() {
                if (searchInput) searchInput.value = '';
                if (typeFilter) typeFilter.value = '';
                if (locationFilter) locationFilter.value = '';
                if (monthFilter) monthFilter.value = '';
                filterTable();
            }

            if (searchInput) searchInput.addEventListener('input', filterTable);
            if (typeFilter) typeFilter.addEventListener('change', filterTable);
            if (locationFilter) locationFilter.addEventListener('change', filterTable);
            if (monthFilter) monthFilter.addEventListener('change', filterTable);
            if (clearFiltersButton) clearFiltersButton.addEventListener('click', clearHistoryFilters);
            if (resetFiltersEmptyBtn) resetFiltersEmptyBtn.addEventListener('click', clearHistoryFilters);

            // Initial calculation
            filterTable();

            // SweetAlert2 Delete Confirmation
            document.querySelectorAll('.btn-delete-quotation').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const code = this.getAttribute('data-code') || 'this quotation';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Delete Quotation?',
                            text: `Are you sure you want to delete ${code}? This quotation record and its itemized breakdown will be permanently removed.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Yes, delete',
                            cancelButtonText: 'Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    } else if (confirm(`Are you sure you want to delete ${code}?`)) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>