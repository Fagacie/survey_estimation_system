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
                  ->orWhere('project_code', 'like', "%{$search}%")
                  ->orWhere('client', 'like', "%{$search}%");
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
        ];

        // (SurveyStatistic table was removed, distance is calculated live in the map UI)
        $totalDistance = 0;

        $projectsWithLines = auth()->user()->projects()->has('surveyLines')->count();
        $projectsAwaitingPlanning = auth()->user()->projects()->doesntHave('boundaries')->count();
        $projectsWithCost = auth()->user()->projects()->has('costEstimation')->count();

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
        $missingParamsCount = auth()->user()->projects()->where('status', '!=', 'completed')->doesntHave('sbesParameters')->count();
        $missingCostCount = auth()->user()->projects()->where('status', '!=', 'completed')->doesntHave('costEstimation')->count();

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

        if (empty($validated['project_code'])) {
            // Auto-generate project code, checking soft-deleted records to avoid unique constraint violations
            $lastProject = \App\Models\Project::withTrashed()->latest('id')->first();
            $nextId = $lastProject ? $lastProject->id + 1 : 1;
            
            $code = 'PRJ-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            while (\App\Models\Project::withTrashed()->where('project_code', $code)->exists()) {
                $nextId++;
                $code = 'PRJ-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
            $validated['project_code'] = $code;
        }

        $validated['user_id'] = auth()->id();
        $project = \App\Models\Project::create($validated);

        return redirect()->route('projects.show', $project->id)->with('success', 'Project created successfully. You can now plan your survey lines.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = auth()->user()->projects()->findOrFail($id);
        // Note: Intentionally NOT loading 'surveyLines' here for performance. They are fetched via mapLines AJAX.
        $project->load('sbesParameters', 'boundaries', 'costEstimation.items');
        return view('projects.show', compact('project'));
    }

    public function mapLines(string $id)
    {
        $project = auth()->user()->projects()->findOrFail($id);

        // Avoid eloquent hydration for performance when returning potentially thousands of lines
        $lines = \Illuminate\Support\Facades\DB::table('survey_lines')
            ->where('project_id', $project->id)
            ->select('geometry', 'type')
            ->get()
            ->map(function ($line) {
                $geom = is_string($line->geometry) ? json_decode($line->geometry, true) : $line->geometry;
                $geom['properties'] = $geom['properties'] ?? [];
                $geom['properties']['line_type'] = $line->type;
                return $geom;
            });
            
        return response()->json($lines);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = auth()->user()->projects()->findOrFail($id);
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateProjectRequest $request, string $id)
    {
        $project = auth()->user()->projects()->findOrFail($id);
        $project->update($request->validated());

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
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
