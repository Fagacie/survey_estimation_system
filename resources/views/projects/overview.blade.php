<x-app-layout containerClass="w-full px-8 py-8">
    <x-slot name="header">{{ $project->name }}</x-slot>

    <div class="w-full max-w-5xl mx-auto">
        
        <div class="mb-4">
            <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 rounded text-xs font-bold transition-colors shadow-sm">
                &larr; Back
            </a>
        </div>

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
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight mb-1">{{ $project->name }}</h1>
            <div class="text-sm font-medium text-slate-500">Project Code: {{ $project->number ?? 'N/A' }}</div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 bg-white border border-slate-200 rounded-lg mt-6 shadow-sm overflow-hidden">
                <div class="p-5 border-b md:border-b-0 md:border-r border-slate-200">
                    <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase mb-1">Client</div>
                    <div class="text-sm font-semibold text-slate-800">{{ $project->client?->company_name ?? 'N/A' }}</div>
                </div>
                <div class="p-5 border-b md:border-b-0 md:border-r border-slate-200">
                    <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase mb-1">Period</div>
                    <div class="text-sm font-semibold text-slate-800">{{ $project->period ?? 'N/A' }}</div>
                </div>
                <div class="p-5">
                    <div class="text-[10px] font-bold tracking-wider text-slate-400 uppercase mb-1">Created</div>
                    <div class="text-sm font-semibold text-slate-800">{{ $project->created_at->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        <!-- 2. SURVEY AREAS -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">1. Survey Areas</h2>
                <div class="flex gap-2">
                    @if($project->survey_type === 'drone')
                        <a href="{{ url('/admin/equipment') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 rounded text-[11px] font-bold uppercase tracking-wider transition-colors shadow-sm">
                            <i class="fa-solid fa-database"></i> Equipment Database
                        </a>
                    @endif
                    <button type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white border border-transparent rounded text-[11px] font-bold uppercase tracking-wider transition-colors shadow-sm" data-bs-toggle="modal" data-bs-target="#newSurveyModal">
                        <i class="fa-solid fa-plus"></i> Add Area
                    </button>
                </div>
            </div>

            @if($project->surveyLocations->count() > 0)
                <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                    <ul class="divide-y divide-slate-100">
                        @foreach($project->surveyLocations as $location)
                            <li class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 transition-colors">
                                <div>
                                    <div class="flex items-center gap-2.5 mb-1">
                                        <div class="font-bold text-slate-800 text-sm">{{ $location->name }}</div>
                                        @if($location->status === 'Mapped')
                                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Mapped</span>
                                        @else
                                            <span class="bg-slate-100 text-slate-600 border border-slate-200 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Pending</span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-slate-500 font-medium">Added {{ $location->created_at->format('M d, Y') }}</div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('projects.surveys.map', [$project->project_Id, $location->id]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1">
                                        Open Map <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i>
                                    </a>
                                    <form action="{{ route('projects.surveys.destroy', [$project->project_Id, $location->id]) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this survey area?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-700 transition-colors">Remove</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <div class="text-center py-12 bg-white border border-slate-200 rounded-lg shadow-sm">
                    <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fa-regular fa-map text-xl"></i>
                    </div>
                    <div class="text-sm font-medium text-slate-500 mb-4">No survey areas have been defined.</div>
                    <button type="button" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 rounded text-xs font-bold uppercase tracking-wider transition-colors shadow-sm" data-bs-toggle="modal" data-bs-target="#newSurveyModal">
                        Create First Area
                    </button>
                </div>
            @endif
        </div>

        <!-- 3. MODELLING -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">2. Modelling</h2>
            </div>

            @php $summary = $project->modellingSummary; @endphp

            @if($summary)
                <div class="bg-white border border-slate-200 rounded-lg shadow-sm">
                    <ul class="divide-y divide-slate-100">
                        <li class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-slate-50 transition-colors">
                            <div>
                                <div class="flex items-center gap-2.5 mb-1">
                                    <div class="font-bold text-slate-800 text-sm">Modelling configured</div>
                                    <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider">Saved</span>
                                </div>
                                <div class="text-[11px] text-slate-500 font-medium">
                                    Package: {{ $summary->package_name ?? 'Custom selection' }},
                                    {{ $project->modellingItems->pluck('catalog_module_id')->unique()->count() }} modules,
                                    RM {{ number_format($summary->grand_total, 2) }}
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <a href="{{ route('projects.modeling.builder', ['project_id' => $project->project_Id]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1">
                                    Edit Modelling <i class="fa-solid fa-arrow-right text-[10px] ml-0.5"></i>
                                </a>
                                <form action="{{ route('projects.modeling.destroy', $project->project_Id) }}" method="POST" class="m-0" onsubmit="return confirm('Remove modelling data for this project?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-700 transition-colors">Remove</button>
                                </form>
                            </div>
                        </li>
                    </ul>
                </div>
            @else
                <div class="text-center py-12 bg-white border border-slate-200 rounded-lg shadow-sm">
                    <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-cubes text-xl"></i>
                    </div>
                    <div class="text-sm font-medium text-slate-500 mb-4">No modelling has been done yet.</div>
                    <a href="{{ route('projects.modeling.builder', ['project_id' => $project->project_Id]) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50 rounded text-xs font-bold uppercase tracking-wider transition-colors shadow-sm">
                        Start Modelling
                    </a>
                </div>
            @endif
        </div>

        <!-- 4. ACTION -->
        @php $hasSurvey = $project->surveyLocations->count() > 0; @endphp

        @if($hasSurvey || $project->modellingSummary)
            <div class="mt-8 pt-8 border-t border-slate-200 text-center">
                <div class="flex flex-col sm:flex-row justify-center gap-3">
                    <a href="{{ route('quotation.index', ['project_id' => $project->project_Id]) }}" class="inline-flex justify-center items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-bold transition-colors shadow-sm">
                        Proceed to Quotation
                    </a>
                    @if($hasSurvey)
                        <a href="{{ route('projects.report.preview', $project->project_Id) }}" class="inline-flex justify-center items-center px-6 py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-md text-sm font-bold transition-colors shadow-sm">
                            View Survey Report
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Modal -->
    <div class="modal fade" id="newSurveyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-lg shadow-xl overflow-hidden">
                <form action="{{ route('projects.surveys.store', $project->project_Id) }}" method="POST">
                    @csrf
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h5 class="text-base font-bold text-slate-800">New Survey Area</h5>
                        <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="p-6 bg-white">
                        <div class="mb-6">
                            <label for="name" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Area Name</label>
                            <input type="text" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-md text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-sm transition-shadow" id="name" name="name" placeholder="e.g. Main River, Tributary A" required>
                        </div>
                        <button type="submit" class="w-full flex justify-center py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-bold transition-colors shadow-sm">Create Area</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>