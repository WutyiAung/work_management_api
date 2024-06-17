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
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->string('deployment_type')->nullable();
            $table->string('deployment_brief')->nullable();
            $table->string('server_type')->nullable();
            $table->string('instance_name')->nullable();
            $table->string('configuration')->nullable();
            $table->string('db_type')->nullable();
            $table->string('db_name')->nullable();
            $table->string('ip_and_port')->nullable();
            $table->string('username')->nullable();
            $table->string('project_type')->nullable();
            $table->string('dev_type')->nullable();
            $table->string('sub_domain')->nullable();
            $table->boolean('server_restart_after_deploy')->default(false);
            $table->boolean('apk_released_if_mobile')->default(false);
            $table->string('deployment_issues')->nullable();
            $table->string('deployment_overall')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployments');
    }
};
