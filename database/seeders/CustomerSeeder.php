<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        Customer::factory()->count(15)->create();

        Customer::firstOrCreate(
            ['email' => 'demo@digitalstore.local'],
            [
                'first_name' => 'Demo',
                'last_name' => 'Customer',
                'full_name' => 'Demo Customer',
                'phone' => '+855 12 345 678',
                'telegram_handle' => '@democustomer',
                'country' => 'Cambodia',
                'locale' => 'km',
                'status' => 'active',
            ],
        );
    }
}
