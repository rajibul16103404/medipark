<?php

namespace Database\Factories;

use App\InstallmentStatus;
use App\Models\Investor;
use App\Models\InvestorInstallment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvestorInstallment>
 */
class InvestorInstallmentFactory extends Factory
{
    protected $model = InvestorInstallment::class;

    public function definition(): array
    {
        return [
            'investor_id' => Investor::factory(),
            'installment_number' => $this->faker->numberBetween(1, 36),
            'amount' => $this->faker->randomFloat(2, 5000, 50000),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'paid_date' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'status' => $this->faker->randomElement(InstallmentStatus::cases()),
            'payment_method' => $this->faker->optional()->randomElement(['cash', 'bank_transfer', 'cheque', 'mobile_banking']),
            'transaction_reference' => $this->faker->optional()->bothify('TXN-####-####'),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
