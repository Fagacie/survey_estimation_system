<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyLine extends Model
{
    protected $guarded = [];

    protected $casts = [
        'geometry' => 'array',
        'length_meters' => 'float',
        'line_number' => 'integer',
        'bearing' => 'float',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
