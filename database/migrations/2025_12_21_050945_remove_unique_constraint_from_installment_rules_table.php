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
        Schema::table('installment_rules', function (Blueprint $table) {
            // Remove the unique constraint to allow soft-deleted records to be recreated
            // Application-level validation will handle uniqueness checks for non-deleted records
            $table->dropUnique(['name', 'duration_months']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installment_rules', function (Blueprint $table) {
            // Restore the unique constraint if rolling back
            $table->unique(['name', 'duration_months']);
        });
    }
};
