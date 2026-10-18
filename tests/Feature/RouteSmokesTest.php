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

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Dashboard'));

        $this->actingAs($user)->get('/projects')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('ProjectIndex'));

        $this->actingAs($user)->get('/projects/create')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('CreateProject'));

        $this->actingAs($user)->get('/profile')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('ProfileSettings'));

        $this->actingAs($user)->get('/quotations/history')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('QuotationHistory'));
    }

    public function test_authenticated_blade_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/quotation')->assertOk();
        $this->actingAs($user)->get('/admin')->assertOk();
        $this->actingAs($user)->get('/newItem')->assertOk();
        $this->actingAs($user)->get('/admin/equipment')->assertOk();
        $this->actingAs($user)->get('/projects/modeling/modules')->assertOk();
        $this->actingAs($user)->get('/projects/modeling/items')->assertOk();
        $this->actingAs($user)->get('/projects/modeling/packages')->assertOk();
        $this->actingAs($user)->get('/projects/modeling/builder')->assertOk();
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
