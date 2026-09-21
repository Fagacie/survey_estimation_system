<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\Calculation\ProjectEstimationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    public function __construct(private ProjectEstimationService $estimationService)
    {
    }

    public function preview(string $id)
    {
        $data = $this->compile($id);
        
        if (request()->has('raw')) {
            return view('reports.survey', $data);
        }
        
        return view('reports.preview', $data);
    }

    public function download(string $id): Response
    {
        $data = $this->compile($id);
        $pdf = Pdf::loadView('reports.survey', $data)->setPaper('A4', 'portrait');

        $safeProjectNumber = str_replace(['/', '\\'], '-', $data['project']->number ?? 'Unknown');
        return $pdf->download('Survey_Report_'.$safeProjectNumber.'_'.now()->format('Ymd').'.pdf');
    }

    private function compile(string $id): array
    {
        $project = auth()->user()->projects()
            ->with(['client', 'surveyLocations.boundaries', 'surveyLocations.surveyLines', 'surveyLocations.sbesParameters'])
            ->findOrFail($id);

        $locations = $project->surveyLocations->map(function ($location) {
            $distance = (float) ($location->sbesParameters?->total_distance_nm ?? 0);
            $speed = (float) ($location->sbesParameters?->survey_speed_knots ?? 0);
            $hoursPerDay = (float) ($location->sbesParameters?->working_hours_per_day ?? 8.0);
            if ($hoursPerDay <= 0) {
                $hoursPerDay = 8.0;
            }
            $hours = $speed > 0 ? $distance / $speed : 0;

            return [
                'name' => $location->name,
                'distance_nm' => $distance,
                'survey_hours' => $hours,
                'execution_days' => $hoursPerDay > 0 ? $hours / $hoursPerDay : 0,
                'line_count' => $location->surveyLines->count(),
                'main_line_count' => $location->surveyLines->where('type', 'main')->count(),
                'cross_line_count' => $location->surveyLines->where('type', 'cross')->count(),
                'boundary_count' => $location->boundaries->count(),
                'boundary_area' => $location->boundaries->sum('area'),
                'screenshot' => $this->screenshotData($location->id),
            ];
        })->values();

        return [
            'project' => $project,
            'locations' => $locations,
            'duration' => $this->estimationService->calculate($project),
            'generated_at' => now(),
        ];
    }

    private function screenshotData(int $locationId): ?string
    {
        $path = storage_path('app/public/maps/'.$locationId.'.png');
        if (!is_file($path)) {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode(file_get_contents($path));
    }
}
