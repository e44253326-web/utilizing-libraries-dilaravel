<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransactionDetail>
 */
class TransactionDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'product_id' => null,
            'product_code' => null,
            'product_name' => fake()->words(2, true),
            'price' => 10000,
            'qty' => 1,
            'discount' => 0,
            'subtotal' => 10000,
        ];
    }
}
