<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_creation_persists_client_address(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('projects.store'), [
            'name' => 'Nigerian Boy',
            'number' => null,
            'client_name' => 'Fagaci',
            'client_address' => 'No 179 Jaoji Qrts, Court Road, Tarauni LGA Kano',
            'period' => '10',
            'pic_name' => 'Abubakar Kabir',
            'pic_no' => '1303',
            'status' => 'draft',
        ]);

        $project = Project::where('name', 'Nigerian Boy')->first();

        $response->assertRedirect(route('projects.show', $project->project_Id));
        $this->assertNotNull($project);
        $this->assertSame('Fagaci', $project->client->company_name);
        $this->assertSame('No 179 Jaoji Qrts, Court Road, Tarauni LGA Kano', $project->client->client_address);
        $this->assertDatabaseHas('clients', [
            'company_name' => 'Fagaci',
            'client_address' => 'No 179 Jaoji Qrts, Court Road, Tarauni LGA Kano',
        ]);
    }
}
