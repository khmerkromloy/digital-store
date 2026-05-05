<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('is_default', true)->first() ?? Branch::first();

        User::updateOrCreate(
            ['email' => 'admin@digitalstore.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'branch_id' => $branch?->id,
                'status' => 'active',
                'locale' => 'en',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@digitalstore.local'],
            [
                'name' => 'Branch Staff',
                'password' => Hash::make('password'),
                'user_type' => 'staff',
                'branch_id' => $branch?->id,
                'status' => 'active',
                'locale' => 'km',
                'email_verified_at' => now(),
            ]
        );
    }
}
