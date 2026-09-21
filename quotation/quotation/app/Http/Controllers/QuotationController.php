<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Module;
use App\Models\Project;
use App\Models\QtInvoice;
use App\Models\QtInvoiceItem;
use App\Models\PaymentTerm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function index()
    {
        $modules = Module::with(['categories.services.items'])->get();

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

        return view('dashboard.home', compact('modules', 'adminModulesTree'));
    }

    public function store(Request $request)
    {
        // 1. Validate payload (start_date & end_date removed)
        $validated = $request->validate([
            'project_id'          => 'nullable|integer',
            'client_name'         => 'nullable|string|max:255',
            'client_address'      => 'nullable|string|max:1000',
            'project_name'        => 'nullable|string|max:255',
            'number'          => 'nullable|string|max:255',
            'period'              => 'nullable|string',
            'start_date'          => 'nullable|date', // Kept optional for fallback calculation
            'end_date'            => 'nullable|date',   // Kept optional for fallback calculation
            'pic'                 => 'nullable|string',
            'pic_no'           => 'nullable|integer',
            'payment_terms'               => 'nullable|array',
            'payment_terms.*.percentage'  => 'required_with:payment_terms|string',
            'payment_terms.*.condition'   => 'nullable|string|max:255',
            'additional_notes' => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.module_id'   => 'required',
            'items.*.item_id'      => 'required|integer|exists:items,item_id',
            'items.*.unit_qty'    => 'required|integer|min:1',
            'items.*.days'        => 'required|integer|min:1',
            'items.*.daily_rate'  => 'required|numeric|min:0',
            'items.*.mark_up'     => 'nullable|numeric|min:0',
        ]);

        try {
            $result = DB::transaction(function () use ($validated, $request) {
                $userId = Auth::id();

                // 2. Client Creation / Retrieval
                $clientId = null;
                if ($request->filled('client_name')) {
                    $client = Client::firstOrCreate(
                        ['company_name' => $request->client_name],
                        ['client_address' => $request->client_address],
                        ['created_by' => $userId]
                    );
                    $clientId = $client->client_Id;
                }

                // 3. Project Creation / Retrieval
                $projectId = $request->input('project_id');
                if (!$projectId && $request->filled('project_name')) {

                    // Determine period string (uses payload period or calculates from dates if present)
                    $periodValue = $request->input('period');
                    if (!$periodValue && $request->filled('start_date') && $request->filled('end_date')) {
                        $start = Carbon::parse($request->start_date);
                        $end   = Carbon::parse($request->end_date);
                        $periodValue = ($start->diffInDays($end) + 1) . ' Days';
                    }

                    $project = Project::create([
                        'client_Id'  => $clientId,
                        'number'     => $request->number,   
                        'name'       => $request->project_name,
                        'period'     => $periodValue,
                        'pic_name'   => $request->pic,
                        'pic_no'  => $request->pic_no ?? 1,
                        'created_by' => $userId,
                    ]);
                    $projectId = $project->project_Id;
                }

                // 4. Calculate Grand Total
                $grandTotal = 0.00;
                foreach ($validated['items'] as $item) {
                    $qty    = (int) $item['unit_qty'];
                    $days   = (int) $item['days'];
                    $rate   = (float) $item['daily_rate'];
                    $markup = isset($item['mark_up']) ? (float) $item['mark_up'] : 0.00;

                    $lineBase = $qty * $days * $rate;
                    $grandTotal += $lineBase + ($lineBase * ($markup / 100));
                }

                // 5. Generate Project-Scoped Quotation Number
                $year = now()->year;

                // Lock the PROJECT row so two saves for the same project
                // can't both compute the same running number at once
                $lockedProject = Project::where('project_Id', $projectId)
                    ->lockForUpdate()
                    ->first();

                $existingCount = QtInvoice::where('project_Id', $projectId)
                    ->whereYear('created_at', $year)
                    ->count();

                $runningNumber = $existingCount + 1;

                $quotationNo = sprintf('%s-QUO/%d/%03d', $lockedProject->number, $year, $runningNumber);

                // 6. Build backward-compatible text summary for the old text column
                $paymentTermsText = null;
                if (!empty($validated['payment_terms'])) {
                    $paymentTermsText = collect($validated['payment_terms'])
                        ->map(function ($term, $index) {
                            $label      = 'Payment ' . ($index + 1);
                            $percentage = $term['percentage'] ?? '';
                            $condition  = $term['condition'] ?? '';
                            return "{$label} : {$percentage} - {$condition}";
                        })
                        ->implode("\n");
                }

                // 7. Create Quotation Header
                $quotation = QtInvoice::create([
                    'project_Id'   => $projectId,
                    'quotation_no' => $quotationNo,
                    'grand_total'  => $grandTotal,
                    'payment_terms'=> $paymentTermsText,   // <-- CHANGED: was $validated['payment_terms'] ?? null
                    'additional_notes' => $validated['additional_notes'] ?? null,
                    'created_by'   => $userId,
                ]);

                // 8. Create Line Items
                foreach ($validated['items'] as $item) {
                    $qty    = (int) $item['unit_qty'];
                    $days   = (int) $item['days'];
                    $rate   = (float) $item['daily_rate'];
                    $markup = isset($item['mark_up']) ? (float) $item['mark_up'] : 0.00;

                    $lineBase  = $qty * $days * $rate;
                    $lineTotal = $lineBase + ($lineBase * ($markup / 100));

                    QtInvoiceItem::create([
                        'quotation_id' => $quotation->quotation_Id,
                        'module_id'    => $item['module_id'],
                        'catalog_item_id' => $item['item_id'],
                        'unit_qty'     => $qty,
                        'days'         => $days,
                        'daily_rate'   => $rate,
                        'mark_up'      => $markup,
                        'line_total'   => $lineTotal,
                    ]);
                }

                // 9. Create structured Payment Term rows (NEW)
                $sstRate = 0.08;
                $finalTotal = $grandTotal + ($grandTotal * $sstRate);   // grand total INCLUDING SST

                foreach (($validated['payment_terms'] ?? []) as $index => $term) {
                    $percentageValue = (float) str_replace('%', '', $term['percentage'] ?? '0');

                    PaymentTerm::create([
                        'quotation_Id' => $quotation->quotation_Id,
                        'name'         => 'Payment ' . ($index + 1),
                        'percentage'   => $percentageValue,
                        'condition'    => $term['condition'] ?? null,
                        'amount'       => round($finalTotal * ($percentageValue / 100), 2),   // now uses post-SST total
                        'created_by'   => $userId,
                    ]);
                }

                return [
                    'quotation_id' => $quotation->quotation_Id,
                    'quotation_no' => $quotationNo,
                ];
            });

        return response()->json([
            'success'      => true,
            'quotation_id' => $result['quotation_id'],
            'quotation_no' => $result['quotation_no'],
            'message'      => 'Quotation successfully saved to database!'
        ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        $quotation = QtInvoice::with([
            'items', 
            'items.catalogItem.category',
            'items.catalogItem.service',
            'project.client',
            'paymentTerms'  
        ])
            ->where('quotation_Id', $id)
            ->firstOrFail();

        return view('dashboard.view', compact('quotation'));
    }

    public function history()
    {
        $quotations = QtInvoice::with([
            'items.module',
            'items.catalogItem.category',
            'items.catalogItem.service',
            'project.client'
        ])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return view('dashboard.history', compact('quotations'));
    }

    public function nextNumber()
    {
        // Running number is global across ALL projects, regardless of type/client.
        // Number format is always: EHS/{TYPE}/{CLIENT_CODE}/{RUNNING} e.g. EHS/CP/TGAS/007
        $lastRunning = Project::whereNotNull('number')
            ->where('number', 'like', '%/%')
            ->get()
            ->map(function ($project) {
                $parts = explode('/', $project->number);
                $last  = end($parts);
                return is_numeric($last) ? (int) $last : 0;
            })
            ->max();

        $nextNumber = ($lastRunning ?? 0) + 1;

        return response()->json([
            'next_number' => $nextNumber,
        ]);
    }

    public function showInvoice($id)
    {
        $quotation = QtInvoice::with('paymentTerms.invoiceCopy')->findOrFail($id);

        $sstRate = 0.08;
        $sstAmount = $quotation->grand_total * $sstRate;
        $finalTotal = $quotation->grand_total + $sstAmount;

        return view('dashboard.tbinvoice', compact('quotation', 'sstAmount', 'finalTotal'));

    }
}