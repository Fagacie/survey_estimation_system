<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentTerm;
use App\Models\QtInvoice;
use App\Models\Project;
use App\Models\Signatory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function create($quotationId, $termId)
    {
        // 1. Get the quotation and the specific original term the user clicked
        $quotation = QtInvoice::findOrFail($quotationId);

        $originalTerm = PaymentTerm::where('id', $termId)
            ->where('quotation_Id', $quotationId)
            ->firstOrFail();

        // 2. Check if this term already has an invoice (prevents duplicates)
        $existingCopy = PaymentTerm::where('source_term_id', $originalTerm->id)
            ->whereNotNull('invoice_Id')
            ->first();

        if ($existingCopy) {
            // Already generated before — just show that invoice instead of creating a new one
            return redirect()->route('invoices.show', $existingCopy->invoice_Id);
        }

        // 3. Not generated yet — create the invoice + copy the term
        $invoice = DB::transaction(function () use ($quotation, $originalTerm) {

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber($quotation->project_Id, $originalTerm->id),
                'quotation_Id'   => $quotation->quotation_Id,
                'invoice_date'   => null,   // set later, when the invoice form is saved
                'printed_date'   => null,
                'due_date'       => null,
                'description'    => null,
                'status'         => 'draft',
                'created_by'     => Auth::id(),
            ]);

            PaymentTerm::create([
                'quotation_Id'   => null,
                'invoice_Id'     => $invoice->invoice_Id,
                'source_term_id' => $originalTerm->id,   // remembers where this came from
                'name'           => $originalTerm->name,
                'percentage'     => $originalTerm->percentage,
                'condition'      => $originalTerm->condition,
                'amount'         => $originalTerm->amount,
                'created_by'     => Auth::id(),
            ]);

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice->invoice_Id);
    }

    public function show(Request $request, $invoiceId)
    {
        $invoice = Invoice::with(['paymentTerm', 'quotation', 'signatory'])->findOrFail($invoiceId);

        // People who can be chosen under "Approved by"
        $signatories = Signatory::orderBy('name')->get();

        if ($request->has('print')) {
            return redirect()->route('invoices.download', $invoiceId);
        }

        return view('dashboard.invoice', compact('invoice', 'signatories'));
    }

    public function download($invoiceId)
    {
        $invoice = Invoice::with(['paymentTerm', 'quotation', 'signatory'])->findOrFail($invoiceId);
        $signatories = Signatory::orderBy('name')->get();

        $pdf = Pdf::loadView('dashboard.invoice-pdf', compact('invoice', 'signatories'))
                  ->setPaper('A4', 'portrait');

        $safeInvoiceNo = str_replace(['/', '\\'], '-', $invoice->invoice_number ?? 'Unknown');
        return $pdf->download('Invoice_' . $safeInvoiceNo . '.pdf');
    }

    private function generateInvoiceNumber($projectId, $originalTermId): string
    {
        $year = now()->format('Y');

        $lockedProject = Project::where('project_Id', $projectId)
            ->lockForUpdate()
            ->first();

        // Get ALL original (quotation-side) payment term IDs under this project,
        // ordered by creation - this fixes the numbering regardless of click order
        $termIdsInOrder = PaymentTerm::whereHas('quotation', function ($q) use ($projectId) {
                $q->where('project_Id', $projectId);
            })
            ->whereNull('invoice_Id')   // only original terms, not invoice-side copies
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        $position = array_search($originalTermId, $termIdsInOrder);
        $runningNumber = $position !== false ? $position + 1 : count($termIdsInOrder) + 1;

        return sprintf('%s-INV/%d/%03d', $lockedProject->number, $year, $runningNumber);
    }

    /**
     * Single save for the invoice slip: dates + description + payment condition.
     * Replaces the old issue() and updateDetails() pair.
     */
    public function updateDetails(Request $request, $invoiceId)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'due_date'     => 'required|date|after_or_equal:invoice_date',
            'description'  => 'nullable|string|max:1000',
            'condition'    => 'nullable|string|max:1000',

            // Approved by: an existing person (id) or "new"
            'signatory_id' => ['required', function ($attribute, $value, $fail) {
                if ($value !== 'new' && ! Signatory::whereKey($value)->exists()) {
                    $fail('The selected signatory is invalid.');
                }
            }],
            'new_name'      => 'required_if:signatory_id,new|nullable|string|max:255',
            'new_position'  => 'required_if:signatory_id,new|nullable|string|max:255',
            'new_signature' => 'required_if:signatory_id,new|nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $invoice = Invoice::with('paymentTerm')->findOrFail($invoiceId);

        DB::transaction(function () use ($invoice, $validated, $request) {
            $firstSave = is_null($invoice->printed_date);

            // Approved by: use the chosen person, or create a new one from the form
            $signatoryId = $validated['signatory_id'];

            if ($signatoryId === 'new') {
                $file     = $request->file('new_signature');
                $fileName = 'sig_' . Str::slug($validated['new_name']) . '_' . time() . '.' . $file->guessExtension();

                File::ensureDirectoryExists(public_path('images/signatures'));
                $file->move(public_path('images/signatures'), $fileName);

                $signatory = Signatory::create([
                    'name'           => $validated['new_name'],
                    'position'       => $validated['new_position'],
                    'signature_path' => 'images/signatures/' . $fileName,
                    'created_by'     => Auth::id(),
                ]);

                $signatoryId = $signatory->id;
            }

            $invoice->update([
                'invoice_date' => $validated['invoice_date'],
                'due_date'     => $validated['due_date'],
                'description'  => $validated['description'] ?? null,
                'signatory_id' => $signatoryId,
                // Only set on the first save, so later edits don't change the date on the slip
                'printed_date' => $invoice->printed_date ?? now(),
                // Only move draft -> unpaid on the first save, so a "paid" invoice isn't reset
                'status'       => $firstSave ? 'unpaid' : $invoice->status,
                'updated_by'   => Auth::id(),
            ]);

            if ($invoice->paymentTerm) {
                $invoice->paymentTerm->update([
                    'condition'  => $validated['condition'] ?? null,
                    'updated_by' => Auth::id(),
                ]);
            }
        });

        return response()->json(['success' => true]);
    }
}