<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ProjectController extends Controller
{
    /** Render the migrated React project register while preserving the existing query contract. */
    public function indexReact(Request $request): InertiaResponse
    {
        $query = Project::with('client')->latest();

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('number', 'like', "%{$search}%")
                    ->orWhereHas('client', fn ($client) => $client->where('company_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) $query->where('status', $request->input('status'));
        if ($request->filled('project_type')) $query->where('project_type', $request->input('project_type'));
        if ($request->filled('location')) $query->where('location', $request->input('location'));

        if ($request->filled('date_range')) {
            $now = now();
            switch ($request->input('date_range')) {
                case 'last_30_days':
                    $query->where('created_at', '>=', $now->copy()->subDays(30));
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', $now->year);
                    break;
            }
        }

        if ($request->input('survey_status') === 'Mapped') {
            $query->whereHas('surveyLocations', fn ($builder) => $builder->where('status', 'Mapped'));
        }
        if ($request->input('has_quotation') === '1') $query->has('lineItems');
        elseif ($request->input('has_quotation') === '0') $query->doesntHave('lineItems');

        $attention = $request->input('attention');
        if (in_array($attention, ['boundaries', 'lines', 'cost'], true)) {
            $query->where('status', '!=', 'completed');
            if ($attention === 'boundaries') $query->doesntHave('boundaries');
            elseif ($attention === 'lines') $query->has('boundaries')->doesntHave('surveyLines');
            elseif ($attention === 'cost') $query->doesntHave('lineItems');
        }

        $projects = $query->paginate(10)->withQueryString();
        $currentPage = $projects->currentPage();
        $lastPage = $projects->lastPage();
        $startPage = max(1, $currentPage - 2);
        $endPage = min($lastPage, $currentPage + 2);
        $pageLinks = [];

        if ($startPage > 1) {
            $pageLinks[] = ['label' => '1', 'url' => $projects->url(1), 'active' => false];
            if ($startPage > 2) $pageLinks[] = ['label' => '...', 'url' => null, 'active' => false];
        }
        for ($page = $startPage; $page <= $endPage; $page++) {
            $pageLinks[] = ['label' => (string) $page, 'url' => $projects->url($page), 'active' => $page === $currentPage];
        }
        if ($endPage < $lastPage) {
            if ($endPage < $lastPage - 1) $pageLinks[] = ['label' => '...', 'url' => null, 'active' => false];
            $pageLinks[] = ['label' => (string) $lastPage, 'url' => $projects->url($lastPage), 'active' => false];
        }

        return Inertia::render('ProjectIndex', [
            'projects' => [
                'data' => $projects->getCollection()->map(fn ($project) => [
                    'id' => $project->project_Id,
                    'number' => $project->number,
                    'name' => $project->name,
                    'period' => $project->period,
                    'client' => $project->client?->company_name,
                    'projectTypeCode' => $project->project_type,
                    'projectType' => Project::TYPES[$project->project_type] ?? ($project->project_type ?: 'Not set'),
                    'location' => $project->location,
                    'status' => $project->status ?: 'draft',
                    'createdAt' => $project->created_at?->format('d M Y'),
                ])->values(),
                'currentPage' => $currentPage,
                'lastPage' => $lastPage,
                'perPage' => $projects->perPage(),
                'total' => $projects->total(),
                'from' => $projects->firstItem(),
                'to' => $projects->lastItem(),
                'previousUrl' => $projects->previousPageUrl(),
                'nextUrl' => $projects->nextPageUrl(),
                'pageLinks' => $pageLinks,
            ],
            'totalProjects' => Project::count(),
            'statusCounts' => [
                'draft' => Project::where('status', 'draft')->count(),
                'planned' => Project::where('status', 'planned')->count(),
                'completed' => Project::where('status', 'completed')->count(),
            ],
            'projectTypes' => Project::TYPES,
            'locations' => Project::LOCATIONS,
            'filters' => array_merge([
                'search' => '', 'status' => '', 'project_type' => '', 'location' => '',
                'date_range' => '', 'survey_status' => '', 'has_quotation' => '', 'attention' => '',
            ], $request->only(['search', 'status', 'project_type', 'location', 'date_range', 'survey_status', 'has_quotation', 'attention'])),
            'today' => now()->format('d M Y'),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // 1. Base Query for Projects with Filtering (Shared Company-wide)
        $query = Project::with('client')->latest();

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

        if ($request->filled('project_type')) {
            $query->where('project_type', $request->project_type);
        }

        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }

        if ($request->filled('date_range')) {
            $now = now();
            switch ($request->date_range) {
                case 'last_30_days':
                    $query->where('created_at', '>=', $now->subDays(30));
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', $now->year);
                    break;
            }
        }

        if ($request->input('survey_status') === 'Mapped') {
            $query->whereHas('surveyLocations', fn ($q) => $q->where('status', 'Mapped'));
        }

        if ($request->input('has_quotation') === '1') {
            $query->has('lineItems');
        } elseif ($request->input('has_quotation') === '0') {
            $query->doesntHave('lineItems');
        }

        $attention = $request->input('attention');
        if (in_array($attention, ['boundaries', 'lines', 'cost'], true)) {
            $query->where('status', '!=', 'completed');

            if ($attention === 'boundaries') {
                $query->doesntHave('boundaries');
            } elseif ($attention === 'lines') {
                $query->has('boundaries')->doesntHave('surveyLines');
            } elseif ($attention === 'cost') {
                $query->doesntHave('lineItems');
            }
        }

        $projects = $query->paginate(10)->withQueryString();

        $totalProjects = Project::count();

        return view('projects.index', [
            'projects' => $projects,
            'totalProjects' => $totalProjects,
            'projectTypes' => \App\Models\Project::TYPES,
            'locations' => \App\Models\Project::LOCATIONS,
        ]);
    }

    /** Render the new React project intake form. */
    public function createReact(Request $request): InertiaResponse
    {
        return Inertia::render('CreateProject', [
            'projectTypes' => Project::TYPES,
            'locations' => Project::LOCATIONS,
            'csrfToken' => csrf_token(),
            'oldInput' => $request->session()->get('_old_input', []),
            'today' => now()->format('d M Y'),
        ]);
    }

    /** Render the new React project edit form. */
    public function editReact(Request $request, string $id): InertiaResponse
    {
        $project = Project::with('client')->findOrFail($id);

        return Inertia::render('EditProject', [
            'project' => [
                'id' => $project->project_Id,
                'number' => $project->number,
                'name' => $project->name,
                'projectType' => $project->project_type,
                'location' => $project->location,
                'status' => $project->status,
                'period' => $project->period,
                'clientName' => $project->client?->company_name,
                'clientAddress' => $project->client?->client_address,
                'picName' => $project->pic_name,
                'picNo' => $project->pic_no,
            ],
            'projectTypes' => Project::TYPES,
            'locations' => Project::LOCATIONS,
            'csrfToken' => csrf_token(),
            'oldInput' => $request->session()->get('_old_input', []),
            'today' => now()->format('d M Y'),
        ]);
    }

    /** Keep the existing Blade form available as a migration fallback. */
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
            // Auto-generate project code: EHS/<type>/<client initials>/<running no.>
            $typeCode = $validated['project_type'];

            // Client code = initials of each word in the client name, e.g. "KU"
            $clientCode = collect(preg_split('/\s+/', trim($validated['client_name'] ?? '')))
                ->filter()
                ->map(fn ($w) => strtoupper(mb_substr($w, 0, 1)))
                ->implode('') ?: 'XX';

            $prefix = "EHS/{$typeCode}/{$clientCode}/";

            // Running number is global across ALL projects (same rule as QuotationController::nextNumber)
            $lastRunning = \App\Models\Project::withTrashed()
                ->whereNotNull('number')
                ->pluck('number')
                ->filter(fn ($n) => str_contains($n, '/'))
                ->map(fn ($n) => \Illuminate\Support\Str::afterLast($n, '/'))
                ->filter(fn ($last) => is_numeric($last))
                ->map(fn ($last) => (int) $last)
                ->max() ?? 0;

            $validated['number'] = $prefix . str_pad($lastRunning + 1, 3, '0', STR_PAD_LEFT);
        }

        $project = \App\Models\Project::create([
            'client_Id'  => $clientId,
            'number'     => $validated['number'],
            'project_type' => $validated['project_type'],
            'name'       => $validated['name'],
            'location'   => $validated['location'],
            'period'     => $validated['period'] ?? null,
            'description' => $validated['description'] ?? null,
            'pic_name'   => $validated['pic_name'] ?? null,
            'pic_no'     => $validated['pic_no'] ?? null,
            'status'     => $validated['status'] ?? 'draft',
            'created_by' => $userId,
        ]);

        return redirect()->route('projects.show', $project->project_Id)->with('success', 'Project created successfully. You can now add survey areas or set up modelling.');

    }

    /** Render the React project workspace while preserving the existing workflow endpoints. */
    public function showReact(Request $request, string $id): InertiaResponse
    {
        $project = Project::with(['client', 'surveyLocations', 'modellingSummary', 'modellingItems'])->findOrFail($id);
        $summary = $project->modellingSummary;

        return Inertia::render('ProjectOverview', [
            'project' => [
                'id' => $project->project_Id,
                'name' => $project->name,
                'number' => $project->number,
                'projectType' => Project::TYPES[$project->project_type] ?? ($project->project_type ?: 'Type not set'),
                'location' => $project->location,
                'status' => $project->status ?: 'draft',
                'period' => $project->period,
                'createdAt' => $project->created_at?->format('d M Y'),
                'clientName' => $project->client?->company_name,
                'weatherDays' => (float) ($project->weather_days ?? 0),
                'modDemodDays' => (float) ($project->mod_demod_days ?? 0),
                'patchTestDays' => (float) ($project->patch_test_days ?? 0),
            ],
            'surveyLocations' => $project->surveyLocations->map(fn ($location) => [
                'id' => $location->id,
                'name' => $location->name,
                'surveyType' => $location->survey_type === 'drone' ? 'drone' : 'sbes',
                'status' => $location->status ?: 'Pending',
                'createdAt' => $location->created_at?->format('d M Y'),
            ])->values(),
            'modelling' => $summary ? [
                'packageName' => $summary->package_name,
                'grandTotal' => (float) $summary->grand_total,
                'moduleCount' => $project->modellingItems->pluck('catalog_module_id')->unique()->count(),
            ] : null,
            'csrfToken' => csrf_token(),
            'oldInput' => $request->session()->get('_old_input', []),
            'today' => now()->format('d M Y'),
        ]);
    }

    /** Keep the existing Blade project workspace available during migration. */
    public function show(string $id)
    {
        $project = Project::with([
            'client',
            'surveyLocations',
            'modellingSummary',
            'modellingItems',
        ])->findOrFail($id);
        return view('projects.overview', compact('project'));
    }

    public function updateAllowances(Request $request, string $id)
    {
        $project = Project::findOrFail($id);
        
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
        $project = Project::findOrFail($id);
        return view('projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateProjectRequest $request, string $id)
    {
        $project = Project::findOrFail($id);
        $validated = $request->validated();

        $userId = auth()->id();

        // Client Creation / Retrieval
        $clientId = null;
        if (!empty($validated['client_name'])) {
            $client = \App\Models\Client::firstOrCreate(
                ['company_name' => $validated['client_name']],
                ['client_address' => $validated['client_address'] ?? null, 'created_by' => $userId]
            );

            // Company already existed: keep its address up to date
            if (!empty($validated['client_address']) && $client->client_address !== $validated['client_address']) {
                $client->update([
                    'client_address' => $validated['client_address'],
                    'updated_by'     => $userId,
                ]);
            }

            $clientId = $client->client_Id;
        }

        // Project type is editable. If it changes, rebuild the number with the new type code,
        // keeping the same running number: EHS/<type>/<client initials>/<running no.>
        $newType = $validated['project_type'] ?? $project->project_type;
        $number  = $project->number;

        if ($newType && $newType !== $project->project_type) {
            $clientCode = collect(preg_split('/\s+/', trim($validated['client_name'] ?? '')))
                ->filter()
                ->map(fn ($w) => strtoupper(mb_substr($w, 0, 1)))
                ->implode('') ?: 'XX';

            $running = \Illuminate\Support\Str::afterLast($project->number ?? '', '/');
            $running = is_numeric($running) ? $running : str_pad((string) $project->project_Id, 3, '0', STR_PAD_LEFT);

            $number = "EHS/{$newType}/{$clientCode}/{$running}";
        }

        // If the number still has the XX placeholder and a company is now given, fix it
        $number = \App\Models\Project::fillClientInNumber($number, $validated['client_name'] ?? null);

        $project->update([
            'client_Id'  => $clientId,
            'number'     => $number,
            'project_type' => $newType,
            'name'       => $validated['name'],
            'location'   => $validated['location'] ?? $project->location,   // <-- NEW
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
        $project = Project::findOrFail($id);
        $project->delete();
        
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

}