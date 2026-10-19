<x-app-layout containerClass="w-full px-4 py-6 sm:px-8 sm:py-8">
@push('styles')
<link rel="stylesheet" href="{{ asset('css/projects-dashboard.css') }}?v={{ filemtime(public_path('css/projects-dashboard.css')) }}">
@endpush

<div class="projects-dashboard">
    <!-- BREADCRUMB -->
    <nav class="pr-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('dashboard') }}">
            <i class="fa-solid fa-chart-line"></i> Dashboard
        </a>
        <span class="breadcrumb-separator">/</span>
        <span class="pr-breadcrumb-current">Survey Projects</span>
    </nav>

    <!-- 1. PAGE HEADER -->
    <header class="projects-dashboard-header">
        <div class="pr-header-title-wrap">
            <h1>Survey Projects</h1>
            <p>Coordinate operational intake, survey area boundaries, and commercial quotations across projects.</p>
        </div>
        <div class="pr-header-actions">
            <span class="pr-date-pill">
                <i class="fa-regular fa-calendar"></i>
                {{ now()->format('d M Y') }}
            </span>
            <a href="{{ route('projects.create') }}" class="pr-btn pr-btn--primary">
                <i class="fa-solid fa-plus"></i>
                <span>New Project</span>
            </a>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 px-4 py-3 mb-6 flex items-start gap-3 text-sm rounded-lg shadow-sm">
            <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    @php
        $plannedCount = \App\Models\Project::where('status', 'planned')->count();
        $draftCount = \App\Models\Project::where('status', 'draft')->count();
        $completedCount = \App\Models\Project::where('status', 'completed')->count();
    @endphp

    <!-- 2. TELEMETRY & KPI METRIC TILES -->
    <section class="pr-kpi-grid" aria-label="Projects Register Summary">
        <div class="pr-kpi-tile">
            <div class="pr-kpi-icon">
                <i class="fa-solid fa-folder-tree"></i>
            </div>
            <div class="pr-kpi-content">
                <span class="pr-kpi-label">Total Projects</span>
                <span class="pr-kpi-value">{{ $totalProjects }}</span>
                <span class="pr-kpi-subtext">All registered survey records</span>
            </div>
        </div>

        <div class="pr-kpi-tile">
            <div class="pr-kpi-icon pr-kpi-icon--blue">
                <i class="fa-solid fa-water"></i>
            </div>
            <div class="pr-kpi-content">
                <span class="pr-kpi-label">Active / Planned</span>
                <span class="pr-kpi-value">{{ $plannedCount }}</span>
                <span class="pr-kpi-subtext">Under active survey planning</span>
            </div>
        </div>

        <div class="pr-kpi-tile">
            <div class="pr-kpi-icon pr-kpi-icon--amber">
                <i class="fa-regular fa-file-lines"></i>
            </div>
            <div class="pr-kpi-content">
                <span class="pr-kpi-label">Draft Intake</span>
                <span class="pr-kpi-value">{{ $draftCount }}</span>
                <span class="pr-kpi-subtext">Awaiting boundary mapping</span>
            </div>
        </div>

        <div class="pr-kpi-tile">
            <div class="pr-kpi-icon pr-kpi-icon--emerald">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="pr-kpi-content">
                <span class="pr-kpi-label">Completed</span>
                <span class="pr-kpi-value">{{ $completedCount }}</span>
                <span class="pr-kpi-subtext">Surveys & deliverables signed off</span>
            </div>
        </div>
    </section>

    @if($totalProjects > 0)
        <!-- 3. PROJECT REGISTER & FILTERS -->
        <section class="projects-dashboard-register" aria-labelledby="register-title">
            <div class="projects-dashboard-register-header">
                <div class="pr-register-title-wrap">
                    <h2 id="register-title">Project Register</h2>
                    <span class="projects-dashboard-result-count">{{ $projects->total() }} results</span>
                </div>
            </div>

            <!-- Modern Filter Toolbar -->
            <form method="GET" action="{{ route('projects.index') }}" class="projects-dashboard-filters">
                <!-- Search Box -->
                <div class="pr-search-box">
                    <i class="fa-solid fa-magnifying-glass pr-search-icon"></i>
                    <input type="search"
                           name="search"
                           class="pr-search-input"
                           aria-label="Search projects"
                           placeholder="Search name, code, or client company..."
                           value="{{ request('search') }}">
                </div>

                <!-- Status Filter -->
                <div class="pr-select-box">
                    <select name="status" class="pr-select-input" aria-label="Filter by status" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>Planned (In Progress)</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <!-- Type Filter -->
                <div class="pr-select-box">
                    <select name="project_type" class="pr-select-input" aria-label="Filter by project type" onchange="this.form.submit()">
                        <option value="">All Project Types</option>
                        @foreach($projectTypes as $code => $label)
                            <option value="{{ $code }}" {{ request('project_type') === $code ? 'selected' : '' }}>
                                {{ $code }} · {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Filter -->
                <div class="pr-select-box">
                    <select name="location" class="pr-select-input" aria-label="Filter by location" onchange="this.form.submit()">
                        <option value="">All Locations</option>
                        @foreach($locations as $location)
                            <option value="{{ $location }}" {{ request('location') === $location ? 'selected' : '' }}>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range Filter -->
                <div class="pr-select-box">
                    <select name="date_range" class="pr-select-input" aria-label="Filter by timeline" onchange="this.form.submit()">
                        <option value="">All Time</option>
                        <option value="last_30_days" {{ request('date_range') === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="this_month" {{ request('date_range') === 'this_month' ? 'selected' : '' }}>This Month</option>
                        <option value="this_year" {{ request('date_range') === 'this_year' ? 'selected' : '' }}>This Year</option>
                    </select>
                </div>

                @if(request('search') || request('status') || request('project_type') || request('location') || request('date_range') || request('survey_status') || request('has_quotation') || request('attention'))
                    <a href="{{ route('projects.index') }}" class="projects-dashboard-clear">
                        <i class="fa-solid fa-xmark"></i> Clear Filters
                    </a>
                @endif
                <button type="submit" class="hidden">Filter</button>
            </form>

            <!-- 4. DATA TABLE -->
            <div class="projects-dashboard-table-wrap">
                <table class="projects-dashboard-table">
                    <caption class="sr-only">Survey project register table</caption>
                    <thead>
                        <tr>
                            <th scope="col">Project Code</th>
                            <th scope="col">Project Name & Scope</th>
                            <th scope="col">Client Entity</th>
                            <th scope="col">Type & Location</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="projects-dashboard-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            @php
                                $status = strtolower($project->status ?: 'draft');
                                $clientName = $project->client?->company_name ?? 'No client assigned';
                                $initials = collect(preg_split('/\s+/', $clientName))->filter()->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('') ?: 'PR';
                            @endphp
                            <tr>
                                <!-- Code -->
                                <td>
                                    <span class="projects-dashboard-project-code">
                                        {{ $project->number ?? 'PRJ-' . str_pad($project->project_Id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>

                                <!-- Name & Period -->
                                <td>
                                    <div class="projects-dashboard-project-name">
                                        {{ $project->name }}
                                    </div>
                                    <div class="projects-dashboard-project-meta">
                                        <i class="fa-regular fa-clock mr-1 opacity-70"></i>
                                        {{ $project->period ?? 'Unspecified duration' }}
                                    </div>
                                </td>

                                <!-- Client -->
                                <td>
                                    <div class="pr-client-cell">
                                        <div class="pr-client-avatar">
                                            {{ strtoupper($initials) }}
                                        </div>
                                        <span class="projects-dashboard-client">
                                            {{ $clientName }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Type & Location -->
                                <td>
                                    <div class="pr-type-tag">
                                        <i class="fa-solid fa-layer-group text-blue-500"></i>
                                        <span>{{ $projectTypes[$project->project_type] ?? $project->project_type ?? 'Survey' }}</span>
                                    </div>
                                    @if($project->location)
                                        <div class="pr-location-sub">
                                            <i class="fa-solid fa-location-dot text-rose-500 mr-0.5"></i>
                                            {{ $project->location }}
                                        </div>
                                    @endif
                                </td>

                                <!-- Status Pill with colored dot -->
                                <td>
                                    <span class="pr-status-pill pr-status-pill--{{ $status }}">
                                        <span class="pr-status-dot"></span>
                                        {{ ucfirst($project->status ?: 'draft') }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="projects-dashboard-actions">
                                    <div class="pr-action-group">
                                        <a href="{{ route('projects.show', $project->project_Id) }}"
                                           class="pr-action-btn pr-action-btn--workspace"
                                           title="Open Project Workspace">
                                            <span>Workspace</span>
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>

                                        <a href="{{ route('projects.edit', $project->project_Id) }}"
                                           class="pr-action-btn"
                                           title="Edit Project Details">
                                            <i class="fa-solid fa-pen"></i>
                                            <span>Edit</span>
                                        </a>

                                        <form action="{{ route('projects.destroy', $project->project_Id) }}" method="POST" class="inline-block form-delete m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    class="pr-action-btn pr-action-btn--delete btn-delete-action"
                                                    title="Delete project"
                                                    aria-label="Delete project: {{ $project->name }}">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 bg-white">
                                    <div class="text-slate-400 mb-2">
                                        <i class="fa-regular fa-folder-open text-3xl"></i>
                                    </div>
                                    <div class="text-slate-700 font-semibold mb-1">No survey projects match your criteria</div>
                                    <p class="text-slate-400 text-xs mb-3">Try adjusting your filters or search keywords.</p>
                                    <a href="{{ route('projects.index') }}" class="pr-btn pr-btn--secondary pr-btn--sm">
                                        Reset Filters
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($projects->hasPages())
                <div class="px-5 py-3.5 border-t border-slate-200 bg-slate-50 flex justify-center">
                    {{ $projects->links('pagination::tailwind') }}
                </div>
            @endif
        </section>
    @else
        <!-- Empty State -->
        <section class="projects-dashboard-empty">
            <div class="pr-empty-icon-wrap">
                <i class="fa-solid fa-layer-group"></i>
            </div>
            <h2>No survey projects yet</h2>
            <p>Create your first hydrographic or drone mapping project to begin defining boundaries, runlines, and cost estimations.</p>
            <a href="{{ route('projects.create') }}" class="pr-btn pr-btn--primary">
                <i class="fa-solid fa-plus"></i>
                <span>Create New Project</span>
            </a>
        </section>
    @endif
</div>

<!-- SweetAlert2 Delete Confirmation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete-action');
        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                const form = this.closest('form');
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Delete this project?',
                        text: "All map boundaries, survey lines, and cost quotations will be permanently removed.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, delete project',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else if (confirm('Are you sure you want to delete this project?')) {
                    form.submit();
                }
            });
        });
    });
</script>
</x-app-layout>
