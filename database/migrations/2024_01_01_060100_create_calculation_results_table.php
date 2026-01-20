<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('calculation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calculation_id')->constrained('calculations')->cascadeOnDelete();
            $table->json('summary');
            $table->string('timeseries_path');
            $table->string('report_path');
            $table->string('export_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calculation_results');
    }
};
