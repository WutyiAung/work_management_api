<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('artwork_sizes', function (Blueprint $table) {
            $table->id();
            $table->string('visual_format');
            $table->string('aspect_ratio');
            $table->string('width');
            $table->string('height');
            $table->string('resolution');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artwork_sizes');
    }
};
