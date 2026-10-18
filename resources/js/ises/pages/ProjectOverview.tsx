import { useState } from 'react';
import { Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, CheckCircle2, Clock3, Cuboid, FileText, FolderKanban, MapPinned, Pencil, Plane, Plus, Trash2, Waves, X } from 'lucide-react';
import { AppLayout } from '../layouts/AppLayout';

type SurveyArea = { id: number; name: string; surveyType: 'sbes' | 'drone'; status: string; createdAt: string | null };
type ProjectData = { id: number; name: string; number: string | null; projectType: string; location: string | null; status: string; period: string | null; createdAt: string | null; clientName: string | null; weatherDays: number; modDemodDays: number; patchTestDays: number };
type Modelling = { packageName: string | null; grandTotal: number; moduleCount: number } | null;
type Props = { project: ProjectData; surveyLocations: SurveyArea[]; modelling: Modelling; csrfToken: string; oldInput?: Record<string, string | undefined>; today: string; flash?: { success?: string | null; error?: string | null }; errors?: Record<string, string> };
const money = (amount: number) => new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR', minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount);
const statusText = (status: string) => status.replaceAll('_', ' ').replace(/\b\w/g, (letter) => letter.toUpperCase());

export default function ProjectOverview({ project, surveyLocations, modelling, csrfToken, oldInput = {}, today, flash, errors = {} }: Props) {
    const [surveyType, setSurveyType] = useState<'sbes' | 'drone'>(oldInput.survey_type === 'drone' ? 'drone' : 'sbes');
    const [modalOpen, setModalOpen] = useState(Boolean(errors.name || errors.survey_type));
    const [deleteTarget, setDeleteTarget] = useState<{ kind: 'survey' | 'modelling'; id?: number; label: string } | null>(null);
    const [isDeleting, setIsDeleting] = useState(false);
    const userErrors = usePage().props.errors as Record<string, string> | undefined;
    const allErrors = { ...userErrors, ...errors };

    const confirmDelete = () => {
        if (!deleteTarget || isDeleting) return;
        const target = deleteTarget;
        setIsDeleting(true);
        const url = target.kind === 'survey' ? `/projects/${project.id}/surveys/${target.id}` : `/projects/modeling/${project.id}`;
        router.delete(url, { preserveScroll: true, onFinish: () => { setIsDeleting(false); setDeleteTarget(null); } });
    };

    return (
        <AppLayout title="Project workspace">
            <div className="ises-overview-back"><Link href="/projects"><ArrowLeft size={14} /> Back to project register</Link><span>Project workspace <span aria-hidden="true">/</span> {project.number || `PRJ-${String(project.id).padStart(4, '0')}`}</span></div>
            {flash?.success && <div className="ises-project-feedback" role="status"><CheckCircle2 size={17} />{flash.success}</div>}
            {flash?.error && <div className="ises-project-feedback ises-overview-error" role="alert"><X size={17} />{flash.error}</div>}

            <section className="ises-overview-hero">
                <div className="ises-overview-hero-top">
                    <div className="ises-overview-title-block">
                        <p className="ises-eyebrow">Survey operations / project overview</p>
                        <h1>{project.name}</h1>
                        <div className="ises-overview-project-code">{project.number || `PRJ-${String(project.id).padStart(4, '0')}`}</div>
                        <div className="ises-overview-tags"><span className={`ises-status-badge ises-status-${project.status.toLowerCase()}`}>{statusText(project.status)}</span><span className="ises-overview-type">{project.projectType}</span>{project.location && <span className="ises-overview-location"><MapPinned size={13} />{project.location}</span>}</div>
                    </div>
                    <Link href={`/projects/${project.id}/edit`} className="ises-button"><Pencil size={15} /> Edit project</Link>
                </div>
                <div className="ises-overview-meta-grid">
                    <div><span>Client</span><strong>{project.clientName || 'No client assigned'}</strong></div>
                    <div><span>Project period</span><strong>{project.period || 'Not specified'}</strong></div>
                    <div><span>Created</span><strong>{project.createdAt || 'Not available'}</strong></div>
                </div>
            </section>

            <section className="ises-overview-summary-grid" aria-label="Project workflow summary">
                <div className="ises-overview-summary-card"><span className="ises-overview-summary-icon"><MapPinned size={17} /></span><span><small>Survey areas</small><strong>{surveyLocations.length}</strong></span></div>
                <div className="ises-overview-summary-card"><span className="ises-overview-summary-icon is-mapped"><CheckCircle2 size={17} /></span><span><small>Mapped areas</small><strong>{surveyLocations.filter((area) => area.status === 'Mapped').length}</strong></span></div>
                <div className="ises-overview-summary-card"><span className="ises-overview-summary-icon is-model"><Cuboid size={17} /></span><span><small>Modelling</small><strong>{modelling ? 'Configured' : 'Not added'}</strong></span></div>
            </section>

            <section className="ises-panel ises-overview-add-panel">
                <header className="ises-panel-header"><div><h2 className="ises-panel-title">Add a workflow</h2><p className="ises-panel-description">Create a survey area or open the modelling workspace for this project.</p></div><a className="ises-panel-action" href="/admin/equipment">Equipment database <ArrowRight size={13} style={{ display: 'inline', verticalAlign: 'middle' }} /></a></header>
                <div className="ises-overview-add-grid">
                    <button className="ises-workflow-add-card" type="button" onClick={() => { setSurveyType('sbes'); setModalOpen(true); }}><span className="ises-workflow-add-icon is-sbes"><Waves size={21} /></span><strong>Single Beam (SBES)</strong><span>Add a hydrographic survey area.</span><span className="ises-workflow-add-cta">Create survey area <Plus size={13} /></span></button>
                    <button className="ises-workflow-add-card" type="button" onClick={() => { setSurveyType('drone'); setModalOpen(true); }}><span className="ises-workflow-add-icon is-drone"><Plane size={21} /></span><strong>Drone Mapping</strong><span>Add an aerial survey area.</span><span className="ises-workflow-add-cta">Create survey area <Plus size={13} /></span></button>
                    {modelling ? <div className="ises-workflow-add-card is-disabled" aria-disabled="true"><span className="ises-workflow-add-icon is-model"><Cuboid size={21} /></span><strong>3D Modelling</strong><span>Modelling is already configured for this project.</span><span className="ises-workflow-add-cta">Already added <CheckCircle2 size={13} /></span></div> : <a className="ises-workflow-add-card" href={`/projects/modeling/builder?project_id=${project.id}`}><span className="ises-workflow-add-icon is-model"><Cuboid size={21} /></span><strong>3D Modelling</strong><span>Configure processing and modelling packages.</span><span className="ises-workflow-add-cta">Open modelling <ArrowRight size={13} /></span></a>}
                </div>
            </section>

            <section className="ises-panel ises-overview-workflows">
                <header className="ises-panel-header"><div><h2 className="ises-panel-title">Active workflows</h2><p className="ises-panel-description">Each survey area keeps its map, report and quotation actions together.</p></div>{surveyLocations.length > 1 && <a className="ises-button ises-button-secondary" href={`/projects/${project.id}/report/preview`}><FileText size={14} /> Full project report</a>}</header>
                {surveyLocations.length === 0 && !modelling && <div className="ises-overview-empty"><span><FolderKanban size={24} /></span><h3>No workflows added yet</h3><p>Start with a survey area or configure a modelling package. Your work will appear here.</p><button type="button" className="ises-button" onClick={() => { setSurveyType('sbes'); setModalOpen(true); }}><Plus size={15} /> Add first survey area</button></div>}
                {surveyLocations.length > 0 && <div className="ises-workflow-list">{surveyLocations.map((area) => <article className="ises-workflow-row" key={area.id}>
                    <span className={`ises-workflow-kind ${area.surveyType === 'drone' ? 'is-drone' : 'is-sbes'}`}>{area.surveyType === 'drone' ? <Plane size={19} /> : <Waves size={19} />}</span>
                    <div className="ises-workflow-details"><div className="ises-workflow-title-line"><h3>{area.surveyType === 'drone' ? 'Drone Mapping' : 'SBES Survey'}: {area.name}</h3><span className={`ises-status-badge ${area.status === 'Mapped' ? 'ises-status-planned' : 'ises-status-draft'}`}>{area.status === 'Mapped' ? 'Mapped' : 'Pending'}</span></div><p><Clock3 size={12} /> Added {area.createdAt || 'date unavailable'}</p></div>
                    <div className="ises-workflow-actions"><a href={`/projects/${project.id}/report/preview?location_id=${area.id}`} className="ises-workflow-action"><FileText size={14} /> Report</a><a href={`/projects/${project.id}/surveys/${area.id}/planning`} className="ises-workflow-action"><MapPinned size={14} /> Open map</a><a href={`/quotation?project_id=${project.id}&module=${area.surveyType}&location_id=${area.id}`} className="ises-workflow-action is-primary"><FileText size={14} /> Quotation</a><button type="button" className="ises-workflow-delete" aria-label={`Delete survey area ${area.name}`} title="Delete survey area" onClick={() => setDeleteTarget({ kind: 'survey', id: area.id, label: area.name })}><Trash2 size={14} /></button></div>
                </article>)}</div>}
                {modelling && <article className="ises-workflow-row"><span className="ises-workflow-kind is-model"><Cuboid size={19} /></span><div className="ises-workflow-details"><div className="ises-workflow-title-line"><h3>3D Modelling</h3><span className="ises-status-badge ises-status-planned">Saved</span></div><p>{modelling.packageName || 'Custom selection'} · {modelling.moduleCount} modules · {money(modelling.grandTotal)}</p></div><div className="ises-workflow-actions"><a href={`/projects/modeling/builder?project_id=${project.id}`} className="ises-workflow-action"><Pencil size={14} /> Edit modules</a><a href={`/quotation?project_id=${project.id}&module=modelling`} className="ises-workflow-action is-primary"><FileText size={14} /> Quotation</a><button type="button" className="ises-workflow-delete" aria-label="Delete modelling package" title="Delete modelling package" onClick={() => setDeleteTarget({ kind: 'modelling', label: '3D Modelling package' })}><Trash2 size={14} /></button></div></article>}
            </section>
            <footer className="ises-footer">Eco HydroTech · Project workspace · {today}</footer>

            {modalOpen && <div className="ises-modal-backdrop" role="presentation" onMouseDown={(event) => { if (event.target === event.currentTarget) setModalOpen(false); }}><section className="ises-confirm-dialog ises-survey-dialog" role="dialog" aria-modal="true" aria-labelledby="new-survey-title"><button type="button" className="ises-dialog-close" onClick={() => setModalOpen(false)} aria-label="Close dialog"><X size={17} /></button><span className={`ises-workflow-add-icon ${surveyType === 'drone' ? 'is-drone' : 'is-sbes'}`}>{surveyType === 'drone' ? <Plane size={21} /> : <Waves size={21} />}</span><h2 id="new-survey-title">{surveyType === 'drone' ? 'New drone mapping area' : 'New SBES survey area'}</h2><p>Create a named area under <strong>{project.name}</strong>. You can configure the boundary and survey parameters after creating it.</p><form action={`/projects/${project.id}/surveys`} method="POST"><input type="hidden" name="_token" value={csrfToken} /><input type="hidden" name="survey_type" value={surveyType} /><label className="ises-form-field"><span className="ises-survey-label">Area name <span className="ises-required">*</span></span><input className={`ises-form-input ${allErrors.name ? 'has-error' : ''}`} name="name" defaultValue={oldInput.name || ''} placeholder="e.g. Main river reach, Zone A" maxLength={255} required autoFocus />{allErrors.name && <span className="ises-field-error" role="alert">{allErrors.name}</span>}</label><div className="ises-confirm-actions"><button type="button" className="ises-button ises-button-secondary" onClick={() => setModalOpen(false)}>Cancel</button><button type="submit" className="ises-button"><Plus size={15} /> Create survey area</button></div></form></section></div>}

            {deleteTarget && <div className="ises-modal-backdrop" role="presentation" onMouseDown={(event) => { if (event.target === event.currentTarget && !isDeleting) setDeleteTarget(null); }}><section className="ises-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-workflow-title"><span className="ises-confirm-icon"><Trash2 size={21} /></span><h2 id="delete-workflow-title">Remove this workflow?</h2><p><strong>{deleteTarget.label}</strong> will be removed from this project. Associated workflow data may also be deleted. This cannot be undone.</p><div className="ises-confirm-actions"><button type="button" className="ises-button ises-button-secondary" disabled={isDeleting} onClick={() => setDeleteTarget(null)}>Cancel</button><button type="button" className="ises-button ises-button-danger" disabled={isDeleting} onClick={confirmDelete}>{isDeleting ? 'Removing…' : <><Trash2 size={14} /> Remove workflow</>}</button></div></section></div>}
        </AppLayout>
    );
}
