import type { FormEvent, ReactNode } from 'react';
import { usePage } from '@inertiajs/react';
import { ArrowLeft, ArrowRight, Check } from 'lucide-react';
import { AppLayout } from '../layouts/AppLayout';

type ProjectRecord = {
    id: number;
    number: string | null;
    name: string;
    projectType: string | null;
    location: string | null;
    status: string | null;
    period: string | null;
    clientName: string | null;
    clientAddress: string | null;
    picName: string | null;
    picNo: string | null;
};
type Props = {
    mode: 'create' | 'edit';
    projectTypes: Record<string, string>;
    locations: string[];
    csrfToken: string;
    oldInput?: Record<string, string | undefined>;
    project?: ProjectRecord;
    today: string;
};
type SharedProps = { errors?: Record<string, string> };

function Field({
    label,
    name,
    required,
    error,
    children,
    span2 = false,
}: {
    label: string;
    name: string;
    required?: boolean;
    error?: string;
    children: ReactNode;
    span2?: boolean;
}) {
    return (
        <div className={`ises-form-field ${span2 ? 'ises-form-span-2' : ''}`}>
            <label htmlFor={name}>
                {label}
                {required && <span className="ises-required"> *</span>}
            </label>
            {children}
            {error && <p className="ises-field-error" role="alert">{error}</p>}
        </div>
    );
}

