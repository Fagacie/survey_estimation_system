<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('sensor_width_mm', 8, 4);
            $table->decimal('sensor_height_mm', 8, 4);
            $table->decimal('focal_length_mm', 8, 4);
            $table->integer('image_width_px');
            $table->integer('image_height_px');
            $table->decimal('min_photo_interval_sec', 5, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cameras');
    }
};
