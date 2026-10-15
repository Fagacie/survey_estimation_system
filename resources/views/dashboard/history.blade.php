<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Quotation History</x-slot>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="{{ asset('css/quotation-history.css') }}?v={{ filemtime(public_path('css/quotation-history.css')) }}">

    <!-- Main Content Layout Wrapper -->
    <div class="quotation-history-page">
    <div class="history-container">

        <!-- Page Heading Section -->
        <div class="page-header-wrapper">
            <p class="history-eyebrow">Commercial records / quotations</p>
            <h2 class="page-header-title">Quotation History</h2>
            <p class="page-header-subtitle">Review estimates, open quotation workspaces, and move approved work into invoicing.</p>
        </div>
        
        <!-- Search Input and Filter Bar -->
        <div class="filter-card-custom">
            <div class="row g-3">
                <!-- Text Input for Keyword Searching -->
                <div class="col-md-4">
                    <label class="filter-label">
                        SEARCH
                        <i class="bi bi-search text-muted"></i>
                    </label>
                    <input
                        type="text"
                        class="filter-input"
                        id="searchHistoryInput"
                        placeholder="Search project name, client, or code...">
                </div>

                <!-- Dropdown for Project Type -->
                <div class="col-md-3">
                    <label class="filter-label">PROJECT TYPE</label>
                    <select id="typeFilter" class="filter-select">
                        <option value="">All Project Types</option>
                        <option value="CP">CP - Coastal Project</option>
                        <option value="RP">RP - River Project</option>
                        <option value="MP">MP - Maritime Project</option>
                        <option value="GP">GP - Geotech Project</option>
                        <option value="JP">JP - Jetty Project</option>
                    </select>
                </div>

                <!-- NEW: Dropdown for Location -->
                <div class="col-md-3">
                    <label class="filter-label">
                        LOCATION
                        <i class="bi bi-geo-alt text-muted"></i>
                    </label>
                    <select id="locationFilter" class="filter-select">
                        <option value="">All Locations</option>
                        @foreach(\App\Models\Project::LOCATIONS as $loc)
                            <option value="{{ $loc }}">{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown for Month Filtering -->
                <div class="col-md-2">
                    <label class="filter-label">
                        MONTH
                        <i class="bi bi-calendar3 text-muted"></i>
                    </label>
                    <select id="monthFilter" class="filter-select">
                        <option value="">Choose Month</option>
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
            </div>
            <div class="history-filter-footer">
                <span id="historyResultsCount" class="history-results-count" aria-live="polite">
                    {{ $quotations->count() }} records
                </span>
                <button type="button" id="clearHistoryFilters" class="history-clear-filters">
                    <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                    Clear filters
                </button>
            </div>
        </div>

        <!-- History Data Table Container -->
        <div class="table-card">
            <table class="custom-project-table" id="quotationHistoryTable">
                <caption class="sr-only">Quotation history register</caption>
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th style="width: 180px;">CODE</th>
                        <th>PROJECT DETAILS</th>
                        <th>CLIENT</th>
                        <th style="width: 140px;">UPDATED</th>
                        <th class="text-end" style="width: 140px;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($quotations as $index => $item)
                    @php
                        $createdDate = $item->created_at ? \Carbon\Carbon::parse($item->created_at) : null;
                        $formattedDate = $createdDate ? $createdDate->format('Y-m-d') : '';
                        $monthVal = $createdDate ? $createdDate->format('m') : '';
                        $codeVal = $item->project?->number ?? $item->quotation_no ?? '';
                        $locationVal = $item->project?->location ?? '';   // NEW
                    @endphp
                    <tr class="quotation-row" 
                        data-code="{{ strtolower($codeVal) }}" 
                        data-date="{{ $formattedDate }}" 
                        data-month="{{ $monthVal }}"
                        data-type="{{ strtolower($item->project?->project_type ?? '') }}"
                        data-location="{{ strtolower($locationVal) }}">
                        
                        <!-- Row Index -->
                        <td class="text-muted fw-semibold row-number">{{ $loop->iteration }}</td>
                        
                        <!-- Project Code/Number -->
                        <td>
                            <span class="project-code-text">
                                {{ $codeVal ?: 'EHS/PRJ/26/000' }}
                            </span>
                        </td>

                        <!-- Project Details -->
                        <td>
                            <span class="project-title-text">
                                {{ $item->project?->name ?? $item->project?->project_name ?? 'Untitled Project' }}
                            </span>
                            <div class="project-sub-info">
                                <i class="bi bi-clock me-1"></i> {{ $createdDate ? $createdDate->diffForHumans() : 'Recently' }}
                                {{-- NEW: show location --}}
                                @if($locationVal)
                                    <span class="ms-2"><i class="bi bi-geo-alt me-1"></i>{{ $locationVal }}</span>
                                @endif
                            </div>
                        </td>
                        
                        <!-- Client Name -->
                        <td>
                            <span class="text-secondary fw-medium client-name">
                                {{ $item->project?->client?->company_name ?? $item->project?->client?->name ?? '—' }}
                            </span>
                        </td>
                        
                        <!-- Date Created -->
                        <td class="text-secondary font-monospace fs-7">
                            {{ $formattedDate ?: 'N/A' }}
                        </td>
                        
                        <!-- Actions -->
                        <td>
                            <div class="action-buttons-group">
                                <a href="{{ route('quotations.show', $item->quotation_Id) }}" class="action-circle-btn" title="View Details" aria-label="View quotation details">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                                
                                <a href="{{ route('quotations.invoice', $item->quotation_Id) }}" class="action-circle-btn" title="Generate Invoice" aria-label="Generate invoice">
                                    <i class="bi bi-file-earmark-text"></i>
                                </a>

                                <button type="button" class="action-circle-btn delete-btn" 
                                        title="Delete" aria-label="Delete quotation" 
                                        data-id="{{ $item->quotation_Id }}" 
                                        data-delete-url="{{ route('quotations.destroy', $item->quotation_Id) }}">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="emptyStateRow">
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-slate-300"></i>
                            No history records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

    </div>
    </div>

    <!-- Hidden Confirmation Modal Pop-up for Deleting Records -->
    <div id="deleteModalOverlay" class="modal-overlay">
        <div class="modal-card">
            <h3>Delete Quotation</h3>
            <p>Are you sure you want to delete this quotation? This action cannot be undone.</p>
            
            <form id="deleteQuotationForm" method="POST" action="">
                @csrf
                @method('DELETE')
                
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" id="cancelDeleteBtn">Cancel</button>
                    <button type="submit" class="btn-confirm">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchHistoryInput');
            const typeFilter = document.getElementById('typeFilter');
            const locationFilter = document.getElementById('locationFilter');   // NEW
            const monthFilter = document.getElementById('monthFilter');
            const clearFiltersButton = document.getElementById('clearHistoryFilters');
            const resultsCount = document.getElementById('historyResultsCount');
            const rows = Array.from(document.querySelectorAll('.quotation-row'));

            function filterTable() {
                const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const selectedType = typeFilter ? typeFilter.value.toLowerCase().trim() : '';
                const selectedLocation = locationFilter ? locationFilter.value.toLowerCase().trim() : '';
                const selectedMonth = monthFilter ? monthFilter.value.trim() : '';
                let visibleCount = 0;

                rows.forEach(row => {
                    const rowText = row.innerText.toLowerCase();
                    const rowType = row.getAttribute('data-type') || '';
                    const rowLocation = row.getAttribute('data-location') || '';
                    const rowMonth = row.getAttribute('data-month') || '';

                    const matchesSearch = query === '' || rowText.includes(query);
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
            }

            function clearHistoryFilters() {
                if (searchInput) searchInput.value = '';
                if (typeFilter) typeFilter.value = '';
                if (locationFilter) locationFilter.value = '';
                if (monthFilter) monthFilter.value = '';
                filterTable();
            }
            // Event Listeners for Filters
            if (searchInput) searchInput.addEventListener('keyup', filterTable);
            if (typeFilter) typeFilter.addEventListener('change', filterTable);
            if (locationFilter) locationFilter.addEventListener('change', filterTable);   // NEW
            if (monthFilter) monthFilter.addEventListener('change', filterTable);
            if (clearFiltersButton) clearFiltersButton.addEventListener('click', clearHistoryFilters);

            // Initialize count and keep it synchronized with the active filters.
            filterTable();

            // Modal logic for Delete button
            const deleteOverlay = document.getElementById('deleteModalOverlay');
            const deleteForm = document.getElementById('deleteQuotationForm');
            const cancelBtn = document.getElementById('cancelDeleteBtn');

            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const deleteUrl = this.getAttribute('data-delete-url');
                    if (deleteForm && deleteUrl) {
                        deleteForm.action = deleteUrl;
                    }
                    if (deleteOverlay) {
                        deleteOverlay.style.display = 'flex';
                    }
                });
            });

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function () {
                    if (deleteOverlay) {
                        deleteOverlay.style.display = 'none';
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>