import ProjectForm from '../components/ProjectForm';

type Props = { projectTypes: Record<string, string>; locations: string[]; csrfToken: string; oldInput?: Record<string, string | undefined>; today: string };

export default function CreateProject(props: Props) {
    return <ProjectForm {...props} mode="create" />;
}
