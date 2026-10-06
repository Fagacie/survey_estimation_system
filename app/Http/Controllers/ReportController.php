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
        $locationId = request()->query('location_id');
        $data = $this->compile($id, $locationId);
        
        if (request()->has('raw')) {
            return view('reports.survey', $data);
        }
        
        return view('reports.preview', $data);
    }

    public function download(string $id): Response
    {
        $locationId = request()->query('location_id');
        $data = $this->compile($id, $locationId);
        $pdf = Pdf::loadView('reports.survey', $data)->setPaper('A4', 'portrait');

        $safeProjectNumber = str_replace(['/', '\\'], '-', $data['project']->number ?? 'Unknown');
        
        $isDrone = false;
        if ($data['locations']->count() > 0) {
            $isDrone = $data['locations']->first()['is_drone'];
        }
        $prefix = $isDrone ? 'Drone_Mapping_Report_' : 'Survey_Report_';
        
        return $pdf->download($prefix.$safeProjectNumber.'_'.now()->format('Ymd').'.pdf');
    }

    private function compile(string $id, ?string $locationId = null): array
    {
        $project = auth()->user()->projects()
            ->with(['client', 'surveyLocations.boundaries', 'surveyLocations.surveyLines', 'surveyLocations.sbesParameters', 'surveyLocations.droneMappingParameters'])
            ->findOrFail($id);

        $locations = $project->surveyLocations;
        if ($locationId) {
            $locations = $locations->where('id', $locationId);
        }

        $locations = $locations->map(function ($location) {
            $isDrone = $location->survey_type === 'drone';

            if ($isDrone) {
                $droneParams = $location->droneMappingParameters;
                $distanceMeters = (float) ($droneParams?->total_flight_distance_m ?? 0);
                $speedMs = (float) ($droneParams?->speed_ms ?? 0);
                $durationHours = (float) ($droneParams?->estimated_duration_hours ?? 0);
                $pureFlightTimeS = $speedMs > 0 ? $distanceMeters / $speedMs : 0;
                
                return [
                    'name' => $location->name,
                    'is_drone' => true,
                    'distance_m' => $distanceMeters,
                    'pure_flight_time_s' => (int) $pureFlightTimeS,
                    'survey_hours' => $durationHours,
                    'execution_days' => 0, // Drone mapping may be calculated differently or kept 0 if not used
                    
                    'drone_model' => $droneParams?->drone_model ?? 'N/A',
                    'camera_model' => $droneParams?->camera_model ?? 'N/A',
                    'altitude_m' => $droneParams?->altitude_m ?? 0,
                    'target_gsd_cm' => $droneParams?->target_gsd_cm ?? 0,
                    'gsd_cm' => $droneParams?->gsd_cm ?? 0,
                    'front_overlap' => $droneParams?->front_overlap_percent ?? 0,
                    'side_overlap' => $droneParams?->side_overlap_percent ?? 0,
                    'speed_ms' => $speedMs,
                    'course_angle' => $droneParams?->course_angle_deg ?? 0,
                    'photo_spacing_m' => $droneParams?->photo_spacing_m ?? 0,
                    'photo_interval_s' => $droneParams?->photo_interval_s ?? 0,
                    'usable_flight_time_min' => $droneParams?->usable_flight_time_min ?? 0,
                    'sortie_count' => $droneParams?->sortie_count ?? 1,
                    
                    'line_count' => $location->surveyLines->count(),
                    'flight_lines' => $location->surveyLines->where('type', 'main')->count(),
                    'total_images' => $droneParams?->total_images ?? 0,
                    
                    'boundary_count' => $location->boundaries->count(),
                    'boundary_area' => $location->boundaries->sum('area'),
                    'screenshot' => $this->screenshotData($location->id),
                ];
            } else {
                $distance = (float) ($location->sbesParameters?->total_distance_nm ?? 0);
                $speed = (float) ($location->sbesParameters?->survey_speed_knots ?? 0);
                $hoursPerDay = (float) ($location->sbesParameters?->working_hours_per_day ?? 8.0);
                if ($hoursPerDay <= 0) {
                    $hoursPerDay = 8.0;
                }
                $hours = $speed > 0 ? $distance / $speed : 0;

                return [
                    'name' => $location->name,
                    'is_drone' => false,
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
            }
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
