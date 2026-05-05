<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'code' => 'PNH-MAIN',
                'name' => 'Phnom Penh Main Store',
                'name_kh' => 'ហាងធំភ្នំពេញ',
                'slug' => 'phnom-penh-main',
                'description' => 'Flagship branch — Phnom Penh, Cambodia.',
                'email' => 'pnh@digitalstore.local',
                'phone' => '+855 23 000 001',
                'address' => '#1, Street 240, Phnom Penh',
                'city' => 'Phnom Penh',
                'country' => 'Cambodia',
                'timezone' => 'Asia/Phnom_Penh',
                'currency' => 'USD',
                'is_active' => true,
                'is_default' => true,
                'sort_order' => 1,
            ],
            [
                'code' => 'SR-01',
                'name' => 'Siem Reap Branch',
                'name_kh' => 'សាខាសៀមរាប',
                'slug' => 'siem-reap',
                'description' => 'Siem Reap regional branch.',
                'email' => 'sr@digitalstore.local',
                'phone' => '+855 63 000 002',
                'address' => 'Sivutha Blvd, Siem Reap',
                'city' => 'Siem Reap',
                'country' => 'Cambodia',
                'timezone' => 'Asia/Phnom_Penh',
                'currency' => 'USD',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 2,
            ],
            [
                'code' => 'BB-01',
                'name' => 'Battambang Branch',
                'name_kh' => 'សាខាបាត់ដំបង',
                'slug' => 'battambang',
                'description' => 'Battambang regional branch.',
                'email' => 'bb@digitalstore.local',
                'phone' => '+855 53 000 003',
                'address' => 'Riverside, Battambang',
                'city' => 'Battambang',
                'country' => 'Cambodia',
                'timezone' => 'Asia/Phnom_Penh',
                'currency' => 'USD',
                'is_active' => true,
                'is_default' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($branches as $b) {
            Branch::updateOrCreate(['code' => $b['code']], $b);
        }
    }
}
