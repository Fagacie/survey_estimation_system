<x-app-layout containerClass="w-full px-8 py-8 bg-slate-50 relative min-h-screen">
    <x-slot name="header">{{ isset($item) ? 'Edit Item' : 'Create Item' }}</x-slot>

    <div class="max-w-4xl mx-auto pb-12">

        <!-- PAGE TITLE & BUTTON -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-3 tracking-tight">
                <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 shadow-sm border border-teal-100">
                    <i class="fa-solid fa-database text-lg"></i>
                </div>
                Data Management
            </h2>
            <a href="{{ route('admin.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 hover:text-slate-800 transition-colors shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Back
            </a>
        </div>

        <!-- Dynamic Blue Banner -->
        <div class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-xl p-6 mb-8 shadow-md text-white relative overflow-hidden">
            <div class="absolute -right-10 -top-10 opacity-10">
                <i class="fa-solid fa-shapes text-9xl"></i>
            </div>
            <h2 class="text-2xl font-bold tracking-tight mb-2 relative z-10">{{ isset($item) ? 'Edit Item' : 'Create New Item' }}</h2>
            <p class="text-slate-300 text-sm relative z-10">{{ isset($item) ? 'Modify the details of this quotation item.' : 'Create a new item that can be added to quotations.' }}</p>
        </div>

        <!-- Main Content Area -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-6 pb-3 border-b border-slate-100"><i class="fa-solid fa-circle-info text-slate-400 mr-2"></i> Item Information</h3>

            <!-- Display Validation Errors -->
            @if ($errors->any())
                <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 mb-6 rounded">
                    <ul class="list-disc ml-5 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="itemForm" action="{{ isset($item) ? route('items.update', ['id' => $item->item_id]) : route('items.store') }}" method="POST">
                @csrf
                @if(isset($item))
                    @method('PUT')
                @endif
                <input type="hidden" id="itemId" name="item_id" value="{{ $item->item_id ?? '' }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Column 1: Dropdowns & Dynamic Inputs -->
                    <div class="space-y-5">
                        
                        <!-- MODULE SELECT -->
                        <div>
                            <label for="chooseModule" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Choose Module</label>
                            <select id="chooseModule" name="module_id" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors bg-white">
                                <option value="">-- Select Module --</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->module_id }}" {{ old('module_id', isset($item) ? ($item->module_id ?? $item->category?->module_id ?? '') : '') == $module->module_id ? 'selected' : '' }}>
                                        {{ strtoupper($module->module_name) }}
                                    </option>
                                @endforeach
                                <option value="NEW_MODULE" class="font-bold text-teal-600 bg-teal-50">+ Add New Module</option>
                            </select>
                        </div>

                        <!-- HIDDEN NEW MODULE INPUT -->
                        <div id="newModuleContainer" class="hidden">
                            <label for="createModule" class="block text-xs font-bold text-teal-600 uppercase tracking-wider mb-2">New Module Name</label>
                            <input type="text" id="createModule" name="new_module_name" placeholder="Type new module name..." class="w-full rounded-lg border-teal-300 bg-teal-50 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors">
                        </div>

                        <!-- CATEGORY SELECT -->
                        <div>
                            <label for="chooseCategory" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Choose Category</label>
                            <select id="chooseCategory" name="category_id" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors bg-white">
                                <option value="">-- Select Category First --</option>
                                <option value="NEW_CATEGORY" class="font-bold text-teal-600 bg-teal-50">+ Add New Category</option>
                            </select>
                        </div>

                        <!-- HIDDEN NEW CATEGORY INPUT -->
                        <div id="newCategoryContainer" class="hidden">
                            <label for="createCategory" class="block text-xs font-bold text-teal-600 uppercase tracking-wider mb-2">New Category Name</label>
                            <input type="text" id="createCategory" name="new_category_name" placeholder="Type new category name..." class="w-full rounded-lg border-teal-300 bg-teal-50 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors">
                        </div>

                        <!-- SERVICE SELECT -->
                        <div>
                            <label for="chooseService" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Choose Service</label>
                            <select id="chooseService" name="service_id" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors bg-white">
                                <option value="">-- Select Service First --</option>
                                <option value="NEW_SERVICE" class="font-bold text-teal-600 bg-teal-50">+ Add New Service</option>
                            </select>
                        </div>

                        <!-- HIDDEN NEW SERVICE INPUT -->
                        <div id="newServiceContainer" class="hidden">
                            <label for="createService" class="block text-xs font-bold text-teal-600 uppercase tracking-wider mb-2">New Service Name</label>
                            <input type="text" id="createService" name="new_service_name" placeholder="Type new service name..." class="w-full rounded-lg border-teal-300 bg-teal-50 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors">
                        </div>

                    </div>

                    <!-- Column 2: Item Details & Rates -->
                    <div class="space-y-5">
                        
                        <!-- ITEM NAME -->
                        <div>
                            <label for="itemName" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Item Name <span class="text-rose-500">*</span></label>
                            <input type="text" id="itemName" name="name" value="{{ old('name', isset($item) ? ($item->item_name ?? '') : '') }}" required class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors" placeholder="e.g. Environmental Engineer">
                        </div>

                        <!-- INTERNAL RATE (MYR) & UNIT -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="internalRate" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Internal Rate (MYR) <span class="text-rose-500">*</span></label>
                                <input type="number" id="internalRate" name="internal_rate" step="0.01" value="{{ old('internal_rate', isset($item) ? ($item->internal_rate ?? '') : '') }}" required class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors" placeholder="0.00">
                            </div>

                            <div class="relative">
                                <div id="unitSelectContainer">
                                    <label for="chooseUnit" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Unit</label>
                                    <select id="chooseUnit" name="unit_id" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors bg-white">
                                        <option value="">-- Unit --</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->unit_id }}" {{ old('unit_id', isset($item) ? ($item->unit_id ?? '') : '') == $unit->unit_id ? 'selected' : '' }}>
                                                {{ strtoupper($unit->unit_name) }}
                                            </option>
                                        @endforeach
                                        <option value="NEW_UNIT" class="font-bold text-teal-600 bg-teal-50">+ Add Unit</option>
                                    </select>
                                </div>

                                <!-- NEW UNIT INLINE INPUT -->
                                <div id="newUnitContainer" class="hidden relative">
                                    <label for="createUnit" class="block text-xs font-bold text-teal-600 uppercase tracking-wider mb-2">New Unit</label>
                                    <div class="relative">
                                        <input type="text" id="createUnit" name="new_unit_name" class="w-full rounded-lg border-teal-300 bg-teal-50 py-2.5 pl-3 pr-10 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors" placeholder="e.g. kg, pcs">
                                        <button type="button" id="cancelNewUnitBtn" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-rose-500 transition-colors" title="Back to select">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- DESCRIPTION / REMARK -->
                        <div>
                            <label for="description" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Remark (Optional)</label>
                            <textarea id="description" name="description" rows="4" class="w-full rounded-lg border-slate-300 py-2.5 px-3 text-sm focus:border-teal-500 focus:ring-teal-500 shadow-sm transition-colors">{{ old('description', isset($item) ? ($item->description ?? '') : '') }}</textarea>
                        </div>

                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-10 pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.index') }}" class="px-6 py-2.5 text-sm font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors shadow-sm">
                        CANCEL
                    </a>
                    <button type="submit" id="submitBtn" class="px-6 py-2.5 text-sm font-semibold text-white bg-teal-600 border border-transparent rounded-lg hover:bg-teal-700 transition-colors shadow-sm">
                        {{ isset($item) ? 'UPDATE ITEM' : 'SAVE ITEM' }}
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
    </script>

    <!-- Load item.js after window.editItemData is declared -->
    <script src="{{ asset('js/item.js') }}"></script>
    @endpush
</x-app-layout>