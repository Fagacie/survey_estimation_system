<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">Quotation Workspace</x-slot>

    <!-- Custom inline styles to safely override any bootstrap conflicts and style tabs -->
    <style>
        .custom-nav-tabs { border-bottom: 1px solid #e2e8f0; display: flex; flex-wrap: nowrap; overflow-x: auto; -webkit-overflow-scrolling: touch; margin-bottom: 0; padding-left: 0; list-style: none; }
        .custom-nav-tabs .nav-link { 
            border: none; background: transparent; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #64748b; 
            border-bottom: 2px solid transparent; transition: all 0.2s ease; white-space: nowrap; 
        }
        .custom-nav-tabs .nav-link:hover { color: #0f172a; border-color: #cbd5e1; }
        .custom-nav-tabs .nav-link.active { color: #0d9488; border-color: #0d9488; }
        
        .sticky-bottom-summary {
            background: white; border-top: 1px solid #e2e8f0; padding: 1.5rem 2rem; border-radius: 0.75rem; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); margin-bottom: 1.5rem;
        }
        .bottom-bar {
            background: #0f172a; padding: 1rem 2rem; border-radius: 0.75rem; display: flex; justify-content: space-between; align-items: center; color: white;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); margin-bottom: 2rem; position: sticky; bottom: 1rem; z-index: 50;
        }
        /* Scoped bootstrap grid layout overrides to let Tailwind handle the grid cleanly */
        .project-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
    </style>

    <div class="max-w-7xl mx-auto">
        <div class="flex items-center gap-3 mb-8">
            <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 shadow-sm border border-teal-100">
                <i class="fa-solid fa-file-invoice-dollar text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Quotation Builder</h1>
        </div>

        <!-- ================================================================== -->
        <!-- 1. EDIT FORM CONTAINER (Visible in Edit Mode) -->
        <!-- ================================================================== -->
        <div id="editFormContainer">

            <!-- PROJECT INFORMATION -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-5 pb-3 border-b border-slate-100"><i class="fa-solid fa-folder-open text-slate-400 mr-2"></i> Project Details</h3>
                
                <div class="project-grid">
                    <input type="hidden" id="project_id" name="project_id" value="{{ $prefillProject?->project_Id ?? '' }}">
                    
                    <div>
                        <label for="project" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Project Name</label>
                        <input type="text" id="project" name="project" value="{{ $prefillProject?->name ?? '' }}" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors">
                    </div>

                    <div>
                        <label for="selectType" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Project Type</label>
                        <select id="selectType" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors bg-white">
                            <option value="">Choose Project Type</option>
                            <option value="1">CP - Coastal Project</option>
                            <option value="2">RP - River Project</option>
                            <option value="3">MP - Maritime Project</option>
                            <option value="4">GP - Geotech Project</option>
                            <option value="5">JP - Jetty Project</option>
                        </select>
                    </div>

                    <div class="col-span-full md:col-span-2 lg:col-span-1 grid grid-cols-2 gap-4">
                        <div>
                            <label for="project_start_date" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Start Date</label>
                            <input type="date" id="project_start_date" name="project_start_date" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors">
                        </div>
                        <div>
                            <label for="project_end_date" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">End Date</label>
                            <input type="date" id="project_end_date" name="project_end_date" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors">
                        </div>
                    </div>

                    <div>
                        <label for="period" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Period</label>
                        <input type="text" id="period" name="period" value="{{ $prefillProject?->period ?? '' }}" placeholder="Auto-calculated (if dates present)" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm bg-slate-50 focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors">
                    </div>

                    <div>
                        <label for="client" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Client</label>
                        <input type="text" id="client" name="client" value="{{ $prefillProject?->client?->company_name ?? '' }}" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors">
                    </div>

                    <div class="col-span-full md:col-span-2">
                        <label for="client_address" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Client Address</label>
                        <textarea id="client_address" name="client_address" rows="2" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors">{{ $prefillProject?->client?->client_address ?? '' }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Project No.</label>
                        <div class="flex gap-2">
                            <input type="text" id="number_prefix" class="flex-1 rounded-lg border-slate-300 py-2.5 px-3 text-sm bg-slate-100 text-slate-500 shadow-sm" readonly placeholder="EHS/--/--/">
                            <input type="text" id="number_running" class="w-24 rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors" placeholder="001" maxlength="4">
                        </div>
                        <input type="hidden" id="number" name="number" value="{{ $prefillProject?->number ?? '' }}">
                    </div>

                    <input type="hidden" id="quotation_no" name="quotation_no" value="">

                    <div>
                        <label for="pic" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">PIC</label>
                        <input type="text" id="pic" name="pic" value="{{ $prefillProject?->pic_name ?? '' }}" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors">
                    </div>

                    <div>
                        <label for="pic_no" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1.5">No. of PIC</label>
                        <input type="text" id="pic_no" name="pic_no" value="{{ $prefillProject?->pic_no ?? '' }}" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors">
                    </div>
                </div>
            </div>

            @if($estimation)
                <div class="bg-blue-50/50 rounded-xl shadow-sm border border-blue-100 p-5 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <div class="text-xs font-bold text-blue-800 uppercase tracking-wider mb-1">Survey estimation snapshot</div>
                        <div class="text-sm text-blue-600/80">Calculated from the saved survey areas for this project.</div>
                    </div>
                    <div class="flex gap-6 text-right">
                        <div>
                            <div class="text-xs text-blue-600/70 uppercase tracking-wider font-semibold mb-0.5">Distance</div>
                            <div class="text-lg font-bold text-blue-900">{{ number_format($estimation['distance_nm'], 4) }} NM</div>
                        </div>
                        <div>
                            <div class="text-xs text-blue-600/70 uppercase tracking-wider font-semibold mb-0.5">Survey hours</div>
                            <div class="text-lg font-bold text-blue-900">{{ number_format($estimation['survey_hours'], 2) }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-blue-600/70 uppercase tracking-wider font-semibold mb-0.5">Total duration</div>
                            <div class="text-lg font-bold text-blue-900">{{ number_format($estimation['total_days'], 2) }} days</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- MODULE TABS & SECTIONS -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <ul class="custom-nav-tabs bg-slate-50 border-b border-slate-200" id="moduleTabs" role="tablist">
                    @foreach($modules as $index => $module)
                    <li class="nav-item">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                id="tab-{{ $module->module_id }}"
                                data-bs-toggle="tab"
                                data-bs-target="#module-{{ $module->module_id }}"
                                type="button"
                                role="tab"
                                data-module-name="{{ strtoupper($module->module_name) }}">
                            <i class="fa-solid fa-folder-open mr-1 opacity-70"></i> {{ strtoupper($module->module_name) }}
                        </button>
                    </li>
                    @endforeach
                </ul>

                <div class="tab-content p-6" id="categoryTabsContent">
                    @foreach($modules as $index => $module)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                         id="module-{{ $module->module_id }}" 
                         role="tabpanel"
                         aria-labelledby="tab-{{ $module->module_id }}">
                        @foreach($module->categories as $category)
                            <!-- The legacy section-card depends on bootstrap grid, but we will inject it. We should make sure section-card works okay in tailwind -->
                            @include('dashboard.section-card', [
                                'target' => 'cat-' . $module->module_id . '-' . $category->category_id,
                                'title' => strtoupper($category->category_name ?? $category->name),
                                'isLast' => $loop->last 
                            ])
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- SUMMARY BREAKDOWN PANEL -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- PAYMENT TERMS & ADDITIONAL NOTES -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col h-full">
                    <div class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-4 pb-3 border-b border-slate-100 flex justify-between items-center">
                        <span>Payment Terms</span>
                    </div>

                    <div id="paymentTermsList" class="space-y-3 mb-4"></div>

                    <button type="button" id="addPaymentTermBtn" class="self-start text-sm font-semibold text-teal-600 hover:text-teal-700 bg-teal-50 hover:bg-teal-100 px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i> Add Payment Term
                    </button>

                    <input type="hidden" id="paymentTermsValue" name="payment_terms">

                    <!-- ADDITIONAL NOTES -->
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <label for="additional_notes" class="block text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Additional Notes</label>
                        <textarea id="additional_notes" name="additional_notes" rows="4" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors placeholder:text-slate-400" placeholder="Enter any special conditions or notes here..."></textarea>
                    </div>
                </div>

                <!-- SERVICE BREAKDOWN -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex flex-col h-full">
                    <div class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-4 pb-3 border-b border-slate-100 flex justify-between items-center">
                        <span>Service Breakdown</span>
                        <span>Total</span>
                    </div>

                    <div id="serviceBreakdownContainer" class="flex-1"></div>

                    <div class="flex justify-between items-center pt-4 border-t border-slate-200 mt-4">
                        <span class="text-sm font-bold text-slate-800 uppercase">Module Total (<span class="active-module-title text-teal-600">{{ strtoupper($modules->first()->module_name ?? 'MODULE') }}</span>)</span>
                        <span class="text-xl font-bold text-teal-600 current-module-total">MYR 0.00</span>
                    </div>
                </div>
            </div>

            <!-- STICKY BOTTOM SUMMARY BAR -->
            <div class="sticky-bottom-summary flex flex-wrap justify-between items-center gap-4">
                <div class="flex-1 overflow-x-auto pr-4">
                    <span class="text-sm font-bold text-teal-600 uppercase tracking-wider all-modules-list">ALL MODULES</span>
                </div>
                <div class="text-right whitespace-nowrap">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider mr-4">Grand Total</span>
                    <span class="text-2xl font-black text-slate-900 grand-project-total">MYR 0.00</span>
                </div>
            </div>

            <!-- EDIT MODE BOTTOM BAR -->
            <footer class="bottom-bar">
                <div class="flex items-center gap-4 text-sm">
                    <strong class="bg-slate-800 px-3 py-1 rounded text-slate-300 text-xs tracking-wider">DRAFT MODE</strong>
                    <span class="text-slate-400" id="lastSaved">Last autosaved at --:--:--</span>
                </div>

                <div class="flex gap-3">
                    <button type="button" class="preview-button px-5 py-2.5 rounded-lg text-sm font-bold border border-slate-600 hover:bg-slate-800 transition-colors" id="previewBtn">
                        <i class="fa-regular fa-eye mr-2"></i> PREVIEW
                    </button>

                    <button type="button" id="saveQuotationBtn" class="px-5 py-2.5 rounded-lg text-sm font-bold bg-teal-500 hover:bg-teal-400 text-white shadow-sm transition-colors border border-transparent">
                        <i class="fa-regular fa-floppy-disk mr-2"></i> SAVE / DOWNLOAD
                    </button>
                </div>
            </footer>

        </div> <!-- /#editFormContainer -->

        <!-- ================================================================== -->
        <!-- 2. PREVIEW MODE CONTAINER (Hidden by default, shown via home.js) -->
        <!-- ================================================================== -->
        <div id="previewModeContainer" class="d-none">

            <!-- Preview Action Bar -->
            <div class="flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-slate-200 mb-6 sticky top-4 z-50">
                <button type="button" id="backToEditBtn" class="px-4 py-2 rounded-lg text-sm font-bold border border-slate-300 hover:bg-slate-50 transition-colors text-slate-700">
                    <i class="fa-solid fa-arrow-left mr-2"></i> BACK TO EDIT
                </button>
            </div>

            <!-- Rendered Document View -->
            <div class="bg-white border border-slate-200 p-12 shadow-sm max-w-4xl mx-auto rounded-sm" id="quotationPreviewDocument" style="min-height: 800px;">
                <!-- JS dynamically builds the quotation table and project details here -->
                @include('dashboard.qtpreview')
            </div>

        </div> <!-- /#previewModeContainer -->

    </div>

    <!-- Quotation Save Success Modal -->
    <div id="quotationSaveModal" class="modal" tabindex="-1" style="display: none; background: rgba(15,23,42,0.6);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-xl border-0 shadow-lg overflow-hidden">
                <div class="modal-header border-0 pb-0 pt-5 px-6">
                    <h5 class="text-lg font-bold text-teal-600 flex items-center gap-3">
                        <i class="fa-solid fa-circle-check text-2xl"></i> Quotation Saved
                    </h5>
                </div>
                
                <div class="modal-body py-4 px-6 text-slate-600 text-sm leading-relaxed">
                    The quotation has been successfully saved to the database. Would you like to view and print/export it as a PDF now?
                </div>

                <div class="modal-footer border-0 pt-2 pb-5 px-6 flex gap-3">
                    <button type="button" id="cancelQuotationSaveBtn" class="flex-1 py-2.5 rounded-lg text-sm font-semibold border border-slate-300 text-slate-700 hover:bg-slate-50 transition-colors">
                        No, stay here
                    </button>
                    <button type="button" id="confirmQuotationSaveBtn" class="flex-1 py-2.5 rounded-lg text-sm font-bold bg-teal-600 text-white hover:bg-teal-500 shadow-sm transition-colors">
                        Yes, View PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- ITEM CARD TEMPLATE (REUSED BY JS) -->
    <div id="item-card-template" class="d-none">
        <div class="card quotation-item-card p-3 mb-3 bg-white border position-relative rounded" data-item-row="true">
            <!-- DELETE TRASH ICON -->
            <button type="button" class="btn btn-sm text-danger position-absolute top-0 end-0 m-2 remove-item-btn btn-delete-item" title="Delete Item">
                <i class="bi bi-trash-fill fs-6"></i>
            </button>

            <div class="row g-3">
                <!-- CATEGORY / SERVICE DROPDOWN -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-bold text-uppercase">SERVICE</label>
                    <select name="items[__INDEX__][service_id]" class="form-select form-select-sm item-service select-service">
                        <option value="">Select Service...</option>
                    </select>
                </div>

                <!-- ITEM DROPDOWN -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-bold text-uppercase">ITEM</label>
                    <select name="items[__INDEX__][item_id]" class="form-select form-select-sm item-name select-item">
                        <option value="">Select Item...</option>
                    </select>
                </div>

                <!-- DAILY RATE -->
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label fw-bold text-uppercase">DAILY RATE (MYR)</label>
                    <input type="number" step="0.01" name="items[__INDEX__][daily_rate]" class="form-control form-control-sm item-rate input-daily-rate" value="0.00">
                </div>

                <!-- UNIT QTY -->
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label fw-bold text-uppercase">UNIT QTY</label>
                    <input type="number" name="items[__INDEX__][unit_qty]" class="form-control form-control-sm item-qty input-unit-qty" value="1" min="1">
                </div>

                <!-- DAYS -->
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label fw-bold text-uppercase">DAYS</label>
                    <input type="number" name="items[__INDEX__][days]" class="form-control form-control-sm item-days input-days" value="1" min="1">
                </div>

                <!-- MARK-UP (%) -->
                <div class="col-6 col-md-3 col-lg-2">
                    <label class="form-label fw-bold text-uppercase">MARK-UP (%)</label>
                    <input type="number" step="0.01" name="items[__INDEX__][mark_up]" class="form-control form-control-sm item-markup input-markup" value="0">
                </div>

                <!-- INTERNAL RATE WITH UNIT DISPLAY -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-bold text-uppercase">INTERNAL RATE (MYR)</label>
                    <div class="input-group input-group-sm">
                        <input type="text" 
                               name="items[__INDEX__][internal_rate]" 
                               class="form-control form-control-sm input-internal-rate" 
                               placeholder="0.00" 
                               readonly>
                        <span class="input-group-text bg-light text-muted rate-unit-display d-none" id="rateUnitDisplay___INDEX__"></span>
                    </div>
                </div>
            </div>

            <!-- LINE ITEM FOOTER TOTAL -->
            <div class="item-card-footer d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                <span class="text-uppercase text-muted small fw-bold">LINE ITEM TOTAL</span>
                <span class="fw-bold text-dark fs-6 line-item-total line-total">MYR 0.00</span>
            </div>

            <!-- HIDDEN KEYS -->
            <input type="hidden" name="items[__INDEX__][module_id]" class="input-module-id">
            <input type="hidden" name="items[__INDEX__][category_id]" class="input-section-id">
        </div>
    </div>
        
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            window.adminModulesTree = @json($adminModulesTree ?? []);
            window.projectEstimation = @json($estimation ?? null);
        </script>
        <!-- Include flatpickr if necessary, though native dates usually suffice. The script was here. -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="{{ asset('js/home.js') }}?v={{ time() }}"></script>
    @endpush
</x-app-layout>