export default function ProjectForm({ mode, projectTypes, locations, csrfToken, oldInput = {}, project, today }: Props) {
    const isEdit = mode === 'edit';
    const page = usePage();
    const errors = (page.props as SharedProps).errors || {};
    const value = (field: string, fallback: string | null = null) => oldInput[field] ?? fallback ?? '';
    const action = isEdit && project ? `/projects/${project.id}` : '/projects';
    const cancelHref = isEdit && project ? `/projects/${project.id}` : '/projects';

    const handleSubmit = (event: FormEvent<HTMLFormElement>) => {
        const form = event.currentTarget;
        if (!form.reportValidity()) event.preventDefault();
    };

    return (
        <AppLayout title={isEdit ? 'Edit project' : 'New project'}>
            <section className="ises-page-header ises-form-page-header">
                <div>
                    <p className="ises-eyebrow">Project intake</p>
                    <h1 className="ises-page-title">{isEdit ? 'Edit project' : 'Create a project'}</h1>
                </div>
                <a className="ises-button ises-button-secondary" href="/projects">
                    <ArrowLeft size={15} /> Project register
                </a>
            </section>

            {Object.keys(errors).length > 0 && (
                <div className="ises-project-feedback ises-form-error-summary" role="alert">
                    <span className="ises-form-error-mark">!</span>
                    <div>
                        <strong>Please review the highlighted fields.</strong>
                    </div>
                </div>
            )}

            <form className="ises-project-form" action={action} method="POST" onSubmit={handleSubmit}>
                <input type="hidden" name="_token" value={csrfToken} />
                {isEdit && <input type="hidden" name="_method" value="PUT" />}

                <div className="ises-form-section">
                    <div className="ises-form-grid">
                        {isEdit && (
                            <Field label="Project number" name="project_number" span2>
                                <input
                                    id="project_number"
                                    className="ises-form-input ises-form-readonly"
                                    value={project?.number || 'Not assigned'}
                                    readOnly
                                />
                            </Field>
                        )}

                        <Field label="Project name" name="name" required error={errors.name} span2>
                            <input
                                id="name"
                                name="name"
                                className={`ises-form-input ${errors.name ? 'has-error' : ''}`}
                                defaultValue={value('name', project?.name)}
                                placeholder="e.g. Nearshore hydrographic survey"
                                maxLength={255}
                                required
                            />
                        </Field>

                        <Field label="Project type" name="project_type" required={!isEdit} error={errors.project_type}>
                            <select
                                id="project_type"
                                name="project_type"
                                className={`ises-form-input ${errors.project_type ? 'has-error' : ''}`}
                                defaultValue={value('project_type', project?.projectType)}
                                required={!isEdit}
                            >
                                <option value="">{isEdit ? 'Keep existing type' : 'Choose a project type'}</option>
                                {Object.entries(projectTypes).map(([code, label]) => (
                                    <option value={code} key={code}>
                                        {code} · {label}
                                    </option>
                                ))}
                            </select>
                        </Field>

                        <Field label="Survey location" name="location" required={!isEdit} error={errors.location}>
                            <select
                                id="location"
                                name="location"
                                className={`ises-form-input ${errors.location ? 'has-error' : ''}`}
                                defaultValue={value('location', project?.location)}
                                required={!isEdit}
                            >
                                <option value="">{isEdit ? 'Keep existing location' : 'Choose a location'}</option>
                                {locations.map((loc) => (
                                    <option value={loc} key={loc}>
                                        {loc}
                                    </option>
                                ))}
                            </select>
                        </Field>

                        <Field label="Project status" name="status" required error={errors.status}>
                            <select
                                id="status"
                                name="status"
                                className={`ises-form-input ${errors.status ? 'has-error' : ''}`}
                                defaultValue={value('status', project?.status || 'draft')}
                                required
                            >
                                <option value="draft">Draft</option>
                                <option value="planned">Planned</option>
                                <option value="completed">Completed</option>
                            </select>
                        </Field>

                        <Field label="Estimated duration" name="period" error={errors.period}>
                            <input
                                id="period"
                                name="period"
                                className={`ises-form-input ${errors.period ? 'has-error' : ''}`}
                                defaultValue={value('period', project?.period)}
                                placeholder="e.g. 5 days"
                                maxLength={255}
                            />
                        </Field>

                        <Field label="Client company" name="client_name" error={errors.client_name}>
                            <input
                                id="client_name"
                                name="client_name"
                                className={`ises-form-input ${errors.client_name ? 'has-error' : ''}`}
                                defaultValue={value('client_name', project?.clientName)}
                                placeholder="Client or organisation name"
                                maxLength={255}
                            />
                        </Field>

                        <Field label="Person in charge" name="pic_name" error={errors.pic_name}>
                            <input
                                id="pic_name"
                                name="pic_name"
                                className={`ises-form-input ${errors.pic_name ? 'has-error' : ''}`}
                                defaultValue={value('pic_name', project?.picName)}
                                placeholder="Contact person"
                                maxLength={255}
                            />
                        </Field>

                        <Field label="Contact details" name="pic_no" error={errors.pic_no}>
                            <input
                                id="pic_no"
                                name="pic_no"
                                className={`ises-form-input ${errors.pic_no ? 'has-error' : ''}`}
                                defaultValue={value('pic_no', project?.picNo)}
                                placeholder="Phone or email"
                                maxLength={255}
                            />
                        </Field>

                        <Field label="Client address" name="client_address" error={errors.client_address}>
                            <textarea
                                id="client_address"
                                name="client_address"
                                className={`ises-form-input ises-form-textarea ${errors.client_address ? 'has-error' : ''}`}
                                defaultValue={value('client_address', project?.clientAddress)}
                                placeholder="Address for correspondence"
                                rows={2}
                                maxLength={1000}
                            />
                        </Field>
                    </div>

                    <footer className="ises-project-form-footer">
                        <span>Fields marked with * are required.</span>
                        <div>
                            <a className="ises-button ises-button-secondary" href={cancelHref}>
                                Cancel
                            </a>
                            <button className="ises-button" type="submit">
                                <Check size={15} /> {isEdit ? 'Save changes' : 'Create project'} <ArrowRight size={15} />
                            </button>
                        </div>
                    </footer>
                </div>
            </form>
            <footer className="ises-footer">Eco HydroTech · Project intake · {today}</footer>
        </AppLayout>
    );
}
