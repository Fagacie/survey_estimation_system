<?php

namespace App\Services\Map;

use App\Models\SurveyLocation;

class SurveyLineService
{
    /**
     * Replace all survey lines for a project.
     * This receives features from the map (manual or generated)
     */
    public function saveLines(SurveyLocation $survey, array $geojson, bool $isGenerated = false, array $adcpMarkers = []): void
    {
        $survey->surveyLines()->delete();
        
        foreach ($geojson['features'] as $index => $feature) {
            $lineType = $this->canonicalLineType($feature['properties']['line_type'] ?? null) ?? 'main';
            $length = $feature['properties']['length'] ?? 0;
            $bearing = $feature['properties']['bearing'] ?? null;

            $survey->surveyLines()->create([
                'project_id' => $survey->project_id,
                'type' => $lineType,
                'line_number' => $index + 1,
                'geometry' => $feature,
                'length_meters' => $length,
                'bearing' => $bearing,
            ]);
        }

        if (isset($adcpMarkers['features'])) {
            foreach ($adcpMarkers['features'] as $index => $feature) {
                $survey->surveyLines()->create([
                    'project_id' => $survey->project_id,
                    'type' => 'adcp_marker',
                    'line_number' => $index + 1,
                    'geometry' => $feature,
                    'length_meters' => 0,
                ]);
            }
        }
    }

    private function canonicalLineType(?string $type): ?string
    {
        return in_array($type, ['main', 'cross', 'reference', 'adcp_marker'], true) ? $type : null;
    }
}
