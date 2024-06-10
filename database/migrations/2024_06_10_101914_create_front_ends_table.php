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
        Schema::create('front_ends', function (Blueprint $table) {
            $table->id();
            $table->enum('feature_type',['Form','List','Dialog','Flow'])->nullable();
            $table->string('reference_figma')->nullable();
            $table->string('detail_task')->nullable();
            $table->string('design_validation_detail')->nullable();
            $table->string('styling_detail')->nullable();
            $table->string('api_integration')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('front_ends');
    }
};
