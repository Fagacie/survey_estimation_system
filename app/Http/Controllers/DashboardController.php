<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the operational dashboard.
     */
    public function index(): View
    {
        $metrics = [
            'total' => Project::count(),
            'planned' => Project::where('status', 'planned')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'mapped' => Project::whereHas('surveyLocations', fn ($query) => $query->where('status', 'Mapped'))->count(),
            'quotations' => Project::has('lineItems')->count(),
        ];

        $recentProjects = Project::with('client')->latest('updated_at')->limit(5)->get();

        $attentionBase = fn () => Project::with('client')->where('status', '!=', 'completed');
        $attentionItems = collect([
            [
                'key' => 'boundaries',
                'label' => 'Missing boundaries',
                'description' => 'Define the survey area before planning lines.',
                'icon' => 'fa-solid fa-draw-polygon',
                'count' => (clone $attentionBase())->doesntHave('boundaries')->count(),
            ],
            [
                'key' => 'lines',
                'label' => 'Pending survey lines',
                'description' => 'Survey areas that still need line planning.',
                'icon' => 'fa-solid fa-route',
                'count' => (clone $attentionBase())->has('boundaries')->doesntHave('surveyLines')->count(),
            ],
            [
                'key' => 'cost',
                'label' => 'Awaiting cost estimation',
                'description' => 'Projects without saved quotation line items.',
                'icon' => 'fa-solid fa-calculator',
                'count' => (clone $attentionBase())->doesntHave('lineItems')->count(),
            ],
        ])->filter(fn ($item) => $item['count'] > 0)->values();

        return view('dashboard.index', compact('metrics', 'recentProjects', 'attentionItems'));
    }
}
