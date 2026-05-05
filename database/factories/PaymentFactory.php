<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'payment_number' => 'PAY-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
            'method' => $this->faker->randomElement(['cash', 'bakong', 'aba', 'wing', 'usdt']),
            'amount' => $this->faker->randomFloat(2, 5, 500),
            'currency' => 'USD',
            'status' => $this->faker->randomElement(['pending', 'succeeded', 'failed']),
            'reference_no' => Str::upper(Str::random(10)),
            'note' => null,
            'paid_at' => now()->subDays(rand(0, 14)),
        ];
    }
}
