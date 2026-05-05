<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BranchInventorySeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();
        $products = Product::all();

        if ($branches->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach ($branches as $branch) {
            // Each branch stocks ~half of the products.
            foreach ($products->random(min((int) ceil($products->count() / 2), $products->count())) as $product) {
                BranchInventory::firstOrCreate(
                    ['branch_id' => $branch->id, 'product_id' => $product->id],
                    [
                        'stock' => rand(0, 50),
                        'price_override' => null,
                        'is_active' => true,
                    ],
                );
            }
        }
    }
}
