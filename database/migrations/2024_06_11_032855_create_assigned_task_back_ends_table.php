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
        Schema::create('assigned_task_back_ends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assigned_task_id')->constrained()->onDelete('cascade');
            $table->foreignId('back_end_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigned_task_back_ends');
    }
};
