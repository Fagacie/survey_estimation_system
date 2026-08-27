<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSurveyParameterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'mbes' => 'nullable|array',
            'mbes.water_depth' => 'nullable|numeric|min:0.1',
            'mbes.swath_angle' => 'nullable|numeric|min:1|max:180',
            'mbes.overlap' => 'nullable|numeric|min:0|max:100',
            'mbes.survey_speed' => 'nullable|numeric|min:0.1',
            'mbes.working_hours' => 'nullable|numeric|min:1|max:24',
            'mbes.weather_days' => 'nullable|numeric|min:0',
            'mbes.patch_test_days' => 'nullable|numeric|min:0',

            'sbes' => 'nullable|array',
            'sbes.survey_speed_knots' => 'nullable|numeric|min:0',
            'sbes.working_hours_per_day' => 'nullable|numeric|min:0|max:24',
            'sbes.weather_days' => 'nullable|numeric|min:0',
            'sbes.patch_test_days' => 'nullable|numeric|min:0',
            'sbes.mod_demod_days' => 'nullable|numeric|min:0',

            'adcp' => 'nullable|array',
            'adcp.units' => 'nullable|integer|min:1',
            'adcp.rental_days' => 'nullable|numeric|min:0',
            'adcp.boat_days' => 'nullable|numeric|min:0',
            'adcp.personnel_count' => 'nullable|integer|min:0',
        ];
    }
}
