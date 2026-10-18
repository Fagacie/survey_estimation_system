<x-app-layout containerClass="w-full px-8 py-8">
    <link rel="stylesheet" href="{{ asset('css/create-project.css') }}?v={{ filemtime(public_path('css/create-project.css')) }}">
    <div class="create-project-page">
    
    <!-- HEADER -->
    <div class="mb-8 border-b border-slate-200 pb-5 flex flex-col md:flex-row justify-between md:items-end gap-4">
        <div>
            <p class="create-project-eyebrow">Project intake / setup</p>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Create New Project</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Capture the project context before moving into survey planning and estimation.</p>
        </div>
        <div>
            <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-600 px-4 py-2 rounded-lg text-sm font-semibold border border-slate-200 transition-colors shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- FORM CONTAINER -->
    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('projects.store') }}" method="POST" id="createProjectForm">
            @csrf
            
            <div class="p-6 md:p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Left Side: Project details -->
                    <div class="space-y-4">
                        <div>
                            <label for="project_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Project Name <span class="text-red-500">*</span></label>
                            <input type="text" id="project_name" name="name" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm @error('name') border-red-500 @enderror" value="{{ old('name') }}" placeholder="e.g. Nearshore Hydrographic Survey" required>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="project_type" class="block text-sm font-semibold text-slate-700 mb-1.5">Project Type <span class="text-red-500">*</span></label>
                            <select id="project_type" name="project_type" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm @error('project_type') border-red-500 @enderror" required>
                                <option value="" disabled {{ old('project_type') ? '' : 'selected' }}>Choose project type</option>
                                @foreach(\App\Models\Project::TYPES as $code => $label)
                                    <option value="{{ $code }}" {{ old('project_type') == $code ? 'selected' : '' }}>{{ $code }} - {{ $label }}</option>
                                @endforeach
                            </select>
                            @error('project_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="location" class="block text-sm font-semibold text-slate-700 mb-1.5">Location <span class="text-red-500">*</span></label>
                            <select id="location" name="location" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm @error('location') border-red-500 @enderror" required>
                                <option value="" disabled {{ old('location') ? '' : 'selected' }}>Choose location</option>
                                @foreach(\App\Models\Project::LOCATIONS as $loc)
                                    <option value="{{ $loc }}" {{ old('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                @endforeach
                            </select>
                            @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-semibold text-slate-700 mb-1.5">Project Status</label>
                            <select id="status" name="status" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm @error('status') border-red-500 @enderror" required>
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="planned" {{ old('status') == 'planned' ? 'selected' : '' }}>Planned (In Progress)</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="period" class="block text-sm font-semibold text-slate-700 mb-1.5">Estimated Duration</label>
                            <input type="text" id="period" name="period" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm" value="{{ old('period') }}" placeholder="e.g. 5 Days">
                        </div>
                    </div>

                    <!-- Right Side: Details of the client -->
                    <div class="space-y-4">
                        <div>
                            <label for="client_name" class="block text-sm font-semibold text-slate-700 mb-1.5">Client Company</label>
                            <input type="text" id="client_name" name="client_name" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm" value="{{ old('client_name') }}" placeholder="Client Organization Name">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="pic_name" class="block text-sm font-semibold text-slate-700 mb-1.5">PIC Name</label>
                                <input type="text" id="pic_name" name="pic_name" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm" value="{{ old('pic_name') }}" placeholder="Contact Name">
                            </div>
                            <div>
                                <label for="pic_no" class="block text-sm font-semibold text-slate-700 mb-1.5">PIC Contact</label>
                                <input type="text" id="pic_no" name="pic_no" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm" value="{{ old('pic_no') }}" placeholder="Phone / Email">
                            </div>
                        </div>

                        <div>
                            <label for="client_address" class="block text-sm font-semibold text-slate-700 mb-1.5">Client Address</label>
                            <textarea id="client_address" name="client_address" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 shadow-sm" rows="3" placeholder="Full address for quotations">{{ old('client_address') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- FORM FOOTER -->
            <div class="bg-slate-50 p-6 md:px-8 border-t border-slate-200 flex justify-end">
                <button type="submit" class="bg-sky-700 hover:bg-sky-800 text-white px-8 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center gap-2">
                    Create Project <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>

</div>
</x-app-layout>