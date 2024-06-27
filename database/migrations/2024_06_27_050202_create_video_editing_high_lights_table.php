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
        Schema::create('video_editing_high_lights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_editing_id')->constrained()->onDelete('cascade');
            $table->foreignId('high_light_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_editing_high_lights');
    }
};
