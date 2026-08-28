<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostItem extends Model
{
    protected $fillable = [
        'cost_estimation_id',
        'cost_rate_id',
        'category',
        'description',
        'days',
        'units',
        'unit_rate',
        'total_price',
    ];

    protected $casts = [
        'days' => 'float',
        'units' => 'integer',
        'unit_rate' => 'float',
        'total_price' => 'float',
    ];

    public function costEstimation()
    {
        return $this->belongsTo(CostEstimation::class);
    }

    public function costRate()
    {
        return $this->belongsTo(CostRate::class);
    }
}
