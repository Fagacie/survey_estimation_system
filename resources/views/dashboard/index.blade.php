<x-app-layout containerClass="w-full px-4 py-6 sm:px-8 sm:py-8">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/ises-dashboard.css') }}?v={{ filemtime(public_path('css/ises-dashboard.css')) }}">
    @endpush

    <div class="ises-dashboard">
        <!-- Dashboard Header -->
        <header class="ises-dashboard-header">
            <div class="ises-dashboard-header-title">
                <h1>Operations Overview</h1>
                <p>Survey planning, workload progress, and technical estimation metrics.</p>
            </div>
            <div class="ises-dashboard-header-actions">
                <nav class="ises-dashboard-utility-links" aria-label="Quick links">
                    <a class="ises-dashboard-utility-link" href="{{ route('quotations.history') }}" title="Quotation history">
                        <i class="fa-solid fa-file-invoice-dollar" aria-hidden="true"></i> Quotations
                    </a>
                    <a class="ises-dashboard-utility-link" href="{{ route('admin.equipment.index') }}" title="Drone and sensor equipment">
                        <i class="fa-solid fa-plane-up" aria-hidden="true"></i> Drone gear
                    </a>
                    <a class="ises-dashboard-utility-link" href="{{ route('projects.modeling.modules.index') }}" title="Modelling service catalog">
                        <i class="fa-solid fa-database" aria-hidden="true"></i> Modelling
                    </a>
                </nav>
                <a class="ises-dashboard-primary-btn" href="{{ route('projects.create') }}">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i> New project
                </a>
            </div>
        </header>

        @if(session('success'))
            <div class="ises-dashboard-feedback" role="status">
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i> {{ session('success') }}
            </div>
        @endif

        <!-- 1. Primary KPI Metrics -->
        <section class="ises-dashboard-metrics" aria-label="Portfolio summary">
            <a class="ises-dashboard-metric" href="{{ route('projects.index') }}" aria-label="View all projects">
                <div class="ises-dashboard-metric-top">
                    <span class="ises-dashboard-metric-label">Total Projects</span>
                    <span class="ises-dashboard-metric-icon ises-icon-blue"><i class="fa-solid fa-folder-open" aria-hidden="true"></i></span>
                </div>
                <strong>{{ $metrics['total'] }}</strong>
                <div class="ises-dashboard-metric-bottom">
                    <span class="ises-dashboard-metric-note">Across your workspace</span>
                    <span class="ises-dashboard-metric-arrow"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span>
                </div>
            </a>

            <a class="ises-dashboard-metric" href="{{ route('projects.index', ['status' => 'planned']) }}" aria-label="View planned projects">
                <div class="ises-dashboard-metric-top">
                    <span class="ises-dashboard-metric-label">Planned</span>
                    <span class="ises-dashboard-metric-icon ises-icon-amber"><i class="fa-solid fa-chart-line" aria-hidden="true"></i></span>
                </div>
                <strong>{{ $metrics['planned'] }}</strong>
                <div class="ises-dashboard-metric-bottom">
                    <span class="ises-dashboard-metric-note">Ready for survey lines</span>
                    <span class="ises-dashboard-metric-arrow"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span>
                </div>
            </a>

            <a class="ises-dashboard-metric" href="{{ route('projects.index', ['survey_status' => 'Mapped']) }}" aria-label="View projects with mapped areas">
                <div class="ises-dashboard-metric-top">
                    <span class="ises-dashboard-metric-label">Mapped Areas</span>
                    <span class="ises-dashboard-metric-icon ises-icon-teal"><i class="fa-solid fa-draw-polygon" aria-hidden="true"></i></span>
                </div>
                <strong>{{ $metrics['mapped'] }}</strong>
                <div class="ises-dashboard-metric-bottom">
                    <span class="ises-dashboard-metric-note">Defined survey boundaries</span>
                    <span class="ises-dashboard-metric-arrow"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span>
                </div>
            </a>

            <a class="ises-dashboard-metric" href="{{ route('projects.index', ['has_quotation' => '1']) }}" aria-label="View projects with quotations">
                <div class="ises-dashboard-metric-top">
                    <span class="ises-dashboard-metric-label">Costed Projects</span>
                    <span class="ises-dashboard-metric-icon ises-icon-indigo"><i class="fa-solid fa-calculator" aria-hidden="true"></i></span>
                </div>
                <strong>{{ $metrics['quotations'] }}</strong>
                <div class="ises-dashboard-metric-bottom">
                    <span class="ises-dashboard-metric-note">Projects with cost estimates</span>
                    <span class="ises-dashboard-metric-arrow"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></span>
                </div>
            </a>
        </section>

        <!-- 2. Analytics Row (Portfolio Status & Project Mix) -->
        <section class="ises-dashboard-charts" aria-label="Project analytics">
            <!-- Portfolio Status Doughnut -->
            <article class="ises-dashboard-card">
                <div class="ises-dashboard-card-header">
                    <h2>Portfolio Status</h2>
                    <a class="ises-dashboard-card-action" href="{{ route('projects.index') }}">
                        All projects <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
                @if($statusBreakdown->isNotEmpty())
                    <div class="ises-dashboard-chart-body">
                        <div class="ises-dashboard-chart-canvas-wrap">
                            <canvas id="projectStatusChart" role="img" aria-label="Chart showing project status distribution"></canvas>
                            <div class="ises-donut-center-overlay" aria-hidden="true">
                                <strong>{{ $metrics['total'] }}</strong>
                                <span>Projects</span>
                            </div>
                        </div>
                        <ul class="ises-dashboard-status-list">
                            @php
                                $statusColors = [
                                    'draft' => '#d97706',
                                    'planned' => '#1d518a',
                                    'completed' => '#10b981',
                                    'active' => '#2463a6',
                                    'in_progress' => '#0d9488',
                                ];
                                $totalProjects = max(1, $metrics['total']);
                            @endphp
                            @foreach($statusBreakdown as $status)
                                @php
                                    $dotColor = $statusColors[strtolower($status['key'])] ?? '#64748b';
                                    $pct = round(($status['total'] / $totalProjects) * 100);
                                @endphp
                                <li>
                                    <a class="ises-dashboard-status-item" href="{{ route('projects.index', ['status' => $status['key']]) }}">
                                        <span class="ises-status-dot-wrap">
                                            <span class="ises-status-dot" style="background-color: {{ $dotColor }};"></span>
                                            {{ $status['label'] }}
                                        </span>
                                        <span class="ises-status-count-wrap">
                                            <strong>{{ $status['total'] }}</strong>
                                            <span class="ises-status-pct">{{ $pct }}%</span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <div style="padding: 2rem; text-align: center; color: var(--dash-muted);">
                        No projects recorded yet.
                    </div>
                @endif
            </article>

            <!-- Project Type Mix Progress Track -->
            <article class="ises-dashboard-card">
                <div class="ises-dashboard-card-header">
                    <h2>Project Mix</h2>
                    <span style="font-size: 0.74rem; color: var(--dash-muted); font-weight: 600;">By category</span>
                </div>
                @if($projectTypeBreakdown->isNotEmpty())
                    @php
                        $maxType = max(1, $projectTypeBreakdown->max('total'));
                    @endphp
                    <div class="ises-dashboard-type-list">
                        @foreach($projectTypeBreakdown as $type)
                            @php
                                $fillPct = round(($type['total'] / $maxType) * 100);
                            @endphp
                            <a class="ises-type-row" href="{{ route('projects.index', ['project_type' => $type['key']]) }}">
                                <div class="ises-type-row-header">
                                    <span class="ises-type-name">{{ $type['label'] }}</span>
                                    <span class="ises-type-count">{{ $type['total'] }} {{ Str::plural('project', $type['total']) }}</span>
                                </div>
                                <div class="ises-type-progress-track">
                                    <div class="ises-type-progress-fill" style="width: {{ $fillPct }}%;"></div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div style="padding: 2rem; text-align: center; color: var(--dash-muted);">
                        No project type data recorded yet.
                    </div>
                @endif
            </article>
        </section>

        <!-- 3. Commercial & Quotation Overview -->
        <section class="ises-dashboard-card ises-dashboard-financial" aria-label="Commercial overview">
            <div class="ises-dashboard-card-header">
                <div>
                    <h2>Commercial Overview</h2>
                </div>
                <a class="ises-dashboard-card-action" href="{{ route('quotations.history') }}">
                    Quotation history <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="ises-dashboard-financial-grid">
                <div class="ises-dashboard-financial-chip">
                    <span>Total Quoted Value</span>
                    <strong>MYR {{ number_format($quotationStats['total_value'], 2) }}</strong>
                    <small>{{ number_format($quotationStats['count']) }} saved quotations</small>
                </div>
                <div class="ises-dashboard-financial-chip">
                    <span>Average Quotation</span>
                    <strong>MYR {{ number_format($quotationStats['average_value'], 2) }}</strong>
                    <small>Average per estimation</small>
                </div>
                <div class="ises-dashboard-financial-chip">
                    <span>This Month</span>
                    <strong>MYR {{ number_format($quotationStats['this_month_value'], 2) }}</strong>
                    <small>{{ number_format($quotationStats['this_month_count']) }} created this month</small>
                </div>
            </div>

            <div class="ises-dashboard-quotation-chart-container">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="font-size: 0.82rem; font-weight: 700; color: var(--dash-ink-secondary);">Quotation Activity (Last 6 Months)</span>
                    <span style="font-size: 0.72rem; color: var(--dash-muted); font-weight: 600;">MYR / Monthly Volume</span>
                </div>
                <div class="ises-dashboard-quotation-chart-wrap">
                    <canvas id="quotationTrendChart" role="img" aria-label="Quotation value trend over past six months"></canvas>
                </div>
            </div>
        </section>

        <!-- 4. Operational Bottom Row: Action Queue & Recent Projects -->
        <section class="ises-dashboard-bottom-grid">
            <!-- Operational Action Queue -->
            <article class="ises-dashboard-card">
                <div class="ises-dashboard-card-header">
                    <h2>Operational Queue</h2>
                    <span style="font-size: 0.74rem; font-weight: 700; color: #475569; background: #f1f5f9; padding: 0.2rem 0.55rem; border-radius: 999px;">
                        {{ $attentionItems->sum('count') }} pending
                    </span>
                </div>

                @if($attentionItems->sum('count') > 0)
                    <div class="ises-dashboard-attention-list">
                        @foreach($attentionItems as $item)
                            <a class="ises-dashboard-attention-card" href="{{ route('projects.index', ['attention' => $item['key']]) }}">
                                <div class="ises-dashboard-attention-left">
                                    <span class="ises-dashboard-attention-icon">
                                        <i class="{{ $item['icon'] }}" aria-hidden="true"></i>
                                    </span>
                                    <div class="ises-dashboard-attention-copy">
                                        <h3>{{ $item['label'] }}</h3>
                                        <p>{{ $item['description'] }}</p>
                                    </div>
                                </div>
                                <span class="ises-dashboard-attention-badge {{ $item['count'] > 0 ? 'has-count' : '' }}">
                                    {{ $item['count'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="ises-dashboard-empty-attention">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        <div>
                            <strong style="display: block; color: #15803d; font-size: 0.88rem;">All projects up to date</strong>
                            <span style="color: var(--dash-muted); font-size: 0.76rem;">No outstanding survey planning or estimation checks.</span>
                        </div>
                    </div>
                @endif
            </article>

            <!-- Recent Projects Register -->
            <article class="ises-dashboard-card">
                <div class="ises-dashboard-card-header">
                    <h2>Recent Projects</h2>
                    <a class="ises-dashboard-card-action" href="{{ route('projects.index') }}">
                        View register <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                @if($recentProjects->isNotEmpty())
                    <div class="ises-dashboard-table-wrap">
                        <table class="ises-dashboard-table">
                            <thead>
                                <tr>
                                    <th scope="col">Project</th>
                                    <th scope="col">Client</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" style="text-align: right;"><span class="sr-only">Action</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProjects as $project)
                                    @php
                                        $statusClass = 'ises-status-pill-' . strtolower($project->status ?? 'draft');
                                        $clientName = $project->client?->company_name ?? 'Internal Project';
                                        $initials = collect(preg_split('/\s+/', $clientName))->filter()->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="ises-project-cell">
                                                <a href="{{ route('projects.show', $project->project_Id) }}">{{ $project->name }}</a>
                                                <span class="ises-project-code">{{ $project->number }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="ises-client-cell">
                                                <span class="ises-client-avatar">{{ $initials ?: '—' }}</span>
                                                <span>{{ Str::limit($clientName, 26) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="ises-status-pill {{ $statusClass }}">
                                                <span class="ises-pill-dot"></span>
                                                {{ Str::headline($project->status ?? 'Draft') }}
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <a class="ises-table-action-link" href="{{ route('projects.show', $project->project_Id) }}">
                                                Open <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.65rem;" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="padding: 2.5rem; text-align: center; color: var(--dash-muted);">
                        <i class="fa-solid fa-folder-open" style="font-size: 1.8rem; margin-bottom: 0.5rem; color: #cbd5e1; display: block;" aria-hidden="true"></i>
                        <h3 style="font-size: 0.95rem; margin-bottom: 0.25rem; color: var(--dash-ink);">No projects yet</h3>
                        <p style="font-size: 0.78rem; margin-bottom: 1rem;">Create your first survey project to start planning boundaries and costs.</p>
                        <a class="ises-dashboard-primary-btn" style="display: inline-flex;" href="{{ route('projects.create') }}">Create project</a>
                    </div>
                @endif
            </article>
        </section>
    </div>

    <!-- Chart.js Scripts -->
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // 1. Portfolio Status Doughnut Chart
                const statusData = @json($statusBreakdown);
                const statusCanvas = document.getElementById('projectStatusChart');

                if (window.Chart && statusCanvas && statusData.length) {
                    const statusColorsMap = {
                        'draft': '#d97706',
                        'planned': '#1d518a',
                        'completed': '#10b981',
                        'active': '#2463a6',
                        'in_progress': '#0d9488'
                    };
                    const fallbackColors = ['#1d518a', '#0d9488', '#d97706', '#64748b', '#6366f1'];
                    const bgColors = statusData.map((item, idx) => statusColorsMap[item.key.toLowerCase()] || fallbackColors[idx % fallbackColors.length]);

                    new Chart(statusCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: statusData.map(item => item.label),
                            datasets: [{
                                data: statusData.map(item => item.total),
                                backgroundColor: bgColors,
                                borderWidth: 2,
                                borderColor: '#ffffff',
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '74%',
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    padding: 10,
                                    boxPadding: 4,
                                    usePointStyle: true,
                                    callbacks: {
                                        label: context => ` ${context.label}: ${context.formattedValue} projects`
                                    }
                                }
                            },
                            onClick: (event, elements) => {
                                if (!elements.length) return;
                                const item = statusData[elements[0].index];
                                window.location.href = new URL({!! json_encode(route('projects.index')) !!} + '?status=' + encodeURIComponent(item.key), window.location.origin).href;
                            }
                        }
                    });
                }

                // 2. Quotation Value Trend Area Chart
                const monthlyCanvas = document.getElementById('quotationTrendChart');
                const monthlyData = @json($quotationMonthly);

                if (window.Chart && monthlyCanvas && monthlyData.length) {
                    const ctx = monthlyCanvas.getContext('2d');
                    let gradient = null;
                    if (ctx) {
                        gradient = ctx.createLinearGradient(0, 0, 0, 240);
                        gradient.addColorStop(0, 'rgba(36, 99, 166, 0.28)');
                        gradient.addColorStop(1, 'rgba(36, 99, 166, 0.00)');
                    }

                    new Chart(monthlyCanvas, {
                        type: 'line',
                        data: {
                            labels: monthlyData.map(row => row.label),
                            datasets: [
                                {
                                    label: 'Quoted Value (MYR)',
                                    data: monthlyData.map(row => row.value),
                                    borderColor: '#1d518a',
                                    borderWidth: 2.2,
                                    fill: true,
                                    backgroundColor: gradient || 'rgba(36, 99, 166, 0.08)',
                                    tension: 0.35,
                                    pointBackgroundColor: '#ffffff',
                                    pointBorderColor: '#1d518a',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    pointHoverRadius: 6,
                                    yAxisID: 'yValue'
                                },
                                {
                                    type: 'bar',
                                    label: 'Quotations Created',
                                    data: monthlyData.map(row => row.count),
                                    backgroundColor: 'rgba(148, 163, 184, 0.35)',
                                    hoverBackgroundColor: 'rgba(148, 163, 184, 0.55)',
                                    borderRadius: 4,
                                    maxBarThickness: 24,
                                    yAxisID: 'yCount'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                    align: 'end',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 6,
                                        font: { size: 11, weight: '600' },
                                        padding: 14
                                    }
                                },
                                tooltip: {
                                    padding: 10,
                                    boxPadding: 4,
                                    callbacks: {
                                        label: context => {
                                            if (context.dataset.yAxisID === 'yValue') {
                                                return ` Value: MYR ${Number(context.raw).toLocaleString('en-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                                            }
                                            return ` Count: ${context.raw} quotations`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { size: 11 } }
                                },
                                yValue: {
                                    type: 'linear',
                                    position: 'left',
                                    beginAtZero: true,
                                    grid: { color: '#f1f5f9' },
                                    ticks: {
                                        font: { size: 11 },
                                        callback: val => 'MYR ' + Number(val).toLocaleString('en-MY', { maximumFractionDigits: 0 })
                                    }
                                },
                                yCount: {
                                    type: 'linear',
                                    position: 'right',
                                    beginAtZero: true,
                                    grid: { display: false },
                                    ticks: {
                                        precision: 0,
                                        font: { size: 11 }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
