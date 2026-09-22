<div class="card border-0 shadow-sm mb-3 section-block" 
     data-section-id="{{ $category->category_id ?? $category->id }}" 
     data-module-id="{{ $module->module_id ?? $module->id }}">
    <!-- SECTION HEADER BAR -->
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2 px-3 border-0">
        <button class="btn p-0 text-start fw-bold text-uppercase text-dark custom-dropdown-btn d-flex align-items-center gap-2" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#collapse-{{ $target }}" 
                aria-expanded="true">
            <i class="bi bi-chevron-down toggle-icon text-primary"></i>
            <span>{{ $title }}</span>
        </button>

        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small fw-bold">Subtotal: <strong class="text-dark">MYR <span class="section-subtotal">0.00</span></strong></span>
            <button type="button" 
                    class="btn btn-sm btn-outline-primary fw-bold text-uppercase add-item-btn"
                    data-module-id="{{ $module->module_id ?? $module->id }}" 
                    data-section-id="{{ $category->category_id ?? $category->id }}">
                <i class="bi bi-plus-lg me-1"></i> ADD ITEM
            </button>
        </div>
    </div>

    <!-- COLLAPSIBLE SECTION BODY -->
    <!-- Collapsible Content -->
    <div id="collapse-{{ $target }}" class="collapse show">
        <div class="card-body p-3 section-items-container items-container item-list" id="container-{{ $module->module_id ?? $module->id }}-{{ $category->category_id ?? $category->id }}">
            {{-- Dynamically inserted quotation-item-card elements will load here --}}
        </div>
    </div>
</div>
