<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\Module;
use App\Models\Project;
use App\Models\SbesParameter;
use App\Models\Service;
use App\Models\SurveyLocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationEstimationTest extends TestCase
{
    use RefreshDatabase;

    public function test_quotation_stores_the_current_survey_estimation_snapshot(): void
    {
        $user = User::factory()->create();
        $project = Project::create([
            'created_by' => $user->id,
            'number' => 'TEST-QUOTATION',
            'name' => 'Quotation Integration Test',
            'status' => 'draft',
        ]);

        $location = SurveyLocation::create([
            'project_id' => $project->project_Id,
            'name' => 'Area A',
        ]);

        SbesParameter::create([
            'project_id' => $project->project_Id,
            'survey_location_id' => $location->id,
            'total_distance_nm' => 74,
            'survey_speed_knots' => 5,
            'working_hours_per_day' => 8,
        ]);

        $module = Module::create(['module_name' => 'Survey']);
        $category = Category::create([
            'module_id' => $module->module_id,
            'category_name' => 'Equipment',
        ]);
        $service = Service::create([
            'category_id' => $category->category_id,
            'service_name' => 'Vessel',
        ]);
        $item = Item::create([
            'module_id' => $module->module_id,
            'category_id' => $category->category_id,
            'service_id' => $service->service_id,
            'item_name' => 'Survey vessel',
            'internal_rate' => 100,
        ]);

        $response = $this->actingAs($user)->postJson(route('quotation.store'), [
            'project_id' => $project->project_Id,
            'items' => [[
                'module_id' => $module->module_id,
                'item_id' => $item->item_id,
                'unit_qty' => 1,
                'mark_up' => 0,
            ]],
            'payment_terms' => [],
        ]);

        $response->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas('qt_invoice', [
            'project_Id' => $project->project_Id,
            'survey_distance_nm' => 74,
            'survey_hours' => 14.8,
            'survey_duration_days' => 1.85,
        ]);
        $this->assertDatabaseHas('qt_invoice_items', [
            'catalog_item_id' => $item->item_id,
            'days' => 2,
            'daily_rate' => 100,
            'line_total' => 200,
        ]);
    }
}
