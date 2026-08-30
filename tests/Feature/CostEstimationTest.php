<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\CostRate;
use App\Models\CostEstimation;
use App\Models\SbesParameter;
use App\Models\SurveyLocation;
use App\Services\Calculation\CostEstimationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CostEstimationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sbes_calculation_sums_multiple_survey_locations_and_global_allowances()
    {
        $project = Project::create([
            'project_code' => 'TEST-SBES',
            'name' => 'West Johor SBES',
            'status' => 'draft',
            'weather_days' => 1,
            'mod_demod_days' => 2,
            'patch_test_days' => 0.5,
        ]);

        $locationA = SurveyLocation::create(['project_id' => $project->id, 'name' => 'Besut']);
        $locationB = SurveyLocation::create(['project_id' => $project->id, 'name' => 'Kuala Terengganu']);

        SbesParameter::create([
            'project_id' => $project->id,
            'survey_location_id' => $locationA->id,
            'total_distance_nm' => 74,
            'survey_speed_knots' => 5,
            'working_hours_per_day' => 8,
        ]);

        SbesParameter::create([
            'project_id' => $project->id,
            'survey_location_id' => $locationB->id,
            'total_distance_nm' => 26,
            'survey_speed_knots' => 4,
            'working_hours_per_day' => 6,
        ]);

        CostRate::create([
            'category' => 'Equipment',
            'name' => 'Single Beam Rental Packages',
            'unit_type' => 'Per Day',
            'base_multiplier' => 'Execution Days',
            'default_rate' => 100,
        ]);

        CostRate::create([
            'category' => 'Logistics',
            'name' => 'Weather Standby',
            'unit_type' => 'Per Day',
            'base_multiplier' => 'Weather Days',
            'default_rate' => 200,
        ]);

        CostRate::create([
            'category' => 'Logistics',
            'name' => 'Mod and Demod',
            'unit_type' => 'Per Day',
            'base_multiplier' => 'MOB/DEMOB Days',
            'default_rate' => 300,
        ]);

        CostRate::create([
            'category' => 'Equipment',
            'name' => 'Boat Rental',
            'unit_type' => 'Per Day',
            'base_multiplier' => 'Total Duration',
            'default_rate' => 50,
        ]);

        CostRate::create([
            'category' => 'Analysis',
            'name' => 'Report',
            'unit_type' => 'Lump Sum',
            'base_multiplier' => 'Total Duration',
            'default_rate' => 1000,
        ]);

        $service = new CostEstimationService();
        $result = $service->calculate($project);

        $this->assertSame(100.0, $result['duration']['distance_nm']);
        $this->assertSame(21.3, $result['duration']['survey_hours']);
        $this->assertSame(2.93, $result['duration']['execution_days']);
        $this->assertSame(6.43, $result['duration']['total_days']);
        $this->assertEquals(2450, $result['total_cost']);
        $this->assertCount(5, $result['cost_items']);
    }

    public function test_sbes_calculation_can_be_persisted()
    {
        $project = Project::create([
            'project_code' => 'TEST-PERSIST',
            'name' => 'Persisted SBES',
            'status' => 'draft',
        ]);

        CostRate::create([
            'category' => 'Analysis',
            'name' => 'Report',
            'unit_type' => 'Lump Sum',
            'base_multiplier' => 'Total Duration',
            'default_rate' => 500,
        ]);

        $service = new CostEstimationService();
        $result = $service->calculate($project);
        $estimation = $service->persist(
            $project,
            $result['cost_items'],
            $result['total_cost'],
            'Calculated',
            $result['duration']
        );

        $this->assertInstanceOf(CostEstimation::class, $estimation);
        $this->assertEquals(500, $estimation->total_cost);
        $this->assertCount(1, $estimation->items);
    }
}
