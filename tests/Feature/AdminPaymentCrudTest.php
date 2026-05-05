<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPaymentCrudTest extends TestCase
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
        $this->get(route('admin.payments.index'))->assertOk();
    }

    public function test_can_create_payment(): void
    {
        $this->loginAsAdmin();
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $this->post(route('admin.payments.store'), [
            'order_id' => $order->id,
            'method' => 'cash',
            'amount' => 99.50,
            'currency' => 'USD',
            'status' => 'succeeded',
        ])->assertRedirect(route('admin.payments.index'));

        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'amount' => 99.50, 'status' => 'succeeded']);
    }

    public function test_payment_number_auto_generated(): void
    {
        $this->loginAsAdmin();
        $customer = Customer::factory()->create();
        $order = Order::factory()->create(['customer_id' => $customer->id]);

        $this->post(route('admin.payments.store'), [
            'order_id' => $order->id,
            'method' => 'aba',
            'amount' => 10.00,
            'status' => 'pending',
        ]);

        $payment = Payment::firstWhere('order_id', $order->id);
        $this->assertNotNull($payment);
        $this->assertStringStartsWith('PAY-', $payment->payment_number);
    }
}
