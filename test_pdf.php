<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    $quotation = App\Models\QtInvoice::latest()->first(); 
    if (!$quotation) { echo 'No quotation'; exit; } 
    $pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.quotation-pdf', ['quotation' => $quotation, 'signer' => $quotation->signatory ?? App\Models\Signatory::first()]); 
    file_put_contents('test_output.pdf', $pdf->output());
    echo 'SUCCESS - test_output.pdf saved'; 
} catch (\Exception $e) { 
    echo 'ERROR: ' . $e->getMessage() . "\n" . $e->getTraceAsString(); 
}
