<x-app-layout>
    <x-slot name="header">
        {{ __('Cost Estimation Settings') }}
    </x-slot>

    <style>
        .settings-card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; }
        .settings-table th { background: #f8fafc; color: #64748b; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 16px; font-weight: 700; border-bottom: 1px solid #e2e8f0; border-top: none; }
        .settings-table td { padding: 12px 16px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .nav-pills .nav-link { border-radius: 30px; font-weight: 600; padding: 8px 20px; color: #64748b; margin-right: 10px; }
        .nav-pills .nav-link.active { background-color: #0f172a; color: #fff; }
        .badge-category { font-size: 0.7rem; font-weight: 600; padding: 4px 8px; border-radius: 6px; }
        .badge-equip { background-color: #e0f2fe; color: #0284c7; }
        .badge-pers { background-color: #fef3c7; color: #d97706; }
        .badge-log { background-color: #f3e8ff; color: #9333ea; }
        .badge-misc { background-color: #f1f5f9; color: #475569; }
        .badge-analysis { background-color: #dcfce7; color: #16a34a; }
    </style>

    <div class="container-fluid px-4 py-4" style="max-width: 1400px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1 text-slate-800">Master Cost Rates</h3>
                <p class="text-muted mb-0">Manage default cost items for different survey types.</p>
            </div>
            <button class="btn btn-primary rounded-pill fw-bold px-4" data-bs-toggle="modal" data-bs-target="#addRateModal">
                <i class="fa-solid fa-plus me-2"></i> Add New Rate
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success shadow-sm rounded-3 border-0 mb-4 fw-bold">
                <i class="fa-solid fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="settings-card mb-4 p-4">
            <h5 class="fw-bold text-slate-800 mb-4"><i class="fa-solid fa-list me-2"></i> SBES Master Rates</h5>

            <table class="table settings-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 15%">Category</th>
                        <th style="width: 35%">Item Name</th>
                        <th style="width: 15%">Unit Type</th>
                        <th style="width: 20%">Default Rate (RM)</th>
                        <th style="width: 15%" class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rates as $rate)
                        <tr>
                            <td>
                                @php
                                    $badgeClass = 'badge-misc';
                                    if($rate->category == 'Equipment') $badgeClass = 'badge-equip';
                                    elseif($rate->category == 'Personnel') $badgeClass = 'badge-pers';
                                    elseif($rate->category == 'Logistics') $badgeClass = 'badge-log';
                                    elseif($rate->category == 'Analysis') $badgeClass = 'badge-analysis';
                                @endphp
                                <span class="badge-category {{ $badgeClass }}">{{ $rate->category }}</span>
                            </td>
                            <td class="fw-bold text-slate-800">{{ $rate->name }}</td>
                            <td>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-2 shadow-sm">{{ $rate->unit_type }}</span>
                            </td>
                            <td class="fw-bold text-slate-800">
                                {{ number_format($rate->default_rate, 2) }}
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-light text-primary rounded-circle me-1" style="width:32px;height:32px;padding:0;" data-bs-toggle="modal" data-bs-target="#editRateModal{{ $rate->id }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <form action="{{ route('settings.costs.destroy', $rate->id) }}" method="POST" class="d-inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-light text-danger rounded-circle btn-delete-action" style="width:32px;height:32px;padding:0;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>

                                <!-- Edit Rate Modal -->
                                <div class="modal fade text-start" id="editRateModal{{ $rate->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                                            <form action="{{ route('settings.costs.update', $rate->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold">Edit Cost Rate</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body pb-0">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label class="form-label fw-bold text-muted small">Category</label>
                                                            <select name="category" class="form-select rounded-3" required>
                                                                <option value="Equipment" {{ $rate->category == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                                                                <option value="Personnel" {{ $rate->category == 'Personnel' ? 'selected' : '' }}>Personnel</option>
                                                                <option value="Logistics" {{ $rate->category == 'Logistics' ? 'selected' : '' }}>Logistics</option>
                                                                <option value="Analysis" {{ $rate->category == 'Analysis' ? 'selected' : '' }}>Analysis & Reporting</option>
                                                                <option value="Miscellaneous" {{ $rate->category == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label fw-bold text-muted small">Item Name</label>
                                                            <input type="text" name="name" class="form-control rounded-3" required value="{{ $rate->name }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-bold text-muted small">Unit Type</label>
                                                            <select name="unit_type" class="form-select rounded-3" required>
                                                                <option value="Per Day" {{ $rate->unit_type == 'Per Day' ? 'selected' : '' }}>Per Day</option>
                                                                <option value="Lump Sum" {{ $rate->unit_type == 'Lump Sum' ? 'selected' : '' }}>Lump Sum</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-bold text-muted small">Default Rate (RM)</label>
                                                            <input type="number" step="0.01" name="default_rate" class="form-control rounded-3" required value="{{ $rate->default_rate }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-top-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Update Rate</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open fs-2 mb-3 d-block text-slate-300"></i>
                                No rates configured for SBES.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Rate Modal -->
    <div class="modal fade" id="addRateModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <form action="{{ route('settings.costs.store') }}" method="POST">
                    @csrf
                    <div class="modal-header border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold">Add Cost Rate</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body pb-0">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">Category</label>
                                <select name="category" class="form-select rounded-3" required>
                                    <option value="Equipment">Equipment</option>
                                    <option value="Personnel">Personnel</option>
                                    <option value="Logistics">Logistics</option>
                                    <option value="Analysis">Analysis & Reporting</option>
                                    <option value="Miscellaneous">Miscellaneous</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">Item Name</label>
                                <input type="text" name="name" class="form-control rounded-3" required placeholder="e.g. Survey Boat">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">Unit Type</label>
                                <select name="unit_type" class="form-select rounded-3" required>
                                    <option value="Per Day">Per Day</option>
                                    <option value="Lump Sum">Lump Sum</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Default Rate (RM)</label>
                                <input type="number" step="0.01" name="default_rate" class="form-control rounded-3" required placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Rate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.btn-delete-action');
            deleteButtons.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const form = this.closest('form');
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</x-app-layout>
