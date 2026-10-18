<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\QtInvoice;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    /**
     * Display the operational dashboard.
     */
    public function index(): InertiaResponse
    {
        $data = $this->dashboardData();
        $data['recentProjects'] = $data['recentProjects']->map(fn ($project) => [
            'id' => $project->project_Id,
            'number' => $project->number,
            'name' => $project->name,
            'client' => $project->client?->company_name,
            'type' => Project::TYPES[$project->project_type] ?? ($project->project_type ?: 'Not set'),
            'status' => Str::headline($project->status ?? 'draft'),
            'updatedAt' => $project->updated_at?->toISOString(),
        ])->values();

        return Inertia::render('Dashboard', $data);
    }

    /** Keep the existing Blade dashboard available during the migration. */
    public function legacy(): View
    {
        return view('dashboard.index', $this->dashboardData());
    }

    private function dashboardData(): array
    {
        $metrics = [
            'total' => Project::count(),
            'planned' => Project::where('status', 'planned')->count(),
            'completed' => Project::where('status', 'completed')->count(),
            'mapped' => Project::whereHas('surveyLocations', fn ($query) => $query->where('status', 'Mapped'))->count(),
            'quotations' => Project::has('lineItems')->count(),
        ];

        $statusBreakdown = Project::query()
            ->selectRaw("COALESCE(NULLIF(status, ''), 'draft') as label, status as status_key, COUNT(*) as total")
            ->groupBy('status')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'label' => Str::headline($row->label),
                'key' => $row->status_key ?: 'draft',
                'total' => (int) $row->total,
            ]);

        $projectTypeBreakdown = Project::query()
            ->selectRaw('project_type as label, COUNT(*) as total')
            ->groupBy('project_type')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row) => [
                'label' => Project::TYPES[$row->label] ?? ($row->label ?: 'Not set'),
                'key' => $row->label,
                'total' => (int) $row->total,
            ]);

        $quotationStats = [
            'count' => QtInvoice::count(),
            'total_value' => (float) QtInvoice::sum('grand_total'),
            'average_value' => (float) (QtInvoice::count() ? QtInvoice::avg('grand_total') : 0),
            'this_month_count' => QtInvoice::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'this_month_value' => (float) QtInvoice::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('grand_total'),
        ];

        $quotationMonthly = collect(range(5, 0))->map(function ($monthsAgo) {
            $month = now()->startOfMonth()->subMonths($monthsAgo);
            $query = QtInvoice::whereBetween('created_at', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()]);

            return [
                'label' => $month->format('M Y'),
                'count' => (int) $query->count(),
                'value' => (float) $query->sum('grand_total'),
            ];
        });

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

        return compact('metrics', 'recentProjects', 'attentionItems', 'statusBreakdown', 'projectTypeBreakdown', 'quotationStats', 'quotationMonthly') + [
            'today' => now()->format('d M Y'),
        ];
    }
}
