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
        Schema::create('photo_editings', function (Blueprint $table) {
            $table->id();
            $table->string('brand_name')->nullable();
            $table->string('project_title')->nullable();
            $table->string('project_start_date')->nullable();
            $table->string('draft_deadline')->nullable();
            $table->string('final_deadline')->nullable();
            $table->string('account_executive')->nullable();
            $table->string('photo_retoucher')->nullable();
            $table->longText('project_description')->nullable();
            $table->longText('client_request_detail')->nullable();
            $table->integer('number_of_retouch_photos')->nullable();
            $table->string('color_grade')->nullable();
            $table->string('editing_style')->nullable();
            $table->string('remark')->nullable();
            $table->string('editing_reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photo_editings');
    }
};
