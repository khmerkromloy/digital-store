<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'paid', 'delivered', 'cancelled']);
        $payment = match ($status) {
            'paid', 'delivered' => 'paid',
            'cancelled' => 'unpaid',
            default => $this->faker->randomElement(['unpaid', 'partial']),
        };
        $delivery = match ($status) {
            'delivered' => 'delivered',
            'cancelled' => 'failed',
            default => 'pending',
        };

        return [
            'order_number' => 'ORD-'.now()->format('ymd').'-'.Str::upper(Str::random(5)),
            'customer_id' => Customer::factory(),
            'branch_id' => Branch::query()->inRandomOrder()->value('id'),
            'subtotal' => 0,
            'discount_total' => 0,
            'tax_total' => 0,
            'grand_total' => 0,
            'currency' => 'USD',
            'status' => $status,
            'payment_status' => $payment,
            'delivery_status' => $delivery,
            'payment_method' => $this->faker->randomElement(['cash', 'bakong', 'aba', 'wing']),
            'delivery_method' => 'email',
            'placed_at' => now()->subDays(rand(0, 30)),
            'paid_at' => $payment === 'paid' ? now()->subDays(rand(0, 30)) : null,
            'delivered_at' => $delivery === 'delivered' ? now()->subDays(rand(0, 30)) : null,
        ];
    }
}
