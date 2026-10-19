<x-app-layout containerClass="w-full px-4 py-6 sm:px-8 sm:py-8">
    <link rel="stylesheet" href="{{ asset('css/create-project.css') }}?v={{ filemtime(public_path('css/create-project.css')) }}">

    <div class="create-project-page">
        <!-- BREADCRUMB -->
        <nav class="create-project-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('projects.index') }}">
                <i class="fa-solid fa-folder-tree"></i> Projects
            </a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current">New Project</span>
        </nav>

        <!-- PAGE HEADER -->
        <div class="create-project-header">
            <div class="create-project-header-info">
                <h1>Create Survey Project</h1>
                <p>Define operational scope, survey type, and client coordinates to initialize the project workspace.</p>
            </div>
            <div class="create-project-header-actions">
                <a href="{{ route('projects.index') }}" class="create-project-back-btn">
                    <i class="fa-solid fa-arrow-left"></i> Project Register
                </a>
            </div>
        </div>

        <!-- FORM WRAPPER -->
        <form action="{{ route('projects.store') }}" method="POST" id="createProjectForm" class="create-project-form">
            @csrf

            <div class="create-project-grid">
                <!-- CARD 1: SCOPE & OPERATIONAL PARAMETERS -->
                <div class="create-project-card">
                    <div class="create-project-card-header">
                        <div class="create-project-card-icon">
                            <i class="fa-solid fa-compass-drafting"></i>
                        </div>
                        <div class="create-project-card-title">
                            <h2>Project Scope</h2>
                            <p>Operational categorization and timeline parameters</p>
                        </div>
                    </div>

                    <div class="create-project-card-body">
                        <!-- Project Name -->
                        <div class="cp-field-group">
                            <label for="project_name" class="cp-label">
                                <span>Project Name <span class="cp-label-required">*</span></span>
                            </label>
                            <input type="text"
                                   id="project_name"
                                   name="name"
                                   class="cp-input @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Terengganu Coastal Bathymetric Survey"
                                   required>
                            @error('name')
                                <div class="cp-error-message">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Project Type & Location Grid -->
                        <div class="cp-field-row-2">
                            <div class="cp-field-group">
                                <label for="project_type" class="cp-label">
                                    <span>Project Type <span class="cp-label-required">*</span></span>
                                </label>
                                <select id="project_type"
                                        name="project_type"
                                        class="cp-select @error('project_type') is-invalid @enderror"
                                        required>
                                    <option value="" disabled {{ old('project_type') ? '' : 'selected' }}>Choose project type</option>
                                    @foreach(\App\Models\Project::TYPES as $code => $label)
                                        <option value="{{ $code }}" {{ old('project_type') == $code ? 'selected' : '' }}>
                                            {{ $code }} · {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('project_type')
                                    <div class="cp-error-message">
                                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="cp-field-group">
                                <label for="location" class="cp-label">
                                    <span>Location <span class="cp-label-required">*</span></span>
                                </label>
                                <select id="location"
                                        name="location"
                                        class="cp-select @error('location') is-invalid @enderror"
                                        required>
                                    <option value="" disabled {{ old('location') ? '' : 'selected' }}>Choose location</option>
                                    @foreach(\App\Models\Project::LOCATIONS as $loc)
                                        <option value="{{ $loc }}" {{ old('location') == $loc ? 'selected' : '' }}>
                                            {{ $loc }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('location')
                                    <div class="cp-error-message">
                                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status & Duration Grid -->
                        <div class="cp-field-row-2">
                            <div class="cp-field-group">
                                <label for="status" class="cp-label">
                                    <span>Initial Status</span>
                                </label>
                                <select id="status"
                                        name="status"
                                        class="cp-select @error('status') is-invalid @enderror"
                                        required>
                                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft (New intake)</option>
                                    <option value="planned" {{ old('status') == 'planned' ? 'selected' : '' }}>Planned (In progress)</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status')
                                    <div class="cp-error-message">
                                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="cp-field-group">
                                <label for="period" class="cp-label">
                                    <span>Estimated Duration</span>
                                    <span class="cp-label-hint">Operational window</span>
                                </label>
                                <div class="cp-input-icon-wrap">
                                    <i class="fa-regular fa-clock cp-input-icon"></i>
                                    <input type="text"
                                           id="period"
                                           name="period"
                                           class="cp-input"
                                           value="{{ old('period') }}"
                                           placeholder="e.g. 5 Days or 2 Weeks">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: CLIENT & POINT OF CONTACT -->
                <div class="create-project-card">
                    <div class="create-project-card-header">
                        <div class="create-project-card-icon create-project-card-icon--teal">
                            <i class="fa-solid fa-building-user"></i>
                        </div>
                        <div class="create-project-card-title">
                            <h2>Client Details</h2>
                            <p>Billing organization and primary contact coordinates</p>
                        </div>
                    </div>

                    <div class="create-project-card-body">
                        <!-- Client Company -->
                        <div class="cp-field-group">
                            <label for="client_name" class="cp-label">
                                <span>Client Company</span>
                                <span class="cp-label-hint">For quotations & reports</span>
                            </label>
                            <div class="cp-input-icon-wrap">
                                <i class="fa-regular fa-building cp-input-icon"></i>
                                <input type="text"
                                       id="client_name"
                                       name="client_name"
                                       class="cp-input"
                                       value="{{ old('client_name') }}"
                                       placeholder="e.g. PETRONAS Carigali Sdn Bhd">
                            </div>
                        </div>

                        <!-- PIC Name & PIC Contact Grid -->
                        <div class="cp-field-row-2">
                            <div class="cp-field-group">
                                <label for="pic_name" class="cp-label">
                                    <span>Person In Charge</span>
                                </label>
                                <div class="cp-input-icon-wrap">
                                    <i class="fa-regular fa-user cp-input-icon"></i>
                                    <input type="text"
                                           id="pic_name"
                                           name="pic_name"
                                           class="cp-input"
                                           value="{{ old('pic_name') }}"
                                           placeholder="e.g. Ts. Ahmad Razak">
                                </div>
                            </div>

                            <div class="cp-field-group">
                                <label for="pic_no" class="cp-label">
                                    <span>PIC Contact</span>
                                </label>
                                <div class="cp-input-icon-wrap">
                                    <i class="fa-solid fa-phone cp-input-icon"></i>
                                    <input type="text"
                                           id="pic_no"
                                           name="pic_no"
                                           class="cp-input"
                                           value="{{ old('pic_no') }}"
                                           placeholder="Phone or email">
                                </div>
                            </div>
                        </div>

                        <!-- Client Address -->
                        <div class="cp-field-group">
                            <label for="client_address" class="cp-label">
                                <span>Billing Address</span>
                                <span class="cp-label-hint">Official billing location</span>
                            </label>
                            <textarea id="client_address"
                                      name="client_address"
                                      class="cp-textarea"
                                      rows="3"
                                      placeholder="Full corporate address for commercial invoices and quotation headers...">{{ old('client_address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FORM FOOTER ACTION BAR -->
            <div class="create-project-footer">
                <div class="create-project-hint">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>Survey boundaries and runline planning can be configured immediately after workspace initialization.</span>
                </div>
                <div class="create-project-actions">
                    <a href="{{ route('projects.index') }}" class="create-project-cancel-btn">
                        Cancel
                    </a>
                    <button type="submit" class="create-project-submit-btn">
                        <span>Initialize Workspace</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>