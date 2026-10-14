<x-app-layout containerClass="w-full px-4 py-6 sm:px-8 sm:py-8">
    <link rel="stylesheet" href="{{ asset('css/ises-dashboard.css') }}?v={{ filemtime(public_path('css/ises-dashboard.css')) }}">

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
            <a class="ises-dashboard-metric ises-dashboard-metric-link" href="{{ route('projects.index', ['status' => 'draft']) }}" aria-label="View draft projects">
                <span class="ises-dashboard-metric-label">Draft</span>
                <strong>{{ $metrics['draft'] }}</strong>
                <span class="ises-dashboard-metric-note">Still being prepared</span>
            </a>
            <a class="ises-dashboard-metric ises-dashboard-metric-link" href="{{ route('projects.index', ['status' => 'planned']) }}" aria-label="View planned projects">
                <span class="ises-dashboard-metric-label">Planned</span>
                <strong>{{ $metrics['planned'] }}</strong>
                <span class="ises-dashboard-metric-note">Ready for next steps</span>
            </a>
            <a class="ises-dashboard-metric ises-dashboard-metric-link" href="{{ route('projects.index', ['survey_status' => 'Mapped']) }}" aria-label="View projects with mapped areas">
                <span class="ises-dashboard-metric-label">Mapped areas</span>
                <strong>{{ $metrics['mapped'] }}</strong>
                <span class="ises-dashboard-metric-note">Projects with mapped surveys</span>
            </a>
            <a class="ises-dashboard-metric ises-dashboard-metric-link" href="{{ route('projects.index', ['has_quotation' => '1']) }}" aria-label="View projects with quotations">
                <span class="ises-dashboard-metric-label">Quotations</span>
                <strong>{{ $metrics['quotations'] }}</strong>
                <span class="ises-dashboard-metric-note">Projects with cost items</span>
            </a>
        </section>

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
                            <a href="{{ route('projects.index') }}" aria-label="Review projects: {{ $item['label'] }}"><i class="fa-solid fa-arrow-right"></i></a>
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
                            <tr><th>Project</th><th>Client</th><th>Type</th><th>Status</th><th><span class="sr-only">Action</span></th></tr>
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
