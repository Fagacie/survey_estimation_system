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

Route::get('/', function () {
    return redirect()->route('login');
});

// DEV ROUTE: Test Perf
Route::get('/dev/test-perf', function() {
    $start = microtime(true);
    $modules = \App\Models\Module::with(['categories.services.items.unit'])->get();
    
    $adminModulesTree = $modules->map(function ($module) {
        return [
            'module_id'   => $module->getKey(),
            'module_name' => $module->module_name ?? $module->name ?? 'UNKNOWN MODULE',
            'categories'  => $module->categories->map(function ($cat) {
                return [
                    'category_id'   => $cat->getKey(),
                    'category_name' => $cat->category_name ?? $cat->name ?? 'N/A',
                    'services'      => $cat->services->map(function ($serv) {
                        return [
                            'service_id'   => $serv->getKey(),
                            'service_name' => $serv->service_name ?? $serv->name ?? 'N/A',
                            'items'        => $serv->items->map(function ($item) {
                                return [
                                    'item_id'   => $item->getKey(),
                                    'item_name' => $item->item_name ?? $item->name ?? 'N/A',
                                    'rate'      => $item->internal_rate ?? 0,
                                    'unit'      => $item->unit->unit_name ?? $item->unit_name ?? $item->unit ?? '',
                                ];
                            })->values()->toArray(),
                        ];
                    })->values()->toArray(),
                ];
            })->values()->toArray(),
        ];
    })->values()->toArray();

    return response()->json([
        'time' => microtime(true) - $start,
        'modules' => $modules->count(),
        'tree' => count($adminModulesTree)
    ]);
});

Route::get('/dev/ping', function() {
    return 'pong';
});

Route::get('/dev/counts', function() {
    return [
        'modules' => \App\Models\Module::count(),
        'categories' => \App\Models\Category::count(),
        'services' => \App\Models\Service::count(),
        'items' => \App\Models\Item::count(),
        'units' => \App\Models\Unit::count(),
    ];
});

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

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/projects/coming-soon', function() {
        return view('projects.coming_soon');
    })->name('projects.coming_soon');

    Route::resource('projects', App\Http\Controllers\ProjectController::class);

    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::put('/projects/{project}/allowances', [\App\Http\Controllers\ProjectController::class, 'updateAllowances'])->name('projects.updateAllowances');
    Route::get('/projects/{project}/surveys/{surveyLocation}/planning', [\App\Http\Controllers\SurveyLocationController::class, 'map'])->name('projects.surveys.map');
    Route::get('/projects/{project}/surveys/{surveyLocation}/map-lines', [\App\Http\Controllers\SurveyLocationController::class, 'mapLines'])->name('projects.surveys.lines');
    Route::post('/projects/{project}/surveys/{surveyLocation}/map/save', [\App\Http\Controllers\SurveyLocationController::class, 'saveMap'])->name('projects.surveys.map.save');
    Route::post('/projects/{project}/surveys/{surveyLocation}/map/screenshot', [\App\Http\Controllers\SurveyLocationController::class, 'saveScreenshot'])->name('projects.surveys.map.screenshot');
    Route::post('/projects/{project}/surveys/{surveyLocation}/parameters', [\App\Http\Controllers\SurveyLocationController::class, 'saveParameters'])->name('projects.surveys.parameters.store');
    Route::post('/projects/{project}/surveys', [\App\Http\Controllers\SurveyLocationController::class, 'store'])->name('projects.surveys.store');
    Route::delete('/projects/{project}/surveys/{surveyLocation}', [\App\Http\Controllers\SurveyLocationController::class, 'destroy'])->name('projects.surveys.destroy');
    Route::get('/projects/{project}/report/preview', [\App\Http\Controllers\ReportController::class, 'preview'])->name('projects.report.preview');
    Route::get('/projects/{project}/report/pdf', [\App\Http\Controllers\ReportController::class, 'download'])->name('projects.report.pdf');

    // 1. Home / Dashboard Route
    Route::get('/home', function () {
        return redirect()->route('projects.index');
    })->name('home');

    Route::get('/dashboard', function () {
        return redirect()->route('projects.index');
    })->name('dashboard');

    // 2. Quotation Routes
    Route::get('/quotation', [QuotationController::class, 'index'])->name('quotation.index');
    Route::post('/quotation', [QuotationController::class, 'store'])->name('quotation.store');
    Route::get('/quotations/history', [HistoryController::class, 'index'])->name('quotations.history');
    Route::get('/quotations/{id}', [QuotationController::class, 'show'])->name('quotations.show');
    Route::get('/project/next-number', [QuotationController::class, 'nextNumber']);
    Route::get('/quotations/{id}/invoice', [QuotationController::class, 'showInvoice'])->name('quotations.invoice');
    // 3. History Routes
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::delete('/quotations/{id}', [HistoryController::class, 'destroy'])->name('quotations.destroy');
    
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

require __DIR__.'/auth.php';