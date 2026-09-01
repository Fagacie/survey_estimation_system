<x-app-layout containerClass="px-0">
    <style>
        .dashboard-wrapper {
            background-color: #f8fafc;
            min-height: calc(100vh - 64px);
        }

        /* Top Dark Section */
        .dashboard-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 3rem 1.5rem 7rem;
            position: relative;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-title {
            color: #ffffff;
            font-size: 1.85rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 0.25rem;
        }
        .page-subtitle {
            color: #94a3b8;
            font-size: 0.95rem;
        }

        .btn-premium {
            background: #3b82f6;
            color: #ffffff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-premium:hover {
            background: #2563eb;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        /* Floating KPI Cards */
        .kpi-wrapper {
            margin-top: -4.5rem;
            padding: 0 1.5rem;
            position: relative;
            z-index: 10;
        }
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }
        .kpi-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            border: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 20px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
        }
        .kpi-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .icon-blue { background: #eff6ff; color: #3b82f6; }
        .icon-amber { background: #fffbeb; color: #f59e0b; }
        .icon-indigo { background: #eef2ff; color: #6366f1; }
        .icon-emerald { background: #ecfdf5; color: #10b981; }

        .kpi-content h3 {
            color: #64748b;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .kpi-content .kpi-number {
            color: #0f172a;
            font-size: 1.75rem;
            font-weight: 700;
            line-height: 1;
        }

        /* Main Content Area */
        .content-area {
            padding: 3rem 1.5rem 5rem;
        }

        /* Secondary Stats (Estimation Overview) */
        .stats-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 3rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .stat-item {
            border-right: 1px solid #f1f5f9;
            padding: 0 1rem;
        }
        .stat-item:last-child {
            border-right: none;
        }
        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.25rem;
        }
        .stat-label {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Attention Alerts */
        .attention-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 1.25rem;
            border-left: 4px solid #ef4444;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            border-top: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .attention-card.warning { border-left-color: #f59e0b; }
        .attention-card.info { border-left-color: #3b82f6; }
        .attention-icon {
            font-size: 1.25rem;
            margin-top: 2px;
        }
        .warning .attention-icon { color: #f59e0b; }
        .info .attention-icon { color: #3b82f6; }
        .attention-card h4 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 0.25rem;
        }
        .attention-card p {
            font-size: 0.85rem;
            color: #64748b;
            margin: 0;
        }

        /* Projects Table */
        .table-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-top: 2rem;
        }
        .table-header-controls {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .table-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .premium-input {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 6px;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            color: #334155;
            transition: all 0.2s;
        }
        .premium-input:focus {
            outline: none;
            border-color: #3b82f6;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .premium-table {
            width: 100%;
            border-collapse: collapse;
        }
        .premium-table th {
            padding: 1rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .premium-table td {
            padding: 1.25rem 1.5rem;
            font-size: 0.9rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .premium-table tr:last-child td {
            border-bottom: none;
        }
        .premium-table tr:hover td {
            background: #fcfcfd;
        }
        
        .badge-premium {
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .badge-draft { background: #f1f5f9; color: #475569; }
        .badge-planned { background: #eff6ff; color: #2563eb; }
        .badge-completed { background: #ecfdf5; color: #059669; }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 6px;
            color: #64748b;
            transition: all 0.2s;
            text-decoration: none;
            margin-left: 0.25rem;
            background: transparent;
        }
        .action-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .action-btn.delete:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        /* Empty State */
        .premium-empty {
            text-align: center;
            padding: 4rem 2rem;
            background: #ffffff;
            border-radius: 12px;
            border: 1px dashed #cbd5e1;
            margin-top: 2rem;
        }
        .empty-icon {
            font-size: 3rem;
            color: #94a3b8;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 992px) {
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }
            .stats-strip { grid-template-columns: repeat(2, 1fr); }
            .stat-item:nth-child(2) { border-right: none; }
            .kpi-wrapper { margin-top: -3rem; }
        }
        @media (max-width: 576px) {
            .kpi-grid, .stats-strip { grid-template-columns: 1fr; }
            .stat-item { border-right: none; border-bottom: 1px solid #f1f5f9; padding: 1rem 0; }
            .stat-item:last-child { border-bottom: none; }
            .kpi-wrapper { margin-top: -2rem; padding: 0 1rem; }
        }
    </style>

    <div class="dashboard-wrapper">
        <!-- 1. HERO SECTION -->
        <div class="dashboard-hero">
            <div class="dashboard-container d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="page-title">Dashboard</h1>
                    <div class="page-subtitle">SBES Survey Estimation Management</div>
                </div>
                <div class="mt-4 mt-md-0">
                    <a href="{{ route('projects.create') }}" class="btn-premium">
                        <i class="fa-solid fa-plus"></i> New Project
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. FLOATING KPI CARDS -->
        <div class="kpi-wrapper dashboard-container">
            @if(session('success'))
                <div class="alert alert-success rounded-3 border-0 mb-4" style="background: #ecfdf5; color: #059669; box-shadow: 0 2px 4px rgba(0,0,0,0.05); padding: 1rem 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fa-solid fa-circle-check fs-5"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-icon-box icon-indigo"><i class="fa-solid fa-folder-tree"></i></div>
                    <div class="kpi-content">
                        <h3>Total Projects</h3>
                        <div class="kpi-number">{{ $metrics['total'] }}</div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon-box icon-amber"><i class="fa-solid fa-file-pen"></i></div>
                    <div class="kpi-content">
                        <h3>Drafts</h3>
                        <div class="kpi-number">{{ $metrics['draft'] }}</div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon-box icon-blue"><i class="fa-solid fa-map"></i></div>
                    <div class="kpi-content">
                        <h3>In Progress</h3>
                        <div class="kpi-number">{{ $metrics['planned'] }}</div>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-icon-box icon-emerald"><i class="fa-solid fa-check-double"></i></div>
                    <div class="kpi-content">
                        <h3>Completed</h3>
                        <div class="kpi-number">{{ $metrics['completed'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. MAIN CONTENT AREA -->
        <div class="content-area dashboard-container">
            
            @if($metrics['total'] > 0)
                <!-- Estimation Overview Stats -->
                <div class="stats-strip">
                    <div class="stat-item">
                        <div class="stat-value">{{ $overview['total_distance'] }} <span style="font-size: 0.85rem; color: #94a3b8;">NM</span></div>
                        <div class="stat-label">Planned Distance</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value text-primary">{{ $overview['with_lines'] }}</div>
                        <div class="stat-label">Projects w/ Lines</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value text-warning">{{ $overview['awaiting_planning'] }}</div>
                        <div class="stat-label">Awaiting Map</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value text-success">{{ $overview['completed_estimation'] }}</div>
                        <div class="stat-label">Cost Estimated</div>
                    </div>
                </div>

                <!-- Attention Area -->
                @if($attention['missing_boundaries'] > 0 || $attention['missing_lines'] > 0 || $attention['missing_parameters'] > 0 || $attention['missing_cost'] > 0)
                    <div class="mb-5">
                        <h3 class="fw-bold mb-3" style="font-size: 1.1rem; color: #0f172a;">Requires Attention</h3>
                        <div class="row g-3">
                            @if($attention['missing_boundaries'] > 0)
                                <div class="col-md-6">
                                    <div class="attention-card warning">
                                        <div class="attention-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                                        <div>
                                            <h4>Missing Boundaries</h4>
                                            <p><strong>{{ $attention['missing_boundaries'] }}</strong> project(s) have no boundaries defined.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($attention['missing_lines'] > 0)
                                <div class="col-md-6">
                                    <div class="attention-card info">
                                        <div class="attention-icon"><i class="fa-solid fa-route"></i></div>
                                        <div>
                                            <h4>Survey Lines Pending</h4>
                                            <p><strong>{{ $attention['missing_lines'] }}</strong> project(s) lack generated lines.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($attention['missing_parameters'] > 0)
                                <div class="col-md-6">
                                    <div class="attention-card warning">
                                        <div class="attention-icon"><i class="fa-solid fa-sliders"></i></div>
                                        <div>
                                            <h4>Missing Parameters</h4>
                                            <p><strong>{{ $attention['missing_parameters'] }}</strong> project(s) need working parameters.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($attention['missing_cost'] > 0)
                                <div class="col-md-6">
                                    <div class="attention-card info">
                                        <div class="attention-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                                        <div>
                                            <h4>Estimation Pending</h4>
                                            <p><strong>{{ $attention['missing_cost'] }}</strong> project(s) haven't completed costing.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Projects Table -->
                <div class="table-card">
                    <div class="table-header-controls">
                        <h3 class="table-title">Recent Projects</h3>
                        
                        <form method="GET" action="{{ route('projects.index') }}" class="d-flex flex-wrap gap-2">
                            <input type="text" name="search" class="premium-input" placeholder="Search projects..." value="{{ request('search') }}" style="width: 220px;">
                            <select name="status" class="premium-input" style="width: 140px;" onchange="this.form.submit()">
                                <option value="">All Statuses</option>
                                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @if(request('search') || request('status'))
                                <a href="{{ route('projects.index') }}" class="btn btn-light btn-sm px-3 align-self-center" style="border-radius: 6px;">Clear</a>
                            @endif
                            <button type="submit" class="d-none">Filter</button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Project Details</th>
                                    <th>Client</th>
                                    <th>Status</th>
                                    <th>Last Updated</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projects as $project)
                                    <tr>
                                        <td>
                                            <span style="font-family: 'Courier New', monospace; font-size: 0.8rem; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 4px 8px; border-radius: 4px;">
                                                {{ $project->project_code ?? 'PRJ-' . str_pad($project->id, 4, '0', STR_PAD_LEFT) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #0f172a; margin-bottom: 2px;">{{ $project->name }}</div>
                                            <div style="font-size: 0.8rem; color: #64748b;">
                                                <i class="fa-solid fa-location-dot me-1" style="color: #cbd5e1;"></i> {{ $project->location ?? 'No location specified' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-weight: 500;">{{ $project->client?->name ?? $project->getRawOriginal('client') ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($project->status === 'draft')
                                                <span class="badge-premium badge-draft">Draft</span>
                                            @elseif($project->status === 'planned')
                                                <span class="badge-premium badge-planned">In Progress</span>
                                            @else
                                                <span class="badge-premium badge-completed">Completed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-size: 0.85rem; color: #475569;">{{ $project->updated_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="text-end text-nowrap">
                                            <a href="{{ route('projects.show', $project->id) }}" class="action-btn" title="View Overview">
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </a>
                                            <a href="{{ route('projects.edit', $project->id) }}" class="action-btn" title="Edit Details">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="action-btn delete btn-delete-action" title="Delete Project">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted mb-2">No projects match your search criteria.</div>
                                            <a href="{{ route('projects.index') }}" class="text-primary text-decoration-none" style="font-size: 0.9rem; font-weight: 500;">Clear Filters</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($projects->hasPages())
                        <div class="p-3 border-top bg-light d-flex justify-content-center">
                            {{ $projects->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>

            @else
                <!-- Empty State -->
                <div class="premium-empty">
                    <i class="fa-solid fa-layer-group empty-icon"></i>
                    <h3 class="fw-bold mb-3" style="color: #0f172a;">Your workspace is empty</h3>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 500px; font-size: 0.95rem;">
                        Create your first survey estimation project to start plotting boundaries, calculating survey lines, and generating detailed cost analyses.
                    </p>
                    <a href="{{ route('projects.create') }}" class="btn-premium px-4">
                        <i class="fa-solid fa-plus"></i> Create New Project
                    </a>
                </div>
            @endif
        </div>
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
                        cancelButtonColor: '#f1f5f9',
                        customClass: {
                            cancelButton: 'text-dark',
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
