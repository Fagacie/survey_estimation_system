import { useState, type FormEvent } from 'react';
import { usePage } from '@inertiajs/react';
import { AlertTriangle, ArrowLeft, CheckCircle2, Clock3, KeyRound, LockKeyhole, Mail, ShieldCheck, UserRound, X } from 'lucide-react';
import { AppLayout } from '../layouts/AppLayout';

type ProfileUser = { id: number; name: string; email: string; createdAt: string | null; emailVerified: boolean };
type Props = { user: ProfileUser; csrfToken: string; oldInput?: { name?: string; email?: string }; status?: string | null; updatePasswordErrors?: Record<string, string[]>; userDeletionErrors?: Record<string, string[]>; today: string };
type SharedProps = { errors?: Record<string, string>; flash?: { success?: string | null; error?: string | null } };

export default function ProfileSettings({ user, csrfToken, oldInput = {}, status, updatePasswordErrors = {}, userDeletionErrors = {}, today }: Props) {
    const [deleteOpen, setDeleteOpen] = useState(Object.keys(userDeletionErrors).length > 0);
    const page = usePage();
    const shared = page.props as SharedProps;
    const errors = shared.errors || {};
    const first = (bag: Record<string, string[]>, key: string) => bag[key]?.[0];
    const name = oldInput.name ?? user.name;
    const email = oldInput.email ?? user.email;
    const initials = user.name.trim().split(/\s+/).filter(Boolean).slice(0, 2).map((part) => part[0]).join('').toUpperCase();
    const saved = status === 'profile-updated' || status === 'password-updated';

    const keepFormNative = (event: FormEvent<HTMLFormElement>) => {
        if (!event.currentTarget.reportValidity()) event.preventDefault();
    };

    return (
        <AppLayout title="Account settings">
            <section className="ises-page-header ises-profile-page-header">
                <div><p className="ises-eyebrow">Account / preferences</p><h1 className="ises-page-title">My profile</h1><p className="ises-page-description">Manage your account details, password and account access.</p></div>
                <a href="/dashboard" className="ises-button ises-button-secondary"><ArrowLeft size={15} /> Back to workspace</a>
            </section>

            {saved && <div className="ises-profile-feedback is-success" role="status"><CheckCircle2 size={17} /><span>{status === 'password-updated' ? 'Your password has been updated.' : 'Your profile changes have been saved.'}</span></div>}
            {status === 'verification-link-sent' && <div className="ises-profile-feedback is-success" role="status"><Mail size={17} /><span>A fresh verification link has been sent to your email address.</span></div>}
            {shared.flash?.success && <div className="ises-profile-feedback is-success" role="status"><CheckCircle2 size={17} /><span>{shared.flash.success}</span></div>}
            {shared.flash?.error && <div className="ises-profile-feedback is-error" role="alert"><AlertTriangle size={17} /><span>{shared.flash.error}</span></div>}

            <section className="ises-profile-overview">
                <div className="ises-profile-avatar" aria-hidden="true">{initials || 'U'}</div>
                <div className="ises-profile-overview-details"><span className="ises-profile-kicker">Signed-in account</span><h2>{user.name}</h2><p><Mail size={14} /> {user.email}</p><span className={`ises-profile-verified ${user.emailVerified ? 'is-verified' : 'is-pending'}`}>{user.emailVerified ? <><CheckCircle2 size={12} /> Email verified</> : <><Clock3 size={12} /> Verification pending</>}</span></div>
                <div className="ises-profile-created"><span>Account created</span><strong>{user.createdAt || 'Date unavailable'}</strong><span className="ises-profile-created-note">Member account · Eco HydroTech</span></div>
            </section>

            <div className="ises-profile-grid">
                <div className="ises-profile-main">
                    <section className="ises-panel ises-profile-panel">
                        <header className="ises-panel-header"><div className="ises-profile-section-title"><span className="ises-profile-section-icon"><UserRound size={17} /></span><div><h2 className="ises-panel-title">Personal information</h2><p className="ises-panel-description">Update the name and email address associated with your account.</p></div></div></header>
                        {!user.emailVerified && <form id="resend-verification" method="POST" action="/email/verification-notification"><input type="hidden" name="_token" value={csrfToken} /></form>}
                        <form className="ises-profile-form" method="POST" action="/profile" onSubmit={keepFormNative}><input type="hidden" name="_token" value={csrfToken} /><input type="hidden" name="_method" value="PATCH" />
                            <div className="ises-profile-field"><label htmlFor="profile-name">Full name</label><input id="profile-name" name="name" type="text" className={`ises-form-input ${errors.name ? 'has-error' : ''}`} defaultValue={name} autoComplete="name" maxLength={255} required />{errors.name && <span className="ises-field-error" role="alert">{errors.name}</span>}</div>
                            <div className="ises-profile-field"><label htmlFor="profile-email">Email address</label><input id="profile-email" name="email" type="email" className={`ises-form-input ${errors.email ? 'has-error' : ''}`} defaultValue={email} autoComplete="email" maxLength={255} required />{errors.email && <span className="ises-field-error" role="alert">{errors.email}</span>}{!user.emailVerified && <div className="ises-profile-verification-note"><span>Your email address has not been verified.</span><button type="submit" form="resend-verification">Resend verification email</button></div>}</div>
                            <footer className="ises-profile-form-footer"><span>Changes apply to your account immediately after saving.</span><button type="submit" className="ises-button"><CheckCircle2 size={15} /> Save profile</button></footer>
                        </form>
                    </section>

                    <section className="ises-panel ises-profile-panel">
                        <header className="ises-panel-header"><div className="ises-profile-section-title"><span className="ises-profile-section-icon is-security"><KeyRound size={17} /></span><div><h2 className="ises-panel-title">Account security</h2><p className="ises-panel-description">Change your password. Use a unique password that you do not reuse elsewhere.</p></div></div></header>
                        <form className="ises-profile-form" method="POST" action="/password" onSubmit={keepFormNative}><input type="hidden" name="_token" value={csrfToken} /><input type="hidden" name="_method" value="PUT" />
                            <div className="ises-profile-field"><label htmlFor="current-password">Current password</label><input id="current-password" name="current_password" type="password" className={`ises-form-input ${first(updatePasswordErrors, 'current_password') ? 'has-error' : ''}`} autoComplete="current-password" required />{first(updatePasswordErrors, 'current_password') && <span className="ises-field-error" role="alert">{first(updatePasswordErrors, 'current_password')}</span>}</div>
                            <div className="ises-profile-form-row"><div className="ises-profile-field"><label htmlFor="new-password">New password</label><input id="new-password" name="password" type="password" className={`ises-form-input ${first(updatePasswordErrors, 'password') ? 'has-error' : ''}`} autoComplete="new-password" minLength={8} required />{first(updatePasswordErrors, 'password') && <span className="ises-field-error" role="alert">{first(updatePasswordErrors, 'password')}</span>}</div><div className="ises-profile-field"><label htmlFor="confirm-password">Confirm new password</label><input id="confirm-password" name="password_confirmation" type="password" className={`ises-form-input ${first(updatePasswordErrors, 'password_confirmation') ? 'has-error' : ''}`} autoComplete="new-password" minLength={8} required />{first(updatePasswordErrors, 'password_confirmation') && <span className="ises-field-error" role="alert">{first(updatePasswordErrors, 'password_confirmation')}</span>}</div></div>
                            <footer className="ises-profile-form-footer"><span><LockKeyhole size={13} /> Your password is never displayed.</span><button type="submit" className="ises-button"><KeyRound size={15} /> Update password</button></footer>
                        </form>
                    </section>
                </div>

                <aside className="ises-profile-aside">
                    <section className="ises-profile-security-card"><span className="ises-profile-security-icon"><ShieldCheck size={21} /></span><h2>Protect your account</h2><p>A unique password helps protect your project records, survey plans and quotation data.</p><ul><li><CheckCircle2 size={14} /> Use a long, unique password</li><li><CheckCircle2 size={14} /> Never share your sign-in details</li><li><CheckCircle2 size={14} /> Keep your email address current</li></ul></section>
                    <section className="ises-profile-danger-card"><div className="ises-profile-danger-heading"><span><AlertTriangle size={16} /></span><h2>Delete account</h2></div><p>Account deletion is permanent. You may lose access to information associated with this account.</p><button type="button" className="ises-button ises-button-danger ises-profile-delete-trigger" onClick={() => setDeleteOpen(true)}>Delete my account</button></section>
                </aside>
            </div>
            <footer className="ises-footer">Eco HydroTech · Account settings · {today}</footer>

            {deleteOpen && <div className="ises-modal-backdrop" role="presentation" onMouseDown={(event) => { if (event.target === event.currentTarget) setDeleteOpen(false); }}><section className="ises-confirm-dialog ises-profile-delete-dialog" role="dialog" aria-modal="true" aria-labelledby="profile-delete-title"><button type="button" className="ises-dialog-close" onClick={() => setDeleteOpen(false)} aria-label="Close dialog"><X size={17} /></button><span className="ises-confirm-icon"><AlertTriangle size={22} /></span><h2 id="profile-delete-title">Permanently delete your account?</h2><p>This cannot be undone. Enter your current password to confirm account deletion.</p><form method="POST" action="/profile" onSubmit={keepFormNative}><input type="hidden" name="_token" value={csrfToken} /><input type="hidden" name="_method" value="DELETE" /><div className="ises-profile-field"><label htmlFor="delete-password">Current password</label><input id="delete-password" name="password" type="password" autoComplete="current-password" className={`ises-form-input ${first(userDeletionErrors, 'password') ? 'has-error' : ''}`} required autoFocus />{first(userDeletionErrors, 'password') && <span className="ises-field-error" role="alert">{first(userDeletionErrors, 'password')}</span>}</div><div className="ises-confirm-actions"><button type="button" className="ises-button ises-button-secondary" onClick={() => setDeleteOpen(false)}>Cancel</button><button type="submit" className="ises-button ises-button-danger">Delete account</button></div></form></section></div>}
        </AppLayout>
    );
}
