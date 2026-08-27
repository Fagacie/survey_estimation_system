<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostRate extends Model
{
    use HasFactory;

    protected $fillable = [

        'category',
        'name',
        'unit_type', // 'Per Day', 'Lump Sum'
        'default_rate',
        'is_active'
    ];

    public function costItems()
    {
        return $this->hasMany(CostItem::class);
    }
}
