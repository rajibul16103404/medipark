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
            $table->json('education')->nullable()->after('known_languages');
            $table->json('experience')->nullable()->after('education');
            $table->json('social_media')->nullable()->after('experience');
            $table->json('membership')->nullable()->after('social_media');
            $table->json('awards')->nullable()->after('membership');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['education', 'experience', 'social_media', 'membership', 'awards']);
        });
    }
};
