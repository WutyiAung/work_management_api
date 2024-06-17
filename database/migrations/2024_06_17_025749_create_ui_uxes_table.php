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
        Schema::create('ui_uxes', function (Blueprint $table) {
            $table->id();
            $table->string('customer_requirement')->nullable();
            $table->string('ui_type')->nullable();
            $table->string('reference_platform')->nullable();
            $table->string('ui_detail_task')->nullable();
            $table->string('ui_styling_detail')->nullable();
            $table->integer('total_ui_screen')->nullable();
            $table->integer('confirmed_ui_screen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ui_uxes');
    }
};
