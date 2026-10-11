<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\Calculation\ProjectEstimationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

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

        return $pdf->download($data['filename']);
    }

    private function compile(string $id, ?string $locationId = null): array
    {
        $project = auth()->user()->projects()
            ->with(['client', 'surveyLocations.boundaries', 'surveyLocations.surveyLines', 'surveyLocations.sbesParameters', 'surveyLocations.droneMappingParameters'])
            ->findOrFail($id);

        $allLocations = $project->surveyLocations;
        $filteredLocations = $allLocations;
        $isSingle = false;

        if ($locationId !== null && $locationId !== '') {
            $matched = $allLocations->where('id', (int) $locationId);
            if ($matched->isNotEmpty()) {
                $filteredLocations = $matched;
                $isSingle = true;
            }
        }

        // Determine Report Type & Title
        if ($isSingle) {
            $singleLoc = $filteredLocations->first();
            $reportType = $singleLoc->survey_type === 'drone' ? 'drone' : 'sbes';
            $reportTitle = $reportType === 'drone' ? 'DRONE MAPPING SURVEY REPORT' : 'HYDROGRAPHIC SURVEY REPORT';
            $reportSubtitle = $singleLoc->name;
            $safeLocName = Str::slug($singleLoc->name);
            $safeProjNo = Str::slug(str_replace(['/', '\\'], '-', $project->number ?? 'Project'));
            $prefix = $reportType === 'drone' ? 'Drone_Report_' : 'Hydrographic_Report_';
            $filename = $prefix . $safeLocName . '_' . $safeProjNo . '_' . now()->format('Ymd') . '.pdf';
        } else {
            $hasDrone = $filteredLocations->contains(fn($l) => $l->survey_type === 'drone');
            $hasSbes = $filteredLocations->contains(fn($l) => $l->survey_type !== 'drone');

            if ($hasDrone && !$hasSbes) {
                $reportType = 'drone';
                $reportTitle = 'DRONE MAPPING SURVEY REPORT';
            } elseif (!$hasDrone && $hasSbes) {
                $reportType = 'sbes';
                $reportTitle = 'HYDROGRAPHIC SURVEY REPORT';
            } else {
                $reportType = 'mixed';
                $reportTitle = 'SURVEY OPERATIONS MASTER REPORT';
            }

            $reportSubtitle = $project->name;
            $safeProjNo = Str::slug(str_replace(['/', '\\'], '-', $project->number ?? 'Project'));
            $prefix = $reportType === 'drone' ? 'Drone_Report_' : ($reportType === 'sbes' ? 'Hydrographic_Report_' : 'Survey_Report_');
            $filename = $prefix . $safeProjNo . '_' . now()->format('Ymd') . '.pdf';
        }

        $locationsData = $filteredLocations->map(function ($location) {
            $isDrone = $location->survey_type === 'drone';
            $lineCount = $location->surveyLines->count();
            $boundaryCount = $location->boundaries->count();
            $isMapped = ($location->status === 'Mapped') || ($lineCount > 0 && $boundaryCount > 0);

            if ($isDrone) {
                $droneParams = $location->droneMappingParameters;
                $distanceMeters = (float) ($droneParams?->total_flight_distance_m ?? 0);
                $speedMs = (float) ($droneParams?->speed_ms ?? 0);
                $durationHours = (float) ($droneParams?->estimated_duration_hours ?? 0);
                $pureFlightTimeS = $speedMs > 0 ? $distanceMeters / $speedMs : 0;
                $boundaryArea = (float) $location->boundaries->sum('area');

                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'survey_type' => 'drone',
                    'is_drone' => true,
                    'is_mapped' => $isMapped,
                    'status' => $location->status ?? ($isMapped ? 'Mapped' : 'Pending'),
                    'distance_m' => $distanceMeters,
                    'distance_km' => $distanceMeters / 1000,
                    'pure_flight_time_s' => (int) $pureFlightTimeS,
                    'survey_hours' => $durationHours,
                    'drone_model' => $droneParams?->drone_model ?: 'Not specified',
                    'camera_model' => $droneParams?->camera_model ?: 'Not specified',
                    'altitude_m' => (float) ($droneParams?->altitude_m ?? 0),
                    'target_gsd_cm' => (float) ($droneParams?->target_gsd_cm ?? 0),
                    'gsd_cm' => (float) ($droneParams?->gsd_cm ?? 0),
                    'front_overlap' => (float) ($droneParams?->front_overlap_percent ?? 0),
                    'side_overlap' => (float) ($droneParams?->side_overlap_percent ?? 0),
                    'speed_ms' => $speedMs,
                    'course_angle' => (float) ($droneParams?->course_angle_deg ?? 0),
                    'photo_spacing_m' => (float) ($droneParams?->photo_spacing_m ?? 0),
                    'photo_interval_s' => (float) ($droneParams?->photo_interval_s ?? 0),
                    'usable_flight_time_min' => (float) ($droneParams?->usable_flight_time_min ?? 0),
                    'sortie_count' => (int) ($droneParams?->sortie_count ?? 1),
                    'line_count' => $lineCount,
                    'flight_lines' => $location->surveyLines->where('type', 'main')->count(),
                    'total_images' => (int) ($droneParams?->total_images ?? 0),
                    'boundary_count' => $boundaryCount,
                    'boundary_area' => $boundaryArea,
                    'boundary_area_ha' => $boundaryArea / 10000,
                    'screenshot' => $isMapped ? $this->screenshotData($location->id) : null,
                ];
            } else {
                $distance = (float) ($location->sbesParameters?->total_distance_nm ?? 0);
                $speed = (float) ($location->sbesParameters?->survey_speed_knots ?? 0);
                $hoursPerDay = (float) ($location->sbesParameters?->working_hours_per_day ?? 8.0);
                if ($hoursPerDay <= 0) {
                    $hoursPerDay = 8.0;
                }
                $hours = $speed > 0 ? $distance / $speed : 0;
                $executionDays = $hoursPerDay > 0 ? $hours / $hoursPerDay : 0;
                $boundaryArea = (float) $location->boundaries->sum('area');

                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'survey_type' => 'sbes',
                    'is_drone' => false,
                    'is_mapped' => $isMapped,
                    'status' => $location->status ?? ($isMapped ? 'Mapped' : 'Pending'),
                    'distance_nm' => $distance,
                    'survey_speed_knots' => $speed,
                    'working_hours_per_day' => $hoursPerDay,
                    'survey_hours' => $hours,
                    'execution_days' => $executionDays,
                    'execution_days_ceil' => ceil($executionDays),
                    'line_count' => $lineCount,
                    'main_line_count' => $location->surveyLines->where('type', 'main')->count(),
                    'cross_line_count' => $location->surveyLines->where('type', 'cross')->count(),
                    'boundary_count' => $boundaryCount,
                    'boundary_area' => $boundaryArea,
                    'boundary_area_ha' => $boundaryArea / 10000,
                    'screenshot' => $isMapped ? $this->screenshotData($location->id) : null,
                ];
            }
        })->values();

        // Calculate scope-specific summary
        $sbesLocations = $locationsData->where('is_drone', false);
        $droneLocations = $locationsData->where('is_drone', true);

        $weatherDays = (float) ($project->weather_days ?? 0);
        $modDemodDays = (float) ($project->mod_demod_days ?? 0);
        $patchTestDays = (float) ($project->patch_test_days ?? 0);

        // SBES Summary
        $sbesTotalDistanceNm = $sbesLocations->sum('distance_nm');
        $sbesTotalHours = $sbesLocations->sum('survey_hours');
        $sbesExecutionDays = $sbesLocations->sum('execution_days_ceil');
        $sbesTotalDuration = $sbesExecutionDays + $weatherDays + $modDemodDays + $patchTestDays;

        // Drone Summary
        $droneTotalDistanceM = $droneLocations->sum('distance_m');
        $droneTotalImages = $droneLocations->sum('total_images');
        $droneTotalHours = $droneLocations->sum('survey_hours');
        $droneTotalSorties = $droneLocations->sum('sortie_count');
        $droneTotalArea = $droneLocations->sum('boundary_area');

        $summary = [
            'sbes' => [
                'total_distance_nm' => round($sbesTotalDistanceNm, 4),
                'total_hours' => round($sbesTotalHours, 2),
                'execution_days' => round($sbesExecutionDays, 2),
                'weather_days' => round($weatherDays, 2),
                'mod_demod_days' => round($modDemodDays, 2),
                'patch_test_days' => round($patchTestDays, 2),
                'total_days' => round($sbesTotalDuration, 2),
                'count' => $sbesLocations->count(),
            ],
            'drone' => [
                'total_distance_m' => round($droneTotalDistanceM, 1),
                'total_distance_km' => round($droneTotalDistanceM / 1000, 2),
                'total_hours' => round($droneTotalHours, 2),
                'total_images' => $droneTotalImages,
                'total_sorties' => $droneTotalSorties,
                'total_area_m2' => round($droneTotalArea, 2),
                'total_area_ha' => round($droneTotalArea / 10000, 2),
                'count' => $droneLocations->count(),
            ],
        ];

        return [
            'project' => $project,
            'locationId' => $locationId,
            'is_single' => $isSingle,
            'report_type' => $reportType,
            'report_title' => $reportTitle,
            'report_subtitle' => $reportSubtitle,
            'filename' => $filename,
            'locations' => $locationsData,
            'summary' => $summary,
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
