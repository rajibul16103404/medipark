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
            $table->string('identity_number')->unique()->nullable()->after('id');
            $table->string('mobile_number', 20)->nullable()->after('email');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('mobile_number');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->text('present_address')->nullable()->after('date_of_birth');
            $table->text('permanent_address')->nullable()->after('present_address');
            $table->decimal('salary', 15, 2)->nullable()->after('permanent_address');
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable()->after('salary');
            $table->date('joining_date')->nullable()->after('blood_group');
            $table->string('image')->nullable()->after('joining_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'identity_number',
                'mobile_number',
                'gender',
                'date_of_birth',
                'present_address',
                'permanent_address',
                'salary',
                'blood_group',
                'joining_date',
                'image',
            ]);
        });
    }
};
