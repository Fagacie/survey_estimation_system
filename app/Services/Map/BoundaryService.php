<?php

namespace App\Services\Map;

use App\Models\Project;
use App\Models\ProjectBoundary;

class BoundaryService
{
    /**
     * Store or update a project's boundaries
     */
    public function saveBoundaries(Project $project, array $boundariesData)
    {
        // Delete old boundaries to replace with new ones
        $project->boundaries()->delete();

        $saved = [];
        foreach ($boundariesData as $boundaryData) {
            $saved[] = $project->boundaries()->create([
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
