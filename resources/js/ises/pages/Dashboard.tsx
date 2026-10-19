import { useEffect } from 'react';
import { router } from '@inertiajs/react';
import { Activity, ArrowRight, ArrowUpRight, Calculator, CheckCircle2, CircleAlert, FileText, FolderKanban, MapPinned, Plus, Route } from 'lucide-react';
import { AppLayout } from '../layouts/AppLayout';

type Metric = { total: number; planned: number; completed: number; mapped: number; quotations: number };
type Breakdown = { label: string; key: string; total: number };
type AttentionItem = { key: string; label: string; description: string; count: number };
type ProjectRow = { id: number | string; number: string; name: string; client: string | null; type: string; status: string; updatedAt?: string | null };
type QuotationStats = { count: number; total_value: number; average_value: number; this_month_count: number; this_month_value: number };
type MonthlyRow = { label: string; count: number; value: number };
type DashboardProps = {
    metrics: Metric;
    statusBreakdown: Breakdown[];
    projectTypeBreakdown: Breakdown[];
    quotationStats: QuotationStats;
    quotationMonthly: MonthlyRow[];
    recentProjects: ProjectRow[];
    attentionItems: AttentionItem[];
    today: string;
};

const money = (value: number) => new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR', maximumFractionDigits: 0 }).format(value);
const number = (value: number) => new Intl.NumberFormat('en-MY').format(value);
const statusHref = (key: string) => `/projects?status=${encodeURIComponent(key)}`;

