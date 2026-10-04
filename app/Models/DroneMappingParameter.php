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
        'drone_model',
        'camera_model',
        'altitude_m',
        'target_gsd_cm',
        'gsd_cm',
        'ground_footprint_width_m',
        'ground_footprint_height_m',
        'photo_spacing_m',
        'flight_line_spacing_m',
        'speed_ms',
        'front_overlap_percent',
        'side_overlap_percent',
        'course_angle_deg',
        'total_flight_distance_m',
        'total_images',
        'estimated_duration_hours',
        'photo_interval_s',
        'usable_flight_time_min',
        'sortie_count',
        'mapping_margin_m',
        'capture_mode',
    ];

    public function surveyLocation(): BelongsTo
    {
        return $this->belongsTo(SurveyLocation::class);
    }
}
