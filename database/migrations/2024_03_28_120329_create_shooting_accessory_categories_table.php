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
        Schema::create('shooting_accessory_categories', function (Blueprint $table) {
            $table->id();
            $table->string('accessory_name')->unique();
            $table->integer('required_qty')->nullable();
            $table->integer('taken_qty')->default('0');
            $table->integer('returned_qty')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shooting_accessory_categories');
    }
};
