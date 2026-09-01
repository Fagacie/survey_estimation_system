<x-app-layout containerClass="px-0">
    <style>
        .dashboard-wrapper {
            background-color: #f8fafc;
            min-height: calc(100vh - 64px);
        }

        /* Top Dark Section */
        .dashboard-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 3rem 1.5rem 5rem;
            position: relative;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-title {
            color: #ffffff;
            font-size: 1.85rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 0.25rem;
        }
        .page-subtitle {
            color: #94a3b8;
            font-size: 0.95rem;
        }

        .btn-premium {
            background: #3b82f6;
            color: #ffffff;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-premium:hover {
            background: #2563eb;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        /* Main Content Area */
        .content-area {
            padding: 0 1.5rem 5rem;
            margin-top: -3rem;
            position: relative;
            z-index: 10;
        }

        /* Table */
        .table-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
            border: 1px solid rgba(226, 232, 240, 0.8);
            overflow: hidden;
        }
        .table-header-controls {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .table-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .premium-table {
            width: 100%;
            border-collapse: collapse;
        }
        .premium-table th {
            padding: 1rem 1.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }
        .premium-table td {
            padding: 1.25rem 1.5rem;
            font-size: 0.9rem;
            color: #334155;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .premium-table tr:last-child td {
            border-bottom: none;
        }
        .premium-table tr:hover td {
            background: #fcfcfd;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 6px;
            color: #64748b;
            transition: all 0.2s;
            text-decoration: none;
            margin-left: 0.25rem;
            background: transparent;
            border: none;
        }
        .action-btn:hover {
            background: #f1f5f9;
            color: #0f172a;
        }
        .action-btn.delete:hover {
            background: #fef2f2;
            color: #dc2626;
        }
    </style>

    <div class="dashboard-wrapper">
        <div class="dashboard-hero">
            <div class="dashboard-container d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="page-title">Clients</h1>
                    <div class="page-subtitle">Manage client details and contacts</div>
                </div>
                <div class="mt-4 mt-md-0">
                    <button class="btn-premium" data-bs-toggle="modal" data-bs-target="#createClientModal">
                        <i class="fa-solid fa-plus"></i> Add Client
                    </button>
                </div>
            </div>
        </div>

        <div class="content-area dashboard-container">
            @if(session('success'))
                <div class="alert alert-success rounded-3 border-0 mb-4" style="background: #ecfdf5; color: #059669; box-shadow: 0 2px 4px rgba(0,0,0,0.05); padding: 1rem 1.5rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fa-solid fa-circle-check fs-5"></i>
                    {{ session('success') }}
                </div>
            @endif

            <div class="table-card">
                <div class="table-header-controls">
                    <h3 class="table-title">Client Directory</h3>
                </div>

                <div class="table-responsive">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>Name / Company</th>
                                <th>Contact Info</th>
                                <th>Projects</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clients as $client)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: #0f172a; margin-bottom: 2px;">{{ $client->name }}</div>
                                        <div style="font-size: 0.8rem; color: #64748b;">
                                            <i class="fa-regular fa-building me-1" style="color: #cbd5e1;"></i> {{ $client->company ?? 'No Company' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.85rem; color: #475569; margin-bottom: 2px;">
                                            <i class="fa-regular fa-envelope me-1" style="color: #cbd5e1;"></i> {{ $client->email ?? '-' }}
                                        </div>
                                        <div style="font-size: 0.85rem; color: #475569;">
                                            <i class="fa-solid fa-phone me-1" style="color: #cbd5e1;"></i> {{ $client->phone ?? '-' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span style="background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 99px; font-size: 0.8rem; font-weight: 600;">
                                            {{ $client->projects->count() }} Projects
                                        </span>
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <button type="button" class="action-btn" title="Edit Client">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Delete Client" onclick="return confirm('Are you sure?')">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="text-muted mb-2">No clients found.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Client Modal -->
    <div class="modal fade" id="createClientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: none; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
                <div class="modal-header" style="border-bottom: 1px solid #f1f5f9; padding: 1.5rem;">
                    <h5 class="modal-title fw-bold" style="color: #0f172a;">Add New Client</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('clients.store') }}" method="POST">
                    @csrf
                    <div class="modal-body" style="padding: 1.5rem;">
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.85rem; font-weight: 600; color: #475569;">Client Name *</label>
                            <input type="text" name="name" class="form-control" required style="border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" style="font-size: 0.85rem; font-weight: 600; color: #475569;">Company</label>
                            <input type="text" name="company" class="form-control" style="border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px;">
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label" style="font-size: 0.85rem; font-weight: 600; color: #475569;">Email</label>
                                <input type="email" name="email" class="form-control" style="border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px;">
                            </div>
                            <div class="col-6">
                                <label class="form-label" style="font-size: 0.85rem; font-weight: 600; color: #475569;">Phone</label>
                                <input type="text" name="phone" class="form-control" style="border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px;">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.85rem; font-weight: 600; color: #475569;">Address</label>
                            <textarea name="address" class="form-control" rows="2" style="border: 1px solid #e2e8f0; background: #f8fafc; border-radius: 8px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #f1f5f9; padding: 1.25rem 1.5rem;">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="font-weight: 600; color: #475569; border: 1px solid #e2e8f0; border-radius: 8px;">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="font-weight: 600; background: #3b82f6; border: none; border-radius: 8px;">Save Client</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
