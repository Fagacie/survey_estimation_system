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
            $length = (float) ($feature['properties']['length'] ?? 0);
            if ($length <= 0 && isset($feature['geometry']['coordinates']) && is_array($feature['geometry']['coordinates'])) {
                $length = $this->calculateCoordinatesLength($feature['geometry']['coordinates']);
            }
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

    private function calculateCoordinatesLength(array $coords): float
    {
        $total = 0.0;
        // LineString coordinates: list of [lng, lat]
        if (isset($coords[0]) && is_array($coords[0]) && !is_array($coords[0][0])) {
            for ($i = 0; $i < count($coords) - 1; $i++) {
                $total += $this->haversineMeters($coords[$i][1], $coords[$i][0], $coords[$i + 1][1], $coords[$i + 1][0]);
            }
        }
        return round($total, 2);
    }

    private function haversineMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371000; // in meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function canonicalLineType(?string $type): ?string
    {
        return in_array($type, ['main', 'cross', 'reference', 'adcp_marker'], true) ? $type : null;
    }
}

