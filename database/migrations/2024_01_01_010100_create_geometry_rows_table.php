<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('geometry_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('geometry_dataset_id')->constrained()->cascadeOnDelete();
            $table->string('component_id')->nullable();
            $table->float('surface_area_m2');
            $table->string('exposure_class')->nullable();
            $table->string('material_subtype_code')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('geometry_rows');
    }
};
