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
        $project = auth()->user()->projects()->findOrFail($id);
        $data = $this->reportService->compileReportData($project);
        return view('reports.unified', $data);
    }
}
