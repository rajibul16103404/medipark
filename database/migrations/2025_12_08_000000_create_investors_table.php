<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investors', function (Blueprint $table) {
            $table->id();

            // Applicant details
            $table->string('file_number')->nullable();
            $table->string('applicant_full_name')->nullable();
            $table->string('fathers_name')->nullable();
            $table->string('mothers_name')->nullable();
            $table->string('spouses_name')->nullable();
            $table->text('present_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('nid_pp_bc_number')->nullable();
            $table->string('tin_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->string('mobile_number', 50)->nullable();
            $table->string('email')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->enum('residency_status', ['resident', 'non_resident'])->nullable();
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed', 'other'])->nullable();
            $table->date('marriage_date')->nullable();
            $table->string('organization')->nullable();
            $table->string('profession')->nullable();
            $table->string('applicant_image')->nullable();

            // Nominee details
            $table->string('nominee_name')->nullable();
            $table->string('nominee_relation')->nullable();
            $table->string('nominee_mobile_number', 50)->nullable();
            $table->string('nominee_nid_pp_bc_number')->nullable();
            $table->text('nominee_present_address')->nullable();
            $table->text('nominee_permanent_address')->nullable();
            $table->string('nominee_image')->nullable();

            // Project details
            $table->string('project_name')->nullable();
            $table->text('project_present_address')->nullable();
            $table->text('project_permanent_address')->nullable();
            $table->string('category_of_share')->nullable();
            $table->decimal('price_per_hss', 15, 2)->nullable();
            $table->unsignedInteger('number_of_hss')->nullable();
            $table->decimal('total_price', 15, 2)->nullable();
            $table->string('total_price_in_words')->nullable();
            $table->decimal('special_discount', 15, 2)->nullable();
            $table->decimal('installment_per_month', 15, 2)->nullable();
            $table->string('mode_of_payment')->nullable();
            $table->text('others_instructions')->nullable();
            $table->decimal('agreed_price', 15, 2)->nullable();
            $table->date('installment_start_from')->nullable();
            $table->date('installment_start_to')->nullable();

            // Payment schedule - left section
            $table->decimal('booking_money', 15, 2)->nullable();
            $table->string('booking_money_in_words')->nullable();
            $table->date('booking_money_date')->nullable();
            $table->string('booking_money_cash_cheque_no')->nullable();
            $table->string('booking_money_branch')->nullable();
            $table->date('booking_money_on_or_before')->nullable();
            $table->string('booking_money_mobile_number', 50)->nullable();

            // Payment schedule - right section
            $table->string('payment_in_words')->nullable();
            $table->date('final_payment_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->decimal('down_payment', 15, 2)->nullable();
            $table->date('down_payment_date')->nullable();
            $table->text('instructions_if_any')->nullable();
            $table->string('reference_name_a')->nullable();
            $table->string('reference_name_b')->nullable();
            $table->decimal('rest_amount', 15, 2)->nullable();
            $table->string('rest_amount_in_words')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investors');
    }
};
