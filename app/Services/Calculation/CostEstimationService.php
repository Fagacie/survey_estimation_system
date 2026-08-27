<?php

namespace App\Services\Calculation;

use App\Models\Project;
use App\Models\CostRate;
use App\Models\CostEstimation;

class CostEstimationService
{
    /**
     * Calculate all time and cost estimations for an SBES project.
     * This is the SINGLE SOURCE OF TRUTH for all calculations.
     *
     * @return array{duration: array, cost_items: array, total_cost: float}
     */
    public function calculate(Project $project): array
    {
        $params = $project->sbesParameters;

        // ─── TIME CALCULATIONS ───────────────────────────────────
        $distanceNm  = (float) ($params->total_distance_nm ?? 0);
        $speedKnots  = (float) ($params->survey_speed_knots ?? 5.0);
        $hoursPerDay = (float) ($params->working_hours_per_day ?? 8.0);

        $weatherDays  = (float) ($params->weather_days ?? 0);
        $modDemodDays = (float) ($params->mod_demod_days ?? 0);
        $patchDays    = (float) ($params->patch_test_days ?? 0);

        // FORMULA 1: Survey Hours = Distance (NM) / Speed (knots)
        $surveyHours = $speedKnots > 0 ? $distanceNm / $speedKnots : 0;

        // FORMULA 2: Execution Days = Survey Hours / Working Hours Per Day
        $executionDays = $hoursPerDay > 0 ? $surveyHours / $hoursPerDay : 0;

        // FORMULA 3: Total Duration = Execution + Weather + MOB/DEMOB + Patch Test
        $totalDays = $executionDays + $weatherDays + $modDemodDays + $patchDays;

        $duration = [
            'distance_nm'        => round($distanceNm, 4),
            'speed_knots'        => $speedKnots,
            'hours_per_day'      => $hoursPerDay,
            'survey_hours'       => round($surveyHours, 2),
            'execution_days'     => round($executionDays, 2),
            'weather_days'       => $weatherDays,
            'mod_demod_days'     => $modDemodDays,
            'patch_test_days'    => $patchDays,
            'total_days'         => round($totalDays, 2),
        ];

        // ─── COST CALCULATIONS ───────────────────────────────────
        $masterRates = CostRate::where('is_active', true)->orderBy('category')->orderBy('id')->get();

        $costItems = [];
        $totalCost = 0;

        foreach ($masterRates as $rate) {
            $qty = $this->resolveQuantity($rate, $totalDays);

            if ($qty <= 0) {
                continue;
            }

            $unitRate   = (float) $rate->default_rate;
            $totalPrice = $qty * $unitRate;
            $totalCost += $totalPrice;

            $costItems[] = [
                'cost_rate_id' => $rate->id,
                'category'     => $rate->category ?? 'General',
                'description'  => $rate->name,
                'quantity'     => $qty,
                'unit_rate'    => $unitRate,
                'total_price'  => $totalPrice,
            ];
        }

        return [
            'duration'   => $duration,
            'cost_items' => $costItems,
            'total_cost' => $totalCost,
        ];
    }

    /**
     * Persist a calculated (or manually adjusted) cost estimation to the database.
     *
     * @param Project $project
     * @param array   $items       Array of item arrays with keys: cost_rate_id, category, description, quantity, unit_rate, total_price
     * @param float   $totalCost
     * @param string  $status      'Calculated' for auto, 'Manual' for user-edited
     * @param array   $duration    The duration breakdown to store as JSON
     * @return CostEstimation
     */
    public function persist(Project $project, array $items, float $totalCost, string $status = 'Calculated', array $duration = []): CostEstimation
    {
        $estimation = $project->costEstimation()->updateOrCreate(
            ['project_id' => $project->id],
            [
                'total_cost'         => $totalCost,
                'currency'           => 'MYR',
                'status'             => $status,
                'duration_breakdown'  => !empty($duration) ? $duration : null,
            ]
        );

        // Clear old items and save new ones
        $estimation->items()->delete();

        foreach ($items as $item) {
            $estimation->items()->create([
                'cost_rate_id' => $item['cost_rate_id'] ?? null,
                'category'     => $item['category'],
                'description'  => $item['description'],
                'quantity'     => (float) $item['quantity'],
                'unit_rate'    => (float) $item['unit_rate'],
                'total_price'  => (float) $item['quantity'] * (float) $item['unit_rate'],
            ]);
        }

        return $estimation->fresh(['items']);
    }

    /**
     * Resolve the quantity for a cost rate item based on its unit type.
     */
    protected function resolveQuantity(CostRate $rate, float $totalDays): float
    {
        if (strtolower($rate->unit_type) === 'lump sum') {
            return 1.0;
        }

        if (strtolower($rate->unit_type) === 'per day') {
            return $totalDays > 0 ? ceil($totalDays) : 0;
        }

        return 1.0;
    }
}
