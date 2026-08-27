<x-app-layout containerClass="px-0">
    <!-- ISES Dashboard Custom Design System -->
    <style>
        body {
            background-color: #ffffff !important; 
        }
        
        .ises-dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 24px;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #0f172a;
        }

        /* Surfaces & Cards */
        .ises-surface {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease-in-out;
        }

        /* Typography */
        .ises-text-primary { color: #0f172a; }
        .ises-text-secondary { color: #475569; }
        .ises-text-tertiary { color: #64748b; }
        
        /* Buttons */
        .ises-btn-primary {
            background-color: #0ea5e9;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(14, 165, 233, 0.2);
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .ises-btn-primary:hover {
            background-color: #0284c7;
            color: white;
            box-shadow: 0 4px 6px rgba(14, 165, 233, 0.25);
            transform: translateY(-1px);
        }
        
        .ises-btn-secondary {
            background-color: #f8fafc;
            color: #334155;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .ises-btn-secondary:hover {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        /* Action Buttons */
        .ises-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.2s;
            margin-left: 6px;
            text-decoration: none;
        }
        .ises-action-btn:hover {
            background: #f1f5f9;
            color: #0ea5e9;
            border-color: #cbd5e1;
        }
        .ises-action-btn.delete:hover {
            color: #ef4444;
        }

        /* KPI Cards */
        .ises-kpi-card {
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .ises-kpi-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .ises-kpi-icon.blue { background: #f0f9ff; color: #0ea5e9; }
        .ises-kpi-icon.amber { background: #fffbeb; color: #f59e0b; }
        .ises-kpi-icon.green { background: #f0fdf4; color: #10b981; }
        .ises-kpi-icon.navy { background: #f8fafc; color: #334155; }
        
        .ises-kpi-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1.1;
            color: #0f172a;
        }
        .ises-kpi-label {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-top: 4px;
        }

        /* Overview Panel */
        .ises-overview-panel {
            background: #0f172a;
            color: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
        }
        .ises-overview-stat {
            border-left: 2px solid rgba(255,255,255,0.1);
            padding-left: 16px;
        }
        .ises-overview-stat:first-child {
            border-left: none;
            padding-left: 0;
        }

        /* Requires Attention */
        .ises-attention-alert {
            background: #fff7ed;
            border-left: 4px solid #ea580c;
            border-radius: 8px;
            padding: 16px 20px;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        
        /* Table */
        .ises-table-container {
            overflow-x: auto;
        }
        .ises-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .ises-table th {
            text-align: left;
            padding: 16px 24px;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .ises-table th:first-child { border-top-left-radius: 16px; }
        .ises-table th:last-child { border-top-right-radius: 16px; text-align: right; }
        
        .ises-table td {
            padding: 16px 24px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
            font-size: 0.9rem;
        }
        .ises-table tbody tr:hover td {
            background-color: #f8fafc;
        }
        
        /* Badges */
        .ises-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        .ises-badge-draft { background: #fef3c7; color: #b45309; }
        .ises-badge-planned { background: #e0f2fe; color: #0369a1; }
        .ises-badge-completed { background: #dcfce7; color: #15803d; }
        .ises-badge-neutral { background: #f1f5f9; color: #475569; }

        /* Form Inputs */
        .ises-input {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.9rem;
            color: #334155;
            background: #ffffff;
            transition: all 0.2s;
        }
        .ises-input:focus {
            outline: none;
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }
        
        .empty-state {
            padding: 64px 24px;
            text-align: center;
        }
        .empty-state-icon {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 24px;
        }
    </style>

    <div class="ises-dashboard">
        
        <!-- HEADER SECTION -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 mt-4">
            <div class="mb-3 mb-md-0">
                <h1 class="ises-text-primary mb-1 fw-bold" style="font-size: 1.8rem; letter-spacing: -0.5px;">ISES Dashboard</h1>
                <p class="ises-text-secondary mb-0" style="font-size: 1rem;">SBES Survey Estimation Management</p>
            </div>
            <div>
                <a href="{{ route('projects.create') }}" class="ises-btn-primary">
                    <i class="fa-solid fa-plus"></i> New Project
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="ises-surface mb-4" style="background: #f0fdf4; border-color: #bbf7d0; padding: 16px 24px; display: flex; align-items: center; gap: 12px; color: #15803d; font-weight: 500;">
                <i class="fa-solid fa-circle-check fs-5"></i>
                {{ session('success') }}
            </div>
        @endif

        <!-- 1. KPI SUMMARY SECTION -->
        <div class="row g-4 mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="ises-surface ises-kpi-card">
                    <div class="ises-kpi-icon navy"><i class="fa-solid fa-folder-tree"></i></div>
                    <div>
                        <div class="ises-kpi-value">{{ $metrics['total'] }}</div>
                        <div class="ises-kpi-label">Total SBES</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="ises-surface ises-kpi-card">
                    <div class="ises-kpi-icon amber"><i class="fa-solid fa-file-pen"></i></div>
                    <div>
                        <div class="ises-kpi-value">{{ $metrics['draft'] }}</div>
                        <div class="ises-kpi-label">Draft</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="ises-surface ises-kpi-card">
                    <div class="ises-kpi-icon blue"><i class="fa-solid fa-map"></i></div>
                    <div>
                        <div class="ises-kpi-value">{{ $metrics['planned'] }}</div>
                        <div class="ises-kpi-label">In Progress</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="ises-surface ises-kpi-card">
                    <div class="ises-kpi-icon green"><i class="fa-solid fa-check-double"></i></div>
                    <div>
                        <div class="ises-kpi-value">{{ $metrics['completed'] }}</div>
                        <div class="ises-kpi-label">Completed</div>
                    </div>
                </div>
            </div>
        </div>

        @if($metrics['total'] > 0)
            <!-- 2. ESTIMATION OVERVIEW -->
            <div class="ises-overview-panel mb-5">
                <h6 class="text-uppercase fw-bold mb-4" style="color: #94a3b8; letter-spacing: 1px; font-size: 0.75rem;">Estimation Overview</h6>
                <div class="row g-4">
                    <div class="col-md-3 ises-overview-stat">
                        <div style="font-size: 1.8rem; font-weight: 700; color: #fff;">{{ $overview['total_distance'] }} <span style="font-size: 1rem; color: #94a3b8; font-weight: 500;">NM</span></div>
                        <div style="color: #cbd5e1; font-size: 0.85rem; margin-top: 4px;">Total Survey Distance Planned</div>
                    </div>
                    <div class="col-md-3 ises-overview-stat">
                        <div style="font-size: 1.8rem; font-weight: 700; color: #38bdf8;">{{ $overview['with_lines'] }}</div>
                        <div style="color: #cbd5e1; font-size: 0.85rem; margin-top: 4px;">Projects w/ Survey Lines</div>
                    </div>
                    <div class="col-md-3 ises-overview-stat">
                        <div style="font-size: 1.8rem; font-weight: 700; color: #f472b6;">{{ $overview['awaiting_planning'] }}</div>
                        <div style="color: #cbd5e1; font-size: 0.85rem; margin-top: 4px;">Awaiting Map Planning</div>
                    </div>
                    <div class="col-md-3 ises-overview-stat">
                        <div style="font-size: 1.8rem; font-weight: 700; color: #34d399;">{{ $overview['completed_estimation'] }}</div>
                        <div style="color: #cbd5e1; font-size: 0.85rem; margin-top: 4px;">Completed Cost Estimations</div>
                    </div>
                </div>
            </div>

            <!-- 3. PROJECT WORKFLOW / ATTENTION AREA -->
            @if($attention['missing_boundaries'] > 0 || $attention['missing_lines'] > 0 || $attention['missing_parameters'] > 0 || $attention['missing_cost'] > 0)
                <div class="mb-5">
                    <h5 class="ises-text-primary fw-bold mb-3" style="font-size: 1.1rem;">Requires Attention</h5>
                    <div class="row g-3">
                        @if($attention['missing_boundaries'] > 0)
                        <div class="col-md-6">
                            <div class="ises-attention-alert">
                                <i class="fa-solid fa-triangle-exclamation fs-4 text-warning"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 ises-text-primary">Missing Boundaries</h6>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem;"><strong>{{ $attention['missing_boundaries'] }}</strong> project(s) have no survey boundaries defined.</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($attention['missing_lines'] > 0)
                        <div class="col-md-6">
                            <div class="ises-attention-alert">
                                <i class="fa-solid fa-route fs-4 text-warning"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 ises-text-primary">Survey Lines Pending</h6>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem;"><strong>{{ $attention['missing_lines'] }}</strong> project(s) have boundaries but no generated survey lines.</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($attention['missing_parameters'] > 0)
                        <div class="col-md-6">
                            <div class="ises-attention-alert">
                                <i class="fa-solid fa-sliders fs-4 text-warning"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 ises-text-primary">Missing Parameters</h6>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem;"><strong>{{ $attention['missing_parameters'] }}</strong> project(s) need working hours and weather parameters.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($attention['missing_cost'] > 0)
                        <div class="col-md-6">
                            <div class="ises-attention-alert">
                                <i class="fa-solid fa-file-invoice-dollar fs-4 text-warning"></i>
                                <div>
                                    <h6 class="fw-bold mb-1 ises-text-primary">Cost Estimation Pending</h6>
                                    <p class="mb-0 text-muted" style="font-size: 0.85rem;"><strong>{{ $attention['missing_cost'] }}</strong> project(s) have not completed the cost estimation phase.</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- 4. RECENT PROJECTS & FILTERING -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-3 mt-5">
                <h5 class="ises-text-primary fw-bold mb-3 mb-md-0" style="font-size: 1.2rem;">Recent Projects</h5>
                
                <form method="GET" action="{{ route('projects.index') }}" class="d-flex gap-2">
                    <input type="text" name="search" class="ises-input" placeholder="Search code, name, client..." value="{{ request('search') }}" style="width: 250px;">
                    <select name="status" class="ises-input" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="planned" {{ request('status') === 'planned' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @if(request('search') || request('status'))
                        <a href="{{ route('projects.index') }}" class="ises-btn-secondary">Clear</a>
                    @else
                        <button type="submit" class="ises-btn-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
                    @endif
                </form>
            </div>

            <div class="ises-surface mb-4">
                <div class="ises-table-container">
                    <table class="ises-table">
                        <thead>
                            <tr>
                                <th>Project Code</th>
                                <th>Project Name</th>
                                <th>Client</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Updated</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $project)
                                <tr>
                                    <td>
                                        <span class="ises-badge ises-badge-neutral font-monospace">
                                            {{ $project->project_code ?? 'PRJ-' . str_pad($project->id, 4, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold ises-text-primary">{{ $project->name }}</div>
                                    </td>
                                    <td>{{ $project->client ?? '-' }}</td>
                                    <td>{{ $project->location ?? '-' }}</td>
                                    <td>
                                        @if($project->status === 'draft')
                                            <span class="ises-badge ises-badge-draft">Draft</span>
                                        @elseif($project->status === 'planned')
                                            <span class="ises-badge ises-badge-planned">In Progress</span>
                                        @else
                                            <span class="ises-badge ises-badge-completed">Completed</span>
                                        @endif
                                    </td>
                                    <td class="ises-text-tertiary" style="font-size: 0.85rem;">
                                        {{ $project->updated_at->diffForHumans() }}
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <a href="{{ route('projects.show', $project->id) }}" class="ises-action-btn" title="Map Planning">
                                            <i class="fa-solid fa-map-location-dot"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project->id) }}" class="ises-action-btn" title="Edit Project">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="ises-action-btn delete btn-delete-action" title="Delete Project">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        No projects match your search criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($projects->hasPages())
                    <div class="p-3 border-top d-flex justify-content-center">
                        {{ $projects->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

        @else
            <!-- 5. EMPTY STATE -->
            <div class="ises-surface empty-state mt-4">
                <i class="fa-solid fa-folder-open empty-state-icon"></i>
                <h3 class="ises-text-primary fw-bold mb-3">No SBES projects yet.</h3>
                <p class="ises-text-secondary mb-4 mx-auto" style="max-width: 500px; font-size: 1.05rem; line-height: 1.6;">
                    Create your first survey estimation project to begin planning survey boundaries, generating survey lines, and accurately estimating project durations and costs.
                </p>
                <a href="{{ route('projects.create') }}" class="ises-btn-primary" style="padding: 12px 28px; font-size: 1.05rem;">
                    <i class="fa-solid fa-plus me-2"></i> Create New Project
                </a>
            </div>
        @endif

    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert Delete Confirmation
            const deleteButtons = document.querySelectorAll('.btn-delete-action');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Delete this project?',
                        text: "This will permanently delete all map data, parameter settings, and cost estimations. This action cannot be undone.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, delete it!'
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
