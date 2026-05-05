<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $product = Product::query()->inRandomOrder()->first() ?? Product::factory()->create();
        $unit = (float) $product->price;
        $qty = $this->faker->numberBetween(1, 3);
        $discount = $this->faker->randomElement([0, 0, 0, round($unit * 0.1, 2)]);
        $line = round(($unit * $qty) - $discount, 2);

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku ?? null,
            'unit_price' => $unit,
            'quantity' => $qty,
            'discount' => $discount,
            'line_total' => $line,
        ];
    }
}
