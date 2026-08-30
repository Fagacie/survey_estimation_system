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
        $data = $request->validate([
            'sbes' => 'required|array',
            'sbes.survey_speed_knots' => 'nullable|numeric',
            'sbes.working_hours_per_day' => 'nullable|numeric',
        ]);

        $surveyLocation->sbesParameters()->updateOrCreate(
            ['survey_location_id' => $surveyLocation->id],
            [
                'project_id' => $project->id, // For legacy fallback if needed
                'survey_speed_knots' => $data['sbes']['survey_speed_knots'] ?? null,
                'working_hours_per_day' => $data['sbes']['working_hours_per_day'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Survey parameters saved successfully.'
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
                        'line_type' => $line->type ?? 'main'
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
        abort_unless((int) $project->user_id === (int) auth()->id(), 404);
    }

    private function authorizeSurveyLocation(Project $project, SurveyLocation $surveyLocation): void
    {
        $this->authorizeProject($project);

        abort_unless((int) $surveyLocation->project_id === (int) $project->id, 404);
    }

    private function canonicalLineType(?string $type): ?string
    {
        return in_array($type, ['main', 'cross', 'reference', 'adcp_marker'], true) ? $type : null;
    }
}
