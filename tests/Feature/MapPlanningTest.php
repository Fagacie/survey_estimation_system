<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\SurveyLine;
use App\Models\SurveyLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MapPlanningTest extends TestCase
{
    use RefreshDatabase;

    public function test_map_lines_preserve_geojson_line_type_over_legacy_generated_database_type(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'user_id' => $user->id,
            'project_code' => 'TEST-MAP',
            'name' => 'Map Test',
            'status' => 'draft',
        ]);
        $surveyLocation = SurveyLocation::create([
            'project_id' => $project->id,
            'name' => 'Area A',
        ]);

        SurveyLine::create([
            'project_id' => $project->id,
            'survey_location_id' => $surveyLocation->id,
            'type' => 'generated',
            'line_number' => 1,
            'length_meters' => 1000,
            'geometry' => [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'LineString',
                    'coordinates' => [[101.0, 4.0], [101.1, 4.1]],
                ],
                'properties' => [
                    'line_type' => 'main',
                    'length' => 1000,
                ],
            ],
        ]);

        $response = $this
            ->actingAs($user)
            ->getJson(route('projects.surveys.lines', [$project, $surveyLocation]));

        $response
            ->assertOk()
            ->assertJsonPath('features.0.properties.line_type', 'main');
    }

    public function test_map_save_stores_generated_lines_as_canonical_main_lines(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'user_id' => $user->id,
            'project_code' => 'TEST-SAVE',
            'name' => 'Save Test',
            'status' => 'draft',
        ]);
        $surveyLocation = SurveyLocation::create([
            'project_id' => $project->id,
            'name' => 'Area A',
        ]);

        $payload = [
            'boundaries' => [],
            'lines' => [
                'type' => 'FeatureCollection',
                'features' => [[
                    'type' => 'Feature',
                    'geometry' => [
                        'type' => 'LineString',
                        'coordinates' => [[101.0, 4.0], [101.1, 4.1]],
                    ],
                    'properties' => [
                        'length' => 1000,
                    ],
                ]],
            ],
            'generation_settings' => [
                'line_spacing' => 100,
                'orientation_angle' => 90,
                'cross_spacing' => 500,
            ],
            'is_generated' => true,
            'override_total_distance_meters' => 1000,
        ];

        $response = $this
            ->actingAs($user)
            ->postJson(route('projects.surveys.map.save', [$project, $surveyLocation]), $payload);

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseHas('survey_lines', [
            'project_id' => $project->id,
            'survey_location_id' => $surveyLocation->id,
            'type' => 'main',
        ]);
    }
}
