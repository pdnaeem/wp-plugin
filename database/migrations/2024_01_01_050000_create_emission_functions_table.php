<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('emission_functions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('substance_id')->constrained()->cascadeOnDelete();
            $table->string('function_type');
            $table->json('parameters');
            $table->string('source');
            $table->json('diagnostics')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emission_functions');
    }
};
