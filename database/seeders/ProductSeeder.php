<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'License Keys' => [
                ['name' => 'Windows 11 Pro Retail Key', 'short' => 'Lifetime activation, retail genuine.', 'price' => 19.99, 'original' => 199.00, 'stock' => 45, 'featured' => true],
                ['name' => 'Microsoft Office 2021 Pro Plus', 'short' => 'Word, Excel, PowerPoint — lifetime license.', 'price' => 29.99, 'original' => 249.00, 'stock' => 30, 'featured' => true],
                ['name' => 'Adobe Photoshop 1 Year', 'short' => 'Authentic Adobe CC license — 1 year of Photoshop.', 'price' => 49.00, 'original' => 239.00, 'stock' => 22, 'featured' => false],
                ['name' => 'IDM Internet Download Manager Lifetime', 'short' => 'Permanent license — 1 PC.', 'price' => 8.99, 'original' => 24.95, 'stock' => 80, 'featured' => false],
                ['name' => 'NordVPN 2 Years Plan', 'short' => 'Premium VPN — 6 devices, 2 years.', 'price' => 29.00, 'original' => 89.00, 'stock' => 50, 'featured' => true],
                ['name' => 'Kaspersky Total Security 1 Year', 'short' => '5 devices, 1 year — antivirus + VPN.', 'price' => 14.99, 'original' => 59.99, 'stock' => 40, 'featured' => false],
            ],
            'Spotify Accounts' => [
                ['name' => 'Spotify Premium 1 Month (Private)', 'short' => 'Private upgrade — your own account stays yours.', 'price' => 2.99, 'original' => 10.99, 'stock' => 100, 'featured' => true],
                ['name' => 'Spotify Premium 1 Year (Private)', 'short' => 'A full year of Spotify Premium on your own account.', 'price' => 19.99, 'original' => 119.88, 'stock' => 60, 'featured' => true],
                ['name' => 'Spotify Family 6 Months', 'short' => 'Up to 6 members — Premium for the whole family.', 'price' => 14.99, 'original' => 95.94, 'stock' => 35, 'featured' => false],
                ['name' => 'Spotify Duo 3 Months', 'short' => 'For two people living together — Premium tier.', 'price' => 9.99, 'original' => 38.97, 'stock' => 40, 'featured' => false],
            ],
            'TikTok Accounts' => [
                ['name' => 'TikTok Aged 2018 Account', 'short' => 'Authentic 2018 account, low followers, ready for growth.', 'price' => 11.50, 'original' => 25.00, 'stock' => 18, 'featured' => true],
                ['name' => 'TikTok 1k Followers Account', 'short' => 'Real-looking 1,000 followers — established account.', 'price' => 24.00, 'original' => 60.00, 'stock' => 12, 'featured' => false],
                ['name' => 'TikTok 10k Followers Account', 'short' => '10,000 organic-looking followers — brand-ready.', 'price' => 79.00, 'original' => 199.00, 'stock' => 6, 'featured' => true],
                ['name' => 'TikTok Fresh PVA Account', 'short' => 'Phone-verified, ready to use immediately.', 'price' => 3.50, 'original' => 9.00, 'stock' => 80, 'featured' => false],
            ],
            'Facebook Accounts' => [
                ['name' => 'Facebook PVA Account (USA)', 'short' => 'US phone-verified, fresh email — ready to use.', 'price' => 4.50, 'original' => 12.00, 'stock' => 70, 'featured' => false],
                ['name' => 'Facebook Aged 2015 Account', 'short' => 'Real aged account from 2015 with friends list.', 'price' => 22.00, 'original' => 49.00, 'stock' => 14, 'featured' => true],
                ['name' => 'Facebook Business Manager (BM5)', 'short' => 'Verified BM with 5 ad account slots.', 'price' => 65.00, 'original' => 150.00, 'stock' => 8, 'featured' => true],
                ['name' => 'Facebook Page 10k Likes', 'short' => 'Niche-able page with 10,000 organic likes.', 'price' => 89.00, 'original' => 199.00, 'stock' => 5, 'featured' => false],
            ],
            'Streaming Services' => [
                ['name' => 'Netflix Premium 1 Month', 'short' => '4K UHD, 4 screens — 1 month subscription.', 'price' => 4.99, 'original' => 22.99, 'stock' => 60, 'featured' => true],
                ['name' => 'Disney+ 1 Year', 'short' => 'Full year of Disney+ streaming, all devices.', 'price' => 24.99, 'original' => 109.99, 'stock' => 28, 'featured' => false],
                ['name' => 'YouTube Premium 6 Months', 'short' => 'Ad-free YouTube + YouTube Music.', 'price' => 12.50, 'original' => 71.94, 'stock' => 45, 'featured' => false],
                ['name' => 'HBO Max 3 Months', 'short' => '3 months of premium HBO content.', 'price' => 9.99, 'original' => 47.97, 'stock' => 30, 'featured' => false],
            ],
            'Gaming' => [
                ['name' => 'Steam Wallet $50 Code (Global)', 'short' => 'Top up your Steam wallet — global region.', 'price' => 47.50, 'original' => 50.00, 'stock' => 50, 'featured' => false],
                ['name' => 'Cyberpunk 2077 Steam Key', 'short' => 'Global Steam key for Cyberpunk 2077.', 'price' => 19.99, 'original' => 59.99, 'stock' => 25, 'featured' => true],
                ['name' => 'PlayStation Plus 12 Months', 'short' => 'Full year of PS Plus Essential.', 'price' => 39.99, 'original' => 79.99, 'stock' => 20, 'featured' => false],
                ['name' => 'Xbox Game Pass Ultimate 3 Months', 'short' => 'Console + PC + EA Play included.', 'price' => 29.99, 'original' => 50.97, 'stock' => 22, 'featured' => true],
            ],
        ];

        foreach ($catalog as $categoryName => $items) {
            $category = Category::where('name', $categoryName)->first();
            if (! $category) {
                continue;
            }
            foreach ($items as $item) {
                Product::updateOrCreate(
                    ['name' => $item['name']],
                    [
                        'category_id' => $category->id,
                        'short_description' => $item['short'],
                        'description' => $this->longDescription($item['name'], $item['short']),
                        'price' => $item['price'],
                        'original_price' => $item['original'],
                        'currency' => 'USD',
                        'stock' => $item['stock'],
                        'is_active' => true,
                        'is_featured' => $item['featured'],
                        'product_type' => $this->guessType($categoryName),
                        'view_count' => random_int(50, 5000),
                        'sales_count' => random_int(5, 500),
                    ]
                );
            }
        }
    }

    private function guessType(string $categoryName): string
    {
        return match ($categoryName) {
            'License Keys', 'Gaming' => 'license_key',
            'Spotify Accounts', 'TikTok Accounts', 'Facebook Accounts' => 'account',
            'Streaming Services' => 'subscription',
            default => 'other',
        };
    }

    private function longDescription(string $name, string $short): string
    {
        return <<<TEXT
{$short}

Why buy {$name} from us?

- Instant delivery after payment confirmation — keys/credentials are emailed and visible in your account dashboard.
- 24/7 customer support via live chat and contact form.
- Replacement guarantee within the warranty window if anything goes wrong.
- Trusted by thousands of customers worldwide.

How it works:

1. Create an account and verify your email address.
2. Add {$name} to your cart and complete the checkout.
3. Receive your product instantly in your dashboard and inbox.

Note: This is a digital product. All sales are final once the key/credential has been viewed, except where covered by our replacement guarantee.
TEXT;
    }
}
