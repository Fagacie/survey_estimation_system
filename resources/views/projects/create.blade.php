<x-app-layout containerClass="w-full px-8 py-8">
    
    <!-- HEADER -->
    <div class="mb-8 border-b border-slate-200 pb-5 flex flex-col md:flex-row justify-between md:items-end gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Create New Project</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Set up project configuration and select the operational workflow.</p>
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
            
            <div class="p-6 md:p-8 space-y-8">

                <!-- SECTION 1: PROJECT CONFIGURATION -->
                <div>
                    <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="bg-teal-100 text-teal-700 w-6 h-6 rounded-full flex items-center justify-center text-xs">1</span> 
                        Project Configuration
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Side -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Project Type <span class="text-red-500">*</span></label>
                                <select name="project_type" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm @error('project_type') border-red-500 @enderror" required>
                                    <option value="" disabled {{ old('project_type') ? '' : 'selected' }}>Choose project type</option>
                                    @foreach(\App\Models\Project::TYPES as $code => $label)
                                        <option value="{{ $code }}" {{ old('project_type') == $code ? 'selected' : '' }}>{{ $code }} - {{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('project_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Project Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm @error('name') border-red-500 @enderror" value="{{ old('name') }}" placeholder="e.g. Nearshore Hydrographic Survey" required>
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Location <span class="text-red-500">*</span></label>
                                <select name="location" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm @error('location') border-red-500 @enderror" required>
                                    <option value="" disabled {{ old('location') ? '' : 'selected' }}>Choose location</option>
                                    @foreach(\App\Models\Project::LOCATIONS as $loc)
                                        <option value="{{ $loc }}" {{ old('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                                    @endforeach
                                </select>
                                @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Project Status</label>
                                <select name="status" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm @error('status') border-red-500 @enderror" required>
                                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="planned" {{ old('status') == 'planned' ? 'selected' : '' }}>Planned (In Progress)</option>
                                    <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Estimated Duration</label>
                                <input type="text" name="period" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm" value="{{ old('period') }}" placeholder="e.g. 5 Days">
                            </div>
                        </div>

                        <!-- Right Side -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Client Company</label>
                                <input type="text" name="client_name" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm" value="{{ old('client_name') }}" placeholder="Client Organization Name">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">PIC Name</label>
                                    <input type="text" name="pic_name" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm" value="{{ old('pic_name') }}" placeholder="Contact Name">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">PIC Contact</label>
                                    <input type="text" name="pic_no" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm" value="{{ old('pic_no') }}" placeholder="Phone / Email">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Client Address</label>
                                <textarea name="client_address" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm" rows="2" placeholder="Full address for quotations">{{ old('client_address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- SECTION 2: DESCRIPTION (Optional) -->
                <div>
                    <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="bg-teal-100 text-teal-700 w-6 h-6 rounded-full flex items-center justify-center text-xs">2</span> 
                        Additional Details
                    </h2>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Description</label>
                        <textarea name="description" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm" rows="3" placeholder="Brief project description (optional)">{{ old('description') }}</textarea>
                    </div>
                </div>
                
            </div>
            
            <!-- FORM FOOTER -->
            <div class="bg-slate-50 p-6 md:px-8 border-t border-slate-200 flex justify-end">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-8 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center gap-2">
                    Create Project <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>

</x-app-layout>