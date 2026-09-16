<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('projects.index');
    })->name('dashboard');

    Route::resource('projects', ProjectController::class);
    Route::resource('clients', ClientController::class)->except(['create', 'edit', 'show', 'update']);
    Route::put('/projects/{project}/allowances', [\App\Http\Controllers\ProjectController::class, 'updateAllowances'])->name('projects.updateAllowances');
    Route::get('/projects/{project}/surveys/{surveyLocation}/planning', [\App\Http\Controllers\SurveyLocationController::class, 'map'])->name('projects.surveys.map');
    Route::get('/projects/{project}/surveys/{surveyLocation}/map-lines', [\App\Http\Controllers\SurveyLocationController::class, 'mapLines'])->name('projects.surveys.lines');
    Route::post('/projects/{project}/surveys/{surveyLocation}/map/save', [\App\Http\Controllers\SurveyLocationController::class, 'saveMap'])->name('projects.surveys.map.save');
    Route::post('/projects/{project}/surveys/{surveyLocation}/map/screenshot', [\App\Http\Controllers\SurveyLocationController::class, 'saveScreenshot'])->name('projects.surveys.map.screenshot');
    Route::post('/projects/{project}/surveys/{surveyLocation}/parameters', [\App\Http\Controllers\SurveyLocationController::class, 'saveParameters'])->name('projects.surveys.parameters.store');
    
    Route::post('/projects/{project}/surveys', [\App\Http\Controllers\SurveyLocationController::class, 'store'])->name('projects.surveys.store');
    Route::delete('/projects/{project}/surveys/{surveyLocation}', [\App\Http\Controllers\SurveyLocationController::class, 'destroy'])->name('projects.surveys.destroy');

    Route::get('/settings/costs', [\App\Http\Controllers\SettingsController::class, 'costs'])->name('settings.costs');
    Route::post('/settings/costs', [\App\Http\Controllers\SettingsController::class, 'storeCost'])->name('settings.costs.store');
    Route::put('/settings/costs/{costRate}', [\App\Http\Controllers\SettingsController::class, 'updateCost'])->name('settings.costs.update');
    Route::delete('/settings/costs/{costRate}', [\App\Http\Controllers\SettingsController::class, 'destroyCost'])->name('settings.costs.destroy');

    Route::get('/projects/{project}/cost', [\App\Http\Controllers\CostEstimationController::class, 'show'])->name('projects.cost.show');
    Route::post('/projects/{project}/cost', [\App\Http\Controllers\CostEstimationController::class, 'store'])->name('projects.cost.store');
    Route::post('/projects/{project}/cost/recalculate', [\App\Http\Controllers\CostEstimationController::class, 'recalculate'])->name('projects.cost.recalculate');

    Route::get('/projects/{project}/report/preview', [\App\Http\Controllers\ReportController::class, 'preview'])->name('projects.report.preview');
    Route::get('/projects/{project}/report/pdf', [\App\Http\Controllers\ReportController::class, 'downloadReport'])->name('projects.report.pdf');

    // ── INVOICE MANAGEMENT ──────────────────────────────────────
    Route::get('/invoices', [\App\Http\Controllers\InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create', [\App\Http\Controllers\InvoiceController::class, 'create'])->name('invoices.create');
    Route::get('/projects/{project}/invoices/create', [\App\Http\Controllers\InvoiceController::class, 'createFromProject'])->name('projects.invoices.create');
    Route::post('/invoices', [\App\Http\Controllers\InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
    Route::put('/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/invoices/{invoice}', [\App\Http\Controllers\InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/invoices/{invoice}/preview', [\App\Http\Controllers\InvoiceController::class, 'preview'])->name('invoices.preview');
    Route::patch('/invoices/{invoice}/status', [\App\Http\Controllers\InvoiceController::class, 'updateStatus'])->name('invoices.status');

    // ── COMPANY SETTINGS ────────────────────────────────────────
    Route::get('/settings/company', [\App\Http\Controllers\SettingsController::class, 'company'])->name('settings.company');
    Route::post('/settings/company', [\App\Http\Controllers\SettingsController::class, 'updateCompany'])->name('settings.company.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/projects/{project}/surveys/{surveyLocation}/report-map', [\App\Http\Controllers\ReportController::class, 'captureMap'])->name('projects.surveys.report-map');

require __DIR__.'/auth.php';
