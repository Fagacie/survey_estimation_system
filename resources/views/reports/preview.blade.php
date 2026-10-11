<x-app-layout containerClass="w-full bg-slate-50 p-0">
    <x-slot name="header">Survey Report Preview</x-slot>

    @push('styles')
    <style>
        .preview-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 2rem;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .preview-title-badge {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .preview-tag {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
        }
        .preview-tag-sbes {
            background: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
        }
        .preview-tag-drone {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .preview-tag-mixed {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .preview-iframe {
            width: 100%;
            min-height: 297mm;
            border: none;
            display: block;
        }
        .btn-outline-dark-minimal {
            background: transparent;
            border: 1px solid #0f172a;
            color: #0f172a;
            padding: 0.5rem 1rem;
            font-size: 0.825rem;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.15s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-outline-dark-minimal:hover {
            background: #0f172a;
            color: #fff;
        }
        .btn-dark-minimal {
            background: #0f172a;
            border: 1px solid #0f172a;
            color: #fff;
            padding: 0.5rem 1.15rem;
            font-size: 0.825rem;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.15s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 4px rgba(15,23,42,0.15);
        }
        .btn-dark-minimal:hover {
            background: #1e293b;
            border-color: #1e293b;
            color: #fff;
        }
        .preview-container {
            padding: 2.5rem 1rem;
            background: #f1f5f9;
            display: flex;
            justify-content: center;
        }
        .preview-paper {
            background: white;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid #cbd5e1;
            width: 210mm;
            min-height: 297mm;
            border-radius: 4px;
            overflow: hidden;
        }
    </style>
    @endpush

    <div class="preview-toolbar">
        <div class="preview-title-badge">
            <a href="{{ route('projects.show', $project->project_Id) }}" class="btn-outline-dark-minimal">
                <i class="fa-solid fa-arrow-left"></i> Back to Project
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-800 text-sm">{{ $report_title }}</span>
                    @if($report_type === 'drone')
                        <span class="preview-tag preview-tag-drone">UAV Drone Mapping</span>
                    @elseif($report_type === 'sbes')
                        <span class="preview-tag preview-tag-sbes">Hydrographic Survey</span>
                    @else
                        <span class="preview-tag preview-tag-mixed">Master Operations</span>
                    @endif
                </div>
                <div class="text-[11px] text-slate-500 font-medium">Scope: {{ $report_subtitle }} &bull; Total Duration: {{ number_format($summary['sbes']['total_days'] ?? 0, 2) }} days</div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('projects.report.pdf', array_filter(['project' => $project->project_Id, 'location_id' => $locationId])) }}" class="btn-dark-minimal">
                <i class="fa-solid fa-file-arrow-down"></i> Download PDF
            </a>
        </div>
    </div>

    <div class="preview-container">
        <div class="preview-paper">
            <iframe src="{{ route('projects.report.preview', array_filter(['project' => $project->project_Id, 'location_id' => $locationId, 'raw' => 1, 't' => time()])) }}" class="preview-iframe"></iframe>
        </div>
    </div>
</x-app-layout>
