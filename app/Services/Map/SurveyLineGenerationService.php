<?php

namespace App\Services\Map;

use App\Models\SurveyLocation;
use App\Models\SurveyGenerationSetting;

class SurveyLineGenerationService
{
    /**
     * Save the settings used to generate the lines
     */
    public function saveSettings(SurveyLocation $survey, array $settings): SurveyGenerationSetting
    {
        return $survey->surveyGenerationSetting()->updateOrCreate(
            ['survey_location_id' => $survey->id],
            [
                'project_id' => $survey->project_id,
                'line_spacing' => $settings['line_spacing'] ?? 0,
                'orientation_angle' => $settings['orientation_angle'] ?? 0,
                'cross_line_spacing' => $settings['cross_line_spacing'] ?? $settings['cross_spacing'] ?? 0,
                'cross_line_angle' => $settings['cross_line_angle'] ?? 0,
            ]
        );
    }
}
