<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\BranchInventory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminInventoryCrudTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsAdmin(): User
    {
        $user = User::create([
            'name' => 'A', 'email' => 'a@a.com', 'password' => Hash::make('password'),
            'user_type' => 'admin', 'status' => 'active', 'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        return $user;
    }

    public function test_index_renders(): void
    {
        $this->loginAsAdmin();
        $this->get(route('admin.inventory.index'))->assertOk();
    }

    public function test_can_create_inventory(): void
    {
        $this->loginAsAdmin();
        $branch = Branch::factory()->create();
        $product = Product::factory()->create();

        $this->post(route('admin.inventory.store'), [
            'branch_id' => $branch->id,
            'product_id' => $product->id,
            'stock' => 25,
            'is_active' => 1,
        ])->assertRedirect(route('admin.inventory.index'));

        $this->assertDatabaseHas('branch_product', [
            'branch_id' => $branch->id,
            'product_id' => $product->id,
            'stock' => 25,
        ]);
    }

    public function test_unique_branch_product_pair(): void
    {
        $this->loginAsAdmin();
        $branch = Branch::factory()->create();
        $product = Product::factory()->create();
        BranchInventory::create([
            'branch_id' => $branch->id,
            'product_id' => $product->id,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->post(route('admin.inventory.store'), [
            'branch_id' => $branch->id,
            'product_id' => $product->id,
            'stock' => 10,
        ])->assertSessionHasErrors('branch_id');
    }
}
