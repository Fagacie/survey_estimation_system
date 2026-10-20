<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RouteSmokesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'inertia.pages.paths' => [
                resource_path('js/ises/pages'),
                resource_path('js/pages'),
            ],
            'inertia.testing.ensure_pages_exist' => false,
        ]);
    }

    public function test_guest_routes_render_or_redirect(): void
    {
        $this->get('/')->assertRedirect(route('signin'));
        $this->get('/signin')->assertOk();
        $this->get('/signup')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->get('/forgot-password')->assertOk();
    }

    public function test_authenticated_primary_react_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard-react')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Dashboard'));

        $this->actingAs($user)->get('/projects-react')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('ProjectIndex'));

        $this->actingAs($user)->get('/projects-react/create')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('CreateProject'));

        $this->actingAs($user)->get('/profile-react')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('ProfileSettings'));

        $this->actingAs($user)->get('/quotations/history-react')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('QuotationHistory'));
    }

    public function test_authenticated_blade_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk();
        $this->actingAs($user)->get('/projects')->assertOk();
        $this->actingAs($user)->get('/projects/create')->assertOk();
        $this->actingAs($user)->get('/profile')->assertOk();
        $this->actingAs($user)->get('/quotations/history')->assertOk();
        $this->actingAs($user)->get('/quotation')->assertOk();
        $this->actingAs($user)->get('/admin')->assertOk();
        $this->actingAs($user)->get('/newItem')->assertOk();
        $this->actingAs($user)->get('/admin/equipment')->assertOk();
        $this->actingAs($user)->get('/projects/modeling/modules')->assertOk();
        $this->actingAs($user)->get('/projects/modeling/items')->assertOk();
        $this->actingAs($user)->get('/projects/modeling/packages')->assertOk();
        $this->actingAs($user)->get('/projects/modeling/builder')->assertOk();

        $project = \App\Models\Project::create([
            'name' => 'Workspace Visual Test',
            'project_type' => 'MBES',
            'location' => 'Terengganu',
            'status' => 'planned',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->get('/projects/' . $project->project_Id)
            ->assertOk()
            ->assertSee('pw-hero-card')
            ->assertSee('Workspace Visual Test');

        $this->actingAs($user)->get('/projects/' . $project->project_Id . '/edit')
            ->assertOk()
            ->assertSee('project-edit-card')
            ->assertSee('Workspace Visual Test');

        $quotation = \App\Models\QtInvoice::create([
            'project_Id' => $project->project_Id,
            'quotation_no' => 'QT-TEST-001',
            'grand_total' => 10000.00,
            'survey_total' => 8000.00,
            'modelling_total' => 2000.00,
            'created_by' => $user->id,
        ]);

        $term = \App\Models\PaymentTerm::create([
            'quotation_Id' => $quotation->quotation_Id,
            'name' => '1st Payment - Mobilization',
            'percentage' => 50,
            'condition' => 'Upon contract signing',
            'amount' => 5000.00,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->get('/quotations/' . $quotation->quotation_Id)
            ->assertOk()
            ->assertSee('QT-TEST-001');

        $this->actingAs($user)->get('/quotations/' . $quotation->quotation_Id . '/invoice')
            ->assertOk()
            ->assertSee('Invoice Milestones')
            ->assertSee('QT-TEST-001');

        $invoice = \App\Models\Invoice::create([
            'invoice_number' => 'INV-TEST-001',
            'quotation_Id' => $quotation->quotation_Id,
            'status' => 'draft',
            'created_by' => $user->id,
        ]);

        \App\Models\PaymentTerm::create([
            'invoice_Id' => $invoice->invoice_Id,
            'source_term_id' => $term->id,
            'name' => $term->name,
            'percentage' => $term->percentage,
            'condition' => $term->condition,
            'amount' => $term->amount,
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->get('/invoices/' . $invoice->invoice_Id)
            ->assertOk()
            ->assertSee('INV-TEST-001');
    }

    public function test_authenticated_legacy_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/home')->assertRedirect(route('dashboard'));
        $this->actingAs($user)->get('/history')->assertRedirect(route('quotations.history'));
        $this->actingAs($user)->get('/dashboard-legacy')->assertOk();
        $this->actingAs($user)->get('/projects-legacy')->assertOk();
        $this->actingAs($user)->get('/profile-legacy')->assertOk();
        $this->actingAs($user)->get('/quotations-history-legacy')->assertOk();
    }

    public function test_unauthenticated_users_are_redirected_to_signin(): void
    {
        $this->get('/dashboard')->assertRedirect(route('signin'));
        $this->get('/projects')->assertRedirect(route('signin'));
        $this->get('/profile')->assertRedirect(route('signin'));
        $this->get('/quotation')->assertRedirect(route('signin'));
        $this->get('/history')->assertRedirect(route('signin'));
        $this->get('/admin')->assertRedirect(route('signin'));
        $this->get('/newItem')->assertRedirect(route('signin'));
        $this->get('/admin/equipment')->assertRedirect(route('signin'));
    }
}
