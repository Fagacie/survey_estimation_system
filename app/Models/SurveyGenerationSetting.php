<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyGenerationSetting extends Model
{
    protected $fillable = [
        'project_id',
        'line_spacing',
        'orientation_angle',
        'margin',
        'cross_line_spacing',
        'cross_line_angle',
    ];

    protected $casts = [
        'line_spacing' => 'float',
        'orientation_angle' => 'float',
        'margin' => 'float',
        'cross_line_spacing' => 'float',
        'cross_line_angle' => 'float',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
