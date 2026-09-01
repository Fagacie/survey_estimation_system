<x-app-layout>
    <x-slot name="header">{{ $project->name }}</x-slot>

    <style>
        .page-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 3rem 1rem 5rem;
        }
        
        .section-header {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #888;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        /* METADATA STRIP */
        .meta-strip {
            display: flex;
            background: #fafafa;
            border: 1px solid #eaeaea;
            border-radius: 4px;
            margin-top: 1.5rem;
        }
        .meta-item {
            flex: 1;
            padding: 1.25rem 1.5rem;
            border-right: 1px solid #eaeaea;
        }
        .meta-item:last-child {
            border-right: none;
        }
        .meta-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #888;
            margin-bottom: 0.25rem;
        }
        .meta-value {
            font-size: 0.95rem;
            font-weight: 500;
            color: #111;
        }

        /* FORMS & INPUTS */
        .allowance-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            background: #fff;
            border: 1px solid #eaeaea;
            padding: 2rem;
            border-radius: 4px;
        }
        .minimal-input {
            border: 1px solid #eaeaea;
            border-radius: 4px;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            background: #fafafa;
            color: #111;
            width: 100%;
            transition: border-color 0.2s;
        }
        .minimal-input:focus {
            outline: none;
            border-color: #111;
            background: #fff;
        }
        .input-label {
            font-size: 0.8rem;
            color: #444;
            margin-bottom: 0.5rem;
            display: block;
            font-weight: 500;
        }

        /* BUTTONS */
        .btn-outline-dark-minimal {
            background: transparent;
            border: 1px solid #111;
            color: #111;
            padding: 0.6rem 1.5rem;
            font-size: 0.85rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.2s;
        }
        .btn-outline-dark-minimal:hover {
            background: #111;
            color: #fff;
        }
        .btn-dark-minimal {
            background: #111;
            border: 1px solid #111;
            color: #fff;
            padding: 0.75rem 2.5rem;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.2s;
            display: inline-block;
            text-decoration: none;
        }
        .btn-dark-minimal:hover {
            background: #333;
            border-color: #333;
            color: #fff;
        }

        /* LISTS */
        .clean-list {
            list-style: none;
            padding: 0;
            margin: 0;
            border: 1px solid #eaeaea;
            border-radius: 4px;
        }
        .clean-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #eaeaea;
            background: #fff;
        }
        .clean-list-item:last-child {
            border-bottom: none;
        }
    </style>

    <div class="page-container">
        
        @if(session('success'))
            <div class="alert alert-success rounded-0 border-0 bg-light text-success mb-5" style="border-left: 3px solid #10b981 !important;">
                {{ session('success') }}
            </div>
        @endif

        <!-- 1. PROJECT HEADER -->
        <div class="mb-5">
            <h1 class="display-5 fw-bold mb-1" style="color: #111; letter-spacing: -0.03em;">{{ $project->name }}</h1>
            <div class="text-muted" style="font-size: 0.95rem;">Project Code: {{ $project->project_code ?? 'N/A' }}</div>
            
            <div class="meta-strip mt-4">
                <div class="meta-item">
                    <div class="meta-label">Client</div>
                    <div class="meta-value">{{ $project->client?->name ?? $project->getRawOriginal('client') ?? 'N/A' }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Location</div>
                    <div class="meta-value">{{ $project->location ?? 'N/A' }}</div>
                </div>
                <div class="meta-item">
                    <div class="meta-label">Created</div>
                    <div class="meta-value">{{ $project->created_at->format('d M Y') }}</div>
                </div>
            </div>
        </div>

        <!-- 2. SURVEY AREAS -->
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-end mb-3">
                <div class="section-header border-0 mb-0 pb-0">1. Survey Areas</div>
                <button type="button" class="btn-outline-dark-minimal py-1 px-3" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#newSurveyModal">
                    + Add Area
                </button>
            </div>

            @if($project->surveyLocations->count() > 0)
                <ul class="clean-list">
                    @foreach($project->surveyLocations as $location)
                        <li class="clean-list-item">
                            <div>
                                <div style="font-weight: 500; color: #111; font-size: 0.95rem;">{{ $location->name }}</div>
                                <div style="font-size: 0.8rem; color: #888; margin-top: 2px;">Added {{ $location->created_at->format('M d, Y') }}</div>
                            </div>
                            <div class="d-flex align-items-center gap-4">
                                <a href="{{ route('projects.surveys.map', [$project->id, $location->id]) }}" class="text-decoration-none" style="color: #111; font-size: 0.85rem; font-weight: 500;">
                                    Open Map &rarr;
                                </a>
                                <form action="{{ route('projects.surveys.destroy', [$project->id, $location->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this survey area?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background:none; border:none; color:#ef4444; font-size: 0.85rem; padding:0;">Remove</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center py-5 border" style="background: #fafafa; border-color: #eaeaea; border-radius: 4px;">
                    <div class="text-muted mb-3" style="font-size: 0.9rem;">No survey areas have been defined.</div>
                    <button type="button" class="btn-outline-dark-minimal" data-bs-toggle="modal" data-bs-target="#newSurveyModal">
                        Create First Area
                    </button>
                </div>
            @endif
        </div>

        <!-- 3. PROJECT SETTINGS (GLOBAL ALLOWANCES) -->
        <div class="mb-5">
            <div class="section-header">2. Global Allowances</div>
            <form action="{{ route('projects.updateAllowances', $project->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="allowance-grid">
                    <div>
                        <label class="input-label">Weather Standby (Days)</label>
                        <input type="number" step="0.1" name="weather_days" class="minimal-input" value="{{ $project->weather_days }}" placeholder="0.0">
                    </div>
                    <div>
                        <label class="input-label">MOB/DEMOB (Days)</label>
                        <input type="number" step="0.1" name="mod_demod_days" class="minimal-input" value="{{ $project->mod_demod_days }}" placeholder="0.0">
                    </div>
                    <div>
                        <label class="input-label">Patch Test (Days)</label>
                        <input type="number" step="0.1" name="patch_test_days" class="minimal-input" value="{{ $project->patch_test_days }}" placeholder="0.0">
                    </div>
                </div>
                <div class="text-end mt-3">
                    <button type="submit" class="btn-outline-dark-minimal">Save Settings</button>
                </div>
            </form>
        </div>

        <!-- 4. ACTION -->
        @if($project->surveyLocations->count() > 0)
            <div class="text-center mt-5 pt-5 border-top" style="border-color: #eaeaea !important;">
                <p class="text-muted mb-4" style="font-size: 0.95rem;">All areas mapped and allowances configured?</p>
                <a href="{{ route('projects.cost.show', $project->id) }}" class="btn-dark-minimal">
                    Proceed to Cost Estimation
                </a>
            </div>
        @endif
    </div>

    <!-- New Survey Area Modal -->
    <div class="modal fade" id="newSurveyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0 border-0" style="box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
                <form action="{{ route('projects.surveys.store', $project->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-bold" style="font-size: 1.1rem; letter-spacing: -0.02em;">New Survey Area</h5>
                        <button type="button" class="btn-close" style="font-size: 0.7rem;" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label for="name" class="input-label" style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; color: #888;">Area Name</label>
                            <input type="text" class="minimal-input" style="border-top:none; border-left:none; border-right:none; border-radius:0; padding: 0.5rem 0; background: transparent;" id="name" name="name" placeholder="e.g. Main River, Tributary A" required>
                        </div>
                        <button type="submit" class="btn-dark-minimal w-100" style="padding: 0.75rem;">Create Area</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
