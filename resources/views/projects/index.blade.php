<x-app-layout containerClass="w-full px-4 py-6 sm:px-8 sm:py-8">
<link rel="stylesheet" href="{{ asset('css/projects-dashboard.css') }}?v={{ filemtime(public_path('css/projects-dashboard.css')) }}">
<div class="projects-dashboard">
    
    <!-- 1. PAGE HEADER -->
    <header class="projects-dashboard-header">
        <div>
            <p class="projects-dashboard-eyebrow">Operations / project register</p>
            <h1>Survey Projects</h1>
            <p class="projects-dashboard-intro">Coordinate project setup, survey planning, and estimation from one operational workspace.</p>
        </div>
        <div class="projects-dashboard-header-tools">
            <span class="projects-dashboard-date">{{ now()->format('d M Y') }}</span>
            <a href="{{ route('projects.create') }}" class="projects-dashboard-primary"><i class="fa-solid fa-plus"></i> New project</a>
        </div>
    </header>

    @if(session('success'))
        <div class="projects-dashboard-feedback" role="status">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <section class="projects-dashboard-overview" aria-label="Project overview">
        <div class="projects-dashboard-overview-lead">
            <span class="projects-dashboard-overview-label">Portfolio overview</span>
            <strong>{{ $metrics['total'] }}</strong>
            <span>total projects in your workspace</span>
        </div>
        <dl class="projects-dashboard-overview-metrics">
            <div><dt>Drafts</dt><dd>{{ $metrics['draft'] }}</dd></div>
            <div><dt>Mapped areas</dt><dd>{{ $metrics['mapped'] }}</dd></div>
            <div><dt>Quotations</dt><dd>{{ $metrics['quotations'] }}</dd></div>
        </dl>
    </section>

    @if($metrics['total'] > 0)
        @if($attention['missing_boundaries'] > 0 || $attention['missing_lines'] > 0 || $attention['missing_parameters'] > 0 || $attention['missing_cost'] > 0)
            <section class="projects-dashboard-queue" aria-labelledby="attention-title">
                <div class="projects-dashboard-section-heading">
                    <div><p class="projects-dashboard-eyebrow">Next actions</p><h2 id="attention-title">Needs attention</h2></div>
                    <span>{{ collect($attention)->sum() }} open checks</span>
                </div>
                <div class="projects-dashboard-queue-grid">
                    @if($attention['missing_boundaries'] > 0)
                        <div><i class="fa-solid fa-draw-polygon"></i><span>Missing boundaries</span><strong>{{ $attention['missing_boundaries'] }}</strong></div>
                    @endif
                    @if($attention['missing_lines'] > 0)
                        <div><i class="fa-solid fa-route"></i><span>Pending survey lines</span><strong>{{ $attention['missing_lines'] }}</strong></div>
                    @endif
                    @if($attention['missing_parameters'] > 0)
                        <div><i class="fa-solid fa-sliders"></i><span>Missing parameters</span><strong>{{ $attention['missing_parameters'] }}</strong></div>
                    @endif
                    @if($attention['missing_cost'] > 0)
                        <div><i class="fa-solid fa-calculator"></i><span>Awaiting cost estimation</span><strong>{{ $attention['missing_cost'] }}</strong></div>
                    @endif
                </div>
            </section>
        @endif

        <section class="projects-dashboard-register" aria-labelledby="register-title">
            <div class="projects-dashboard-register-header">
                <div><p class="projects-dashboard-eyebrow">Work queue</p><h2 id="register-title">Project register</h2><p>Search and filter projects before opening a workspace.</p></div>
                <span class="projects-dashboard-result-count">{{ $projects->total() }} results</span>
            </div>
            <form method="GET" action="{{ route('projects.index') }}" class="projects-dashboard-filters">
                <label class="projects-dashboard-search"><i class="fa-solid fa-magnifying-glass"></i><input type="search" name="search" aria-label="Search projects" placeholder="Search name, code, or client" value="{{ request('search') }}"></label>
                    
                    <label class="projects-dashboard-select"><span>Status</span><select name="status" aria-label="Filter by status" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select></label>

                    <label class="projects-dashboard-select"><span>Type</span><select name="project_type" aria-label="Filter by project type" onchange="this.form.submit()">
                            <option value="">All Project Types</option>
                            @foreach($projectTypes as $code => $label)
                                <option value="{{ $code }}" {{ request('project_type') === $code ? 'selected' : '' }}>{{ $code }} · {{ $label }}</option>
                            @endforeach
                        </select></label>

                    <label class="projects-dashboard-select"><span>Location</span><select name="location" aria-label="Filter by location" onchange="this.form.submit()">
                            <option value="">All Locations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location }}" {{ request('location') === $location ? 'selected' : '' }}>{{ $location }}</option>
                            @endforeach
                        </select></label>

                    <label class="projects-dashboard-select"><span>Created</span><select name="date_range" aria-label="Filter by date range" onchange="this.form.submit()">
                            <option value="">All Time</option>
                            <option value="last_30_days" {{ request('date_range') === 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
                            <option value="this_month" {{ request('date_range') === 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="this_year" {{ request('date_range') === 'this_year' ? 'selected' : '' }}>This Year</option>
                        </select></label>

                    @if(request('search') || request('status') || request('project_type') || request('location') || request('date_range'))
                        <a href="{{ route('projects.index') }}" class="projects-dashboard-clear"><i class="fa-solid fa-xmark"></i> Clear</a>
                    @endif
                    <button type="submit" class="hidden">Filter</button>
            </form>

            <!-- Table Data (Dense) -->
            <div class="projects-dashboard-table-wrap">
                <table class="projects-dashboard-table">
                    <caption class="sr-only">Survey project register</caption>
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200 text-slate-500">
                            <th scope="col">Project</th><th scope="col">Survey details</th><th scope="col">Client</th><th scope="col">Type</th><th scope="col" class="projects-dashboard-actions-heading">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($projects as $project)
                            <tr class="hover:bg-slate-50 transition-colors group">
                                <td>
                                    <span class="projects-dashboard-project-code">
                                        {{ $project->number ?? 'PRJ-' . str_pad($project->project_Id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="projects-dashboard-project-name">{{ $project->name }}</div>
                                    <div class="projects-dashboard-project-meta">
                                        <i class="fa-regular fa-clock mr-1 opacity-70"></i> {{ $project->period ?? 'Unspecified' }}
                                    </div>
                                </td>
                                <td><span class="projects-dashboard-client">{{ $project->client?->company_name ?? 'No client assigned' }}</span>
                                </td>
                                <td><span class="projects-dashboard-type">
                                        {{ $projectTypes[$project->project_type] ?? $project->project_type ?? 'Not set' }}
                                    </span>
                                </td>
                                <td class="projects-dashboard-actions"><div>
                                        <a href="{{ route('projects.show', $project->project_Id) }}" class="projects-dashboard-action" title="Open project"><i class="fa-solid fa-arrow-up-right-from-square"></i><span>Open</span>
                                            <i class="fa-solid fa-arrow-right font-light"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project->project_Id) }}" class="projects-dashboard-action projects-dashboard-action-muted" title="Edit project"><i class="fa-solid fa-pen"></i><span>Edit</span>
                                            <i class="fa-solid fa-pen font-light text-sm"></i>
                                        </a>
                                        <form action="{{ route('projects.destroy', $project->project_Id) }}" method="POST" class="inline-block form-delete m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="projects-dashboard-action projects-dashboard-action-danger btn-delete-action" title="Delete project">
                                                <i class="fa-solid fa-trash-can"></i><span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10 bg-white">
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
        </section>

    @else
        <!-- Empty State -->
        <section class="projects-dashboard-empty">
            <i class="fa-solid fa-layer-group"></i>
            <h2>No projects yet</h2>
            <p>
                Create your first survey estimation project to begin tracking boundaries, lines, and costs.
            </p>
            <a href="{{ route('projects.create') }}" class="projects-dashboard-primary"><i class="fa-solid fa-plus"></i> Create project
            </a>
        </section>
    @endif
</div>

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
