<?php

namespace App\Services\Map;

use App\Models\Project;
use App\Models\SurveyLocation;
use Illuminate\Support\Facades\DB;

class MapService
{
    protected $boundaryService;
    protected $surveyLineService;
    protected $generationService;
    protected $statsService;
    
    public function __construct(
        BoundaryService $boundaryService,
        SurveyLineService $surveyLineService,
        SurveyLineGenerationService $generationService,
        SurveyStatisticsService $statsService
    ) {
        $this->boundaryService = $boundaryService;
        $this->surveyLineService = $surveyLineService;
        $this->generationService = $generationService;
        $this->statsService = $statsService;
    }

    /**
     * Process the entire map payload from the frontend
     */
    public function processMapData(Project $project, SurveyLocation $survey, array $data): array
    {
        return DB::transaction(function () use ($project, $survey, $data) {
            \Log::info('MapService step 1: Save Boundaries');
            // 1. Save Boundaries if present
            if (isset($data['boundaries']) && is_array($data['boundaries'])) {
                $this->boundaryService->saveBoundaries($survey, $data['boundaries']);
            }

            \Log::info('MapService step 2: Save Lines');
            // 2. Save Lines and Markers if present
            if (isset($data['lines']) && isset($data['lines']['features'])) {
                $isGenerated = $data['is_generated'] ?? false;
                $adcpMarkers = $data['adcp_markers'] ?? [];
                $this->surveyLineService->saveLines($survey, $data['lines'], $isGenerated, $adcpMarkers);
            }

            \Log::info('MapService step 3: Save Settings');
            // 3. Save Generation Settings if present
            if (isset($data['generation_settings']) && !empty($data['generation_settings'])) {
                $this->generationService->saveSettings($survey, $data['generation_settings']);
            }

            \Log::info('MapService step 4: Calc Stats');
            // 4. Calculate new stats
            $stats = $this->statsService->calculateStatistics($project, $survey, $data);



            \Log::info('MapService step 6: Complete');
            // 6. Return new stats
            return $stats;
        });
    }
}
