<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\SurveyLocation;
use Illuminate\Http\Request;
use App\Services\Map\MapService;

class SurveyLocationController extends Controller
{
    protected $mapService;

    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }

    public function store(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $project->surveyLocations()->create([
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Survey area created successfully.');
    }

    public function destroy(Project $project, SurveyLocation $surveyLocation)
    {
        $this->authorizeSurveyLocation($project, $surveyLocation);

        $surveyLocation->delete();
        return redirect()->back()->with('success', 'Survey area deleted successfully.');
    }

    public function map(Project $project, SurveyLocation $surveyLocation)
    {
        $this->authorizeSurveyLocation($project, $surveyLocation);

        // Load relationships needed for the map
        $surveyLocation->load('boundaries', 'sbesParameters');
        return view('projects.map', compact('project', 'surveyLocation'));
    }

    public function saveMap(Request $request, Project $project, SurveyLocation $surveyLocation)
    {
        $this->authorizeSurveyLocation($project, $surveyLocation);

        $request->validate([
            'boundaries' => 'nullable|array',
            'lines' => 'nullable|array',
            'adcp_markers' => 'nullable|array',
            'generation_settings' => 'nullable|array',
            'is_generated' => 'nullable|boolean',
        ]);

        $stats = $this->mapService->processMapData($project, $surveyLocation, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Map data saved successfully.',
            'statistics' => $stats
        ]);
    }

    public function saveParameters(Request $request, Project $project, SurveyLocation $surveyLocation)
    {
        $this->authorizeSurveyLocation($project, $surveyLocation);

        // Simple direct save for sbes parameters linked to this survey location
        \Log::info("saveParameters HIT for location: " . $surveyLocation->id);
        
        $data = $request->validate([
            'sbes' => 'required|array',
            'sbes.survey_speed_knots' => 'nullable|numeric',
            'sbes.working_hours_per_day' => 'nullable|numeric',
            'drone' => 'nullable|array',
            'drone.camera_model' => 'nullable|string',
            'drone.altitude_m' => 'nullable|numeric',
            'drone.speed_ms' => 'nullable|numeric',
            'drone.front_overlap_percent' => 'nullable|numeric',
            'drone.side_overlap_percent' => 'nullable|numeric',
            'drone.course_angle_deg' => 'nullable|numeric',
            'drone.total_flight_distance_m' => 'nullable|numeric',
            'drone.total_images' => 'nullable|numeric',
            'drone.estimated_duration_hours' => 'nullable|numeric',
            'allowances' => 'nullable|array',
            'allowances.weather_days' => 'nullable|numeric|min:0',
            'allowances.mod_demod_days' => 'nullable|numeric|min:0',
            'allowances.patch_test_days' => 'nullable|numeric|min:0',
        ]);

        \Log::info("saveParameters validation passed: " . json_encode($data));

        $surveyLocation->sbesParameters()->updateOrCreate(
            ['survey_location_id' => $surveyLocation->id],
            [
                'project_id' => $project->project_Id, // Support both just in case
                'survey_speed_knots' => $data['sbes']['survey_speed_knots'] ?? null,
                'working_hours_per_day' => $data['sbes']['working_hours_per_day'] ?? null,
            ]
        );

        if (!empty($data['drone']['camera_model'])) {
            $surveyLocation->droneMappingParameters()->updateOrCreate(
                ['survey_location_id' => $surveyLocation->id],
                [
                    'camera_model' => $data['drone']['camera_model'],
                    'altitude_m' => $data['drone']['altitude_m'] ?? 100,
                    'speed_ms' => $data['drone']['speed_ms'] ?? 15,
                    'front_overlap_percent' => $data['drone']['front_overlap_percent'] ?? 80,
                    'side_overlap_percent' => $data['drone']['side_overlap_percent'] ?? 70,
                    'course_angle_deg' => $data['drone']['course_angle_deg'] ?? 0,
                    'total_flight_distance_m' => $data['drone']['total_flight_distance_m'] ?? 0,
                    'total_images' => $data['drone']['total_images'] ?? 0,
                    'estimated_duration_hours' => $data['drone']['estimated_duration_hours'] ?? 0,
                ]
            );
        }

        $project->update([
            'weather_days' => $data['allowances']['weather_days'] ?? $project->weather_days ?? 0,
            'mod_demod_days' => $data['allowances']['mod_demod_days'] ?? $project->mod_demod_days ?? 0,
            'patch_test_days' => $data['allowances']['patch_test_days'] ?? $project->patch_test_days ?? 0,
        ]);

        \Log::info("sbesParameters updated, current fillable: " . json_encode($surveyLocation->getFillable()));
        \Log::info("Current status before update: " . $surveyLocation->status);

        // Update SurveyLocation status
        $updated = $surveyLocation->update(['status' => 'Mapped']);
        
        \Log::info("SurveyLocation update returned: " . ($updated ? 'true' : 'false'));
        \Log::info("New status after update: " . $surveyLocation->fresh()->status);

        return response()->json([
            'success' => true,
            'message' => 'Survey parameters saved successfully.'
        ]);
    }

    public function saveScreenshot(Request $request, Project $project, SurveyLocation $surveyLocation)
    {
        $this->authorizeSurveyLocation($project, $surveyLocation);

        $request->validate([
            'image' => 'required|string',
        ]);

        $imageData = $request->input('image');
        
        // Strip the data URI prefix if present
        if (str_contains($imageData, ',')) {
            $imageData = explode(',', $imageData, 2)[1];
        }

        $decoded = base64_decode($imageData);
        if ($decoded === false) {
            return response()->json(['success' => false, 'message' => 'Invalid image data.'], 422);
        }

        $dir = storage_path('app/public/maps');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = $dir . '/' . $surveyLocation->id . '.png';
        file_put_contents($path, $decoded);

        return response()->json([
            'success' => true,
            'message' => 'Map screenshot saved.',
        ]);
    }

    public function mapLines(Project $project, SurveyLocation $surveyLocation)
    {
        $this->authorizeSurveyLocation($project, $surveyLocation);

        // Avoid eloquent hydration for performance when returning potentially thousands of lines
        $lines = \Illuminate\Support\Facades\DB::table('survey_lines')
            ->where('survey_location_id', $surveyLocation->id)
            ->select('geometry', 'type')
            ->get()
            ->map(function ($line) {
                $geom = is_string($line->geometry) ? json_decode($line->geometry, true) : $line->geometry;
                $lineType = $line->type ?? 'main';

                if (is_array($geom) && isset($geom['properties']['line_type'])) {
                    $lineType = $geom['properties']['line_type'];
                }
                
                // If the geometry field incorrectly contains a full GeoJSON Feature, extract the actual geometry
                // Do this recursively in case it was double or triple nested by old bugs
                while (is_array($geom) && isset($geom['type']) && $geom['type'] === 'Feature' && isset($geom['geometry'])) {
                    $geom = $geom['geometry'];
                }

                if (!is_array($geom)) {
                    return null;
                }

                return [
                    'type' => 'Feature',
                    'geometry' => $geom,
                    'properties' => [
                        'line_type' => $lineType
                    ]
                ];
            })
            ->filter(); // remove any nulls if decoding failed

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $lines->values()->all()
        ]);
    }

    private function authorizeProject(Project $project): void
    {
        abort_unless((int) $project->created_by === (int) auth()->id(), 404);
    }

    private function authorizeSurveyLocation(Project $project, SurveyLocation $surveyLocation): void
    {
        $this->authorizeProject($project);

        abort_unless((int) $surveyLocation->project_id === (int) $project->project_Id, 404);
    }

    private function canonicalLineType(?string $type): ?string
    {
        return in_array($type, ['main', 'cross', 'reference', 'adcp_marker'], true) ? $type : null;
    }
}

