<?php

namespace App\Http\Controllers;

use App\Services\Report\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function downloadReport(string $id)
    {
        set_time_limit(300); // Allow up to 5 minutes for report generation
        $project = auth()->user()->projects()->findOrFail($id);
        $data = $this->reportService->compileReportData($project);

        $this->reportService->trackReport($project, 'full_report', $data['report_number']);

        $pdf = Pdf::loadView('reports.unified', $data);
        $pdf->setPaper('A4', 'portrait');

        $filename = 'Survey_Proposal_' . $project->project_code . '_' . now()->format('Ymd') . '.pdf';
        return $pdf->download($filename);
    }



    public function preview(string $id)
    {
        set_time_limit(300); // Allow up to 5 minutes for report generation
        $project = auth()->user()->projects()->findOrFail($id);
        $data = $this->reportService->compileReportData($project);
        return view('reports.unified', $data);
    }

    public function captureMap(string $projectId, string $locationId)
    {
        $project = \App\Models\Project::findOrFail($projectId);
        $location = $project->surveyLocations()->findOrFail($locationId);

        $boundaries = [
            'type' => 'FeatureCollection',
            'features' => $location->boundaries->map(fn($b) => $b->geometry)->toArray()
        ];
        
        $lines = [
            'type' => 'FeatureCollection',
            'features' => $location->surveyLines->map(fn($l) => $l->geometry)->toArray()
        ];

        return view('reports.map-capture', [
            'location' => $location,
            'boundaries' => json_encode($boundaries),
            'lines' => json_encode($lines)
        ]);
    }
}
