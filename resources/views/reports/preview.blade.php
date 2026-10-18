<x-app-layout containerClass="w-full bg-slate-50 p-0">
    <x-slot name="header">Survey Report Preview</x-slot>

    @push('styles')
    <style>
        .report-preview-page {
            --report-ink: #142f49;
            --report-muted: #62758a;
            --report-line: #dce4ec;
            background: #f3f6f9;
            min-height: calc(100vh - 4rem);
            color: var(--report-ink);
        }
        .report-preview-toolbar {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            padding: 1.15rem clamp(1rem, 3vw, 2.5rem);
            background: #ffffff;
            border-bottom: 1px solid var(--report-line);
        }
        .report-preview-toolbar::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: #1685b5;
        }
        .report-preview-heading {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 0;
        }
        .report-preview-heading-copy { min-width: 0; }
        .report-preview-eyebrow {
            margin: 0 0 0.3rem;
            color: #1685b5;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }
        .report-preview-title {
            margin: 0;
            color: #142f49;
            font-size: clamp(1rem, 1.4vw, 1.25rem);
            font-weight: 750;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }
        .report-preview-subtitle {
            margin-top: 0.35rem;
            color: var(--report-muted);
            font-size: 0.82rem;
            line-height: 1.5;
        }
        .report-preview-type {
            display: inline-flex;
            align-items: center;
            margin-top: 0.65rem;
            padding: 0.3rem 0.55rem;
            background: #edf6fa;
            border: 1px solid #d1e8f2;
            color: #176487;
            border-radius: 3px;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.055em;
            text-transform: uppercase;
        }
        .report-preview-type-drone {
            background: #eef6f0;
            border-color: #d6e8da;
            color: #2e6844;
        }
        .report-preview-type-mixed {
            background: #f1f4f7;
            border-color: #dfe5eb;
            color: #4b5c6c;
        }
        .report-preview-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.6rem;
            flex-shrink: 0;
        }
        .report-preview-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 40px;
            padding: 0.65rem 0.9rem;
            border: 1px solid #cbd6e0;
            border-radius: 4px;
            background: #fff;
            color: #243f58 !important;
            font-size: 0.8rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            transition: background-color 120ms ease, border-color 120ms ease, color 120ms ease;
        }
        .report-preview-button:hover {
            background: #f5f8fa;
            border-color: #91a6b8;
            color: #102b46 !important;
        }
        .report-preview-button-primary {
            background: #102b46 !important;
            border-color: #102b46 !important;
            color: #fff !important;
        }
        .report-preview-button-primary:hover {
            background: #1b4262 !important;
            border-color: #1b4262 !important;
            color: #fff !important;
        }
        .report-preview-stage {
            padding: clamp(1rem, 3vw, 2.25rem) 1rem 3rem;
        }
        .report-preview-stage-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            max-width: 210mm;
            margin: 0 auto 0.8rem;
            color: #62758a;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .report-preview-paper {
            width: 210mm;
            max-width: 100%;
            min-height: 297mm;
            margin: 0 auto;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #d5dee7;
            box-shadow: 0 4px 16px rgba(20, 47, 73, 0.08);
        }
        .report-preview-frame {
            display: block;
            width: 100%;
            height: 594mm;
            border: 0;
            background: #ffffff;
        }
        @media (max-width: 760px) {
            .report-preview-toolbar {
                align-items: stretch;
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            .report-preview-heading { gap: 0.7rem; }
            .report-preview-actions {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            }
            .report-preview-button { padding-inline: 0.55rem; }
            .report-preview-stage { padding: 1rem 0.5rem 2rem; }
            .report-preview-paper { width: 100%; }
            .report-preview-frame { height: 594mm; }
            .report-preview-stage-head { padding-inline: 0.35rem; }
        }
        @media (prefers-reduced-motion: reduce) {
            .report-preview-button { transition: none; }
        }
    </style>
    @endpush

    <main class="report-preview-page">
        <header class="report-preview-toolbar">
            <div class="report-preview-heading">
                <div class="report-preview-heading-copy">
                    <p class="report-preview-eyebrow">Eco Hydrotech Solutions / Report Centre</p>
                    <h1 class="report-preview-title">{{ $report_title }}</h1>
                    <div class="report-preview-subtitle">
                        {{ $is_single ? 'Survey area: ' . $report_subtitle : 'Project: ' . $project->name }}
                        <span aria-hidden="true"> &bull; </span>
                        Reference {{ $project->number ?? 'N/A' }}
                    </div>
                    @if($report_type === 'drone')
                        <span class="report-preview-type report-preview-type-drone">UAV Drone Mapping</span>
                    @elseif($report_type === 'sbes')
                        <span class="report-preview-type">Hydrographic SBES</span>
                    @else
                        <span class="report-preview-type report-preview-type-mixed">Survey Operations</span>
                    @endif
                </div>
            </div>

            <nav class="report-preview-actions" aria-label="Report actions">
                <a href="{{ route('projects.show', $project->project_Id) }}" class="report-preview-button">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    <span>Back to project</span>
                </a>
                <a href="{{ route('projects.report.pdf', array_filter(['project' => $project->project_Id, 'location_id' => $locationId])) }}" class="report-preview-button report-preview-button-primary">
                    <i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i>
                    <span>Download PDF</span>
                </a>
            </nav>
        </header>

        <section class="report-preview-stage" aria-label="Survey report document preview">
            <div class="report-preview-stage-head">
                <span>Document preview</span>
                <span>A4 &bull; Portrait</span>
            </div>
            <div class="report-preview-paper">
                <iframe
                    title="Survey report document"
                    src="{{ route('projects.report.preview', array_filter(['project' => $project->project_Id, 'location_id' => $locationId, 'raw' => 1, 't' => time()])) }}"
                    class="report-preview-frame"></iframe>
            </div>
        </section>
    </main>
</x-app-layout>
