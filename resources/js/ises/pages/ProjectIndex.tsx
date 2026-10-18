import { useState, type FormEvent } from 'react';
import { Link, router } from '@inertiajs/react';
import { ArrowDown, ArrowLeft, ArrowRight, ArrowUpRight, Check, ChevronDown, CircleAlert, Clock3, Filter, FolderKanban, MapPinned, Pencil, Plus, Search, Trash2, X } from 'lucide-react';
import { AppLayout } from '../layouts/AppLayout';

type ProjectRow = {
    id: number;
    number: string;
    name: string;
    period: string | null;
    client: string | null;
    projectTypeCode: string | null;
    projectType: string;
    location: string | null;
    status: string;
    createdAt: string | null;
};
type ProjectPage = {
    data: ProjectRow[];
    currentPage: number;
    lastPage: number;
    perPage: number;
    total: number;
    from: number | null;
    to: number | null;
    previousUrl: string | null;
    nextUrl: string | null;
    pageLinks: { label: string; url: string | null; active: boolean }[];
};
type ProjectFilters = {
    search: string;
    status: string;
    project_type: string;
    location: string;
    date_range: string;
    survey_status: string;
    has_quotation: string;
    attention: string;
};
type Props = {
    projects: ProjectPage;
    totalProjects: number;
    statusCounts: { draft: number; planned: number; completed: number };
    projectTypes: Record<string, string>;
    locations: string[];
    filters: ProjectFilters;
    today: string;
    flash?: { success?: string | null };
};

const emptyFilters: ProjectFilters = { search: '', status: '', project_type: '', location: '', date_range: '', survey_status: '', has_quotation: '', attention: '' };
const hasFilters = (filters: ProjectFilters) => Object.values(filters).some((value) => value.trim() !== '');
const statusLabel = (status: string) => ({ draft: 'Draft', planned: 'Planned', completed: 'Completed' }[status] || status.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase()));

