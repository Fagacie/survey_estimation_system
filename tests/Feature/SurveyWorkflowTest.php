<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\SbesParameter;
use App\Models\SurveyLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SurveyWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_survey_parameters_save_project_allowances_used_by_estimation(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'created_by' => $user->id,
            'number' => 'WORKFLOW-1',
            'name' => 'Workflow Project',
            'status' => 'draft',
        ]);
        $location = SurveyLocation::create([
            'project_id' => $project->project_Id,
            'name' => 'Area A',
        ]);
        SbesParameter::create([
            'project_id' => $project->project_Id,
            'survey_location_id' => $location->id,
            'total_distance_nm' => 40,
        ]);

        $response = $this->actingAs($user)->postJson(
            route('projects.surveys.parameters.store', [$project->project_Id, $location->id]),
            [
                'sbes' => ['survey_speed_knots' => 5, 'working_hours_per_day' => 8],
                'allowances' => ['weather_days' => 1.5, 'mod_demod_days' => 2, 'patch_test_days' => 0.5],
            ]
        );

        $response->assertOk()->assertJsonPath('success', true);
        $this->assertSame(1.5, (float) $project->fresh()->weather_days);
        $this->assertSame(2.0, (float) $project->fresh()->mod_demod_days);
        $this->assertSame(0.5, (float) $project->fresh()->patch_test_days);
    }

    public function test_survey_report_contains_duration_and_not_costing(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'created_by' => $user->id,
            'number' => 'REPORT-1',
            'name' => 'Report Project',
            'status' => 'draft',
            'weather_days' => 1,
        ]);
        $location = SurveyLocation::create(['project_id' => $project->project_Id, 'name' => 'Area A']);
        SbesParameter::create([
            'project_id' => $project->project_Id,
            'survey_location_id' => $location->id,
            'total_distance_nm' => 40,
            'survey_speed_knots' => 5,
            'working_hours_per_day' => 8,
        ]);

        $previewResponse = $this->actingAs($user)->get(route('projects.report.preview', $project->project_Id));
        $previewResponse->assertOk()->assertSee('HYDROGRAPHIC SURVEY REPORT');

        $response = $this->actingAs($user)->get(route('projects.report.preview', [
            'project' => $project->project_Id,
            'raw' => 1,
        ]));

        $response->assertOk()
            ->assertSee('HYDROGRAPHIC SURVEY REPORT')
            ->assertSee('Total Duration')
            ->assertSee('2.00')
            ->assertDontSee('Itemized Cost Quotation');
    }

    public function test_drone_mapping_report_isolation_and_distinct_metrics(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'created_by' => $user->id,
            'number' => 'REPORT-DRONE',
            'name' => 'Mixed Survey Project',
            'status' => 'draft',
        ]);
        $sbesLoc = SurveyLocation::create([
            'project_id' => $project->project_Id,
            'name' => 'Pantai UMT',
            'survey_type' => 'sbes',
            'status' => 'Mapped',
        ]);
        SbesParameter::create([
            'project_id' => $project->project_Id,
            'survey_location_id' => $sbesLoc->id,
            'total_distance_nm' => 100,
            'survey_speed_knots' => 5,
            'working_hours_per_day' => 8,
        ]);

        $droneLoc = SurveyLocation::create([
            'project_id' => $project->project_Id,
            'name' => 'Near UMT',
            'survey_type' => 'drone',
            'status' => 'Mapped',
        ]);
        \App\Models\DroneMappingParameter::create([
            'survey_location_id' => $droneLoc->id,
            'drone_model' => 'DJI Matrice 350 RTK',
            'camera_model' => 'DJI Zenmuse P1',
            'altitude_m' => 100,
            'gsd_cm' => 1.83,
            'speed_ms' => 10,
            'total_flight_distance_m' => 12397,
            'total_images' => 618,
            'sortie_count' => 1,
            'estimated_duration_hours' => 0.34,
        ]);

        // Request specifically the drone report
        $response = $this->actingAs($user)->get(route('projects.report.preview', [
            'project' => $project->project_Id,
            'location_id' => $droneLoc->id,
            'raw' => 1,
        ]));

        $response->assertOk()
            ->assertSee('DRONE MAPPING SURVEY REPORT')
            ->assertSee('DJI Matrice 350 RTK')
            ->assertSee('Near UMT')
            ->assertDontSee('Pantai UMT')
            ->assertDontSee('Sounding Distance');
    }
}
