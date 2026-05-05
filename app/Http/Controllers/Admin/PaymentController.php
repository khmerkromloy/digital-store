<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\Payment;
use App\Support\ColumnRenderer;
use App\Support\CrudField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends BaseCrudController
{
    protected string $modelClass = Payment::class;

    protected string $routeSlug = 'payments';

    protected string $singularKey = 'admin.resources.payment';

    protected string $pluralKey = 'admin.resources.payments';

    protected array $columns = [
        'payment_number' => 'admin.fields.payment_number',
        'order.order_number' => 'admin.fields.order_number',
        'method' => 'admin.fields.method',
        'amount' => 'admin.fields.amount',
        'currency' => 'admin.fields.currency',
        'status' => 'admin.fields.status',
        'paid_at' => 'admin.fields.paid_at',
    ];

    protected array $with = ['order'];

    protected array $searchable = ['payment_number', 'method', 'reference_no', 'note'];

    protected function fields(): array
    {
        $orders = Order::orderByDesc('id')->take(200)->pluck('order_number', 'id')->toArray();

        $methods = [];
        foreach (['cash', 'bakong', 'aba', 'wing', 'telegram', 'usdt', 'manual'] as $m) {
            $methods[$m] = __('admin.payment_methods.'.$m);
        }

        $statuses = [];
        foreach (['pending', 'succeeded', 'failed', 'refunded'] as $s) {
            $statuses[$s] = __('admin.statuses.'.$s);
        }

        return [
            CrudField::text('payment_number', 'Payment #', false, 'col-md-4'),
            CrudField::select('order_id', 'Order', $orders, true, 'col-md-4'),
            CrudField::select('method', 'Method', $methods, true, 'col-md-4'),
            CrudField::decimal('amount', 'Amount', true, 'col-md-3'),
            CrudField::text('currency', 'Currency', false, 'col-md-2'),
            CrudField::select('status', 'Status', $statuses, true, 'col-md-3'),
            CrudField::datetime('paid_at', 'Paid at', false, 'col-md-4'),
            CrudField::text('reference_no', 'Reference #', false, 'col-md-4'),
            CrudField::textarea('note', 'Note', false, 'col-md-12', '2'),
        ];
    }

    protected function rules(?Model $record = null): array
    {
        $id = $record?->id;

        return [
            'payment_number' => ['nullable', 'string', 'max:64', Rule::unique('payments', 'payment_number')->ignore($id)->whereNull('deleted_at')],
            'order_id' => ['required', 'exists:orders,id'],
            'method' => ['required', 'in:cash,bakong,aba,wing,telegram,usdt,manual'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'status' => ['required', 'in:pending,succeeded,failed,refunded'],
            'paid_at' => ['nullable', 'date'],
            'reference_no' => ['nullable', 'string', 'max:120'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    protected function applyExtraFilters(Builder $query, Request $request): void
    {
        if ($status = $request->input('status_filter')) {
            $query->where('status', $status);
        }
    }

    protected function beforeSave(array $data, Request $request, ?Model $record = null): array
    {
        if (empty($data['payment_number'])) {
            $data['payment_number'] = Payment::generatePaymentNumber();
        }
        if (empty($data['currency'])) {
            $order = Order::find($data['order_id']);
            $data['currency'] = $order?->currency ?? 'USD';
        }

        return $data;
    }

    protected function columnFormatters(): array
    {
        return [
            'status' => fn (Payment $p) => ColumnRenderer::badge($p->status, 'admin.statuses'),
            'amount' => fn (Payment $p) => ColumnRenderer::money($p->amount, $p->currency),
            'paid_at' => fn (Payment $p) => optional($p->paid_at)->format('Y-m-d H:i') ?: '<span class="text-muted">—</span>',
        ];
    }
}
