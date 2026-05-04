<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function seedCatalog(): array
    {
        $category = Category::create([
            'name' => 'License Keys',
            'icon' => 'bi-key-fill',
            'description' => 'Software keys',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Windows 11 Pro Retail Key',
            'short_description' => 'Lifetime activation, retail genuine.',
            'description' => 'Long description here.',
            'price' => 19.99,
            'original_price' => 199.00,
            'currency' => 'USD',
            'stock' => 50,
            'is_active' => true,
            'is_featured' => true,
        ]);

        return [$category, $product];
    }

    public function test_home_renders(): void
    {
        $this->seedCatalog();
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('DigitalShop')
            ->assertSee('Browse by category')
            ->assertSee('Windows 11 Pro Retail Key');
    }

    public function test_products_index_renders(): void
    {
        $this->seedCatalog();
        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('All products')
            ->assertSee('id="products-table"', false);
    }

    public function test_products_data_endpoint_returns_json(): void
    {
        $this->seedCatalog();
        $response = $this->get(route('products.data', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
        ]));
        $response->assertOk()
            ->assertJsonStructure(['data', 'recordsTotal', 'recordsFiltered']);
        $this->assertGreaterThanOrEqual(1, $response->json('recordsTotal'));
    }

    public function test_product_show_renders(): void
    {
        [, $product] = $this->seedCatalog();
        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('Description');
    }

    public function test_categories_index_renders(): void
    {
        $this->seedCatalog();
        $this->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Browse categories')
            ->assertSee('License Keys');
    }

    public function test_category_show_renders(): void
    {
        [$category] = $this->seedCatalog();
        $this->get(route('categories.show', $category->slug))
            ->assertOk()
            ->assertSee($category->name);
    }

    public function test_about_page_renders(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('About DigitalShop');
    }

    public function test_privacy_page_renders(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('Privacy policy');
    }

    public function test_terms_page_renders(): void
    {
        $this->get(route('terms'))
            ->assertOk()
            ->assertSee('Terms of service');
    }

    public function test_contact_page_renders(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Send us a message');
    }

    public function test_contact_store_validates(): void
    {
        $this->postJson(route('contact.store'), [
            'name' => '',
            'email' => 'not-an-email',
            'message' => 'short',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    public function test_contact_store_saves_message(): void
    {
        $this->postJson(route('contact.store'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Hello',
            'message' => 'Hi there, this is a test message that meets the minimum length.',
        ])->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'john@example.com',
            'subject' => 'Hello',
        ]);
    }
}
