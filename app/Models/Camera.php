<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sensor_width_mm',
        'sensor_height_mm',
        'focal_length_mm',
        'image_width_px',
        'image_height_px',
        'min_photo_interval_sec',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
