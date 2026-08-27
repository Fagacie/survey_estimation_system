<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Project;
use App\Models\CostEstimation;
use App\Services\Calculation\CostEstimationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CostEstimationTest extends TestCase
{
    use RefreshDatabase;

    public function test_mbes_company_cost_template_matches_source_document()
    {
        $project = Project::create([
            'project_code' => 'TEST-MBES',
            'name' => 'West Johor MBES',
            'status' => 'draft',
            'survey_type' => 'MBES',
        ]);

        $service = new CostEstimationService();
        $estimation = $service->generateEstimation($project, 52, 64, 0, [
            'patch_test_days' => 6,
        ]);

        $this->assertInstanceOf(CostEstimation::class, $estimation);
        $this->assertEquals(491000, $estimation->total_cost);
        $this->assertCount(12, $estimation->items);
    }

    public function test_sbes_company_cost_template_matches_source_document()
    {
        $project = Project::create([
            'project_code' => 'TEST-SBES',
            'name' => 'West Johor SBES Six Rivers',
            'status' => 'draft',
            'survey_type' => 'SBES',
        ]);

        $service = new CostEstimationService();
        $estimation = $service->generateEstimation($project, 6, 11);

        $this->assertEquals(103600, $estimation->total_cost);
        $this->assertCount(10, $estimation->items);
    }

    public function test_adcp_company_cost_template_matches_source_document()
    {
        $project = Project::create([
            'project_code' => 'TEST-ADCP',
            'name' => 'West Johor ADCP',
            'status' => 'draft',
            'survey_type' => 'ADCP',
        ]);

        $service = new CostEstimationService();
        $estimation = $service->generateEstimation($project, 15, 15, 3, [
            'boat_days' => 12,
        ]);

        $this->assertEquals(139000, $estimation->total_cost);
        $this->assertCount(9, $estimation->items);
    }
}
