<?php

namespace App\Services\Map;

use App\Models\Project;

class SurveyLineService
{
    /**
     * Replace all survey lines for a project.
     * This receives features from the map (manual or generated)
     */
    public function saveLines(Project $project, array $geojson, bool $isGenerated = false, array $adcpMarkers = []): void
    {
        $project->surveyLines()->delete();

        foreach ($geojson['features'] as $index => $feature) {
            $length = $feature['properties']['length'] ?? 0;
            $bearing = $feature['properties']['bearing'] ?? null;
            // Read line_type from feature properties; fallback to $isGenerated for backward compatibility
            $type = $feature['properties']['line_type'] ?? ($isGenerated ? 'generated' : 'main');

            $project->surveyLines()->create([
                'type' => $type,
                'line_number' => $index + 1,
                'geometry' => $feature,
                'length_meters' => $length,
                'bearing' => $bearing,
            ]);
        }

        if (isset($adcpMarkers['features'])) {
            foreach ($adcpMarkers['features'] as $index => $feature) {
                $project->surveyLines()->create([
                    'type' => 'adcp_marker',
                    'line_number' => $index + 1,
                    'geometry' => $feature,
                    'length_meters' => 0,
                ]);
            }
        }
    }
}

