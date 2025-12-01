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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('roles', function (Blueprint $table) {
            if (! Schema::hasColumn('roles', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('privileges', function (Blueprint $table) {
            if (! Schema::hasColumn('privileges', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('otps', function (Blueprint $table) {
            if (! Schema::hasColumn('otps', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        // homepage_hero_sections already has softDeletes() in its creation migration
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('privileges', function (Blueprint $table) {
            if (Schema::hasColumn('privileges', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('otps', function (Blueprint $table) {
            if (Schema::hasColumn('otps', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        // homepage_hero_sections softDeletes() is handled in its creation migration
    }
};
