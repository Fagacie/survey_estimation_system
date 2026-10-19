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
            'created_by' => $user->id,
            'number' => 'TEST-MAP',
            'name' => 'Map Test',
            'status' => 'draft',
        ]);
        $surveyLocation = SurveyLocation::create([
            'project_id' => $project->project_Id,
            'name' => 'Area A',
        ]);

        SurveyLine::create([
            'project_id' => $project->project_Id,
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
            'created_by' => $user->id,
            'number' => 'TEST-SAVE',
            'name' => 'Save Test',
            'status' => 'draft',
        ]);
        $surveyLocation = SurveyLocation::create([
            'project_id' => $project->project_Id,
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
            'project_id' => $project->project_Id,
            'survey_location_id' => $surveyLocation->id,
            'type' => 'main',
        ]);
    }
    public function test_map_planning_page_renders_the_new_workspace_shell_and_boot_config(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'created_by' => $user->id,
            'number' => 'TEST-MAP-UI',
            'name' => 'Map UI Test',
            'status' => 'draft',
        ]);
        $surveyLocation = SurveyLocation::create([
            'project_id' => $project->project_Id,
            'name' => 'Planning Area',
            'survey_type' => 'sbes',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('projects.surveys.map', [$project, $surveyLocation]));

        $response
            ->assertOk()
            ->assertSee('map-workspace.css')
            ->assertSee('js/ises/map-workspace.js')
            ->assertSee('planning-sidebar')
            ->assertSee('btn-toggle-planning-panel')
            ->assertSee('MAP_WORKSPACE_CONFIG')
            ->assertSee('surveyType: "sbes"', false);
    }

    public function test_coordinate_input_modal_rendered_in_workspace(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'created_by' => $user->id,
            'number' => 'TEST-COORD-UI',
            'name' => 'Coord UI Test',
            'status' => 'draft',
        ]);
        $surveyLocation = SurveyLocation::create([
            'project_id' => $project->project_Id,
            'name' => 'Planning Area',
            'survey_type' => 'sbes',
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('projects.surveys.map', [$project, $surveyLocation]));

        $response
            ->assertOk()
            ->assertSee('id="coordInputModal"', false)
            ->assertSee('id="btn-open-coord-modal"', false)
            ->assertSee('id="coordOrderLatLng"', false)
            ->assertSee('id="coordOrderLngLat"', false)
            ->assertSee('id="coord-bulk-input"', false)
            ->assertSee('id="coord-table-body"', false)
            ->assertSee('id="btn-coord-preview"', false)
            ->assertSee('id="btn-coord-apply"', false);
    }

    public function test_coordinate_defined_boundary_can_be_saved_and_reloaded(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'created_by' => $user->id,
            'number' => 'TEST-COORD-SAVE',
            'name' => 'Coord Save Test',
            'status' => 'draft',
        ]);
        $surveyLocation = SurveyLocation::create([
            'project_id' => $project->project_Id,
            'name' => 'Area Coordinate Test',
            'survey_type' => 'sbes',
        ]);

        // Boundary defined by decimal degree coordinates
        // Points: [Lng, Lat] format in GeoJSON
        $coords = [
            [101.9758, 4.2105],
            [102.0500, 4.2105],
            [102.0500, 4.1500],
            [101.9758, 4.1500],
            [101.9758, 4.2105],
        ];

        $boundaryGeoJson = [
            'type' => 'Feature',
            'geometry' => [
                'type' => 'Polygon',
                'coordinates' => [$coords],
            ],
            'properties' => [],
        ];

        $payload = [
            'boundaries' => [
                [
                    'geometry' => $boundaryGeoJson,
                    'area' => 54321.5,
                    'perimeter' => 2400.0,
                    'vertex_count' => 4,
                    'centroid' => [
                        'type' => 'Point',
                        'coordinates' => [102.0129, 4.18025],
                    ],
                ],
            ],
            'lines' => [
                'type' => 'FeatureCollection',
                'features' => [],
            ],
            'generation_settings' => [],
            'is_generated' => true,
        ];

        $saveResponse = $this
            ->actingAs($user)
            ->postJson(route('projects.surveys.map.save', [$project, $surveyLocation]), $payload);

        $saveResponse->assertOk()->assertJsonPath('success', true);

        // Verify database persistence
        $this->assertDatabaseHas('project_boundaries', [
            'project_id' => $project->project_Id,
            'survey_location_id' => $surveyLocation->id,
            'vertex_count' => 4,
        ]);

        // Reload the map workspace and assert boundary coordinates are rendered in MAP_WORKSPACE_CONFIG
        $reloadResponse = $this
            ->actingAs($user)
            ->get(route('projects.surveys.map', [$project, $surveyLocation]));

        $reloadResponse
            ->assertOk()
            ->assertSee('101.9758')
            ->assertSee('4.2105');
    }

    public function test_coordinate_defined_boundary_with_sbes_survey_lines_can_be_saved_and_reloaded(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'created_by' => $user->id,
            'number' => 'TEST-SBES-COORD',
            'name' => 'SBES Coord Test',
            'status' => 'draft',
        ]);
        $surveyLocation = SurveyLocation::create([
            'project_id' => $project->project_Id,
            'name' => 'Marine Hydro Area',
            'survey_type' => 'sbes',
        ]);

        $coords = [
            [101.5000, 3.0000],
            [101.5500, 3.0000],
            [101.5500, 2.9500],
            [101.5000, 2.9500],
            [101.5000, 3.0000],
        ];

        $payload = [
            'boundaries' => [
                [
                    'geometry' => [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'Polygon',
                            'coordinates' => [$coords],
                        ],
                        'properties' => [],
                    ],
                    'area' => 30000000,
                    'perimeter' => 22000,
                    'vertex_count' => 4,
                ],
            ],
            'lines' => [
                'type' => 'FeatureCollection',
                'features' => [
                    [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'LineString',
                            'coordinates' => [[101.5000, 2.9800], [101.5500, 2.9800]],
                        ],
                        'properties' => [
                            'line_type' => 'main',
                            'length' => 5500,
                        ],
                    ],
                    [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'LineString',
                            'coordinates' => [[101.5250, 2.9500], [101.5250, 3.0000]],
                        ],
                        'properties' => [
                            'line_type' => 'cross',
                            'length' => 5500,
                        ],
                    ],
                ],
            ],
            'generation_settings' => [
                'line_spacing' => 100,
                'orientation_angle' => 90,
                'cross_spacing' => 500,
            ],
            'is_generated' => true,
            'override_total_distance_meters' => 11000,
        ];

        $saveResponse = $this
            ->actingAs($user)
            ->postJson(route('projects.surveys.map.save', [$project, $surveyLocation]), $payload);

        $saveResponse->assertOk()->assertJsonPath('success', true);

        // Verify lines retrieval
        $linesResponse = $this
            ->actingAs($user)
            ->getJson(route('projects.surveys.lines', [$project, $surveyLocation]));

        $linesResponse
            ->assertOk()
            ->assertJsonPath('features.0.properties.line_type', 'main')
            ->assertJsonPath('features.1.properties.line_type', 'cross');
    }

    public function test_coordinate_defined_boundary_with_drone_mapping_preserves_drone_workspace(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'created_by' => $user->id,
            'number' => 'TEST-DRONE-COORD',
            'name' => 'Drone Coord Test',
            'status' => 'draft',
        ]);
        $surveyLocation = SurveyLocation::create([
            'project_id' => $project->project_Id,
            'name' => 'Coastal Drone Mapping Area',
            'survey_type' => 'drone',
        ]);

        $coords = [
            [101.2155, 4.1355],
            [101.2588, 4.1355],
            [101.2588, 4.0822],
            [101.2155, 4.0822],
            [101.2155, 4.1355],
        ];

        $payload = [
            'boundaries' => [
                [
                    'geometry' => [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'Polygon',
                            'coordinates' => [$coords],
                        ],
                        'properties' => [],
                    ],
                    'area' => 4500000,
                    'perimeter' => 8800,
                    'vertex_count' => 4,
                ],
            ],
            'lines' => [
                'type' => 'FeatureCollection',
                'features' => [
                    [
                        'type' => 'Feature',
                        'geometry' => [
                            'type' => 'LineString',
                            'coordinates' => [[101.2200, 4.0900], [101.2500, 4.0900]],
                        ],
                        'properties' => [
                            'line_type' => 'main',
                            'length' => 1700,
                        ],
                    ],
                ],
            ],
            'generation_settings' => [
                'line_spacing' => 50,
                'orientation_angle' => 0,
            ],
            'is_generated' => true,
            'override_total_distance_meters' => 1700,
        ];

        $saveResponse = $this
            ->actingAs($user)
            ->postJson(route('projects.surveys.map.save', [$project, $surveyLocation]), $payload);

        $saveResponse->assertOk()->assertJsonPath('success', true);

        // Reload Drone workspace and verify surveyType and boundary JSON
        $reloadResponse = $this
            ->actingAs($user)
            ->get(route('projects.surveys.map', [$project, $surveyLocation]));

        $reloadResponse
            ->assertOk()
            ->assertSee('surveyType: "drone"', false)
            ->assertSee('101.2155')
            ->assertSee('4.1355');
    }
}

