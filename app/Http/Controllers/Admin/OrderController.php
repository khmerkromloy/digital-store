<?php

namespace App\Http\Controllers\Admin;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Support\ColumnRenderer;
use App\Support\CrudField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderController extends BaseCrudController
{
    protected string $modelClass = Order::class;

    protected string $routeSlug = 'orders';

    protected string $singularKey = 'admin.resources.order';

    protected string $pluralKey = 'admin.resources.orders';

    protected array $columns = [
        'order_number' => 'admin.fields.order_number',
        'customer.full_name' => 'admin.fields.customer',
        'branch.name' => 'admin.fields.branch',
        'grand_total' => 'admin.fields.grand_total',
        'status' => 'admin.fields.status',
        'payment_status' => 'admin.fields.payment_status',
        'placed_at' => 'admin.fields.placed_at',
    ];

    protected array $with = ['customer', 'branch'];

    protected array $searchable = ['order_number', 'payment_method', 'admin_note', 'customer_note'];

    protected function fields(): array
    {
        $customers = Customer::orderBy('full_name')->pluck('full_name', 'id')->toArray();
        $branches = Branch::orderBy('name')->pluck('name', 'id')->toArray();

        $statuses = $this->mapKeysWithLabels([
            'pending', 'paid', 'partial', 'refunded', 'cancelled', 'delivered', 'failed',
        ], 'admin.statuses');

        $paymentStatuses = $this->mapKeysWithLabels([
            'unpaid', 'partial', 'paid', 'refunded',
        ], 'admin.statuses');

        $deliveryStatuses = $this->mapKeysWithLabels([
            'pending', 'delivered', 'failed',
        ], 'admin.statuses');

        $methods = $this->mapKeysWithLabels([
            'cash', 'bakong', 'aba', 'wing', 'telegram', 'usdt', 'manual',
        ], 'admin.payment_methods');

        $deliveryMethods = $this->mapKeysWithLabels([
            'email', 'manual', 'telegram',
        ], 'admin.delivery_methods');

        return [
            CrudField::text('order_number', 'Order #', false, 'col-md-4'),
            CrudField::select('customer_id', 'Customer', $customers, true, 'col-md-4'),
            CrudField::select('branch_id', 'Branch', $branches, false, 'col-md-4'),
            CrudField::select('status', 'Status', $statuses, true, 'col-md-3'),
            CrudField::select('payment_status', 'Payment status', $paymentStatuses, true, 'col-md-3'),
            CrudField::select('delivery_status', 'Delivery status', $deliveryStatuses, true, 'col-md-3'),
            CrudField::text('currency', 'Currency', false, 'col-md-3'),
            CrudField::select('payment_method', 'Payment method', $methods, false, 'col-md-3'),
            CrudField::select('delivery_method', 'Delivery method', $deliveryMethods, false, 'col-md-3'),
            CrudField::datetime('placed_at', 'Placed at', false, 'col-md-3'),
            CrudField::datetime('paid_at', 'Paid at', false, 'col-md-3'),
            CrudField::datetime('delivered_at', 'Delivered at', false, 'col-md-3'),
            CrudField::textarea('customer_note', 'Customer note', false, 'col-md-6', '2'),
            CrudField::textarea('admin_note', 'Admin note', false, 'col-md-6', '2'),
        ];
    }

    protected function rules(?Model $record = null): array
    {
        $id = $record?->id;

        return [
            'order_number' => ['nullable', 'string', 'max:64', Rule::unique('orders', 'order_number')->ignore($id)->whereNull('deleted_at')],
            'customer_id' => ['required', 'exists:customers,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'status' => ['required', 'in:pending,paid,partial,refunded,cancelled,delivered,failed'],
            'payment_status' => ['required', 'in:unpaid,partial,paid,refunded'],
            'delivery_status' => ['required', 'in:pending,delivered,failed'],
            'currency' => ['nullable', 'string', 'size:3'],
            'payment_method' => ['nullable', 'in:cash,bakong,aba,wing,telegram,usdt,manual'],
            'delivery_method' => ['nullable', 'in:email,manual,telegram'],
            'placed_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'delivered_at' => ['nullable', 'date'],
            'customer_note' => ['nullable', 'string', 'max:2000'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function applyExtraFilters(Builder $query, Request $request): void
    {
        if ($status = $request->input('status_filter')) {
            $query->where('status', $status);
        }
        if ($branch = $request->input('branch_filter')) {
            $query->where('branch_id', $branch);
        }
    }

    protected function beforeSave(array $data, Request $request, ?Model $record = null): array
    {
        if (empty($data['order_number'])) {
            $data['order_number'] = Order::generateOrderNumber();
        }

        return $data;
    }

    protected function afterSave(Model $record, Request $request): void
    {
        if (! $record instanceof Order) {
            return;
        }
        $record->loadMissing('items');
        $record->recalculateTotals();
        $this->syncCustomerAggregates($record->customer_id);
    }

    protected function columnFormatters(): array
    {
        return [
            'status' => fn (Order $o) => ColumnRenderer::badge($o->status, 'admin.statuses'),
            'payment_status' => fn (Order $o) => ColumnRenderer::badge($o->payment_status, 'admin.statuses'),
            'grand_total' => fn (Order $o) => ColumnRenderer::money($o->grand_total, $o->currency),
            'placed_at' => fn (Order $o) => optional($o->placed_at)->format('Y-m-d H:i') ?: '<span class="text-muted">—</span>',
        ];
    }

    public function show($key)
    {
        $order = Order::with(['customer', 'branch', 'items.product', 'payments'])->findOrFail($key);

        return view('admin.orders.show', [
            'record' => $order,
            'meta' => $this->meta(),
        ]);
    }

    /**
     * Recalculate the order totals on demand and bounce back to the show page.
     */
    public function recalculate(Order $order)
    {
        $order->recalculateTotals();
        $this->syncCustomerAggregates($order->customer_id);
        flash()->success(__('admin.flash.totals_recalculated'));

        return redirect()->route('admin.orders.show', $order);
    }

    /**
     * Mark the order as fully paid: status=paid, payment_status=paid, paid_at=now,
     * and create a balancing payment record so totals reconcile.
     */
    public function markPaid(Order $order)
    {
        $order->loadMissing('payments');
        $alreadyPaid = (float) $order->payments->where('status', 'succeeded')->sum('amount');
        $delta = max(0, (float) $order->grand_total - $alreadyPaid);

        DB::transaction(function () use ($order, $delta) {
            if ($delta > 0) {
                Payment::create([
                    'order_id' => $order->id,
                    'payment_number' => Payment::generatePaymentNumber(),
                    'method' => $order->payment_method ?? 'manual',
                    'amount' => $delta,
                    'currency' => $order->currency,
                    'status' => 'succeeded',
                    'paid_at' => now(),
                    'note' => 'Marked paid from admin.',
                ]);
            }
            $order->status = 'paid';
            $order->payment_status = 'paid';
            $order->paid_at = $order->paid_at ?: now();
            $order->save();
        });

        $this->syncCustomerAggregates($order->customer_id);
        flash()->success(__('admin.flash.updated', ['name' => __($this->singularKey)]));

        return redirect()->route('admin.orders.show', $order);
    }

    /**
     * @param  array<int,string>  $keys
     * @return array<string,string>
     */
    private function mapKeysWithLabels(array $keys, string $prefix): array
    {
        $out = [];
        foreach ($keys as $k) {
            $out[$k] = __($prefix.'.'.$k);
        }

        return $out;
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
