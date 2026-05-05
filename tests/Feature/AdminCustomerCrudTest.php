<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCustomerCrudTest extends TestCase
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
        $this->get(route('admin.customers.index'))->assertOk();
    }

    public function test_data_endpoint_returns_json(): void
    {
        $this->loginAsAdmin();
        Customer::factory()->count(3)->create();

        $r = $this->getJson(route('admin.customers.data').'?draw=1&start=0&length=10');
        $r->assertOk()->assertJsonStructure(['data', 'recordsTotal', 'recordsFiltered']);
        $this->assertCount(3, $r->json('data'));
    }

    public function test_can_create_customer(): void
    {
        $this->loginAsAdmin();

        $this->post(route('admin.customers.store'), [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '012345678',
            'status' => 'active',
            'locale' => 'en',
        ])->assertRedirect(route('admin.customers.index'));

        $this->assertDatabaseHas('customers', ['email' => 'john@example.com', 'full_name' => 'John Doe']);
    }

    public function test_unique_email_validation(): void
    {
        $this->loginAsAdmin();
        Customer::factory()->create(['email' => 'taken@x.com']);

        $this->post(route('admin.customers.store'), [
            'full_name' => 'X',
            'email' => 'taken@x.com',
            'status' => 'active',
        ])->assertSessionHasErrors('email');
    }

    public function test_can_delete_customer(): void
    {
        $this->loginAsAdmin();
        $customer = Customer::factory()->create();

        $this->delete(route('admin.customers.destroy', $customer))
            ->assertRedirect(route('admin.customers.index'));

        $this->assertSoftDeleted('customers', ['id' => $customer->id]);
    }
}
