<?php

namespace App\Services\Map;

use App\Models\Project;
use App\Models\SurveyGenerationSetting;

class SurveyLineGenerationService
{
    /**
     * Save the settings used to generate the lines
     */
    public function saveSettings(Project $project, array $settings): SurveyGenerationSetting
    {
        return $project->surveyGenerationSetting()->updateOrCreate(
            ['project_id' => $project->id],
            [
                'line_spacing' => $settings['line_spacing'],
                'orientation_angle' => $settings['orientation_angle'],
                'cross_line_spacing' => $settings['cross_line_spacing'] ?? 0,
                'cross_line_angle' => $settings['cross_line_angle'] ?? 0,
            ]
        );
    }
}
