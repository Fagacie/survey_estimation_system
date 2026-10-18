import { useState, useEffect, type PropsWithChildren } from 'react';
import { Link, usePage } from '@inertiajs/react';
import { ChartNoAxesCombined, FileText, FolderKanban, LogOut, Menu, Settings2, X } from 'lucide-react';

type SharedProps = { auth?: { user?: { name?: string } | null } };
const navigation = [
    { label: 'Overview', href: '/dashboard', icon: ChartNoAxesCombined },
    { label: 'Projects', href: '/projects', icon: FolderKanban },
    { label: 'Quotations', href: '/quotations/history', icon: FileText },
];
const management = [
    { label: 'Admin catalogue', href: '/admin', icon: Settings2 },
    { label: 'Drone equipment', href: '/admin/equipment', icon: Settings2 },
];

export function AppLayout({ children, title = 'Overview' }: PropsWithChildren<{ title?: string }>) {
    const today = new Intl.DateTimeFormat('en-MY', { day: '2-digit', month: 'short', year: 'numeric' }).format(new Date());
    const page = usePage();
    const { auth } = page.props as SharedProps;
    const userName = auth?.user?.name || 'Account';
    const initials = userName.split(/\s+/).filter(Boolean).slice(0, 2).map((part) => part[0]).join('').toUpperCase();
    const [mobileNavOpen, setMobileNavOpen] = useState(false);

    useEffect(() => {
        const handleKeyDown = (e: KeyboardEvent) => {
            if (e.key === 'Escape') setMobileNavOpen(false);
        };
        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, []);

    // Accurate active-state matching preventing /admin from matching /admin/equipment,
    // and supporting all quotation routes (/quotation, /quotations/*) under Quotations.
    const isActive = (href: string): boolean => {
        const current = page.url.split('?')[0];
        if (href === '/dashboard') return current === href;
        if (href === '/admin') return current === '/admin' || (current.startsWith('/admin') && !current.startsWith('/admin/equipment'));
        if (href === '/quotations/history') return current === href || current.startsWith('/quotations/') || current === '/quotation';
        return current === href || current.startsWith(href + '/');
    };

    return (
        <div className="ises-shell">
            {/* Mobile backdrop overlay */}
            <div
                className={`ises-mobile-overlay ${mobileNavOpen ? 'is-visible' : ''}`}
                onClick={() => setMobileNavOpen(false)}
                aria-hidden="true"
            />
            <aside className={`ises-sidebar ${mobileNavOpen ? 'is-open' : ''}`} aria-label="Main navigation">
                <div className="ises-sidebar-header">
                    <Link className="ises-brand" href="/dashboard" aria-label="Eco HydroTech home" onClick={() => setMobileNavOpen(false)}>
                        <span className="ises-brand-name">Eco HydroTech</span>
                        <span className="ises-brand-caption">Survey estimation workspace</span>
                    </Link>
                    <button
                        type="button"
                        className="ises-mobile-close-btn"
                        onClick={() => setMobileNavOpen(false)}
                        aria-label="Close navigation drawer"
                    >
                        <X size={18} strokeWidth={2} />
                    </button>
                </div>
                <nav className="ises-nav" aria-label="Workspace pages">
                    {[...navigation, ...management].map(({ label, href, icon: Icon }) => (
                        /* Note: /dashboard, /admin, and /admin/equipment currently render via Blade shell,
                           so standard <a> tags are retained until those pages are migrated to React in subsequent phases. */
                        (href === '/projects' || href === '/quotations/history') ? (
                            <Link
                                className={`ises-nav-link ${isActive(href) ? 'is-active' : ''}`}
                                href={href}
                                aria-current={isActive(href) ? 'page' : undefined}
                                key={label}
                                onClick={() => setMobileNavOpen(false)}
                            >
                                <span className="ises-nav-icon"><Icon size={18} strokeWidth={1.9} /></span>{label}
                            </Link>
                        ) : (
                            <a
                                className={`ises-nav-link ${isActive(href) ? 'is-active' : ''}`}
                                href={href}
                                aria-current={isActive(href) ? 'page' : undefined}
                                key={label}
                                onClick={() => setMobileNavOpen(false)}
                            >
                                <span className="ises-nav-icon"><Icon size={18} strokeWidth={1.9} /></span>{label}
                            </a>
                        )
                    ))}
                </nav>
                <div className="ises-sidebar-account" aria-label="Account actions">
                    <Link
                        className="ises-sidebar-user-card"
                        href="/profile"
                        aria-label="Profile settings"
                        title="Profile settings"
                        onClick={() => setMobileNavOpen(false)}
                    >
                        <span className="ises-avatar" aria-label={'Signed-in user: ' + userName}>
                            {initials || 'U'}
                        </span>
                        <div className="ises-sidebar-user-info">
                            <span className="ises-sidebar-user-name">{userName}</span>
                            <span className="ises-sidebar-user-role">Profile settings</span>
                        </div>
                    </Link>
                    <form method="post" action="/logout" className="ises-sidebar-logout-form">
                        <input type="hidden" name="_token" value={(page.props as { csrfToken?: string }).csrfToken || ''} />
                        <button
                            className="ises-sidebar-logout-btn"
                            type="submit"
                            aria-label="Log out"
                            title="Log out"
                        >
                            <LogOut size={16} strokeWidth={2} />
                        </button>
                    </form>
                </div>
            </aside>
            <div className="ises-main">
                <header className="ises-topbar">
                    <div className="ises-topbar-left">
                        <button
                            type="button"
                            className="ises-mobile-nav-toggle"
                            aria-label={mobileNavOpen ? "Close navigation menu" : "Open navigation menu"}
                            aria-expanded={mobileNavOpen}
                            onClick={() => setMobileNavOpen((prev) => !prev)}
                        >
                            {mobileNavOpen ? <X size={19} strokeWidth={2} /> : <Menu size={19} strokeWidth={2} />}
                        </button>
                        <div className="ises-breadcrumb"><span>Workspace</span><span aria-hidden="true"> / </span><strong>{title}</strong></div>
                    </div>
                    <div className="ises-topbar-right">
                        <span className="ises-topbar-date">{today}</span>
                        <span className="ises-avatar" aria-label={'Signed-in user: ' + userName}>{initials || 'U'}</span>
                        <Link className="ises-panel-action" href="/profile">{userName}</Link>
                    </div>
                </header>
                <main className="ises-content">{children}</main>
            </div>
        </div>
    );
}
