<x-app-layout>
    <x-slot name="header">{{ $project->name }} – Overview</x-slot>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        <div class="row">
            <!-- Project Details -->
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h4 class="card-title fw-bold mb-3">Project Details</h4>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Code:</span>
                                <span class="fw-medium">{{ $project->project_code ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Client:</span>
                                <span class="fw-medium">{{ $project->client ?? 'N/A' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Location:</span>
                                <span class="fw-medium">{{ $project->location ?? 'N/A' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Global Allowances -->
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mb-3">Global Allowances</h5>
                        <form action="{{ route('projects.updateAllowances', $project->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label text-muted">Weather Days</label>
                                <input type="number" step="0.1" name="weather_days" class="form-control" value="{{ $project->weather_days }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">MOB/DEMOB Days</label>
                                <input type="number" step="0.1" name="mod_demod_days" class="form-control" value="{{ $project->mod_demod_days }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted">Patch Test Days</label>
                                <input type="number" step="0.1" name="patch_test_days" class="form-control" value="{{ $project->patch_test_days }}">
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill">Save Allowances</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Survey Areas -->
            <div class="col-md-8 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <h4 class="card-title fw-bold mb-0">Survey Areas</h4>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#newSurveyModal">
                            + Add Survey Area
                        </button>
                    </div>
                    <div class="card-body">
                        @if($project->surveyLocations->count() > 0)
                            <div class="list-group">
                                @foreach($project->surveyLocations as $location)
                                    <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0 mb-2 rounded bg-light">
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $location->name }}</h6>
                                            <small class="text-muted">Added {{ $location->created_at->format('M d, Y') }}</small>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <a href="{{ route('projects.surveys.map', [$project->id, $location->id]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                                Open Map
                                            </a>
                                            <form action="{{ route('projects.surveys.destroy', [$project->id, $location->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this survey area?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" style="width: 32px; height: 32px; padding: 0;">&times;</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <p class="mb-0">No survey areas defined yet.</p>
                                <small>Add a survey area to start mapping.</small>
                            </div>
                        @endif
                    </div>
                    @if($project->surveyLocations->count() > 0)
                        <div class="card-footer bg-white border-top-0 pb-4 text-end">
                            <a href="{{ route('projects.cost.show', $project->id) }}" class="btn btn-success btn-lg rounded-pill px-4 shadow-sm fw-bold">
                                Continue to Cost Estimation &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- New Survey Area Modal -->
    <div class="modal fade" id="newSurveyModal" tabindex="-1" aria-labelledby="newSurveyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('projects.surveys.store', $project->id) }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold" id="newSurveyModalLabel">New Survey Area</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label for="name" class="form-label text-muted fw-semibold">Area Name</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" id="name" name="name" placeholder="e.g. Main River, Tributary A" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">Create Area</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
