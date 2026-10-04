<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Admin - Data Management</x-slot>

    <!-- Bootstrap & Icons (still needed by the module-item partial and admin.js) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    <style>
        /* ---------- GROUP (one per category) ---------- */
        .admin-container .data-group {
            margin-bottom: 2rem;
        }
        .admin-container .data-group h2 {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748b;
            margin: 0;
        }

        /* ---------- TABLE CARD ---------- */
        .admin-container .table-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
            overflow-x: auto;
        }
        .admin-container .table-card table {
            width: 100%;
            border-collapse: collapse;
        }
        .admin-container .table-card thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }
        .admin-container .table-card tbody td {
            color: #334155;
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .admin-container .table-card tbody tr:last-child td {
            border-bottom: none;
        }
        .admin-container .table-card tbody tr:not(.empty-row):hover {
            background: #f8fafc;
        }
        .admin-container .number-column {
            width: 48px;
            text-align: center !important;
            color: #94a3b8;
        }
        .admin-container .empty-row td {
            height: 48px;
        }

        /* ---------- RATE BADGE ---------- */
        .admin-container .rate-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            background: #f0fdfa;
            color: #0f766e;
            border: 1px solid #99f6e4;
            border-radius: 9999px;
            padding: 0.15rem 0.65rem;
            font-size: 0.8rem;
            font-weight: 600;
            white-space: nowrap;
        }

        /* ---------- ACTION BUTTONS ---------- */
        .admin-container .action-buttons {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .admin-container .action-button {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            color: #64748b;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.8rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s;
        }
        .admin-container .edit-button:hover:not(:disabled) {
            color: #0d9488;
            border-color: #5eead4;
            background: #f0fdfa;
        }
        .admin-container .delete-button:hover:not(:disabled) {
            color: #ef4444;
            border-color: #fecaca;
            background: #fef2f2;
        }
        .admin-container .action-button:disabled {
            cursor: not-allowed;
        }

        /* ---------- CLICK FEEDBACK (class added by admin.js) ---------- */
        .admin-container .action-button.clicked,
        #createButton.clicked {
            transform: scale(0.92);
        }
    </style>

    <!-- MAIN WRAPPER -->
    <div class="admin-container w-full max-w-7xl mx-auto">

        <!-- PAGE TITLE & BUTTON -->
        <div class="mb-8 border-b border-slate-200 pb-5 flex flex-col md:flex-row justify-between md:items-end gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Data Management</h1>
                <p class="text-sm font-medium text-slate-500 mt-1">Manage packages, modules and items used in quotations.</p>
            </div>

            <!-- CREATE NEW ITEM BUTTON -->
            <a href="{{ route('newItem') }}" id="createButton" class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm no-underline">
                <i class="fa-solid fa-plus"></i> Create New
            </a>
        </div>
        
        <!-- SEARCH -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
            <label for="searchInput" class="block text-xs font-bold uppercase tracking-wide text-slate-500 mb-1.5">
                <i class="bi bi-search mr-1"></i> Search
            </label>
            <input
                type="text"
                id="searchInput"
                class="w-full rounded-lg border border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm"
                placeholder="Search item...">
        </div>

        <!-- MODULE ITEMS TABLE GROUPS -->
        @forelse ($dataGroups as $groupName => $group)
            @include('dashboard.module-item', [
                'title' => $groupName,
                'items' => $group['items'] ?? $group,
                'categoryId' => $group['id'] ?? null
            ])
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 py-12 text-center">
                <p class="text-sm text-slate-500">No data groups found.</p>
            </div>
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