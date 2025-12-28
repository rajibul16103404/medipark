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
        Schema::table('facilities', function (Blueprint $table) {
            $table->longText('description1')->nullable()->after('short_description');
            $table->json('accordions')->nullable()->after('description1');
            $table->longText('description2')->nullable()->after('accordions');
            $table->longText('footer')->nullable()->after('description2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn(['description1', 'accordions', 'description2', 'footer']);
        });
    }
};
