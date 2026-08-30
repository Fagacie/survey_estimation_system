<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_code',
        'name',
        'client',
        'location',
        'start_date',
        'end_date',
        'description',
        'weather_days',
        'mod_demod_days',
        'patch_test_days',
        'status',
        'user_id',
    ];

    /**
     * Get the user that owns the project.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project's boundaries.
     */
    public function boundaries()
    {
        return $this->hasMany(ProjectBoundary::class);
    }

    /**
     * Helper to get the first boundary for backwards compatibility if needed
     */
    public function getBoundaryAttribute()
    {
        return $this->boundaries()->first();
    }

    public function surveyLines()
    {
        return $this->hasMany(SurveyLine::class);
    }

    public function surveyLocations()
    {
        return $this->hasMany(SurveyLocation::class);
    }

    public function sbesParameters()
    {
        return $this->hasOne(SbesParameter::class);
    }
    
    // We keep these helper methods because some views (like dashboard) might still reference them
    public function surveyGenerationSetting()
    {
        return $this->hasOne(SurveyGenerationSetting::class);
    }

    public function costEstimation()
    {
        return $this->hasOne(CostEstimation::class);
    }
}