export default function ProjectIndex({ projects, totalProjects, statusCounts, projectTypes, locations, filters: initialFilters, today, flash }: Props) {
    const [filters, setFilters] = useState<ProjectFilters>({ ...emptyFilters, ...initialFilters });
    const [deleteTarget, setDeleteTarget] = useState<ProjectRow | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);
    const activeFilters = hasFilters(filters);

    const updateFilter = (key: keyof ProjectFilters, value: string) => setFilters((current) => ({ ...current, [key]: value }));
    const applyFilters = (event: FormEvent<HTMLFormElement>) => {
        event.preventDefault();
        router.get('/projects', filters as unknown as Record<string, string>, { preserveState: true, preserveScroll: true, replace: true });
    };
    const clearFilters = () => {
        setFilters(emptyFilters);
        router.get('/projects', {}, { preserveState: true, preserveScroll: true, replace: true });
    };
    const confirmDelete = () => {
        if (!deleteTarget || isDeleting) return;
        setIsDeleting(true);
        router.delete(`/projects/${deleteTarget.id}`, {
            preserveScroll: true,
            onFinish: () => { setIsDeleting(false); setDeleteTarget(null); },
        });
    };

    return (
        <AppLayout title="Projects">
            <section className="ises-page-header">
                <div>
                    <p className="ises-eyebrow">Workspace / project register</p>
                    <h1 className="ises-page-title">Survey projects</h1>
                    <p className="ises-page-description">Manage project records, follow planning progress and move directly into survey estimation.</p>
                </div>
                <Link className="ises-button" href="/projects/create"><Plus size={16} /> New project</Link>
            </section>

            {flash?.success && <div className="ises-project-feedback" role="status"><Check size={17} /><span>{flash.success}</span></div>}

            <section className="ises-project-metrics" aria-label="Project status summary">
                <div className="ises-project-metric"><span className="ises-project-metric-label">All projects</span><strong>{totalProjects.toLocaleString('en-MY')}</strong><span className="ises-project-metric-foot">Across the workspace</span></div>
                <div className="ises-project-metric"><span className="ises-project-metric-label">Draft</span><strong>{statusCounts.draft.toLocaleString('en-MY')}</strong><span className="ises-project-metric-foot">Setup in progress</span></div>
                <div className="ises-project-metric"><span className="ises-project-metric-label">Planned</span><strong>{statusCounts.planned.toLocaleString('en-MY')}</strong><span className="ises-project-metric-foot">Ready for field planning</span></div>
                <div className="ises-project-metric"><span className="ises-project-metric-label">Completed</span><strong>{statusCounts.completed.toLocaleString('en-MY')}</strong><span className="ises-project-metric-foot">Work marked complete</span></div>
            </section>

            <section className="ises-panel ises-project-register" aria-labelledby="project-register-title">
                <header className="ises-panel-header ises-project-register-header">
                    <div>
                        <div className="ises-register-heading-line"><span className="ises-register-heading-icon"><FolderKanban size={17} /></span><h2 id="project-register-title" className="ises-panel-title">Project register</h2></div>
                        <p className="ises-panel-description">Search the register or combine filters to narrow the work queue.</p>
                    </div>
                    <div className="ises-register-count"><strong>{projects.total.toLocaleString('en-MY')}</strong><span>{activeFilters ? 'matching results' : 'records'}</span></div>
                </header>

                <form className="ises-project-filters" onSubmit={applyFilters}>
                    <label className="ises-project-search"><Search size={17} /><span className="sr-only">Search projects</span><input type="search" value={filters.search} onChange={(event) => updateFilter('search', event.target.value)} placeholder="Search project, code or client" /></label>
                    <label className="ises-filter-field"><span>Status</span><span className="ises-filter-select"><select value={filters.status} onChange={(event) => updateFilter('status', event.target.value)}><option value="">All statuses</option><option value="draft">Draft</option><option value="planned">Planned</option><option value="completed">Completed</option></select><ChevronDown size={14} /></span></label>
                    <label className="ises-filter-field"><span>Project type</span><span className="ises-filter-select"><select value={filters.project_type} onChange={(event) => updateFilter('project_type', event.target.value)}><option value="">All types</option>{Object.entries(projectTypes).map(([code, label]) => <option value={code} key={code}>{code} · {label}</option>)}</select><ChevronDown size={14} /></span></label>
                    <label className="ises-filter-field"><span>Location</span><span className="ises-filter-select"><select value={filters.location} onChange={(event) => updateFilter('location', event.target.value)}><option value="">All locations</option>{locations.map((location) => <option value={location} key={location}>{location}</option>)}</select><ChevronDown size={14} /></span></label>
                    <label className="ises-filter-field"><span>Created</span><span className="ises-filter-select"><select value={filters.date_range} onChange={(event) => updateFilter('date_range', event.target.value)}><option value="">Any time</option><option value="last_30_days">Last 30 days</option><option value="this_month">This month</option><option value="this_year">This year</option></select><ChevronDown size={14} /></span></label>
                    <label className="ises-filter-field"><span>Survey area</span><span className="ises-filter-select"><select value={filters.survey_status} onChange={(event) => updateFilter('survey_status', event.target.value)}><option value="">Any state</option><option value="Mapped">Mapped</option></select><ChevronDown size={14} /></span></label>
                    <label className="ises-filter-field"><span>Quotation</span><span className="ises-filter-select"><select value={filters.has_quotation} onChange={(event) => updateFilter('has_quotation', event.target.value)}><option value="">Any state</option><option value="1">Has quotation</option><option value="0">No quotation</option></select><ChevronDown size={14} /></span></label>
                    <label className="ises-filter-field"><span>Planning step</span><span className="ises-filter-select"><select value={filters.attention} onChange={(event) => updateFilter('attention', event.target.value)}><option value="">All steps</option><option value="boundaries">Missing boundaries</option><option value="lines">Pending survey lines</option><option value="cost">Awaiting cost estimate</option></select><ChevronDown size={14} /></span></label>
                    <div className="ises-filter-actions"><button className="ises-button" type="submit"><Filter size={15} /> Apply filters</button>{activeFilters && <button className="ises-button ises-button-secondary" type="button" onClick={clearFilters}><X size={15} /> Clear</button>}</div>
                </form>

                <div className="ises-project-table-wrap">
                    <table className="ises-project-table">
                        <caption className="sr-only">Survey project register</caption>
                        <thead><tr><th scope="col">Project</th><th scope="col">Client</th><th scope="col">Type & location</th><th scope="col">Period</th><th scope="col">Status</th><th scope="col"><span className="sr-only">Actions</span></th></tr></thead>
                        <tbody>
                            {projects.data.map((project) => <tr key={project.id}>
                                <td><Link href={`/projects/${project.id}`} className="ises-project-row-name">{project.name || 'Untitled project'}</Link><span className="ises-project-row-code">{project.number || `PRJ-${String(project.id).padStart(4, '0')}`}</span></td>
                                <td><span className="ises-project-client">{project.client || 'No client assigned'}</span></td>
                                <td><span className="ises-project-type">{project.projectType || 'Not set'}</span><span className="ises-project-location">{project.location ? <><MapPinned size={12} /> {project.location}</> : 'Location not set'}</span></td>
                                <td><span className="ises-project-period"><Clock3 size={13} /> {project.period || 'Unspecified'}</span></td>
                                <td><span className={`ises-status-badge ises-status-${project.status.toLowerCase().replace(/[^a-z0-9]+/g, '-')}`}>{statusLabel(project.status)}</span></td>
                                <td><div className="ises-project-row-actions"><Link href={`/projects/${project.id}`} className="ises-row-action ises-row-action-primary" aria-label={`Open ${project.name}`} title="Open project"><ArrowUpRight size={15} /></Link><Link href={`/projects/${project.id}/edit`} className="ises-row-action" aria-label={`Edit ${project.name}`} title="Edit project"><Pencil size={14} /></Link><button type="button" className="ises-row-action ises-row-action-delete" aria-label={`Delete ${project.name}`} title="Delete project" onClick={() => setDeleteTarget(project)}><Trash2 size={14} /></button></div></td>
                            </tr>)}
                            {projects.data.length === 0 && <tr><td colSpan={6}><div className="ises-project-empty"><span className="ises-project-empty-icon"><FolderKanban size={23} /></span><h3>{activeFilters ? 'No matching projects' : 'No projects yet'}</h3><p>{activeFilters ? 'Try adjusting or clearing your filters to see more results.' : 'Create a project to start planning survey areas, lines and cost estimates.'}</p>{activeFilters ? <button type="button" className="ises-button ises-button-secondary" onClick={clearFilters}>Clear filters</button> : <Link className="ises-button" href="/projects/create"><Plus size={15} /> Create first project</Link>}</div></td></tr>}
                        </tbody>
                    </table>
                </div>

                <footer className="ises-project-pagination">
                    <span>Showing <strong>{projects.from ?? 0}–{projects.to ?? 0}</strong> of <strong>{projects.total.toLocaleString('en-MY')}</strong></span>
                    <nav className="ises-page-links" aria-label="Project pages">
                        {projects.previousUrl ? <Link className="ises-page-link" href={projects.previousUrl} preserveScroll aria-label="Previous page"><ArrowLeft size={14} /></Link> : <span className="ises-page-link is-disabled" aria-disabled="true"><ArrowLeft size={14} /></span>}
                        {projects.pageLinks.map((link, index) => link.url ? <Link className={`ises-page-link ${link.active ? 'is-active' : ''}`} href={link.url} preserveScroll key={`${link.label}-${index}`}>{link.label}</Link> : <span className="ises-page-ellipsis" key={`${link.label}-${index}`}>{link.label === '...' ? '…' : link.label}</span>)}
                        {projects.nextUrl ? <Link className="ises-page-link" href={projects.nextUrl} preserveScroll aria-label="Next page"><ArrowRight size={14} /></Link> : <span className="ises-page-link is-disabled" aria-disabled="true"><ArrowRight size={14} /></span>}
                    </nav>
                </footer>
            </section>

            <footer className="ises-footer">Eco HydroTech · Project register · {today}</footer>

            {deleteTarget && <div className="ises-modal-backdrop" role="presentation" onMouseDown={(event) => { if (event.target === event.currentTarget && !isDeleting) setDeleteTarget(null); }}>
                <section className="ises-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-project-title" aria-describedby="delete-project-description">
                    <span className="ises-confirm-icon"><CircleAlert size={23} /></span>
                    <h2 id="delete-project-title">Delete this project?</h2>
                    <p id="delete-project-description">You are about to delete <strong>{deleteTarget.name}</strong>. Its associated map data and cost estimates may also be removed. This action cannot be undone.</p>
                    <div className="ises-confirm-actions"><button type="button" className="ises-button ises-button-secondary" disabled={isDeleting} onClick={() => setDeleteTarget(null)}>Cancel</button><button type="button" className="ises-button ises-button-danger" disabled={isDeleting} onClick={confirmDelete}>{isDeleting ? 'Deleting…' : <><Trash2 size={15} /> Delete project</>}</button></div>
                </section>
            </div>}
        </AppLayout>
    );
}
