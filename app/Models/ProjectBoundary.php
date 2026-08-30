<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectBoundary extends Model
{
    protected $guarded = [];

    protected $casts = [
        'geometry' => 'array',
        'centroid' => 'array',
        'area' => 'float',
        'perimeter' => 'float',
        'vertex_count' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function surveyLocation()
    {
        return $this->belongsTo(SurveyLocation::class);
    }
}
