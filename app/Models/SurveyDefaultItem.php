<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SurveyDefaultItem extends Model
{
    protected $table = 'survey_default_items';

    protected $fillable = [
        'survey_type',
        'item_id',
        'default_qty',
        'days_rule',
        'sort_order',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }
}