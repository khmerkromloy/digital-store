<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminOrderCrudTest extends TestCase
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
        $this->get(route('admin.orders.index'))->assertOk();
    }

    public function test_can_create_order(): void
    {
        $this->loginAsAdmin();
        $customer = Customer::factory()->create();
        $branch = Branch::factory()->create();

        $this->post(route('admin.orders.store'), [
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'delivery_status' => 'pending',
            'currency' => 'USD',
        ])->assertRedirect(route('admin.orders.index'));

        $this->assertDatabaseHas('orders', ['customer_id' => $customer->id, 'status' => 'pending']);
    }

    public function test_creating_order_auto_generates_order_number(): void
    {
        $this->loginAsAdmin();
        $customer = Customer::factory()->create();

        $this->post(route('admin.orders.store'), [
            'customer_id' => $customer->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'delivery_status' => 'pending',
        ]);

        $order = Order::firstWhere('customer_id', $customer->id);
        $this->assertNotNull($order);
        $this->assertNotEmpty($order->order_number);
        $this->assertStringStartsWith('ORD-', $order->order_number);
    }

    public function test_recalculate_totals(): void
    {
        $this->loginAsAdmin();
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);
        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit_price' => 25.00,
            'quantity' => 2,
            'discount' => 5.00,
            'line_total' => 45.00,
        ]);

        $this->post(route('admin.orders.recalculate', $order))->assertRedirect();

        $order->refresh();
        $this->assertEquals(50.00, (float) $order->subtotal);
        $this->assertEquals(5.00, (float) $order->discount_total);
        $this->assertEquals(45.00, (float) $order->grand_total);
    }

    public function test_mark_paid_creates_payment_and_updates_status(): void
    {
        $this->loginAsAdmin();
        $customer = Customer::factory()->create();
        $order = Order::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'subtotal' => 100,
            'grand_total' => 100,
            'payment_method' => 'cash',
        ]);

        $this->post(route('admin.orders.mark-paid', $order))->assertRedirect();

        $order->refresh();
        $this->assertEquals('paid', $order->status);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertNotNull($order->paid_at);
        $this->assertCount(1, $order->payments);
        $this->assertEquals(100.00, (float) $order->payments->first()->amount);
    }

    public function test_add_line_item(): void
    {
        $this->loginAsAdmin();
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $this->post(route('admin.orders.items.store', $order), [
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 30.00,
            'discount' => 5.00,
        ])->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'line_total' => 85.00,
        ]);

        $order->refresh();
        $this->assertEquals(90.00, (float) $order->subtotal);
        $this->assertEquals(85.00, (float) $order->grand_total);
    }
}
