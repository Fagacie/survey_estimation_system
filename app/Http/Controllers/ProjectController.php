<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Base Query for Projects with Filtering
        $query = auth()->user()->projects()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('number', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('company_name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->paginate(10);

        // 2. Core KPIs
        $totalSbes = auth()->user()->projects()->count();
        $metrics = [
            'total' => $totalSbes,
            'draft' => auth()->user()->projects()->where('status', 'draft')->count(),
            'planned' => auth()->user()->projects()->where('status', 'planned')->count(),
            'completed' => auth()->user()->projects()->where('status', 'completed')->count(),
            'mapped' => auth()->user()->projects()->whereHas('surveyLocations', fn ($q) => $q->where('status', 'Mapped'))->count(),
            'quotations' => auth()->user()->projects()->has('lineItems')->count(),
        ];

        // (SurveyStatistic table was removed, distance is calculated live in the map UI)
        $totalDistance = 0;

        $projectsWithLines = auth()->user()->projects()->has('surveyLines')->count();
        $projectsAwaitingPlanning = auth()->user()->projects()->doesntHave('boundaries')->count();
        $projectsWithCost = auth()->user()->projects()->has('lineItems')->count();

        $overview = [
            'total_distance' => round($totalDistance, 2),
            'with_lines' => $projectsWithLines,
            'awaiting_planning' => $projectsAwaitingPlanning,
            'completed_estimation' => $projectsWithCost,
        ];

        // 4. Requires Attention Metrics
        // Projects that are active/draft but missing key steps
        $missingBoundariesCount = auth()->user()->projects()->where('status', '!=', 'completed')->doesntHave('boundaries')->count();
        $missingLinesCount = auth()->user()->projects()->where('status', '!=', 'completed')->has('boundaries')->doesntHave('surveyLines')->count();
        $missingParamsCount = 0; // Removed this metric for now
        $missingCostCount = auth()->user()->projects()->where('status', '!=', 'completed')->doesntHave('lineItems')->count();

        $attention = [
            'missing_boundaries' => $missingBoundariesCount,
            'missing_lines' => $missingLinesCount,
            'missing_parameters' => $missingParamsCount,
            'missing_cost' => $missingCostCount,
        ];

        return view('projects.index', compact('projects', 'metrics', 'overview', 'attention'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreProjectRequest $request)
    {
        $validated = $request->validated();
        $userId = auth()->id();

        // Client Creation / Retrieval
        $clientId = null;
        if (!empty($validated['client_name'])) {
            $client = \App\Models\Client::firstOrCreate(
                ['company_name' => $validated['client_name']],
                ['client_address' => $validated['client_address'] ?? null, 'created_by' => $userId]
            );
            $clientId = $client->client_Id;
        }

        if (empty($validated['number'])) {
            // Auto-generate project code
            $lastProject = \App\Models\Project::withTrashed()->latest('project_Id')->first();
            $nextId = $lastProject ? $lastProject->project_Id + 1 : 1;
            
            $code = 'EHS/PRJ/' . date('y') . '/' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            while (\App\Models\Project::withTrashed()->where('number', $code)->exists()) {
                $nextId++;
                $code = 'EHS/PRJ/' . date('y') . '/' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
            $validated['number'] = $code;
        }

        $project = \App\Models\Project::create([
            'client_Id'  => $clientId,
            'number'     => $validated['number'],
            'name'       => $validated['name'],
            'period'     => $validated['period'] ?? null,
            'pic_name'   => $validated['pic_name'] ?? null,
            'pic_no'     => $validated['pic_no'] ?? null,
            'status'     => $validated['status'] ?? 'draft',
            'project_category' => $validated['project_category'] ?? 'survey',
            'survey_type' => $validated['survey_type'] ?? 'sbes',
            'created_by' => $userId,
        ]);

        // Redirection Logic
        if (($validated['project_category'] ?? 'survey') === 'modeling') {
            return redirect()->route('projects.coming_soon')->with('success', 'Project created successfully. Modeling workflow is coming soon.');
        }

        if (($validated['survey_type'] ?? 'sbes') !== 'sbes') {
            return redirect()->route('projects.coming_soon')->with('success', 'Project created successfully. This survey workflow is coming soon.');
        }

        return redirect()->route('projects.show', $project->project_Id)->with('success', 'Project created successfully. You can now plan your survey lines.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = auth()->user()->projects()->findOrFail($id);
        $project->load('surveyLocations');
        return view('projects.overview', compact('project'));
    }

    public function updateAllowances(Request $request, string $id)
    {
        $project = auth()->user()->projects()->findOrFail($id);
        
        $data = $request->validate([
            'weather_days' => 'nullable|numeric|min:0',
            'mod_demod_days' => 'nullable|numeric|min:0',
            'patch_test_days' => 'nullable|numeric|min:0',
        ]);

        $project->update([
            'weather_days' => $data['weather_days'] ?? 0,
            'mod_demod_days' => $data['mod_demod_days'] ?? 0,
            'patch_test_days' => $data['patch_test_days'] ?? 0,
        ]);

        return redirect()->back()->with('success', 'Global allowances updated successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $project = auth()->user()->projects()->findOrFail($id);
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateProjectRequest $request, string $id)
    {
        $project = \App\Models\Project::where('project_Id', $id)->firstOrFail();
        $validated = $request->validated();

        $userId = auth()->id();

        // Client Creation / Retrieval
        $clientId = null;
        if (!empty($validated['client_name'])) {
            $client = \App\Models\Client::firstOrCreate(
                ['company_name' => $validated['client_name']],
                ['client_address' => $validated['client_address'] ?? null, 'created_by' => $userId]
            );
            $clientId = $client->client_Id;
        }

        $project->update([
            'client_Id'  => $clientId,
            'number'     => $validated['number'] ?? $project->number,
            'name'       => $validated['name'],
            'period'     => $validated['period'] ?? null,
            'pic_name'   => $validated['pic_name'] ?? null,
            'pic_no'     => $validated['pic_no'] ?? null,
            'status'     => $validated['status'] ?? 'draft',
            'updated_by' => $userId,
        ]);

        return redirect()->route('projects.show', $project->project_Id)->with('success', 'Project updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $project = auth()->user()->projects()->findOrFail($id);
        $project->delete();
        
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

}
