<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Project;
use App\Models\CompanySetting;
use App\Helpers\NumberToWords;
use App\Services\InvoiceNumberGenerator;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of all invoices.
     */
    public function index(Request $request)
    {
        $query = auth()->user()->invoices()->with(['client', 'project']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Search by invoice number
        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%');
        }

        $invoices = $query->orderByDesc('created_at')->paginate(15);
        $clients = Client::orderBy('name')->get();

        return view('invoices.index', compact('invoices', 'clients'));
    }

    public function create()
    {
        $clients = Client::orderBy('name')->get();
        $projects = auth()->user()->projects()->with('costEstimation')->orderByDesc('created_at')->get();
        $invoiceNumber = InvoiceNumberGenerator::generate();
        $invoice = null;
        $prefill = [];

        // Load company defaults
        $defaults = CompanySetting::getMany([
            'payment_bank_name', 'payment_account_name',
            'payment_account_number', 'payment_swift_code',
            'default_prepared_by_name', 'default_prepared_by_title',
            'default_approved_by_name', 'default_approved_by_title',
        ]);

        return view('invoices.create', compact(
            'clients', 'projects', 'invoiceNumber', 'defaults', 'invoice', 'prefill'
        ));
    }

    /**
     * Show the form for creating an invoice linked to a specific project.
     */
    public function createFromProject(string $projectId)
    {
        $project = auth()->user()->projects()->with(['client', 'costEstimation'])->findOrFail($projectId);
        $clients = Client::orderBy('name')->get();
        $projects = auth()->user()->projects()->with('costEstimation')->orderByDesc('created_at')->get();
        $invoiceNumber = InvoiceNumberGenerator::generate();

        // Load company defaults
        $defaults = CompanySetting::getMany([
            'payment_bank_name', 'payment_account_name',
            'payment_account_number', 'payment_swift_code',
            'default_prepared_by_name', 'default_prepared_by_title',
            'default_approved_by_name', 'default_approved_by_title',
        ]);

        // Pre-fill from project
        $prefill = [
            'client_id'          => $project->client_id,
            'project_id'         => $project->id,
            'our_ref'            => $project->costEstimation?->quotation_number,
            'quote_total'        => $project->costEstimation?->total_cost ?? 0,
            'cost_estimation_id' => $project->costEstimation?->id,
        ];
        
        $invoice = null;

        return view('invoices.create', compact(
            'clients', 'projects', 'invoiceNumber', 'defaults', 'prefill', 'project', 'invoice'
        ));
    }

    /**
     * Store a newly created invoice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_number'          => 'required|string|max:50|unique:invoices,invoice_number',
            'client_id'               => 'required|exists:clients,id',
            'project_id'              => 'nullable|exists:projects,id',
            'issue_date'              => 'required|date',
            'due_date'                => 'nullable|date|after_or_equal:issue_date',
            'payment_terms'           => 'nullable|string|max:100',
            'client_ref'              => 'nullable|string|max:100',
            'our_ref'                 => 'nullable|string|max:100',
            'currency'                => 'required|string|max:10',
            'tax_rate'                => 'nullable|numeric|min:0|max:100',
            'discount_amount'         => 'nullable|numeric|min:0',
            'notes'                   => 'nullable|string',
            'prepared_by_name'        => 'nullable|string|max:100',
            'prepared_by_title'       => 'nullable|string|max:100',
            'approved_by_name'        => 'nullable|string|max:100',
            'approved_by_title'       => 'nullable|string|max:100',
            'payment_bank_name'       => 'nullable|string|max:255',
            'payment_account_name'    => 'nullable|string|max:255',
            'payment_account_number'  => 'nullable|string|max:50',
            'payment_swift_code'      => 'nullable|string|max:20',
            'items'                   => 'required|array|min:1',
            'items.*.title'           => 'required|string|max:255',
            'items.*.description'     => 'nullable|string',
            'items.*.quantity'        => 'required|numeric|min:0.01',
            'items.*.unit_price'      => 'required|numeric|min:0',
        ]);

        // Calculate totals
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += (float) $item['quantity'] * (float) $item['unit_price'];
        }

        $taxRate = (float) ($validated['tax_rate'] ?? 0);
        $taxAmount = $taxRate > 0 ? round($subtotal * ($taxRate / 100), 2) : 0;
        $discountAmount = (float) ($validated['discount_amount'] ?? 0);
        $grandTotal = $subtotal - $discountAmount + $taxAmount;

        // Generate amount in words
        $amountInWords = NumberToWords::convert($grandTotal, $validated['currency']);

        // Create invoice
        $invoice = Invoice::create([
            'invoice_number'          => $validated['invoice_number'],
            'client_id'               => $validated['client_id'],
            'project_id'              => $validated['project_id'] ?? null,
            'cost_estimation_id'      => $request->input('cost_estimation_id'),
            'user_id'                 => auth()->id(),
            'status'                  => 'Draft',
            'issue_date'              => $validated['issue_date'],
            'due_date'                => $validated['due_date'] ?? null,
            'payment_terms'           => $validated['payment_terms'] ?? null,
            'client_ref'              => $validated['client_ref'] ?? null,
            'our_ref'                 => $validated['our_ref'] ?? null,
            'currency'                => $validated['currency'],
            'subtotal'                => $subtotal,
            'tax_rate'                => $taxRate > 0 ? $taxRate : null,
            'tax_amount'              => $taxAmount > 0 ? $taxAmount : null,
            'discount_amount'         => $discountAmount > 0 ? $discountAmount : null,
            'grand_total'             => $grandTotal,
            'amount_in_words'         => $amountInWords,
            'payment_bank_name'       => $validated['payment_bank_name'] ?? null,
            'payment_account_name'    => $validated['payment_account_name'] ?? null,
            'payment_account_number'  => $validated['payment_account_number'] ?? null,
            'payment_swift_code'      => $validated['payment_swift_code'] ?? null,
            'notes'                   => $validated['notes'] ?? null,
            'prepared_by_name'        => $validated['prepared_by_name'] ?? null,
            'prepared_by_title'       => $validated['prepared_by_title'] ?? null,
            'approved_by_name'        => $validated['approved_by_name'] ?? null,
            'approved_by_title'       => $validated['approved_by_title'] ?? null,
        ]);

        // Create line items
        foreach ($validated['items'] as $index => $itemData) {
            $invoice->items()->create([
                'sort_order'  => $index,
                'title'       => $itemData['title'],
                'description' => $itemData['description'] ?? null,
                'quantity'    => (float) $itemData['quantity'],
                'unit_price'  => (float) $itemData['unit_price'],
                'total_price' => round((float) $itemData['quantity'] * (float) $itemData['unit_price'], 2),
            ]);
        }

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice created successfully.');
    }

    /**
     * Display / Edit an existing invoice.
     */
    public function show(string $id)
    {
        $invoice = auth()->user()->invoices()->with(['client', 'project', 'items'])->findOrFail($id);
        $clients = Client::orderBy('name')->get();
        $projects = auth()->user()->projects()->orderByDesc('created_at')->get();

        return view('invoices.show', compact('invoice', 'clients', 'projects'));
    }

    /**
     * Update an existing invoice.
     */
    public function update(Request $request, string $id)
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);

        $validated = $request->validate([
            'invoice_number'          => 'required|string|max:50|unique:invoices,invoice_number,' . $invoice->id,
            'client_id'               => 'required|exists:clients,id',
            'project_id'              => 'nullable|exists:projects,id',
            'status'                  => 'required|in:Draft,Issued,Paid,Overdue,Cancelled',
            'issue_date'              => 'required|date',
            'due_date'                => 'nullable|date|after_or_equal:issue_date',
            'payment_terms'           => 'nullable|string|max:100',
            'client_ref'              => 'nullable|string|max:100',
            'our_ref'                 => 'nullable|string|max:100',
            'currency'                => 'required|string|max:10',
            'tax_rate'                => 'nullable|numeric|min:0|max:100',
            'discount_amount'         => 'nullable|numeric|min:0',
            'notes'                   => 'nullable|string',
            'prepared_by_name'        => 'nullable|string|max:100',
            'prepared_by_title'       => 'nullable|string|max:100',
            'approved_by_name'        => 'nullable|string|max:100',
            'approved_by_title'       => 'nullable|string|max:100',
            'payment_bank_name'       => 'nullable|string|max:255',
            'payment_account_name'    => 'nullable|string|max:255',
            'payment_account_number'  => 'nullable|string|max:50',
            'payment_swift_code'      => 'nullable|string|max:20',
            'items'                   => 'required|array|min:1',
            'items.*.title'           => 'required|string|max:255',
            'items.*.description'     => 'nullable|string',
            'items.*.quantity'        => 'required|numeric|min:0.01',
            'items.*.unit_price'      => 'required|numeric|min:0',
        ]);

        // Calculate totals
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += (float) $item['quantity'] * (float) $item['unit_price'];
        }

        $taxRate = (float) ($validated['tax_rate'] ?? 0);
        $taxAmount = $taxRate > 0 ? round($subtotal * ($taxRate / 100), 2) : 0;
        $discountAmount = (float) ($validated['discount_amount'] ?? 0);
        $grandTotal = $subtotal - $discountAmount + $taxAmount;

        $amountInWords = NumberToWords::convert($grandTotal, $validated['currency']);

        $invoice->update([
            'invoice_number'          => $validated['invoice_number'],
            'client_id'               => $validated['client_id'],
            'project_id'              => $validated['project_id'] ?? null,
            'status'                  => $validated['status'],
            'issue_date'              => $validated['issue_date'],
            'due_date'                => $validated['due_date'] ?? null,
            'payment_terms'           => $validated['payment_terms'] ?? null,
            'client_ref'              => $validated['client_ref'] ?? null,
            'our_ref'                 => $validated['our_ref'] ?? null,
            'currency'                => $validated['currency'],
            'subtotal'                => $subtotal,
            'tax_rate'                => $taxRate > 0 ? $taxRate : null,
            'tax_amount'              => $taxAmount > 0 ? $taxAmount : null,
            'discount_amount'         => $discountAmount > 0 ? $discountAmount : null,
            'grand_total'             => $grandTotal,
            'amount_in_words'         => $amountInWords,
            'payment_bank_name'       => $validated['payment_bank_name'] ?? null,
            'payment_account_name'    => $validated['payment_account_name'] ?? null,
            'payment_account_number'  => $validated['payment_account_number'] ?? null,
            'payment_swift_code'      => $validated['payment_swift_code'] ?? null,
            'notes'                   => $validated['notes'] ?? null,
            'prepared_by_name'        => $validated['prepared_by_name'] ?? null,
            'prepared_by_title'       => $validated['prepared_by_title'] ?? null,
            'approved_by_name'        => $validated['approved_by_name'] ?? null,
            'approved_by_title'       => $validated['approved_by_title'] ?? null,
        ]);

        // Replace line items
        $invoice->items()->delete();
        foreach ($validated['items'] as $index => $itemData) {
            $invoice->items()->create([
                'sort_order'  => $index,
                'title'       => $itemData['title'],
                'description' => $itemData['description'] ?? null,
                'quantity'    => (float) $itemData['quantity'],
                'unit_price'  => (float) $itemData['unit_price'],
                'total_price' => round((float) $itemData['quantity'] * (float) $itemData['unit_price'], 2),
            ]);
        }

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', 'Invoice updated successfully.');
    }

    /**
     * Delete a draft invoice.
     */
    public function destroy(string $id)
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);

        if ($invoice->status !== 'Draft') {
            return back()->with('error', 'Only draft invoices can be deleted.');
        }

        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    /**
     * Preview the print-optimized A4 invoice.
     */
    public function preview(string $id)
    {
        $invoice = auth()->user()->invoices()->with(['client', 'project', 'items'])->findOrFail($id);

        // Load company settings for header/logo
        $company = CompanySetting::getMany([
            'company_name', 'company_reg_no', 'company_address',
            'company_phone', 'company_email', 'company_logo',
            'prepared_by_signature', 'approved_by_signature',
        ]);

        return view('invoices.preview', compact('invoice', 'company'));
    }

    /**
     * Update invoice status.
     */
    public function updateStatus(Request $request, string $id)
    {
        $invoice = auth()->user()->invoices()->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Draft,Issued,Paid,Overdue,Cancelled',
        ]);

        $invoice->update(['status' => $validated['status']]);

        return back()->with('success', 'Invoice status updated to ' . $validated['status'] . '.');
    }
}
