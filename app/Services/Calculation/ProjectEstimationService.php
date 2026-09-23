<?php

namespace App\Services\Calculation;

use App\Models\Project;

class ProjectEstimationService
{
    public function calculate(Project $project): array
    {
        $project->loadMissing(['surveyLocations.sbesParameters', 'surveyLocations.droneMappingParameters']);

        $distanceNm = 0.0;
        $surveyHours = 0.0;
        $executionDays = 0.0;

        $droneService = new \App\Services\Calculation\DroneMapping\DroneEstimationService();
        $droneResult = $droneService->calculate($project);

        foreach ($project->surveyLocations as $location) {
            $sbesParams = $location->sbesParameters;
            if ($sbesParams) {
                $distance = (float) ($sbesParams->total_distance_nm ?? 0);
                $speed = (float) ($sbesParams->survey_speed_knots ?? 0);
                $hoursPerDay = (float) ($sbesParams->working_hours_per_day ?? 8.0);
                if ($hoursPerDay <= 0) {
                    $hoursPerDay = 8.0;
                }

                $hours = $speed > 0 ? $distance / $speed : 0.0;
                $days = $hoursPerDay > 0 ? $hours / $hoursPerDay : 0.0;

                $distanceNm += $distance;
                $surveyHours += $hours;
                $executionDays += $days;
            }
        }

        $weatherDays = (float) ($project->weather_days ?? 0);
        $modDemodDays = (float) ($project->mod_demod_days ?? 0);
        $patchTestDays = (float) ($project->patch_test_days ?? 0);

        // Include SBES and Drone results
        return [
            'distance_nm' => round($distanceNm, 4),
            'survey_hours' => round($surveyHours, 2),
            'execution_days' => round($executionDays, 2),
            'weather_days' => round($weatherDays, 2),
            'mod_demod_days' => round($modDemodDays, 2),
            'patch_test_days' => round($patchTestDays, 2),
            'total_days' => round($executionDays + $weatherDays + $modDemodDays + $patchTestDays, 2),
            
            // Drone metrics
            'drone_total_flight_distance_m' => $droneResult['drone_total_flight_distance_m'] ?? 0,
            'drone_survey_hours' => $droneResult['drone_survey_hours'] ?? 0,
            'drone_total_images' => $droneResult['drone_total_images'] ?? 0,
            'drone_total_days' => $droneResult['total_days'] ?? 0,
        ];
    }
}
