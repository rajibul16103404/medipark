<?php

namespace Database\Factories;

use App\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InstallmentRule>
 */
class InstallmentRuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentType = fake()->randomElement(['full_payment', 'down_payment', 'emi_installment']);
        $regularPrice = fake()->randomFloat(2, 500000, 1000000);
        $specialDiscount = fake()->randomFloat(2, 50000, 150000);
        $offerPrice = $regularPrice - $specialDiscount;

        $baseData = [
            'name' => fake()->words(3, true).' Plan',
            'payment_type' => $paymentType,
            'regular_price' => $regularPrice,
            'special_discount' => $specialDiscount,
            'offer_price' => $offerPrice,
            'status' => fake()->randomElement([Status::Active->value, Status::Inactive->value]),
            'description' => fake()->optional()->paragraph(),
            'is_limited_time_offer' => fake()->boolean(30),
        ];

        if ($paymentType === 'down_payment') {
            $baseData['down_payment_amount'] = fake()->randomFloat(2, 200000, 400000);
            $baseData['emi_amount'] = fake()->randomFloat(2, 10000, 20000);
            $baseData['duration_months'] = fake()->numberBetween(36, 48);
            $baseData['waiver_frequency_months'] = fake()->randomElement([12, 17]);
            $baseData['number_of_waivers'] = fake()->numberBetween(2, 4);
            $baseData['waiver_amount_per_installment'] = $baseData['emi_amount'];
            $baseData['total_waiver_amount'] = $baseData['waiver_amount_per_installment'] * $baseData['number_of_waivers'];
        } elseif ($paymentType === 'emi_installment') {
            $baseData['emi_amount'] = fake()->randomFloat(2, 15000, 25000);
            $baseData['duration_months'] = fake()->numberBetween(48, 60);
            $baseData['waiver_frequency_months'] = fake()->randomElement([12, 17]);
            $baseData['number_of_waivers'] = fake()->numberBetween(2, 4);
            $baseData['waiver_amount_per_installment'] = $baseData['emi_amount'];
            $baseData['total_waiver_amount'] = $baseData['waiver_amount_per_installment'] * $baseData['number_of_waivers'];
        }

        return $baseData;
    }
}
