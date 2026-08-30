<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyLocation extends Model
{
    use HasFactory;

    protected $fillable = ['project_id', 'name'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function boundaries()
    {
        return $this->hasMany(ProjectBoundary::class);
    }

    public function surveyLines()
    {
        return $this->hasMany(SurveyLine::class);
    }

    public function sbesParameters()
    {
        return $this->hasOne(SbesParameter::class);
    }

    public function surveyGenerationSetting()
    {
        return $this->hasOne(SurveyGenerationSetting::class);
    }
}
