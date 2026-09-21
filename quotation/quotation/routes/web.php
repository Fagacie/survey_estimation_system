<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InvoiceController;
use App\Models\Module;
use App\Models\Category;
use App\Models\Service;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/signin', [AuthController::class, 'showSignin'])->name('signin');
    Route::post('/signin', [AuthController::class, 'signin']);

    Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup']);
});

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // 1. Home / Dashboard Route (Fetches modules to fix Undefined Variable error)
   Route::get('/home', [QuotationController::class, 'index'])->name('home');

    // 2. Quotation Routes
    Route::get('/quotation', [QuotationController::class, 'index'])->name('quotation.index');
    Route::post('/quotation', [QuotationController::class, 'store'])->name('quotation.store');
    Route::get('/quotations/{id}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::get('/project/next-number', [QuotationController::class, 'nextNumber']);
    Route::get('/quotations/{id}/invoice', [QuotationController::class, 'showInvoice'])->name('quotations.invoice');
    // 3. History Routes
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::delete('/quotations/{id}', [HistoryController::class, 'destroy'])->name('quotations.destroy');
    Route::get('/quotations/history', [App\Http\Controllers\QuotationController::class, 'history'])->name('quotations.history');
    
    Route::post('/quotations/{quotationId}/invoices', [InvoiceController::class, 'store']);

    Route::get('/invoices/create/{quotation}/{term}', [InvoiceController::class, 'create'])
        ->name('invoices.create');

    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
        ->name('invoices.show');
    Route::post('/invoices/{invoice}/issue', [InvoiceController::class, 'issue'])->name('invoices.issue');
    Route::post('/invoices/{invoice}/update-details', [InvoiceController::class, 'updateDetails'])->name('invoices.updateDetails');
    
    /*
    |--------------------------------------------------------------------------
    | Admin & Item Management Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/newItem', [AdminController::class, 'create'])->name('newItem');
    Route::get('/items/create', [AdminController::class, 'create'])->name('items.create');
    Route::post('/items', [AdminController::class, 'store'])->name('items.store');
    Route::get('/items/{id}/edit', [AdminController::class, 'edit'])->name('items.edit');
    Route::put('/items/{id}', [AdminController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}', [AdminController::class, 'destroy'])->name('items.destroy');

    /*
    |--------------------------------------------------------------------------
    | Dynamic Dropdown API Endpoints (AJAX)
    |--------------------------------------------------------------------------
    */
    // Fetch categories under a selected module
    Route::get('/api/modules/{moduleId}/categories', function ($moduleId) {
        return response()->json(Category::where('module_id', $moduleId)->get());
    });

    // Fetch services under a selected category
    Route::get('/api/categories/{categoryId}/services', function ($categoryId) {
        return response()->json(Service::where('category_id', $categoryId)->get());
    });

    /*
    |--------------------------------------------------------------------------
    | Quotation Preview Route
    |--------------------------------------------------------------------------
    */
    Route::get('/quotation-preview', function () {
        $data = [
            'quotationNumber' => '1234',
            'quotationDate' => '01/25/2030',
            'companyLogo' => asset('images/logo.png'),
            'companyName' => 'ECO HYDROTECH SOLUTIONS SDN. BHD.',
            'companyAddressLine1' => 'Institute of Oceanography and Environment',
            'companyAddressLine2' => 'Universiti Malaysia Terengganu',
            'companyAddressLine3' => '21030, Kuala Nerus, Terengganu',
            'companyCountry' => 'Malaysia',
            'companyEmail' => 'ecohydrosolution@gmail.com',
            'companyPhone' => '+60 16-322 7527',
            'customer' => [
                'name' => 'Acme Corporation',
                'address' => '123 Business Street, Tech Park, 50000 Kuala Lumpur',
                'email' => 'client@acme.com',
                'phone' => '+60 12-345 6789',
            ],
            'project' => [
                'name' => 'Coastal Monitoring System',
                'description' => 'Supply and installation of water sampling and telemetry units.',
            ],
            'items' => [
                [
                    'description' => '[Product / Service Description]',
                    'quantity' => 5,
                    'unit_price' => 100,
                ],
                [
                    'description' => '[Product / Service Description]',
                    'quantity' => 5,
                    'unit_price' => 100,
                ],
                [
                    'description' => '[Product / Service Description]',
                    'quantity' => 5,
                    'unit_price' => 100,
                ],
            ],
            'subtotal' => 1500,
            'tax' => 100,
            'vat' => 50,
            'grandTotal' => 1650,
            'validDays' => 30,
            'deliveryTimeline' => '7-14 working days',
            'paymentTerms' => '50% advance / 50% upon delivery',
        ];

        return view('dashboard.quotation', $data);
    })->name('quotation.preview');
});