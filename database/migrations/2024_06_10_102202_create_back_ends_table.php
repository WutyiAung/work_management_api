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
        Schema::create('back_ends', function (Blueprint $table) {
            $table->id();
            $table->string('use_case')->nullable();
            $table->string('crud_type')->nullable();
            $table->string('detail')->nullable();
            $table->string('database_migration')->nullable();
            $table->string('controller_name')->nullable();
            $table->string('routes')->nullable();
            $table->string('related_view')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('back_ends');
    }
};
