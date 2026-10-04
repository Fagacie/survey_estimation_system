<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Quotation History</x-slot>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* Base Overrides */
        body {
            color: #334155;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .history-container {
            max-width: none !important;
            margin: 0 !important;
        }

        /* Page Heading */
        .page-header-wrapper {
            margin-bottom: 24px;
        }

        .page-header-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
            margin-bottom: 4px;
        }

        .page-header-subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0;
        }

        /* Filter Box Cards */
        .filter-card-custom {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .filter-label {
            font-size: 0.725rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .filter-input, .filter-select {
            width: 100%;
            height: 42px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 0.875rem;
            color: #1e293b;
            background-color: #ffffff;
            transition: all 0.2s ease;
        }

        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
        }

        /* Table Design */
        .table-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }

        .custom-project-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .custom-project-table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.725rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 14px 20px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .custom-project-table tbody td {
            padding: 16px 20px;
            font-size: 0.875rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            background-color: #ffffff;
        }

        .custom-project-table tbody tr:last-child td {
            border-bottom: none;
        }

        .custom-project-table tbody tr:hover td {
            background-color: #f8fafc;
        }

        .project-code-text {
            color: #64748b;
            font-size: 0.825rem;
            font-weight: 500;
        }

        .project-title-text {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.925rem;
            display: block;
        }

        .project-sub-info {
            font-size: 0.775rem;
            color: #94a3b8;
            margin-top: 2px;
        }

        /* Circular Action Buttons */
        .action-buttons-group {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
        }

        .action-circle-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #cbd5e1;
            background-color: #ffffff;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .action-circle-btn:hover {
            background-color: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .action-circle-btn.delete-btn:hover {
            background-color: #fef2f2;
            color: #ef4444;
            border-color: #fca5a5;
        }

        /* Custom Modal Styling */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1050;
            align-items: center;
            justify-content: center;
        }

        .modal-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            max-width: 440px;
            width: 90%;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .modal-card h3 {
            font-size: 1.125rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .modal-card p {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 20px;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn-cancel {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .btn-confirm {
            background: #ef4444;
            border: none;
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
        }
    </style>

    <!-- Main Content Layout Wrapper -->
    <div class="history-container">

        <!-- Page Heading Section -->
        <div class="page-header-wrapper">
            <h2 class="page-header-title">Quotation History</h2>
            <p class="page-header-subtitle">Search, view, and manage all previous project estimations and quotations.</p>
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
        </div>

        <!-- History Data Table Container -->
        <div class="table-card">
            <table class="custom-project-table" id="quotationHistoryTable">
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
                                <a href="{{ route('quotations.show', $item->quotation_Id) }}" class="action-circle-btn" title="View Details">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                                
                                <a href="{{ route('quotations.invoice', $item->quotation_Id) }}" class="action-circle-btn" title="Generate Invoice">
                                    <i class="bi bi-file-earmark-text"></i>
                                </a>

                                <button type="button" class="action-circle-btn delete-btn" 
                                        title="Delete" 
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
            const rows = document.querySelectorAll('.quotation-row');

            function filterTable() {
                const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
                const selectedType = typeFilter ? typeFilter.value.toLowerCase().trim() : '';
                const selectedLocation = locationFilter ? locationFilter.value.toLowerCase().trim() : '';   // NEW
                const selectedMonth = monthFilter ? monthFilter.value.trim() : '';

                rows.forEach(row => {
                    const rowText = row.innerText.toLowerCase();
                    const rowCode = row.getAttribute('data-code') || '';
                    const rowLocation = row.getAttribute('data-location') || '';   // NEW
                    const rowMonth = row.getAttribute('data-month') || '';

                    const matchesSearch = query === '' || rowText.includes(query);
                    const matchesType = selectedType === '' || rowCode.includes(selectedType);
                    const matchesLocation = selectedLocation === '' || rowLocation === selectedLocation;   // NEW
                    const matchesMonth = selectedMonth === '' || rowMonth === selectedMonth;

                    if (matchesSearch && matchesType && matchesLocation && matchesMonth) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            // Event Listeners for Filters
            if (searchInput) searchInput.addEventListener('keyup', filterTable);
            if (typeFilter) typeFilter.addEventListener('change', filterTable);
            if (locationFilter) locationFilter.addEventListener('change', filterTable);   // NEW
            if (monthFilter) monthFilter.addEventListener('change', filterTable);

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