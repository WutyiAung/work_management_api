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
        Schema::create('designs', function (Blueprint $table) {
            $table->id();
            $table->string('brand')->nullable();
            $table->string('type_of_media')->nullable();
            $table->string('deadline')->nullable();
            $table->string('content_writer')->nullable();
            $table->string('designer')->nullable();
            $table->string('visual_copy')->nullable();
            $table->string('headline')->nullable();
            $table->string('body')->nullable();
            $table->string('objective')->nullable();
            $table->string('important_info')->nullable();
            $table->string('taste_style')->nullable();
            $table->string('reference')->nullable();
            $table->string('reference_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designs');
    }
};
