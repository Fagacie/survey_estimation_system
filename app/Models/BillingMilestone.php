<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingMilestone extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'percentage',
        'amount',
        'status',
        'invoice_id'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
