<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Module;
use App\Models\Item;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\QtInvoice;
use App\Models\QtInvoiceItem;
use App\Models\PaymentTerm;
use App\Models\Signatory;
use App\Services\Calculation\ProjectEstimationService;
use App\Models\SurveyDefaultItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuotationController extends Controller
{
    public function __construct(private ProjectEstimationService $estimationService)
    {
    }

    public function index(Request $request)
    {
        \Illuminate\Support\Facades\Log::info('QuotationController@index - START');
        $start = microtime(true);

        $modules = Module::with(['categories.services.items.unit'])->get();
        \Illuminate\Support\Facades\Log::info('QuotationController@index - Queries finished in ' . (microtime(true) - $start) . 's');

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
        \Illuminate\Support\Facades\Log::info('QuotationController@index - Mapping finished in ' . (microtime(true) - $start) . 's');

        $prefillProject = null;
        $estimation = null;
        $surveyDefaults = [];
        if ($request->filled('project_id')) {
            $prefillProject = auth()->user()->projects()
                ->with(['client', 'modellingSummary'])
                ->whereKey($request->project_id)
                ->firstOrFail();
            $estimation = $this->estimationService->calculate($prefillProject);
            \Illuminate\Support\Facades\Log::info('QuotationController@index - Prefill Project fetched');
        }

                // Default quotation items for the survey type(s) this project has
        $surveyDefaults = [];
        if ($prefillProject) {
            $prefillProject->loadMissing(['surveyLocations.sbesParameters', 'surveyLocations.droneMappingParameters']);

            $surveyTypes = [];
            if ($prefillProject->surveyLocations->contains(fn ($loc) => ($loc->survey_type === 'sbes' || $loc->survey_type === 'single_beam' || $loc->survey_type === null) && !$loc->droneMappingParameters)) {
                $surveyTypes[] = 'single_beam';
            }
            if ($prefillProject->surveyLocations->contains(fn ($loc) => $loc->survey_type === 'drone' || $loc->survey_type === 'drone_mapping' || $loc->droneMappingParameters)) {
                $surveyTypes[] = 'drone_mapping';
            }

            if (!empty($surveyTypes)) {
                $surveyDefaults = SurveyDefaultItem::with('item')
                    ->whereIn('survey_type', $surveyTypes)
                    ->orderBy('sort_order')
                    ->get()
                    ->unique('item_id')   // an item shared by two survey types is only added once
                    ->filter(fn ($d) => $d->item)
                    ->map(fn ($d) => [
                        'item_id'     => $d->item_id,
                        'module_id'   => $d->item->module_id,
                        'category_id' => $d->item->category_id,
                        'service_id'  => $d->item->service_id,
                        'default_qty' => $d->default_qty,
                        'days_rule'   => $d->days_rule,
                    ])
                    ->values()
                    ->all();
            }
        }

        // People who can be chosen under "Signed by", and the default (first person added)
        $signatories   = Signatory::orderBy('name')->get();
        $defaultSigner = Signatory::orderBy('id')->first();

        \Illuminate\Support\Facades\Log::info('QuotationController@index - Returning View at ' . (microtime(true) - $start) . 's');
        return view('dashboard.home', compact('modules', 'adminModulesTree', 'prefillProject', 'estimation', 'signatories', 'defaultSigner', 'surveyDefaults'));
    }

    public function store(Request $request)
    {
        // 1. Validate payload (start_date & end_date removed)
        $validated = $request->validate([
            'project_id'          => 'nullable|integer|exists:projects,project_Id',
            'client_name'         => 'nullable|string|max:255',
            'client_address'      => 'nullable|string|max:1000',
            'project_name'        => 'required_without:project_id|nullable|string|max:255',
            'number'          => 'nullable|string|max:255',
            'period'              => 'nullable|string',
            'start_date'          => 'nullable|date', // Kept optional for fallback calculation
            'end_date'            => 'nullable|date',   // Kept optional for fallback calculation
            'pic'                 => 'nullable|string',
            'pic_no'           => 'nullable|string|max:255',
            'payment_terms'               => 'nullable|array',
            'payment_terms.*.percentage'  => 'required_with:payment_terms|string',
            'payment_terms.*.condition'   => 'nullable|string|max:255',
            'additional_notes' => 'nullable|string',
            'signatory_id'        => 'nullable|integer|exists:signatories,id',   // Signed by
            'items'               => 'required|array|min:1',
            'items.*.module_id'   => 'required',
            'items.*.item_id'     => 'required_without:items.*.custom_name|nullable|integer|exists:items,item_id',
            'items.*.custom_name' => 'required_without:items.*.item_id|nullable|string|max:255',
            'items.*.category_id' => 'nullable|integer',
            'items.*.unit_qty'    => 'required|integer|min:1',
            'items.*.days'        => 'nullable|integer|min:1',
            'items.*.daily_rate'  => 'nullable|numeric|min:0',
            'items.*.mark_up'     => 'nullable|numeric|min:0',
        ]);

        try {
            $result = DB::transaction(function () use ($validated, $request) {
                $userId = Auth::id();

                // 2. Client Creation / Retrieval
                $clientId = null;
                if ($request->filled('client_name')) {
                    $client = Client::firstOrCreate(
                        ['company_name' => trim($request->client_name)],
                        ['client_address' => $request->client_address, 'created_by' => $userId]
                    );

                    // Company already existed: keep its address up to date
                    if ($request->filled('client_address') && $client->client_address !== $request->client_address) {
                        $client->update([
                            'client_address' => $request->client_address,
                            'updated_by'     => $userId,
                        ]);
                    }

                    $clientId = $client->client_Id;
                }

                // 3. Project Creation / Retrieval
                $projectId = $request->input('project_id');
                $project = null;
                if ($projectId) {
                    $project = auth()->user()->projects()->whereKey($projectId)->firstOrFail();

                    // Save the details typed on this page back to the project.
                    // Only filled boxes are saved, so an empty box never wipes existing data.
                    $changes = ['updated_by' => $userId];

                    if ($request->filled('project_name')) { $changes['name']      = $request->project_name; }
                    if ($request->filled('period'))       { $changes['period']    = $request->period; }
                    if ($request->filled('pic'))          { $changes['pic_name']  = $request->pic; }
                    if ($request->filled('pic_no'))       { $changes['pic_no']    = $request->pic_no; }
                    if ($clientId)                        { $changes['client_Id'] = $clientId; }

                    // Fix the XX placeholder in the project number once the company is known
                    $fixedNumber = Project::fillClientInNumber($project->number, $request->client_name);
                    if ($fixedNumber !== $project->number) {
                        $changes['number'] = $fixedNumber;
                    }

                    // Older project with no number at all: use the one built on the page
                    if (empty($project->number) && $request->filled('number')) {
                        $changes['number'] = $request->number;
                    }

                    $project->update($changes);
                }

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

                if (!$project) {
                    throw new \RuntimeException('A valid project is required to create a quotation.');
                }

                $estimation = $this->estimationService->calculate($project);

                // 4. Calculate Survey Subtotal (before SST)
                $grandTotal = 0.00;
                foreach ($validated['items'] as $item) {
                    $qty    = (int) $item['unit_qty'];
                    $days   = (int) ($item['days'] ?? max(1, ceil($estimation['total_days'])));
                    $catalogItem = !empty($item['item_id']) ? Item::findOrFail($item['item_id']) : null;
                    $rate   = array_key_exists('daily_rate', $item) && $item['daily_rate'] !== null
                        ? (float) $item['daily_rate']
                        : (float) ($catalogItem ? $catalogItem->internal_rate : 0);
                    $markup = isset($item['mark_up']) ? (float) $item['mark_up'] : 0.00;

                    $unitPrice = round($rate * (1 + $markup / 100), 2);
                    $grandTotal += $unitPrice * $qty * $days;
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

                // Totals: survey (with SST) + modelling (if the project has saved Modelling)
                $sstRate        = 0.08;
                $surveySubtotal = round($grandTotal, 2);
                $surveySst      = round($surveySubtotal * $sstRate, 2);
                $modellingTotal = round((float) optional($project->modellingSummary)->grand_total, 2);
                $combinedTotal  = round($surveySubtotal + $surveySst + $modellingTotal, 2);

                // 7. Create Quotation Header
                $quotation = QtInvoice::create([
                    'project_Id'      => $projectId,
                    'quotation_no'    => $quotationNo,
                    'grand_total'     => $combinedTotal,
                    'survey_total'    => $surveySubtotal,
                    'modelling_total' => $modellingTotal,
                    'survey_distance_nm' => $estimation['distance_nm'],
                    'survey_hours' => $estimation['survey_hours'],
                    'survey_duration_days' => $estimation['total_days'],
                    'payment_terms'=> $paymentTermsText,
                    'additional_notes' => $validated['additional_notes'] ?? null,
                    'signatory_id' => $validated['signatory_id'] ?? null,   // Signed by
                    'created_by'   => $userId,
                ]);

                // 8. Create Line Items
                foreach ($validated['items'] as $item) {
                    $qty    = (int) $item['unit_qty'];
                    $days   = (int) ($item['days'] ?? max(1, ceil($estimation['total_days'])));
                    $catalogItem = !empty($item['item_id']) ? Item::findOrFail($item['item_id']) : null;
                    $rate   = array_key_exists('daily_rate', $item) && $item['daily_rate'] !== null
                        ? (float) $item['daily_rate']
                        : (float) $catalogItem->internal_rate;
                    $markup = isset($item['mark_up']) ? (float) $item['mark_up'] : 0.00;

                    $unitPrice = round($rate * (1 + $markup / 100), 2);
                    $lineTotal = round($unitPrice * $qty * $days, 2);

                    QtInvoiceItem::create([
                        'quotation_id'     => $quotation->quotation_Id,
                        'module_id'        => $item['module_id'],
                        'catalog_item_id'  => $item['item_id'] ?? null,
                        'custom_item_name' => empty($item['item_id']) ? trim($item['custom_name']) : null,
                        'category_id'      => empty($item['item_id']) ? ($item['category_id'] ?? null) : null,
                        'unit_qty'         => $qty,
                        'days'             => $days,
                        'daily_rate'       => $rate,
                        'mark_up'          => $markup,
                        'line_total'       => $lineTotal,
                    ]);
                }

                // 9. Create structured Payment Term rows
                $finalTotal = $combinedTotal;   // survey + SST + modelling

                foreach (($validated['payment_terms'] ?? []) as $index => $term) {
                    $percentageValue = (float) str_replace('%', '', $term['percentage'] ?? '0');

                    PaymentTerm::create([
                        'quotation_Id' => $quotation->quotation_Id,
                        'name'         => 'Payment ' . ($index + 1),
                        'percentage'   => $percentageValue,
                        'condition'    => $term['condition'] ?? null,
                        'amount'       => round($finalTotal * ($percentageValue / 100), 2),   // percentage of the full amount
                        'created_by'   => $userId,
                    ]);
                }

                return [
                    'quotation_id'   => $quotation->quotation_Id,
                    'quotation_no'   => $quotationNo,
                    'project_number' => $lockedProject->number,
                ];
            });

            return response()->json([
                'success'        => true,
                'quotation_id'   => $result['quotation_id'],
                'quotation_no'   => $result['quotation_no'],
                'project_number' => $result['project_number'],
                'message'        => 'Quotation successfully saved to database!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(\Illuminate\Http\Request $request, $id)
    {
        $quotation = QtInvoice::with([
            'items',
            'items.category',
            'items.catalogItem.category',
            'items.catalogItem.service',
            'project.client',
            'paymentTerms',
            'signatory'
        ])
            ->where('quotation_Id', $id)
            ->firstOrFail();

        // Chosen signer; older quotations (no signer chosen) fall back to the first person in the list
        $signer = $quotation->signatory ?? Signatory::orderBy('id')->first();

        if ($request->has('print')) {
            return redirect()->route('quotations.download', $id);
        }

        return view('dashboard.view', compact('quotation', 'signer'));
    }

    public function download($id)
    {
        $quotation = QtInvoice::with([
            'items',
            'items.catalogItem.category',
            'items.catalogItem.service',
            'project.client',
            'paymentTerms',
            'signatory'
        ])
            ->where('quotation_Id', $id)
            ->firstOrFail();

        $signer = $quotation->signatory ?? Signatory::orderBy('id')->first();

        $pdf = Pdf::loadView('dashboard.quotation-pdf', compact('quotation', 'signer'))
                  ->setPaper('A4', 'portrait');

        $safeQuotationNo = str_replace(['/', '\\'], '-', $quotation->quotation_no ?? 'Unknown');
        return $pdf->download('Quotation_' . $safeQuotationNo . '.pdf');
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
        // withTrashed() so deleted projects are counted too (same rule as ProjectController@store)
        $lastRunning = Project::withTrashed()
            ->whereNotNull('number')
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

        // SST is only on the survey part; grand_total already includes survey + SST + modelling
        $sstAmount  = round($quotation->survey_total * 0.08, 2);
        $finalTotal = $quotation->grand_total;

        return view('dashboard.tbinvoice', compact('quotation', 'sstAmount', 'finalTotal'));
    }
}