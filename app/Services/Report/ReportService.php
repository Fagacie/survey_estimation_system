<?php

namespace App\Services\Report;

use App\Models\Project;
use App\Models\Report;
use App\Services\Calculation\CostEstimationService;

class ReportService
{
    protected CostEstimationService $costService;

    public function __construct(CostEstimationService $costService)
    {
        $this->costService = $costService;
    }

    /**
     * Compile all project data for the full survey report.
     */
    public function compileReportData(Project $project): array
    {
        $project->load([
            'boundaries',
            'surveyLines',
            'surveyLocations.sbesParameters',
            'costEstimation.items.costRate',
            'surveyGenerationSetting',
            'user',
        ]);

        // Get the latest cost calculation
        $calcResult = $this->costService->calculate($project);

        // Use saved estimation if exists, otherwise use calculated
        $estimation = $project->costEstimation;
        $costItems  = $estimation ? $estimation->items : collect();

        // Group cost items by category
        $groupedCostItems = $costItems->groupBy('category');

        // Survey line statistics
        $mainLines = $project->surveyLines->filter(function ($line) {
            return $this->canonicalLineType($line) === 'main';
        });
        $crossLines = $project->surveyLines->filter(function ($line) {
            return $this->canonicalLineType($line) === 'cross';
        });

        $totalLengthMeters = $project->surveyLines->sum('length_meters');
        $mainLengthMeters  = $mainLines->sum('length_meters');
        $crossLengthMeters = $crossLines->sum('length_meters');

        // Boundary statistics
        $totalArea      = $project->boundaries->sum('area');
        $totalPerimeter = $project->boundaries->sum('perimeter');
        $totalVertices  = $project->boundaries->sum('vertex_count');

        // Generation settings
        $genSettings = $project->surveyGenerationSetting;

        // Per-location breakdown
        $locationsData = [];
        $globalDistanceNm = 0;
        foreach ($project->surveyLocations as $location) {
            $locLines = $project->surveyLines->where('survey_location_id', $location->id);
            $locBoundaries = $project->boundaries->where('survey_location_id', $location->id);
            
            $mainLinesCount = $locLines->filter(fn($l) => $this->canonicalLineType($l) === 'main')->count();
            $crossLinesCount = $locLines->filter(fn($l) => $this->canonicalLineType($l) === 'cross')->count();
            
            $hasLines = $locLines->count() > 0;
            $hasBoundaries = $locBoundaries->count() > 0;
            $status = ($hasLines && $hasBoundaries) ? 'Completed' : 'Pending';

            $areaM2 = $locBoundaries->sum('area');
            $distNm = $location->sbesParameters->total_distance_nm ?? 0;
            $globalDistanceNm += $distNm;

            $locationsData[] = [
                'name'               => $location->name,
                'status'             => $status,
                'boundary_count'     => $locBoundaries->count(),
                'area_m2'            => round($areaM2, 2),
                'area_km2'           => round($areaM2 / 1_000_000, 4),
                'main_line_count'    => $mainLinesCount,
                'cross_line_count'   => $crossLinesCount,
                'total_length_nm'    => $distNm,
                'survey_speed_knots' => $location->sbesParameters->survey_speed_knots ?? 'N/A',
            ];
        }

        return [
            'project'          => $project,
            'duration'         => $calcResult['duration'],
            'estimation'       => $estimation,
            'cost_items'       => $costItems,
            'grouped_costs'    => $groupedCostItems,
            'total_cost'       => $estimation ? $estimation->total_cost : $calcResult['total_cost'],
            'locations_data'   => $locationsData,

            // Global Survey stats
            'boundary_count'   => $project->boundaries->count(),
            'total_area_m2'    => round($totalArea, 2),
            'total_area_km2'   => round($totalArea / 1_000_000, 4),
            'total_perimeter_m' => round($totalPerimeter, 2),
            'total_perimeter_km' => round($totalPerimeter / 1000, 2),
            'total_vertices'   => $totalVertices,

            // Global Survey lines
            'total_lines'       => $project->surveyLines->count(),
            'main_line_count'   => $mainLines->count(),
            'cross_line_count'  => $crossLines->count(),
            'total_length_nm'   => round($globalDistanceNm, 4),
            'line_spacing'      => $genSettings->line_spacing ?? 'N/A',
            'orientation_angle' => $genSettings->orientation_angle ?? 'N/A',

            'generated_at'     => now(),
            'report_number'    => $this->generateReportNumber($project, 'RPT'),
            'quotation_number' => $this->generateQuotationNumber($project),
        ];
    }

    /**
     * Generate a unique report number.
     */
    protected function generateReportNumber(Project $project, string $prefix = 'RPT'): string
    {
        $existing = Report::where('project_id', $project->id)
            ->where('type', $prefix === 'RPT' ? 'full_report' : 'quotation')
            ->latest()
            ->first();

        $version = $existing ? $existing->version + 1 : 1;

        return $prefix . '-' . now()->format('Y') . '-' . str_pad($project->id, 5, '0', STR_PAD_LEFT) . '-V' . $version;
    }

    /**
     * Generate a unique quotation number.
     */
    protected function generateQuotationNumber(Project $project): string
    {
        return $this->generateReportNumber($project, 'QTN');
    }

    /**
     * Track a generated report in the database.
     */
    public function trackReport(Project $project, string $type, string $number, ?string $filePath = null): Report
    {
        $existing = Report::where('project_id', $project->id)->where('type', $type)->latest()->first();
        $version  = $existing ? $existing->version + 1 : 1;

        // If generating a quotation/proposal, save the quotation number to the cost estimation
        if ($project->costEstimation) {
            $project->costEstimation->update([
                'quotation_number' => $number,
            ]);
        }

        return Report::create([
            'project_id'    => $project->id,
            'report_number' => $number,
            'type'          => $type,
            'version'       => $version,
            'file_path'     => $filePath,
            'generated_at'  => now(),
        ]);
    }

    private function canonicalLineType($line): string
    {
        $geometryType = $line->geometry['properties']['line_type'] ?? null;

        if (in_array($geometryType, ['main', 'cross', 'reference', 'adcp_marker'], true)) {
            return $geometryType;
        }

        if (in_array($line->type, ['main', 'cross', 'reference', 'adcp_marker'], true)) {
            return $line->type;
        }

        return 'main';
    }
}
