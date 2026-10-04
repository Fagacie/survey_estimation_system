<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectModellingSummary extends Model
{
    protected $table = 'project_modelling_summaries';

    protected $fillable = [
        'project_Id', 'package_id', 'package_name',
        'contingency_percent', 'tax_percent',
        'internal_subtotal', 'client_subtotal',
        'contingency_amount', 'tax_amount', 'grand_total',
        'created_by',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_Id', 'project_Id');
    }
}