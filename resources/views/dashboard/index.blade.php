<x-app-layout containerClass="w-full px-4 py-6 sm:px-8 sm:py-8">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/ises-dashboard.css') }}?v={{ filemtime(public_path('css/ises-dashboard.css')) }}">
    @endpush

    <div class="ises-dashboard">
        <header class="ises-dashboard-header">
            <div>
                <p class="ises-dashboard-eyebrow">Operations / overview</p>
                <h1>Dashboard</h1>
                <p class="ises-dashboard-intro">A clear view of your survey workload, project progress, and next actions.</p>
            </div>
            <div class="ises-dashboard-header-actions">
                <span class="ises-dashboard-date"><i class="fa-regular fa-calendar"></i>{{ now()->format('d M Y') }}</span>
                <a class="ises-dashboard-primary" href="{{ route('projects.create') }}"><i class="fa-solid fa-plus"></i> New project</a>
            </div>
        </header>

        @if(session('success'))
            <div class="ises-dashboard-feedback" role="status"><i class="fa-solid fa-circle-check"></i>{{ session('success') }}</div>
        @endif

        <section class="ises-dashboard-quick-links" aria-label="Other workspaces">
            <a href="{{ route('quotations.history') }}"><span><i class="fa-solid fa-file-invoice-dollar"></i></span><div><strong>Quotation history</strong><small>Review saved estimates</small></div><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            <a href="{{ route('admin.equipment.index') }}"><span><i class="fa-solid fa-plane-up"></i></span><div><strong>Drone equipment</strong><small>Manage drones and cameras</small></div><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            <a href="{{ route('projects.modeling.modules.index') }}"><span><i class="fa-solid fa-database"></i></span><div><strong>Modelling catalogue</strong><small>Maintain services and rates</small></div><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
        </section>

        <section class="ises-dashboard-metrics" aria-label="Portfolio summary">
            <a class="ises-dashboard-metric ises-dashboard-metric-primary ises-dashboard-metric-link" href="{{ route('projects.index') }}" aria-label="View all projects">
                <span class="ises-dashboard-metric-label">Total projects</span>
                <strong>{{ $metrics['total'] }}</strong>
                <span class="ises-dashboard-metric-note">Across your workspace</span>
            </a>
            <a class="ises-dashboard-metric ises-dashboard-metric-link" href="{{ route('projects.index', ['status' => 'planned']) }}" aria-label="View planned projects">
                <span class="ises-dashboard-metric-label">Planned</span>
                <strong>{{ $metrics['planned'] }}</strong>
                <span class="ises-dashboard-metric-note">Ready for next steps</span>
            </a>
            <a class="ises-dashboard-metric ises-dashboard-metric-link" href="{{ route('projects.index', ['survey_status' => 'Mapped']) }}" aria-label="View projects with mapped areas">
                <span class="ises-dashboard-metric-label">Mapped projects</span>
                <strong>{{ $metrics['mapped'] }}</strong>
                <span class="ises-dashboard-metric-note">Projects with mapped surveys</span>
            </a>
            <a class="ises-dashboard-metric ises-dashboard-metric-link" href="{{ route('projects.index', ['has_quotation' => '1']) }}" aria-label="View projects with quotations">
                <span class="ises-dashboard-metric-label">Costed projects</span>
                <strong>{{ $metrics['quotations'] }}</strong>
                <span class="ises-dashboard-metric-note">Projects with saved cost items</span>
            </a>
        </section>

        <section class="ises-dashboard-charts" aria-label="Project analytics">
            <article class="ises-dashboard-section ises-dashboard-chart-card">
                <div class="ises-dashboard-section-heading">
                    <div>
                        <p class="ises-dashboard-eyebrow">Portfolio</p>
                        <h2>Project status</h2>
                        <p>Select a segment to open projects with that status.</p>
                    </div>
                    <a class="ises-dashboard-chart-link" href="{{ route('projects.index') }}">All projects <i class="fa-solid fa-arrow-right"></i></a>
                </div>
                @if($statusBreakdown->isNotEmpty())
                    <div class="ises-dashboard-chart-body">
                        <div class="ises-dashboard-chart-canvas"><canvas id="projectStatusChart" role="img" aria-label="Doughnut chart showing project counts by status"></canvas></div>
                        <ul class="ises-dashboard-chart-legend">
                            @foreach($statusBreakdown as $status)
                                <li><a href="{{ route('projects.index', ['status' => $status['key']]) }}"><span>{{ $status['label'] }}</span><strong>{{ $status['total'] }}</strong></a></li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="ises-dashboard-chart-empty">Project status data will appear here once projects are created.</p>
                @endif
            </article>

            <article class="ises-dashboard-section ises-dashboard-chart-card">
                <div class="ises-dashboard-section-heading">
                    <div>
                        <p class="ises-dashboard-eyebrow">Project mix</p>
                        <h2>By project type</h2>
                        <p>Compare the number of projects in each category.</p>
                    </div>
                    <span class="ises-dashboard-chart-note">Live project records</span>
                </div>
                @if($projectTypeBreakdown->isNotEmpty())
                    <div class="ises-dashboard-chart-body ises-dashboard-type-chart-body">
                        <div class="ises-dashboard-type-chart-canvas"><canvas id="projectTypeChart" role="img" aria-label="Bar chart showing project counts by type"></canvas></div>
                        <ul class="ises-dashboard-chart-legend ises-dashboard-type-legend">
                            @foreach($projectTypeBreakdown as $type)
                                <li><a href="{{ route('projects.index', ['project_type' => $type['key']]) }}"><span>{{ $type['label'] }}</span><strong>{{ $type['total'] }}</strong></a></li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p class="ises-dashboard-chart-empty">Project type data will appear here once projects are created.</p>
                @endif
            </article>
        </section>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
            <script>
                (() => {
                    const statusData = @json($statusBreakdown);
                    const typeData = @json($projectTypeBreakdown);
                    const statusCanvas = document.getElementById('projectStatusChart');
                    const typeCanvas = document.getElementById('projectTypeChart');
                    const palette = ['#2463a6', '#2d8a83', '#83a9cb', '#6d8298', '#c4d4e2', '#8b9c67'];

                    if (window.Chart && statusCanvas && statusData.length) {
                        new Chart(statusCanvas, {
                            type: 'doughnut',
                            data: {
                                labels: statusData.map(item => item.label),
                                datasets: [{ data: statusData.map(item => item.total), backgroundColor: statusData.map((_, index) => palette[index % palette.length]), borderColor: '#ffffff', borderWidth: 3, hoverOffset: 5 }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                cutout: '68%',
                                plugins: {
                                    legend: { display: false },
                                    tooltip: { callbacks: { label: context => ' ' + context.label + ': ' + context.formattedValue + ' projects' } }
                                },
                                onClick: (event, elements) => {
                                    if (!elements.length) return;
                                    const item = statusData[elements[0].index];
                                    window.location.href = new URL({!! json_encode(route('projects.index')) !!} + '?status=' + encodeURIComponent(item.key), window.location.origin).href;
                                }
                            }
                        });
                    }

                    if (window.Chart && typeCanvas && typeData.length) {
                        new Chart(typeCanvas, {
                            type: 'bar',
                            data: {
                                labels: typeData.map(item => item.label),
                                datasets: [{ label: 'Projects', data: typeData.map(item => item.total), backgroundColor: '#4d83b8', hoverBackgroundColor: '#2463a6', borderRadius: 3, maxBarThickness: 36 }]
                            },
                            options: {
                                indexAxis: 'y',
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false },
                                    tooltip: { callbacks: { label: context => ' ' + context.formattedValue + ' projects' } }
                                },
                                scales: {
                                    x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#edf1f5' }, border: { display: false } },
                                    y: { grid: { display: false }, border: { display: false } }
                                },
                                onClick: (event, elements) => {
                                    if (!elements.length) return;
                                    const item = typeData[elements[0].index];
                                    const url = new URL({!! json_encode(route('projects.index')) !!}, window.location.origin);
                                    url.searchParams.set('project_type', item.key || '');
                                    window.location.href = url.href;
                                }
                            }
                        });
                    }
                })();
            </script>
        @endpush

        <section class="ises-dashboard-section ises-dashboard-attention">
            <div class="ises-dashboard-section-heading">
                <div>
                    <p class="ises-dashboard-eyebrow">Next actions</p>
                    <h2>Needs attention</h2>
                    <p>Outstanding planning and estimation steps across active projects.</p>
                </div>
                <span class="ises-dashboard-count">{{ $attentionItems->sum('count') }} open checks</span>
            </div>
            @if($attentionItems->isNotEmpty())
                <div class="ises-dashboard-attention-grid">
                    @foreach($attentionItems as $item)
                        <article class="ises-dashboard-attention-card">
                            <span class="ises-dashboard-attention-icon"><i class="{{ $item['icon'] }}"></i></span>
                            <div class="ises-dashboard-attention-copy">
                                <h3>{{ $item['label'] }}</h3>
                                <p>{{ $item['description'] }}</p>
                            </div>
                            <strong>{{ $item['count'] }}</strong>
                            <a href="{{ route('projects.index', ['attention' => $item['key']]) }}" aria-label="Review projects: {{ $item['label'] }}"><i class="fa-solid fa-arrow-right"></i></a>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="ises-dashboard-empty-attention">
                    <i class="fa-solid fa-circle-check"></i>
                    <div><strong>No outstanding checks</strong><p>There are no planning or estimation checks to show right now.</p></div>
                </div>
            @endif
        </section>

        <section class="ises-dashboard-section ises-dashboard-recent">
            <div class="ises-dashboard-section-heading">
                <div>
                    <p class="ises-dashboard-eyebrow">Your workspace</p>
                    <h2>Recent projects</h2>
                    <p>Return to a project or open the complete register.</p>
                </div>
                <a class="ises-dashboard-secondary" href="{{ route('projects.index') }}">View all projects <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            @if($recentProjects->isNotEmpty())
                <div class="ises-dashboard-table-wrap">
                    <table class="ises-dashboard-table">
                        <thead>
                            <tr><th scope="col">Project</th><th scope="col">Client</th><th scope="col">Type</th><th scope="col">Status</th><th scope="col"><span class="sr-only">Action</span></th></tr>
                        </thead>
                        <tbody>
                            @foreach($recentProjects as $project)
                                <tr>
                                    <td>
                                        <a class="ises-dashboard-project-name" href="{{ route('projects.show', $project->project_Id) }}">{{ $project->name }}</a>
                                        <span class="ises-dashboard-project-code">{{ $project->number }}</span>
                                    </td>
                                    <td>{{ $project->client?->company_name ?? '—' }}</td>
                                    <td><span class="ises-dashboard-type">{{ \App\Models\Project::TYPES[$project->project_type] ?? $project->project_type ?? 'Not set' }}</span></td>
                                    <td><span class="ises-dashboard-status ises-dashboard-status-{{ \Illuminate\Support\Str::slug($project->status ?? 'draft') }}">{{ \Illuminate\Support\Str::headline($project->status ?? 'draft') }}</span></td>
                                    <td class="ises-dashboard-row-action"><a href="{{ route('projects.show', $project->project_Id) }}">Open <i class="fa-solid fa-arrow-up-right-from-square"></i></a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="ises-dashboard-empty-projects">
                    <span class="ises-dashboard-empty-icon"><i class="fa-solid fa-folder-open"></i></span>
                    <h3>No projects yet</h3>
                    <p>Create your first survey project to start planning boundaries, survey lines, and costs.</p>
                    <a class="ises-dashboard-primary" href="{{ route('projects.create') }}"><i class="fa-solid fa-plus"></i> Create project</a>
                </div>
            @endif
        </section>


    </div>
</x-app-layout>
