<x-app-layout containerClass="w-full bg-slate-50 p-0">
    <x-slot name="header">Survey Report Preview</x-slot>

    @push('styles')
    <style>
        .preview-toolbar {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            padding: 1rem 2rem;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
        }
        .preview-iframe {
            width: 100%;
            min-height: 297mm;
            border: none;
            display: block;
        }
        .btn-outline-dark-minimal {
            background: transparent;
            border: 1px solid #111;
            color: #111;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-outline-dark-minimal:hover {
            background: #111;
            color: #fff;
        }
        .btn-dark-minimal {
            background: #111;
            border: 1px solid #111;
            color: #fff;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-dark-minimal:hover {
            background: #333;
            border-color: #333;
        }
        .preview-container {
            padding: 2rem;
            background: #f8fafc;
            display: flex;
            justify-content: center;
        }
        .preview-paper {
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            width: 210mm;
            overflow: hidden;
        }
    </style>
    @endpush

    <div class="preview-toolbar">
        <a href="{{ route('projects.show', $project->project_Id) }}" class="btn-outline-dark-minimal">
            <i class="fa-solid fa-arrow-left"></i> Back to Project
        </a>
        <a href="{{ route('projects.report.pdf', $project->project_Id) }}" class="btn-dark-minimal">
            <i class="fa-solid fa-download"></i> Download PDF
        </a>
    </div>

    <div class="preview-container">
        <div class="preview-paper">
            <iframe src="{{ route('projects.report.preview', $project->project_Id) }}?raw=1&t={{ time() }}" class="preview-iframe"></iframe>
        </div>
    </div>
</x-app-layout>
