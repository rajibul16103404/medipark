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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('doctor_identity_number')->unique()->nullable();
            $table->string('doctor_name');
            $table->string('department')->nullable();
            $table->string('specialist')->nullable();
            $table->string('email_address')->unique();
            $table->string('mobile_number')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->json('known_languages')->nullable();
            $table->string('registration_number')->nullable();
            $table->text('about')->nullable();
            $table->string('image')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('display_name')->nullable();
            $table->string('user_name')->unique();
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
