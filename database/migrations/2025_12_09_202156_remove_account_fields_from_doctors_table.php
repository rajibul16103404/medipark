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
        Schema::table('doctors', function (Blueprint $table) {
            // Drop unique index on user_name first (if exists)
            // This is needed for SQLite which doesn't support dropping columns with indexes
            if (Schema::hasColumn('doctors', 'user_name')) {
                try {
                    $table->dropUnique(['user_name']);
                } catch (\Exception $e) {
                    // Index might not exist or already dropped
                }
            }
        });

        Schema::table('doctors', function (Blueprint $table) {
            // Drop columns
            $table->dropColumn(['display_name', 'user_name', 'password']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('permanent_address');
            $table->string('user_name')->unique()->after('display_name');
            $table->string('password')->after('user_name');
        });
    }
};
