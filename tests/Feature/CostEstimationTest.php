<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\SbesParameter;
use App\Models\SurveyLocation;
use App\Models\User;
use App\Services\Calculation\ProjectEstimationService;
use App\Services\Calculation\CostEstimationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CostEstimationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sbes_calculation_sums_multiple_survey_locations_and_global_allowances()
    {
        $project = Project::create([
            'number' => 'TEST-SBES',
            'created_by' => User::factory()->create()->id,
            'name' => 'West Johor SBES',
            'status' => 'draft',
            'weather_days' => 1,
            'mod_demod_days' => 2,
            'patch_test_days' => 0.5,
        ]);

        $locationA = SurveyLocation::create(['project_id' => $project->project_Id, 'name' => 'Besut']);
        $locationB = SurveyLocation::create(['project_id' => $project->project_Id, 'name' => 'Kuala Terengganu']);

        SbesParameter::create([
            'project_id' => $project->project_Id,
            'survey_location_id' => $locationA->id,
            'total_distance_nm' => 74,
            'survey_speed_knots' => 5,
            'working_hours_per_day' => 8,
        ]);

        SbesParameter::create([
            'project_id' => $project->project_Id,
            'survey_location_id' => $locationB->id,
            'total_distance_nm' => 26,
            'survey_speed_knots' => 4,
            'working_hours_per_day' => 6,
        ]);

        $service = new ProjectEstimationService();
        $result = $service->calculate($project);

        $this->assertSame(100.0, $result['distance_nm']);
        $this->assertSame(21.3, $result['survey_hours']);
        $this->assertSame(2.93, $result['execution_days']);
        $this->assertSame(6.43, $result['total_days']);
    }

    public function test_sbes_calculation_handles_a_project_without_survey_data()
    {
        $project = Project::create([
            'number' => 'TEST-PERSIST',
            'created_by' => User::factory()->create()->id,
            'name' => 'Persisted SBES',
            'status' => 'draft',
        ]);

        $service = new ProjectEstimationService();
        $result = $service->calculate($project);

        $this->assertSame(0.0, $result['distance_nm']);
        $this->assertSame(0.0, $result['total_days']);
    }
}
