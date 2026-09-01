<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <span>Edit Project</span>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>
    </x-slot>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body p-4">
                    <form action="{{ route('projects.update', $project->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Project Reference (Code)</label>
                                <input type="text" name="project_code" class="form-control @error('project_code') is-invalid @enderror" value="{{ old('project_code', $project->project_code) }}" required>
                                @error('project_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                    <option value="draft" {{ old('status', $project->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="planned" {{ old('status', $project->status) == 'planned' ? 'selected' : '' }}>Planned</option>
                                    <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Project Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $project->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Client</label>
                                <select name="client_id" class="form-select @error('client_id') is-invalid @enderror">
                                    <option value="">Select a Client...</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>{{ $client->name }} {{ $client->company ? '('.$client->company.')' : '' }}</option>
                                    @endforeach
                                </select>
                                @error('client_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Location</label>
                                <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $project->location) }}">
                                @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>



                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Start Date</label>
                                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '') }}">
                                @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">End Date</label>
                                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '') }}">
                                @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $project->description) }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1">Save Changes</button>
                            <a href="{{ route('projects.show', $project->id) }}" class="btn btn-outline-secondary btn-lg">Go to Map</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
