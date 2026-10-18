import { useState, type FormEvent } from 'react';
import { router } from '@inertiajs/react';
import { ArrowDownToLine, ArrowUpRight, CalendarDays, FileText, Search, Trash2, X } from 'lucide-react';
import { AppLayout } from '../layouts/AppLayout';

type Quotation = {
    id: number;
    number: string;
    projectName: string;
    projectNumber: string;
    client: string;
    total: number;
    createdAt: string | null;
};
type Props = {
    quotations: Quotation[];
    filters: { search: string; month: string };
    flash?: { success?: string | null; error?: string | null };
};
const money = (amount: number) => new Intl.NumberFormat('en-MY', { style: 'currency', currency: 'MYR' }).format(amount || 0);
const monthOptions = [
    ['1', 'January'], ['2', 'February'], ['3', 'March'], ['4', 'April'], ['5', 'May'], ['6', 'June'],
    ['7', 'July'], ['8', 'August'], ['9', 'September'], ['10', 'October'], ['11', 'November'], ['12', 'December'],
];

export default function QuotationHistory({ quotations, filters: initialFilters, flash }: Props) {
    const [search, setSearch] = useState(initialFilters.search || '');
    const [month, setMonth] = useState(initialFilters.month || '');
    const [deleteTarget, setDeleteTarget] = useState<Quotation | null>(null);
    const [deleting, setDeleting] = useState(false);

    const applyFilters = (event: FormEvent) => {
        event.preventDefault();
        router.get('/quotations/history', { search, month }, { preserveState: true, preserveScroll: true, replace: true });
    };
    const clearFilters = () => {
        setSearch(''); setMonth('');
        router.get('/quotations/history', {}, { preserveState: true, preserveScroll: true, replace: true });
    };
    const deleteQuotation = () => {
        if (!deleteTarget || deleting) return;
        setDeleting(true);
        router.delete(`/quotations/${deleteTarget.id}`, { preserveScroll: true, onFinish: () => { setDeleting(false); setDeleteTarget(null); } });
    };

    return <AppLayout title="Quotation history">
        <section className="ises-page-header">
            <div><p className="ises-eyebrow">Commercial / records</p><h1 className="ises-page-title">Quotation history</h1><p className="ises-page-description">Find saved quotations, review project values, and open or download a document.</p></div>
            <a className="ises-button" href="/quotation"><FileText size={16} /> New quotation</a>
        </section>
        {(flash?.success || flash?.error) && <div className={`ises-project-feedback ${flash.error ? 'is-error' : ''}`} role="status">{flash.error || flash.success}</div>}
        <section className="ises-project-metrics" aria-label="Quotation summary">
            <div className="ises-project-metric"><span className="ises-project-metric-label">Saved quotations</span><strong>{quotations.length.toLocaleString('en-MY')}</strong><span className="ises-project-metric-foot">Matching current filters</span></div>
            <div className="ises-project-metric"><span className="ises-project-metric-label">Combined value</span><strong>{money(quotations.reduce((sum, item) => sum + item.total, 0))}</strong><span className="ises-project-metric-foot">Saved quotation totals</span></div>
        </section>
        <section className="ises-panel ises-project-register" aria-labelledby="quotation-history-title">
            <header className="ises-panel-header ises-project-register-header"><div><h2 id="quotation-history-title" className="ises-panel-title">Saved records</h2><p className="ises-panel-description">Search by quotation number, project, or client. Filter by creation month.</p></div><div className="ises-register-count"><strong>{quotations.length}</strong><span>records</span></div></header>
            <form className="ises-project-filters ises-history-filters" onSubmit={applyFilters}>
                <label className="ises-project-search"><Search size={17} /><span className="sr-only">Search quotations</span><input type="search" value={search} onChange={event => setSearch(event.target.value)} placeholder="Quotation, project or client" /></label>
                <label className="ises-filter-field"><span>Created month</span><span className="ises-filter-select"><select value={month} onChange={event => setMonth(event.target.value)}><option value="">All months</option>{monthOptions.map(([value, label]) => <option value={value} key={value}>{label}</option>)}</select></span></label>
                <div className="ises-filter-actions"><button className="ises-button" type="submit"><Search size={15} /> Search</button>{(search || month) && <button className="ises-button ises-button-secondary" type="button" onClick={clearFilters}><X size={15} /> Clear</button>}</div>
            </form>
            <div className="ises-project-table-wrap"><table className="ises-project-table"><caption className="sr-only">Saved quotation records and document actions</caption><thead><tr><th scope="col">Quotation</th><th scope="col">Project / client</th><th scope="col">Created</th><th scope="col">Total value</th><th scope="col"><span className="sr-only">Actions</span></th></tr></thead><tbody>
                {quotations.map(item => <tr key={item.id}><td><span className="ises-project-row-name">{item.number || `Quotation #${item.id}`}</span><span className="ises-project-row-code">Record ID {item.id}</span></td><td><span className="ises-project-row-name">{item.projectName || 'Untitled project'}</span><span className="ises-project-row-code">{item.projectNumber || 'No project number'} · {item.client || 'No client assigned'}</span></td><td><span className="ises-project-period"><CalendarDays size={13} /> {item.createdAt || 'Date unavailable'}</span></td><td><strong className="ises-history-total">{money(item.total)}</strong></td><td><div className="ises-project-row-actions"><a href={`/quotations/${item.id}`} className="ises-row-action ises-row-action-primary" aria-label={`Open quotation ${item.number}`} title="Open quotation"><ArrowUpRight size={15} /></a><a href={`/quotations/${item.id}/download`} className="ises-row-action" aria-label={`Download quotation ${item.number}`} title="Download PDF"><ArrowDownToLine size={15} /></a><button type="button" className="ises-row-action ises-row-action-delete" aria-label={`Delete quotation ${item.number}`} title="Delete quotation" onClick={() => setDeleteTarget(item)}><Trash2 size={14} /></button></div></td></tr>)}
                {quotations.length === 0 && <tr><td colSpan={5}><div className="ises-project-empty"><span className="ises-project-empty-icon"><FileText size={23} /></span><h3>No quotations found</h3><p>{search || month ? 'Try another search or clear the month filter.' : 'Saved quotations will appear here after a quotation is created.'}</p>{(search || month) && <button type="button" className="ises-button ises-button-secondary" onClick={clearFilters}>Clear filters</button>}</div></td></tr>}
            </tbody></table></div>
        </section>
        <footer className="ises-footer">Eco HydroTech · Quotation history</footer>
        {deleteTarget && <div className="ises-modal-backdrop" role="presentation" onMouseDown={event => { if (event.target === event.currentTarget && !deleting) setDeleteTarget(null); }}><section className="ises-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="delete-quotation-title"><span className="ises-confirm-icon"><Trash2 size={22} /></span><h2 id="delete-quotation-title">Delete quotation?</h2><p>This will delete <strong>{deleteTarget.number || `quotation #${deleteTarget.id}`}</strong> and its saved line items. This cannot be undone.</p><div className="ises-confirm-actions"><button className="ises-button ises-button-secondary" type="button" disabled={deleting} onClick={() => setDeleteTarget(null)}>Cancel</button><button className="ises-button ises-button-danger" type="button" disabled={deleting} onClick={deleteQuotation}>{deleting ? 'Deleting…' : <><Trash2 size={15} /> Delete quotation</>}</button></div></section></div>}
    </AppLayout>;
}