export default function Dashboard({ metrics, statusBreakdown, projectTypeBreakdown, quotationStats, quotationMonthly, recentProjects, attentionItems, today }: DashboardProps) {
    useEffect(() => {
        const refreshDashboard = () => router.reload({ only: ['metrics', 'statusBreakdown', 'projectTypeBreakdown', 'quotationStats', 'quotationMonthly', 'recentProjects', 'attentionItems', 'today'] });
        const interval = window.setInterval(refreshDashboard, 60000);
        return () => window.clearInterval(interval);
    }, []);

    const maxStatus = Math.max(1, ...statusBreakdown.map((item) => item.total));
    const maxType = Math.max(1, ...projectTypeBreakdown.map((item) => item.total));
    const maxValue = Math.max(1, ...quotationMonthly.map((item) => item.value));
    const chartWidth = 720;
    const chartHeight = 210;
    const chartPadX = 30;
    const chartPadTop = 22;
    const chartPadBottom = 30;
    const chartPlotHeight = chartHeight - chartPadTop - chartPadBottom;
    const chartPoints = quotationMonthly.map((item, index) => ({
        ...item,
        x: quotationMonthly.length < 2 ? chartWidth / 2 : chartPadX + (index * (chartWidth - chartPadX * 2)) / (quotationMonthly.length - 1),
        y: chartPadTop + chartPlotHeight - (item.value / maxValue) * chartPlotHeight,
    }));
    const chartLine = chartPoints.map((point) => `${point.x},${point.y}`).join(' ');
    const chartArea = chartPoints.length ? `${chartPoints[0].x},${chartHeight - chartPadBottom} ${chartLine} ${chartPoints[chartPoints.length - 1].x},${chartHeight - chartPadBottom}` : '';

    const stats = [
        { label: 'Total projects', value: metrics.total, note: 'Projects in your workspace', icon: FolderKanban, href: '/projects' },
        { label: 'Planned', value: metrics.planned, note: 'Projects marked as planned', icon: Activity, href: '/projects?status=planned' },
        { label: 'Mapped projects', value: metrics.mapped, note: 'Projects with mapped survey areas', icon: MapPinned, href: '/projects?survey_status=Mapped' },
        { label: 'Costed projects', value: metrics.quotations, note: 'Projects with saved cost items', icon: Calculator, href: '/projects?has_quotation=1' },
    ];

    const attentionIcon = (key: string) => {
        if (key === 'boundaries') return <MapPinned size={17} />;
        if (key === 'lines') return <Route size={17} />;
        return <Calculator size={17} />;
    };

    return (
        <AppLayout title="Overview">
            <section className="ises-page-header">
                <div>
                    <h1 className="ises-page-title">Operations Overview</h1>
                    <p className="ises-page-description">Real-time status of survey planning, workload progress, and technical estimation.</p>
                </div>
                <a className="ises-button" href="/projects/create"><Plus size={16} /> New project</a>
            </section>

            <section className="ises-stats-grid" aria-label="Portfolio summary">
                {stats.map(({ label, value, note, icon: Icon, href }) => (
                    <a className="ises-stat" href={href} key={label}>
                        <div className="ises-stat-top"><span>{label}</span><span className="ises-stat-icon"><Icon size={16} /></span></div>
                        <div className="ises-stat-value">{number(value)}</div>
                        <div className="ises-stat-note">{note}</div>
                    </a>
                ))}
            </section>

            <section className="ises-grid-main" aria-label="Project analytics">
                <article className="ises-panel">
                    <header className="ises-panel-header">
                        <div><h2 className="ises-panel-title">Portfolio Status</h2></div>
                        <a className="ises-panel-action" href="/projects">All projects <ArrowUpRight size={13} style={{ display: 'inline', verticalAlign: 'middle' }} /></a>
                    </header>
                    <div className="ises-panel-body">
                        {statusBreakdown.length ? <div className="ises-status-list">
                            {statusBreakdown.map((item, index) => (
                                <a className="ises-status-row" href={statusHref(item.key)} key={`${item.key}-${index}`}>
                                    <span className="ises-status-name">{item.label}</span>
                                    <span className="ises-progress" aria-label={`${item.label}: ${item.total} projects`}><span className="ises-progress-fill" style={{ display: 'block', width: `${(item.total / maxStatus) * 100}%` }} /></span>
                                    <strong className="ises-status-count">{item.total}</strong>
                                </a>
                            ))}
                        </div> : <div className="ises-empty">Project status data will appear when projects are created.</div>}
                    </div>
                </article>

                <article className="ises-panel">
                    <header className="ises-panel-header">
                        <div><h2 className="ises-panel-title">Project Mix</h2></div>
                    </header>
                    <div className="ises-panel-body">
                        {projectTypeBreakdown.length ? <div className="ises-project-types">
                            {projectTypeBreakdown.map((item, index) => (
                                <a className="ises-type-row" href={`/projects?project_type=${encodeURIComponent(item.key ?? '')}`} key={`${item.key}-${index}`}>
                                    <span className="ises-type-name">{item.label}</span>
                                    <span style={{ display: 'flex', alignItems: 'center', gap: 12, minWidth: 90, justifyContent: 'flex-end' }}>
                                        <span className="ises-progress" style={{ width: 70 }}><span className="ises-progress-fill" style={{ display: 'block', width: `${(item.total / maxType) * 100}%` }} /></span>
                                        <strong className="ises-type-value">{item.total}</strong>
                                    </span>
                                </a>
                            ))}
                        </div> : <div className="ises-empty">Project type data will appear when projects are created.</div>}
                    </div>
                </article>
            </section>

            <section className="ises-panel" style={{ marginBottom: 18 }} aria-label="Quotation value overview">
                <header className="ises-panel-header">
                    <div><h2 className="ises-panel-title">Commercial Overview</h2></div>
                    <a className="ises-panel-action" href="/quotations/history">Quotation history <ArrowRight size={13} style={{ display: 'inline', verticalAlign: 'middle' }} /></a>
                </header>
                <div className="ises-panel-body">
                    <div className="ises-commercial-grid">
                        <div className="ises-commercial-item"><div className="ises-commercial-label">Total quoted value</div><div className="ises-commercial-value">{money(quotationStats.total_value)}</div><div className="ises-commercial-note">Across {number(quotationStats.count)} saved quotations</div></div>
                        <div className="ises-commercial-item"><div className="ises-commercial-label">Average quotation</div><div className="ises-commercial-value">{money(quotationStats.average_value)}</div><div className="ises-commercial-note">Average value per saved quotation</div></div>
                        <div className="ises-commercial-item"><div className="ises-commercial-label">Created this month</div><div className="ises-commercial-value">{money(quotationStats.this_month_value)}</div><div className="ises-commercial-note">{number(quotationStats.this_month_count)} quotations this month</div></div>
                    </div>
                    <div className="ises-line-chart-wrap" role="img" aria-label="Quotation value trend over the last six months. Hover or focus a point for its monthly total.">
                        <div className="ises-line-chart-legend"><span className="ises-line-chart-key" /> <span>Saved quotation value</span><span className="ises-line-chart-unit">MYR</span></div>
                        <svg className="ises-line-chart" viewBox={`0 0 ${chartWidth} ${chartHeight}`} preserveAspectRatio="none" aria-hidden="true">
                            {[0, 1, 2, 3].map((step) => {
                                const y = chartPadTop + (chartPlotHeight * step) / 3;
                                return <line key={step} x1={chartPadX} x2={chartWidth - chartPadX} y1={y} y2={y} className="ises-chart-gridline" />;
                            })}
                            {chartPoints.length > 1 && <polygon points={chartArea} className="ises-chart-area" />}
                            {chartPoints.length > 1 && <polyline points={chartLine} className="ises-chart-line" />}
                            {chartPoints.map((point) => <circle key={point.label} cx={point.x} cy={point.y} r="4.5" className="ises-chart-point"><title>{`${point.label}: ${money(point.value)} · ${point.count} quotations`}</title></circle>)}
                        </svg>
                        <div className="ises-line-chart-labels">{chartPoints.map((point) => <span key={point.label} title={`${point.label}: ${money(point.value)}`}>{point.label.split(' ')[0]}</span>)}</div>
                        <div className="ises-line-chart-latest"><span>Latest month</span><strong>{money(quotationMonthly[quotationMonthly.length - 1]?.value || 0)}</strong></div>
                    </div>
                    {!quotationMonthly.some((item) => item.value > 0) && <div className="ises-empty">Monthly quotation activity will appear here after quotations are saved. The line will update with the latest saved data whenever this dashboard loads.</div>}
                </div>
            </section>

            <section className="ises-panel" style={{ marginBottom: 18 }} aria-label="Outstanding work">
                <header className="ises-panel-header">
                    <div><h2 className="ises-panel-title">Operational Queue</h2></div>
                    <span className="ises-pill">{number(attentionItems.reduce((sum, item) => sum + item.count, 0))} open checks</span>
                </header>
                {attentionItems.length ? <div className="ises-attention-list">
                    {attentionItems.map((item) => (
                        <a className="ises-attention-row" href={`/projects?attention=${encodeURIComponent(item.key)}`} key={item.key}>
                            <span className="ises-attention-icon">{attentionIcon(item.key)}</span>
                            <span><span className="ises-attention-title" style={{ display: 'block' }}>{item.label}</span><span className="ises-attention-description" style={{ display: 'block' }}>{item.description}</span></span>
                            <span style={{ display: 'flex', alignItems: 'center', gap: 10 }}><strong className="ises-attention-count">{item.count}</strong><ArrowRight size={14} color="#71838d" /></span>
                        </a>
                    ))}
                </div> : <div className="ises-empty"><CheckCircle2 size={20} style={{ display: 'block', margin: '0 auto 8px' }} />No outstanding planning checks were returned.</div>}
            </section>

            <section className="ises-panel" aria-label="Recent projects">
                <header className="ises-panel-header">
                    <div><h2 className="ises-panel-title">Recent Projects</h2></div>
                    <a className="ises-panel-action" href="/projects">Project register <ArrowRight size={13} style={{ display: 'inline', verticalAlign: 'middle' }} /></a>
                </header>
                {recentProjects.length ? <div className="ises-table-wrap">
                    <table className="ises-table">
                        <thead><tr><th scope="col">Project</th><th scope="col">Client</th><th scope="col">Type</th><th scope="col">Status</th><th scope="col"><span className="sr-only">Action</span></th></tr></thead>
                        <tbody>{recentProjects.map((project) => (
                            <tr key={project.id}>
                                <td><a className="ises-project-name" href={`/projects/${project.id}`}>{project.name}</a><span className="ises-project-code">{project.number}</span></td>
                                <td>{project.client || '—'}</td>
                                <td>{project.type || 'Not set'}</td>
                                <td><span className="ises-pill">{project.status}</span></td>
                                <td><a className="ises-panel-action" href={`/projects/${project.id}`}>Open <ArrowUpRight size={12} style={{ display: 'inline', verticalAlign: 'middle' }} /></a></td>
                            </tr>
                        ))}</tbody>
                    </table>
                </div> : <div className="ises-empty"><FolderKanban size={21} style={{ display: 'block', margin: '0 auto 8px' }} />No projects yet. Create a project to begin survey planning.</div>}
            </section>
            <footer className="ises-footer">INOS Survey Estimation System · {today}</footer>
        </AppLayout>
    );
}
