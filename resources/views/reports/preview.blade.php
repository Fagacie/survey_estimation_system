<x-app-layout containerClass="w-full bg-slate-50 p-0">
    <x-slot name="header">Survey Report Preview</x-slot>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @push('styles')
    <style>
        .report-preview-page {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            min-height: calc(100vh - 4rem);
            color: #1e293b;
        }

        .report-preview-breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #64748b;
            padding: 1rem clamp(1rem, 3vw, 2.5rem) 0;
        }

        .report-preview-breadcrumb a {
            color: #64748b;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .report-preview-breadcrumb a:hover {
            color: #0284c7;
        }

        .report-preview-breadcrumb-sep {
            color: #cbd5e1;
        }

        .report-preview-breadcrumb-current {
            color: #0f172a;
            font-weight: 600;
        }

        .report-preview-toolbar {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            padding: 1.25rem clamp(1rem, 3vw, 2.5rem);
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            margin-top: 0.75rem;
        }

        .report-preview-toolbar::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 4px;
            background: linear-gradient(180deg, #0284c7 0%, #0d9488 100%);
        }

        .report-preview-heading {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            min-width: 0;
        }

        .report-preview-heading-copy { min-width: 0; }

        .report-preview-eyebrow {
            margin: 0 0 0.35rem;
            color: #0284c7;
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .report-preview-title {
            margin: 0;
            color: #0f172a;
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(1.25rem, 1.8vw, 1.6rem);
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: -0.025em;
            overflow-wrap: anywhere;
        }

        .report-preview-subtitle {
            margin-top: 0.35rem;
            color: #64748b;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .report-preview-type {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 0.6rem;
            padding: 0.25rem 0.65rem;
            background: #e0f2fe;
            border: 1px solid rgba(2, 132, 199, 0.25);
            color: #0369a1;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .report-preview-type-drone {
            background: #ecfdf5;
            border-color: rgba(16, 185, 129, 0.25);
            color: #047857;
        }

        .report-preview-type-mixed {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #334155;
        }

        .report-preview-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-shrink: 0;
            flex-wrap: wrap;
        }

        .report-preview-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 0.5rem 1.1rem;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            background: #ffffff;
            color: #334155 !important;
            font-size: 0.84rem;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .report-preview-button:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a !important;
            transform: translateY(-1px);
        }

        .report-preview-button-primary {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
            border-color: rgba(2, 132, 199, 0.3) !important;
            color: #ffffff !important;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);
        }

        .report-preview-button-primary:hover {
            background: linear-gradient(135deg, #0369a1 0%, #075985 100%) !important;
            border-color: #0369a1 !important;
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(2, 132, 199, 0.3);
        }

        .report-preview-stage {
            padding: clamp(1.25rem, 3vw, 2.5rem) 1rem 3.5rem;
        }

        .report-preview-stage-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            max-width: 210mm;
            margin: 0 auto 0.85rem;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .report-preview-paper {
            width: 210mm;
            max-width: 100%;
            min-height: 297mm;
            margin: 0 auto;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(15, 23, 42, 0.04);
        }

        .report-preview-frame {
            display: block;
            width: 100%;
            height: 594mm;
            border: 0;
            background: #ffffff;
        }

        @media (max-width: 768px) {
            .report-preview-toolbar {
                align-items: stretch;
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            .report-preview-actions {
                width: 100%;
            }
            .report-preview-button {
                flex: 1;
            }
            .report-preview-paper { width: 100%; }
        }
    </style>
    @endpush

    <main class="report-preview-page">

        {{-- Breadcrumb Navigation --}}
        <nav class="report-preview-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}"><i class="bi bi-house-door"></i> Dashboard</a>
            <span class="report-preview-breadcrumb-sep">/</span>
            <a href="{{ route('projects.index') }}">Projects</a>
            <span class="report-preview-breadcrumb-sep">/</span>
            <a href="{{ route('projects.show', $project->project_Id) }}">{{ Str::limit($project->name, 26) }}</a>
            <span class="report-preview-breadcrumb-sep">/</span>
            <span class="report-preview-breadcrumb-current">Survey Report Preview</span>
        </nav>

        {{-- Toolbar --}}
        <header class="report-preview-toolbar">
            <div class="report-preview-heading">
                <div class="report-preview-heading-copy">
                    <p class="report-preview-eyebrow">Eco Hydrotech Solutions &bull; Report Centre</p>
                    <h1 class="report-preview-title">{{ $report_title }}</h1>
                    <div class="report-preview-subtitle">
                        {{ $is_single ? 'Survey Area: ' . $report_subtitle : 'Project: ' . $project->name }}
                        <span aria-hidden="true"> &bull; </span>
                        Reference {{ $project->number ?? 'N/A' }}
                    </div>
                    @if($report_type === 'drone')
                        <span class="report-preview-type report-preview-type-drone"><i class="bi bi-airplane-engines"></i> UAV Drone Mapping</span>
                    @elseif($report_type === 'sbes')
                        <span class="report-preview-type"><i class="bi bi-water"></i> Hydrographic SBES</span>
                    @else
                        <span class="report-preview-type report-preview-type-mixed"><i class="bi bi-layers"></i> Survey Operations</span>
                    @endif
                </div>
            </div>

            <nav class="report-preview-actions" aria-label="Report actions">
                <a href="{{ route('projects.show', $project->project_Id) }}" class="report-preview-button">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    <span>Back to Project</span>
                </a>
                <a href="{{ route('projects.report.pdf', array_filter(['project' => $project->project_Id, 'location_id' => $locationId])) }}" class="report-preview-button report-preview-button-primary">
                    <i class="fa-solid fa-file-arrow-down" aria-hidden="true"></i>
                    <span>Download PDF</span>
                </a>
            </nav>
        </header>

        {{-- Document Preview Stage --}}
        <section class="report-preview-stage" aria-label="Survey report document preview">
            <div class="report-preview-stage-head">
                <span><i class="bi bi-file-earmark-pdf me-1"></i> Document Preview</span>
                <span>A4 &bull; Portrait Format</span>
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
