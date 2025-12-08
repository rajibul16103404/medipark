<?php

namespace Database\Factories;

use App\Models\Investor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Investor>
 */
class InvestorFactory extends Factory
{
    protected $model = Investor::class;

    public function definition(): array
    {
        return [
            'file_number' => $this->faker->unique()->bothify('INV-####'),
            'applicant_full_name' => $this->faker->name(),
            'fathers_name' => $this->faker->name(),
            'mothers_name' => $this->faker->name(),
            'spouses_name' => $this->faker->optional()->name(),
            'present_address' => $this->faker->address(),
            'permanent_address' => $this->faker->address(),
            'nid_pp_bc_number' => $this->faker->numerify('############'),
            'tin_number' => $this->faker->optional()->numerify('########'),
            'date_of_birth' => $this->faker->date(),
            'nationality' => 'Bangladeshi',
            'religion' => $this->faker->randomElement(['Islam', 'Hinduism', 'Buddhism', 'Christianity', 'Other']),
            'mobile_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'residency_status' => $this->faker->randomElement(['resident', 'non_resident']),
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed', 'other']),
            'marriage_date' => $this->faker->optional()->date(),
            'organization' => $this->faker->company(),
            'profession' => $this->faker->jobTitle(),
            'project_name' => $this->faker->words(3, true),
            'category_of_share' => $this->faker->randomElement(['A', 'B', 'C']),
            'price_per_hss' => $this->faker->randomFloat(2, 100000, 500000),
            'number_of_hss' => $this->faker->numberBetween(1, 10),
            'total_price' => $this->faker->randomFloat(2, 100000, 1000000),
            'total_price_in_words' => $this->faker->words(5, true),
            'special_discount' => $this->faker->optional()->randomFloat(2, 0, 50000),
            'installment_per_month' => $this->faker->optional()->randomFloat(2, 5000, 50000),
            'mode_of_payment' => $this->faker->randomElement(['cash', 'bank_transfer', 'cheque']),
            'others_instructions' => $this->faker->optional()->sentence(),
            'agreed_price' => $this->faker->randomFloat(2, 100000, 1000000),
            'installment_start_from' => $this->faker->optional()->date(),
            'installment_start_to' => $this->faker->optional()->date(),
            'booking_money' => $this->faker->optional()->randomFloat(2, 50000, 200000),
            'booking_money_in_words' => $this->faker->optional()->words(4, true),
            'booking_money_date' => $this->faker->optional()->date(),
            'booking_money_cash_cheque_no' => $this->faker->optional()->bothify('CHQ-#####'),
            'booking_money_branch' => $this->faker->optional()->city(),
            'booking_money_on_or_before' => $this->faker->optional()->date(),
            'booking_money_mobile_number' => $this->faker->optional()->phoneNumber(),
            'payment_in_words' => $this->faker->optional()->words(4, true),
            'final_payment_date' => $this->faker->optional()->date(),
            'bank_name' => $this->faker->optional()->company(),
            'down_payment' => $this->faker->optional()->randomFloat(2, 50000, 200000),
            'down_payment_date' => $this->faker->optional()->date(),
            'instructions_if_any' => $this->faker->optional()->sentence(),
            'reference_name_a' => $this->faker->optional()->name(),
            'reference_name_b' => $this->faker->optional()->name(),
            'rest_amount' => $this->faker->optional()->randomFloat(2, 50000, 500000),
            'rest_amount_in_words' => $this->faker->optional()->words(4, true),
        ];
    }
}
