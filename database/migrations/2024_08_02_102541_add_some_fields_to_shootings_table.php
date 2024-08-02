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
        Schema::table('shootings', function (Blueprint $table) {
            $table->longText('shooting_description')->nullable()->after('project_details');
            $table->string('food_charge')->nullable()->after('project_details');
            $table->string('other_charge')->nullable()->after('project_details');
            $table->string('total_charge')->nullable()->after('project_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shootings', function (Blueprint $table) {
            $table->dropColumn('shooting_description');
            $table->dropColumn('food_charge');
            $table->dropColumn('other_charge');
            $table->dropColumn('total_charge');
        });
    }
};
