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
        Schema::create('shootings', function (Blueprint $table) {
            $table->id();
            $table->string('shooting_location');
            $table->longText('type_detail')->nullable();
            $table->longText('script_detail')->nullable();
            $table->string('scene_number')->nullable();
            $table->string('document')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('duration')->nullable();
            $table->string('type')->nullable();
            // $table->foreignId('customer_id')->constrained()m->cascadeOnDelete();
            $table->string('client')->nullable();
            $table->string('video_shooting_project')->nullable();
            $table->string('photo_shooting_project')->nullable();
            $table->string('arrive_office_on_time')->nullable();
            $table->string('transportation_charge')->nullable();
            $table->string('out_time')->nullable();
            $table->string('in_time')->nullable();
            $table->string('crew_list')->nullable();
            $table->string('project_details')->nullable();
            $table->longText('description')->nullable();
            $table->string('food_charge')->nullable();
            $table->string('other_charge')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shootings');
    }
};
