import ProjectForm from '../components/ProjectForm';

type ProjectRecord = { id: number; number: string | null; name: string; projectType: string | null; location: string | null; status: string | null; period: string | null; clientName: string | null; clientAddress: string | null; picName: string | null; picNo: string | null };
type Props = { project: ProjectRecord; projectTypes: Record<string, string>; locations: string[]; csrfToken: string; oldInput?: Record<string, string | undefined>; today: string };

export default function EditProject({ project, ...props }: Props) {
    return <ProjectForm {...props} mode="edit" project={project} />;
}
