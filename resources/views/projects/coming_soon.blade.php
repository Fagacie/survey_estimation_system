<x-app-layout containerClass="w-full px-8 py-12">
    <div class="max-w-3xl mx-auto text-center mt-12">
        <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-100 mb-6">
            <i class="fa-solid fa-person-digging text-4xl text-slate-400"></i>
        </div>
        
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-4">Workflow Under Construction</h1>
        
        <p class="text-lg text-slate-500 mb-8 max-w-xl mx-auto">
            You've successfully created the project! However, the specific workflow for this project type (Modeling or advanced Survey) is currently being built. 
        </p>

        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-8 rounded-lg inline-flex items-center gap-3 text-sm mx-auto">
                <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
            <br>
        @endif

        <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg text-sm font-semibold transition-colors shadow-sm">
            <i class="fa-solid fa-arrow-left"></i> Return to Dashboard
        </a>
    </div>
</x-app-layout>
