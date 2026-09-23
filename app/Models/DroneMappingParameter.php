<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DroneMappingParameter extends Model
{
    use HasFactory;

    protected $fillable = [
        'survey_location_id',
        'camera_model',
        'altitude_m',
        'speed_ms',
        'front_overlap_percent',
        'side_overlap_percent',
        'course_angle_deg',
        'total_flight_distance_m',
        'total_images',
        'estimated_duration_hours',
    ];

    public function surveyLocation(): BelongsTo
    {
        return $this->belongsTo(SurveyLocation::class);
    }
}
