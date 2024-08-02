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
        Schema::create('content_managements', function (Blueprint $table) {
            $table->id();
            $table->string('content_title');
            $table->string('notify_date');
            $table->string('notify_time');
            $table->longText('content_description')->nullable();
            $table->boolean('is_close')->default(false);
            $table->boolean('is_seen')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_management');
    }
};
