<x-app-layout containerClass="w-full px-4 sm:px-6 xl:px-10 py-8">
    <link rel="stylesheet" href="{{ asset('css/clients.css') }}?v={{ filemtime(public_path('css/clients.css')) }}">

    <div class="client-directory-page max-w-7xl mx-auto" x-data="{ search: '' }">
        
        <!-- BREADCRUMB -->
        <div class="mb-4">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Client Directory'],
            ]" />
        </div>

        <!-- 1. PAGE HEADER CARD -->
        <div class="client-header-card">
            <div class="flex flex-col md:flex-row justify-between md:items-center gap-6 relative z-10">
                <div>
                    <div class="client-eyebrow">
                        <span class="client-eyebrow-dot"></span>
                        CRM & RELATIONSHIPS
                    </div>
                    <div class="flex items-center gap-3">
                        <h1>Client Directory</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-sky-400/20 text-sky-200 border border-sky-400/30">
                            {{ $clients->count() }} Accounts
                        </span>
                    </div>
                    <p class="client-header-desc">
                        Manage marine survey enterprise clients, corporate partners, and project contacts across all operational sectors.
                    </p>
                </div>
                <div>
                    <button type="button" data-bs-toggle="modal" data-bs-target="#createClientModal" class="btn-client-primary">
                        <i class="fa-solid fa-plus font-light"></i> Add Client
                    </button>
                </div>
            </div>
        </div>

        <!-- NOTIFICATION ALERTS -->
        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-800 border border-emerald-200/80 rounded-xl p-4 mb-6 flex items-start gap-3 text-sm shadow-sm" role="status">
                <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600 text-base"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-rose-50 text-rose-800 border border-rose-200/80 rounded-xl p-4 mb-6 flex items-start gap-3 text-sm shadow-sm" role="alert">
                <i class="fa-solid fa-circle-exclamation mt-0.5 text-rose-600 text-base"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="bg-rose-50 text-rose-800 border border-rose-200/80 rounded-xl p-4 mb-6 text-sm shadow-sm" role="alert">
                <div class="font-semibold flex items-center gap-2 mb-1">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i> Please check the required fields:
                </div>
                <ul class="list-disc list-inside text-rose-700 pl-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 2. KPI SUMMARY RIBBON -->
        <div class="client-stat-grid">
            <div class="client-stat-card">
                <div class="client-stat-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="client-stat-value">{{ $clients->count() }}</div>
                    <div class="client-stat-label">Total Accounts</div>
                </div>
            </div>

            <div class="client-stat-card">
                <div class="client-stat-icon icon-indigo">
                    <i class="fa-solid fa-diagram-project"></i>
                </div>
                <div>
                    <div class="client-stat-value">{{ $clients->sum(fn($c) => $c->projects->count()) }}</div>
                    <div class="client-stat-label">Active Engagements</div>
                </div>
            </div>

            <div class="client-stat-card">
                <div class="client-stat-icon icon-emerald">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <div class="client-stat-value">{{ $clients->whereNotNull('company')->unique('company')->count() }}</div>
                    <div class="client-stat-label">Corporate Entities</div>
                </div>
            </div>
        </div>

        <!-- 3. CLIENTS TABLE WORKSPACE -->
        <div class="client-workspace-panel">
            
            <!-- Table Toolbar -->
            <div class="client-table-toolbar">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Enterprise Accounts</h3>
                    <span class="text-xs text-slate-400 font-medium">({{ $clients->count() }} records)</span>
                </div>
                <div class="client-search-wrapper">
                    <i class="fa-solid fa-magnifying-glass client-search-icon"></i>
                    <input type="text" 
                           x-model="search" 
                           placeholder="Filter clients or company..." 
                           class="client-search-input"
                           aria-label="Filter clients by name or company">
                </div>
            </div>

            <!-- Table Data -->
            <div class="overflow-x-auto">
                <table class="client-table">
                    <caption class="sr-only">Client directory with contact details, project counts, and available actions</caption>
                    <thead>
                        <tr>
                            <th scope="col">Client / Organization</th>
                            <th scope="col">Contact Details</th>
                            <th scope="col">Location / Address</th>
                            <th scope="col">Projects</th>
                            <th scope="col" class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr x-show="!search || '{{ strtolower(addslashes($client->name . ' ' . ($client->company ?? '') . ' ' . ($client->email ?? ''))) }}'.includes(search.toLowerCase())">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="client-avatar">
                                            {{ strtoupper(substr($client->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900">{{ $client->name }}</div>
                                            <div class="client-company-tag">
                                                <i class="fa-regular fa-building text-slate-400"></i>
                                                <span>{{ $client->company ?: 'Independent / Private' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex flex-col gap-1">
                                        <div class="text-xs font-medium text-slate-700 flex items-center gap-2">
                                            <i class="fa-regular fa-envelope text-slate-400 w-3.5"></i>
                                            @if($client->email)
                                                <a href="mailto:{{ $client->email }}" class="text-sky-700 hover:underline">{{ $client->email }}</a>
                                            @else
                                                <span class="text-slate-400">None</span>
                                            @endif
                                        </div>
                                        <div class="text-xs font-medium text-slate-700 flex items-center gap-2">
                                            <i class="fa-solid fa-phone text-slate-400 w-3.5"></i>
                                            <span>{{ $client->phone ?: 'None' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-xs text-slate-600 max-w-xs truncate flex items-center gap-1.5" title="{{ $client->address }}">
                                        <i class="fa-solid fa-location-dot text-slate-400 flex-shrink-0"></i>
                                        <span>{{ $client->address ?: 'No address specified' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @php $projectCount = $client->projects->count(); @endphp
                                    <span class="badge-project-pill {{ $projectCount > 0 ? 'active' : '' }}">
                                        <i class="fa-solid fa-folder-open text-[11px] opacity-75"></i>
                                        {{ $projectCount }} {{ Str::plural('Project', $projectCount) }}
                                    </span>
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if($projectCount > 0)
                                            <span class="inline-block" title="Cannot delete client with active project engagements">
                                                <button type="button" class="btn-action-icon" disabled>
                                                    <i class="fa-solid fa-lock text-slate-300"></i>
                                                </button>
                                            </span>
                                        @else
                                            <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="inline-block m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn-action-icon" 
                                                        title="Delete Client" 
                                                        onclick="return confirm('Are you sure you want to delete client {{ addslashes($client->name) }}?')">
                                                    <i class="fa-solid fa-trash-can font-light"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 bg-white">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                        <i class="fa-regular fa-folder-open text-xl"></i>
                                    </div>
                                    <div class="text-slate-700 font-semibold mb-1">No clients registered yet</div>
                                    <p class="text-slate-400 text-xs mb-4">Add your first corporate or independent client to begin linking projects.</p>
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#createClientModal" class="btn-client-primary !py-2 !px-3 text-xs">
                                        <i class="fa-solid fa-plus"></i> Add New Client
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- CREATE CLIENT MODAL -->
    <div class="modal fade client-modal" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="text-xs uppercase tracking-wider text-sky-400 font-bold mb-1">New Account</div>
                        <h5 class="modal-title" id="createClientModalLabel">Add Corporate Client</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('clients.store') }}" method="POST">
                    @csrf
                    <div class="modal-body space-y-4">
                        <div>
                            <label class="client-form-label">Client / Contact Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" class="client-form-input" placeholder="e.g. Captain James Vance" required>
                        </div>
                        <div>
                            <label class="client-form-label">Company / Organization</label>
                            <input type="text" name="company" class="client-form-input" placeholder="e.g. Petronas Marine Offshore Bhd">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="client-form-label">Work Email</label>
                                <input type="email" name="email" class="client-form-input" placeholder="james@marinecorp.com">
                            </div>
                            <div>
                                <label class="client-form-label">Contact Phone</label>
                                <input type="text" name="phone" class="client-form-input" placeholder="+60 12-345 6789">
                            </div>
                        </div>
                        <div>
                            <label class="client-form-label">Operating Address / Port HQ</label>
                            <textarea name="address" rows="2" class="client-form-input" placeholder="Terminal 3, Kemaman Supply Base..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer flex justify-end gap-2.5">
                        <button type="button" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 transition-colors" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn-client-primary !py-2 !px-4 text-sm font-semibold rounded-lg">
                            <i class="fa-solid fa-check"></i> Save Client
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
