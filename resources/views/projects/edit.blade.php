<x-app-layout>
    
    <x-slot name="header">Edit Project</x-slot>

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="mt-4">
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Projects
                </a>
            </div>

            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body p-4">
                    <form action="{{ route('projects.update', $project->project_Id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Project Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $project->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Project No.</label>
                                <input type="text" class="form-control bg-light" value="{{ $project->number }}" readonly>
                                <div class="form-text">Updates automatically when the type changes.</div>
                            </div>
                        </div>

                        <!-- Project Type + Location (NEW) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Project Type</label>
                                <select name="project_type" class="form-select @error('project_type') is-invalid @enderror">
                                    <option value="">{{ $project->project_type ? 'Choose project type' : 'Not set (older project)' }}</option>
                                    @foreach(\App\Models\Project::TYPES as $code => $label)
                                        <option value="{{ $code }}" {{ old('project_type', $project->project_type) == $code ? 'selected' : '' }}>
                                            {{ $code }} - {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Location</label>
                                <select name="location" class="form-select @error('location') is-invalid @enderror">
                                    <option value="">{{ $project->location ? 'Choose location' : 'Not set (older project)' }}</option>
                                    @foreach(\App\Models\Project::LOCATIONS as $loc)
                                        <option value="{{ $loc }}" {{ old('location', $project->location) == $loc ? 'selected' : '' }}>
                                            {{ $loc }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Client Name</label>
                                <input type="text" name="client_name" class="form-control @error('client_name') is-invalid @enderror" value="{{ old('client_name', $project->client->company_name ?? '') }}">
                                @error('client_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Period</label>
                                <input type="text" name="period" class="form-control @error('period') is-invalid @enderror" value="{{ old('period', $project->period) }}" placeholder="e.g. 5 Days">
                                @error('period') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Client Address</label>
                            <textarea name="client_address" class="form-control @error('client_address') is-invalid @enderror" rows="2">{{ old('client_address', $project->client->client_address ?? '') }}</textarea>
                            @error('client_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">PIC Name</label>
                                <input type="text" name="pic_name" class="form-control @error('pic_name') is-invalid @enderror" value="{{ old('pic_name', $project->pic_name) }}">
                                @error('pic_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">PIC No.</label>
                                <input type="text" name="pic_no" class="form-control @error('pic_no') is-invalid @enderror" value="{{ old('pic_no', $project->pic_no) }}">
                                @error('pic_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="draft" {{ old('status', $project->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="planned" {{ old('status', $project->status) == 'planned' ? 'selected' : '' }}>Planned</option>
                                <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1">Save Changes</button>
                            <a href="{{ route('projects.show', $project->project_Id) }}" class="btn btn-outline-secondary btn-lg">Go to Map</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>