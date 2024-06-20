<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('front_ends', function (Blueprint $table) {
            // Add a new temporary column with the new data type
            $table->string('feature_type_temp')->default('string');
        });

        // Copy data from the old column to the new temporary column
        DB::table('front_ends')->update([
            'feature_type_temp' => DB::raw('feature_type')
        ]);

        Schema::table('front_ends', function (Blueprint $table) {
            // Drop the old column
            $table->dropColumn('feature_type');
        });

        // Rename the new temporary column to the old column name using raw SQL
        DB::statement('ALTER TABLE front_ends CHANGE feature_type_temp feature_type VARCHAR(255) DEFAULT "string"');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('front_ends', function (Blueprint $table) {
            // Add a new temporary column with the original data type
            $table->enum('feature_type_temp', ['string', 'boolean'])->default('string');
        });

        // Copy data from the string column to the enum column
        DB::table('front_ends')->update([
            'feature_type_temp' => DB::raw('feature_type')
        ]);

        Schema::table('front_ends', function (Blueprint $table) {
            // Drop the string column
            $table->dropColumn('feature_type');
        });

        // Rename the temporary column back to the original name using raw SQL
        DB::statement('ALTER TABLE front_ends CHANGE feature_type_temp feature_type ENUM("string", "boolean") DEFAULT "string"');
    }
};
