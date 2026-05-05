<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $first = $this->faker->firstName();
        $last = $this->faker->lastName();

        return [
            'first_name' => $first,
            'last_name' => $last,
            'full_name' => $first.' '.$last,
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'telegram_handle' => '@'.$this->faker->unique()->userName(),
            'country' => 'Cambodia',
            'locale' => $this->faker->randomElement(['en', 'km']),
            'total_spent' => 0,
            'orders_count' => 0,
            'status' => 'active',
            'note' => null,
        ];
    }
}
