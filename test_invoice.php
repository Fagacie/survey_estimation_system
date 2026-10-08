<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invoice = \App\Models\Invoice::with(['paymentTerm', 'quotation', 'signatory'])->first();
if (!$invoice) {
    echo "No invoices found\n";
    exit;
}

$signatories = \App\Models\Signatory::orderBy('name')->get();

try {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.invoice-pdf', compact('invoice', 'signatories'))
                ->setPaper('A4', 'portrait');
    
    $content = $pdf->output();
    file_put_contents('test_invoice.pdf', $content);
    echo "PDF generated successfully, size: " . strlen($content) . " bytes\n";
} catch (\Exception $e) {
    echo "Error generating PDF: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
