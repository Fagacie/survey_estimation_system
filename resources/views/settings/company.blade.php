<x-app-layout containerClass="w-full px-8 py-8">
    <x-slot name="header">Company Settings</x-slot>

    <div class="flex justify-between items-center mb-6 mt-2">
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Company Settings</h1>
        <a href="{{ route('settings.costs') }}" class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 px-4 py-2 text-sm font-medium border border-slate-200 transition-colors shadow-sm no-underline">
            <i class="fa-solid fa-arrow-left"></i> Cost Rates
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 border border-emerald-200 p-4 mb-6 flex items-start gap-3 text-sm">
            <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="space-y-6">

            {{-- COMPANY INFO --}}
            <div class="bg-white border border-slate-200 p-6 shadow-sm">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-4">Company Information</div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Company Name</label>
                        <input type="text" name="company_name" value="{{ $settings['company_name'] ?? '' }}" placeholder="Eco Hydrotech Solutions Sdn. Bhd."
                            class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Registration No.</label>
                        <input type="text" name="company_reg_no" value="{{ $settings['company_reg_no'] ?? '' }}" placeholder="202401234567 (12345-X)"
                            class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Address</label>
                        <textarea name="company_address" rows="2" placeholder="Full company address..."
                            class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">{{ $settings['company_address'] ?? '' }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Phone</label>
                        <input type="text" name="company_phone" value="{{ $settings['company_phone'] ?? '' }}"
                            class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
                        <input type="email" name="company_email" value="{{ $settings['company_email'] ?? '' }}"
                            class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Company Logo</label>
                        @if($settings['company_logo'] ?? false)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $settings['company_logo']) }}" alt="Logo" class="h-12">
                            </div>
                        @endif
                        <input type="file" name="company_logo" accept="image/png,image/jpeg"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    </div>
                </div>
            </div>

            {{-- BANK DETAILS --}}
            <div class="bg-white border border-slate-200 p-6 shadow-sm">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-4">Default Payment Details</div>
                <p class="text-xs text-slate-400 mb-4">These will be pre-filled on every new invoice.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Bank Name</label>
                        <input type="text" name="payment_bank_name" value="{{ $settings['payment_bank_name'] ?? '' }}"
                            class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Account Name</label>
                        <input type="text" name="payment_account_name" value="{{ $settings['payment_account_name'] ?? '' }}"
                            class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Account Number</label>
                        <input type="text" name="payment_account_number" value="{{ $settings['payment_account_number'] ?? '' }}"
                            class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                    </div>
                </div>
            </div>

            {{-- SIGNATURES --}}
            <div class="bg-white border border-slate-200 p-6 shadow-sm">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-4">Default Signatures</div>
                <p class="text-xs text-slate-400 mb-4">These will be pre-filled on every new invoice.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Prepared By</div>
                        <input type="text" name="default_prepared_by_name" value="{{ $settings['default_prepared_by_name'] ?? '' }}" placeholder="Name"
                            class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                        <input type="text" name="default_prepared_by_title" value="{{ $settings['default_prepared_by_title'] ?? '' }}" placeholder="Title / Role"
                            class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Signature Image</label>
                            @if($settings['prepared_by_signature'] ?? false)
                                <div class="mb-2"><img src="{{ asset('storage/' . $settings['prepared_by_signature']) }}" alt="Signature" class="h-10"></div>
                            @endif
                            <input type="file" name="prepared_by_signature" accept="image/png,image/jpeg"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Approved By</div>
                        <input type="text" name="default_approved_by_name" value="{{ $settings['default_approved_by_name'] ?? '' }}" placeholder="Name"
                            class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                        <input type="text" name="default_approved_by_title" value="{{ $settings['default_approved_by_title'] ?? '' }}" placeholder="Title / Role"
                            class="w-full border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:outline-none focus:border-slate-400 focus:bg-white transition-colors">
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">Signature Image</label>
                            @if($settings['approved_by_signature'] ?? false)
                                <div class="mb-2"><img src="{{ asset('storage/' . $settings['approved_by_signature']) }}" alt="Signature" class="h-10"></div>
                            @endif
                            <input type="file" name="approved_by_signature" accept="image/png,image/jpeg"
                                class="w-full text-sm text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:border-0 file:text-xs file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 text-sm font-medium transition-colors flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Save Company Settings
            </button>
        </div>
    </form>
</x-app-layout>
