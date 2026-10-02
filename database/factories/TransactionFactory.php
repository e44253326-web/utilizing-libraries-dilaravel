<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_number' => 'TRX-'.now('Asia/Jakarta')->format('Ymd').'-'.fake()->unique()->numerify('###'),
            'user_id' => null,
            'customer_name' => 'Umum',
            'customer_phone' => null,
            'subtotal' => 0,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'discount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'grand_total' => 0,
            'paid_amount' => 0,
            'change_amount' => 0,
            'payment_method' => 'Tunai',
            'status' => 'held',
        ];
    }
}
