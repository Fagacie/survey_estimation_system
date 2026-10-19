<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_the_react_inertia_page_with_live_metrics(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard.react'));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('metrics')
            ->where('metrics.total', 0)
            ->has('statusBreakdown')
            ->has('projectTypeBreakdown')
            ->has('quotationStats')
            ->has('quotationMonthly')
            ->has('recentProjects', 0)
            ->has('attentionItems', 0)
            ->where('auth.user.name', $user->name)
        );

        $bladeResponse = $this->actingAs($user)->get(route('dashboard'));
        $bladeResponse->assertOk();
    }
}
