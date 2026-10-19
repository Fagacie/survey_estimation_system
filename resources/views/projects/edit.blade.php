<x-app-layout containerClass="w-full px-4 py-6 sm:px-8 sm:py-8">
    <link rel="stylesheet" href="{{ asset('css/project-edit.css') }}?v={{ filemtime(public_path('css/project-edit.css')) }}">

    <div class="project-edit-page">
        <!-- BREADCRUMB -->
        <nav class="project-edit-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('projects.index') }}">
                <i class="fa-solid fa-folder-tree"></i> Projects
            </a>
            <span class="breadcrumb-separator">/</span>
            <a href="{{ route('projects.show', $project->project_Id) }}">
                {{ $project->name }}
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">Edit Details</span>
        </nav>

        <!-- PAGE HEADER -->
        <div class="project-edit-header">
            <div class="project-edit-header-info">
                <h1>Edit Project Details</h1>
                <p>Modify project classification, operational duration, or client contact coordinates.</p>
            </div>
            <div class="project-edit-header-actions">
                <a href="{{ route('projects.show', $project->project_Id) }}" class="project-edit-back-btn">
                    <i class="fa-solid fa-arrow-left"></i> Project Workspace
                </a>
            </div>
        </div>

        <!-- FORM WRAPPER -->
        <form action="{{ route('projects.update', $project->project_Id) }}" method="POST" id="editProjectForm" class="project-edit-form">
            @csrf
            @method('PUT')

            <div class="project-edit-grid">
                <!-- CARD 1: SCOPE & OPERATIONAL PARAMETERS -->
                <div class="project-edit-card">
                    <div class="project-edit-card-header">
                        <div class="project-edit-card-icon">
                            <i class="fa-solid fa-compass-drafting"></i>
                        </div>
                        <div class="project-edit-card-title">
                            <h2>Project Scope</h2>
                            <p>Operational categorization and timeline parameters</p>
                        </div>
                    </div>

                    <div class="project-edit-card-body">
                        <!-- Project Name & Project Code Grid -->
                        <div class="pe-field-row-2">
                            <div class="pe-field-group">
                                <label for="project_name" class="pe-label">
                                    <span>Project Name <span class="pe-label-required">*</span></span>
                                </label>
                                <input type="text"
                                       id="project_name"
                                       name="name"
                                       class="pe-input @error('name') is-invalid @enderror"
                                       value="{{ old('name', $project->name) }}"
                                       required>
                                @error('name')
                                    <div class="pe-error-message">
                                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="pe-field-group">
                                <label for="project_number" class="pe-label">
                                    <span>Project Code</span>
                                    <span class="pe-label-hint">System code</span>
                                </label>
                                <input type="text"
                                       id="project_number"
                                       class="pe-input"
                                       value="{{ $project->number }}"
                                       readonly>
                            </div>
                        </div>

                        <!-- Project Type & Location Grid -->
                        <div class="pe-field-row-2">
                            <div class="pe-field-group">
                                <label for="project_type" class="pe-label">
                                    <span>Project Type <span class="pe-label-required">*</span></span>
                                </label>
                                <select id="project_type"
                                        name="project_type"
                                        class="pe-select @error('project_type') is-invalid @enderror"
                                        required>
                                    <option value="">{{ $project->project_type ? 'Choose project type' : 'Not set (older project)' }}</option>
                                    @foreach(\App\Models\Project::TYPES as $code => $label)
                                        <option value="{{ $code }}" {{ old('project_type', $project->project_type) == $code ? 'selected' : '' }}>
                                            {{ $code }} · {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_type')
                                    <div class="pe-error-message">
                                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="pe-field-group">
                                <label for="location" class="pe-label">
                                    <span>Location <span class="pe-label-required">*</span></span>
                                </label>
                                <select id="location"
                                        name="location"
                                        class="pe-select @error('location') is-invalid @enderror"
                                        required>
                                    <option value="">{{ $project->location ? 'Choose location' : 'Not set (older project)' }}</option>
                                    @foreach(\App\Models\Project::LOCATIONS as $loc)
                                        <option value="{{ $loc }}" {{ old('location', $project->location) == $loc ? 'selected' : '' }}>
                                            {{ $loc }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location')
                                    <div class="pe-error-message">
                                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status & Duration Grid -->
                        <div class="pe-field-row-2">
                            <div class="pe-field-group">
                                <label for="status" class="pe-label">
                                    <span>Project Status <span class="pe-label-required">*</span></span>
                                </label>
                                <select id="status"
                                        name="status"
                                        class="pe-select @error('status') is-invalid @enderror"
                                        required>
                                    <option value="draft" {{ old('status', $project->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="planned" {{ old('status', $project->status) == 'planned' ? 'selected' : '' }}>Planned (In progress)</option>
                                    <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <div class="pe-error-message">
                                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="pe-field-group">
                                <label for="period" class="pe-label">
                                    <span>Estimated Duration</span>
                                    <span class="pe-label-hint">Operational window</span>
                                </label>
                                <div class="pe-input-icon-wrap">
                                    <i class="fa-regular fa-clock pe-input-icon"></i>
                                    <input type="text"
                                           id="period"
                                           name="period"
                                           class="pe-input"
                                           value="{{ old('period', $project->period) }}"
                                           placeholder="e.g. 5 Days">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: CLIENT & POINT OF CONTACT -->
                <div class="project-edit-card">
                    <div class="project-edit-card-header">
                        <div class="project-edit-card-icon project-edit-card-icon--teal">
                            <i class="fa-solid fa-building-user"></i>
                        </div>
                        <div class="project-edit-card-title">
                            <h2>Client Details</h2>
                            <p>Billing organization and primary contact coordinates</p>
                        </div>
                    </div>

                    <div class="project-edit-card-body">
                        <!-- Client Company -->
                        <div class="pe-field-group">
                            <label for="client_name" class="pe-label">
                                <span>Client Company</span>
                                <span class="pe-label-hint">For quotations & reports</span>
                            </label>
                            <div class="pe-input-icon-wrap">
                                <i class="fa-regular fa-building pe-input-icon"></i>
                                <input type="text"
                                       id="client_name"
                                       name="client_name"
                                       class="pe-input"
                                       value="{{ old('client_name', $project->client->company_name ?? '') }}"
                                       placeholder="Client Organization Name">
                            </div>
                        </div>

                        <!-- PIC Name & PIC Contact Grid -->
                        <div class="pe-field-row-2">
                            <div class="pe-field-group">
                                <label for="pic_name" class="pe-label">
                                    <span>Person In Charge</span>
                                </label>
                                <div class="pe-input-icon-wrap">
                                    <i class="fa-regular fa-user pe-input-icon"></i>
                                    <input type="text"
                                           id="pic_name"
                                           name="pic_name"
                                           class="pe-input"
                                           value="{{ old('pic_name', $project->pic_name) }}"
                                           placeholder="Contact Name">
                                </div>
                            </div>

                            <div class="pe-field-group">
                                <label for="pic_no" class="pe-label">
                                    <span>PIC Contact</span>
                                </label>
                                <div class="pe-input-icon-wrap">
                                    <i class="fa-solid fa-phone pe-input-icon"></i>
                                    <input type="text"
                                           id="pic_no"
                                           name="pic_no"
                                           class="pe-input"
                                           value="{{ old('pic_no', $project->pic_no) }}"
                                           placeholder="Phone or email">
                                </div>
                            </div>
                        </div>

                        <!-- Client Address -->
                        <div class="pe-field-group">
                            <label for="client_address" class="pe-label">
                                <span>Billing Address</span>
                                <span class="pe-label-hint">Official billing location</span>
                            </label>
                            <textarea id="client_address"
                                      name="client_address"
                                      class="pe-textarea"
                                      rows="3"
                                      placeholder="Full corporate address for commercial invoices and quotation headers...">{{ old('client_address', $project->client->client_address ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM FOOTER ACTION BAR -->
            <div class="project-edit-footer">
                <div class="project-edit-hint">
                    <i class="fa-solid fa-circle-info text-blue-500"></i>
                    <span>Updating project details will synchronize quotation headers and survey documents.</span>
                </div>
                <div class="project-edit-actions">
                    <a href="{{ route('projects.show', $project->project_Id) }}" class="project-edit-cancel-btn">
                        Cancel
                    </a>
                    <button type="submit" class="project-edit-submit-btn">
                        <i class="fa-solid fa-check"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>