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
        Schema::create('shooting_classification_pivots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shooting_id');
            $table->unsignedBigInteger('shooting_accessory_category_id');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shooting_classification_pivots');
    }
};
