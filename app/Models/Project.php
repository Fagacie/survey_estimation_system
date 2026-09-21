<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'projects';
    protected $primaryKey = 'project_Id';

    protected $fillable = [
        'number',
        'name',
        'location',
        'status',
        'description',
        'start_date',
        'end_date',
        'period',
        'client_Id',
        'pic_name',
        'pic_no',
        'weather_days',
        'mod_demod_days',
        'patch_test_days',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'weather_days' => 'float',
        'mod_demod_days' => 'float',
        'patch_test_days' => 'float',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_Id', 'client_Id');
    }

    public function lineItems()
    {
        return $this->hasMany(QtInvoice::class, 'project_Id', 'project_Id');
    }

    public function surveyLocations()
    {
        return $this->hasMany(SurveyLocation::class, 'project_id', 'project_Id');
    }

    public function surveyLines()
    {
        return $this->hasMany(SurveyLine::class, 'project_id', 'project_Id');
    }

    public function boundaries()
    {
        return $this->hasMany(ProjectBoundary::class, 'project_id', 'project_Id');
    }

}