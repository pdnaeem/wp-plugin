<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('geometry_dataset_id')->constrained('geometry_datasets');
            $table->foreignId('weather_dataset_id')->constrained('weather_datasets');
            $table->foreignId('substance_id')->constrained('substances');
            $table->foreignId('emission_function_id')->constrained('emission_functions');
            $table->string('status')->default('queued');
            $table->json('settings')->nullable();
            $table->json('logs')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};
