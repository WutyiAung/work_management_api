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
        Schema::create('design_artwork_sizes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('design_id');
            $table->unsignedBigInteger('artwork_size_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_artwork_sizes');
    }
};
