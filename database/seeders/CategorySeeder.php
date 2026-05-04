<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'License Keys',
                'icon' => 'bi-key-fill',
                'description' => 'Genuine software license keys for popular apps and games — instant delivery after purchase.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Spotify Accounts',
                'icon' => 'bi-spotify',
                'description' => 'Premium Spotify accounts with ad-free streaming, offline playback, and high-quality audio.',
                'sort_order' => 2,
            ],
            [
                'name' => 'TikTok Accounts',
                'icon' => 'bi-tiktok',
                'description' => 'Aged TikTok accounts ready for content creation, marketing, and monetization.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Facebook Accounts',
                'icon' => 'bi-facebook',
                'description' => 'Verified Facebook accounts and pages — choose from PVA, BM, or aged options.',
                'sort_order' => 4,
            ],
            [
                'name' => 'Streaming Services',
                'icon' => 'bi-play-btn-fill',
                'description' => 'Netflix, Disney+, HBO, YouTube Premium and more streaming subscriptions.',
                'sort_order' => 5,
            ],
            [
                'name' => 'Gaming',
                'icon' => 'bi-controller',
                'description' => 'Steam keys, gift cards, and game accounts for your favorite titles.',
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
