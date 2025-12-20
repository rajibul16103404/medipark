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
            $table->enum('payment_type', ['full_payment', 'down_payment', 'emi_installment'])->after('name');
            $table->decimal('regular_price', 15, 2)->nullable()->after('payment_type');
            $table->decimal('special_discount', 15, 2)->nullable()->after('regular_price');
            $table->decimal('offer_price', 15, 2)->nullable()->after('special_discount');
            $table->decimal('down_payment_amount', 15, 2)->nullable()->after('offer_price');
            $table->decimal('emi_amount', 15, 2)->nullable()->after('down_payment_amount');
            $table->unsignedInteger('waiver_frequency_months')->nullable()->after('emi_amount')->comment('Installment waiver frequency in months (e.g., every 12 months or 17 months)');
            $table->unsignedInteger('number_of_waivers')->nullable()->after('waiver_frequency_months')->comment('Total number of installments to be waived');
            $table->decimal('waiver_amount_per_installment', 15, 2)->nullable()->after('number_of_waivers');
            $table->decimal('total_waiver_amount', 15, 2)->nullable()->after('waiver_amount_per_installment');
            $table->boolean('is_limited_time_offer')->default(false)->after('total_waiver_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('installment_rules', function (Blueprint $table) {
            $table->dropColumn([
                'payment_type',
                'regular_price',
                'special_discount',
                'offer_price',
                'down_payment_amount',
                'emi_amount',
                'waiver_frequency_months',
                'number_of_waivers',
                'waiver_amount_per_installment',
                'total_waiver_amount',
                'is_limited_time_offer',
            ]);
        });
    }
};
