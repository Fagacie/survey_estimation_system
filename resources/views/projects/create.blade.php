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
                
                <!-- SECTION 1: WORKFLOW TYPOLOGY -->
                <div>
                    <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="bg-teal-100 text-teal-700 w-6 h-6 rounded-full flex items-center justify-center text-xs">1</span> 
                        Workflow Category
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Survey Option -->
                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none border-slate-200 hover:border-teal-300 transition-all" id="label-survey">
                            <input type="radio" name="project_category" value="survey" class="sr-only" checked>
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-bold text-slate-900 flex items-center gap-2">
                                        <i class="fa-solid fa-water text-teal-600 w-4 text-center"></i> Field Survey
                                    </span>
                                    <span class="mt-1 flex items-center text-xs text-slate-500">Hydrographic or topographic data collection.</span>
                                </span>
                            </span>
                            <i class="fa-solid fa-circle-check text-teal-600 text-lg opacity-0 transition-opacity" id="icon-survey"></i>
                        </label>
                        
                        <!-- Modeling Option -->
                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none border-slate-200 hover:border-teal-300 transition-all" id="label-modeling">
                            <input type="radio" name="project_category" value="modeling" class="sr-only">
                            <span class="flex flex-1">
                                <span class="flex flex-col">
                                    <span class="block text-sm font-bold text-slate-900 flex items-center gap-2">
                                        <i class="fa-solid fa-cubes text-slate-600 w-4 text-center"></i> 3D Modeling
                                    </span>
                                    <span class="mt-1 flex items-center text-xs text-slate-500">Data processing and volume calculations.</span>
                                </span>
                            </span>
                            <i class="fa-solid fa-circle-check text-teal-600 text-lg opacity-0 transition-opacity" id="icon-modeling"></i>
                        </label>
                    </div>
                </div>

                <!-- SUBSECTION: SURVEY TYPE (Conditional) -->
                <div id="surveyTypeSection" class="bg-slate-50 -mx-6 md:-mx-8 px-6 md:px-8 py-5 border-y border-slate-100 transition-all duration-300">
                    <label class="block text-sm font-bold text-slate-900 mb-3">Select Survey Instrument</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        
                        <!-- SBES -->
                        <label class="cursor-pointer">
                            <input type="radio" name="survey_type" value="sbes" class="sr-only" checked>
                            <div id="instrument-sbes" class="instrument-card rounded-lg border border-teal-600 bg-teal-50 p-3 text-center transition-all ring-1 ring-teal-600 text-teal-800">
                                <i id="icon-sbes" class="fa-solid fa-anchor text-teal-600 mb-1"></i>
                                <div class="font-semibold text-sm">Single Beam (SBES)</div>
                            </div>
                        </label>

                        <!-- MBES -->
                        <label class="cursor-pointer">
                            <input type="radio" name="survey_type" value="mbes" class="sr-only">
                            <div id="instrument-mbes" class="instrument-card rounded-lg border border-slate-200 bg-white p-3 text-center transition-all hover:border-teal-300 text-slate-700">
                                <i id="icon-mbes" class="fa-solid fa-satellite-dish text-slate-400 mb-1"></i>
                                <div class="font-semibold text-sm">Multi-Beam (MBES)</div>
                            </div>
                        </label>

                        <!-- Drone -->
                        <label class="cursor-pointer">
                            <input type="radio" name="survey_type" value="drone" class="sr-only">
                            <div id="instrument-drone" class="instrument-card rounded-lg border border-slate-200 bg-white p-3 text-center transition-all hover:border-teal-300 text-slate-700">
                                <i id="icon-drone" class="fa-solid fa-plane-up text-slate-400 mb-1"></i>
                                <div class="font-semibold text-sm">Drone Mapping</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- SECTION 2: CORE DETAILS -->
                <div>
                    <h2 class="text-base font-bold text-slate-900 mb-4 flex items-center gap-2">
                        <span class="bg-teal-100 text-teal-700 w-6 h-6 rounded-full flex items-center justify-center text-xs">2</span> 
                        Project Configuration
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Side -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Project Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm @error('name') border-red-500 @enderror" value="{{ old('name') }}" placeholder="e.g. Nearshore Hydrographic Survey" required>
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                
            </div>
            
            <!-- FORM FOOTER -->
            <div class="bg-slate-50 p-6 md:px-8 border-t border-slate-200 flex justify-end">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-8 py-2.5 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center gap-2">
                    Create Project <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Script for interactive elements -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const categoryInputs = document.querySelectorAll('input[name="project_category"]');
            const surveySection = document.getElementById('surveyTypeSection');
            
            const lblSurvey = document.getElementById('label-survey');
            const lblModeling = document.getElementById('label-modeling');
            const iconSurvey = document.getElementById('icon-survey');
            const iconModeling = document.getElementById('icon-modeling');

            function updateUI() {
                const selected = document.querySelector('input[name="project_category"]:checked').value;
                
                // Reset styling
                lblSurvey.classList.remove('border-teal-600', 'ring-1', 'ring-teal-600', 'bg-teal-50/20');
                lblSurvey.classList.add('border-slate-200');
                iconSurvey.classList.add('opacity-0');
                
                lblModeling.classList.remove('border-teal-600', 'ring-1', 'ring-teal-600', 'bg-teal-50/20');
                lblModeling.classList.add('border-slate-200');
                iconModeling.classList.add('opacity-0');

                // Apply active styling
                if (selected === 'survey') {
                    lblSurvey.classList.remove('border-slate-200');
                    lblSurvey.classList.add('border-teal-600', 'ring-1', 'ring-teal-600', 'bg-teal-50/20');
                    iconSurvey.classList.remove('opacity-0');
                    
                    // Show survey type options
                    surveySection.style.display = 'block';
                    
                    // Check default survey type if none selected
                    if(!document.querySelector('input[name="survey_type"]:checked')) {
                        document.querySelector('input[name="survey_type"][value="sbes"]').checked = true;
                    }
                } else {
                    lblModeling.classList.remove('border-slate-200');
                    lblModeling.classList.add('border-teal-600', 'ring-1', 'ring-teal-600', 'bg-teal-50/20');
                    iconModeling.classList.remove('opacity-0');
                    
                    // Hide survey type options and uncheck them
                    surveySection.style.display = 'none';
                    document.querySelectorAll('input[name="survey_type"]').forEach(el => el.checked = false);
                }
            }

            // Attach change listeners to the inputs
            categoryInputs.forEach(input => {
                input.addEventListener('change', updateUI);
            });

            // -----------------------------------------------------
            // Instrument Logic
            // -----------------------------------------------------
            const instrumentInputs = document.querySelectorAll('input[name="survey_type"]');
            
            function updateInstruments() {
                const selected = document.querySelector('input[name="survey_type"]:checked');
                if(!selected) return;
                
                const val = selected.value;

                // Reset all instruments
                document.querySelectorAll('.instrument-card').forEach(card => {
                    card.className = "instrument-card rounded-lg border border-slate-200 bg-white p-3 text-center transition-all hover:border-teal-300 text-slate-700";
                });
                // Reset all icons
                document.querySelectorAll('.instrument-card i').forEach(icon => {
                    icon.classList.remove('text-teal-600');
                    icon.classList.add('text-slate-400');
                });

                // Apply active state
                const activeCard = document.getElementById(`instrument-${val}`);
                const activeIcon = document.getElementById(`icon-${val}`);
                
                if (activeCard && activeIcon) {
                    activeCard.className = "instrument-card rounded-lg border border-teal-600 bg-teal-50 p-3 text-center transition-all ring-1 ring-teal-600 text-teal-800";
                    activeIcon.classList.remove('text-slate-400');
                    activeIcon.classList.add('text-teal-600');
                }
            }

            instrumentInputs.forEach(input => {
                input.addEventListener('change', updateInstruments);
            });

            // Initialize on load
            updateUI();
            updateInstruments();
        });
    </script>
</x-app-layout>
