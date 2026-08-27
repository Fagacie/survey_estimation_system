<?php

namespace App\Services\Calculation;

use App\Models\Project;

class SurveyParameterService
{
    /**
     * Store or update a project's survey parameters, then trigger cost recalculation.
     */
    public function saveParameters(Project $project, array $data): void
    {
        if (isset($data['sbes'])) {
            $project->sbesParameters()->updateOrCreate(
                ['project_id' => $project->id],
                $data['sbes']
            );
        }

        // Trigger cost recalculation using the centralized service
        try {
            $project->refresh(); // Reload sbesParameters
            $costService = app(CostEstimationService::class);
            $result = $costService->calculate($project);

            // Only auto-persist if the estimation doesn't exist yet or was auto-calculated
            $existing = $project->costEstimation;
            if (!$existing || $existing->status === 'Calculated') {
                $costService->persist(
                    $project,
                    $result['cost_items'],
                    $result['total_cost'],
                    'Calculated',
                    $result['duration']
                );
            }
        } catch (\Exception $e) {
            // Calculation can fail if map lines aren't drawn yet — that's OK
            \Log::warning('Auto cost calculation skipped: ' . $e->getMessage());
        }
    }
}
