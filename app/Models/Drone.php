<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'usable_flight_time_min',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
