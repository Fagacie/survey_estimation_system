<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\CostItem;
use App\Services\Calculation\CostEstimationService;
use Illuminate\Http\Request;

class CostEstimationController extends Controller
{
    protected CostEstimationService $costService;

    public function __construct(CostEstimationService $costService)
    {
        $this->costService = $costService;
    }

    /**
     * Display the cost estimation page for a project.
     */
    public function show(string $id)
    {
        $project = auth()->user()->projects()->findOrFail($id);
        $project->load('sbesParameters', 'boundaries', 'costEstimation.items.costRate');

        // Always calculate the latest engineering figures from the centralized service
        $calcResult = $this->costService->calculate($project);
        $duration = $calcResult['duration'];

        // Check if the user has a saved estimation with manual edits
        $estimation = $project->costEstimation;

        if ($estimation && $estimation->items->count() > 0) {
            // Use the saved items (preserves manual user edits)
            $lineItems = $estimation->items;
        } else {
            // Generate draft line items from the auto-calculation
            $lineItems = collect($calcResult['cost_items'])->map(fn($item) => new CostItem($item));
        }

        // Group items by category for the view
        $groupedItems = collect($lineItems)->groupBy('category');

        return view('projects.cost', compact(
            'project',
            'duration',
            'lineItems',
            'groupedItems',
            'estimation'
        ));
    }

    /**
     * Save the cost estimation (accepts user-edited items).
     */
    public function store(Request $request, string $id)
    {
        $project = auth()->user()->projects()->findOrFail($id);

        $request->validate([
            'items'               => 'required|array',
            'items.*.category'    => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity'    => 'required|numeric|min:0',
            'items.*.unit_rate'   => 'required|numeric|min:0',
        ]);

        // Build the items array
        $items = [];
        $totalCost = 0;

        foreach ($request->items as $itemData) {
            $qty   = (float) $itemData['quantity'];
            $rate  = (float) $itemData['unit_rate'];
            $price = $qty * $rate;
            $totalCost += $price;

            $items[] = [
                'cost_rate_id' => $itemData['cost_rate_id'] ?? null,
                'category'     => $itemData['category'],
                'description'  => $itemData['description'],
                'quantity'     => $qty,
                'unit_rate'    => $rate,
                'total_price'  => $price,
            ];
        }

        // Get the current duration for storage
        $calcResult = $this->costService->calculate($project);

        // Save as 'Manual' so auto-recalculation doesn't overwrite
        $this->costService->persist($project, $items, $totalCost, 'Manual', $calcResult['duration']);

        return redirect()->route('projects.cost.show', $project->id)
            ->with('success', 'Cost Estimation saved successfully.');
    }

    /**
     * Force recalculation from master rates, discarding manual edits.
     */
    public function recalculate(string $id)
    {
        $project = auth()->user()->projects()->findOrFail($id);

        $calcResult = $this->costService->calculate($project);

        $this->costService->persist(
            $project,
            $calcResult['cost_items'],
            $calcResult['total_cost'],
            'Calculated',
            $calcResult['duration']
        );

        return redirect()->route('projects.cost.show', $project->id)
            ->with('success', 'Cost estimation recalculated from master rates.');
    }
}
