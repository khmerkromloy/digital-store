<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Sub-resource controller for managing line items on a specific order.
 * All routes are nested under /admin/orders/{order}/items.
 */
class OrderItemController extends Controller
{
    public function create(Order $order)
    {
        $products = Product::where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku', 'price']);

        return view('admin.orders.items.create', [
            'order' => $order->load('items'),
            'products' => $products,
        ]);
    }

    public function store(Request $request, Order $order)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $qty = (int) $data['quantity'];
        $unit = (float) $data['unit_price'];
        $discount = (float) ($data['discount'] ?? 0);
        $line = max(0, ($unit * $qty) - $discount);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'unit_price' => $unit,
            'quantity' => $qty,
            'discount' => $discount,
            'line_total' => $line,
        ]);

        $order->load('items');
        $order->recalculateTotals();
        $this->syncCustomerAggregates($order->customer_id);

        flash()->success(__('admin.flash.item_added'));

        return redirect()->route('admin.orders.show', $order);
    }

    public function destroy(Order $order, OrderItem $item)
    {
        abort_unless($item->order_id === $order->id, 404);
        $item->delete();

        $order->load('items');
        $order->recalculateTotals();
        $this->syncCustomerAggregates($order->customer_id);

        flash()->success(__('admin.flash.item_removed'));

        return redirect()->route('admin.orders.show', $order);
    }

    private function syncCustomerAggregates(?int $customerId): void
    {
        if (! $customerId) {
            return;
        }
        $customer = Customer::find($customerId);
        if (! $customer) {
            return;
        }
        $orders = Order::where('customer_id', $customer->id)->get();
        $customer->orders_count = $orders->count();
        $customer->total_spent = (float) $orders->where('payment_status', 'paid')->sum('grand_total');
        $customer->save();
    }
}
