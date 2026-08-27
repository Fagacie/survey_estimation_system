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
        'quantity',
        'unit_rate',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'float',
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
