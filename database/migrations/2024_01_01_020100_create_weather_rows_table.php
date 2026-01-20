<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('weather_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weather_dataset_id')->constrained()->cascadeOnDelete();
            $table->timestamp('observed_at')->index();
            $table->float('precipitation_mm')->default(0);
            $table->float('temperature_c')->nullable();
            $table->float('wind_speed_ms')->nullable();
            $table->float('humidity_pct')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_rows');
    }
};
