{{-- Show/Edit reuses the create form with $invoice populated --}}
@include('invoices.create', ['invoice' => $invoice, 'clients' => $clients, 'projects' => $projects, 'defaults' => \App\Models\CompanySetting::getMany([
    'payment_bank_name', 'payment_account_name', 'payment_account_number', 'payment_swift_code',
    'default_prepared_by_name', 'default_prepared_by_title', 'default_approved_by_name', 'default_approved_by_title',
])])
