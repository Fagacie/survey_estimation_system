<?php

namespace App\Services\Map;

use App\Models\Project;

class SurveyStatisticsService
{
    /**
     * Calculate core statistics based on current survey lines and boundary
     */
    public function calculateStatistics(\App\Models\Project $project, \App\Models\SurveyLocation $surveyLocation, array $data = []): array
    {
        $lines = $surveyLocation->surveyLines;
        $boundaries = $surveyLocation->boundaries;

        // All individual line lengths are stored in METERS (from Turf.js in the browser)
        $totalLengthMeters = $lines->sum('length_meters');
        
        // If the frontend provides an explicit formula override, use it
        if (isset($data['override_total_distance_meters']) && $data['override_total_distance_meters'] > 0) {
            $totalLengthMeters = $data['override_total_distance_meters'];
        }

        $lineCount = $lines->count();
        $averageLengthMeters = $lineCount > 0 ? $totalLengthMeters / $lineCount : 0;
        $longestMeters = $lines->max('length_meters') ?? 0;
        $shortestMeters = $lines->min('length_meters') ?? 0;

        // Convert total distance to Nautical Miles:
        // Step 1: meters -> kilometers (divide by 1000)
        // Step 2: kilometers -> NM (divide by 1.852)
        $totalDistanceNM = ($totalLengthMeters / 1000) / 1.852;

        $adcpCount = $surveyLocation->surveyLines()->where('type', 'adcp_marker')->count();

        // Save the total distance to sbes_parameters for cost calculation
        $surveyLocation->sbesParameters()->updateOrCreate(
            ['survey_location_id' => $surveyLocation->id],
            [
                'project_id' => $project->id,
                'total_distance_nm' => round($totalDistanceNM, 4)
            ]
        );

        return [
            'total_distance'  => round($totalDistanceNM, 4),  // in NM
            'line_count'      => $lineCount,
            'adcp_count'      => $adcpCount,
            'average_length'  => round($averageLengthMeters, 2), // in meters
            'longest_line'    => round($longestMeters, 2),
            'shortest_line'   => round($shortestMeters, 2),
            'boundary_area'   => round($boundaries->sum('area'), 2),
            'boundary_perimeter' => round($boundaries->sum('perimeter'), 2),
        ];
    }
}
