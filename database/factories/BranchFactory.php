<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        $name = $this->faker->company();

        return [
            'code' => 'TST-'.Str::upper(Str::random(4)),
            'name' => $name,
            'name_kh' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'description' => $this->faker->sentence(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'country' => 'Cambodia',
            'timezone' => 'Asia/Phnom_Penh',
            'currency' => 'USD',
            'is_active' => true,
            'is_default' => false,
            'sort_order' => 0,
        ];
    }
}
