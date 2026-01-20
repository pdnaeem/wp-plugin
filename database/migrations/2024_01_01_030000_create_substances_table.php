<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('substances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cas_number')->nullable();
            $table->string('ec_number')->nullable();
            $table->float('acute_threshold')->nullable();
            $table->float('chronic_threshold')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('substances');
    }
};
