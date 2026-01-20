<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('material_subtypes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_type_id')->constrained()->cascadeOnDelete();
            $table->string('code')->unique();
            $table->float('runoff_coefficient');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_subtypes');
    }
};
