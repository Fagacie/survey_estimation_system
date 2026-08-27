<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostEstimation extends Model
{
    protected $fillable = [
        'project_id',
        'total_cost',
        'currency',
        'status',
        'quotation_number',
        'terms_conditions',
        'valid_until',
        'duration_breakdown',
    ];

    protected $casts = [
        'total_cost'          => 'float',
        'valid_until'         => 'date',
        'duration_breakdown'  => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function items()
    {
        return $this->hasMany(CostItem::class);
    }
}
