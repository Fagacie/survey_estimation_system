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

        $response = $this->actingAs($user)->get(route('projects.report.preview', $project->project_Id));

        $response->assertOk()
            ->assertSee('HYDROGRAPHIC SURVEY REPORT')
            ->assertSee('Total Duration')
            ->assertSee('2.00')
            ->assertDontSee('Itemized Cost Quotation');
    }
}
