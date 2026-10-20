<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">{{ isset($item) ? 'Edit Item' : 'Create Item' }}</x-slot>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/newItem.css') }}?v={{ filemtime(public_path('css/newItem.css')) }}">
    @endpush

    <div class="item-editor-page pb-12">
        <!-- Breadcrumb Navigation -->
        <nav class="item-breadcrumb" aria-label="Breadcrumb">
            <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
            <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
            <a href="{{ route('admin.index') }}">Item Catalog</a>
            <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-right"></i></span>
            <span class="breadcrumb-current">{{ isset($item) ? 'Edit Item' : 'Create Item' }}</span>
        </nav>

        <!-- Header -->
        <header class="item-header">
            <div class="item-header-title-wrap">
                <h1>{{ isset($item) ? 'Edit Catalog Item' : 'Create New Item' }}</h1>
                <p>{{ isset($item) ? 'Modify specification, operational classification, or internal cost rates for this item.' : 'Add a new reference survey item to project quotation modules and cost estimates.' }}</p>
            </div>
            <div class="item-header-actions">
                <a href="{{ route('admin.index') }}" class="item-btn item-btn--secondary">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Catalog</span>
                </a>
            </div>
        </header>

        <!-- Form Card Container -->
        <div class="item-card">
            <!-- Validation Errors -->
            @if (isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: var(--item-radius-sm); border: 1px solid #fecaca; background: #fef2f2; color: #991b1b;">
                    <div class="d-flex align-items-center mb-1 font-bold">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        <span>Please correct the errors below:</span>
                    </div>
                    <ul class="mb-0 ps-4 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="itemForm" action="{{ isset($item) ? route('items.update', ['id' => $item->item_id]) : route('items.store') }}" method="POST">
                @csrf
                @if(isset($item))
                    @method('PUT')
                @endif
                <input type="hidden" id="itemId" name="item_id" value="{{ $item->item_id ?? '' }}">

                <div class="item-grid">
                    <!-- Column 1: Classification & Hierarchy -->
                    <div>
                        <h2 class="item-section-title">
                            <i class="fa-solid fa-sitemap"></i>
                            Classification & Hierarchy
                        </h2>

                        <!-- 1. MODULE SELECT -->
                        <div class="item-field-group">
                            <label for="chooseModule" class="item-label">
                                Module Discipline
                                <span class="required-star">*</span>
                            </label>
                            <select id="chooseModule" name="module_id" class="item-select" required>
                                <option value="">-- Select Module --</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->module_id }}" {{ old('module_id', isset($item) ? ($item->module_id ?? $item->category?->module_id ?? '') : '') == $module->module_id ? 'selected' : '' }}>
                                        {{ strtoupper($module->module_name) }}
                                    </option>
                                @endforeach
                                <option value="NEW_MODULE" style="font-weight: 700; color: #1d518a;">+ Add New Module</option>
                            </select>

                            <!-- Hidden New Module Input -->
                            <div id="newModuleContainer" class="item-dynamic-field d-none" style="display: none;">
                                <label for="createModule" class="item-dynamic-label">
                                    <i class="fa-solid fa-plus-circle"></i>
                                    New Module Name
                                </label>
                                <input type="text" id="createModule" name="new_module_name" placeholder="Type new discipline or module name..." class="item-input">
                            </div>
                        </div>

                        <!-- 2. CATEGORY SELECT -->
                        <div class="item-field-group">
                            <label for="chooseCategory" class="item-label">
                                Item Category
                            </label>
                            <select id="chooseCategory" name="category_id" class="item-select">
                                <option value="">-- Select Category First --</option>
                                <option value="NEW_CATEGORY" style="font-weight: 700; color: #1d518a;">+ Add New Category</option>
                            </select>

                            <!-- Hidden New Category Input -->
                            <div id="newCategoryContainer" class="item-dynamic-field d-none" style="display: none;">
                                <label for="createCategory" class="item-dynamic-label">
                                    <i class="fa-solid fa-plus-circle"></i>
                                    New Category Name
                                </label>
                                <input type="text" id="createCategory" name="new_category_name" placeholder="Type new category name..." class="item-input">
                            </div>
                        </div>

                        <!-- 3. SERVICE SELECT -->
                        <div class="item-field-group">
                            <label for="chooseService" class="item-label">
                                Operational Service
                            </label>
                            <select id="chooseService" name="service_id" class="item-select">
                                <option value="">-- Select Service First --</option>
                                <option value="NEW_SERVICE" style="font-weight: 700; color: #1d518a;">+ Add New Service</option>
                            </select>

                            <!-- Hidden New Service Input -->
                            <div id="newServiceContainer" class="item-dynamic-field d-none" style="display: none;">
                                <label for="createService" class="item-dynamic-label">
                                    <i class="fa-solid fa-plus-circle"></i>
                                    New Service Name
                                </label>
                                <input type="text" id="createService" name="new_service_name" placeholder="Type new service name..." class="item-input">
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Specification & Pricing -->
                    <div>
                        <h2 class="item-section-title">
                            <i class="fa-solid fa-tags"></i>
                            Item Details & Pricing
                        </h2>

                        <!-- 4. ITEM NAME -->
                        <div class="item-field-group">
                            <label for="itemName" class="item-label">
                                Item Description / Title
                                <span class="required-star">*</span>
                            </label>
                            <input type="text"
                                   id="itemName"
                                   name="name"
                                   value="{{ old('name', isset($item) ? ($item->item_name ?? '') : '') }}"
                                   required
                                   class="item-input"
                                   placeholder="e.g. Environmental Engineer, Side Scan Sonar">
                        </div>

                        <!-- 5. INTERNAL RATE (MYR) & UNIT -->
                        <div class="item-rate-grid">
                            <div class="item-field-group">
                                <label for="internalRate" class="item-label">
                                    Internal Rate (MYR)
                                    <span class="required-star">*</span>
                                </label>
                                <div class="item-rate-input-wrap">
                                    <span class="item-rate-prefix">RM</span>
                                    <input type="number"
                                           id="internalRate"
                                           name="internal_rate"
                                           step="0.01"
                                           value="{{ old('internal_rate', isset($item) ? ($item->internal_rate ?? '') : '') }}"
                                           required
                                           class="item-input item-rate-input"
                                           placeholder="0.00">
                                </div>
                            </div>

                            <div class="item-field-group">
                                <div id="unitSelectContainer">
                                    <label for="chooseUnit" class="item-label">
                                        Billing Unit
                                    </label>
                                    <select id="chooseUnit" name="unit_id" class="item-select">
                                        <option value="">-- Unit --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->unit_id }}" {{ old('unit_id', isset($item) ? ($item->unit_id ?? '') : '') == $unit->unit_id ? 'selected' : '' }}>
                                                {{ strtoupper($unit->unit_name) }}
                                            </option>
                                        @endforeach
                                        <option value="NEW_UNIT" style="font-weight: 700; color: #1d518a;">+ Add Unit</option>
                                    </select>
                                </div>

                                <!-- Dynamic New Unit Inline Input -->
                                <div id="newUnitContainer" class="item-dynamic-field d-none" style="display: none; position: relative;">
                                    <label for="createUnit" class="item-dynamic-label">
                                        <i class="fa-solid fa-plus-circle"></i>
                                        New Unit
                                    </label>
                                    <div style="position: relative;">
                                        <input type="text" id="createUnit" name="new_unit_name" class="item-input" style="padding-right: 2.2rem;" placeholder="e.g. km, site, day">
                                        <button type="button" id="cancelNewUnitBtn" class="item-cancel-unit-btn" title="Back to unit dropdown">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 6. DESCRIPTION / REMARKS -->
                        <div class="item-field-group">
                            <label for="description" class="item-label">
                                Remarks / Specifications
                                <span class="text-slate-400 font-normal normal-case">(Optional)</span>
                            </label>
                            <textarea id="description"
                                      name="description"
                                      class="item-textarea"
                                      rows="3"
                                      placeholder="Additional technical details, crew size, or equipment specifications...">{{ old('description', isset($item) ? ($item->description ?? '') : '') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Hidden element for live unit preview binding in item.js -->
                <span id="rateUnitDisplay" style="display: none;"></span>

                <!-- Form Action Buttons -->
                <div class="item-card-footer">
                    <a href="{{ route('admin.index') }}" class="item-btn item-btn--secondary">
                        Cancel
                    </a>
                    <button type="submit" id="submitBtn" class="item-btn item-btn--primary">
                        <i class="fa-solid fa-check"></i>
                        <span>{{ isset($item) ? 'Update Catalog Item' : 'Save Catalog Item' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <!-- Pass Existing Edit Item Data to JS -->
    <script>
    window.editItemData = {!! json_encode([
        'module_id'   => old('module_id', isset($item) ? ($item->module_id ?? $item->service?->category?->module_id ?? '') : ''),
        'category_id' => old('category_id', isset($item) ? ($item->category_id ?? $item->service?->category_id ?? '') : ''),
        'service_id'  => old('service_id', isset($item) ? ($item->service_id ?? '') : ''),
    ]) !!};

    window.categoriesUrl = "{{ url('/data/modules/__ID__/categories') }}";
    window.servicesUrl   = "{{ url('/data/categories/__ID__/services') }}";
    </script>
    <script src="{{ asset('js/item.js') }}"></script>
    @endpush
</x-app-layout>