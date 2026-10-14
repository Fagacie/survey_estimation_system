<x-app-layout containerClass="w-full px-8 py-8">
    <x-slot name="header">{{ $project->name }}</x-slot>
    <link rel="stylesheet" href="{{ asset('css/project-overview.css') }}?v={{ filemtime(public_path('css/project-overview.css')) }}">

    <div class="project-overview-page">
    <div class="project-overview-content">
        
        <div class="project-overview-breadcrumb"><a href="{{ route('projects.index') }}"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to projects</a></div>

        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-8 flex items-start gap-3 text-sm rounded-md">
                <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 text-red-800 border border-red-200 p-4 mb-8 flex items-start gap-3 text-sm rounded-md">
                <i class="fa-solid fa-triangle-exclamation mt-0.5 text-red-600"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <!-- 1. PROJECT HEADER -->
        <header class="project-overview-hero">
            <div class="project-overview-hero-top">
                <div class="project-overview-title-group">
                    <p class="project-overview-eyebrow">Project workspace <span aria-hidden="true">/</span> survey operations</p>
                    <h1>{{ $project->name }}</h1>
                    <div class="project-overview-code"><i class="fa-solid fa-hashtag" aria-hidden="true"></i> {{ $project->number ?? 'Project code not assigned' }}</div>
                    <div class="project-overview-badges">
                        <span class="project-overview-status project-overview-status-{{ strtolower($project->status ?? 'draft') }}">{{ ucfirst($project->status ?? 'draft') }}</span>
                        <span class="project-overview-type">{{ \App\Models\Project::TYPES[$project->project_type] ?? $project->project_type ?? 'Type not set' }}</span>
                        @if($project->location)
                            <span class="project-overview-location"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $project->location }}</span>
                        @endif
                    </div>
                </div>
                <div class="project-overview-hero-actions">
                    <a href="{{ route('projects.index') }}" class="project-overview-secondary-action"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Project register</a>
                    <a href="{{ route('projects.edit', $project->project_Id) }}" class="project-overview-primary-action"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Edit project</a>
                </div>
            </div>
            <div class="project-overview-meta-grid">
                <div class="project-overview-meta-item"><span>Client</span><strong>{{ $project->client?->company_name ?? 'No client assigned' }}</strong></div>
                <div class="project-overview-meta-item"><span>Project period</span><strong>{{ $project->period ?: 'Not specified' }}</strong></div>
                <div class="project-overview-meta-item"><span>Created</span><strong>{{ $project->created_at?->format('d M Y') ?? 'N/A' }}</strong></div>
            </div>
            <div class="project-overview-summary" aria-label="Project workflow summary">
                <div class="project-overview-summary-item"><span class="project-overview-summary-icon"><i class="fa-solid fa-map-location-dot" aria-hidden="true"></i></span><span><small>Survey areas</small><strong>{{ $project->surveyLocations->count() }}</strong></span></div>
                <div class="project-overview-summary-item"><span class="project-overview-summary-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></span><span><small>Mapped areas</small><strong>{{ $project->surveyLocations->where('status', 'Mapped')->count() }}</strong></span></div>
                <div class="project-overview-summary-item"><span class="project-overview-summary-icon"><i class="fa-solid fa-cubes" aria-hidden="true"></i></span><span><small>Modelling</small><strong>{{ $project->modellingSummary ? 'Configured' : 'Not added' }}</strong></span></div>
            </div>
        </header>

        <!-- ADD WORKFLOW: kept ahead of the workflow register for a clear next action -->
        <section class="project-overview-section project-overview-add mb-8">
            <div class="project-overview-section-heading flex items-center justify-between mb-4">
                <div><p class="project-overview-section-kicker">Expand this project</p><h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Add a Workflow</h2><p class="project-overview-section-description">Add a survey area or configure a modelling package for this project.</p></div>
                <a href="{{ url('/admin/equipment') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 rounded text-[11px] font-bold uppercase tracking-wider transition-colors shadow-sm">
                    <i class="fa-solid fa-database"></i> Equipment Database
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Add SBES -->
                <button type="button" onclick="openSurveyModal('sbes')" class="flex flex-col items-center justify-center p-6 bg-white border border-slate-200 rounded-lg shadow-sm hover:border-teal-400 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 rounded-full bg-teal-50 text-teal-600 flex items-center justify-center mb-3 group-hover:bg-teal-100 transition-colors">
                        <i class="fa-solid fa-water text-xl"></i>
                    </div>
                    <div class="font-bold text-slate-800 text-sm mb-1">Single Beam (SBES)</div>
                    <div class="text-xs text-slate-500 text-center">Add a hydrographic survey area.</div>
                </button>

                <!-- Add Drone -->
                <button type="button" onclick="openSurveyModal('drone')" class="flex flex-col items-center justify-center p-6 bg-white border border-slate-200 rounded-lg shadow-sm hover:border-blue-400 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-500 flex items-center justify-center mb-3 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                        <i class="fa-solid fa-plane-up text-xl"></i>
                    </div>
                    <div class="font-bold text-slate-800 text-sm mb-1">Drone Mapping</div>
                    <div class="text-xs text-slate-500 text-center">Add an aerial survey area.</div>
                </button>

                <!-- Add Modelling -->
                @if(!$summary)
                <a href="{{ route('projects.modeling.builder', ['project_id' => $project->project_Id]) }}" class="flex flex-col items-center justify-center p-6 bg-white border border-slate-200 rounded-lg shadow-sm hover:border-indigo-400 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 rounded-full bg-slate-50 text-slate-500 flex items-center justify-center mb-3 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                        <i class="fa-solid fa-cubes text-xl"></i>
                    </div>
                    <div class="font-bold text-slate-800 text-sm mb-1">3D Modelling</div>
                    <div class="text-xs text-slate-500 text-center">Configure processing and models.</div>
                </a>
                @else
                <div class="flex flex-col items-center justify-center p-6 bg-slate-50 border border-slate-200 rounded-lg shadow-sm opacity-60">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                        <i class="fa-solid fa-cubes text-xl"></i>
                    </div>
                    <div class="font-bold text-slate-600 text-sm mb-1">3D Modelling</div>
                    <div class="text-xs text-slate-500 text-center">Already added to project.</div>
                </div>
                @endif
                
            </div>
        </section>



        <!-- ACTIVE WORKFLOWS -->
        <section class="project-overview-section project-overview-workflows mb-10">
            <div class="project-overview-section-heading flex items-center justify-between mb-4">
                <div><p class="project-overview-section-kicker">Survey register</p><h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Active Workflows</h2><p class="project-overview-section-description">Manage survey areas and modelling packages, with each action kept beside its workflow.</p></div>
                @if($project->surveyLocations->count() > 1)
                    <a href="{{ route('projects.report.preview', $project->project_Id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:text-slate-900 hover:border-slate-300 rounded text-xs font-bold transition-colors shadow-sm">
                        <i class="fa-solid fa-file-lines text-slate-500"></i> Full Project Report (All Areas)
                    </a>
                @endif
            </div>
            
            <div class="project-overview-workflow-table">
                <div class="project-overview-workflow-table-head" aria-hidden="true"><span>Workflow</span><span>Status / details</span><span>Actions</span></div>
                <ul class="divide-y divide-slate-100">
                    
                    @php 
                        $hasAnyModule = false; 
                        $hasSurvey = $project->surveyLocations->count() > 0;
                    @endphp

                    @foreach($project->surveyLocations as $location)
                        @php $hasAnyModule = true; @endphp
                        <li class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 transition-colors">
                            <div>
                                <div class="flex items-center gap-2.5 mb-1">
                                    @if($location->survey_type === 'drone')
                                        <i class="fa-solid fa-plane-up text-slate-500 w-4 text-center"></i>
                                        <div class="font-bold text-slate-800 text-sm">Drone Mapping: {{ $location->name }}</div>
                                    @else
                                        <i class="fa-solid fa-water text-teal-600 w-4 text-center"></i>
                                        <div class="font-bold text-slate-800 text-sm">SBES Survey: {{ $location->name }}</div>
                                    @endif
                                    
                                    @if($location->status === 'Mapped')
                                        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Mapped</span>
                                    @else
                                        <span class="bg-slate-100 text-slate-600 border border-slate-200 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Pending</span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-500 font-medium">Added {{ $location->created_at->format('M d, Y') }}</div>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center gap-3">
                                <a href="{{ route('projects.report.preview', ['project' => $project->project_Id, 'location_id' => $location->id]) }}" class="text-sm font-semibold text-teal-600 hover:text-teal-800 transition-colors flex items-center gap-1">
                                    Report <i class="fa-solid fa-file-pdf text-[10px] ml-0.5"></i>
                                </a>
                                <a href="{{ route('projects.surveys.map', [$project->project_Id, $location->id]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1">
                                    Open Map <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i>
                                </a>
                                <a href="{{ route('quotation.index', ['project_id' => $project->project_Id, 'module' => $location->survey_type, 'location_id' => $location->id]) }}" class="inline-flex justify-center items-center px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-bold transition-colors shadow-sm">
                                    Proceed to Quotation
                                </a>
                                <form action="{{ route('projects.surveys.destroy', [$project->project_Id, $location->id]) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this survey area?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-700 transition-colors ml-2"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </li>
                    @endforeach

                    @php $summary = $project->modellingSummary; @endphp
                    @if($summary)
                        @php $hasAnyModule = true; @endphp
                        <li class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 transition-colors">
                            <div>
                                <div class="flex items-center gap-2.5 mb-1">
                                    <i class="fa-solid fa-cubes text-slate-600 w-4 text-center"></i>
                                    <div class="font-bold text-slate-800 text-sm">3D Modelling</div>
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Saved</span>
                                </div>
                                <div class="text-[11px] text-slate-500 font-medium">
                                    Package: {{ $summary->package_name ?? 'Custom selection' }},
                                    {{ $project->modellingItems->pluck('catalog_module_id')->unique()->count() }} modules,
                                    RM {{ number_format($summary->grand_total, 2) }}
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center gap-3">
                                <a href="{{ route('projects.modeling.builder', ['project_id' => $project->project_Id]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1">
                                    Edit Modules <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i>
                                </a>
                                <a href="{{ route('quotation.index', ['project_id' => $project->project_Id, 'module' => 'modelling']) }}" class="inline-flex justify-center items-center px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs font-bold transition-colors shadow-sm">
                                    Proceed to Quotation
                                </a>
                                <form action="{{ route('projects.modeling.destroy', $project->project_Id) }}" method="POST" class="m-0" onsubmit="return confirm('Remove modelling data for this project?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-700 transition-colors ml-2"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </div>
                        </li>
                    @endif

                    @if(!$hasAnyModule)
                        <li class="p-8 text-center text-slate-500 font-medium text-sm">
                            <i class="fa-regular fa-folder-open text-3xl text-slate-300 mb-3 block"></i>
                            No workflows have been added to this project yet. Choose an option below to get started.
                        </li>
                    @endif

                </ul>
            </div>
        </section>

    </div>

    <!-- Modal for Survey Areas -->
    <div class="modal fade" id="newSurveyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-lg shadow-xl overflow-hidden">
                <form action="{{ route('projects.surveys.store', $project->project_Id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="survey_type" id="modal_survey_type" value="sbes">
                    
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h5 class="text-base font-bold text-slate-800" id="modal_title">New Survey Area</h5>
                        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="p-6 bg-white">
                        <div class="mb-6">
                            <label for="name" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Area Name</label>
                            <input type="text" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-sm transition-shadow" id="name" name="name" placeholder="e.g. Main River, Tributary A" required>
                        </div>
                        <button type="submit" class="w-full flex justify-center py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-bold transition-colors shadow-sm">Create Module</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script for Modal handling -->
    <script>
        function openSurveyModal(type) {
            console.log("Opening modal for type: " + type);
            var hiddenInput = document.getElementById('modal_survey_type');
            if (hiddenInput) {
                hiddenInput.value = type;
                console.log("Set hidden input to: " + hiddenInput.value);
            }
            if (type === 'drone') {
                document.getElementById('modal_title').innerText = 'New Drone Mapping Area';
            } else {
                document.getElementById('modal_title').innerText = 'New SBES Survey Area';
            }
            var modalEl = document.getElementById('newSurveyModal');
            var myModal = bootstrap.Modal.getInstance(modalEl);
            if (!myModal) {
                myModal = new bootstrap.Modal(modalEl);
            }
            myModal.show();
        }
    </script>
</div>
</x-app-layout>