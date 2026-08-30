<?php

namespace App\Services\Map;

use App\Models\SurveyLocation;
use App\Models\ProjectBoundary;

class BoundaryService
{
    /**
     * Store or update a project's boundaries
     */
    public function saveBoundaries(SurveyLocation $survey, array $boundariesData)
    {
        // Delete old boundaries to replace with new ones
        $survey->boundaries()->delete();

        $saved = [];
        foreach ($boundariesData as $boundaryData) {
            $saved[] = $survey->boundaries()->create([
                'project_id' => $survey->project_id,
                'geometry' => $boundaryData['geometry'],
                'area' => $boundaryData['area'] ?? 0,
                'perimeter' => $boundaryData['perimeter'] ?? 0,
                'vertex_count' => $boundaryData['vertex_count'] ?? 0,
                'centroid' => $boundaryData['centroid'] ?? null,
            ]);
        }
        return $saved;
    }
}
