<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::query()->take(10)->get();
        $products = Product::query()->where('is_active', true)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            return;
        }

        foreach ($customers as $customer) {
            $orderCount = rand(1, 3);

            for ($n = 0; $n < $orderCount; $n++) {
                $order = Order::factory()->create([
                    'customer_id' => $customer->id,
                ]);

                $picked = $products->random(min(rand(1, 3), $products->count()));
                $itemModels = collect($picked)->map(function (Product $product) use ($order) {
                    $unit = (float) $product->price;
                    $qty = rand(1, 2);
                    $discount = 0.0;
                    $line = round(($unit * $qty) - $discount, 2);

                    return OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'unit_price' => $unit,
                        'quantity' => $qty,
                        'discount' => $discount,
                        'line_total' => $line,
                    ]);
                });

                $order->setRelation('items', $itemModels);
                $order->recalculateTotals();

                if ($order->payment_status === 'paid') {
                    Payment::create([
                        'order_id' => $order->id,
                        'payment_number' => Payment::generatePaymentNumber(),
                        'method' => $order->payment_method ?? 'cash',
                        'amount' => $order->grand_total,
                        'currency' => $order->currency,
                        'status' => 'succeeded',
                        'paid_at' => $order->paid_at ?? now(),
                    ]);
                }
            }

            // Sync customer aggregates.
            $orders = Order::where('customer_id', $customer->id)->get();
            $customer->orders_count = $orders->count();
            $customer->total_spent = (float) $orders->where('payment_status', 'paid')->sum('grand_total');
            $customer->save();
        }
    }
}
