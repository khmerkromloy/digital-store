<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'name_kh' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            'sku' => 'SKU-'.Str::upper(Str::random(8)),
            'short_description' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 5, 200),
            'currency' => 'USD',
            'product_type' => $this->faker->randomElement(['license_key', 'account', 'subscription']),
            'stock' => $this->faker->numberBetween(0, 50),
            'is_active' => true,
            'is_featured' => false,
            'auto_deliver' => true,
        ];
    }
}
