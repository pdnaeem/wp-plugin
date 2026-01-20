<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('building_material_substance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('substance_id')->constrained()->cascadeOnDelete();
            $table->float('initial_content')->default(0);
            $table->timestamps();
            $table->unique(['building_material_id', 'substance_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('building_material_substance');
    }
};
