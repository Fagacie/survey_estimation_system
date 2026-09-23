<?php

namespace App\Services\Calculation\DroneMapping;

use App\Models\Project;

class DroneEstimationService
{
    /**
     * Calculates the drone mapping estimation for a project based on its locations.
     * Note: Drone mapping estimations are purely distance/speed based. Weather and 
     * working hours are not applied to the drone itself, only the raw duration.
     * 
     * @param Project $project
     * @return array
     */
    public function calculate(Project $project): array
    {
        // Load the drone mapping parameters if not already loaded
        $project->loadMissing('surveyLocations.droneMappingParameters');

        $totalFlightDistance = 0.0;
        $totalSurveyHours = 0.0;
        $totalImages = 0;

        foreach ($project->surveyLocations as $location) {
            $parameters = $location->droneMappingParameters;
            
            if (!$parameters) {
                continue;
            }

            // Fallback to calculated duration if not strictly provided
            $distance = (float) ($parameters->total_flight_distance_m ?? 0);
            $speed = (float) ($parameters->speed_ms ?? 10.0);
            
            // If the duration was already calculated and saved from the frontend, use it.
            // Otherwise, calculate it: duration in hours = (distance / speed) / 3600
            $durationHours = $parameters->estimated_duration_hours;
            if ($durationHours === null) {
                $durationHours = $speed > 0 ? ($distance / $speed) / 3600 : 0.0;
            }

            $images = (int) ($parameters->total_images ?? 0);

            $totalFlightDistance += $distance;
            $totalSurveyHours += $durationHours;
            $totalImages += $images;
        }

        // Return the drone-specific calculated metrics
        return [
            'drone_total_flight_distance_m' => round($totalFlightDistance, 2),
            'drone_survey_hours' => round($totalSurveyHours, 4),
            'drone_total_images' => $totalImages,
            // We pass through these for compatibility if the parent service expects them,
            // though they might not be utilized the same way.
            'execution_days' => round($totalSurveyHours / 8, 2), // Assuming 8hr workday for reporting
            'total_days' => round($totalSurveyHours / 8, 2),
        ];
    }
}
