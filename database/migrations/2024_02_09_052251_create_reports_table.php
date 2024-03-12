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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_task_id')->constrained()->cascadeOnDelete();
            $table->enum('status',['pending','inProgress','done'])->default('pending');
            $table->integer('progress')->default(0);
            $table->text('progress_description');
            $table->date('report_date')->nullable();
            $table->time('report_time')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('video_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
