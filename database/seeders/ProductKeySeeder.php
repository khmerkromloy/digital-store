<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Product;
use App\Models\ProductKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductKeySeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('is_default', true)->first() ?? Branch::first();
        if (! $branch) {
            return;
        }

        Product::where('product_type', 'license_key')
            ->take(8)
            ->get()
            ->each(function (Product $product) use ($branch) {
                for ($i = 0; $i < 5; $i++) {
                    ProductKey::firstOrCreate(
                        [
                            'product_id' => $product->id,
                            'key_value' => Str::upper(Str::random(5)).'-'.Str::upper(Str::random(5)).'-'.Str::upper(Str::random(5)).'-'.Str::upper(Str::random(5)),
                        ],
                        [
                            'branch_id' => $branch->id,
                            'status' => 'available',
                        ]
                    );
                }
            });
    }
}
